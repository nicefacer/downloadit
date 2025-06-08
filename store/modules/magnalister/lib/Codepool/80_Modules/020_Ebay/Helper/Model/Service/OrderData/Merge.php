<?php
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id: EbayImportOrders.php 167 2013-02-08 12:00:00Z tim.neumann $
 *
 * (c) 2010 - 2015 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
MLFilesystem::gi()->loadClass('Modul_Helper_Model_Service_OrderData_Merge');

class ML_Ebay_Helper_Model_Service_OrderData_Merge extends ML_Modul_Helper_Model_Service_OrderData_Merge {
    
    protected $aEbayDebug = array();
    
    protected function mergeTotalShipping ($aOldTotal, $aNewTotal) {
        $aProduct = array();
        if (empty($this->aCurrentOrder['Products']) && !empty($this->aExistingOrder['Products'])) { //update
            foreach ($this->aExistingOrder['Products'] as $aExistingProduct){
                if(isset($aExistingProduct['MOrderID']) && $aExistingProduct['MOrderID'] == $this->aCurrentOrder['MPSpecific']['MOrderID']){
                    $aProduct = $aExistingProduct;// max 1 article per ebay order
                    break;
                }
            }
        } else {
            $aProduct = current($this->aCurrentOrder['Products']);
        }
        if (empty($aProduct)) { //update
            $aCosts = $this->ebayGetPreviousShippingCost();
        } else {
            $aCosts = array_merge(
                $this->ebayGetPreviousShippingCost(), 
                $this->ebayGetCurrentShippingCost(
                    $aNewTotal['Value'], 
                    $aProduct, // max 1 article per ebay order
                    $this->aCurrentOrder['MPSpecific']['MOrderID']
                )
            );
        }
            $aMaxCost = array('cost' => (float)0, 'add' => null, 'qty' => (int)0, 'total' => (float) 0, 'promotional' => false);
            $fSumCost = $aMaxCost['cost'];
            $iProductQty = $aMaxCost['qty'];
            $fOrderCost = $aMaxCost['total'];
            foreach ($aCosts as &$aCost) {/** @deprecated '&' needed for $this->ebayManipulateDeprecatedCost() */
                $this->ebayManipulateDeprecatedCost($aCost);
                if ($aMaxCost['cost'] < $aCost['cost']) {
                    $aMaxCost = $aCost;
                }
                $fSumCost += $aCost['cost'] * $aCost['qty'];
                $iProductQty += $aCost['qty'];
                $fOrderCost += $aCost['total'];
            }
            $this->ebayDebug(array('aCosts' => $aCosts));
            $this->oMlOrder->set('internaldata' , $aCosts);
            $this->ebayDebug(array('fOrderCost' => $fOrderCost));
            $this->ebayDebug(array('aMaxCost' => $aMaxCost));
            if ($aMaxCost['add'] === null) {
                $this->ebayDebug(array('fSumCost' => $fSumCost));
                $fCost = $fSumCost;
            } else {
                $this->ebayDebug(array('iProductQty' => $iProductQty));
                $fCost = max($aMaxCost['cost'], $aMaxCost['cost'] + (($iProductQty - 1) * $aMaxCost['add']));
            }
            $this->ebayDebug(array('fCost' => $fCost));
            if (count($aCosts) > 1 //check ebay profile only for merged order and not upating
                    && $aMaxCost['promotional']) {
                $fCost = $this->ebayCalcPromotionalDiscount($fCost, $fOrderCost, $iProductQty);
                $this->ebayDebug(array('fCostPromotional' => $fCost));
            }
            $aNewTotal['Value'] = $fCost;
            $this->ebayDebug();
            return $aNewTotal;
    }
    
    /** 
     * @deprecated we use now all promotionals versions 
     */
    protected function ebayManipulateDeprecatedCost (&$aCost) {
        $aCost['promotional'] = isset($aCost['promotional']) ? $aCost['promotional'] : isset($aCost['max']) && $aCost['max'] !== null;
        $aCost['total'] = isset($aCost['total']) ? $aCost['total'] : (float)0;
        return $this;
    }
    
    protected function ebayDebug ($aDebug = null) {
        if ($aDebug === null) {
            MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                'eBay-shipping-costs' => $this->aEbayDebug,
            ));
            MLMessage::gi()->addDebug('eBay-shipping-costs', $this->aEbayDebug);
            $this->aEbayDebug = array();
        } else {
            $this->aEbayDebug = array_merge($this->aEbayDebug, $aDebug);
        }
        return $this;
    }
    
    /**
     * calculates shipping promotional discount
     * @param float $fCost cost for order depends on item addcost
     * @param float $fTotal cost for all products
     * @param int $iQty quantity for all products
     * @return $this
     */
    protected function ebayCalcPromotionalDiscount ($fCost, $fOrderCost, $iQty) {
        $aPromotion = MLModul::gi()->getShippingPromotionalDiscount();
        $this->ebayDebug(array('aPromotion' => $aPromotion));
        $blPromotion = false;
        if (empty($aPromotion)) {
            $aPromotion['ShippingCost'] = $fCost;
        } else {
            switch ($aPromotion['DiscountName']) {
                case 'ShippingCostXForAmountY' : { // "Geben Sie EUR (Y) für mindestens zwei Artikel aus und die Versandkosten betragen (X)"
                    $blPromotion = $fOrderCost >= $aPromotion['OrderAmount'] ;
                    break;
                }
                case 'ShippingCostXForItemCountN' : { // "Kaufen Sie mindestens (N) Artikelanzahl Artikel und die Versandkosten betragen Wählen Sie einen Sonderpreis für den Versand aus. EUR (X)"
                    $blPromotion = $iQty >= $aPromotion['ItemCount'];
                    break;
                }
                case 'MaximumShippingCostPerOrder' : { // "Geben Sie nicht mehr als EUR () für den Versand pro Bestellung aus."
                    $blPromotion = $fCost >= $aPromotion['ShippingCost'];
                    break;
                }
            }
        }
        return $blPromotion ? $aPromotion['ShippingCost'] : $fCost;
    }
    
    
    protected function ebayGetPreviousShippingCost () {
        if (is_array($this->oMlOrder->get('internaldata'))) {
            return $this->oMlOrder->get('internaldata');
        } else {
            $fCost = 0;
            foreach ($this->aExistingOrder['Totals'] as $aTotal) {
                if ($aTotal['Type'] == 'Shipping') {
                    $fCost = $aTotal['Value'];
                    break;
                }
            }
            return $this->ebayGetCurrentShippingCost($fCost, current($this->aExistingOrder['Products']), $this->aExistingOrder['MPSpecific']['MOrderID']);
        }
    }
    
    /**
     * 
     * @param float $fCost
     * @param array $aProduct | ebay have only on article per order
     * @return array
     */
    protected function ebayGetCurrentShippingCost ($fCost, $aProduct, $sMlOrderId) {    
        $fCost = (float) $fCost;
        $oPrepareHelper = MLHelper::gi('Model_Table_Ebay_PrepareData')->setPrepareList(null);
        /* @var $oPrepareHelper ML_Ebay_Helper_Model_Table_Ebay_PrepareData */
        if (isset($aProduct['SKU']) && !empty($aProduct['SKU'])) {
            $oProduct = MLProduct::factory()->getByMarketplaceSKU($aProduct['SKU']);
            if (
                !$oProduct->exists()
                || !MLDatabase::getPrepareTableInstance()->set('products_id', $oProduct->get('id'))->exists()
            ) { // product is not prepared
                $oProduct = null;
            }
        } else {
            $oProduct = null;
        }
        $this->ebayDebug(array('productIsPrepared' => $oProduct !== null));
        # getDestination
        $sOrderCountry = '';
        foreach (array('Shipping', 'Main', 'Billing') as $sAddressType) {
            if (
                array_key_exists($sAddressType, $this->aCurrentOrder['AddressSets'])
                && array_key_exists('CountryCode', $this->aCurrentOrder['AddressSets'][$sAddressType])
            ) {
                $sOrderCountry = $this->aCurrentOrder['AddressSets'][$sAddressType]['CountryCode'];
                break;
            }
        }
        $sDestination = strtoupper(MLModul::gi()->getConfig('country')) == strtoupper($sOrderCountry) ? 'Local' : 'International';
        $this->ebayDebug(array('destination' => $sDestination));
        $aPreparedDestinationData = $oPrepareHelper
            ->setProduct($oProduct)//could be null for default values
            ->getPrepareData(array(
                'Shipping'.$sDestination.'Profile'  => (($oProduct === null) ? array('optional' => array('active' => true)) : array()),
                'Shipping'.$sDestination.'Discount' => (($oProduct === null) ? array('optional' => array('active' => true)) : array()),
            ))
        ;
        $this->ebayDebug(array('preparedDestinationData' => $aPreparedDestinationData));
        $iProfileId = $aPreparedDestinationData['Shipping'.$sDestination.'Profile']['value'];
        $blDiscount = (bool)$aPreparedDestinationData['Shipping'.$sDestination.'Discount']['value'];
        $aMpProfileData = MLModul::gi()->getShippingDiscountProfiles();
        if (array_key_exists($iProfileId, $aMpProfileData)) {
            $this->ebayDebug(array('shippingDiscountProfile' => array('id' => $iProfileId, 'data' => $aMpProfileData[$iProfileId])));
            $fAdd = $aMpProfileData[$iProfileId]['amount'];
            if ($fAdd > 0) {
                $fCost = max(0, $fCost - (($aProduct['Quantity'] -1 ) * $fAdd));
            } elseif ($fAdd < 0) {
                /**
                 * totalCost <=> qty * x - (qty - 1) * - additionalCost
                 * $fCost                                                                       <=> ($aProduct['Quantity'] * x) - (($aProduct['Quantity'] - 1) * -$fAdd)    | +(($aProduct['Quantity'] - 1) * -$fAdd)
                 * $fCost + (($aProduct['Quantity'] - 1) * -$fAdd)                              <=> $aProduct['Quantity'] * x                                             | / $aProduct['Quantity]
                 * ($fCost + (($aProduct['Quantity'] - 1) * -$fAdd)) / $aProduct['Quantity']    <=> x
                 */
                $fCost = ($fCost + (($aProduct['Quantity'] - 1) * -$fAdd)) / $aProduct['Quantity'];
            }
        } else {
            $fAdd = null;
            $fCost = $fCost / $aProduct['Quantity'];
        }
        return array($sMlOrderId => array(
            'cost' => $fCost, // 1. article
            'add' => $fAdd, // each other article
            'qty' => $aProduct['Quantity'], // qty of product
            'total' => $aProduct['Quantity'] * $aProduct['Price'], // cost for ordered products
            'promotional' => $blDiscount,
        ));
    }

    /**
     * new value
     * @return array
     */
    protected function mergeOrder() {
        $aOld = isset($this->aExistingOrder['Order']) ? $this->aExistingOrder['Order'] : array();
        $aNew = isset($this->aCurrentOrder['Order']) ? $this->aCurrentOrder['Order'] : array();
        foreach ($aOld as $sOld => $mOld) {
            if (!isset($aNew[$sOld])) {
                $aNew[$sOld] = $mOld;
            }
        }

        //merge paid status
        $sOldId = (isset($this->aExistingOrder['MPSpecific']) && isset($this->aExistingOrder['MPSpecific']['MOrderID']))
            ? $this->aExistingOrder['MPSpecific']['MOrderID']
            : 'unknown';
        $sOldPaidStatus = isset($aOld['Payed']) ? $aOld['Payed'] : false;

        $sNewId = (isset($this->aCurrentOrder['MPSpecific']) && isset($this->aCurrentOrder['MPSpecific']['MOrderID']))
            ? $this->aCurrentOrder['MPSpecific']['MOrderID']
            : 'unknown';
        $sNewPaidStatus = isset($aNew['Payed']) ? $aNew['Payed'] : false;

        $aNew['Payed'] = ($aNew['Payed'] && $aNew['Payed']);
        if (isset($aNew['StatusDetails'])) {
            $aNew['StatusDetails'] = $aOld['StatusDetails'];
        } elseif (!empty($this->aExistingOrder)) {
            // at first import there is no existing data so don't create status details for old data
            $aNew['StatusDetails'] = array($sOldId => $sOldPaidStatus);
        }
        $aNew['StatusDetails'][$sNewId] = $sNewPaidStatus;

        $blStatus = true;
        foreach ($aNew['StatusDetails'] as $blStatusDetail) {
            $blStatus = $blStatus && $blStatusDetail;
        }
        
        $aNew['Payed'] = $blStatus;
        if ($blStatus) {
            $aNew['Status'] = MLModul::gi()->getConfig('orderstatus.paid');
        } else {
            $aNew['Status'] = MLModul::gi()->getConfig('orderstatus.open');
        }
        return $aNew;
    }

}
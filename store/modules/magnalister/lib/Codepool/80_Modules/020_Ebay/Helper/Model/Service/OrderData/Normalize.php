<?php
MLFilesystem::gi()->loadClass('Modul_Helper_Model_Service_OrderData_Normalize');

class ML_Ebay_Helper_Model_Service_OrderData_Normalize extends ML_Modul_Helper_Model_Service_OrderData_Normalize {

    /**
     * @deprecated (1429879042) we dont take care of shipping data, but its deprecated, perhaps we need it
     */
    protected $sAddressIdDeprecated = null;

    protected function normalizeOrder() {
        parent::normalizeOrder();
        if (!isset($this->aOrder['Products']) || empty($this->aOrder['Products'])) {
            $aOrderData = MLOrder::factory()->getByMagnaOrderId($this->aOrder['MPSpecific']['MOrderID'])->get('orderdata');
            $this->aOrder['Order']['DatePurchased'] =
                isset($this->aOrder['Order']['DatePurchased'])
                    ? $this->aOrder['Order']['DatePurchased']
                    : $aOrderData['Order']['DatePurchased'];
        }
        foreach ($this->aOrder['Totals'] as $aTotal) {
            if ($aTotal['Type'] == 'Payment' && isset($aTotal['Complete']) && $aTotal['Complete'] == true) {
                $this->aOrder['Order']['Payed'] = true;
                $this->aOrder['Order']['Status'] = MLModul::gi()->getConfig('orderstatus.paid');
                break;
            }
        }
        return $this;
    }

    protected function normalizeMpSpecific() {
        parent::normalizeMpSpecific();
        foreach ($this->aOrder['Totals'] as $aTotal) {
            if ($aTotal['Type'] == 'Payment' && isset($aTotal['Complete']) && $aTotal['Complete'] == true) {
                foreach ($aTotal as $sTotalKey => $mTotalValue) {
                    if (!in_array($sTotalKey, array('Type', 'Value', 'Tax'))) {
                        $this->aOrder['MPSpecific']['Payment'][$sTotalKey] = $mTotalValue;
                    }
                }
                break;
            }
        }

        // prev. order-ids
        if ($this->getUpdateMode()) {
            $oOrder = MLOrder::factory()->getByMagnaOrderId($this->aOrder['MPSpecific']['MOrderID']);
        } elseif (MLModul::gi()->getConfig('importonlypaid') != '1') {
            //we don't need set MPreviousOrderID in update order and if we import only paid orders
            $oOrder = $this->ebayGetNotFinalizedOrder();
        } else {
            $oOrder = null;
        }

        if (is_object($oOrder)) {
            $iPreviosId = $oOrder->get('special');
            $iNewId = $this->aOrder['MPSpecific']['MOrderID'];
            $aOrderData = $oOrder->get('orderdata');
            if ($iPreviosId != $iNewId) {
                $this->aOrder['MPSpecific']['MPreviousOrderID'] = array(
                    'id' => $iPreviosId,
                    'date' => $aOrderData['Order']['DatePurchased']
                );
            }
            $aData = $oOrder->get('data');
            $aIds = isset($aData['MPreviousOrderIDS']) ? $aData['MPreviousOrderIDS'] : array();
            $aIds[] = $iPreviosId;
            $this->aOrder['MPSpecific']['MPreviousOrderIDS'] = array_unique($aIds);
        }
        return $this;
    }

    protected function ebayGetNotFinalizedOrder($iStart = 0) {
        // find existing order;
        $aClosedStatuses = MLModul::gi()->getConfig('orderstatus.closed');
        $aClosedStatuses = is_array($aClosedStatuses) ? $aClosedStatuses : array();
        $oOrderList = MLOrder::factory()->getList();
        $oOrderList
            ->getQueryObject()
            ->where("orderdata LIKE '%\"EMailIdent\":\"".$this->aOrder['AddressSets']['Main']['EMail']."\"%'")
            ->where("orderdata LIKE '%\"Currency\":\"".$this->aOrder['Order']['Currency']."\"%'")
            ->where("status NOT IN('".implode("', '", $aClosedStatuses)."')")
            ->orderBy('orders_id DESC')
            ->limit($iStart, 1);
        if (count($oOrderList->getList()) != 0) {
            $oOrder = current($oOrderList->getList());
            if (!in_array($oOrder->getShopOrderStatus(), $aClosedStatuses)) {
                return $oOrder;
            } else {
                return $this->ebayGetNotFinalizedOrder($iStart + 1);
            }
        } else {
            return false;
        }
    }

    /**
     * @deprecated (1429879042) we dont take care of shipping data, but its deprecated, perhaps we need it
     */
    protected function ebayGetNotFinalizedOrderDeprecated($iStart = 0) {
        if ($this->sAddressIdDeprecated === null) {
            $aComparableData = array();
            foreach (array_keys($this->aOrder['AddressSets']) as $sAddressType) {
                if ($sAddressType == 'Shipping') {
                    foreach (array('Gender', 'Firstname', 'Company', 'Street', 'Housenumber', 'Postcode', 'City', 'Suburb', 'CountryCode', 'Phone', 'EMail', 'DayOfBirth',) as $sField) {
                        $aComparableData[$sAddressType][$sField] = $this->aOrder['AddressSets'][$sAddressType][$sField];
                    }
                } else {
                    $aComparableData[$sAddressType]['EMail'] = $this->aOrder['AddressSets'][$sAddressType]['EMail'];
                }
            }
            $this->sAddressIdDeprecated = md5(json_encode($aComparableData));
            $this->aOrder['MPSpecific']['AddressId'] = $this->sAddressIdDeprecated; // for finding existing order by same customer
        }
        $sAddressId = $this->sAddressIdDeprecated;
        // find existing order;
        $aClosedStatuses = MLModul::gi()->getConfig('orderstatus.closed');
        $aClosedStatuses = is_array($aClosedStatuses) ? $aClosedStatuses : array();
        $oOrderList = MLOrder::factory()->getList();
        $oOrderList
            ->getQueryObject()
            ->where("data LIKE '%\"AddressId\":\"".$sAddressId."\"%'")
            ->where("status NOT IN('".implode("', '", $aClosedStatuses)."')")
            ->orderBy('orders_id DESC')
            ->limit($iStart, 1);
        if (count($oOrderList->getList()) != 0) {
            $oOrder = current($oOrderList->getList());
            if (!in_array($oOrder->getShopOrderStatus(), $aClosedStatuses)) {
                return $oOrder;
            } else {
                return $this->ebayGetNotFinalizedOrderDeprecated($iStart + 1);
            }
        } else {
            return false;
        }
    }
    
    protected function normalizeProduct (&$aProduct, $fDefaultProductTax) {
        parent::normalizeProduct($aProduct, $fDefaultProductTax);
        $aProduct['MOrderID'] = $this->aOrder['MPSpecific']['MOrderID'];
        return $this;
    }

}

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
 * $Id$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
class ML_Ayn24_Helper_Model_Service_Product {

    /** @var ML_Database_Model_Table_Selection $oSelection     */
    protected $oSelection = null;
    protected $aSelectionData = array();

    /** @var ML_Ayn24_Model_Table_Ayn24_Prepare $oPrepare     */
    protected $oPrepare = null;

    /** @var ML_Shop_Model_Product_Abstract $oProduct     */
    protected $oProduct = null;
    protected $oVariant = null;
    protected $aData = null;

    public function __call($sName, $mValue) {
        return $sName . '()';
    }

    public function __construct() {
        $this->oPrepare = MLDatabase::factory('ayn24_prepare');
        $this->oSelection = MLDatabase::factory('selection');
    }

    public function setProduct(ML_Shop_Model_Product_Abstract $oProduct) {
        $this->oProduct = $oProduct;
        $this->sPrepareType = '';
        $this->aData = null;
        return $this;
    }

    public function setVariant(ML_Shop_Model_Product_Abstract $oProduct) {
        $this->oVariant = $oProduct;
        return $this;
    }

    public function resetData() {
        $this->aData = null;
        return $this;
    }

    public function getData($blIsMaster = false) {
        if ($this->aData === null) {
            $this->oPrepare->init()->set('products_id', $this->oVariant->get('id'));
            $aData = array();
            $aFields = array(
                'SKU',
                'EAN',
                'Quantity',
                'Price',
                'BasePrice',
                'ShippingTime',
                'ShippingDetails',
                'Currency',
                'MarketplaceCategory',
            );
            if($blIsMaster){
                $aFields = array_merge($aFields, array(
                        'ItemTitle',
                        'TaxPercent',
                        'Description',
                        'ShortDescription',
                        'Images',
                    )
                ); 
            }else{               
                $aFields =  array_merge($aFields, array(
                        'Variation',
                        'VariationId',
                    )
                );
            }
            foreach ($aFields as $sField) {
                if (method_exists($this, 'get' . $sField)) {
                    $mValue = $this->{'get' . $sField}();
                    if (is_array($mValue)) {
                        foreach ($mValue as $sKey => $mCurrentValue) {
                            if (empty($mCurrentValue)) {
                                unset($mValue[$sKey]);
                            }
                        }

                        $mValue = empty($mValue) ? null : $mValue;
                    }

                    if ($mValue !== null) {
                        $aData[$sField] = $mValue;
                    }
                } else {
                    MLMessage::gi()->addWarn("function ML_Ayn24_Helper_Model_Service_Product::get" . $sField . "() doesn't exist");
                }
            }

            if (empty($aData['BasePrice'])) {
                unset($aData['BasePrice']);
            }

            $this->aData = $aData;
        }

        return $this->aData;
    }

    protected function getSKU() {
        return $this->oVariant->getMarketPlaceSku();
    }
    
    protected function getItemTitle() {
        $iLangId = MLModul::gi()->getConfig('lang');
        $this->oVariant->setLang($iLangId);	

        return $this->oVariant->getName();
    }

    protected function getEAN() {
		return $this->oVariant->getModulField('general.ean', true);
    }

    protected function getImages() {
        $aOut = array();
        foreach ($this->oVariant->getImages() as $sImage) {
			try {
				$aImage = MLImage::gi()->resizeImage($sImage, 'product', 500, 500);
				$aOut[]['URL'] = $aImage['url'];
			} catch(Exception $ex) {
				// Happens if image doesn't exist.
			}
        }
        return $aOut;
    }

    protected function getQuantity() {
        if (isset($this->aSelectionData['stock'])) {
            return $this->aSelectionData['stock'];
        }

        $iQty = $this->oVariant->getSuggestedMarketplaceStock(
            MLModul::gi()->getConfig('quantity.type'), MLModul::gi()->getConfig('quantity.value')
        );

        return $iQty < 0 ? 0 : $iQty;
    }

    protected function getPrice() {
        if (isset($this->aSelectionData['price'])) {
            return round($this->aSelectionData['price'], 2);
        }

        return round($this->oVariant->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject()), 2);
    }

    protected function getBasePrice() {
        return $this->oVariant->getBasePrice();
    }

	protected function getTaxPercent() {
		return $this->oVariant->getTax();
	}

    protected function getDescription() {
		$sLongDescAttribute = MLModul::gi()->getConfig('checkin.longdesc');
		if (empty($sLongDescAttribute) === false) {
			return $this->oVariant->getAttributeValue($sLongDescAttribute);
		} else {
			return $this->oVariant->getDescription();
		}
    }

    protected function getShortDescription() {
		$sShortDescAttribute = MLModul::gi()->getConfig('checkin.shortdesc');
		if (empty($sShortDescAttribute) === false) {
			return $this->oVariant->getAttributeValue($sShortDescAttribute);
		} else {
			return $this->oVariant->getShortDescription();
		}
    }

    /**
     * @todo use new custom field to each shop(magento , shopware, prestashop) to return each product leadtimetoship
     */
    protected function getShippingTime() {
        return MLModul::gi()->getConfig('checkin.leadtimetoship');
    }
    
    protected function getShippingDetails() {
        $dShippingCost = $this->oPrepare->get('ShippingCost');
        $sShippingType = $this->oPrepare->get('ShippingType');
        
        return array(
            'ShippingCost' => $dShippingCost,
            'ShippingType' => $sShippingType,
        );
    }

    protected function getCurrency() {
        return MLModul::gi()->getConfig('currency');
    }
	
    protected function getMarketplaceCategory() {
        $sMarketplaceCategory = str_replace('_', '.', $this->oPrepare->get('PrimaryCategory'));
        return $sMarketplaceCategory;
    }

    protected function getVariation() {
        $aResult = $this->oVariant->getVariatonDataOptinalField(array('code','valueid','name','value'));
        return $aResult;
    }

    protected function getVariationId() {
        $aResult = $this->oVariant->getVariatonData();
        if (count($aResult) != 0) {
            return $this->oVariant->get('id');
        } else {
            return null;
        }
    }
}

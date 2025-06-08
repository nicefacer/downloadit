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

class ML_Cdiscount_Helper_Model_Service_Product {

    /** @var ML_Database_Model_Table_Selection $oSelection */
    protected $oSelection = null;
    protected $aSelectionData = array();

    /** @var ML_Cdiscount_Model_Table_Cdiscount_Prepare $oPrepare  */
    protected $oPrepare = null;

    /** @var ML_Shop_Model_Product_Abstract $oProduct  */
    protected $oProduct = null;
    protected $aData = null;

	protected $aLang = null;

    public function __call($sName, $mValue) {
        return $sName . '()';
    }

    public function __construct() {
        $this->oPrepare = MLDatabase::factory('cdiscount_prepare');
        $this->oSelection = MLDatabase::factory('selection');
    }

    public function setProduct(ML_Shop_Model_Product_Abstract $oProduct) {
        $this->oProduct = $oProduct;
        $this->sPrepareType = '';
        $this->aData = null;
        return $this;
    }
    
    public function setVariant(ML_Shop_Model_Product_Abstract $oProduct){
        $this->oVariant=$oProduct;
        return $this;
    }
    
    public function resetData () {
        $this->aData = null;
		$this->aLang = MLModul::gi()->getConfig('lang');
        return $this;
    }
    
    public function getData() {
        if ($this->aData === null) {
            $this->oPrepare->init()->set('products_id', $this->oVariant->get('id'));
            $aData = array();
			$aFields = array(
                'SKU',
                'EAN',
                'MarketplaceCategory',
                'CategoryAttributes',
                'Title',
                'Subtitle',
                'Description',
                'Images',
                'Quantity',
                'Price',
                'Brand',
                'ManufacturerPartNumber',
                'Tax',
                'ShippingInfo',
                'OfferCondition',
                'OfferComment',
//              'Matched',
            );

            foreach ($aFields as $sField) {
                if (method_exists($this, 'get' . $sField)) {
                    $mValue = $this->{'get' . $sField}();
                    if (is_array($mValue)) {
//                        foreach ($mValue as $sKey => $mCurrentValue) {
//                            if (empty($mCurrentValue)) {
//                                unset ($mValue[$sKey]);
//                            }
//                        }
                        $mValue = empty($mValue) ? null : $mValue;
                    }
                    if ($mValue !== null) {
                        $aData[$sField] = $mValue;
                    }
                } else {
                    MLMessage::gi()->addWarn("function  ML_Cdiscount_Helper_Model_Service_Product::get" . $sField . "() doesn't exist");
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
    
    protected function getEAN() {
        $sEan = $this->oPrepare->get('EAN');
        if (isset($sEan) === false || empty($sEan)) {
            $sEan = $this->oVariant->getEAN();
        }

        return $sEan;
    }

    protected function getMarketplaceCategory() {
        return $this->oPrepare->get('PrimaryCategory');
    }

    protected function getCategoryAttributes() {
        return $this->oPrepare->get('CatAttributes');
    }

    protected function getTitle() {
       $sTitle = $this->oPrepare->get('Title');
        if (empty($sTitle) === false && $sTitle !== '') {
            $sTitle = html_entity_decode(fixHTMLUTF8Entities($sTitle), ENT_COMPAT, 'UTF-8');
        } else {
            $iLang = MLModul::gi()->getConfig('lang');
            $this->oVariant->setLang($iLang);
            $sTitle = $this->oVariant->getName();
            $sTitle = html_entity_decode(fixHTMLUTF8Entities($sTitle), ENT_COMPAT, 'UTF-8');
        }

        return $sTitle;
	}
    
    protected function getSubtitle() {
        $sSubtitle = $this->oPrepare->get('Subtitle');
        if (empty($sSubtitle) === false && $sSubtitle !== '') {
            $sSubtitle = html_entity_decode(fixHTMLUTF8Entities($sSubtitle), ENT_COMPAT, 'UTF-8');
        } else {
            $iLang = MLModul::gi()->getConfig('lang');
            $this->oVariant->setLang($iLang);
            $sSubtitle = $this->oVariant->getShortDescription();
            $sSubtitle = html_entity_decode(fixHTMLUTF8Entities($sSubtitle), ENT_COMPAT, 'UTF-8');
        }

        return $sSubtitle;
	}

    protected function getDescription() {
        $sDescription = $this->oPrepare->get('Description');
        if (empty($sDescription) === false && $sDescription !== '') {
            $sDescription = html_entity_decode(fixHTMLUTF8Entities($sDescription), ENT_COMPAT, 'UTF-8');
        } else {
            $iLang = MLModul::gi()->getConfig('lang');
            $this->oVariant->setLang($iLang);
            $sDescription = $this->oVariant->getDescription();
            $sDescription = html_entity_decode(fixHTMLUTF8Entities($sDescription), ENT_COMPAT, 'UTF-8');
        }

        return $sDescription;
    }

    protected function getImages() {
		$aImagesPrepare = $this->oPrepare->get('Images');
        $sImagePathFromConfig = MLModul::gi()->getConfig('imagepath');
        $aOut = array();
		if (empty($aImagesPrepare) === false) {
            $aImages = $this->oVariant->getImages();
            
			foreach ($aImages as $sImage) {
				$sImageName = $this->substringAferLast('\\', $sImage);
				if (isset($sImageName) === false || strpos($sImageName, '/') !== false) {
					$sImageName = $this->substringAferLast('/', $sImage);
				}
				
				if (in_array($sImageName, $aImagesPrepare) === false) {
					continue;
				}

				try {
                    if (isset($sImagePathFromConfig) && $sImagePathFromConfig != '') {
                        $sImagePath = $sImagePathFromConfig . $sImageName;
                    } else {
                        $aImage = MLImage::gi()->resizeImage($sImage, 'products', 500, 500);
                        $sImagePath = $aImage['url'];
                    }
                    
					$aOut[] = array('URL' => $sImagePath);
				} catch(Exception $ex) {
					// Happens if image doesn't exist.
				}
			}
		}

        return $aOut;
    }

    protected function getQuantity() {
        $iQty = $this->oVariant->getSuggestedMarketplaceStock(
            MLModul::gi()->getConfig('quantity.type'),
            MLModul::gi()->getConfig('quantity.value')
        );
        
        return $iQty < 0 ? 0 : $iQty;
    }

    protected function getPrice() {
        return $this->oVariant->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject());
    }
           
    protected function getBrand() {
        return $this->oVariant->getManufacturer();
    }
    
    protected function getManufacturerPartNumber() {
//      $mpn = $this->oVariant->getManufacturerPartNumber();
        return "";
    }
    
    protected function getTax() {
        return $this->oVariant->getTax();
    }

    // Shipping information, all shipping data is saved in one field
    protected function getShippingInfo() {

        $ShippingInfo = array(
        "ShippingTimeMax" => $this->oPrepare->get('ShippingTimeMax'),
        "ShippingTimeMin" => $this->oPrepare->get('ShippingTimeMin'),
        "ShippingFeeStandard" => $this->oPrepare->get('ShippingFeeStandard'),
        "ShippingFeeTracked" => $this->oPrepare->get('ShippingFeeTracked'),
        "ShippingFeeRegistered" => $this->oPrepare->get('ShippingFeeRegistered'),
        "ShippingFeeExtraStandard" => $this->oPrepare->get('ShippingFeeExtraStandard'),
        "ShippingFeeExtraTracked" => $this->oPrepare->get('ShippingFeeExtraTracked'),
        "ShippingFeeExtraRegistered" => $this->oPrepare->get('ShippingFeeExtraRegistered'),
        );

        return json_encode($ShippingInfo);

    }

    protected function getOfferCondition() {
        return $this->oPrepare->get('ItemCondition');
    }

    protected function getOfferComment() {
        return $this->oPrepare->get('Comment');
    }

    protected function getMatched() {
        return $this->oPrepare->get('PrepareType') !== 'apply';
    }
    
	private function substringAferLast($sNeedle, $sString) {
		if (!is_bool($this->strrevpos($sString, $sNeedle))) {
			return substr($sString, $this->strrevpos($sString, $sNeedle) + strlen($sNeedle));
		}
	}
	
	private function strrevpos($instr, $needle) {
		$rev_pos = strpos (strrev($instr), strrev($needle));
		if ($rev_pos === false) {
			return false;
		} else {
			return strlen($instr) - $rev_pos - strlen($needle);
		}
	}
    
}

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

class ML_Hitmeister_Helper_Model_Service_Product {

    /** @var ML_Database_Model_Table_Selection $oSelection */
    protected $oSelection = null;
    protected $aSelectionData = array();

    /** @var ML_Hitmeister_Model_Table_Hitmeister_Prepare $oPrepare  */
    protected $oPrepare = null;

    /** @var ML_Shop_Model_Product_Abstract $oProduct  */
    protected $oProduct = null;
    protected $aData = null;

	protected $aLang = null;

    public function __call($sName, $mValue) {
        return $sName . '()';
    }

    public function __construct() {
        $this->oPrepare = MLDatabase::factory('hitmeister_prepare');
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
                'Manufacturer',
                'ManufacturerpartNumber',
                'ItemTax',
                'ShippingTime',
                'ConditionType',
                'Location',
                'Comment',
                'Matched',
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
                    MLMessage::gi()->addWarn("function  ML_Hitmeister_Helper_Model_Service_Product::get" . $sField . "() doesn't exist");
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
        $aCatAttributes = $this->oPrepare->get('ShopVariation');
        foreach ($aCatAttributes as $key => &$aCatAttribute) {
            $sCode = $aCatAttribute['Code'];
            if ($sCode === 'freetext' || $sCode === 'category') {
                if (!isset($aCatAttribute['Values']) || empty($aCatAttribute['Values'])) {
                    unset($aCatAttributes[$key]);
                    continue;
                } else {
                    if ($sCode === 'category') {
                        $aCatAttribute['Values'] = $this->getCategoryNameById($aCatAttribute['Values']);
                    }

                    $aCatAttribute = $aCatAttribute['Values'];
                }
            } else {
                $shopAttributeValue = $this->oVariant->getAttributeValue($sCode);
                if (!isset($shopAttributeValue) || empty($shopAttributeValue)) {
                    unset($aCatAttributes[$key]);
                    continue;
                } else {
                    foreach ($aCatAttribute['Values'] as $value) {
                        if ($shopAttributeValue === $value['Shop']['Value']) {
                            $aCatAttribute = str_replace(array(' - (Manually matched)', ' - (Auto matched)', ' - (Free text)'), '', $value['Marketplace']['Value']);
                            break;
                        }
                    }

                    if (is_array($aCatAttribute)) {
                        $aCatAttribute = $shopAttributeValue;
                    }
                }
            }

            if ($this->stringStartsWith($key, 'additional_attribute')) {
                $aShopAttributes = MLFormHelper::getShopInstance()->getPrefixedAttributeList();
                $sNewKey = isset($aShopAttributes[$sCode]) ? $aShopAttributes[$sCode] : ucfirst($sCode);
                unset($aCatAttributes[$key]);
                $aCatAttributes[$sNewKey] = $aCatAttribute;
            }
        }

        return $aCatAttributes;
    }

    protected function getTitle() {
        $sTitle = $this->oPrepare->get('Title');
        if (isset($sTitle) === false || empty($sTitle)) {
            $iLangId = MLModul::gi()->getConfig('lang');
            $this->oVariant->setLang($iLangId);
            $sTitle = $this->oVariant->getName();
        }
        
        return $sTitle;
	}
    
    protected function getSubtitle() {
        $sSubtitle = $this->oPrepare->get('Subtitle');
        if (isset($sSubtitle) === false || empty($sSubtitle)) {
            $iLangId = MLModul::gi()->getConfig('lang');
            $this->oVariant->setLang($iLangId);
            $sSubtitle = $this->oVariant->getShortDescription();
        }
        
        return $sSubtitle;
	}

    protected function getDescription() {
        $sDescription = $this->oPrepare->get('Description');
        if (isset($sDescription) === false || empty($sDescription)) {
            $iLangId = MLModul::gi()->getConfig('lang');
            $this->oVariant->setLang($iLangId);
            $sDescription = $this->oVariant->getDescription();
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
           
    protected function getManufacturer() {
        return $this->oVariant->getManufacturer();
    }
    
    protected function getManufacturerPartNumber() {
        return $this->oVariant->getManufacturerPartNumber();
    }
    
    protected function getItemTax() {
        return $this->oVariant->getTax();
    }
	
    protected function getShippingTIme() {
        return $this->oPrepare->get('ShippingTIme');
    }
    
    protected function getConditionType() {
        return $this->oPrepare->get('ItemCondition');
    }
    
    protected function getLocation() {
        return $this->oPrepare->get('ItemCountry');
    }
    
    protected function getComment() {
        return $this->oPrepare->get('Comment');
    }
    
    protected function getMatched() {
        return $this->oPrepare->get('PrepareType') !== 'apply';
    }

    private function getCategoryNameById($categoryID) {
        try {
            $aResponse = MagnaConnector::gi()->submitRequestCached(array('ACTION' => 'GetCategoryDetails', 'DATA' => array('CategoryID' => $categoryID)), 60);
            if ($aResponse['STATUS'] == 'SUCCESS' && isset($aResponse['DATA']) && is_array($aResponse['DATA'])) {
                return $aResponse['DATA']['title_plural'];
            } else {
                return $categoryID;
            }
        } catch (MagnaException $e) {
            return $categoryID;
        }
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

    private function stringStartsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
    
}

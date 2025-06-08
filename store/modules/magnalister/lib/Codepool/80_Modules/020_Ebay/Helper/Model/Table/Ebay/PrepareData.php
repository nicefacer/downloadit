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

MLFilesystem::gi()->loadClass('Form_Helper_Model_Table_PrepareData_Abstract');
class ML_Ebay_Helper_Model_Table_Ebay_PrepareData extends ML_Form_Helper_Model_Table_PrepareData_Abstract{

    public function getPrepareTableProductsIdField() {
        return 'products_id';    
    }
    
    protected function dispatchTimeMaxField(&$aField) {       
        $aField['value'] = $this->getFirstValue($aField);
    }
    
    protected function payPalEmailAddressField(&$aField) {
        $aField['value'] = MLModul::gi()->getConfig('paypal.address');
    }
    
    protected function taxField(&$aField) {
        $aField['value'] = MLModul::gi()->getConfig('mwst');
    }
    
    protected function postalCodeField(&$aField) {
        $aField['value'] = MLModul::gi()->getConfig('postalcode');
    }
    
    protected function locationField(&$aField) {
        $aField['value'] = MLModul::gi()->getConfig('location');
    }
    
    protected function countryField(&$aField) {
        $aField['value'] = MLModul::gi()->getConfig('country');
    }
    
    protected function skuField(&$aField) {
        $aField['value'] = $this->oProduct->getMarketPlaceSku();
    }
    
    protected function quantityField(&$aField) {
        $aConf = MLModul::gi()->getStockConfig($this->getField('ListingType', 'value'));
        $aField['value'] = $this->oProduct->getSuggestedMarketplaceStock($aConf['type'], $aConf['value'],$aConf['max']);
    }
    
    protected function products_idField(&$aField) {
        $aField['value'] = $this->oProduct->get('id');
    }
    
    protected function startTimeField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }
    
    protected function titleField(&$aField) {
        $sValue = $this->getFirstValue($aField,  MLModul::gi()->getConfig('template.name'));
        $aField['value'] = $this->replaceTitle($sValue);
    }
    
    public function replaceTitle ($sTitle) {
//        $sBasePrice = $this->getField('BasePriceString', 'value');
        $aReplace = array(
            '#TITLE#'           => $this->oProduct->getName(),
            '#ARTNR#'           => $this->oProduct->getMarketPlaceSku(), 
            '#PID#'             => $this->oProduct->get('marketplaceidentid'),
//            '#BASEPRICE#'       => $sBasePrice,
        );
        $aReplace['#PRICE#'] = html_entity_decode(MLPrice::factory()->format($this->getField('StartPrice', 'value'),  MLModul::gi()->getConfig('currency')), null, 'UTF-8');
        $sTitle =  str_replace(array_keys($aReplace), array_values($aReplace), $sTitle);
        return trim($sTitle) == '' ? $this->oProduct->getName() : $sTitle;
    }
    
    protected function basePriceStringField(&$aField) {
        $fPrice = $this->getField('StartPrice', 'value');
        $aField['value'] = $this->oProduct->getBasePriceString($fPrice);
    }
    
    protected function shortBasePriceStringField(&$aField) {
        $fPrice = $this->getField('StartPrice', 'value');
        $aField['value'] = $this->oProduct->getBasePriceString($fPrice, false);
    }
    
    protected function subtitleField(&$aField) {
        $aField['value'] = strip_tags($this->getFirstValue($aField, $this->oProduct->getShortDescription()));
    }
    
    protected function getImageSize(){        
        $sSize = MLModul::gi()->getConfig('imagesize');
        $iSize = $sSize == null ? 500 : (int)$sSize;
        return $iSize;
    }
    
    public function replaceDescription($sDescription) {
        if (version_compare(PHP_VERSION, '5.2.0', '>=') && version_compare(PHP_VERSION, '5.3.6', '<=')) {
            if (!@ini_set('pcre.backtrack_limit', '10000000') || !ini_set('pcre.recursion_limit', '10000000')) {
                MLMessage::gi()->addDebug('cannot set pcre limits (ini_set)');
            }
        }
        $oProduct = $this->oProduct;
        $aReplace = $oProduct->getReplaceProperty();
        $aReplace['#PRICE#'] = MLPrice::factory()->format($this->getField('StartPrice', 'value'),  MLModul::gi()->getConfig('currency'));
        $sDescription = str_replace(array_keys($aReplace), array_values($aReplace), $sDescription);
        $iSize = $this->getImageSize();
        //images
        $aImages = $oProduct->getImages();
        $iImageIndex = 1;
        foreach ($aImages as $sPath){
            try {
                $aImage = MLImage::gi()->resizeImage($sPath, 'products', $iSize, $iSize);
                $sDescription = str_replace(
                    '#PICTURE'.(string)($iImageIndex).'#', "<img src=\"".$aImage['url']."\" style=\"border:0;\" alt=\"\" title=\"\" />",
                    preg_replace( '/(src|SRC|href|HREF|rev|REV)(\s*=\s*)(\'|")(#PICTURE'.(string)($iImageIndex).'#)/', '\1\2\3'.$aImage['url'], $sDescription)
                );
                $iImageIndex ++;
            }catch(Exception $oEx){
                //no image in fs
            }
        }
        // delete not replaced #PICTUREx#  
        $sDescription = preg_replace('/<[^<]*(src|SRC|href|HREF|rev|REV)\s*=\s*(\'|")#PICTURE\d+#(\'|")[^>]*\/*>/', '', $sDescription);
        $sDescription = preg_replace('/#PICTURE\d+#/','', $sDescription);
        // delete empty images
        $sDescription = preg_replace('/<img[^>]*src=(""|\'\')[^>]*>/i', '', $sDescription);
        return $sDescription;
    }
    public function descriptionField(&$aField) {
        $sValue = $this->getFirstValue($aField,  MLModul::gi()->getConfig('template.content'));
        $aField['value'] = $this->replaceDescription($sValue);
    }
    
    protected function asynchronousField(&$aField) {
        $aField['value'] = true;
    }
    
    public function pictureUrlField(&$aField) {
        $aImages = $this->oProduct->getImages();
        if ($this->oProduct->get('parentid') != 0) {
            $aImages = array_merge($aImages, $this->oProduct->getParent()->getImages());
        }
        foreach ($aImages as $sImage) {
            try {
                $aField['values'][$sImage] = MLImage::gi()->resizeImage($sImage, 'products', 60, 60);
            } catch(Exception $oEx) {
                //no image in fs
            }
        }
        if (isset($aField['values'])) {
            reset($aImages);
            $aField['value'] = $this->getFirstValue($aField, array_keys($aField['values']));
            $aField['value'] = empty($aField['value']) ? array_keys($aField['values']) : $aField['value'];
            $aField['value'] = (array) $aField['value'];
        }else{
            $aField['value'] = (array)$this->getFirstValue($aField, $aImages);
        }
    }
    
    protected function galleryTypeField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField, 'Gallery');
    }
     
    protected function picturePackField(&$aField) {
        $aField['value'] = 
            MLModul::gi()->getConfig('picturepack') && MLShop::gi()->addonBooked('EbayPicturePack')
            ? true
            : false
        ;
    }
    
    protected static $aCachedVariationDimensionForPictures = array();
    protected function variationDimensionForPicturesField (&$aField) {
        if (
            MLModul::gi()->getConfig('picturepack') 
            && MLShop::gi()->addonBooked('EbayPicturePack')
            && (
                !$this->oProduct instanceof ML_Shop_Model_Product_Abstract
                ||
                $this->oProduct->getVariantCount() > 1
            )
        ) {
            if ($this->oProduct instanceof ML_Shop_Model_Product_Abstract) { // only from product
                $iProductId = $this->oProduct->get('parentid');
                $oMaster = $this->oProduct;
                if(!isset(self::$aCachedVariationDimensionForPictures[$iProductId])){
                    foreach ($this->oProduct->getVariants() as $oVariant) {
                        self::$aCachedVariationDimensionForPictures[$iProductId] = array('' => MLI18n::gi()->get('ConfigFormEmptySelect'));
                        foreach ($oVariant->getVariatonDataOptinalField(array('name', 'code')) as $aVariationData) {
                            self::$aCachedVariationDimensionForPictures[$iProductId][$aVariationData['code']] = $aVariationData['name'];
                        }
                    }
                }
                $aField['values'] = self::$aCachedVariationDimensionForPictures[$iProductId];
                $this->oProduct = $oMaster;
            } else {
                $aField['values'] = array();
                foreach (MLFormHelper::getShopInstance()->getPossibleVariationGroupNames() as $iKey => $sValue) {
                    $aField['values'][$iKey] = $sValue;
                }
            }
            reset ($aField['values']);
            $aField['value'] = $this->getFirstValue($aField, MLModul::gi()->getConfig('variationdimensionforpictures'), key($aField['values']));
        }
    }

    protected static $aCachedVariationPictures = array();
    protected function variationPicturesField (&$aField) {
        if (
            MLModul::gi()->getConfig('picturepack')
            && MLShop::gi()->addonBooked('EbayPicturePack')
            && $this->oProduct instanceof ML_Shop_Model_Product_Abstract
            && $this->oProduct->getVariantCount() > 1
        ) {
            $iProductId = $this->oProduct->get('parentid');
            if(!isset(self::$aCachedVariationPictures[$iProductId])) {
                self::$aCachedVariationPictures[$iProductId] = $aField;
                $sControlValue = $this->getField('VariationDimensionForPictures', 'value');
                if (!empty($sControlValue)) {
                    $aValue = $this->getFirstValue(self::$aCachedVariationPictures[$iProductId], array());
                    foreach ($aValue as $iImageGroup => $aImageGroups) {
                        if ($iImageGroup !== $sControlValue) {
                            unset($aValue[$iImageGroup]);
                        } else {
                            foreach ($aImageGroups as $iImageGroupKey => $aImageGroup) {
                                foreach ($aImageGroup as $iImage => $sImage) {
                                    if ($sImage == 'false') {
                                        unset($aValue[$iImageGroup][$iImageGroupKey][$iImage]);
                                    }
                                }
                            }
                        }
                    }
                    foreach ($this->oProduct->getVariants() as $oVariant) {
                        foreach ($oVariant->getVariatonDataOptinalField(array('code', 'value')) as $aVariationData) {
                            if ($aVariationData['code'] == $sControlValue) {
                                foreach (array_unique($oVariant->getImages()) as $sImage) {
                                    try {
                                        self::$aCachedVariationPictures[$iProductId]['variationpictures'][$aVariationData['code']][$aVariationData['value']]['values'][$sImage] = MLImage::gi()->resizeImage($sImage, 'products', 60, 60);
                                        self::$aCachedVariationPictures[$iProductId]['variationpictures'][$aVariationData['code']][$aVariationData['value']]['title'] = $aVariationData['value'];
                                        self::$aCachedVariationPictures[$iProductId]['default'][$aVariationData['code']][$aVariationData['value']][] = $sImage;
                                    } catch (Exception $oEx) {
                                        //no image in fs
                                    }
                                }
                                self::$aCachedVariationPictures[$iProductId]['default'][$aVariationData['code']][$aVariationData['value']] = array_unique(self::$aCachedVariationPictures[$iProductId]['default'][$aVariationData['code']][$aVariationData['value']]);
                                self::$aCachedVariationPictures[$iProductId]['value'][$aVariationData['code']][$aVariationData['value']] = array_unique(
                                    array_key_exists($aVariationData['code'], $aValue) && array_key_exists($aVariationData['value'], $aValue[$aVariationData['code']]) ? $aValue[$aVariationData['code']][$aVariationData['value']] // saved
                                        : self::$aCachedVariationPictures[$iProductId]['default'][$aVariationData['code']][$aVariationData['value']] // default = all
                                    );
                                break;
                            }
                        }
                    }
                }
            }
            $aField = self::$aCachedVariationPictures[$iProductId];
        }
    }

    protected function purgePicturesField (&$aField) {
        $aField['value'] = 
            MLModul::gi()->getConfig('picturepack') 
            && MLShop::gi()->addonBooked('EbayPicturePack')
        ;
    }

    protected function conditionIdField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField, MLModul::gi()->getConfig('acondition'));
    }

    protected function startPriceField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField, $this->oProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject($this->getField('ListingType', 'value')), true));
    }
    
    protected function buyItNowPriceField(&$aField) {
        if($this->getField('ListingType', 'value') == 'Chinese'){
            $sPrice = $this->oProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject('buyitnow'));
            $aField['value'] = $this->getFirstValue($aField, $sPrice);
        }else{
            $aField['value'] = null;
        }
    }
    
    protected function currencyIdField(&$aField) {
        $aField['value'] = MLModul::gi()->getConfig('currency');
    }
    
    protected function siteField(&$aField) {
        $aField['value'] = MLModul::gi()->getConfig('site');
    }
    
    protected function primaryCategoryField(&$aField) {
        $this->_categoryField($aField);
    }
    
    protected function secondaryCategoryField(&$aField){
        $this->_categoryField($aField);
    }
    protected function storeCategoryField(&$aField){
        $this->_categoryField($aField,true);
    }
    protected function storeCategory2Field(&$aField){
        $this->_categoryField($aField,true);
    }
    protected function _categoryField(&$aField, $blStore = false) {
        $aField['value'] = $this->getFirstValue($aField, '0');
        $aTableInfo = $this->oPrepareList->getModel()->getTableInfo($aField['realname']);
        if (isset($aTableInfo['Null']) && $aTableInfo['Null'] == 'YES') {
            $aField['autooptional'] = false;
            $aField['optional']['active'] = $aField['value'] != '0';
        }
    }
    
    protected function primaryCategoryAttributesField(&$aField) {
        $this->_attributesField($aField);
    }
    
    protected function secondaryCategoryAttributesField(&$aField) {
        $this->_attributesField($aField);
    }
    
    protected function _attributesField(&$aField) {
        $aList = $this->getPrepareList()->get($aField['name'], true);
        if (count($aList) != 1 ) {
            $aList = '[]';
        }
        $aField['value'] = $this->getFirstValue($aField, $aList, '[]');
    }
    
    protected function itemSpecificsField(&$aField) {
        foreach (array(1 => 'primaryCategoryAttributes', 2=> 'secondaryCategoryAttributes') as $iKey => $sField) {
            $aCatField = $this->getField($sField, 'value');
            if (is_array($aCatField) && count($aCatField) > 0) {
                $aCatField = current($aCatField);
                if (isset($aCatField['specifics'])) {
                    $aField['value'][$iKey] = $aCatField['specifics'];
                }
            }
        }
    }
    
    protected function attributesField(&$aField) {
        foreach (array(1 => 'primaryCategoryAttributes', 2 => 'secondaryCategoryAttributes') as $iKey => $sField) {
            $aCatField = $this->getField($sField, 'value');
            if (is_array($aCatField) && count($aCatField) > 0){
                $aCatField = current($aCatField);
                if (isset($aCatField['attributes'])) {
                    $aField['value'][$iKey] = $aCatField['specifics'];
                }
            }
        }
    }
    
    protected function listingTypeField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField, key(MLModul::gi()->getListingTypeValues()));
    }
    
    protected function listingDurationField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }
    
    protected function privateListingField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField, false);
    }
    
    protected function bestOfferEnabledField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField, false);
    }
    
    protected function hitCounterField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }
    
    protected function paymentMethodsField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField, array());
    }
    
    /**
     * compatibility between old config and new prepare
     * @param array $aField
     */
    protected function _shippingField (&$aField) {
        $aField['value'] = array_values($this->getFirstValue($aField, array()));
        $aField['value'] = is_array($aField['value']) ? $aField['value'] : array();
    }
    
    protected function shippingLocalField(&$aField) {
        $this->_shippingField($aField);
    }
    
    protected function shippingInternationalField(&$aField) {
        $this->_shippingField($aField);
    }
    
    protected function shippingLocalDiscountField(&$aField){
        $aField['value']=$this->getFirstValue($aField);
    }
    
    protected function shippingInternationalDiscountField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }
    
    protected function shippingLocalProfileField(&$aField) {
        $this->_shippingProfileField($aField, MLModul::gi()->getConfig('default.shippingprofile.international'));
    }
    
    protected function shippingInternationalProfileField(&$aField) {
        $this->_shippingProfileField($aField, MLModul::gi()->getConfig('default.shippingprofile.local'));
    }
    
    protected function _shippingProfileField(&$aField, $iDefault) {
        $aField['value'] = $this->getFirstValue($aField,$iDefault);
    }
    
    protected function topPrimaryCategoryField(&$aField) {
        $this->_topCategoryField($aField);
    }
    
    protected function topSecondaryCategoryField(&$aField) {
        $this->_topCategoryField($aField);
    }
    
    protected function topStoreCategoryField(&$aField) {
        $this->_topCategoryField($aField);
    }
    
    protected function topStoreCategory2Field(&$aField) {
        $this->_topCategoryField($aField);
    }
    
    protected function _topCategoryField(&$aField) {
        $aField['value'] = $this->getField(substr($aField['name'], 3), 'value');
    }
    
    protected function variationField(&$aField) {
         $aField['value'] = $this->oProduct->getVariatonData();
    }
    
    protected function paymentInstructionsField(&$aField) {
        $aField['value'] = MLModul::gi()->getConfig('paymentinstructions');
    }
    
    protected function returnPolicyField(&$aField) {
        $aReturnPolicy = array();
        $aReturnPolicy['ReturnsAcceptedOption'] = MLModul::gi()->getConfig('returnpolicy.returnsaccepted');
        if (!isset($aReturnPolicy['ReturnsAcceptedOption']) || empty($aReturnPolicy['ReturnsAcceptedOption'])) {
            $aReturnPolicy['ReturnsAcceptedOption'] = 'ReturnsAccepted';
        }
        $aReturnPolicy['ReturnsWithinOption'] =  MLModul::gi()->getConfig('returnpolicy.returnswithin');
        if (empty($aReturnPolicy['ReturnsWithinOption'])) {
            unset($aReturnPolicy['ReturnsWithinOption']);
        }
        $aReturnPolicy['ShippingCostPaidByOption'] =  MLModul::gi()->getConfig('returnpolicy.shippingcostpaidby');
        if (empty($aReturnPolicy['ShippingCostPaidByOption'])){
            unset($aReturnPolicy['ShippingCostPaidByOption']);
        }
        $aReturnPolicy['WarrantyDurationOption'] =  MLModul::gi()->getConfig('returnpolicy.warrantyduration');
        if (empty($aReturnPolicy['WarrantyDurationOption'])) {
            $aReturnPolicy['WarrantyDurationOption'] = 'none';
        }
        $aReturnPolicy['Description'] =  MLModul::gi()->getConfig('returnpolicy.description');
        if (empty($aReturnPolicy['Description'])) {
            unset($aReturnPolicy['Description']);
        }       
        $aField['value'] = $aReturnPolicy ;
    }
    
    /**
     * 
     * @todo make config, table etc like $sKey... after improve config-form
     */
    protected function shippingDetailsField(&$aField) {
        foreach(array(
            'ShippingServiceOptions'                    => 'shippingLocal',
            'InternationalShippingServiceOption'        => 'shippingInternational',
            'ShippingDiscountProfileID'                 => 'shippingLocalProfile',
            'PromotionalShippingDiscount'               => 'shippingLocalDiscount',
            'InternationalShippingDiscountProfileID'    => 'shippingInternationalProfile',
            'InternationalPromotionalShippingDiscount'  => 'shippingInternationalDiscount',
        ) as $sKey => $sField) {
            if ($this->optionalIsActive($sField) && $this->getField($sField,'value') !== null) {
                $mValue = $this->getField($sField, 'value');
                $aField['value'][$sKey] = $mValue;
            }
        }
        if (isset($aField['value']['ShippingDiscountProfileID']) && isset($aField['value']['ShippingServiceOptions'])) {
            foreach ($aField['value']['ShippingServiceOptions'] as &$aService) {
                $aService['ShippingServiceAdditionalCost'] = MLModul::gi()->getShippingDiscountProfiles($aField['value']['ShippingDiscountProfileID']);
            }
        }
        if (isset($aField['value']['InternationalShippingDiscountProfileID'])) {
            foreach ($aField['value']['InternationalShippingServiceOption'] as &$aService) {
                $aService['ShippingServiceAdditionalCost'] = MLModul::gi()->getShippingDiscountProfiles($aField['value']['InternationalShippingDiscountProfileID']);
            }
        }
        // RateTableDetails: possibly switchable-off by config in the future
        $aField['value']['UseRateTables'] = 'true';
    }
    public function basePriceField(&$aField) {
        $aField['value'] = $this->oProduct->getBasePrice();
    }
    
    public function mpnField(&$aField) {
        $aField['value'] = $this->oProduct->getManufacturerPartNumber();
    }
    
    public function eanField(&$aField) {
        $aField['value'] = $this->oProduct->getEAN();
    }
    
    public function brandField(&$aField) {
        $aField['value'] = $this->oProduct->getModulField('productfield.brand');
    } 
    
    /**
     * in version 3 we always calculate baseprice in Plugin , because each shopsystem(e.g. Shopware and Prestashop) has different style to show baseprice
     * @param array $aField
     */
    public function doCalculateBasePriceForVariantsField(&$aField) {
        $aField['value'] = 'false';
    }    
    
    public function eBayPlusField(&$aField) {
        $mValue = $this->getFirstValue($aField, false);
        $blEbayPlusActive = true;
        $aSetting = MLModul::gi()->getEBayAccountSettings();     
        if(!isset($aSetting['eBayPlus']) || $aSetting['eBayPlus'] != "true"){
            $blEbayPlusActive = false;
        }
        if($blEbayPlusActive && $mValue !== null && $this->getField('ListingType', 'value') !='Chinese'){
            $aField['optional']['active'] = true;
            if(in_array($mValue, array("true" , "false"))){//ebay preapare table "true", "false"
                $aField['value'] = $mValue ;
            }else {//config 1,0
                $aField['value'] = $mValue ? "true" : "false";
            }
        }else{
            $aField = array();
        }
    }
    
    public function haveVariationBasePrice($aVariations){
        $iVariationMaxCount = 0;   
        $aVariationBasePrice = array();
        foreach ($aVariations as $aVariation) {
            if (!in_array($aVariation['ShortBasePriceString'], $aVariationBasePrice)) {
                $aVariationBasePrice[] = $aVariation['ShortBasePriceString'];
            }
            $iVariationMaxCount = max($iVariationMaxCount, count($aVariation['Variation']));
        }
        return (
            $iVariationMaxCount < 2 //one-dimension
            && count($aVariationBasePrice) > 1 //not all have same baseprice
        ) ;
    }

    public function manageVariationBasePrice(&$aVariation , $sIsMasterBasePrice){
        foreach ($aVariation['Variation'] as &$aVariationData) {
            if ($sIsMasterBasePrice) {
                $aVariationData['value'] = $this->basePriceReplace($aVariationData['value'], $aVariation, 50);
            } else {
                $aVariationData['value'] = $this->basePriceReplace($aVariationData['value'].' #BASEPRICE#', $aVariation, 50);
            }
            unset($aVariation['ShortBasePriceString']);
        }
    }

    
    /**
     * cuts title, but rescue #BASPRICE#
     * @param string $mValue
     * @param array $aData
     * @return string
     */
    public function basePriceReplace($mValue, $aData, $iMaxChars = 80) {
        if (isset($aData['ShortBasePriceString'])) {
            $sBasePriceString = $aData['ShortBasePriceString'];
        } elseif (isset($aData['BasePriceString'])) {
            $sBasePriceString = $aData['BasePriceString'];
        } else {
            $sBasePriceString = '';
        }
        $iBasePriceLength = strlen($sBasePriceString);
        $iBasePricePos = strpos($mValue, '#BASEPRICE#');
        if (
            $iBasePricePos !== false //have #BASEPRICE#
            && strlen($sBasePriceString) != 0 // Baseprice exists
            && $iBasePricePos + 1 + $iBasePriceLength  > $iMaxChars // baseprice is out of string
        ) {
            $mValue = str_replace('#BASEPRICE#', '', $mValue);//remove #BASEPRICE#
            $mValue = substr($mValue, 0, $iMaxChars - $iBasePriceLength).'#BASEPRICE#';// short string and add #BASEPRICE# to the end
        }
        $mValue = str_replace('#BASEPRICE#', $sBasePriceString, $mValue);
        $mValue = substr($mValue, 0, $iMaxChars);
        return $mValue;
    }
}
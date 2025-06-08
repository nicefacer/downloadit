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

class ML_Ebay_Model_Service_AddItems extends ML_Modul_Model_Service_AddItems_Abstract {
    
    protected $sVariationDimensionForPictures = null;
    
    public function setValidationMode($blValidation){
        $this->sAction=$blValidation?'VerifyAddItems':'AddItems';
        return $this;
    }
    
    protected $oCurrentProduct = null;
    protected function getProductArray() {
        $aOut=array();
        /* @var $oPrepareHelper ML_Ebay_Helper_Model_Table_Ebay_PrepareData */
        $oPrepareHelper = MLHelper::gi('Model_Table_Ebay_PrepareData');
        try {
            $aDefineMasterMaster= $this->getFieldDefineMasterMaster();
            $aDefineMasterVariant = $this->getFieldDefineMasterVariant();            
            $aDefineVariant = $this->getFieldDefineVariant();
            $aDefineComplete=array_merge($aDefineMasterMaster,$aDefineMasterVariant,$aDefineVariant);
            unset($aDefineComplete['Variation'], $aDefineComplete['VariationDimensionForPictures'], $aDefineComplete['VariationPictures']);
            // (master and variant) or (variants as master)
            $blMasterVariant = false;
            $aCatCondition = array();
            $blBreak = false;
            foreach ($this->oList->getList() as $oMaster) {
                /* @var $oMaster ML_Shop_Model_Product_Abstract */
                $aVariants=$this->oList->getVariants($oMaster);
                foreach ($aVariants as $oVariant) {
                    /* @var $oVariant ML_Shop_Model_Product_Abstract */
                    if ($this->oList->isSelected($oVariant)) {
                        $aCategoryData = $oPrepareHelper
                                ->setPrepareList(null)
                                ->setProduct($oVariant)
                                ->getPrepareData(array('PrimaryCategory' => array('optional' => array('active' => true))))
                        ;
                        $oEabyCategory = MLDatabase::factory('ebay_categories')
                                ->set('categoryid', $aCategoryData['PrimaryCategory']['value']);
                        $blMasterVariant = $oEabyCategory
                                ->variationsEnabled();
                        $aCatCondition = $oEabyCategory
                                ->getConditionValues();
                        $blBreak = true;
                        break;
                    }
                }
                if ($blBreak) {
                    break;
                }
            }
            if(empty($aCatCondition)){
                unset($aDefineMasterVariant['ConditionID']);
                unset($aDefineComplete['ConditionID']);
            }
            $blConfigUseVariation = MLModul::gi()->getConfig('usevariations') == '1';
            if(!$blConfigUseVariation) {
                //if variation is disable in configuration
                foreach ($this->oList->getList() as $oMaster) {
                    /* @var $oMaster ML_Shop_Model_Product_Abstract */
                    $this->oCurrentProduct = $oMaster;
                    $oVariant = current($this->oList->getVariants($oMaster));
                    $aOut[$oMaster->get('id')] = $this->replacePrepareData(
                            $oPrepareHelper
                                    ->setPrepareList(null)
                                    ->setProduct($oVariant)
                                    ->getPrepareData($aDefineComplete, 'value')
                    );
                }
            } else 
                if ($blMasterVariant) {
                foreach ($this->oList->getList() as $oMaster) {
                    /* @var $oMaster ML_Shop_Model_Product_Abstract */
                    $this->oCurrentProduct = $oMaster;
                    $aVariants = array();
                    foreach ($this->oList->getVariants($oMaster) as $oVariant) {
                        /* @var $oVariant ML_Shop_Model_Product_Abstract */
                        if ($this->oList->isSelected($oVariant)) {
                            $aVariants[] = $oVariant;
                        }
                    }
                    if (count($aVariants) == 1 && current($aVariants)->getVariatonData() == array()) {
                        //master only
                        $aOut[$oMaster->get('id')] = $this->replacePrepareData(
                                $oPrepareHelper
                                        ->setPrepareList(null)
                                        ->setProduct($oVariant)
                                        ->getPrepareData($aDefineComplete, 'value')
                        );
                    } else {
                        $oFirstVariant = current($aVariants);
                        $oPrepareList = MLDatabase::factory(MLModul::gi()->getMarketPlaceName().'_prepare')->getList();
                        $oPrepareList->getQueryObject()->where($oPrepareHelper->getPrepareTableProductsIdField()." = '".$oFirstVariant->get('id')."'");
                        foreach(array(
                            'Title'                 => array('optional' => array('active' => true)),
                            'Description'           => array('optional' => array('active' => true)),
                            'PictureURL'            => array('optional' => array('active' => true)),
                            'GalleryType'           => array(),
                            'Subtitle'              => array(),
                            'StartPrice'            => array('optional' => array('active' => true)),
                        ) as $sField => $aValue){
                            $aPrepared = $oPrepareList->get($sField, true);
                            if( count($aPrepared) > 0 && !in_array(null, $aPrepared, true)){
                                unset($aDefineMasterMaster[$sField]);
                                $aDefineMasterVariant[$sField] = $aValue;
                            }
                        }
                        $aMasterData = $oPrepareHelper
                                ->setPrepareList(null)
                                ->setProduct($oMaster)
                                ->getPrepareData($aDefineMasterMaster, 'value')
                        ;
                        // set first variation data to master
                        foreach ($oPrepareHelper
                                ->setPrepareList(null)
                                ->setProduct($oFirstVariant)
                                ->getPrepareData($aDefineMasterVariant, 'value')
                        as $sKey => $mValue) {
                            $aMasterData[$sKey] = $mValue;
                        }
                        $aMasterData['Variations'] = array();
                        $aMasterData['Quantity'] = 0;
                        foreach ($aVariants as $oVariant) {
                            /* @var $oVariant ML_Shop_Model_Product_Abstract */
                            $aPreparedVariant = $this->replacePrepareData($oPrepareHelper
                                ->setPrepareList(null)
                                ->setProduct($oVariant)
                                ->getPrepareData($aDefineVariant, 'value')
                            );
                            $aMasterData['Variations'][] = $aPreparedVariant;
                            $aMasterData['Quantity'] += (int)$aPreparedVariant['Quantity'];//when Product have several variants , master quantity is sum of all variants quantity
                        }
                        if (count($aMasterData['Variations']) == 1 && $aMasterData['Variations'][0]['Variation'] == array()) {//is master
                            $aMasterData = array_merge($aMasterData, $aMasterData['Variations'][0]);
                            unset($aMasterData['Variation'], $aMasterData['Variations'], $aMasterData['VariationPictures'], $aMasterData['VariationDimensionForPictures']);
                        }else{
                            if ($oPrepareHelper->haveVariationBasePrice($aMasterData['Variations'])) {// => so we dont show it in main-title
                                unset($aMasterData['BasePriceString']);
                            }
                            foreach ($aMasterData['Variations'] as &$aVariation) {
                                $oPrepareHelper->manageVariationBasePrice($aVariation ,isset($aMasterData['BasePriceString']));
                                $aVariation = $this->replacePrepareData($aVariation);
                            }
                        }
                        $aMasterData = $this->replacePrepareData($aMasterData);
                        unset($aMasterData['BasePriceString']);
                        $aOut[$oMaster->get('id')] = $aMasterData;
                    }
                }
            } else {//all variants transfered as single master article
                foreach ($this->oList->getList() as $oMaster) {
                    $this->oCurrentProduct = $oMaster;
                    /* @var $oMaster ML_Shop_Model_Product_Abstract */
                    $aListOfVariant = $this->oList->getVariants($oMaster);
                    $iVariantCount = count($aListOfVariant);
                    foreach ($aListOfVariant as $oVariant) {
                        /* @var $oVariant ML_Shop_Model_Product_Abstract */
                        if ($this->oList->isSelected($oVariant)) {
                            $oPrepareHelper
                                ->setPrepareList(null)
                                ->setProduct($oVariant);
                            if($iVariantCount > 1) {
                                $aDefineComplete['Title']['value'] = $oPrepareHelper->replaceTitle(MLModul::gi()->getConfig('template.name'));
                            }
                            $aOut[$oVariant->get('id')] = $this->replacePrepareData(
                                $oPrepareHelper->getPrepareData($aDefineComplete, 'value')
                            );
                            unset($aOut[$oVariant->get('id')]['BasePriceString']);
                        }
                    }
                }
            }
//            MLMessage::gi()->addDebug('aOut',$aOut);
//            die;
        } catch (Exception $oEx) {
            echo  $oEx->getMessage();
        }
//        echo '<textarea>'.json_encode($aMasterProducts).'</textarea>';
//        echo '<textarea>'.  var_export($aMasterProducts,1).'</textarea>';
//        new dBug($aOut);
//        die;
        return $aOut;
    }
    
    protected function checkQuantity() {
        if($this->sAction=='VerifyAddItems'){
            return true;
        }
        return parent::checkQuantity();
    }
    
    protected function replacePrepareData($aData){
        foreach ($aData as $sKey=>$mValue){
            if($mValue===null){
                unset($aData[$sKey]);
            }else{
                if(method_exists($this, 'replace'.$sKey)){
                    $aData[$sKey]=$this->{'replace'.$sKey}($mValue, $aData);
                }
            }
        }
        return $aData;
    }
    protected function _getImageUrl($mValue){
        $sSize = MLModul::gi()->getConfig('imagesize');
        $iSize = $sSize == null ? 500 : (int)$sSize;
        $aOut = array();
        foreach (is_array($mValue) ? $mValue : array($mValue) as $sImage) {
            try {
                $aImage = MLImage::gi()->resizeImage($sImage, 'products', $iSize, $iSize);
                if (MLModul::gi()->getConfig('picturepack') && MLShop::gi()->addonBooked('EbayPicturePack')) {
                    $aOut[] = $aImage['url'];
                } else {
                    $aOut[] = str_replace('https:', 'http:', $aImage['url']);
                }
            } catch(Exception $oEx) {//no image
            }
        }
        return is_array($mValue) ? $aOut : current($aOut);
    }
    
    protected function replaceVariationDimensionForPictures ($mValue, $aData) {
        $this->sVariationDimensionForPictures = $mValue;
        $aSearch = MLFormHelper::getShopInstance()->getPossibleVariationGroupNames();
        return array_key_exists($mValue, $aSearch) ? $aSearch[$mValue] : null;
            
    }
    
    protected function replaceVariationPictures ($mValue, $aData) {
        if ($this->sVariationDimensionForPictures !== null) {
            $sVariationDimension = $this->sVariationDimensionForPictures;
        } elseif (is_array($aData) && array_key_exists('VariationDimensionForPictures', $aData)) {
            $sVariationDimension = $aData[$aData];
        } else {
            return null;
        }
        if (is_array($mValue) && array_key_exists($sVariationDimension, $mValue)){
            $aOut = array();
            foreach ($mValue[$sVariationDimension] as $sKey => $aImages) {
                if (!empty($aImages)) {
                    $aOut[$sKey] = $this->replacePictureUrl($aImages, $aData);
                }
            }
            return $aOut;
        } else {
            return null;
        }
    }
    
    protected function replacePictureUrl($mValue, $aData){
        if($this->sAction=='VerifyAddItems'){
            if (is_array($mValue)) {
                foreach ($mValue as &$sLink) {
                    $sLink = 'http://example.com/test.png';
                }
                return $mValue;
            } else {
                return 'http://example.com/test.png';
            }
        }else{
            return $this->_getImageUrl($mValue);
        }
            
    }
    protected function replaceQuantity($mValue, $aData){
        if($this->sAction=='VerifyAddItems'){
            return 1;
        }else{
            return $mValue;
        } 
    }
    
    protected function replaceShippingDetails($mValue, $aData) {
        if (is_array($mValue['ShippingServiceOptions']) && count($mValue['ShippingServiceOptions'])>0) {
            foreach ($mValue['ShippingServiceOptions'] as &$aService) {            
                if($aService['ShippingServiceCost'] == '=GEWICHT'){
                    $aWeight = $this->oCurrentProduct->getWeight();
                    $aService['ShippingServiceCost'] = empty($aWeight) ? '0':(string)$aWeight['Value'];
                }
                $aService['ShippingServiceCost'] = MLPrice::factory()->unformat( $aService['ShippingServiceCost']);
            }
        }
        if (isset($mValue['InternationalShippingServiceOption']) && is_array($mValue['InternationalShippingServiceOption']) && count($mValue['InternationalShippingServiceOption'])>0) {
            foreach ($mValue['InternationalShippingServiceOption'] as $iService => &$aService) {
                if (
                    empty($aService['ShippingService']) // config value = no-shipping
                    || !isset($aService['ShipToLocation']) // no location
                    || empty($aService['ShipToLocation']) // no location
                ) {
                    unset($mValue['InternationalShippingServiceOption'][$iService]);
                } else {
                    $aService['ShippingServiceCost'] = MLPrice::factory()->unformat( $aService['ShippingServiceCost']);
                }
            }
        }
        if (!isset($mValue['InternationalShippingServiceOption']) || empty($mValue['InternationalShippingServiceOption'])) {
            unset($mValue['InternationalShippingServiceOption']);
            unset($mValue['InternationalPromotionalShippingDiscount']);
            unset($mValue['InternationalShippingDiscountProfileID']);
        }
        return $mValue;
    }
    
    protected function replaceStartPrice($mValue, $aData){
        return MLPrice::factory()->unformat($mValue);
    }
    
    /**
     * cuts title, but rescue #BASPRICE#
     * @param string $mValue
     * @param array $aData
     * @return string
     */
    protected function replaceTitle($mValue, $aData, $iMaxChars = 80) {        
        /* @var $oPrepareHelper ML_Ebay_Helper_Model_Table_Ebay_PrepareData */
        $oPrepareHelper = MLHelper::gi('Model_Table_Ebay_PrepareData');
        return $oPrepareHelper->basePriceReplace($mValue, $aData, $iMaxChars);
    }
    
    /**
     * ebay api dont support uploaditems yet
     * @return \ML_Ebay_Model_Service_AddItems
     */
    protected function uploadItems() {
        return $this;
    }
    
    protected function getFieldDefineMasterMaster(){
        $aRetrun = array(
            'Title'                 => array('optional' => array('active' => true)),
            'SKU'                   => array('optional' => array('active' => true)),
            'Description'           => array('optional' => array('active' => true)),
            'PictureURL'            => array('optional' => array('active' => true)),
            'GalleryType'           => array(),
            'Subtitle'              => array(),
            'StartTime'             => array(),
            'StartPrice'            => array('optional' => array('active' => true)),
            'Quantity'              => array('optional' => array('active' => true)),
            'BasePrice'             => array('optional' => array('active' => true)),
            'BasePriceString'       => array('optional' => array('active' => true)),
        );
        
        if (MLShop::gi()->addonBooked('EbayProductIdentifierSync') && MLModul::gi()->getConfig('syncproperties')) {
            $aRetrun += array(
                'Brand'            => array('optional' => array('active' => true)),
                'MPN'              => array('optional' => array('active' => true)),
                'EAN'              => array('optional' => array('active' => true)),
            );
        }
        return $aRetrun;
    }
    
    protected function getFieldDefineMasterVariant(){
        $aRetrun = array(
            'BestOfferEnabled'      => array('optional' => array('active' => true)),
            'HitCounter'            => array('optional' => array('active' => true)),
            'PrimaryCategory'       => array('optional' => array('active' => true)),
            'ConditionID'           => array('optional' => array('active' => true)),
            'SecondaryCategory'     => array(),
            'StoreCategory'         => array(),
            'StoreCategory2'        => array(),
            'ItemSpecifics'         => array('optional' => array('active' => true)),
            'Attributes'            => array('optional' => array('active' => true)),
            'ListingType'           => array('optional' => array('active' => true)),
            'ListingDuration'       => array('optional' => array('active' => true)),
            'Country'               => array('optional' => array('active' => true)),
            'Site'                  => array('optional' => array('active' => true)),
            'currencyID'            => array('optional' => array('active' => true)),
            'Location'              => array('optional' => array('active' => true)),
            'PostalCode'            => array('optional' => array('active' => true)),
            'Tax'                   => array('optional' => array('active' => true)),
            'PaymentMethods'        => array('optional' => array('active' => true)),
            'ShippingDetails'       => array('optional' => array('active' => true)),
            'PayPalEmailAddress'    => array('optional' => array('active' => true)),
            'DispatchTimeMax'       => array('optional' => array('active' => true)),
            'PaymentInstructions'   => array('optional' => array('active' => true)),
            'ReturnPolicy'          => array('optional' => array('active' => true)),
            'doCalculateBasePriceForVariants'          => array('optional' => array('active' => true)),
            'eBayPlus'          => array('optional' => array('active' => true)),
            'PurgePictures'         => array(),
            'VariationDimensionForPictures' => array('optional' => array('active' => true)),
            'VariationPictures'     => array('optional' => array('active' => true)),
            'Asynchronous'          => array('optional' => array('active' => true)),
            'PicturePack'           => array('optional' => array('active' => true)),
        );
        return $aRetrun;           
    }
    
    protected function getFieldDefineVariant(){
        $aRetrun = array(
            'StartPrice'            => array('optional' => array('active' => true)),
            'SKU'                   => array('optional' => array('active' => true)),
            'Quantity'              => array('optional' => array('active' => true)),
            'Variation'             => array('optional' => array('active' => true)),
            'BasePrice'             => array('optional' => array('active' => true)),
            'ShortBasePriceString'  => array('optional' => array('active' => true)),
        );
        
        if (MLShop::gi()->addonBooked('EbayProductIdentifierSync') && MLModul::gi()->getConfig('syncproperties')) {
            $aRetrun += array(
                'EAN'              => array('optional' => array('active' => true)),
            );
        }
        return $aRetrun;
    }
    
}

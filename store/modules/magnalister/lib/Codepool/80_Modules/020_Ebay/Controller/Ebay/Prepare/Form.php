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
 * (c) 2010 - 2015 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
MLFilesystem::gi()->loadClass('Form_Controller_Widget_Form_PrepareAbstract');
class ML_Ebay_Controller_Ebay_Prepare_Form extends ML_Form_Controller_Widget_Form_PrepareAbstract {

    protected $aParameters = array('controller');

    public function __construct() {
        parent::__construct();
        if ($this->oSelectList->getCountTotal() == 1 && $this->oProduct->get('parentid') != 0 ) {
            $this->oProduct = $this->oProduct->getParent();
            $this->oPrepareHelper->setProduct($this->oProduct);
        } 
    }
    protected function getSelectionNameValue(){
        return 'match';
    }
    protected function triggerBeforeFinalizePrepareAction() {
        $oPreparedProduct = current($this->oPrepareList->getList());
        if (is_object($oPreparedProduct)) {
            $iParentId = null;
            foreach($this->oPrepareList->getList() as $oVariant){                
                $sProductsId = $oVariant->get($this->oPrepareHelper->getPrepareTableProductsIdField());
                $oProduct = MLProduct::factory()->set('id', $sProductsId);
                if($iParentId !== null && ($iParentId != $oProduct->get('parentid') || !MLDatabase::factory('ebay_categories')->set('categoryid', $oVariant->get('PrimaryCategory'))->variationsEnabled())){
                    break;
                } 
                $oProductList = MLProductList::gi('generic')->addVariant($oProduct);
                $iParentId = $oProduct->get('parentid');
            }
            $oService = MLService::getAddItemsInstance()->setValidationMode(true)->setProductList($oProductList);
            try {
                $oService->execute();
            } catch (Exception $oEx) {
//            echo $oEx->getMessage();
            }
            if ($oService->haveError()) {
                $this->oPrepareList->set('verified', 'ERROR');
            } else {
                $this->oPrepareList->set('verified', 'OK');
            }
            return !$oService->haveError(); //if service have error, no redirect
        } else {
            MLMessage::gi()->addDebug("One of products is not existed , please try again");
            return false;
        }
    }
    
    protected function priceContainerField(&$aField) {
        $aField['type'] = 'ajax';
        $aField['ajax'] = array(
            'selector' => '#' . $this->getField('listingType', 'id'),
            'trigger' => 'change',
            'field' => array(
                'type' => 'ebay_pricecontainer',
            )
        );
    }

    protected function listingDurationField(&$aField) {
        $aField['type'] = 'ajax';
        $sListingType = $this->getField('listingType', 'value');
        $aField['ajax'] = array(
            'selector' => '#' . $this->getField('listingType', 'id'),
            'trigger' => 'change',
            'field' => array(
                'type' => 'select',
                'values' => MLModul::gi()->getListingDurations($sListingType),
            )
        );
        if(empty($aField['value'])||  MLHttp::gi()->isAjax()){//it is not in prepareData helper class because additems is ajax too and there it will be ever default value
            $aField['value']= MLModul::gi()->getConfig(strtolower($sListingType)== 'chinese'?'chinese.duration':'fixed.duration');
        }
    }

    protected function startTimeField(&$aField) {
        $aField['type']='optional';
        $aField['optional']['field']['type'] = 'datetimepicker';
    }

    protected function titleField(&$aField) {
        $aField['default'] = $this->oPrepareHelper->replaceTitle(MLModul::gi()->getConfig('template.name'));
        $aField['type'] = 'string';
        $aField['maxlength'] = 80;
    }

    protected function subtitleField(&$aField) {
        $aField['optional']['field']['type'] = 'string';
    }

    public function descriptionField(&$aField) {
        $aField['type'] = 'wysiwyg';
        $aField['default'] = $this->oPrepareHelper->replaceDescription(MLModul::gi()->getConfig('template.content'));
    }

    protected function conditionIdField(&$aField) {
        $aField['type'] = 'ajax';
        $aField['ajax'] = array(
            'selector' => '#' . $this->getField(array('name' => 'PrimaryCategory','hint' => array('template' => 'ebay_categories')), 'id'),
            'trigger' => 'change',
            'field' => array(
                'type' => 'select',
                'hint' => array(
                    'template' => 'text'
                )
            )
        );
        $iCategoryId =  $this->getField(array('name' => 'PrimaryCategory','hint' => array('template' => 'ebay_categories')), 'value');
        
        if ($iCategoryId != null && $iCategoryId != '0') {
            $iCatId = (int) $iCategoryId;
            $aField['values'] = MLDatabase::factory('ebay_categories')->set('categoryid', $iCatId)->getConditionValues();
            if (empty($aField['values'])){
                $aField['ajax']['field']['type'] = 'information';
                $aField['value'] = MLI18n::gi()->ml_ebay_no_conditions_applicable_for_cat;
            }
        } else {
            $aField['values'] = MLModul::gi()->getConditionValues();
        }
    }

    protected function startPriceField(&$aField) {
        $aField['type'] = 'ebay_pricecontainer_fixed';
        $aField['autooptional']=false;
        $aField['checkajax']=false;
        $aField['ebay_pricecontainer_fixed'] = array(
            'field' => array(
                'type' => 'string'
            )
        );
    }

    protected function buyItNowPriceField(&$aField) {
        if ($this->getField('listingType', 'value') == 'Chinese') {
            $oActive = json_decode(MLModul::gi()->getConfig('chinese.buyitnow.price.active'));
            $aField['type'] = 'ebay_pricecontainer_buyitnow';
            $aField['autooptional']=false;
            $aField['checkajax']=false;
            $aField['ebay_pricecontainer_buyitnow']['field'] = array(
                'type' => 'optional',
                'optional' => array(
                    'field' => array(
                        'type' => 'string',
                    )
                )
            );
        } else {
            $aField['value'] = null;
        }
    }

    protected function siteField(&$aField) {
        $aField['type']='readonly';
        $aField['value'] = MLModul::gi()->getConfig('site');
    }

    protected function privateListingField(&$aField) {
        $aField['type'] = 'bool';
    }

    protected function bestOfferEnabledField(&$aField) {
        $aField['type'] = 'bool';
    }    
    
    protected function ebayPlusField(&$aField) {
        $aField['type'] = 'bool';
        $aField['autooptional'] = false;        
        $aField['disabled'] = true;
        
        $aSetting = MLModul::gi()->getEBayAccountSettings();
        if(isset($aSetting['eBayPlus']) && $aSetting['eBayPlus'] == "true"){
            $aField['disabled'] = false;
        }
        if(isset($aField['value']) && $aField['value'] === "true"){
            $aField['value'] = true;
        }else{
            $aField['value'] = false;
        }
    }

    protected function pictureUrlField(&$aField) { 
        if (MLModul::gi()->getConfig('picturepack')) {
            $aField['type'] = 'imagemultipleselect';
        } else {
            $aField['type'] = 'imageselect';
            $aField['asarray'] = true;
        }
    }
    
    protected function galleryTypeField(&$aField) {
        MLHelper::gi('model_table_Ebay_ConfigData')->galleryTypeField($aField);
    }
    
    protected function VariationDimensionForPicturesField(&$aField) {
        if (
            MLModul::gi()->getConfig('picturepack')
            && MLShop::gi()->addonBooked('EbayPicturePack')
            && (
                !$this->oProduct instanceof ML_Shop_Model_Product_Abstract
                ||
                $this->oProduct->getVariantCount() > 1
            )
        ) {
            $aField['type'] = 'select';
        }
    }
    protected function VariationPicturesField (&$aField) {
        if (
            MLModul::gi()->getConfig('picturepack')
            && MLShop::gi()->addonBooked('EbayPicturePack')
            && $this->oProduct instanceof ML_Shop_Model_Product_Abstract
            && $this->oProduct->getVariantCount() > 1
        ) {
            $sControlValue = $this->getField('VariationDimensionForPictures', 'value');
            $aField['autooptional'] = false;
            if (MLHttp::gi()->isAjax()) {
                if (empty($sControlValue)) {
                    $aField['type'] = 'ebay_variationpictures';//empty field
                } else {
                    $aField['type'] = 'optional';
                    $aField['checkajax'] = false;
                    $aField['optional'] = array('field' => array('type' => 'ebay_variationpictures'));
                }
            } else {
                $aField['type'] = 'ajax';
                $aField['ajax'] = array(
                    'selector' => '#'.$this->getField('VariationDimensionForPictures', 'id'),
                    'trigger' => 'change',
                );
                if (!empty($sControlValue)) {
                    $aField['ajax']['field'] = array(
                        'type' => 'optional',
                        'optional' => array(
                            'field' => array(
                                'type' => 'ebay_variationpictures',
                                'name' => 'field[VariationPictures]',
                            )
                        )
                    );
                }
            }
        }
    }


    protected function primaryCategoryField(&$aField) {
        $this->_categoryField($aField);
    }

    protected function secondaryCategoryField(&$aField) {
        $this->_categoryField($aField);
    }

    protected function storeCategoryField(&$aField) {
        $this->_categoryField($aField, true);
    }

    protected function storeCategory2Field(&$aField) {
        $this->_categoryField($aField, true);
    }

    protected function _categoryField(&$aField, $blStore = false) {
        $aField['type'] = 'ebay_categories';
        $aField['ebay_categories'] = array(
            'field' => array(
                'type' => 'select',
            )
        );
        $aAjaxData = $this->getAjaxData();
        if ($aAjaxData !== null || $aField['realname'] == 'primarycategory') {
            require_once MLFilesystem::getOldLibPath('php/modules/ebay/ebayFunctions.php');
            require_once MLFilesystem::getOldLibPath('php/modules/ebay/classes/eBayCategoryMatching.php');
            $oCategories = new eBayCategoryMatching();
            $aField['ebay_categories']['oCategory'] = $oCategories;
        }
        if ($aAjaxData === null) {
            require_once MLFilesystem::getOldLibPath('php/modules/ebay/classes/ebayTopTen.php');
            $oTop = new EbayTopTen();
            $aField['ebay_categories']['field']['values'] = $oTop->getTopTenCategories('top' . $aField['name']);
            $aField['ebay_categories']['field']['values'] = array(0 => '..') + $aField['ebay_categories']['field']['values'];
            if (!in_array((int) $aField['value'], $aField['ebay_categories']['field']['values'])) {
                $aField['ebay_categories']['field']['values'][(int) $aField['value']] = MLDatabase::factory('ebay_categories')
                        ->set('storecategory', $blStore)
                        ->set('categoryid', (int) $aField['value'])
                        ->getCategoryPath()
                ;
            }
        }
    }

    protected function primaryCategoryAttributesField(&$aField) {
        $this->_attributesField($aField);
    }

    protected function secondaryCategoryAttributesField(&$aField) {
        $this->_attributesField($aField);
    }

    protected function _attributesField(&$aField) {
        $aAjaxData = $this->getAjaxData();
        if ($aAjaxData == null) {
            $iCatId = $this->getField(substr($aField['name'], 0, -10), 'value');
        } else {
            $iCatId = $aAjaxData['CategoryID'];
        }
        $iCatId = (int) $iCatId;
        $aField['type'] = 'ebay_attributes';
        if ($aAjaxData !== null) {
            include_once MLFilesystem::getOldLibPath('php/modules/ebay/ebayFunctions.php');
            $aField['ebay_attributes'] = array(
                'categoryId' => $iCatId,
            );
        }
        if(isset($aField['value'][$iCatId])&&$iCatId!=0){
            foreach(array_keys($aField['value'][$iCatId]) as $sKey){
                if(!in_array($sKey, array('attributes','specifics'))){
                    unset($aField['value'][$iCatId][$sKey]);
                }
            }
        }else{
            $aField['value']=array();
        }
    }

    protected function listingTypeField(&$aField) {
        $aField['values']=  MLModul::gi()->getListingTypeValues();
        $aField['type'] = 'select';
    }

    protected function hitCounterField(&$aField) {
        $aField['values']= MLModul::gi()->getHitcounterValues();
        $aField['type'] = 'select';
    }

    protected function paymentMethodsField(&$aField) {
        $aField['type'] = 'multipleSelect';
        $aField['values']=MLModul::gi()->getPaymentOptions();
    }

    protected function shippingLocalField(&$aField) {
        $aField['type'] = 'duplicate';
        $aField['duplicate']['field']['type'] = 'ebay_shippingcontainer_shipping';

        $aField['values'] = MLModul::gi()->getLocalShippingServices();
    }

    protected function shippingInternationalField(&$aField) {
        $aField['type'] = 'duplicate';
        $aField['autooptional']=false;
        $aField['duplicate']['field']['type'] = 'ebay_shippingcontainer_shipping';
        $aField['values'] = array_merge(array('' => MLI18n::gi()->get('sEbayNoInternationalShipping')), MLModul::gi()->getInternationalShippingServices());
        $aField['locations'] = MLModul::gi()->getInternationalShippingLocations();
    }

    protected function shippingLocalDiscountField(&$aField) {
        $aField['type'] = 'bool';
    }

    protected function shippingInternationalDiscountField(&$aField) {
        $aField['type'] = 'bool';
    }

    protected function shippingLocalProfileField(&$aField) {
        $this->_shippingProfileField($aField, MLModul::gi()->getConfig('default.shippingprofile.international'));
    }

    protected function shippingInternationalProfileField(&$aField) {
        $this->_shippingProfileField($aField, MLModul::gi()->getConfig('default.shippingprofile.local'));
    }

    protected function _shippingProfileField(&$aField, $iDefault) {
        $aField['type'] = 'optional';
        $aField['optional']['field']['type'] = 'select';
        $aProfiles = array();
        $oI18n = MLI18n::gi();
        $oPrice = MLPrice::factory();
        $sCurrency = MLModul::gi()->getConfig('currency');
        if (isset($aField['i18n'])) {
            foreach (MLModul::gi()->getShippingDiscountProfiles() as $sProfil => $aProfil) {
                $aProfiles[$sProfil] = $oI18n->replace(
                    $aField['i18n']['option'], array(
                        'NAME' => $aProfil['name'],
                        'AMOUNT' => $oPrice->format($aProfil['amount'], $sCurrency)
                    )
                );
            }
        }
        $aField['values'] = $aProfiles;
    }

    protected function dispatchTimeMaxField(&$aField) {
        $aField['default'] = $this->oPrepareHelper->getFromConfig($aField['realname']);
        $aField['values'] = MLI18n::gi()->ebay_configform_prepare_dispatchtimemax_values ;
    }
    
}

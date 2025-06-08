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
MLFilesystem::gi()->loadClass('Shop_Model_ConfigForm_Shop_Abstract');

class ML_Prestashop_Model_ConfigForm_Shop extends ML_Shop_Model_ConfigForm_Shop_Abstract {

    public function getDescriptionValues() {
        $aLangs = array();
        foreach (Language::getLanguages(true) as $aRow) {
            $aLangs[$aRow['id_lang']] = $aRow['name'];
        }

        return $aLangs;
    }
    
    public function getShopValues() {
        $aShops = array();
        foreach (Shop::getShops(true) as $aRow) {
            $aShops[$aRow['id_shop']] = $aRow['name'];
        }

        return $aShops;
    }

    public function getCustomerGroupValues($blNotLoggedIn = false) {
        $aGroupsName = array();
        foreach (Group::getGroups(_LANG_ID_) as $aRow) {
            $aGroupsName[$aRow['id_group']] = $aRow['name'];
        }

        return $aGroupsName;
    }

    public function getOrderStatusValues() {
        $aOrderStatesName = array();
        foreach (OrderState::getOrderStates(_LANG_ID_) as $aRow) {
            $aOrderStatesName[$aRow['id_order_state']] = $aRow['name'];
        }

        return $aOrderStatesName;
    }

    public function getEan() {
        return array(
            '' => MLI18n::gi()->get('ConfigFormEmptySelect'),
            'ean13' => 'EAN', 
            'upc' => 'UPC'
        );
    }

    public function getManufacturer() {
        return array(
            '' => MLI18n::gi()->get('ConfigFormEmptySelect'),
            'manufacturer_name' => 'Manufacturer Name',
            'id_manufacturer' => 'Manufacturer Id'
        );
        /*
        $aOut = array();
        foreach (Manufacturer::getManufacturers() as $aManufacturer ) {
            if ($aManufacturer['id_manufacturer'] != '') {
                $aOut[$aManufacturer['id_manufacturer']] = array (
                    'value' => $aManufacturer['id_manufacturer'] ,
                    'label' => $aManufacturer['name'] ,
                ) ;
            }
        }
        return $aOut;
        */
    }

    public function getManufacturerPartNumber() {
        return $this->getProductFields();
    }
    
    public function getBrand() {
        return $this->getProductFields();
    }
    
    /**
     * get all the available fields for products and combination 
     */
    protected function getProductFields() {
        $oProduct = new Product();
        $oAttribute = new Combination();
        $aFields = array_merge(
                get_object_vars($oProduct), get_object_vars($oAttribute)
        );
        ksort($aFields);
        foreach ($aFields as $sField => &$sValue) {
            $sValue = ucfirst(str_replace('_', ' ', $sField));
        }
        $aFields = array('' => MLI18n::gi()->get('ConfigFormEmptySelect')) + $aFields;
        $aFeatures = Feature::getFeatures(Context::getContext()->language->id);
        $sFeaturesTranlation = Translate::getAdminTranslation('Features', 'AdminProducts');
        foreach ($aFeatures as $aFeature) {
            $aFields['product_feature_' . $aFeature['id_feature']] = $aFeature['name'] . ' (' . $sFeaturesTranlation . ')';
        }
        return $aFields;
    }

    /**
     * Gets list of custom attributes from shop.
     *
     * @return array Collection of all attributes
     */
    public function getAttributeList() {
        // TODO: implement this for Prestashop if aplicable.
        return array();
    }

    /**
     * Gets the list of product attributes prefixed with attribute type.
     *
     * @return array Collection of prefixed attributes
     */
    public function getPrefixedAttributeList() {
        $aAttributes = array('' => MLI18n::gi()->get('ConfigFormEmptySelect'));

        foreach (AttributeGroup::getAttributesGroups(_LANG_ID_) as $aRow) {
            $aAttributes['a_' . $aRow['id_attribute_group']] = $aRow['name'];
        }

        foreach (Feature::getFeatures(_LANG_ID_) as $aRow) {
            $aAttributes['f_' . $aRow['id_feature']] = $aRow['name'];
        }

        return $aAttributes;
    }

    /**
     * Gets the list of product attributes that have options (displayed as dropdown or multiselect fields).
     *
     * @return array Collection of attributes with options
     */
    public function getAttributeListWithOptions() {
        $aAttributes = array('' => MLI18n::gi()->get('ConfigFormEmptySelect'));
        foreach (AttributeGroup::getAttributesGroups(_LANG_ID_) as $aRow) {
            $aAttributes[$aRow['id_attribute_group']] = $aRow['name'];
        }
        
        return $aAttributes;
    }

    /**
     * Gets the list of product attribute values.
     * If $iLangId is set, use translation for attribute options' labels.
     *
     * @return array Collection of attribute values
     */
    public function getAttributeOptions($sAttributeCode, $iLangId = _LANG_ID_) {
        $aAttributes = array();
        $sAttributeCode = filter_var($sAttributeCode, FILTER_SANITIZE_NUMBER_INT);
		foreach (AttributeGroup::getAttributes($iLangId, $sAttributeCode) as $aRow) {
            $aAttributes[$aRow['id_attribute']] = $aRow['name'];
        }

        return $aAttributes;
    }

    /**
     * Gets the list of product attribute values.
     * If $iLangId is set, use translation for attribute options' labels.
     *
     * @return array Collection of attribute values
     */
    public function getPrefixedAttributeOptions($sAttributeCode, $iLangId = _LANG_ID_) {
        $aAttributes = array();
        $aAttributeCode = explode('_', $sAttributeCode);

        if ($aAttributeCode[0] === 'a') {
            foreach (AttributeGroup::getAttributes($iLangId, $aAttributeCode[1]) as $aRow) {
                $aAttributes[$aRow['id_attribute']] = $aRow['name'];
            }
        } else if ($aAttributeCode[0] === 'f' && $this->isFeatureCustom($aAttributeCode[1]) === false) {
            foreach ($this->getFeatureOptions($aAttributeCode[1], $iLangId) as $aRow) {
                $aAttributes[$aRow['id_feature_value']] = $aRow['value'];
            }
        }
        
        return $aAttributes;
    }

    public function getCurrency() {
        MLShop::gi()->getCurrency()->getList();
    }

    /**
     * Gets tax classes for product.
     *
     * @return array Tax classes
     * array(
     *  array(
     *      'value' => string
     *      'label' => string
     *  )
     * )
     */
    public function getTaxClasses() {
        $aTaxGroup = array();
        foreach (TaxRulesGroup::getTaxRulesGroupsForOptions() as $iId => $sName) {
            $aTaxGroup[] = array(
                'value'=> $sName['id_tax_rules_group'] ,
                'label'=> $sName['name']
            );
        }
        return $aTaxGroup;
    }

    private function getFeatureOptions($iFeatureId, $iLangId = _LANG_ID_) {
        return MLDatabase::factorySelectClass()
                        ->select(array('p.id_feature_value', 'l.value'))
                        ->from(_DB_PREFIX_ . 'feature_product', 'p')
                        ->join(array(_DB_PREFIX_ . 'feature_value_lang', 'l', 'p.id_feature_value = l.id_feature_value', ML_Database_Model_Query_Select::JOIN_TYPE_LEFT))
                        ->where("l.id_lang = $iLangId and p.id_feature = $iFeatureId")
                        ->getResult();
    }

    private function isFeatureCustom($iFeatureId) {
        $result = MLDatabase::factorySelectClass()
                ->select('custom')
                ->from(_DB_PREFIX_ . 'feature_product', 'p')
                ->join(array(_DB_PREFIX_ . 'feature_value', 'v', 'p.id_feature_value = v.id_feature_value', ML_Database_Model_Query_Select::JOIN_TYPE_LEFT))
                ->where("p.id_feature = $iFeatureId")
                ->getResult();

        return isset($result[0]['custom']) ? (int)$result[0]['custom'] === 1 : false;
	}
        
    public function manipulateForm(&$aForm) {
        try{
            parent::manipulateForm($aForm);
            MLModul::gi();//throw excepton if we are not in marketplace configuration

//                if(isset($aForm['account'])){
//                    $aForm['account']['fields']['orderimport.shop'] =  array
//                        (
//                            'name' => 'orderimport.shop',
//                            'type' => 'select',
//                            'i18n'=>array(
//                                'label' => 'Shop'
//                            )
//                        );
//                }
            if(isset($aForm['importactive'])){
                foreach ($aForm['importactive']['fields'] as $sKey => $aField){
                    if($aField['name'] == 'orderimport.shop'){
                        unset($aForm['importactive']['fields'][$sKey]);
                    }
                }
            }
        } catch (Exception $ex) {

        }
    }

    public function getPossibleVariationGroupNames () {
        return $this->getAttributeListWithOptions();
    }
    
}

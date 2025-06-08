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

class ML_Magento_Model_ConfigForm_Shop extends ML_Shop_Model_ConfigForm_Shop_Abstract {

    protected $aAttributeList = null;
    protected $aAttributeOptions = null;

    /**
     * Gets list of languages to be selected for description field.
     * Pulls list of all stores and pulles language from each since Magento does not have separated list of languages.
     * 
     * @return array Collection of languages for Description field in config.
     */
    public function getDescriptionValues() {
        $aOut = array();
        foreach (Mage::app()->getWebsites() as $oWebsite) {
            $sLabel = $oWebsite->name;
            foreach ($oWebsite->getGroups() as $oGroup) {
                $sLabel.=' - ' . $oGroup->name;
                foreach ($oGroup->getStores() as $oView) {
                    $sLabel.=' - ' . $oView->name;
                    $aOut[$oView->store_id] = $sLabel;
                }
            }
        }

        return $aOut;
    }
    
    public function getShopValues() {
        return $this->getDescriptionValues();
    }

    public function getCustomerGroupValues($blNotLoggedIn = false) {
        $oGroup = Mage::getModel('customer/group');
        return $oGroup->getCollection()->toOptionHash();
    }

    public function getOrderStatusValues() {
        $collection = Mage::getResourceModel('sales/order_status_collection');
        $collection->joinStates();
        $collection->getSelect()->where('state_table.state not like ?', null);
        $aOut = array();
        foreach ($collection as $oStatus) {
            $aOut[strtolower($oStatus->getstatus())] = $oStatus->getLabel() . ' (' . $oStatus->getState() . ')';
        }

        return $aOut;
    }
    
    public function getBrand() {
        return $this->getAttributeList();
    }

    public function getEan() {
        return $this->getAttributeList();
    }

    public function getManufacturer() {
        return $this->getAttributeList();    
        /*
        return MLHelper::gi('model_productlistdependency_magentoattributefilter')->getFilterValues(MLModul::gi()->getConfig('manufacturer'));
        */
    }

    public function getManufacturerPartNumber() {
        return $this->getAttributeList();
    }

    /**
     * Gets list of custom attributes from shop.
     * 
     * @return array Collection of all attributes
     */
    public function getAttributeList() {
        return $this->getProductAttributes();
    }

	/**
	 * Gets the list of product attributes prefixed with attribute type.
	 *
	 * @return array Collection of prefixed attributes
	 */
	public function getPrefixedAttributeList() {
		return $this->getProductAttributes();
	}

    /**
     * Gets the list of product attributes that have options (displayed as dropdown or multiselect fields).
     * 
     * @return array Collection of attributes with options
     */
    public function getAttributeListWithOptions() {
        $aResult = $this->getProductAttributes('frontend_input', array('in' => array('select', 'multiselect')));

        // filter out attributes without options
        foreach (array_keys($aResult) as $sAttributeCode) {
            if ($sAttributeCode != '' && count($this->getAttributeOptions($sAttributeCode)) === 0) {
                unset($aResult[$sAttributeCode]);
            }
        }

        return $aResult;
    }

    /**
     * Gets the list of product attributes that have options (displayed as dropdown or multiselect fields).
     * If $iLangId is set, uses languages from selected store ($iLangId is store id in magento).
     * 
     * @return array Collection of attributes with options
     */
    public function getAttributeOptions($sAttributeCode, $iLangId = null) {
        $sKey = md5(json_encode(array($sAttributeCode, $iLangId)));
        if (empty($this->aAttributeOptions[$sKey])) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute */
            $oAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $sAttributeCode);
            $aResult = array();
            if ($oAttribute->usesSource()) {
                $oAttribute->setStoreId($iLangId === null ? '0' : $iLangId);
                foreach ($oAttribute->getSource()->getAllOptions(false) as $aOption) {
                    if (is_array($aOption)) { 
                        if (!is_array($aOption['value'])) {
                            $aResult[$aOption['value']] = $aOption['label'];
                        }
                    }
                }
            }
            $this->aAttributeList[$sKey] = $aResult;
        }
        return $this->aAttributeList[$sKey];
    }

	/**
     * Gets the list of product attribute values.
     * If $iLangId is set, use translation for attribute options' labels.
     *
     * @return array Collection of attribute values
     */
    public function getPrefixedAttributeOptions($sAttributeCode, $iLangId = null) {
		return $this->getAttributeOptions($sAttributeCode, $iLangId);
	}
	
    public function getCurrency() {
        // this is apparently not used in Magento
        return array();
    }

    /**
     * Gets tax classes for product.
     * 
     * @return array Tax classes
     * array(
     *  array(
     *      'value' => string
     *      'label' => string
     *  ),
     *   ...
     * )
     */
    public function getTaxClasses() {
        return Mage::getModel('tax/class_source_product')->toOptionArray();
    }

    public function manipulateForm(&$aForm) {
        $oI18n = MLI18n::gi();
        try {
            MLRequest::gi()->get('mode');
        } catch (Exception $oEx) {
            if (isset($aForm['orderimport'])) {
                $aForm = MLHelper::getArrayInstance()->mergeDistinct($aForm, MLI18n::gi()->get('magentospecific_aGeneralForm')); 
            }
            if (isset($aForm['productfields'])) {
                $aForm['productfields']['fields']['weightunit'] = array(
                    'label' => $oI18n->get('Magento_Global_Configuration_Label'),
                    'desc' => $oI18n->get('Magento_Global_Configuration_Description'),
                    'key' => 'general.weightunit',
                    'type' => 'selection',
                    'values' => array(
                        'KG' => 'Kilograms',
                        'LB' => 'Pounds',
                        'GR' => 'Grams',
                        'OZ' => 'Ounce'
                    ),
                    )
                ;
            }
        }
    }

    protected function getProductAttributes($filterBy = null, $condition = null) {
        $sKey = md5(json_encode(array($filterBy, $condition)));
        if (empty($this->aAttributeList[$sKey])) {
            /* @var $oAttributeCollection Mage_Eav_Model_Resource_Entity_Attribute_Collection */
            $oAttributeCollection = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter(Mage::getModel('catalog/product')
                ->getResource()
                ->getTypeId())
            ;
            if ($filterBy) {
                $oAttributeCollection->addFieldToFilter($filterBy, $condition);
            }
            $oAttributeCollection->load();
            $aOut = array('' => MLI18n::gi()->get('ConfigFormEmptySelect'));
            foreach ($oAttributeCollection as $oAttribute) {
                if ($oAttribute->frontend_label != '') {
                    $aOut[$oAttribute->attribute_code] = $oAttribute->frontend_label;
                }
            }
            $this->aAttributeList[$sKey] = $aOut;
        }
        return $this->aAttributeList[$sKey];
    }
    
    public function getPossibleVariationGroupNames () {
        return $this->getProductAttributes('is_configurable', 1);
    }
    
}

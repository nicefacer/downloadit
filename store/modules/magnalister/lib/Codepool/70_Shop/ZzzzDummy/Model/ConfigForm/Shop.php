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

class ML_ZzzzDummy_Model_ConfigForm_Shop extends ML_Shop_Model_ConfigForm_Shop_Abstract {
    public function getDescriptionValues() {
        $aOut = array('de');
        return $aOut;
    }
    
    /**
     * @todo
     */
    public function getCustomerGroupValues($blNotLoggedIn = false) {
        $aOut = array();
        return $aOut;
    }

    /**
     * @todo
     */
    public function getOrderStatusValues() {
        $aOut = array();
        return $aOut;
    }

    public function getEan() {
        return array('ean');
    }

    public function getManufacturer() {
        return array('manufacturer');
    }

    public function getManufacturerPartNumber() {
        return array('manufacturerpartnumber');
    }

    /**
     * Gets list of custom attributes from shop.
     * 
     * @return array Collection of all attributes
     */
    public function getAttributeList() {
        $aOut = array();
        return $aOut;
    }

	/**
	 * Gets the list of product attributes prefixed with attribute type.
	 *
	 * @return array Collection of prefixed attributes
	 */
	public function getPrefixedAttributeList() {
        $aOut = array();
        return $aOut;
	}

    /**
     * Gets the list of product attributes that have options (displayed as dropdown or multiselect fields).
     * 
     * @return array Collection of attributes with options
     */
    public function getAttributeListWithOptions() {
        $aOut = array();
        return $aOut;
    }

    /**
     * Gets the list of product attributes that have options (displayed as dropdown or multiselect fields).
     * If $iLangId is set, uses languages from selected store ($iLangId is store id in magento).
     * 
     * @return array Collection of attributes with options
     */
    public function getAttributeOptions($sAttributeCode, $iLangId = null) {
        $aOut = array();
        return $aOut;
    }

	/**
     * Gets the list of product attribute values.
     * If $iLangId is set, use translation for attribute options' labels.
     *
     * @return array Collection of attribute values
     */
    public function getPrefixedAttributeOptions($sAttributeCode, $iLangId = null) {
        $aOut = array();
        return $aOut;
	}
	
    public function getCurrency() {
        return array('EUR');
    }

    public function getTaxClasses() {
        return array(
            array('value' => '19', 'label' => '19%',),
            array('value' => '7', 'label' => '7%',)
        );
        return $aOut;
    }

    public function manipulateForm(&$aForm) {
    }
}

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
MLFilesystem::gi()->loadClass('Form_Helper_Model_Table_ConfigData_Abstract');

class ML_Tradoria_Helper_Model_Table_Tradoria_ConfigData extends ML_Form_Helper_Model_Table_ConfigData_Abstract {

    /**
     * Gets languages for config form.
     * 
     * @param array $aField
     */
    public function langField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getDescriptionValues();
    }

    public function shippingServiceField(&$aField) {
        $aResponse = $this->callApi(array('ACTION' => 'GetShippingTimes'));
        $aField['values'] = array();
        foreach ($aResponse as $iServiceId => $sServiceName) {
            $aField['values'][$iServiceId] = $sServiceName;
        }
    }

    public function primaryCategoryField(&$aField) {
        $aField['values'] = MLDatabase::factory('tradoria_prepare')->getTopPrimaryCategories();
    }

    /**
     * Pulls values for tax classes from shop specific tables.
     * 
     * @param array $aField Field to add 'valuessrc' and 'valuesdst' keys to.
     */
    public function checkin_taxmatchingField(&$aField) {
        $aField['valuessrc'] = array();
        $taxClasses = MLFormHelper::getShopInstance()->getTaxClasses();
        foreach ($taxClasses as $tax) {
            $aField['valuessrc'][$tax['value']]= array(
                'i18n' => $tax['label'],
                'required' => true,
            );
        }

        $aResponse = $this->callApi(array('ACTION' => 'GetTaxValues'), 1 * 60 * 60);

        foreach ($aResponse as $id => $taxRate) {
            $aField['valuesdst'][$id] = $taxRate;
        }
    }

    public function checkin_shippinggroupField(&$aField) {
		$aField['values'] = array_slice(range(0, 20), 1, null, true);
    }

    public function mail_sendField(&$aField) {
        $aField['values'] = array(
            1 => MLI18n::gi()->get('ML_BUTTON_LABEL_YES'),
            0 => MLI18n::gi()->get('ML_BUTTON_LABEL_NO'));
    }

    public function mail_copyField(&$aField) {
        $aField['values'] = array(
            1 => MLI18n::gi()->get('ML_BUTTON_LABEL_YES'),
            0 => MLI18n::gi()->get('ML_BUTTON_LABEL_NO'));
    }
    
    public function order_importonlypaidField(&$aField) {
        $aField['values'] = array(
            1 => MLI18n::gi()->get('ML_BUTTON_LABEL_YES'),
            0 => MLI18n::gi()->get('ML_BUTTON_LABEL_NO'));
    }
    
    public function orderstatus_carrier_defaultField (&$aField) {
        $aField['values'] = MLModul::gi()->getCarrier();
    }
}

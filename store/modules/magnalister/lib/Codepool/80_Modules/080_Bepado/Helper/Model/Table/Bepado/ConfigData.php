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

class ML_Bepado_Helper_Model_Table_Bepado_ConfigData extends ML_Form_Helper_Model_Table_ConfigData_Abstract {
    
    /**
     * Gets languages for config form.
     * 
     * @param array $aField
     */
    public function shippingTimeField(&$aField) {
        for($i =1 ;$i<32;$i++){
            $aField['values'][$i] = $i; 
        }
    }
    
    /**
     * Gets languages for config form.
     * 
     * @param array $aField
     */
    public function langField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getDescriptionValues();
    }

    public function shippingCountryField (&$aField) {
        $aField['values'] = $this->callApi(array(
            'ACTION' => 'GetCountries', 
            'SUBSYSTEM' => 'Core', 
            'DATA' => array(
                'Language' => MLModul::gi()->getConfig('marketplace.lang')
            )
        ), 60 * 60 * 24 * 30);
    }
    
    public function b2b_price_addKindField (&$aField) {
        $this->price_addKindField($aField);
    }
    
    public function b2b_price_factorField (&$aField) {
        $this->price_factorField($aField);
    }
    
    public function b2b_price_signalField (&$aField) {
        $this->price_signalField($aField);
    }
    
    public function b2b_price_groupField (&$aField) {
        $this->price_groupField($aField);
    }
    public function b2c_price_addKindField (&$aField) {
        $this->price_addKindField($aField);
    }
    
    public function b2c_price_factorField (&$aField) {
        $this->price_factorField($aField);
    }
    
    public function b2c_price_signalField (&$aField) {
        $this->price_signalField($aField);
    }
    
    public function b2c_price_groupField (&$aField) {
        $this->price_groupField($aField);
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
    
}

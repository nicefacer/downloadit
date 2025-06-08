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

class ML_Dawanda_Helper_Model_Table_Dawanda_ConfigData extends ML_Form_Helper_Model_Table_ConfigData_Abstract {
    
    public function productTypeField(&$aField) {
        $aField['values'] = $this->callApi(array('ACTION' => 'GetProductTypes'), 12 * 12 * 60);
    }
    
    public function returnPolicyField (&$aField) {
        $aResponse = $this->callApi(array('ACTION' => 'GetReturnPolicies'), 1 * 60 * 60);
        $aPolicies = array();
        foreach ($aResponse as $aPolicy) {
            $aPolicies[$aPolicy['Id']] = $aPolicy['Title'];
        }
        $aField['values'] = $aPolicies;
    }
    
    public function langsField (&$aField) {
        $aField['valuessrc'] = array();
        $aResponse = $this->callApi(array('ACTION' => 'GetLanguages'), 1 * 60 * 60);
        if (isset($aResponse['AvailableLanguages']) && isset($aResponse['MainLanguage']) && isset($aResponse['Currency'])) {
            foreach ($aResponse['AvailableLanguages'] as $sLang) {
                $aField['valuessrc'][$sLang] = array(
                    'i18n' => $sLang,
                    'required' => $sLang == $aResponse['MainLanguage'],
                );
                if ($aField['valuessrc'][$sLang]['required']) {
                    $aField['valuessrc'][$sLang]['currency'] = $aResponse['Currency'];
                }
            }
            $aField['valuesdst'] = MLFormHelper::getShopInstance()->getDescriptionValues();
        }
    }   
    
    public function checkin_leadtimetoshipField (&$aField) {
        $aField['values'] = $this->callApi(array('ACTION' => 'GetShippingTimes'), 12 * 60 * 60);
    }
    
    public function shippingServiceField (&$aField) {
        $aResponse = $this->callApi(array('ACTION' => 'GetShippingServiceDetails'), 12 * 60 * 60);
        $aField['values'] = array();
        foreach ($aResponse as $iServiceId => $aService) {
            $aField['values'][$iServiceId] = $aService['Name'];
        }
    }
    
    public function mpColorsField (&$aField) {
        $aField ['values'] = $this->callApi(array('ACTION' => 'GetColors'), 12 * 60 * 60);
    }
    
    public function primaryCategoryField (&$aField) {
        $aField['values'] = MLDatabase::factory('dawanda_prepare')->getTopPrimaryCategories(); 
    }
    
    public function secondaryCategoryField (&$aField) {
        $aField['values'] = MLDatabase::factory('dawanda_prepare')->getTopSecondaryCategories(); 
    }
    
    public function shopCategoryField (&$aField) {
        $aField['values'] = MLDatabase::factory('dawanda_prepare')->getTopStoreCategories(); 
    }
    
    public function order_importonlypaidField(&$aField) {
        $aField['values'] = array(
            1 => MLI18n::gi()->get('ML_BUTTON_LABEL_YES'),
            0 => MLI18n::gi()->get('ML_BUTTON_LABEL_NO'));
    }
    
}

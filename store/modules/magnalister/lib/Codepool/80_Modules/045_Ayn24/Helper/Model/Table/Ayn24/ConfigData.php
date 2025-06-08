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

class ML_Ayn24_Helper_Model_Table_Ayn24_ConfigData extends ML_Form_Helper_Model_Table_ConfigData_Abstract {

    /**
     * Gets languages for config form.
     * 
     * @param array $aField
     */
    public function langField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getDescriptionValues();
    }

    public function primaryCategoryField(&$aField) {
        $aField['values'] = MLDatabase::factory('ayn24_prepare')->getTopPrimaryCategories();
    }

    /**
     * Sets values for lead time.
     * @param array $aField
     */
    public function checkin_leadtimetoshipField(&$aField) {
        $aField['values'] = array_slice(range(0, 30), 1, null, true);
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
            $aField['valuessrc'][$tax['value']]['i18n'] = $tax['label'];
        }

        // pull from api if exists, otherwise, pull from config
        $aField['valuesdst'] = $aField['i18n']['matching']['labelsdst'];
    }

    public function checkin_shortdescField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getPrefixedAttributeList();
    }

    public function checkin_longdescField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getPrefixedAttributeList();
    }

    public function shippingtypeField(&$aField) {
        $aField['values'] = array_merge(
            array('' => MLI18n::gi()->ConfigFormEmptySelect), $this->callApi(array('ACTION' => 'GetShippingTypes'), 1 * 60 * 60));
    }

    public function orderstatus_canceled_customerrequestField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getOrderStatusValues();
    }

    public function orderstatus_canceled_outofstockField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getOrderStatusValues();
    }

    public function orderstatus_canceled_damagedgoodsField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getOrderStatusValues();
    }

    public function orderstatus_canceled_dealerrequestField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getOrderStatusValues();
    }

    public function stocksync_toMarketplaceField(&$aField) {
        $aField['values'] = $aField['i18n']['values'];
    }

    public function inventorysync_priceField(&$aField) {
        $aField['values'] = $aField['i18n']['values'];
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

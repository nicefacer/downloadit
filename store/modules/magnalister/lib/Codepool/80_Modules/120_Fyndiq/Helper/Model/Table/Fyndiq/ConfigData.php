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

class ML_Fyndiq_Helper_Model_Table_Fyndiq_ConfigData extends ML_Form_Helper_Model_Table_ConfigData_Abstract
{

    public function siteIdField(&$aField)
    {
        $aSites = $this->callApi(array('ACTION' => 'GetSites'), 60);
        $aField['type'] = 'select';
        $aField['values'] = array();
        $aField['values'][] = MLI18n::gi()->get('ML_AMAZON_LABEL_APPLY_PLEASE_SELECT');
        foreach ($aSites as $aSite) {
            $aField['values'][$aSite['id']] = $aSite['name'];
        }
    }

    public function getSiteDetails()
    {
        return $this->callApi(array('ACTION' => 'GetSiteDetails'), 60);
    }

    public function primaryCategoryField(&$aField)
    {
        $aField['values'] = MLDatabase::factory('fyndiq_prepare')->getTopPrimaryCategories();
    }

    public function currencyField(&$aField)
    {
        $currencies = MLModul::gi()->getConfig('site.currencies');
        $aField['values'][''] = ML_AMAZON_LABEL_APPLY_PLEASE_SELECT;
        if ($currencies) {
            foreach ($currencies as $code => $symbol) {
                $aField['values'][$code] = $code;
            }
        }
    }

    public function listingTypeField(&$aField)
    {
        $listingTypes = MLModul::gi()->getConfig('site.listing_types');
        $aField['values'][''] = ML_AMAZON_LABEL_APPLY_PLEASE_SELECT;
        if ($listingTypes) {
            foreach ($listingTypes as $code => $name) {
                $aField['values'][$code] = $name;
            }
        }
    }

    public function langField(&$aField)
    {
        $aField['values'] = MLFormHelper::getShopInstance()->getDescriptionValues();
    }

    public function mail_sendField(&$aField)
    {
        $aField['values'] = array(
            1 => MLI18n::gi()->get('ML_BUTTON_LABEL_YES'),
            0 => MLI18n::gi()->get('ML_BUTTON_LABEL_NO'));
    }

    public function mail_copyField(&$aField)
    {
        $aField['values'] = array(
            1 => MLI18n::gi()->get('ML_BUTTON_LABEL_YES'),
            0 => MLI18n::gi()->get('ML_BUTTON_LABEL_NO'));
    }

    public function identifierField(&$aField)
    {
        $aField['subfields']['select1']['values'] = $this->callApi(array('ACTION' => 'GetIdentifierType'), 60);
        $aField['subfields']['select2']['values'] = MLFormHelper::getShopInstance()->getPrefixedAttributeList();
    }

    public function vatField(&$aField)
    {
        $shopTaxes = MLFormHelper::getShopInstance()->getTaxClasses();
        $aField['valuessrc'] = array();
        if ($shopTaxes) {
            foreach ($shopTaxes as $tax) {
                $aField['valuessrc'][$tax['value']] = array('i18n' => $tax['label'], 'required' => true);
            }
        }

        $aField['valuesdst'] = $this->callApi(array('ACTION' => 'GetVatPercent'), 60);
    }

    public function imagePathField(&$aField)
    {
        if (isset($aField['value']) === false || empty($aField['value'])) {
            $aField['value'] = MLHttp::gi()->getShopImageUrl();
        }
    }

    public function shippingCostField(&$aField)
    {
        $aField['value'] = isset($aField['value']) ? str_replace(',', '.', trim($aField['value'])) : 0;
        if ((string)((float)$aField['value']) != $aField['value']) {
            $this->addError($aField, MLI18n::gi()->get('fyndiq_config_checkin_badshippingcost'));
        } else {
            $aField['value'] = number_format($aField['value'], 2);
        }
    }

    public function serviceField(&$aField)
    {
        $aField['values'] = $this->callApi(array('ACTION' => 'GetDeliveryServices'), 60);
    }
}

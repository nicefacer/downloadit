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

class ML_Hitmeister_Helper_Model_Table_Hitmeister_ConfigData extends ML_Form_Helper_Model_Table_ConfigData_Abstract {

    public function siteIdField(&$aField) {
        $aSites = $this->callApi(array('ACTION' => 'GetSites'), 60);
        $aField['type'] = 'select';
        $aField['values'] = array();
        $aField['values'][] = MLI18n::gi()->get('ML_AMAZON_LABEL_APPLY_PLEASE_SELECT');
        foreach ($aSites as $aSite) {
            $aField['values'][$aSite['id']] = $aSite['name'];
        }
    }
    
    public function getSiteDetails() {
        return $this->callApi(array('ACTION' => 'GetSiteDetails'), 60);
    }

    public function primaryCategoryField(&$aField) {
        $aField['values'] = MLDatabase::factory('hitmeister_prepare')->getTopPrimaryCategories();
    }
    
	public function checkin_currencyField(&$aField) {
		$currencies = MLModul::gi()->getConfig('site.currencies');
		$aField['values'][''] = ML_AMAZON_LABEL_APPLY_PLEASE_SELECT;
		foreach ($currencies as $code => $symbol) {
			$aField['values'][$code] = $code;
		}
	}
	
	public function checkin_listingTypeField(&$aField) {
		$aListingTypes = MLModul::gi()->getConfig('site.listing_types');
		$aField['values'][''] = ML_AMAZON_LABEL_APPLY_PLEASE_SELECT;
		foreach ($aListingTypes as $code => $name) {
			$aField['values'][$code] = $name;
		}
	}
	
	public function langField (&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getDescriptionValues();
    }
    
    public function imagePathField (&$aField) {
        if (isset($aField['value']) === false || empty($aField['value'])) {
            $aField['value'] = MLHttp::gi()->getShopImageUrl();
        }
    }
	
	public function itemConditionField(&$aField) {
        $aField['values'] = $this->callApi(array('ACTION' => 'GetUnitConditions'), 60);
    }
    
	public function shippingTimeField(&$aField) {
        $aField['values'] = $this->callApi(array('ACTION' => 'GetDeliveryTimes'), 60);
    }
    
	public function itemCountryField(&$aField) {
        $aField['values'] = $this->callApi(array('ACTION' => 'GetDeliveryCountries'), 60);
    }
    
	public function orderstatus_carrierField(&$aField) {
        $orderStatusData = $this->callApi(array('ACTION' => 'GetOrderStatusData'), 60);;
        $aField['values'] = $orderStatusData['CarrierCodes'];
    }
    
	public function orderstatus_cancelreasonField(&$aField) {
        $orderStatusData = $this->callApi(array('ACTION' => 'GetOrderStatusData'), 60);;
        $aField['values'] = $orderStatusData['Reasons'];
    }
    
    public function orderstatus_cancelledField(&$aField) {
        $this->orderstatus_canceledField($aField);
    }
    
    public function shippingTimeMatchingField (&$aField) {
//		$shippingTimeShop = MLFormHelper::getShopInstance();     
        if (empty($shippingTimeShop) === false && isset($shippingTimeShop)) {
            $aField['type'] = 'matching';
            $shippingTimes = $this->callApi(array('ACTION' => 'GetDeliveryTimes'), 60);
            foreach ($shippingTimes as $shippingTime) {
                $aField['valuessrc'][$shippingTime] = array(
                    'i18n' => $shippingTime,
                    'required' => true,
                );
            }
            
            $aField['valuesdst'] = $shippingTimeShop;
        } else {
            $aField['type'] = 'information';
            $aField['value'] = MLI18n::gi()->hitmeister_config_checkin_shippingmatching;
        }
    }
    
}

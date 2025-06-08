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
MLFilesystem::gi()->loadClass('Ebay_Helper_Model_Table_Ebay_ConfigData');

class ML_ShopwareEbay_Helper_Model_Table_Ebay_ConfigData extends ML_Ebay_Helper_Model_Table_Ebay_ConfigData {
    
    public function orderimport_paymentmethodField (&$aField) {
        $aMatching = MLI18n::gi()->get('ebay_configform_orderimport_payment_values');
        $aField['values'] = 
                array('matching' => $aMatching['matching']['title'])
                +
                MLFormHelper::getShopInstance()->getPaymentMethodValues()
        ;
    }
    
    public function orderimport_shippingmethodField (&$aField) {
        $aMatching = MLI18n::gi()->get('ebay_configform_orderimport_shipping_values');
        $aField['values'] = 
                array('matching' => $aMatching['matching']['title'])
                +
                MLFormHelper::getShopInstance()->getShippingMethodValues()
        ;
    }    
        
    public function orderimport_paymentstatusField (&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getPaymentStatusValues();
    }
    
    public function updateable_paymentstatusField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getPaymentStatusValues();
    }    
          
    public function paymentstatus_paidField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getPaymentStatusValues();
    }
    
    public function orderstatus_carrier_defaultField (&$aField) {
        parent::orderstatus_carrier_defaultField($aField);
        $aValues = array();
        foreach ($aField['values'] as $sKey => $sValue) {
            $aValues[$sKey] = $sValue;
            if ($sKey == '') {
                $aValues['-1'] = MLI18n::gi()->get('shopware_orderstatus_carrier_defaultField_value_shippingname');
            }
        }
        $aField['values'] = $aValues;
    }
    
}

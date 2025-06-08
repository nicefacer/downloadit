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
MLFilesystem::gi()->loadClass('Amazon_Helper_Model_Table_Amazon_ConfigData');

class ML_ShopwareAmazon_Helper_Model_Table_Amazon_ConfigData extends ML_Amazon_Helper_Model_Table_Amazon_ConfigData {
    
    public function orderimport_paymentmethodField (&$aField) {
        $aMatching = MLI18n::gi()->get('amazon_configform_orderimport_payment_values');
        $aPayment = MLFormHelper::getShopInstance()->getPaymentMethodValues();       
        $aField['values'] = 
                array('Amazon' => $aMatching['Amazon']['title'])
                +
                $aPayment;       
        
    }     
    
    public function orderimport_shippingmethodField (&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getShippingMethodValues();
        $aField['values']['__automatic__'] = MLI18n::gi()->get('sAmazon_automatically');
    }   
        
    public function orderimport_fbapaymentmethodField (&$aField) {
        $aMatching = MLI18n::gi()->get('amazon_configform_orderimport_payment_values');
        $aPayment = MLFormHelper::getShopInstance()->getPaymentMethodValues();       
        $aField['values'] = 
                array('Amazon' => $aMatching['Amazon']['title'])
                +
                $aPayment;       
        
    }     
    
    public function orderimport_fbashippingmethodField (&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getShippingMethodValues();
        $aField['values']['__automatic__'] = MLI18n::gi()->get('sAmazon_automatically');
    }    
      
    public function orderimport_paymentstatusField (&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getPaymentStatusValues();
    }
    
        
    public function orderstatus_carrier_defaultField(&$aField){
        $aField['ajax']=array(
            'selector' => '#' . $this->getFieldId('orderstatus.carrier.additional'),
            'trigger' => 'change',
            'field' => array(
                'type' => 'select',
            ),
        );
        $aField['values'] = 
                array('-1'=>MLI18n::gi()->get('shopware_orderstatus_carrier_defaultField_value_shippingname'))
                +
                MLFormHelper::getModulInstance()->getCarrierCodeValues();
        
    }
    
}

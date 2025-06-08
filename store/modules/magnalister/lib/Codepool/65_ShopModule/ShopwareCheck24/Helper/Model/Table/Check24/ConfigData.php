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
MLFilesystem::gi()->loadClass('Check24_Helper_Model_Table_Check24_ConfigData');

class ML_ShopwareCheck24_Helper_Model_Table_Check24_ConfigData extends ML_Check24_Helper_Model_Table_Check24_ConfigData {
    
    public function orderimport_paymentmethodField (&$aField) {
        $aField['values'] = 
            array('Check24' => 'Check24')
            + MLFormHelper::getShopInstance()->getPaymentMethodValues()
         ;
    }
    
    public function orderimport_shippingmethodField (&$aField) {
        $aField['values'] = 
            array('Check24' => 'Check24') 
            + MLFormHelper::getShopInstance()->getShippingMethodValues()
        ;
    }
    
    public function paymentstatusField (&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getPaymentStatusValues();
    }
}

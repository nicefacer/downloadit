<?php

MLFilesystem::gi()->loadClass('Dawanda_Helper_Model_Service_OrderData_Normalize');

class ML_ShopwareDawanda_Helper_Model_Service_OrderData_Normalize extends ML_Dawanda_Helper_Model_Service_OrderData_Normalize {
    
    protected function getShippingCode($aTotal) {
        return MLModul::gi()->getConfig('orderimport.shippingmethod');
    }

    protected function getPaymentCode($aTotal) {
        return MLModul::gi()->getConfig('orderimport.paymentmethod') == '__automatic__' ? $aTotal['Code'] : MLModul::gi()->getConfig('orderimport.paymentmethod');
    }
}

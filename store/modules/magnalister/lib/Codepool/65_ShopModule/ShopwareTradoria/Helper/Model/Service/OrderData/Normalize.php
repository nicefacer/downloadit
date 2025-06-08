<?php

MLFilesystem::gi()->loadClass('Modul_Helper_Model_Service_OrderData_Normalize');

class ML_ShopwareTradoria_Helper_Model_Service_OrderData_Normalize extends ML_Modul_Helper_Model_Service_OrderData_Normalize {
    
    protected function normalizeTotals() {
        $this->addMissingTotal();
        parent::normalizeTotals();
        return $this;
    }
    
    protected function getShippingCode($aTotal) {
        $sShipping = MLModul::gi()->getConfig('orderimport.shippingmethod');
        return $sShipping == '' ? MLModul::gi()->getMarketPlaceName() : $sShipping;
    }

    protected function getPaymentCode($aTotal) {
        $sPayment = MLModul::gi()->getConfig('orderimport.paymentmethod');
        return $sPayment == '' ? MLModul::gi()->getMarketPlaceName() : $sPayment;
    }
    
    protected function normalizeOrder () {
        parent::normalizeOrder();
        $this->aOrder['Order']['PaymentStatus'] = MLModul::gi()->getConfig('orderimport.paymentstatus');
        return $this;
    }
    
}

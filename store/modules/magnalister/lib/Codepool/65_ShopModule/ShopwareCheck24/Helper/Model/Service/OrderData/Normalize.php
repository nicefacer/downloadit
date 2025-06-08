<?php

MLFilesystem::gi()->loadClass('Modul_Helper_Model_Service_OrderData_Normalize');

class ML_ShopwareCheck24_Helper_Model_Service_OrderData_Normalize extends ML_Modul_Helper_Model_Service_OrderData_Normalize {
    
    protected function normalizeTotals () {
        $this->aOrder['Totals'] = array_key_exists('Totals', $this->aOrder) ? $this->aOrder['Totals'] : array();
        $blPaymentFound = false;
        foreach ($this->aOrder['Totals'] as $aTotal) {
            if ($aTotal['Type'] == 'Payment') {
                $blPaymentFound = true;
            }
        }
        if (!$blPaymentFound) {
            $this->aOrder['Totals'][] = array('Type' => 'Payment', 'Code' => 'Marketplace', 'Value' => 0);
        }
        return parent::normalizeTotals();
    }
    
    protected function getShippingCode($aTotal) {
        return MLModul::gi()->getConfig('orderimport.shippingmethod');
    }

    protected function getPaymentCode($aTotal) {
        return MLModul::gi()->getConfig('orderimport.paymentmethod');
    }
}

<?php

MLFilesystem::gi()->loadClass('Meinpaket_Helper_Model_Service_OrderData_Normalize');

class ML_ShopwareMeinpaket_Helper_Model_Service_OrderData_Normalize extends ML_Meinpaket_Helper_Model_Service_OrderData_Normalize {
        
    protected function normalizeOrder () {
        parent::normalizeOrder();
        $this->aOrder['Order']['PaymentStatus'] = MLModul::gi()->getConfig('orderimport.paymentstatus');
        return $this;
    }

}

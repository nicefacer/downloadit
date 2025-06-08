<?php

MLFilesystem::gi()->loadClass('Amazon_Helper_Model_Service_OrderData_Normalize');

class ML_ShopwareAmazon_Helper_Model_Service_OrderData_Normalize extends ML_Amazon_Helper_Model_Service_OrderData_Normalize {
    
    /**
     * @deprecated since r4545
     */
    protected function getShippingCode($aTotal) {
        if ($this->aOrder['MPSpecific']['FulfillmentChannel'] == 'AFN' && $this->getModul()->getConfig('orderimport.fbashippingmethod') !== null) { //amazon payed and shipped
            $sStatusKey = 'orderimport.fbashippingmethod';
        }else{
            $sStatusKey = 'orderimport.shippingmethod';
        }
        $sShippingMethod = MLModul::gi()->getConfig($sStatusKey);
        if ('textfield' == $sShippingMethod) {
            $sPayment = MLModul::gi()->getConfig('orderimport.shippingmethod.name');
            return $sPayment == '' ? $aTotal['Code'] : $sPayment;
        } else if ('matching' == $sShippingMethod) {
            if (in_array($aTotal['Code'], array('', 'none', 'None'))) {                
                return MLModul::gi()->getMarketPlaceName();
            } else {
                return $aTotal['Code'];
            }
        } else {
            return $sShippingMethod;
        }
    }

    /**
     * @deprecated just textfield option in shopware is deprecated since r4545
     */
    protected function getPaymentCode($aTotal) {
        if ($this->aOrder['MPSpecific']['FulfillmentChannel'] == 'AFN' && $this->getModul()->getConfig('orderimport.fbapaymentmethod') !== null) { //amazon payed and shipped
            $sStatusKey = 'orderimport.fbapaymentmethod';
        }else{
            $sStatusKey = 'orderimport.paymentmethod';
        }
        $sPaymentMethod =  MLModul::gi()->getConfig($sStatusKey);
        if ('textfield' == $sPaymentMethod) {
            $sPayment = MLModul::gi()->getConfig('orderimport.paymentmethod.name');
            $sPaymentMethod = $sPayment == '' ? MLModul::gi()->getMarketPlaceName() : $sPayment;
        } else if ('matching' == $sPaymentMethod) {
            $sPaymentMethod = MLModul::gi()->getMarketPlaceName();
        }
        return $sPaymentMethod;
    }

}

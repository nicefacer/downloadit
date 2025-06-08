<?php

MLFilesystem::gi()->loadClass('Ebay_Helper_Model_Service_OrderData_Normalize');

class ML_ShopwareEbay_Helper_Model_Service_OrderData_Normalize extends ML_Ebay_Helper_Model_Service_OrderData_Normalize {

    protected function normalizeOrder() {
        parent::normalizeOrder();
        if (isset($this->aOrder['Order']['Payed']) && $this->aOrder['Order']['Payed']) {
             $this->aOrder['Order']['PaymentStatus'] = MLModul::gi()->getConfig('paymentstatus.paid');
        }elseif(MLModul::gi()->getConfig('orderimport.paymentstatus') !== null){
            $this->aOrder['Order']['PaymentStatus'] = MLModul::gi()->getConfig('orderimport.paymentstatus');
        }else{
            $this->aOrder['Order']['PaymentStatus'] = 17;//deprecated code , just use for user who configured ebay before
        }
        return $this;
    }
    protected function normalizeAddressSets () {
        $address = !empty($this->aOrder['AddressSets']['Shipping']['StreetAddress']) 
            ? $this->aOrder['AddressSets']['Shipping']['StreetAddress'] 
            : $this->aOrder['AddressSets']['Shipping']['Street'] . ' ' . $this->aOrder['AddressSets']['Shipping']['Housenumber'];
        if (strpos($address, 'Packstation') === 0) {
            $this->aOrder['AddressSets']['Shipping']['Street'] = $address;
            $this->aOrder['AddressSets']['Shipping']['Housenumber'] = '0';
        }
        parent::normalizeAddressSets();
        return $this;
    }

    protected function getShippingCode($aTotal) {
        $sShippingMethod = MLModul::gi()->getConfig('orderimport.shippingmethod');
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

    protected function getPaymentCode($aTotal) {
        $sPaymentMethod = MLModul::gi()->getConfig('orderimport.paymentmethod');
        if ('textfield' == $sPaymentMethod) {
            $sPayment = MLModul::gi()->getConfig('orderimport.paymentmethod.name');
            return $sPayment == '' ? $aTotal['Code'] : $sPayment;
        } else if ('matching' == $sPaymentMethod) {
            if (in_array($aTotal['Code'], array('', 'none', 'None'))) {
                return MLModul::gi()->getMarketPlaceName();
            } else {
                return $aTotal['Code'];
            }
        } else {
            return $sPaymentMethod;
        }
    }
}

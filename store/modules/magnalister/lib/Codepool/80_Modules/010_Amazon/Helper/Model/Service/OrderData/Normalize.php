<?php
MLFilesystem::gi()->loadClass('Modul_Helper_Model_Service_OrderData_Normalize');

class ML_Amazon_Helper_Model_Service_OrderData_Normalize extends ML_Modul_Helper_Model_Service_OrderData_Normalize {
    
    protected $oModul = null;
    protected function getModul(){
        if($this->oModul === null ){
            $this->oModul = MLModul::gi();
        }
        return $this->oModul;
    }

    protected function normalizeTotalTypeShipping(&$aTotal) {
        parent::normalizeTotalTypeShipping($aTotal);
        if ($this->aOrder['MPSpecific']['FulfillmentChannel'] == 'AFN') { //amazon payed and shipped
            if (isset($this->aOrder['MPSpecific']['Carrier'])) {
                $aTotal['Data']['Carrier'] = $this->aOrder['MPSpecific']['Carrier'];
            }
            if (isset($this->aOrder['MPSpecific']['Trackingcode'])) {
                $aTotal['Data']['Trackingcode'] = $this->aOrder['MPSpecific']['Trackingcode'];
            }
        }
        return $this;
    }
    
    protected function getPaymentCode($aTotal) {//till this version amazon doesn't send any paymentmethod information        
        if ($this->aOrder['MPSpecific']['FulfillmentChannel'] == 'AFN' && $this->getModul()->getConfig('orderimport.fbapaymentmethod') !== null) { //amazon payed and shipped
            $sStatusKey = 'orderimport.fbapaymentmethod';
        }else{
            $sStatusKey = 'orderimport.paymentmethod';
        }
        if ('textfield' == $this->getModul()->getConfig($sStatusKey)) {
            $sPayment = $this->getModul()->getConfig($sStatusKey.'.name');
            return $sPayment == '' ? MLModul::gi()->getMarketPlaceName() : $sPayment;
        } else{//'matching'
            return MLModul::gi()->getMarketPlaceName();
        }
    }
    
    protected function getShippingCode($aTotal) {//till this version amazon doesn't send any paymentmethod information
        if ($this->aOrder['MPSpecific']['FulfillmentChannel'] == 'AFN' && $this->getModul()->getConfig('orderimport.fbashippingmethod') !== null) { //amazon payed and shipped
            $sStatusKey = 'orderimport.fbashippingmethod';
        }else{
            $sStatusKey = 'orderimport.shippingmethod';
        }
        if ('textfield' == $this->getModul()->getConfig($sStatusKey)) {
            $sPayment = $this->getModul()->getConfig($sStatusKey.'.name');
            return $sPayment == '' ? MLModul::gi()->getMarketPlaceName() : $sPayment;
        } else{//'matching'
            return MLModul::gi()->getMarketPlaceName();
        }
    }
    
    protected function normalizeOrder () {
        parent::normalizeOrder();
        $this->aOrder['Order']['Payed'] = true;
        $this->aOrder['Order']['PaymentStatus'] = MLModul::gi()->getConfig('orderimport.paymentstatus');
        if ($this->aOrder['MPSpecific']['FulfillmentChannel'] == 'AFN') { //amazon payed and shipped
            $this->aOrder['Order']['Shipped'] = true;
            $this->aOrder['Order']['Status'] = MLModul::gi()->getConfig('orderstatus.fba');
        }
        return $this;
    }
    
    protected function normalizeProduct (&$aProduct, $fDefaultTax) {
        parent::normalizeProduct($aProduct, $fDefaultTax);
        $aProduct['StockSync'] = 
            MLModul::gi()->getConfig('stocksync.frommarketplace') == 'rel' && $this->aOrder['MPSpecific']['FulfillmentChannel'] != 'AFN'
            ||
            MLModul::gi()->getConfig('stocksync.frommarketplace') == 'fba'
        ;
        return $this;
    }
    
    protected function normalizeMpSpecific () {
        parent::normalizeMpSpecific();
        if (array_key_exists('FulfillmentChannel', $this->aOrder['MPSpecific']) && $this->aOrder['MPSpecific']['FulfillmentChannel'] == 'AFN') {
            $this->aOrder['MPSpecific']['InternalComment'] = 	
                sprintf(MLI18n::gi()->get('ML_GENERIC_AUTOMATIC_ORDER_MP_SHORT'), MLModul::gi()->getMarketPlaceName(false).'FBA' )."\n".
                MLI18n::gi()->get('ML_LABEL_MARKETPLACE_ORDER_ID').': '.$this->aOrder['MPSpecific']['MOrderID']."\n\n"
                .$this->aOrder['Order']['Comments']
            ;
        }
        return $this;
    }

}

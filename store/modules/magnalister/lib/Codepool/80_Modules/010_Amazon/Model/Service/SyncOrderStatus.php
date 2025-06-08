<?php

class ML_Amazon_Model_Service_SyncOrderStatus extends ML_Modul_Model_Service_SyncOrderStatus_Abstract {
    protected function skipAOModifyOrderProcessing($oOrder) {
        $aData = $oOrder->get('data');

        //Check for AFN (amazon fulfillment service) and dont confirm them
        if ($aData['FulfillmentChannel'] == 'AFN') {
            $this->saveOrderData($oOrder);
            return true;
        }

        return false;
    }

    protected function extendSaveOrderData($aOrderData, $aResponseData) {
        $aOrderData = parent::extendSaveOrderData($aOrderData, $aResponseData);

        if (isset($aResponseData['BatchID'])) {
            $aOrderData['BatchID'] = $aResponseData['BatchID'];
        }

        return $aOrderData;
    }

    protected function getCarrier($sCarrier){
        $aCarriers = MLFormHelper::getModulInstance()->getCarrierCodeValues();
        $sConfigCarrier = MLModul::gi()->getConfig('orderstatus.carrier.default');
        if($sConfigCarrier == '-1' || in_array($sCarrier, $aCarriers) || $aCarriers[$sConfigCarrier] == null){
            return $sCarrier;
        }else{
            return $aCarriers[$sConfigCarrier];
        }
        
    }
}

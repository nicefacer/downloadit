<?php

class ML_MeinPaket_Model_Service_SyncOrderStatus extends ML_Modul_Model_Service_SyncOrderStatus_Abstract {

    protected $sOrderIdConfirmations = 'MOrderID';
    protected function submitRequestAndProcessResult($sAction, $aRequest, $aModels) {
        foreach ($aModels as $sModel => $oModel) {
            if (array_key_exists($sModel, $aRequest) && !array_key_exists('ConsignmentID', $aRequest[$sModel])) {
                $aRequest[$sModel]['ConsignmentID'] = $oModel->get('orders_id');
            }
        }
        return parent::submitRequestAndProcessResult($sAction, $aRequest, $aModels);
    }

}

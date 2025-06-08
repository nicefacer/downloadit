<?php

class ML_Fyndiq_Model_Service_SyncOrderStatus extends ML_Modul_Model_Service_SyncOrderStatus_Abstract {

    public function execute() {
        $oModule = MLModul::gi();
        $sCancelState = $oModule->getConfig('orderstatus.cancelled');
        if($sCancelState === null){
            $sCancelState = $oModule->getConfig('orderstatus.canceled');
        }
        $sShippedState = $oModule->getConfig('orderstatus.shipped');

        if ($oModule->getConfig('orderstatus.sync') == 'auto') {
            $oOrder = MLOrder::factory()->setKeys(array_keys(array('special' => $this->sOrderIdentifier)));
            $iOffset = (int)MLModul::gi()->getConfig('orderstatussyncoffset');
            $aChanged = $oOrder->getOutOfSyncOrdersArray($iOffset);
            $oList = $oOrder->getList();
            $oList->getQueryObject()->where("current_orders_id IN ('".implode("', '", $aChanged)."')");

            $aCanceledRequest = array();
            $aCanceledModels = array();
            $aShippedRequest = array();
            $aShippedModels = array();

            foreach ($oList->getList() as $oOrder) {
                try{
                    $sShopStatus = $oOrder->getShopOrderStatus();
                    if ($sShopStatus != $oOrder->get('status')) {
                        $aData = $oOrder->get('data');
                        $sOrderId = $aData[$this->sOrderIdentifier];

                        // skip (and / or) modify order
                        if ($this->skipAOModifyOrderProcessing($oOrder)) {
                            continue;
                        }

                        switch ($sShopStatus) {
                            case $sCancelState: {
                                $aCanceledRequest[$sOrderId] = array($this->sOrderIdentifier => $sOrderId);
                                $aCanceledModels[$sOrderId] = $oOrder;
                                break;
                            }
                            case $sShippedState: {
                                $sCarrier = $oModule->getConfig('service');
                                ($sCarrier !== null) ? $sCarrier : $sCarrier='PostNord';

                                $aShippedRequest[$sOrderId] = array(
                                    $this->sOrderIdentifier => $sOrderId,
                                    'ShippingDate' => $oOrder->getShippingDateTime(),
                                    'Carrier' => $sCarrier,
                                    'TrackingCode' => $oOrder->getShippingTrackingCode()
                                );
                                $aShippedModels[$sOrderId] = $oOrder;
                                break;
                            }
                            default: {
                                // In this case update order status in magnalister tables
                                $oOrder->set('status', $oOrder->getShopOrderStatus());
                                $oOrder->save();
                                continue;
                            }
                        }
                    }
                }  catch (Exception $oExc){
                    MLLog::gi()->add('SyncOrderStatus_'.MLModul::gi()->getMarketPlaceId().'_Exception', array(
                        'Exception' => array(
                            'Message' => $oExc->getMessage(),
                            'Code' => $oExc->getCode(),
                            'Backtrace' => $oExc->getTrace(),
                        )
                    ));
                }
            }
            //echo print_m($aShippedRequest, '$aShippedRequest')."\n";
            //echo print_m($aCanceledRequest, '$aCanceledRequest')."\n";
            $this->submitRequestAndProcessResult('ConfirmShipment', $aShippedRequest, $aShippedModels);
            $this->submitRequestAndProcessResult('CancelShipment', $aCanceledRequest, $aCanceledModels);
        }
    }

}

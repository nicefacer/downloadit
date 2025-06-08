<?php
/**
 * 888888ba                 dP  .88888.                    dP
 * 88    `8b                88 d8'   `88                   88
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b.
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P'
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

class ML_Hitmeister_Model_Service_SyncOrderStatus extends ML_Modul_Model_Service_SyncOrderStatus_Abstract {
	
    public function execute() {
        $oModule = MLModul::gi();
        $sCancelState = $oModule->getConfig('orderstatus.cancelled');
        $sShippedState = $oModule->getConfig('orderstatus.shipped');

        if ($oModule->getConfig('orderstatus.sync') == 'auto') {
            $oOrder = MLOrder::factory()->setKeys(array_keys(array('special' => $this->sOrderIdentifier)));
            $aChanged = $oOrder->getOutOfSyncOrdersArray();
            $oList = $oOrder->getList();
            $oList->getQueryObject()->where("current_orders_id IN ('".implode("', '", $aChanged)."')");

            $aCanceledRequest = array();
            $aCanceledModels = array();
            $aShippedRequest = array();
            $aShippedModels = array();

            foreach ($oList->getList() as $oOrder) {
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
                            $aCanceledRequest[$sOrderId] = array(
                                $this->sOrderIdentifier => $sOrderId,
                                'Reason' => $oModule->getConfig('orderstatus.cancelreason'),
                            );
                            $aCanceledModels[$sOrderId] = $oOrder;
                            break;
                        }
                        case $sShippedState: {
                            $sCarrier = $oModule->getConfig('orderstatus.carrier');
                            $aShippedRequest[$sOrderId] = array(
                                $this->sOrderIdentifier => $sOrderId,
                                'ShippingDate' => $oOrder->getShippingDateTime(),
                                'CarrierCode' => $sCarrier,
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
            }
            //echo print_m($aShippedRequest, '$aShippedRequest')."\n";
            //echo print_m($aCanceledRequest, '$aCanceledRequest')."\n";
            $this->submitRequestAndProcessResult('ConfirmShipment', $aShippedRequest, $aShippedModels);
            $this->submitRequestAndProcessResult('CancelShipment', $aCanceledRequest, $aCanceledModels);
        }
    }
    
}

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
abstract class ML_Modul_Model_Service_SyncOrderStatus_Abstract extends ML_Modul_Model_Service_Abstract {
    protected $blVerbose = false;
    protected $sOrderIdentifier = 'MOrderID';
    protected $sOrderIdConfirmations = 'MOrderId';

    public function __construct() {
        require_once MLFilesystem::getOldLibPath('php/lib/classes/SimplePrice.php');
        MLDatabase::getDbInstance()->logQueryTimes(false);
        MagnaConnector::gi()->setTimeOutInSeconds(600);
        @set_time_limit(60 * 10); // 10 minutes per module
        parent::__construct();
    }

    public function __destruct() {
        MagnaConnector::gi()->resetTimeOut();
        MLDatabase::getDbInstance()->logQueryTimes(true);
    }

    /*
     * You can modify the order and(or)[A(nd)O(r)] skip it in processing
     * @param:
     *  $oOrder: the order object
     * @return: if true should skip this order in process
     */
    protected function skipAOModifyOrderProcessing($oOrder) {
        return false;
    }

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
                                $sCarrier = $oOrder->getShippingCarrier();
                                $sCarrier = $this->getCarrier($sCarrier);
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

    /**
     * return the carrier
     *  special in eBay it check for a valid value in carrier list
     *
     * @return string $sCarrier
     */
    protected function getCarrier($sCarrier) {
        return $sCarrier;
    }
    
    protected function extendSaveOrderData($aOrderData, $aResponseData) {
        if (isset($aResponseData['Note']) && !empty($aResponseData['Note'])) {
            $aOrderData['Note'] = $aResponseData['Note'];
        } elseif (isset($aOrderData['Note'])) {
            unset($aOrderData['Note']);
        }

        return $aOrderData;
    }

    protected function saveOrderData($oOrder, $aResponseData = array()) {
        $oOrder->set('status', $oOrder->getShopOrderStatus());
        $aOrderData = $oOrder->get('data');
        $aOrderData = $this->extendSaveOrderData($aOrderData, $aResponseData);
        $oOrder->set('data', $aOrderData);
        $oOrder->save();
    }


    /**
     * implemented to extend it
     *  special in eBay if order cant updated any more
     */
    protected function postProcessError($aError, &$aModels) {

    }

    protected function submitRequestAndProcessResult($sAction, $aRequest, $aModels) {
        if (!empty($aRequest)) {
            try {
                $aResponse = MagnaConnector::gi()->submitRequest(array(
                    'ACTION' => $sAction,
                    'SUBSYSTEM' => $this->oModul->getMarketplaceName(),
                    'MARKETPLACEID' => $this->oModul->getMarketplaceId(),
                    'DATA' => $aRequest,
                ));
                if (isset($aResponse['STATUS']) && ($aResponse['STATUS']) == 'SUCCESS') {
                    if (isset($aResponse['CONFIRMATIONS'])) {
                        foreach ($aResponse['CONFIRMATIONS'] as $aResponseData) {
                            if (!array_key_exists($aResponseData[$this->sOrderIdConfirmations], $aModels)) {
                                // combined orders get responses for each part, but we need only the "main"
                                continue;
                            }
                            $this->saveOrderData($aModels[$aResponseData[$this->sOrderIdConfirmations]], $aResponseData);
                            unset($aModels[$aResponseData[$this->sOrderIdConfirmations]]);
                        }
                    }
                    if (isset($aResponse['ERRORS'])) {
                        foreach ($aResponse['ERRORS'] as $aError) {
                            $sMessage = null;
                            $aData = null;
                            if (isset($aError['ERRORMESSAGE'])) {
                                $sMessage = $aError['ERRORMESSAGE'];
                                if (isset($aError['DETAILS'])) { // Rakuten
                                    $aData = $aError['DETAILS'];
                                } elseif (isset($aError['VALUE'])) { // eBay
                                    $aData = array('MOrderID' => $aError['VALUE']);
                                } else { // other
                                    $aData = array();
                                }
                            }
                            if ($sMessage !== null) {
                                MLErrorLog::gi()->addError(-1, '', $sMessage, $aData);
                            }
                            $this->postProcessError($aError, $aModels);
                        }
                    }
                }
                MLLog::gi()->add('SyncOrderStatus_'.MLModul::gi()->getMarketPlaceId().'_'.$sAction, array(
                    'Request' => $aRequest,
                    'Response' => $aResponse,
                ));
            } catch (MagnaException $oEx) {
                MLLog::gi()->add('SyncOrderStatus_'.MLModul::gi()->getMarketPlaceId().'_Exception', array(
                    'RequestData' => $aRequest,
                    'Exception' => array(
                        'Message' => $oEx->getMessage(),
                        'Code' => $oEx->getCode(),
                        'Backtrace' => $oEx->getTrace(),
                    )
                ));
            }
        }
    }

}

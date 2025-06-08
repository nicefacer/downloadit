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
abstract class ML_Modul_Model_Service_ImportOrders_Abstract extends ML_Modul_Model_Service_Abstract {
    protected $aOrders = array();
    protected $iMutexId = null;
    protected $aOrdersList = array();
    protected $sGetOrdersApiAction = 'GetOrdersForDateRange';
    protected $sAcknowledgeApiAction = 'AcknowledgeImportedOrders';
    protected $blUpdateMode = false;
    
    /**
     * @var null no local test orders
     * @var array $aLocalTestOrders form orders tools_testorders (no api requests)
     */
    protected $aLocalTestOrders = null;
    
    public function execute() {
        try {
            if ($this->isMutex()) {
                $this->getOrders();
            }
        } catch (Exception $oEx) {
            echo $oEx->getMessage();
        }
        $this->cleanMutex();
        return $this;
    }

    protected function isMutex() {
        $oCache = MLCache::gi();
        $sLock = get_class($this).'.lock';
        if (!$oCache->exists($sLock)) {
            usleep(rand(200, 800));
            if (!$oCache->exists($sLock)) {
                $this->iMutexId = uniqid();
                $oCache->set($sLock, $this->iMutexId, 1200);
            }
        }
        return $oCache->get($sLock) == $this->iMutexId;
    }

    protected function cleanMutex() {
        if ($this->isMutex()) {
            $oCache = MLCache::gi();
            $sLock = get_class($this).'.lock';
            $oCache->delete($sLock);
        }
        return $this;
    }

    protected function acknowledgeOrders() {
        $aProcessedOrders = array();
        $oModul = $this->oModul;
        foreach ($this->aOrders as $iKey => $aOrder) {
            $sOrderId = $this->aOrdersList[$iKey]->get('orders_id');
            if (!empty($sOrderId)) {
                $aOrderParameters = array();
                $aOrderParameters['MOrderID'] = $aOrder['MPSpecific']['MOrderID'];
                $aOrderParameters['ShopOrderID'] = $sOrderId;
                $this->aOrdersList[$iKey]->setSpecificAcknowledgeField($aOrderParameters,$aOrder);
                $aProcessedOrders[] = $aOrderParameters;
            }
        }
        if (count($aProcessedOrders) > 0) {
            $aRequest = array(
                'ACTION' => $this->sAcknowledgeApiAction,
                'SUBSYSTEM' => $oModul->getMarketplaceName(),
                'MARKETPLACEID' => $oModul->getMarketplaceId(),
                'DATA' => $aProcessedOrders,
            );
            try {
                $aResponse = MagnaConnector::gi()->submitRequest($aRequest);
                MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                    'MOrderId' => 'AcknowledgeOrders',
                    'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',                    
                    'Request' => $aRequest,
                    'Response' => $aResponse,
                )); 
            } catch (MagnaException $oEx) {
                if ($oEx->getCode() == MagnaException::TIMEOUT) {
                    $oEx->saveRequest();
                    $oEx->setCriticalStatus(false);
                }
                MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                    'MOrderId' => 'AcknowledgeOrdersException',
                    'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',                    
                    'Request' => $aRequest,
                    'Exception' => $oEx->getMessage(),
                )); 
            }
        }
        return $this;
    }

    protected function updateExchangeRate() {
        if ((boolean)$this->oModul->getConfig('exchangerate_update')) {
            $aCurrencies = array();
            foreach ($this->aOrders as $aOrder) {
                if(isset($aOrder['Order']['Currency']) && !empty($aOrder['Order']['Currency']) && !in_array($aOrder['Order']['Currency'], $aCurrencies)){
                    $aCurrencies[] = $aOrder['Order']['Currency'];
                    MLCurrency::gi()->updateCurrencyRate($aOrder['Order']['Currency']);
                }

            }
        }
        return $this;
    }

    protected function normalizeOrder($aOrder) {
        $aExistingOrder = array();
        foreach (array('Main', 'Billing', 'Shipping') as $sAddressType) {
            if (!isset($aOrder['AddressSets'][$sAddressType]) || empty($aOrder['AddressSets'][$sAddressType])) {
                $aExistingOrder = empty($aExistingOrder) ? MLOrder::factory()->getByMagnaOrderId($aOrder['MPSpecific']['MOrderID'])->get('orderdata') : $aExistingOrder;
                if (isset($aExistingOrder['AddressSets'][$sAddressType])) {
                    $aOrder['AddressSets'][$sAddressType] = $aExistingOrder['AddressSets'][$sAddressType];
                }
            }
        }
        if (!isset($aOrder['AddressSets']['Main']) || count($aOrder['AddressSets']['Main']) < 5) {// < 5 = threshold
            throw new Exception('Main Address is empty.');
        }
        return MLHelper::gi('model_service_orderdata_normalize')->setUpdateMode($this->blUpdateMode)->normalizeServiceOrderData($aOrder);

    }
    
    /**
     * sets local test orders
     * @param array $aOrders
     * @return \ML_Modul_Model_Service_ImportOrders_Abstract
     */
    public function setLocalTestOrders ($aOrders) {
        $this->aLocalTestOrders = $aOrders;
        return $this;
    }
    
    /**
     * get local test orders
     * @return array
     */
    protected function getLocalTestOrders () {
        return $this->aLocalTestOrders;
    }


    protected function getOrders() {
        MLHelper::gi('stream')->stream('Getting orders from magnalister-server');
        if (!$this->oModul->getConfig('import') || $this->oModul->getConfig('import') == 'false') {
            return $this;
        } else {
            $iStartTime = MLSetting::gi()->get('iOrderMinTime');
            $aTimes = array($iStartTime);
            foreach (array('orderimport.lastrun', 'preimport.start') as $sConfig) {
                $iTimestamp = strtotime($this->oModul->getConfig($sConfig));
                if ($sConfig == 'orderimport.lastrun') {
                    $iTimestamp = $iTimestamp - MLSetting::gi()->get('iOrderPastInterval');
                } elseif (
                    $sConfig == 'preimport.start'
                    &&
                    $iTimestamp > time()
                ) {
                    return $this;
                }
                $aTimes[] = $iTimestamp;
                $iStartTime = $iTimestamp > $iStartTime ? $iTimestamp : $iStartTime;
            }
        }

        $aRequest = array(
            'ACTION' => $this->sGetOrdersApiAction,
            'SUBSYSTEM' => $this->getMarketPlaceName(),
            'MARKETPLACEID' => $this->getMarketPlaceId(),
            'IgnoreLastImport' => false,
            'BEGIN' => gmdate('Y-m-d H:i:s', $iStartTime),
            'OFFSET' => array(
                'COUNT' => 50,
                'START' => 0,
            )
        );
        while (is_array($aRequest)) {
            try {
                $this->aOrdersList = array();
                $this->aOrders = array();
                if ($this->getLocalTestOrders() === null) {
                    $aResponse = MagnaConnector::gi()->submitRequest($aRequest);
                    if (!isset($aResponse['HASNEXT']) || !$aResponse['HASNEXT']) {
                        $aRequest = null;
                    } else {
                        $aRequest['OFFSET']['START'] += $aRequest['OFFSET']['COUNT'];
                    }
                    foreach ($aResponse['DATA'] as $aOrder) {
                        $this->aOrders[] = $aOrder;
                    }
                } else {
                    $this->aOrders = $this->getLocalTestOrders();
                    $aRequest = null;
                }
                $this->updateExchangeRate();
                if ($this->isMutex()) {
                    $this->doOrders();
                    if($this->getLocalTestOrders() === null) {
                        $this->acknowledgeOrders();
                    }
                }
            } catch (MagnaException $oEx) {
                $aRequest = null;
                if (MAGNA_CALLBACK_MODE == 'STANDALONE') {
                    echo print_m($oEx->getErrorArray(), 'Error: '.$oEx->getMessage(), true);
                } elseif (MLSetting::gi()->get('blDebug')) {
                    MLMessage::gi()->addFatal($oEx->getMessage());
                }
                if (MLSetting::gi()->get('blDebug') && ($oEx->getMessage() == ML_INTERNAL_API_TIMEOUT)) {
                    $oEx->setCriticalStatus(false);
                }
            }
        }
        return $this;
    }

    abstract public function canDoOrder(ML_Shop_Model_Order_Abstract $oOrder, &$aOrder);

    protected function hookAddOrder(&$aOrder) {
        /* {Hook} "addOrder": Enables you to extend or modify order that's being imported from marketplace.
            Variables that can be used: 
            <ul>
                <li>$iMarketplaceId (int): Id of marketplace</li>
                <li>$sMarketplaceName (string): Name of marketplace</li>
                <li>&$aOrder (array): Order data received from marketplace.</li>
            </ul>
        */
        if (($sHook = MLFilesystem::gi()->findhook('addOrder', 1)) !== false) {
            $iMarketplaceId = MLModul::gi()->getMarketPlaceId();
            $sMarketplaceName = MLModul::gi()->getMarketPlaceName();
            require $sHook;
        }
    }

    protected function doOrders() {
        $aTabIdents = MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.tabident')->get('value');
        foreach ($this->aOrders as $iOrder => $aOrder) {            
            $this->blUpdateMode = $this->blUpdateMode || !isset($aOrder['Products']) || empty($aOrder['Products']);
            $sMpId = MLModul::gi()->getMarketPlaceId();
            MLSetting::gi()->set('sCurrentOrderImportLogFileName', 'OrderImport_'.$sMpId, true);
            MLSetting::gi()->set('sCurrentOrderImportMarketplaceOrderId', isset($aOrder['MPSpecific']) && isset($aOrder['MPSpecific']['MOrderID']) ? $aOrder['MPSpecific']['MOrderID'] : 'unknown', true);
            MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                'OrderDataApiResponse' => $aOrder,
            ));
            try {
                $aOrder = $this->normalizeOrder($aOrder);
                $this->hookAddOrder($aOrder);
                MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                    'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                    'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                    'OrderDataNormalized' => $aOrder,
                ));
                MLHelper::gi('stream')->deeper('Start ('.$aOrder['MPSpecific']['MOrderID'].')');
                //check if order exist
                $oOrder = MLOrder::factory()->getByMagnaOrderId($aOrder['MPSpecific']['MOrderID']);
                if ($oOrder->get('orders_id') !== null && !$this->blUpdateMode) {
                    throw MLException::factory('Model_Service_ImportOrders_OrderExist')->setShopOrder($oOrder);
                }

                if (isset($aOrder['MPSpecific']['MPreviousOrderID']) && !empty($aOrder['MPSpecific']['MPreviousOrderID'])) { // check for extend order
                    $sMPreviousOrderID = is_array($aOrder['MPSpecific']['MPreviousOrderID']) ? $aOrder['MPSpecific']['MPreviousOrderID']['id'] : $aOrder['MPSpecific']['MPreviousOrderID'];
                    $oOrder = MLOrder::factory()->getByMagnaOrderId($sMPreviousOrderID);
                    $blSendmail = !in_array($aOrder['MPSpecific']['MOrderID'], $aOrder['MPSpecific']['MPreviousOrderIDS']);
                } else {
                    $blSendmail = $oOrder->get('orders_id') === null;
                }
                $sInfo = $this->canDoOrder($oOrder, $aOrder);
                if ($oOrder->get('special') === null) {
                    $oOrder
                        ->set('mpid', MLModul::gi()->getMarketPlaceId())
                        ->set('platform', MLModul::gi()->getMarketPlaceName())
                        ->set('special', $this->aOrders[$iOrder]['MPSpecific']['MOrderID'])
                        ->set('logo', null)//reset oder-logo when order is updated
                    ;
                }
                $this->aOrders[$iOrder] = $oOrder->shopOrderByMagnaOrderData($aOrder);
                $oOrder
                    ->set('mpid', MLModul::gi()->getMarketPlaceId())
                    ->set('platform', MLModul::gi()->getMarketPlaceName())
                    ->set('logo', null)//reset oder-logo when order is updated
                    ->set('status', $this->aOrders[$iOrder]['Order']['Status'])
                    ->set('data', $this->aOrders[$iOrder]['MPSpecific'])
                    ->set('special', $this->aOrders[$iOrder]['MPSpecific']['MOrderID'])
                    ->set('orderdata', $this->aOrders[$iOrder])
                    ->save()
                    ->triggerAfterShopOrderByMagnaOrderData()
                ;
                if (    $blSendmail 
                        && MLModul::gi()->getConfig('mail.send') == 'true'
                        && ! $this->blUpdateMode
                        ) {  
                    $this->sendPromotionMail($this->aOrders[$iOrder]);//we should use $this->aOrders, because some important data like user password is filled by shopspecific 
                }
                MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                    'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                    'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',                    
                    'Info' => 'imported in '.$this->aOrders[$iOrder]['MPSpecific']['MOrderID'].'.'.($blSendmail && MLModul::gi()->getConfig('mail.send') == 'true' ? ' Promotion mail sended.' : ''),
                    'FinalTableData' => $oOrder->data(),
                ));
                // $aData=$oOrder->data();
                $this->aOrdersList[$iOrder] = $oOrder;
            } catch (ML_Modul_Exception_Model_Service_ImportOrders_OrderExist $oEx) {
                $sInfo = $oEx->getMessage();
                MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                    'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                    'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                    'ML_Modul_Exception_Model_Service_ImportOrders_OrderExist' => $sInfo,
                )); // temporarily log to check some problem in extended orders
                $this->aOrdersList[$iOrder] = $oEx->getShopOrder();
            } catch (Exception $oEx) {
                $sInfo = $oEx->getMessage();
                MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                    'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                    'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                    'Exception' => $sInfo,
                    'Backtrace' => substr($oEx->getTraceAsString(),0,500).'...',
                ));
                MLMessage::gi()->addDebug("order import : ".$sInfo); // exception like when one currency doesn't exit in shop is shown by a warning message
                unset($this->aOrders[$iOrder]);
            }
            if (MLSetting::gi()->get('blDebug')) {
                MLLog::gi()->add('ordersSync', array(
                    'display' => array(
                        'info' => $sInfo,
                        'marketplace' => MLModul::gi()->getMarketPlaceName().' ('.(isset($aTabIdents[MLModul::gi()->getMarketPlaceId()]) && $aTabIdents[MLModul::gi()->getMarketPlaceId()] != '' ? $aTabIdents[MLModul::gi()->getMarketPlaceId()].' - ' : '').MLModul::gi()->getMarketPlaceId().')',
                        'orderno_marketplace' => $aOrder['MPSpecific']['MOrderID'],
                        'orderno_shop' => (isset($oOrder) && $oOrder->exists() )? '<div class="order-link"><a class="ml-js-noBlockUi" target="_blank" href="'.$oOrder->getEditLink().'">'.$oOrder->get('orders_id').'</a></div>' : '&mdash;',
                        'status' => (isset($oOrder) && $oOrder->exists() )?$aOrder['Order']['Status']." - ".$oOrder->getShopOrderStatusName():'&mdash;'
                    )
                ));
            }
            MLHelper::gi('stream')->stream($sInfo.' ('.$aOrder['MPSpecific']['MOrderID'].')');
            MLHelper::gi('stream')->higher('End ('.$aOrder['MPSpecific']['MOrderID'].')');
        }
        return $this;
    }

    public function sendPromotionMailTest() {
        $oModul = $this->oModul;
        $aOrder = array(
            'AddressSets' => array(
                'Main' => array(
                    'EMail' => $oModul->getConfig('mail.originator.adress'),
                    'Firstname' => 'Max',
                    'Lastname' => 'Mustermann',
                )
            ),
            'Order' => array(
                'Currency' => $oModul->getConfig('currency')
            ),
            'Products' => array(
                array(
                    'Quantity' => 2,
                    'ItemTitle' => 'Lorem Ipsum - Das Buch',
                    'Price' => 12.99,
                ),
                array(
                    'Quantity' => 1,
                    'ItemTitle' => 'Dolor Sit Amet - Das Nachschlagewerk',
                    'Price' => 22.59,
                )
            )
        );
        return $this->sendPromotionMail($aOrder);
    }

    protected function sendPromotionMail($aOrder) {
        $oModule = $this->oModul;
        ob_start();
        {
            include MLFilesystem::gi()->getViewPath('hook_ordermailsummary');
            $sSummary = ob_get_contents();
        }
        ob_end_clean();

        $sMailTo = $aOrder['AddressSets']['Main']['EMail'];
        $sMailFrom = $oModule->getConfig('mail.originator.adress');

        $aReplace = array(
            '#FIRSTNAME#' => $aOrder['AddressSets']['Main']['Firstname'],
            '#LASTNAME#' => $aOrder['AddressSets']['Main']['Lastname'],
            '#PASSWORD#' => isset($aOrder['AddressSets']['Main']['Password']) ? $aOrder['AddressSets']['Main']['Password'] : '(wie bekannt)',
            '#ORIGINATOR#' => $oModule->getConfig('mail.originator.name'),
            '#EMAIL#' => $aOrder['AddressSets']['Main']['EMail'],
            '#MARKETPLACE#' => $oModule->getMarketPlaceName(),
            '#ORDERSUMMARY#' => $sSummary,
        );
        $sMailContent = $this->replace($oModule->getConfig('mail.content'), $aReplace);
        unset($aReplace['#ORDERSUMMARY']);
        $sMailSubject = $this->replace($oModule->getConfig('mail.subject'), $aReplace);
        MLHelper::gi('stream')->stream('Send promotion email to '.$sMailTo);
        try {
            MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                'Send email' => 'Send promotion email to '.$sMailTo,
            ));
        } catch (Exception $oEx) {

        }
        try {
            return MagnaConnector::gi()->submitRequest(array(
                'ACTION' => 'SendSaleConfirmationMail',
                'SUBSYSTEM' => 'Core',
                'RECIPIENTADRESS' => $sMailTo,
                'ORIGINATORNAME' => $oModule->getConfig('mail.originator.name'),
                'ORIGINATORADRESS' => $sMailFrom,
                'SUBJECT' => fixHTMLUTF8Entities($sMailSubject),
                'CONTENT' => $sMailContent,
                'BCC' => $oModule->getConfig('mail.copy') == 'true' && $sMailFrom != $sMailTo
            ));
        } catch (Exception $oEx) {
            return false;
        }
    }
}

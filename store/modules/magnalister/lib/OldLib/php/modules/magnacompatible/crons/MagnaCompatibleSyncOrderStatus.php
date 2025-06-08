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
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_MAGNALISTER_MODULES . 'magnacompatible/crons/MagnaCompatibleCronBase.php');

class MagnaCompatibleSyncOrderStatus extends MagnaCompatibleCronBase {

    /**
     * Name of the confirmation field. May be DATA or CONFIRMATIONS.
     * @var string
     */
    protected $confirmationResponseField = 'DATA';

    /**
     * List of all orders that have to be synced
     * list-entries are ML_Shop_Model_Order_Abstract
     * @var $oOrderList ML_Database_Model_Table_Abstract 
     */
    protected $oOrderList = array();

    /**
     * The order that is currently processed
     * @var $oOrder ML_Shop_Model_Order_Abstract
     */
    protected $oOrder = null;

    /**
     * The index of the current order in the list of all orders.
     * @var int
     */
    protected $iOrderIndex = 0;

    /**
     * A lookup table of the orders. Key is usually the MOrderID,
     * value is the index of the order in the list of all orders.
     * @var array
     */
    protected $aMorderId2OrderIndex = array();

    /**
     * List of all orders where shipping will be confirmed.
     * Will be the DATA element for the ConfirmShipment request.
     * @var array
     */
    protected $confirmations = array();

    /**
     * List of all orders that will be cancelled.
     * Will be the DATA element for the CancelShipment request.
     * @var array
     */
    protected $cancellations = array();

    /**
     * List of all order ids that have a changed status that is not
     * relevant for the marketplace.
     * @var array
     */
    protected $unprocessed = array();

    /**
     * set keys for order-list-model
     * @var array(table-model-key=>requset-data-key)
     */
    protected $aOrderTableKeysToRequestData = array('special' => 'MOrderID');

    public function __construct($mpID, $marketplace) {
        parent::__construct($mpID, $marketplace);
    }

    /**
     * Specifies the settings and their default values for order status
     * synchronisation. Assumes the order status synchronisation is 
     * disabled.
     * @return array
     *   List of settings
     */
    protected function getConfigKeys() {
        return array(
            'OrderStatusSync' => array(
                'key' => 'orderstatus.sync',
                'default' => 'no',
            ),
            'StatusCancelled' => array(
                'key' => 'orderstatus.cancelled',
                'default' => false
            ),
            'StatusShipped' => array(
                'key' => 'orderstatus.shipped',
                'default' => false,
            ),
            'CarrierMatchingTable' => array(
                'key' => 'orderstatus.carrier.dbmatching.table',
                'default' => false,
            ),
            'CarrierMatchingAlias' => array(
                'key' => 'orderstatus.carrier.dbmatching.alias',
                'default' => false,
            ),
            'TrackingCodeMatchingTable' => array(
                'key' => 'orderstatus.trackingcode.dbmatching.table',
                'default' => false,
            ),
            'TrackingCodeMatchingAlias' => array(
                'key' => 'orderstatus.trackingcode.dbmatching.alias',
                'default' => false,
            ),
        );
    }

    /**
     * Builds the base API request for this marketplace.
     * @return array
     *   The base request
     */
    protected function getBaseRequest() {
        return array(
            'SUBSYSTEM' => $this->marketplace,
            'MARKETPLACEID' => $this->mpID,
        );
    }

    /**
     * Helper method to execute a db matching query.
     * @return mixed
     *   A string or false if the matching config is empty.
     */
    protected function runDbMatching($tableSettings, $defaultAlias, $where) {
        if (!isset($tableSettings['Table']['table']) || empty($tableSettings['Table']['table']) || empty($tableSettings['Table']['column'])
        ) {
            return false;
        }
        if (empty($tableSettings['Alias'])) {
            $tableSettings['Alias'] = $defaultAlias;
        }

        return (string) MLDatabase::getDbInstance()->fetchOne('
			SELECT `' . $tableSettings['Table']['column'] . '` 
			  FROM `' . $tableSettings['Table']['table'] . '` 
			 WHERE `' . $tableSettings['Alias'] . '` = "' . MLDatabase::getDbInstance()->escape($where) . '"
		');
    }

    /**
     * Fetches a tracking code if supported by the marketplace.
     * @return string
     *   The tracking code
     */
    protected function getTrackingCode($orderId) {
        return $this->runDbMatching(array(
                    'Table' => $this->config['TrackingCodeMatchingTable'],
                    'Alias' => $this->config['TrackingCodeMatchingAlias']
                        ), 'orders_id', $orderId);
    }

    /**
     * Fetches a carrier if supported by the marketplace.
     * @return string
     *   The carrier
     */
    protected function getCarrier($orderId) {
        return $this->runDbMatching(array(
                    'Table' => $this->config['CarrierMatchingTable'],
                    'Alias' => $this->config['CarrierMatchingAlias']
                        ), 'orders_id', $orderId);
    }

    /**
     * Checks whether the status of the current order should be synchronized with
     * the marketplace.
     * @return bool
     */
    protected function isProcessable() {
        return in_array(
                $this->oOrder->get('status'), array(
            $this->config['StatusShipped'],
            $this->config['StatusCancelled']
                )
        );
    }

    /**
     * Builds an element for the ConfirmShipment request.
     * @return void
     */
    protected function confirmShipment() {
        $cfirm = array(
            'MOrderID' => $this->oOrder->get('special'),
            'ShippingDate' => localTimeToMagnaTime($this->oOrder->getShopOrderLastChangedDate()),
        );
        $aData = $this->oOrder->get('data');
        $aData['ML_LABEL_SHIPPING_DATE'] = $cfirm['ShippingDate'];

        $trackercode = $this->getTrackingCode();
        $carrier = $this->getCarrier();
        if (false != $carrier) {
            $aData['ML_LABEL_CARRIER'] = $cfirm['Carrier'] = $carrier;
        }
        if (false != $trackercode) {
            $aData['ML_LABEL_TRACKINGCODE'] = $cfirm['TrackingCode'] = $trackercode;
        }
        $this->oOrder->set('data', $aData);
        return $cfirm;
    }

    /**
     * Builds an element for the CancelShipment request
     * @return void
     */
    protected function cancelOrder() {
        $cncl = array(
            'MOrderID' => $this->oOrder->get('special')
        );
        $aData = $this->oOrder->get('data');
        $aData['ML_LABEL_ORDER_CANCELLED'] = $this->oOrder->getShopOrderLastChangedDate();
        $this->oOrder->set('data', $aData);
        return $cncl;
    }

    /**
     * Processes the current order.
     * @return void
     */
    protected function prepareSingleOrder() {
        switch ($this->oOrder->get('status')) {
            case $this->config['StatusShipped']: {
                    $this->confirmations[] = $this->confirmShipment();
                    break;
                }
            case $this->config['StatusCancelled']: {
                    $this->cancellations[] = $this->cancelOrder();
                    break;
                }
        }
    }

    /**
     * Adds confirmation information to the order item
     * @param array &$oOrder
     *   The order item
     * @param array $cData
     *   The confirmation element specific to this order
     * @return void
     */
    protected function storeConfirmation($oOrder, $cData) {
        
    }

    /**
     * Processes the confirmations send from the API.
     * Can be overwritten from subclasses if required.
     * 
     * @param array $result
     *   The entire API result.
     * @return void
     */
    protected function processResponseConfirmations($result) {
        if (!isset($result[$this->confirmationResponseField][0])) {
            return;
        }
        foreach ($result[$this->confirmationResponseField] as $cData) {
            if (!isset($cData['MOrderID'])) {
                continue;
            }
            //build key
            $sKey = '';
            foreach ($this->aOrderTableKeysToRequestData as $sCdata) {
                $sKey.='[' . $cData[$sCdata] . ']';
            }
            $oOrder = $this->oOrderList->getByKey($sKey);
            if ($oOrder !== null) {
                $this->storeConfirmation($oOrder, $cData);
            }
        }
        /*
          [DATA] => Array
          (
          [0] => Array
          (
          [BatchID] => 5885100948
          [AmazonOrderID] => 303-9828726-5714706
          [RecordNumber] => 1
          [State] => CONFIRM
          )

          )
         */
    }

    /**
     * Adds an error to the error log. Empty method that can be extended by subclasses.
     * 
     * @param array $error
     *   The entry for the error log.
     * @return void
     */
    protected function addToErrorLog($error) {
        
    }

    /**
     * Processes the API errors. If the error element contains a
     * DETAILS field with an MOrderID it can be added to an error log.
     * 
     * @param array $result
     *   The entire API result.
     * @return void
     */
    protected function processResponseErrors($result) {
        if (!isset($result['ERRORS'][0])) {
            return;
        }
        foreach ($result['ERRORS'] as $eData) {
            if (!isset($eData['DETAILS']['MOrderID'])) {
                continue;
            }
            $this->addToErrorLog($eData);
        }
    }

    /**
     * Submits the status update for ConfirmShipment and CancelShipment.
     * @param string $action
     *   The API action. Either ConfirmShipment or CancelShipment.
     * @param array $data
     *   The data for the DATA element of the API request
     * @return void
     */
    protected function submitStatusUpdate($action, $data) {
        if (empty($data)) {
            return;
        }
        $request = $this->getBaseRequest();
        $request['ACTION'] = $action;
        $request['DATA'] = $data;
        if ($this->_debugLevel >= self::DBGLV_MED) {
            $this->log(print_m($request, $action . ' Request'));
        }

        if ($this->_debugDryRun)
            return;
        try {
            $result = MagnaConnector::gi()->submitRequest($request);
        } catch (MagnaException $e) {
            if ($e->getCode() == MagnaException::TIMEOUT) {
                $this->oOrder->init(); //reset order object
                return;
            }
            $result = array();
        }
        if ($this->_debugLevel >= self::DBGLV_MED) {
            $this->log(print_m($result, $action . ' Response'));
        }
        $this->processResponseConfirmations($result);
        $this->processResponseErrors($result);
    }

    /**
     * Gets a list of all orders for this marketplace where the
     * magnalister_orders.orders_status differs from the one of the shop.
     * @return void
     */
    protected function getOrdersToSync() {
        $oOrder = MLOrder::factory()->setKeys(array_keys($this->aOrderTableKeysToRequestData));
        $aChanged = $oOrder->getOutOfSyncOrdersArray();
        $oList = $oOrder->getList();
        $oList->getQueryObject()->where("current_orders_id in ('" . implode("', '", $aChanged) . "')");
        foreach ($oList->getList() as $oChangedOrder) {
            $oChangedOrder->set('status', $oChangedOrder->getShopOrderStatus());
        }
        return $oList;
    }

    /**
     * Main method of the class that manages the order status update.
     * @return bool
     *   false if the orderstatus sync has been disabled, true otherwise.
     */
    public function process() {
        if ($this->config['OrderStatusSync'] != 'auto') {
            return false;
        }
        $this->oOrderList = $this->getOrdersToSync();
        $this->log(print_m($this->oOrderList->data(), "\n" . '$this->aOrders'));
        if (empty($this->oOrderList))
            return true;

        $this->confirmations = array();
        $this->cancellations = array();
        $this->unprocessed = array();
        foreach ($this->oOrderList->getList() as $key => $oOrder) {
            $this->oOrder = $oOrder;
            $this->iOrderIndex = $key;
            if (!$this->isProcessable()) {
                $this->unprocessed[] = $oOrder;
                continue;
            }
            $this->prepareSingleOrder();
        }
        $this->submitStatusUpdate('ConfirmShipment', $this->confirmations);
        $this->submitStatusUpdate('CancelShipment', $this->cancellations);
        if (!$this->_debugDryRun) {
            $this->oOrderList->save();
        }

        return true;
    }

}

<?php

/**
 * File OrderModel.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * It is available through the world-wide-web at this URL:
 * http://involic.com/license.txt
 * If you are unable to obtain it through the world-wide-web,
 * please send an email to license@involic.com so
 * we can send you a copy immediately.
 *
 * eBay Listener Itegration with PrestaShop e-commerce platform.
 * Adding possibilty list PrestaShop Product dirrectly to eBay.
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2015 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
class Order_OrderModel extends AbstractModel
{
    const STATUS_CHECKOUT_INCOMPLETE = 0;
    const STATUS_CHECKOUT_COMPLETE = 1;

    const STATUS_PAYMENT_NONE = 0;
    const STATUS_PAYMENT_PENDING = 1;
    const STATUS_PAYMENT_FAIL = 2;
    const STATUS_PAYMENT_COMPLETE = 3;

    const STATUS_SHIPPING_NONE = 0;
    const STATUS_SHIPPING_PENDING = 1;
    const STATUS_SHIPPING_FAIL = 2;
    const STATUS_SHIPPING_COMPLETE = 3;

    const STATUS_UPDATE_EBAY_NONE = 0;
    const STATUS_UPDATE_EBAY_PAYMENT = 1;
    const STATUS_UPDATE_EBAY_SHIPPING = 2;
    const STATUS_UPDATE_EBAY_BOTH = 3;
    const STATUS_UPDATE_EBAY_TRACKING = 4;

    public $order_id;
    public $presta_order_id;
    public $containing_order;
    public $buyer_id;
    public $buyer_email;
    public $buyer_name;
    public $buyer_address;
    public $status_checkout;
    public $status_payment;
    public $status_shipping;
    public $paid;
    public $currency;
    public $message;
    public $payment_method;
    public $payment_paypal_email;
    public $payment_date;
    public $shipping_method;
    public $shipping_cost; // float
    public $shipping_date;
    public $create_date;
    public $update_date;
    public $account_id;
    public $tax;
    public $sales_record_number;
    public $order_to_process;

    protected $_shippingName = null;

    public function __construct($id = null, $id_lang = null)
    {
        $this->table = "prestabay_order";
        $this->identifier = "id";

        $this->fieldsRequired = array();

        $this->fieldsSize = array();

        $this->fieldsValidate = array();
        parent::__construct($id, $id_lang);
    }

    public function getFields($removeNull = true)
    {
        parent::validateFields();
        $returnArray = array(
            'order_id'             => pSQL($this->order_id),
            'presta_order_id'      => $this->presta_order_id,
            'containing_order'     => (int)$this->containing_order,
            'buyer_id'             => pSQL($this->buyer_id),
            'buyer_email'          => pSQL($this->buyer_email),
            'buyer_name'           => pSQL($this->buyer_name),
            'buyer_address'        => pSQL($this->buyer_address),
            'status_checkout'      => (int)$this->status_checkout,
            'status_payment'       => (int)$this->status_payment,
            'status_shipping'      => (int)$this->status_shipping,
            'paid'                 => (float)$this->paid,
            'currency'             => pSQL($this->currency),
            'message'              => pSQL($this->message),
            'payment_method'       => pSQL($this->payment_method),
            'payment_paypal_email' => pSQL($this->payment_paypal_email),
            'payment_date'         => $this->payment_date,
            'shipping_method'      => pSQL($this->shipping_method),
            'shipping_cost'        => (float)$this->shipping_cost,
            'shipping_date'        => $this->shipping_date,
            'create_date'          => $this->create_date,
            'update_date'          => $this->update_date,
            'account_id'           => is_null($this->account_id) ? null : ((int)$this->account_id),
            'tax'                  => pSQL($this->tax),
            'sales_record_number'  => (int)$this->sales_record_number,
            'order_to_process'     => (int)$this->order_to_process,
        );

        if ($removeNull) {
            foreach ($returnArray as $k => $value) {
                if (is_null($value) || $value === "") {
                    unset($returnArray[$k]);
                }
            }
        }

        return $returnArray;
    }

    public function setTax($taxValues = array())
    {
        $this->tax = serialize($taxValues);
    }

    public function getTax()
    {
        if ($this->tax) {
            return unserialize($this->tax);
        }

        return array();
    }

    public function getShippingMethodName()
    {
        if (!$this->id) {
            return '';
        }
        $model = new ImportShippingModel();
        $shippingName = $model->getShippingNameById($this->shipping_method);

        return ($shippingName == false) ? $this->shipping_method : $shippingName;
    }

    public function isPreparedForImport()
    {
        if (!$this->id) {
            return false;
        }

        return $this->_checkStatusReadyForImport($this->status_checkout, $this->status_payment);
    }

    protected function _checkStatusReadyForImport($checkout, $payment)
    {
        if ($checkout == Order_OrderModel::STATUS_CHECKOUT_INCOMPLETE &&
            $payment != Order_OrderModel::STATUS_PAYMENT_COMPLETE
        ) {
            // eBay Buyer don't complete checkout and don't have success payment
            return false;
        }

        return true;
    }

    /**
     * Find existing order info
     *
     * @param int $orderId eBay order number
     * @return array
     */
    public function findOrder($orderId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . $this->table . " WHERE order_id= '" . $orderId . "'";

        return Db::getInstance()->getRow($sql, false);
    }

    /**
     * Try to find eBay order that connected to PS order
     *
     * @param int $prestaShopOrderId order number in PS system
     */
    public function findOrderByPrestaShopId($prestaShopOrderId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . $this->table . " WHERE presta_order_id= " . ((int)$prestaShopOrderId) . "";

        return Db::getInstance()->getRow($sql, false);
    }

    /**
     * Delete specific eBay order
     * @param <type> $id
     */
    public function deleteOrder($id, $removeItems = true)
    {
        if ($removeItems) {
            $orderItemModel = new Order_OrderItemsModel();
            $orderItemModel->deleteOrderItems($id);
        }
        $removeSql = "DELETE FROM " . _DB_PREFIX_ . $this->table . " WHERE id = " . $id;
        Db::getInstance()->Execute($removeSql);
    }


    /**
     * Receive information about ebay order and try to import new/updated information into PrestaBay DB
     *
     * Do nothing with find connected items or importing order into PrestaShop
     *
     * @param array $orderInformation
     * @return int ID of imported order
     */
    public function importUpdateOrderInformation($orderInformation)
    {
        $existingOrderInformation = $this->findOrder($orderInformation['order_id']);

        if ($existingOrderInformation && $existingOrderInformation['update_date'] == $orderInformation['update_date']) {
            return $existingOrderInformation['id'];
        }

        $isNew = true;
        $importData = $this->convertEbayOrderToImportData($orderInformation);

        if ($existingOrderInformation) {
            $importData['id'] = $existingOrderInformation['id'];
            $isNew = false;
        }

        $importData['order_to_process'] = 1;
        $orderId = $this->createUpdateOrderData($importData);
        $this->createUpdateExternalTransactionData($orderId, $orderInformation);
        $this->createUpdateOrderItems($orderId, $orderInformation);
        if ($isNew) {
            // Log message has added only when new order created
            Order_LogModel::addLogMessage($orderId, L::t('Imported order from eBay'));
        }

        return $orderId;
    }

    /**
     * Transform data of order api response to order model data
     *
     * @param array $orderInformation
     *
     * @return array
     */
    protected function convertEbayOrderToImportData($orderInformation)
    {
        // We don't have order in DB or such order was update on eBay
        return array(
            'order_id'             => $orderInformation['order_id'],
            'containing_order'     => $orderInformation['containing_order'],
            'buyer_id'             => $orderInformation['buyer']['userid'],
            'buyer_email'          => trim(trim($orderInformation['buyer']['email']), '\n'),
            'buyer_name'           => $orderInformation['buyer']['name'],
            'buyer_address'        => serialize($orderInformation['address']),
            'status_checkout'      => $orderInformation['status']['checkout'],
            'status_payment'       => $orderInformation['status']['payment'],
            'status_shipping'      => $orderInformation['status']['shipping'],
            'paid'                 => $orderInformation['paid'],
            'currency'             => $orderInformation['currency'],
            'message'              => $orderInformation['checkout_message'],
            'payment_method'       => $orderInformation['payment_method'],
            'payment_paypal_email' => $orderInformation['payment_paypal_email'],
            'payment_date'         => $orderInformation['payment_date'],
            'shipping_method'      => $orderInformation['shipping_method'],
            'shipping_cost'        => $orderInformation['shipping_cost'],
            'shipping_date'        => $orderInformation['shipping_date'],
            'create_date'          => $orderInformation['created_date'],
            'update_date'          => $orderInformation['update_date'],
            'account_id'           => $orderInformation['account_id'],
            'tax'                  => serialize(isset($orderInformation['tax']) ? $orderInformation['tax'] : array()),
            'sales_record_number'  => $orderInformation['selling_manager_sales_record_number'],
        );
    }

    protected function createUpdateOrderData($importData)
    {
        $ebayOrderModel = new Order_OrderModel();
        $result = $ebayOrderModel->setData($importData)->save();
        if (!$result) {
            throw new Exception(mysql_error());
        }

        return $ebayOrderModel->id;
    }

    protected function createUpdateExternalTransactionData($orderId, $orderInformation)
    {
        // Try to save information related to external transaction
        if (empty($orderInformation['external_transactions'])
            || !is_array($orderInformation['external_transactions'])
        ) {
            return false;
        }

        $externalTransactionModel = new Order_ExternalTransactionsModel();
        $externalTransactionModel->removeTransactionRelatedToOrder($orderId);
        foreach ($orderInformation['external_transactions'] as $singleExtTransaction) {
            $externalTransactionModel->setData(
                array(
                    'id'                 => null,
                    'transaction_id'     => $singleExtTransaction['transaction_id'],
                    'prestabay_order_id' => $orderId,
                    'time'               => $singleExtTransaction['time'],
                    'fee'                => $singleExtTransaction['fee'],
                    'total'              => $singleExtTransaction['total'],
                    'refund'             => $singleExtTransaction['refund'],
                )
            )->save();
        }
    }

    protected function createUpdateOrderItems($orderId, $orderInformation)
    {
        if (empty($orderInformation['items']) || !is_array($orderInformation['items'])) {
            return false;
        }

        // Save order Items (if order was grouped we possible to delete old transactions)
        $orderItemModel = new Order_OrderItemsModel();
        foreach ($orderInformation['items'] as $orderItem) {
            // Save Order Item. This operation can throw exception on insert new record
            $orderItemModel->createUpdateEbayOrderItem($orderId, $orderItem);
        }
    }

    /**
     * Get all orders that need to be processed
     * This can be new orders from ebay, or orders updated by new ebay information
     */
    public function getOrdersToProcess()
    {
        $sql = "SELECT id FROM " . _DB_PREFIX_ . "prestabay_order WHERE order_to_process = 1";

        $result = Db::getInstance()->ExecuteS($sql, true, false);

        
        if (!is_array($result)) {
            return array();
        }
        $columnValues = array();
        foreach ($result as $row) {
            $columnValues[] = $row['id'];
        }

        return $columnValues;
    }

    /**
     * Find mapping for ebay order items.
     * First try to find mapping in PrestaBay
     * Then if mode active using SKU match
     */
    public function findMapping()
    {
        if (!$this->id) {
            throw new Exception(L::t("Please load model"));
        }

        $this->connectItemsByPrestaBay();

        if (Configuration::get("INVEBAY_SYNCH_ORDER_SKU") == 1) {
            if ($this->connectItemsBySku()) {
                Order_LogModel::addLogMessage($this->id, L::t('Items has been connected to PS by Reference'));
            }
        }
    }

    /**
     * Try to create/update PrestaShop Order
     * @return bool does operation successful
     * @throws Exception
     */
    public function createUpdatePSOrder()
    {
        if (!$this->id) {
            throw new Exception(L::t('Please load model'));
        }

        if (!$this->isPrestaBayConnection()) {
            return false;
        }

        if (Configuration::get("INVEBAY_SYNCH_ORDER_IMPORT") != 1) {
            return false;
        }

        if ((int)$this->presta_order_id > 0) {
            $this->updatePrestaShopOrder();

            return true;
        }

        if ((int)$this->presta_order_id == 0) {
            $isAllowedToCreate    = true;
            if (Configuration::get("INVEBAY_SYNCH_ORDER_OK_PAYMENT") != 1) {
                // When not enabled immediate order creation
                // eBay Buyer don't complete checkout and don't have success payment
                $isAllowedToCreate = $this->isPreparedForImport();
            }

            if ($isAllowedToCreate) {
                $this->createPrestaShopOrder();
            }
        }

        return true;
    }

    public function getBuyerAddress()
    {
        if (is_null($this->id)) {
            return array();
        }

        // Migrate address to json
        $address = json_decode($this->buyer_address, true);
        if ($address == false) {
            $address = unserialize($this->buyer_address);
        }

        return $address;
    }

    public function setBuyerAddress($newAddress)
    {
        $defaultAddress = array(
            'fistname' => 'N/A',
            'lastname' => '',
            'street' => array('N/A', ''),
            'state' => '',
            'postal_code' => '',
            'country' => '',
            'city' => '',
            'phone' => '',
        );

        $buyerAddress = $newAddress + $defaultAddress;
        $this->buyer_address = json_encode($buyerAddress);
    }

    public function getItemsTotal()
    {
        if (is_null($this->id)) {
            return 0;
        }
        $orderItemModel = new Order_OrderItemsModel();

        return $orderItemModel->getItemsTotal($this->id);
    }

    /**
     * Perform checking for eBay Order is created for products that linked for
     * Prestashop product
     */
    public function isPrestaBayConnection()
    {
        if (!$this->id) {
            throw new Exception(L::t("Please load model"));
        }
        $sql    = 'SELECT count(*) as cnt FROM ' . _DB_PREFIX_ . 'prestabay_order_items WHERE presta_id > 0 AND presta_lang_id > 0 AND order_id = ' . $this->id;
        $result = Db::getInstance()->getRow($sql, false);
        if (isset($result['cnt']) && $result['cnt'] > 0) {
            // Have some products created by PrestaBay
            return true;
        }

        // No product in eBay Order that created by PrestaBay
        return false;
    }


    /**
     * Perform check that for selected order having at least one item on PS that
     * have same SKU => Reference. When try set sych connection
     *
     * @throws Exception
     * @return boolean
     */
    public function connectItemsBySku()
    {
        if (!$this->id) {
            throw new Exception(L::t("Please load model"));
        }
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'prestabay_order_items WHERE (sku IS NOT NULL) AND (presta_id IS NULL) AND order_id = ' . $this->id;

        $result      = Db::getInstance()->ExecuteS($sql, true, false);
        $isConverted = false;

        if (!is_array($result)) {
            return false;
        }
        foreach ($result as $singleItem) {
            $productInfo = $this->_getPSProductByReference($singleItem['sku']);

            if (!empty($productInfo) && is_array($productInfo)) {
                $productItem                    = reset($productInfo);
                $orderItemModel                 = new Order_OrderItemsModel($singleItem['id']);
                $orderItemModel->presta_id      = $productItem['id_product'];
                $orderItemModel->presta_lang_id = (int)(Configuration::get('PS_LANG_DEFAULT'));
                $orderItemModel->save();
                $isConverted = true;
            } else {
                // No standard product by reference, check for attribute
                $productInfo = $this->_getPSProductAttributeByReference($singleItem['sku']);
                if (!empty($productInfo) && is_array($productInfo)) {
                    $productItem                    = reset($productInfo);
                    $orderItemModel                 = new Order_OrderItemsModel($singleItem['id']);
                    $orderItemModel->presta_id      = $productItem['id_product'];
                    $orderItemModel->presta_attr_id = $productItem['id_product_attribute'];
                    $orderItemModel->presta_lang_id = (int)(Configuration::get('PS_LANG_DEFAULT'));
                    $orderItemModel->save();
                    $isConverted = true;
                }
            }
        }

        return $isConverted;
    }

    /**
     * Perform check for selected order having not mapped items.
     * And if so try to find corresponding items
     */
    public function connectItemsByPrestaBay()
    {
        if (!$this->id) {
            throw new Exception(L::t("Please load model"));
        }

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'prestabay_order_items WHERE presta_id IS NULL AND order_id = ' . $this->id;
        $result = Db::getInstance()->ExecuteS($sql, true, false);

        if (empty($result) || !is_array($result)) {
            return false;
        }
        foreach ($result as $singleItem) {
            $variationInfo = null;
            if (!empty($singleItem['variation_info'])) {
                $variationInfo = unserialize($singleItem['variation_info']);
            }
            // Check for having connection of selected product on PrestaShop
            // When have connection, add this information to DB
            // Used for create PrestaShop order
            $result = Selling_ConnectionsModel::getPrestaConnectionByEbayId($singleItem['item_id'], $variationInfo);

            if ($result !== false) {
                $orderItemModel = new Order_OrderItemsModel($singleItem['id']);
                $orderItemModel->presta_id = $result['presta_id'];
                $orderItemModel->presta_attr_id = isset($result['attribute_id']) ? $result['attribute_id'] : null;
                $orderItemModel->presta_lang_id = $result['language_id'];
                $orderItemModel->save();
            }
        }
    }

    /**
     * Increase order products qty by order qty value
     */
    public function increaseQTY()
    {
        $this->changeQTY(1);
    }

    /**
     * Change product qty by order qty value
     *
     * @param int $sign > 0 increase, < 0 decrease
     * @throws Exception
     */
    protected function changeQTY($sign)
    {
        if ($sign > 0) {
            // increase
            $modify = -1;
        } else if ($sign < 0) {
            // decrease
            $modify = +1;
        }

        if (!$this->id) {
            throw new Exception(L::t("Please load model"));
        }
        $itemsModel = new Order_OrderItemsModel();
        $connectedProducts = $itemsModel->getPrestaShopConnectedProducts($this->id);
        if (count($connectedProducts) > 0) {
            foreach ($connectedProducts as $itemInfo) {
                PrestaShopHelper::changeQTY($itemInfo['presta_id'], $itemInfo['presta_attr_id'], $modify * (int)$itemInfo['qty']);
            }
        }
    }

    /**
     * Create Order in PrestaShop based on information store in
     * PrestaBay Table
     */
    public function createPrestaShopOrder()
    {
        if (!$this->isPrestaBayConnection()) {
            throw new Exception(L::t('Not PrestaBay Product in eBay Order'));
        }

        if (!is_null($this->presta_order_id) && $this->presta_order_id > 0) {
            throw new Exception(L::t('Already have PrestaShop Order'));
        }

        if (Configuration::get('INVEBAY_SYNC_TASK_QTY') == 1 && Configuration::get("INVEBAY_SYNCH_ORDER_OK_PAYMENT") != 1
            && Configuration::get("INVEBAY_ORDER_QTY_SIMULATION") == 1) {
            // QTY simulation module. Each import order required increase product qty
            $this->increaseQTY();
        }

        $dataOrder                  = $this->getFields(false);
        $dataOrder['id']            = $this->id;
        $dataOrder['buyer_address'] = $this->getBuyerAddress();
        $dataOrder['tax']           = $this->getTax();
        $itemsModel                 = new Order_OrderItemsModel();
        $dataItems                  = $itemsModel->getOrderItems($this->id);

        $orderImportModel = new Order_ImportModel();
        try {
            $orderId = $orderImportModel->importNewOrder($dataOrder, $dataItems);
        } catch (Exception $e) {
            $message = L::t("Can't create PrestaShop Order") . ". " . L::t("Reason") . ":" . $e->getMessage();
            Order_LogModel::addLogMessage($this->id, $message);
            throw new Exception($message);
        }

        if ($orderId == false) {
            // Problem create Order
            Order_LogModel::addLogMessage($this->id, L::t("Can't create PrestaShop Order"));
            throw new Exception(L::t("Can't create PrestaShop Order"));
        } else {
            $viewOrderLink = '<a href="' . UrlHelper::getPrestaUrl("AdminOrders",
                    array(
                        'id_order' => $orderId,
                        'vieworder' => null
                    )) . '">View</a>';
            Order_LogModel::addLogMessage($this->id, L::t('Created order in PrestaShop') . ' - ' . $viewOrderLink);
        }
        $this->presta_order_id = $orderId;
        $this->save();

        return $this->presta_order_id;
    }

    /**
     * Update PrestaShop order information by information recived from eBay.
     * Change Status, Change Price
     */
    public function updatePrestaShopOrder()
    {
        if (!$this->isPrestaBayConnection()) {
            throw new Exception(L::t('Not PrestaBay Product in eBay Order'));
        }
        if (is_null($this->presta_order_id) || $this->presta_order_id == 0) {
            throw new Exception(L::t('First Create PrestaShop Order'));
        }

        $dataOrder                  = $this->getFields(false);
        $dataOrder['id']            = $this->id;
        $dataOrder['buyer_address'] = $this->getBuyerAddress();
        $itemsModel                 = new Order_OrderItemsModel();
        $dataItems                  = $itemsModel->getOrderItems($this->id);

        $orderImportModel = new Order_ImportModel();
        $orderImportModel->updatePrestaOrderRelatedToEbayOrderChange(
            $dataOrder['presta_order_id'],
            $dataOrder,
            $dataItems
        );
    }

    /**
     * Call to updated related to PS Order => eBay order status
     *
     * @param int $psOrderId        PS order id
     * @param int $newOrderStatusId identify of new order status
     * @return bool
     */
    public function updateEbayOrderStatus($psOrderId, $newOrderStatusId)
    {
        $orderInfo = $this->findOrderByPrestaShopId($psOrderId);

        if ($orderInfo != array() && !is_null($orderInfo['account_id'])) {
            // If we found connected order and having account information call
            //  update to order status
            $updateResult = $this->_processUpdateOrderStatus($orderInfo, $newOrderStatusId);
            if ($updateResult != false) {
                return true;
            }
        }

        return false;
    }

    public function updateEbayTracking($psOrderId, $trackingNumber)
    {
        $orderInfo = $this->findOrderByPrestaShopId($psOrderId);

        if ($orderInfo != array() && !is_null($orderInfo['account_id'])) {
            // If we found connected order and having account information call
            //  update to order tracking
            $updateResult = $this->_processUpdateOrderTracking($orderInfo, $trackingNumber);
            if ($updateResult != false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Update eBay order status by click on button from PrestaBay order
     *
     * @param int $newOrderStatusId new status of order
     * @return boolean result of change
     */
    public function updateEbayOrderStatusByPrestaBayId($newOrderStatusId)
    {
        if (is_null($this->account_id)) {
            return false;
        }
        $orderInfo       = $this->getFields();
        $orderInfo['id'] = $this->id;
        $updateResult    = $this->_processUpdateOrderStatus($orderInfo, $newOrderStatusId);
        if ($updateResult != false) {
            if (($psOrderId = (int)$orderInfo['presta_order_id']) > 0) {
                // If order connected to PrestaShop, also change order status
                $importModel = new Order_ImportModel();
                $importModel->changeOrderStatusRelatedToEbayOrder($psOrderId, $orderInfo);
            }

            return true;
        }

        return false;
    }

    public function recalculateTaxForAllImportedOrders()
    {
        // Get all order imported to PresaBay and for witch create PS order
        // Go throw each of them and run recalclulate price

        $orderImportModel = new Order_ImportModel();
        $itemsModel       = new Order_OrderItemsModel();

        $sql    = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_order WHERE presta_order_id > 0";
        $orders = Db::getInstance()->ExecuteS($sql, true, false);
        $totalOrders = count($orders);
        echo "Found {$totalOrders} orders prepared for calculation<br/>";
        foreach ($orders as $dataOrder) {
            // Try to get buyer address
            $address = json_decode($dataOrder['buyer_address'], true);
            if ($address == false) {
                $address = unserialize($dataOrder['buyer_address']);
            }
            $dataOrder['buyer_address'] = $address;
            $dataOrder['tax']           = unserialize($dataOrder['tax']);

            $dataItems = $itemsModel->getOrderItems($dataOrder['id']);
            try {
                $orderImportModel->recalculateOrderPricePS15($dataOrder, $dataItems);
                echo " - Calculate TAX for PrestaShop order #{$dataOrder['presta_order_id']}<br/>";
            } catch (Exception $ex) {
                echo " - FAILED! Order #{$dataOrder['presta_order_id']}. Reason {$ex->getMessage()}<br/>";
            }

        }
        echo "Calculation finished<br/>";
    }

    protected function _processUpdateOrderStatus($orderInfo, $newOrderStatusId)
    {
        $accountModel = new AccountsModel($orderInfo['account_id']);
        if (is_null($accountModel->token) || is_null($accountModel->mode)) {
            Order_LogModel::addLogMessage(
                $orderInfo['id'],
                L::t('eBay Order Status update fail. Invalid account token')
            );

            return false;
        }

        $newStatusForEbayUpdate = self::STATUS_UPDATE_EBAY_NONE;
        $paymentStatusId        = (int)_PS_OS_PAYMENT_;

        $shippingStatusId = (int)_PS_OS_SHIPPING_;
        $shippingFinishId = (int)_PS_OS_DELIVERED_;
        $newOrderStatusId = (int)$newOrderStatusId;

        switch ($newOrderStatusId) {
            case $paymentStatusId:
                // Status of PS order is Complete Payment -> complete payment on eBay
                if ($orderInfo['status_payment'] != self::STATUS_PAYMENT_COMPLETE) {
                    $newStatusForEbayUpdate = self::STATUS_UPDATE_EBAY_PAYMENT;
                }
                break;
            case $shippingFinishId:
            case $shippingStatusId:
                // Status of PS order is Shipped -> set status for eBay as paid and shipped
                if ($orderInfo['status_payment'] != self::STATUS_PAYMENT_COMPLETE) {
                    $newStatusForEbayUpdate = self::STATUS_UPDATE_EBAY_PAYMENT;
                }
                if ($orderInfo['status_shipping'] != self::STATUS_SHIPPING_COMPLETE) {
                    $newStatusForEbayUpdate = ($newStatusForEbayUpdate == self::STATUS_UPDATE_EBAY_PAYMENT) ?
                        self::STATUS_UPDATE_EBAY_BOTH : self::STATUS_UPDATE_EBAY_SHIPPING;
                }
                break;

            default:
                return false;
        }

        if ($newStatusForEbayUpdate == self::STATUS_UPDATE_EBAY_NONE) {
            // Update for order not needed
            return false;
        }

        ApiModel::getInstance()->reset();
        $resultOfChangeOrder = ApiModel::getInstance()->ebay->sale->complete(
            array(
                'token'    => $accountModel->token,
                'mode'     => $accountModel->mode, // 0 - sandbox, 1 - production
                'action'   => $newStatusForEbayUpdate,
                'identify' => array(
                    'order_id' => $orderInfo['order_id']
                )
            )
        )->post();
        if (isset($resultOfChangeOrder['result']) && $resultOfChangeOrder['result']) {
            // Status for order successfully updated
            $this->updatePrestaBayOrderStatus($orderInfo['id'], $newStatusForEbayUpdate);

            return true;
        } else {
            Order_LogModel::addLogMessage(
                $orderInfo['id'],
                L::t(
                    'Error happens while try to update eBay Order Status. Details of error:' . ApiModel::getInstance(
                    )->getErrorsAsHtml()
                )
            );

            return false;
        }
    }

    /**
     * Send request to ebay with shipping tracking number
     *
     * @param $orderInfo
     * @param $trackingNumber
     * @return bool result of send
     */
    protected function _processUpdateOrderTracking($orderInfo, $trackingNumber)
    {
        $accountModel = new AccountsModel($orderInfo['account_id']);
        if (is_null($accountModel->token) || is_null($accountModel->mode)) {
            Order_LogModel::addLogMessage(
                $orderInfo['id'],
                L::t('eBay Order Status update fail. Invalid account token')
            );

            return false;
        }
        ApiModel::getInstance()->reset();
        $resultOfChangeOrder = ApiModel::getInstance()->ebay->sale->complete(
            array(
                'token'    => $accountModel->token,
                'mode'     => $accountModel->mode, // 0 - sandbox, 1 - production
                'action'   => self::STATUS_UPDATE_EBAY_TRACKING,
                'identify' => array(
                    'order_id' => $orderInfo['order_id'],
                    'tracking_number' => $trackingNumber
                )
            )
        )->post();

        if (isset($resultOfChangeOrder['result']) && $resultOfChangeOrder['result']) {
            // Tracking for order successfully updated
            Order_LogModel::addLogMessage($orderInfo['id'],
                    L::t("Tracking number send to ebay").": ".$trackingNumber);
            return true;
        } else {
            Order_LogModel::addLogMessage(
                $orderInfo['id'],
                L::t('Error happens while try to send tracking eBay Order. Details of error:' .
                    ApiModel::getInstance()->getErrorsAsHtml()
                )
            );

            return false;
        }

       return false;
    }

    /**
     * Change status for imported into PrestaBay order
     * @param int $prestaBayOrderId id of order into PrestaBay
     * @param int $newStatusKey     new status key for witch required update
     */
    public function updatePrestaBayOrderStatus($prestaBayOrderId, $newStatusKey)
    {
        $orderModel = new Order_OrderModel($prestaBayOrderId);

        switch ($newStatusKey) {
            case self::STATUS_UPDATE_EBAY_PAYMENT:
                Order_LogModel::addLogMessage($prestaBayOrderId, L::t('eBay Order Status updated to Paid'));
                $orderModel->status_payment = self::STATUS_PAYMENT_COMPLETE;
                break;
            case self::STATUS_UPDATE_EBAY_SHIPPING:
                $orderModel->status_shipping = self::STATUS_SHIPPING_COMPLETE;
                Order_LogModel::addLogMessage($prestaBayOrderId, L::t('eBay Order Status updated to Shipped'));
                break;
            case self::STATUS_UPDATE_EBAY_BOTH:
                Order_LogModel::addLogMessage($prestaBayOrderId, L::t('eBay Order Status updated to Paid and Shipped'));
                $orderModel->status_payment  = self::STATUS_PAYMENT_COMPLETE;
                $orderModel->status_shipping = self::STATUS_SHIPPING_COMPLETE;
                break;
        }

        $orderModel->save();
    }

    protected function _getPSProductByReference($reference)
    {
        $reference = trim($reference);

        return Db::getInstance()->ExecuteS(
            "SELECT `id_product` FROM `" . _DB_PREFIX_ . "product` p
            WHERE p.`reference` = '" . pSQL($reference) . "'",
            true,
            false
        );
    }

    protected function _getPSProductAttributeByReference($reference)
    {
        $reference = trim($reference);

        return Db::getInstance()->ExecuteS(
            "SELECT `id_product_attribute`, `id_product` FROM `" . _DB_PREFIX_ . "product_attribute` pa
            WHERE pa.`reference` = '" . pSQL($reference) . "'",
            true,
            false
        );
    }

}
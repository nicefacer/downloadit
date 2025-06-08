<?php
/**
 * File OrderItemsModel.php
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

class Order_OrderItemsModel extends AbstractModel
{
    public $order_id;
    public $item_id;
    public $transaction_id;
    public $presta_id;
    public $presta_lang_id;
    public $presta_attr_id;
    public $title;
    public $sku;
    public $qty;
    public $price;
    public $currency;
    public $variation_info;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_order_items";
        $this->identifier = "id";

        $this->fieldsRequired = array();

        $this->fieldsSize = array();

        $this->fieldsValidate = array(
        );
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        $returnArray = array(
            'order_id' => (int) $this->order_id,
            'item_id' => $this->item_id,
            'presta_id' => $this->presta_id,
            'presta_lang_id' => $this->presta_lang_id,
            'presta_attr_id' => $this->presta_attr_id,
            'transaction_id' => $this->transaction_id,
            'title' => pSQL($this->title),
            'sku' => pSQL($this->sku),
            'qty' => (int) $this->qty,
            'price' => (float) $this->price,
            'currency' => pSQL($this->currency),
            'variation_info' => pSQL($this->variation_info),
        );
        foreach ($returnArray as $k => $value) {
            if (is_null($value) || $value === "") {
                unset($returnArray[$k]);
            }
        }
        return $returnArray;
    }

    /**
     * Get Information about specific eBay item
     * @param int $itemId number of item on eBay
     * @param int $transactionId number of transaction on eBay
     * @return array with information about item or false
     */
    public function findOrderItem($itemId, $transactionId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . $this->table . " WHERE item_id= " . $itemId . " AND transaction_id = " . $transactionId;
        return Db::getInstance()->getRow($sql, false);
    }

    /**
     * Get all items for selected order as array
     * @param int $orderId PK of order
     * @return array list of items
     */
    public function getOrderItems($orderId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . $this->table . " WHERE order_id= " . $orderId;
        return Db::getInstance()->ExecuteS($sql, true, false);
    }

    /**
     * Get list of items in order that connected to PrestaShop product
     * @param $orderId
     * @return array
     */
    public function getPrestaShopConnectedProducts($orderId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . $this->table . " WHERE order_id= " . $orderId. " AND presta_id > 0 AND presta_lang_id > 0";
        return Db::getInstance()->ExecuteS($sql, true, false);
    }

    /**
     * Remove all items from single order
     * @param int $orderId PrestaBay order id (FK)
     */
    public function deleteOrderItems($orderId)
    {
        $removeSql = "DELETE FROM " . _DB_PREFIX_ . $this->table . " WHERE order_id = " . $orderId;
        Db::getInstance()->Execute($removeSql);
    }

    public function createUpdateEbayOrderItem($orderId, $orderItemInfo)
    {
        // Step 1. Check for have such information in our table
        $existingOrderItemInfo = $this->findOrderItem($orderItemInfo['item_id'], $orderItemInfo['transaction_id']);
        if ($existingOrderItemInfo) {
            // Step 2. When information exist compare orderId.
            if ($existingOrderItemInfo['order_id'] == $orderId) {
                // Step 3. OrderId equal, return id
                return $existingOrderItemInfo['id'];
            } else {
                // Step 4. OrderId not equal, remove this order with item
                // this is old order, that has been not grouped
                $orderModel = new Order_OrderModel;
                $orderModel->deleteOrder($existingOrderItemInfo['order_id']);
            }
        }
        // Step 5. (also when not information) Insert new Order Item
        $ebayOrderItemsModel = new Order_OrderItemsModel();
        $importData = array(
            'order_id'       => $orderId,
            'item_id'        => $orderItemInfo['item_id'],
            'transaction_id' => $orderItemInfo['transaction_id'],
            'title'          => $orderItemInfo['title'],
            'sku'            => trim($orderItemInfo['sku']),
            'qty'            => $orderItemInfo['qty'],
            'price'          => $orderItemInfo['price'],
            'currency'       => $orderItemInfo['currency'],
            'variation_info' => isset($orderItemInfo['variations']) ? serialize($orderItemInfo['variations']) : null
        );

        $result = $ebayOrderItemsModel->setData($importData)->save();
        if (!$result) {
            throw new Exception(mysql_error());
        }

        return $ebayOrderItemsModel->id;
    }

    public function importEbayOrderItem($orderId, $orderItemInfo)
    {
        // Step 1. Check for have such information in our table
        $existingOrderItemInfo = $this->findOrderItem($orderItemInfo['item_id'], $orderItemInfo['transaction_id']);
        if ($existingOrderItemInfo) {
            // Step 2. When information exist compare orderId.
            if ($existingOrderItemInfo['order_id'] == $orderId) {
                // Step 3. OrderId equal, return id
                return $existingOrderItemInfo['id'];
            } else {
                // Step 4. OrderId not equal, remove this order with item
                // this is old order, that has been not grouped
                $orderModel = new Order_OrderModel;
                $orderModel->deleteOrder($existingOrderItemInfo['order_id']);
            }
        }
        // Step 5. (also when not information) Insert new Order Item

        $ebayOrderItemsModel = new Order_OrderItemsModel();
        $importData = array(
            'order_id' => $orderId,
            'item_id' => $orderItemInfo['item_id'],
            'transaction_id' => $orderItemInfo['transaction_id'],
            'title' => $orderItemInfo['title'],
            'sku' => trim($orderItemInfo['sku']),
            'qty' => $orderItemInfo['qty'],
            'price' => $orderItemInfo['price'],
            'currency' => $orderItemInfo['currency'],
            'variation_info' => isset($orderItemInfo['variations'])?serialize($orderItemInfo['variations']):null
        );
        // Check for having connection of selected product on PrestaShop
        // When have connection, add this information to DB
        // Used for create PrestaShop order
        $result = Selling_ConnectionsModel::getPrestaConnectionByEbayId($orderItemInfo['item_id'], isset($orderItemInfo['variations'])?$orderItemInfo['variations']:null);

        if ($result !== false) {
            $importData['presta_id'] = $result['presta_id'];
            $importData['presta_lang_id'] = $result['language_id'];
            $importData['presta_attr_id'] = isset($result['attribute_id'])?$result['attribute_id']:null;
        }

        $result = $ebayOrderItemsModel->setData($importData)->save();
        if (!$result) {
            throw new Exception(mysql_error());
        }

        return $ebayOrderItemsModel->id;
    }

    public function getItemsTotal($orderId)
    {
        $sql = 'SELECT sum(price*qty) as total_price FROM '. _DB_PREFIX_ .'prestabay_order_items WHERE order_id = ' . $orderId;
        $result = Db::getInstance()->getRow($sql, false);
        return $result['total_price'];
    }

    public function filterByOrder($orderId)
    {
        $this->_filter = "order_id = {$orderId}";
        return $this;
    }

}
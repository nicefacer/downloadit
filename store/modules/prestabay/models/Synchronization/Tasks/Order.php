<?php

/**
 * File Order.php
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
class Synchronization_Tasks_Order extends Synchronization_BaseTask
{

    protected $_syncType = Log_SyncModel::LOG_TASK_ORDER;

    protected function _execute()
    {
        $this->importNewEbayOrders();

        $this->processEbayOrders();
    }

    protected function importNewEbayOrders()
    {
        $accountsModel = new AccountsModel();
        $successTime = false;
        $lastSuccessTime = Configuration::get('INVEBAY_SYNC_ORDER_SUCCESS_TIME');

        foreach ($accountsModel->getSelect()->getItems() as $account) {

            ApiModel::getInstance()->reset();
            $result = ApiModel::getInstance()->ebay->changes->orders(array(
                'token' => $account['token'],
                'updateTime' => $lastSuccessTime
            ))->post();

            if (count(ApiModel::getInstance()->getWarnings()) > 0) {
                $this->_appendWarning(ApiModel::getInstance()->getErrorsAsHtml(), array('ebay_account_id' => $account['id']));
            }

            if (count(ApiModel::getInstance()->getErrors()) > 0 || $result == false || !isset($result['orders'])) {
                if (count(ApiModel::getInstance()->getErrors()) > 0) {
                    $this->_appendError(ApiModel::getInstance()->getErrorsAsHtml(), array('ebay_account_id' => $account['id']));
                    $this->_hasErrors = true;
                }
                continue;
            }

            $this->_processAccountOrders($result['orders'], $account['id']);
            if (!$successTime) {
                $successTime = $result['time'];
            }
        }
        if ($successTime) {
            Configuration::updateValue('INVEBAY_SYNC_ORDER_SUCCESS_TIME', $successTime);
        }

    }

    protected function _processAccountOrders($orderList, $accountId)
    {
        $orderModel = new Order_OrderModel();       
        foreach ($orderList as $order) {
            $eBayOrderId = null;
            try {
                // import PrestaBay and PrestaShop Order based on eBay Data
                // ! this code also import order that has linked product to PrestaShop
                $order['account_id'] = $accountId;

                $eBayOrderId = $orderModel->importUpdateOrderInformation($order);
                if ($eBayOrderId == false) {
                    $this->_appendError(L::t("Failed insert order information into DB."), array('ebay_account_id' => $accountId));
                }
            } catch (Exception $ex) {
                $message = 'Error during import order ' . $ex->getMessage();
                Order_LogModel::addLogMessage($eBayOrderId, $message);

                // Problem on importing into PrestaBay stage
                $this->_appendError($message, array('pb_order_id' => $eBayOrderId, 'ebay_account_id' => $accountId));
                $this->_hasErrors = true;
                continue;
            }
        }
    }

    /**
     * Get list of orders that need to be processed
     * - Find mapping for PS products
     * - Create/Update PrestaShop order status
     */
    protected function processEbayOrders()
    {
        $orderModel = new Order_OrderModel();

        $idsOrdersToProcess = $orderModel->getOrdersToProcess();
        if (empty($idsOrdersToProcess) || !is_array($idsOrdersToProcess)) {
            return;
        }

        foreach ($idsOrdersToProcess as $orderId) {
            try {
                $specificOrderModel = new Order_OrderModel((int)$orderId);
                // Reset flag, to prevent double action
                $specificOrderModel->order_to_process = 0;
                $specificOrderModel->save();

                $specificOrderModel->findMapping();
                $specificOrderModel->createUpdatePSOrder();
                $specificOrderModel->save();
            } catch (Exception $ex) {
                // Problem on importing into PrestaBay stage
                $message = 'Error during processing order ' . $ex->getMessage();
                Order_LogModel::addLogMessage($orderId, $message);
                $this->_appendError($message, array('pb_order_id' => $orderId));
                $this->_hasErrors = true;
                continue;
            }
        }
    }
}
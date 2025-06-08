<?php

/**
 * File OrderController.php
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
class EbayOrderController extends BaseAdminController
{

    public function indexAction()
    {
        // view order list
        $grid = new Grids_Orders();
        echo $grid->getHtml();
    }

    public function viewAction()
    {
        // view specific order
        $orderId = UrlHelper::getGet("id", null);
        if (is_null($orderId)) {
            RenderHelper::addError(L::t("Invalid Order Id"));
            UrlHelper::redirect("order/index");
            return;
        }
        $orderModel = new Order_OrderModel($orderId);

        $this->view("order/view.phtml", array('order' => $orderModel));
    }

    public function createAction()
    {
        $orderId = UrlHelper::getGet('id', null);
        if (is_null($orderId)) {
            RenderHelper::addError(L::t("Invalid Order Id"));
            UrlHelper::redirect("order/index");
            return;
        }
        try {
            $orderModel = new Order_OrderModel($orderId);
            $orderModel->createPrestaShopOrder();
            RenderHelper::addSuccess(L::t("Created order in PrestaShop"));
        } catch (Exception $ex) {
            RenderHelper::addError($ex->getMessage());
        }
        UrlHelper::redirect("order/view", array('id' => $orderId));
    }

    /**
     * Change status for assigned eBay and PS order
     * accept two action: paid, shipped
     */
    public function changeStatusAction()
    {
        $orderId = UrlHelper::getGet('id', null);
        if (is_null($orderId)) {
            RenderHelper::addError(L::t("Invalid Order Id"));
            UrlHelper::redirect("order/index");
            return;
        }
        $statusKey = UrlHelper::getGet('status', null);
        if (!in_array($statusKey, array('paid', 'shipped'))) {
            RenderHelper::addError(L::t("Invalid Order new Status"));
            UrlHelper::redirect("order/view", array('id' => $orderId));
            return;
        }
        $newStatusIdKey = 0;
        if ($statusKey == 'paid') {
            $newStatusIdKey = _PS_OS_PAYMENT_;
        } else if ($statusKey == 'shipped') {
            $newStatusIdKey = _PS_OS_SHIPPING_;
        }
        $orderModel = new Order_OrderModel($orderId);
        $result = $orderModel->updateEbayOrderStatusByPrestaBayId($newStatusIdKey);
        if ($result) {
            RenderHelper::addSuccess(L::t('eBay Order Status has been updated'));
        } else {
            RenderHelper::addError(L::t('eBay Order Status not updated. Details available on log.'));
        }
        UrlHelper::redirect("order/view", array('id' => $orderId));
    }

    /**
     * Request modal window to change order address
     */
    public function changeAddressModalAction()
    {
        $orderId = UrlHelper::getGet('orderId', false);
        $orderModel = new Order_OrderModel($orderId);

        $this->view('order/changeAddressModal.phtml', array(
            'order' => $orderModel
        ));
    }

    /**
     * Save updated order address ajax
     */
    public function changeAddressAjaxAction()
    {
        RenderHelper::cleanOutput();
        $orderId = UrlHelper::getPost('id', false);
        $address = UrlHelper::getPost('address', array());

        if (empty($orderId) || empty($address)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Invalid Request'
            ));

            return false;
        }

        $orderModel = new Order_OrderModel($orderId);
        if (!$orderModel->id) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Invalid Request'
            ));

            return false;
        }

        $orderModel->setBuyerAddress($address);
        $orderModel->save();

        echo json_encode(array(
            'success' => true
        ));
        return true;



    }

    /**
     * Show grid with all orders log
     */
    public function logAction()
    {
        $grid = new Grids_FullOrdersLog();
        $grid->getHtml();
    }
}

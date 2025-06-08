<?php
/**
 * File ProfilesHelper.php
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


class OrderViewHelper
{
    public static function getOrderTotalStatus($checkoutStatus, $paymentStatus, $shippingStatus)
    {

        if ($checkoutStatus == Order_OrderModel::STATUS_CHECKOUT_INCOMPLETE) {
            return "<font color='red'>".L::t("Incomplete")."</font>";
        }
        if ($paymentStatus == Order_OrderModel::STATUS_PAYMENT_PENDING || $paymentStatus == Order_OrderModel::STATUS_PAYMENT_NONE) {
            return L::t("Awaiting Payment");
        }
        if ($paymentStatus == Order_OrderModel::STATUS_PAYMENT_FAIL) {
            return "<font color='red'>".L::t("Payment Problem")."</font>";
        }
        if ($shippingStatus == Order_OrderModel::STATUS_SHIPPING_NONE || $shippingStatus == Order_OrderModel::STATUS_SHIPPING_PENDING) {
            return L::t("Awaiting Shipment");
        }

        if ($shippingStatus == Order_OrderModel::STATUS_SHIPPING_FAIL) {
            return "<font color='red'>".L::t("Shipping Problem")."</font>";
        }

        if ($paymentStatus == Order_OrderModel::STATUS_PAYMENT_COMPLETE && $shippingStatus == Order_OrderModel::STATUS_SHIPPING_COMPLETE) {
            return "<font color='green'>".L::t("Paid and Shipped")."</font>";
        }
    }
}
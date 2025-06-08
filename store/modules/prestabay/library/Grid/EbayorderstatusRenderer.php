<?php
/**
 * File EbayOrderStatusRenderer.php
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

class Grid_EbayorderstatusRenderer extends Grid_AbstractRenderer
{

    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {
        $checkoutStatus = isset($row['status_checkout']) ? $row['status_checkout'] : Order_OrderModel::STATUS_CHECKOUT_INCOMPLETE;
        $paymentStatus = isset($row['status_payment']) ? $row['status_payment'] : Order_OrderModel::STATUS_PAYMENT_NONE;
        $shippingStatus = isset($row['status_shipping']) ? $row['status_shipping'] : Order_OrderModel::STATUS_SHIPPING_NONE;
        return OrderViewHelper::getOrderTotalStatus($checkoutStatus, $paymentStatus, $shippingStatus);
    }

    public function filter($fieldKey = null, $value = null, $config = array(), $grid = null)
    {
        return '';
    }

}
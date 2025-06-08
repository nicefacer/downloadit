<?php
/**
 * File EbayorderitemsRenderer.php
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

class Grid_EbayorderitemsRenderer extends Grid_AbstractRenderer
{

    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {
        $orderItemsModel = new Order_OrderItemsModel();
        $orderItems = $orderItemsModel->getOrderItems($row['id']);
        echo "<div class='order-items'>";
        foreach ($orderItems as $orderItem) {
            echo "<b>".$orderItem['title']."</b> x ".$orderItem['qty']."<br/>";
        }
        echo "</div>";
        if (!empty($row['sales_record_number'])) {
            echo '<p><i><small>Sales Record Number: '.$row['sales_record_number'].'</small></i></p>';
        }
    }

    public function filter($fieldKey = null, $value = null, $config = array(), $grid = null) {
        return '';
    }

}
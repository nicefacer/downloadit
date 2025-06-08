<?php

/**
 * File SynchdataRenderer.php
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
class Grid_SynchdataRenderer extends Grid_AbstractRenderer
{

    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {

        $renderedOutput = "";
        if ($row['selling_product_id'] > 0) {
            $sellingProduct = new Selling_ProductsModel($row['selling_product_id']);
            if ($sellingProduct->id > 0) {
                $sellingProductUrl = UrlHelper::getUrl("selling/edit", array(
                            'id' => $sellingProduct->selling_id))."&filter[id]=".$sellingProduct->id;
                $renderedOutput .= "<a class='desoration-underline' href='" .
                            $sellingProductUrl . "'>" .
                            L::t("Selling Product").": ".$sellingProduct->ebay_name. "</a>";
                $renderedOutput .= "<br/>";
            }
        }
        if ($row['ps_product_id'] > 0) {
            $product = new Product($row['ps_product_id']);
            if ($product->id) {
                $productName = reset($product->name);
                $renderedOutput .= "<a class='desoration-underline' href='" .
                            UrlHelper::getPrestaUrl("AdminProducts", array(
                                'id_product' => $product->id,
                                'updateproduct' => null)) . "'>" .
                            L::t("Product").": ".$productName . "</a>";
                $renderedOutput .= "<br/>";
            }
        }
        if ($row['pb_order_id'] > 0) {
            $order = new Order_OrderModel($row['pb_order_id']);
            if ($order->id) {
                $renderedOutput .= "<a class='desoration-underline' href='" .
                        UrlHelper::getUrl("order/view", array(
                            'id' => $row['pb_order_id'])) . "'>" .
                        L::t("PrestaBay Order") . "</a>";
                if ($order->presta_order_id > 0) {
                    $renderedOutput .= "<br/><a class='desoration-underline' href='" .
                            UrlHelper::getPrestaUrl("AdminOrders", array(
                                'id_order' => $order->presta_order_id,
                                'vieworder' => null)) . "'>" .
                            L::t("PrestaShop Order") . "</a>";
                }

                $renderedOutput .= "<br/>";
            }
        }
        if ($row['ebay_item_id'] > 0) {
            $renderedOutput .= L::t("Item") . " #" . $row['ebay_item_id'];
            $renderedOutput .= "<br/>";
        }
        if ($row['ebay_account_id'] > 0) {
            $account = new AccountsModel($row['ebay_account_id']);
            if ($account->id > 0) {
                $renderedOutput .= "<a class='desoration-underline' href='" . UrlHelper::getUrl('accounts/edit', array('id' => $row['ebay_account_id'])) . "'>{$account->name}</a>";
            } else {
                $renderedOutput .= L::t("Account deleted");
            }
            $renderedOutput .= "<br/>";
        }
        return $renderedOutput;
    }

    public function filter($fieldKey = null, $value = null, $config = array(), $grid = null)
    {
        return '-';
    }

}


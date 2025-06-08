<?php
/**
 * File PrestaShopHelper.php
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
class PrestaShopHelper
{
    /**
     * @param $productId
     * @param $attributeId
     * @param $qtyChange
     */
    public static function changeQTY($productId, $attributeId, $qtyChange)
    {
        // Change QTY without variation
        $idAttribute      = ($attributeId > 0) ? (int)$attributeId : false;
        $idAttributePSNew = $idAttribute;
        $idAttribute == false && $idAttributePSNew = 0;
        if (CoreHelper::isPS13()) {
            // for the branch 1.3.x
            Product::updateQuantity(
                array(
                    'id_product'           => $productId,
                    'cart_quantity'        => (int)$qtyChange,
                    'id_product_attribute' => $idAttribute,
                    'out_of_stock'         => false
                )
            );
        } elseif (CoreHelper::isPS15()) {
            if ($idAttributePSNew > 0) {
                // Check that product have specific attribute that we want to update,
                // if attribute not exist we skip qty update for it
//                $sql = "SELECT * FROM `ps_product_attribute`
//							WHERE `id_product` = '{$productId}' AND id_product_attribute = '{$idAttributePSNew}'";
//                $queryResult = Db::getInstance()->ExecuteS($sql, true, false);
//                if (empty($queryResult)) {
//                    // Attribute not exist
//                    return false;
//                }
            }
            StockAvailable::updateQuantity($productId, $idAttributePSNew, -(int)$qtyChange);
        } else {
            $productModel = new Product($productId);
            $productModel->addStockMvt(-(int)$qtyChange, (int)_STOCK_MOVEMENT_ORDER_REASON_, $idAttributePSNew);
        }

    }
}
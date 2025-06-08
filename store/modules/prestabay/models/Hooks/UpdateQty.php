<?php

/**
 * File UpdateQty.php
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
class Hooks_UpdateQty
{

    public function execute($productId, $productQty, $productIdAttribute = false, $attributeQty = false)
    {

        $sellingProducts = new Selling_ProductsModel();
        $sellingProductsList = $sellingProducts->getSellingProductsByProductId($productId, $productIdAttribute);

        if (count($sellingProductsList) > 0) {
            foreach ($sellingProductsList as $sellingProductInfo) {
                if (empty($sellingProductInfo['product_name'])) {
                    continue;
                }
                $qtyChange = $productQty - $sellingProductInfo['product_qty'];
                if ((int)$sellingProductInfo['product_id_attribute'] > 0 && $attributeQty) {
                    $qtyChange = $attributeQty - $sellingProductInfo['product_qty'];
                } else {
                    if ((int)$sellingProductInfo['product_id_attribute'] > 0 && !$attributeQty) {
                        // For attribute listings that not attribute qty in change we ignore
                        continue;
                    }
                }
                if ($qtyChange != 0) {
                    // Product QTY was changed
                    $sellingProductInfo['product_qty'] += $qtyChange;
                    $sellingProductInfo['product_qty_change'] += $qtyChange;
                    $sellingProducts->setData($sellingProductInfo)->save();
                }

            }
        }
        if (!$productIdAttribute) {
            return false;
        }
        // Check same but for variation products
        $sellingProductsList = $sellingProducts->getSellingProductsByProductId($productId, 0);
        if (count($sellingProductsList) > 0) {
            foreach ($sellingProductsList as $sellingProductInfo) {
                $qtyChange = $productQty - $sellingProductInfo['product_qty'];
                if ($productIdAttribute && $attributeQty !== false &&
                    $sellingProducts->isVariationListing($sellingProductInfo['id'])
                ) {
                    // Check that product multi-variation
                    // If yes -> update related items
                    $this->_updateVariationQty($sellingProductInfo['id'], $productIdAttribute, $attributeQty);
                }
                if ($qtyChange != 0) {
                    // Product QTY was changed
                    $sellingProductInfo['product_qty'] += $qtyChange;
                    $sellingProductInfo['product_qty_change'] += $qtyChange;
                    $sellingProducts->setData($sellingProductInfo)->save();
                }
            }
        }


    }

    protected function _updateVariationQty($sellingProductId, $productIdAttribute, $attributeQty)
    {
        $sellingProductVariation = new Selling_VariationsModel();
        $variationInfo           = $sellingProductVariation->getVariationBySellingAndAttribute(
            $sellingProductId,
            $productIdAttribute
        );
        if (!is_array($variationInfo)) {
            // skip, no correct array
            return false;
        }
        $varQtyChange = $attributeQty - $variationInfo['qty'];
        if ($varQtyChange != 0) {
            $variationInfo['qty'] = $attributeQty;
            $variationInfo['qty_change'] += $varQtyChange;
            $sellingProductVariation->setData($variationInfo)->save();
        }

        return true;
    }

}
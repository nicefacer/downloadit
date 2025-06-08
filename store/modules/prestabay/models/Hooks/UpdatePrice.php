<?php

/**
 * File UpdatePrice.php
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
class Hooks_UpdatePrice
{

    public function execute($productId, $productPrice, $productIdAttribute = false, $attributePrice = false)
    {

        $sellingProducts = new Selling_ProductsModel();
        $sellingProductsList = $sellingProducts->getSellingProductsByProductId($productId, $productIdAttribute);

        if (!is_array($sellingProductsList) || $sellingProductsList == array()) {
            return false;
        }

        foreach ($sellingProductsList as $sellingProductInfo) {
            // --- When on selling profile we enable wholesale price we need to change
            // price value that we get from hook to wholesale price
            $sellingListModel = new Selling_ListModel($sellingProductInfo['selling_id']);
            if ((int)$sellingListModel->profile <= 0) {
                // Connected to not valid profile
                continue;
            }
            $sellingProfileModel  = new ProfilesModel($sellingListModel->profile);
            if ($sellingProfileModel->price_start == ProfilesModel::PRICE_MODE_WHOLESALE_PRICE) {
                $productModel = new Product($productId);
                $productPriceUsed = $productModel->wholesale_price;
            } else {
                $productPriceUsed = $productPrice;
            }

            $priceChange = 0;
            if ((int)$sellingProductInfo['product_id_attribute'] > 0 && $attributePrice) {
                $priceChange = $attributePrice - $sellingProductInfo['product_price'];
            } else if ((int)$sellingProductInfo['product_id_attribute'] > 0 && !$attributePrice) {
                // For attribute listings that not attribute price in change we ignore
                continue;
            } else if ($productIdAttribute && $attributePrice !== false && $sellingProducts->isVariationListing($sellingProductInfo['id'])) {
                // Check that product multi-variation
                // If yes -> update related items
                 $this->_updateVariationPrice($sellingProductInfo['id'], $productIdAttribute, $attributePrice);
                 continue;
            } else {
                // For non variation price change work as usual 
                $priceChange = $productPriceUsed - $sellingProductInfo['product_price'];
            }
            
            if (abs($priceChange) >= 0.01) {
                // Price for product has been changed for value more that 0.01
                $sellingProductInfo['product_price']+=$priceChange;
                $sellingProductInfo['product_price_change']+=$priceChange;
                $sellingProducts->setData($sellingProductInfo)->save();
            }
        }
    }

    public function _updateVariationPrice($sellingProductId, $productIdAttribute, $attributePrice)
    {
        $sellingProductVariation = new Selling_VariationsModel();
        $variationInfo = $sellingProductVariation->getVariationBySellingAndAttribute($sellingProductId, $productIdAttribute);
        if (!is_array($variationInfo)) {
            // skip, no correct array
            return false;
        }
        $varPriceChange = $attributePrice - $variationInfo['price'];
        if (abs($varPriceChange) >= 0.01) {
            $variationInfo['price']+=$varPriceChange;
            $variationInfo['price_change']+=$varPriceChange;
            $sellingProductVariation->setData($variationInfo)->save();
        }
        return true;
    }

}
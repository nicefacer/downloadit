<?php

/**
 * File UpdateCategory.php
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
class Hooks_UpdateCategory
{

    public function execute($productId, $productDefaultCategory, $productCategoryList = array())
    {
        if (Configuration::get("INVEBAY_AUTO_CATEGORY_ADD") != 1) {
            // Allow processing auto add/remove only when has enabled settings
            return;
        }

        $product = new ProductCore($productId);

        $sellingLogModel = new Log_SellingModel();

        // Part 1. Append product to new category
        // !!!!! Only main "Default Category" supported
        $productCategoryList = array($productDefaultCategory);

        $selingIdList = Selling_CategoriesModel::getSellingIdsMappedToCategories($productCategoryList);

        if ($selingIdList != false) {
            // Find some Selling that have mapping for specify category
            foreach ($selingIdList as $sellingId) {
                if (Selling_ProductsModel::isProductExistOnSelling($sellingId, $productId)) {
                    // Product already on selling list, skip it
                    continue;
                }
                // Product not in Selling List
                $sellingListModel = new Selling_ListModel($sellingId);
                $sellingProductId = $sellingListModel->appendProduct($productId);
                if ($sellingProductId === false) {
                    // Problem adding product to selling
                    // Write log message
                    $sellingLogModel->writeLogMessages($sellingId, 0, Log_SellingModel::LOG_ACTION_SEND,
                        Log_SellingModel::LOG_LEVEL_ERROR, array(L::t("Can't append product to Selling. DB Error. Product Id") . ": {$productId}"));
                    continue;
                }

                if ($sellingProductId === -1) {
                    $sellingLogModel->writeLogMessages($sellingId, 0, Log_SellingModel::LOG_ACTION_SEND,
                        Log_SellingModel::LOG_LEVEL_ERROR, array(L::t("Can't append product to Selling. Already exist") . ": {$productId}"));
                    continue;
                }

                if ($product->active) {
                    // Only active product can be send to eBay

                    // Try to send this product to eBay
                    // Result of this call automaticly writed to log
                    EbayListHelper::sendList($sellingId, $sellingProductId);
                }
            }
        }

        // Part 2. Check for witch category was product connected
        // Working only for category mapping mode
        $producToMappedCategories = Selling_ListModel::getSellingProductMappedToAnotherCategoryAndTargetProduct($productCategoryList, $productId);
        if ($producToMappedCategories != false) {
            // Found some selling list that required to stop product
            foreach ($producToMappedCategories as $sellingId) {
                if ($sellingId['status'] == Selling_ProductsModel::STATUS_ACTIVE) {
                    EbayListHelper::stopList($sellingId['selling_id'], $sellingId['id']);
                }
                $sellingLogModel->addSuccessLog($sellingId['selling_id'], $sellingId['id'], Log_SellingModel::LOG_ACTION_SEND, L::t("Product removed from Selling List due to change category. Product Id") . ": " . $sellingId['product_id']);

                Selling_ProductsModel::deleteSellingProductById($sellingId['id']);
            }
        }
    }

}
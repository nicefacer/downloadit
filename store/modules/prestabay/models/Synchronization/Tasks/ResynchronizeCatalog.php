<?php

/*
 * File ResynchronizeCatalog.php
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

class Synchronization_Tasks_ResynchronizeCatalog extends Synchronization_BaseTask
{

    protected $_syncType = Log_SyncModel::LOG_TASK_RESYNCHRONIZE_CATALOG;

    protected function _execute()
    {
        list($totalStops, $totalAdds) = $this->_resynchronizeCategoryProducts();

        if ($totalStops + $totalAdds > 0) {
            $this->_appendSucces(sprintf(L::t('%s listings have been stoped, %s listings has been added related to catalogs products changes in PS'),
                    $totalStops, $totalAdds));
        }
    }

    protected function _resynchronizeCategoryProducts()
    {
        // Get list of selling list with category mode
        $sellingIds = Selling_ListModel::getSellingIdWithCategoryMode();

        if (empty($sellingIds)) {
            // no category mode selling list
            return;
        }
        $sellingProductsModel = new Selling_ProductsModel();
        $totalStops = 0;
        $totalAdds = 0;
        foreach ($sellingIds as $singleSellingId) {
            // Get categories for witch assigned currect Selling List
            $mappedCategoriesList = Selling_CategoriesModel::getCategoriesMapped($singleSellingId);
            if (empty($mappedCategoriesList)) {
                continue;
            }
            // Get PS Products ids assigned to selling list
            $productsIds = $sellingProductsModel->getSellingProductsIdsBySellingId($singleSellingId);
            // Get all PS Products that put into categories
            $prestaShopProductsIds = Selling_CategoriesModel::getProductsForCategories($mappedCategoriesList);

            if (!is_array($productsIds) || !is_array($prestaShopProductsIds)) {
                continue;
            }
            // Compare products that we have on Selling List and Products that should be there
            $productsIdsToStop = array_diff($productsIds, $prestaShopProductsIds);

            if (!empty($productsIdsToStop)) {
                $totalStops += count($productsIdsToStop);
                $this->_stopAndRemoveItemsFromSelling($singleSellingId, $productsIdsToStop);
            }
            $productsIdsToAdd = array_diff($prestaShopProductsIds, $productsIds);

            if (!empty($productsIdsToAdd)) {
                $totalAdds += count($productsIdsToAdd);
                $this->_sendItemToEBay($singleSellingId, $productsIdsToAdd);
            }
        }
        
        return array($totalStops, $totalAdds);
    }

    protected function _stopAndRemoveItemsFromSelling($sellingId, $productsIdsToStop)
    {
        $sellingLogModel = new Log_SellingModel();
        $sellingProductsModel = new Selling_ProductsModel();

        // Found some selling list that required to stop product

        $sellingItemsToStop = $sellingProductsModel->getSellingProductsByProductIdsSellingId($productsIdsToStop, $sellingId);
        if (!is_array($sellingItemsToStop)) {
            return;
        }
        foreach ($sellingItemsToStop as $itemToStop) {
            if ($itemToStop['status'] == Selling_ProductsModel::STATUS_ACTIVE) {
                EbayListHelper::stopList($itemToStop['selling_id'], $itemToStop['id']);
            }
            $sellingLogModel->addSuccessLog($itemToStop['selling_id'], $itemToStop['id'], Log_SellingModel::LOG_ACTION_SEND, L::t("Product removed from Selling List due to change category. Product Id") . ": " . $itemToStop['product_id']);

            Selling_ProductsModel::deleteSellingProductById($itemToStop['id']);
        }
    }

    protected function _sendItemToEBay($sellingId, $productsIdsToAdd)
    {
        $sellingLogModel = new Log_SellingModel();

        // Product not in Selling List
        $sellingListModel = new Selling_ListModel($sellingId);
        foreach ($productsIdsToAdd as $prestaProductId) {
            $sellingProductId = $sellingListModel->appendProduct($prestaProductId);
            if ($sellingProductId === false) {
                // Problem adding product to selling
                // Write log message
                $sellingLogModel->writeLogMessages($sellingId, 0, Log_SellingModel::LOG_ACTION_SEND, Log_SellingModel::LOG_LEVEL_ERROR, array(L::t("Can't append product to Selling. DB Error. Product Id") . ": {$prestaProductId}"));
                continue;
            }
            if ($sellingProductId === -1) {
                $sellingLogModel->writeLogMessages($sellingId, 0, Log_SellingModel::LOG_ACTION_SEND,
                    Log_SellingModel::LOG_LEVEL_ERROR, array(L::t("Can't append product to Selling. Already exist Product Id") . ": {$prestaProductId}"));
                continue;
            }

            $productModel = new Product($prestaProductId);
            if ($productModel->active) {
                // ONLY ACTIVE PRODUCT SHOULD BE SEND
                // Try to send this product to eBay
                // Result of this call automaticly writed to log
                EbayListHelper::sendList($sellingId, $sellingProductId);
            }
        }
    }

}
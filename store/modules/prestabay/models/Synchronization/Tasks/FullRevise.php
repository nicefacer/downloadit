<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 *  It is available through the world-wide-web at this URL:
 *  http://involic.com/license.txt
 *  If you are unable to obtain it through the world-wide-web,
 *  please send an email to license@involic.com so
 *  we can send you a copy immediately.
 *
 *  PrestaBay - eBay Integration with PrestaShop e-commerce platform.
 *  Adding possibilty list PrestaShop Product dirrectly to eBay.
 *
 *  @author      Involic <contacts@involic.com>
 *  @copyright   Copyright (c) 2011- 2016 by Involic (http://www.involic.com)
 *  @license     http://involic.com/license.txt
 */

class Synchronization_Tasks_FullRevise extends Synchronization_BaseTask
{

    protected $_syncType = Log_SyncModel::LOG_TASK_FULL_REVISE;

    protected function _execute()
    {
        // Get all products marked for full_revise
        // go throw each of them
        // Revise it

        $sellingProduct = new Selling_ProductsModel();
        $productsForFullRevise = $sellingProduct->getProductsForFullRevise();
        $totalFullRevised = 0;
        foreach ($productsForFullRevise as $productToRevise) {
            // Before do full revise check that item is not yet full revised
            $sellingProductModel = new Selling_ProductsModel($productToRevise['id']);
            if ($sellingProductModel->status != Selling_ProductsModel::STATUS_ACTIVE || $sellingProductModel->full_revise == 0) {
                continue;
            }
            $result = EbayListHelper::reviseList($productToRevise['selling_id'], $productToRevise['id']);

            if ($result['warnings'] != "") {
                $this->_hasWarnings = true;
                $this->_appendWarning($result['warnings'], array(
                    'selling_product_id' => $productToRevise['id'],
                    'ps_product_id' => $productToRevise['product_id'],
                    'ebay_item_id' => $productToRevise['ebay_id']
                ));
            }
            if ($result['errors'] != "") {
                $this->_hasErrors = true;
                $this->_appendError($result['errors'], array(
                    'selling_product_id' => $productToRevise['id'],
                    'ps_product_id' => $productToRevise['product_id'],
                ));
            } else {
                $totalFullRevised++;
            }
        }

        if ($totalFullRevised > 0) {
            $this->_appendSucces(sprintf(L::t("%s eBay Listings has been full revised"), $totalFullRevised));
        }
    }

}
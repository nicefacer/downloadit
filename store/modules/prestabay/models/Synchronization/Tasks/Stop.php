<?php
/*
 * File Stop.php
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

class Synchronization_Tasks_Stop extends Synchronization_BaseTask
{

    protected $_syncType = Log_SyncModel::LOG_TASK_END;

    protected function _execute()
    {
        // Auto stop eBay Items that becomed out of stock in PrestaShop
        // Get Products that finish and have product_qty > 0
        $sellingProduct = new Selling_ProductsModel();
        $sellingProductsBecomedOutOfStock = $sellingProduct->getActiveOutOfStockProducts();
        $totalStopped = 0;
        foreach ($sellingProductsBecomedOutOfStock as $productBecomedOutOfStock) {
            $this->_hasChanges = true;
            // option true => GTC listings + Out Of Stock control prevent items from stopping
            $result = EbayListHelper::stopList($productBecomedOutOfStock['selling_id'], $productBecomedOutOfStock['id'], true);

            if (isset($result['skipped']) && $result['skipped']) {
                // If item is skipped, just continue
                continue;
            }

            if ($result['warnings'] != "") {
                $this->_hasWarnings = true;
                $this->_appendWarning($result['warnings'], array(
                    'selling_product_id' => $productBecomedOutOfStock['id'],
                    'ps_product_id' => $productBecomedOutOfStock['product_id'],
                    'ebay_item_id' => $productBecomedOutOfStock['ebay_id']
                ));
            }
            if ($result['errors'] != "") {
                $this->_hasErrors = true;
                $this->_appendError($result['errors'], array(
                    'selling_product_id' => $productBecomedOutOfStock['id'],
                    'ps_product_id' => $productBecomedOutOfStock['product_id'],
                    'ebay_item_id' => $productBecomedOutOfStock['ebay_id']
                ));
            } else {
                $totalStopped++;
            }
        }

        if ($totalStopped > 0) {
            $this->_appendSucces(sprintf(L::t("%s eBay Listings has been stopped"), $totalStopped));
        }
    }

}
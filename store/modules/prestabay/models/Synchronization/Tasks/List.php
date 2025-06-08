<?php
/*
 * File List.php
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

class Synchronization_Tasks_List extends Synchronization_BaseTask
{

    protected $_syncType = Log_SyncModel::LOG_TASK_LIST;

    protected function _execute()
    {
        // Get listings that have possitive QTY and have status = Not Active
        // List this products to ebay
        $sellingProduct = new Selling_ProductsModel();
        $sellingProductsNotActiveInStock = $sellingProduct->getNotActiveInStockProducts();

        $totalListed = 0;
        foreach ($sellingProductsNotActiveInStock as $productToList) {
            $this->_hasChanges = true;
            $result = EbayListHelper::sendList($productToList['selling_id'], $productToList['id']);

            if ($result['warnings'] != "") {
                $this->_hasWarnings = true;
                $this->_appendWarning($result['warnings'], array(
                    'selling_product_id' => $productToList['id'],
                    'ps_product_id' => $productToList['product_id'],
                    'ebay_item_id' => $productToList['ebay_id']
                ));
            }
            if ($result['errors'] != "") {
                $this->_hasErrors = true;
                $this->_appendError($result['errors'], array(
                    'selling_product_id' => $productToList['id'],
                    'ps_product_id' => $productToList['product_id'],
                ));
            } else {
                $totalListed++;
            }
        }

        if ($totalListed > 0) {
            $this->_appendSucces(sprintf(L::t("%s eBay Listings has been listed"), $totalListed));
        }
    }

}
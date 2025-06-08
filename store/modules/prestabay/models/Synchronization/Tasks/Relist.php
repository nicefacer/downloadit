<?php

/*
 * File Relist.php
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

class Synchronization_Tasks_Relist extends Synchronization_BaseTask
{

    protected $_syncType = Log_SyncModel::LOG_TASK_RELIST;

    protected function _execute()
    {
        // Get Products that finish and have product_qty > 0
        $sellingProduct = new Selling_ProductsModel();
        $sellingProductsBecomedInStock = $sellingProduct->getFinishInStockProducts();
        $totalRelistedItems = 0;
        foreach ($sellingProductsBecomedInStock as $productBecomedInStock) {
            $this->_hasChanges = true;
            $result = EbayListHelper::relistList($productBecomedInStock['selling_id'], $productBecomedInStock['id']);
            $result2 = array(
                'warnings' => '',
                'errors' => ''
            );
            if (!$result['success']) {
                // If relist fail, set status to manual stoped to avoid future relist try
                $productBecomedInStock['status'] = Selling_ProductsModel::STATUS_STOPED;
                $sellingProduct->setData($productBecomedInStock);
                $sellingProduct->save();
            } else {
                // Automaticly revise product after relist. Need to handle correct QTY, Price and others
                $result2 = EbayListHelper::reviseList($productBecomedInStock['selling_id'], $productBecomedInStock['id'], EbayListHelper::MODE_FULL);
            }
            if ($result['warnings'] != "" || $result2['warnings'] != "") {
                $this->_hasWarnings = true;
                if ($result['warnings'] != "") {
                    $this->_appendWarning($result['warnings'], array(
                        'selling_product_id' => $productBecomedInStock['id'],
                        'ps_product_id' => $productBecomedInStock['product_id'],
                        'ebay_item_id' => $productBecomedInStock['ebay_id']));
                }
                if ($result2['warnings'] != "") {
                    $this->_appendWarning($result2['warnings'], array(
                        'selling_product_id' => $productBecomedInStock['id'],
                        'ps_product_id' => $productBecomedInStock['product_id'],
                        'ebay_item_id' => $productBecomedInStock['ebay_id']));
                }
            }
            if ($result['errors'] != "" || $result2['errors'] != "") {
                $this->_hasErrors = true;
                if ($result['errors'] != "") {
                    $this->_appendError($result['errors'], array(
                        'selling_product_id' => $productBecomedInStock['id'],
                        'ps_product_id' => $productBecomedInStock['product_id'],
                        'ebay_item_id' => $productBecomedInStock['ebay_id']));
                }
                if ($result2['errors'] != "") {
                    $this->_appendError($result2['errors'], array(
                        'selling_product_id' => $productBecomedInStock['id'],
                        'ps_product_id' => $productBecomedInStock['product_id'],
                        'ebay_item_id' => $productBecomedInStock['ebay_id']));
                }
            } else {
                $totalRelistedItems++;
            }
        }
        
        if ($totalRelistedItems > 0) {
            $this->_appendSucces(sprintf(L::t("%s listings has been relisted"), $totalRelistedItems));
        }
    }

}
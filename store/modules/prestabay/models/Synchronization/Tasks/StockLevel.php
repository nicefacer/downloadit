<?php

/*
 * File StockLevel.php
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

class Synchronization_Tasks_StockLevel extends Synchronization_BaseTask
{

    protected $_syncType = Log_SyncModel::LOG_TASK_STOCK_LEVEL;

    protected function _execute()
    {
        $sellingLogModel = new Log_SellingModel();

        $isOOSC = Configuration::get('INVEBAY_SYNC_TASK_OOSC');
        // 1) Get list of selling products that have ebay_sold_qty_sync more that 0
        // 2) Load related PrestaBay product, change QTY, save product
        // 3) Connected selling product ebay_sold_qty_sync set to 0 and save.
        $sellingProduct = new Selling_ProductsModel();
        $eBayQtyChangeProducts = $sellingProduct->getEbayChangedProducts();
        $totalPSQtyChange = 0;
        $totalEbayQtyChange = 0;
        if (!is_array($eBayQtyChangeProducts)) {
            $eBayQtyChangeProducts = array();
        }
        foreach ($eBayQtyChangeProducts as $eBayQtyChange) {
            // Important when we update product stock, hook on update QTY not called
            $productModel = new Product($eBayQtyChange['product_id']);
            if (!$productModel->id) {
                // No connected product
                continue;
            }

            $this->_hasChanges = true;

            if ($sellingProduct->isVariationListing($eBayQtyChange['id'])) {
                // Variation product work in different way
                $this->_changeStockForVariation($eBayQtyChange['id'], $eBayQtyChange['product_id']);
            } else {
                // Working only on disabled order synchronization or disabled import order to PrestaShop
                if ($this->_needChangeQTY()) {
                    // Change QTY without variation
                    PrestaShopHelper::changeQTY($eBayQtyChange['product_id'], $eBayQtyChange['product_id_attribute'], $eBayQtyChange['ebay_sold_qty_sync']);
                    // Indicate that we change qty
                    $sellingLogModel->addSuccessLog($eBayQtyChange['selling_id'], $eBayQtyChange['id'], Log_SellingModel::LOG_ACTION_REVISE, "PrestaShop Product Stock Movement by " . (-(int) $eBayQtyChange['ebay_sold_qty_sync']));
                    $eBayQtyChange['product_qty'] -= (int) $eBayQtyChange['ebay_sold_qty_sync'];
                }
            }

            $eBayQtyChange['ebay_sold_qty_sync'] = 0;
            $sellingProduct->setData($eBayQtyChange);
            $sellingProduct->save();
            $totalPSQtyChange++;
        }

        // 4) Get list of selling products that has product_qty_change!=0
        // 5) For this product set product_qty_change = 0. Save.
        // 6) Perform revise for connected selling product
        $sellingProductChangedInPresta = $sellingProduct->getPrestashopChangedProducts();
        if (!is_array($sellingProductChangedInPresta)) {
            $sellingProductChangedInPresta = array();
        }
        foreach ($sellingProductChangedInPresta as $singleSellingProduct) {
            // First remove product qty change
            $singleSellingProduct['product_qty_change'] = 0;
            $sellingProduct->setData($singleSellingProduct);
            $sellingProduct->save();
            if ($sellingProduct->isVariationListing($singleSellingProduct['id'])) {
                $this->_resetProductQTYChangeForVariation($singleSellingProduct['id']);
            }
            if ($singleSellingProduct['product_qty'] > 0 || $isOOSC) {
                $this->_hasChanges = true;
                // Do revise when qty > 0 or OOSC enabled
                $result = EbayListHelper::reviseList($singleSellingProduct['selling_id'], $singleSellingProduct['id'], EbayListHelper::MODE_QTY);
                if ($result['warnings'] != "") {
                    $this->_hasWarnings = true;
                    $this->_appendWarning($result['warnings'], array(
                        'selling_product_id' => $singleSellingProduct['id'],
                        'ps_product_id' => $singleSellingProduct['product_id'],
                        'ebay_item_id' => $singleSellingProduct['ebay_id']
                    ));
                }
                if ($result['errors'] != "") {
                    $this->_hasErrors = true;
                    $this->_appendError($result['errors'], array(
                        'selling_product_id' => $singleSellingProduct['id'],
                        'ps_product_id' => $singleSellingProduct['product_id'],
                        'ebay_item_id' => $singleSellingProduct['ebay_id']
                    ));
                } else {
                    $totalEbayQtyChange++;
                }
            }
        }
        // Go throw all Variation product that has qty change.
        // Possible that change variation qty, but total product qty not changed
        // @todo test
        $sellingVariation = new Selling_VariationsModel();
        $variationChangedProdudcts = $sellingVariation->getPrestashopChangedProductsAttributes();
        if (!is_array($variationChangedProdudcts)) {
            $variationChangedProdudcts = array();
        }
        foreach ($variationChangedProdudcts as $singleVariationChangedProduct) {
            $this->_resetProductQTYChangeForVariation($singleVariationChangedProduct['selling_product_id']);
            $this->_hasChanges = true;
            // There no reason to update qty when it's less zero.
            $result = EbayListHelper::reviseList($singleVariationChangedProduct['selling_id'], $singleVariationChangedProduct['selling_product_id'], EbayListHelper::MODE_QTY);
            if ($result['warnings'] != "") {
                $this->_hasWarnings = true;
                $this->_appendWarning($result['warnings'], array(
                    'selling_product_id' => $singleVariationChangedProduct['selling_product_id'],
                    'ps_product_id' => $singleVariationChangedProduct['product_id'],
                    'ebay_item_id' => $singleVariationChangedProduct['ebay_id']
                ));
            }
            if ($result['errors'] != "") {
                $this->_hasErrors = true;
                $this->_appendError($result['errors'], array(
                    'selling_product_id' => $singleVariationChangedProduct['selling_product_id'],
                    'ps_product_id' => $singleVariationChangedProduct['product_id'],
                    'ebay_item_id' => $singleVariationChangedProduct['ebay_id']
                ));
            } else {
                $totalEbayQtyChange++;
            }
        }

        if ($totalPSQtyChange > 0 || $totalEbayQtyChange > 0) {
            $this->_appendSucces(sprintf(L::t("%s PrestaShop Product QTY changed, %s eBay Item Revised"), $totalPSQtyChange, $totalEbayQtyChange));
        }
    }

    /**
     * Update QTY from eBay to Combination Product
     * @param int $sellingProductId PrestaBay product id
     * @param int $productId PrestaShop product id
     */
    protected function _changeStockForVariation($sellingProductId, $productId)
    {
        $sellingVariation = new Selling_VariationsModel();
        $variationChangedQty = $sellingVariation->getEbayChangedProducts($sellingProductId);

        foreach ($variationChangedQty as $singleVariationChange) {
            // Change QTY for variation
            if ($this->_needChangeQTY()) {
                PrestaShopHelper::changeQTY($productId, $singleVariationChange['product_id_attribute'], $singleVariationChange['ebay_sold_qty_sync']);
            }

            // Decrease qty of product that mapped to our DB
            $singleVariationChange['qty'] -= (int) $singleVariationChange['ebay_sold_qty_sync'];

            $singleVariationChange['ebay_sold_qty_sync'] = 0;
            $sellingVariation->setData($singleVariationChange);
            $sellingVariation->save();
        }
    }

    protected function _resetProductQTYChangeForVariation($sellingProductId)
    {
        $sellingVariation = new Selling_VariationsModel();
        return $sellingVariation->resetProductQTYChange($sellingProductId);
    }


    protected function _needChangeQTY()
    {
        if (Configuration::get('INVEBAY_SYNC_TASK_ORDER') == 0 || Configuration::get("INVEBAY_SYNCH_ORDER_IMPORT") == 0) {
            return true;
        }

        // Order import enabled
        if (Configuration::get("INVEBAY_SYNCH_ORDER_OK_PAYMENT") != 1 && Configuration::get("INVEBAY_ORDER_QTY_SIMULATION") == 1) {
            // For this combination always decrease qty
            // Order should be imported after complete payment and enabled qty simulation
            return true;
        }

        return false;
    }
}
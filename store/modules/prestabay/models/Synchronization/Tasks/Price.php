<?php

/*
 * File Price.php
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

class Synchronization_Tasks_Price extends Synchronization_BaseTask
{

    protected $_syncType = Log_SyncModel::LOG_TASK_PRICE;

    protected function _execute()
    {
        // There only one type of task. On change price into PrestaShop => revise item

        $sellingProduct = new Selling_ProductsModel();

        $eBayPriceChange = 0;

        // 1) Get list of selling products that has product_price_change!=0
        // 2) For this product set product_price_change = 0. Save.
        // 3) Perform revise for connected selling product
        $sellingProductPriceChangeInPresta = $sellingProduct->getPrestashopPriceChangedProducts();
        if (!is_array($sellingProductPriceChangeInPresta)) {
            $sellingProductPriceChangeInPresta = array();
        }
        foreach ($sellingProductPriceChangeInPresta as $singleSellingProduct) {
            // First remove product price change
            $singleSellingProduct['product_price_change'] = 0;
            $singleSellingProduct['product_qty_change'] = 0; // also reset qty change if it available
            $sellingProduct->setData($singleSellingProduct);
            $sellingProduct->save();
            if ($sellingProduct->isVariationListing($singleSellingProduct['id'])) {
                $this->_resetProductPriceQTYChangeForVariation($singleSellingProduct['id']);
            }
            if ($singleSellingProduct['product_price'] > 0) {
                $this->_hasChanges = true;
                $result = EbayListHelper::reviseList($singleSellingProduct['selling_id'], $singleSellingProduct['id'], EbayListHelper::MODE_QTY_PRICE);

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
                    $eBayPriceChange++;
                }
            } else {
                // Final product price less that 0. Not possible to revise product
                // with such price
                $this->_hasErrors = true;
            }
        }
        // Go throw all Variation product that has price change.
        // Very common situation when change only one of attribute without change
        // main product price

        $sellingVariation = new Selling_VariationsModel();
        $variationPriceChangedProdudcts = $sellingVariation->getPrestashopPriceChangedProductsAttributes();
        if (!is_array($variationPriceChangedProdudcts)) {
            $variationPriceChangedProdudcts = array();
        }
        foreach ($variationPriceChangedProdudcts as $singleVariationChangedProduct) {
            $this->_resetProductPriceQTYChangeForVariation($singleVariationChangedProduct['selling_product_id']);
            $this->_hasChanges = true;

            $result = EbayListHelper::reviseList($singleVariationChangedProduct['selling_id'], $singleVariationChangedProduct['selling_product_id'], EbayListHelper::MODE_QTY_PRICE);
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
                $eBayPriceChange++;
            }
        }
    }

    protected function _resetProductPriceQTYChangeForVariation($sellingProductId)
    {
        $sellingVariation = new Selling_VariationsModel();
        return $sellingVariation->resetProductPriceQTYChange($sellingProductId);
    }

}
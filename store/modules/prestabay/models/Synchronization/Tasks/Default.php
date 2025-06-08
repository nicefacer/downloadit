<?php

/**
 * File Default.php
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
class Synchronization_Tasks_Default extends Synchronization_BaseTask
{

    protected $_syncType = Log_SyncModel::LOG_TASK_DEFAULT;

    protected function _execute()
    {
        $accountsModel = new AccountsModel();
        $successTime = false;
        $lastSuccessTime = Configuration::get('INVEBAY_SYNC_SUCCESS_TIME');

        foreach ($accountsModel->getSelect()->getItems() as $account) {
            ApiModel::getInstance()->reset();
            $result = ApiModel::getInstance()->ebay->changes->items(array(
                        'token' => $account['token'],
                        'updateTime' => $lastSuccessTime
                    ))->post();

            if (count(ApiModel::getInstance()->getWarnings()) > 0) {
                $this->_appendWarning(ApiModel::getInstance()->getWarningsAsHtml(), array('ebay_account_id' => $account['id']));
            }

            if (count(ApiModel::getInstance()->getErrors()) > 0 || $result == false) {
                $this->_appendError(ApiModel::getInstance()->getErrorsAsHtml(), array('ebay_account_id' => $account['id']));
                continue;
            }
            
            $this->_processSingleAccountItemsChanges($result['events'], $account['id'], $account['mode'] );
            if (!$successTime) {
                $successTime = $result['time'];
            }
        }

        if ($successTime) {
            Configuration::updateValue('INVEBAY_SYNC_SUCCESS_TIME', $successTime);
        }      
    }

    protected function _processSingleAccountItemsChanges($itemsChanges, $accountId, $accountMode)
    {
        $sellingProductModel = new Selling_ProductsModel();

        foreach ($itemsChanges as $change) {
            $sellingProductInformation = $sellingProductModel->getSellingProductByEbayId($change['itemId']);
            if (!$sellingProductInformation) {
                // $this->processEbayListings($accountId, $accountMode, $change);
                continue;
            }
                     
            // Change Sold QTY value
            $qtySyncChange = $change['qtySold'] - $sellingProductInformation['ebay_sold_qty'];

            $sellingProductInformation['ebay_qty'] = $change['qty'];
            $sellingProductInformation['ebay_sold_qty']+=$qtySyncChange;

            // Number of qty that has been sold from last synchronization
            $sellingProductInformation['ebay_sold_qty_sync']+=$qtySyncChange;

            // Change product status
            if ($sellingProductInformation['status'] != Selling_ProductsModel::STATUS_STOPED) {
                // Update produce status only if current status not stopped.
                // Stopped status mean that product stop by hands and don't need future relist
                $sellingProductInformation['status'] = $change['status'];
            }

            // Synchronize variation status, if such node available on input
            if (isset($change['variations']) && $change['variations'] != array()) {
                $this->_variationDataSynchronization($sellingProductInformation['id'], $change['variations']);
            }

            // Update information to DB
            $sellingProductModel->setData($sellingProductInformation)->save();
            $this->_hasChanges = true;
        }
    }

    /**
     * Process updates for ebay listings
     *
     * @param $accountId
     * @param $accountMode
     * @param $change
     *
     */
    protected function processEbayListings($accountId, $accountMode, $change)
    {
        // Try to find that itemId on our eBay Listings
        $ebayListingsModel = EbayListingsModel::loadByItemId($change['itemId']);
        $isNew = false;
        if (!$ebayListingsModel) {
            // Not found - create new
            $isNew = true;
            $ebayListingsModel = new EbayListingsModel();
        }
        $ebayListingsModel->account_id = $accountId;
        $ebayListingsModel->buy_price = $change['currentPrice'];
        $ebayListingsModel->currency = $change['currency'];
        $ebayListingsModel->item_id = $change['itemId'];
        $ebayListingsModel->listing_duration =  $change['listingDuration']; ;
        $ebayListingsModel->listing_type = EbayHelper::getListingTypeCodeByName($change['listingType']);
        $isNew && $ebayListingsModel->picture_url = ''; // Not available on this call
        $ebayListingsModel->qty = $change['qty'] + $change['qtySold'];
        $ebayListingsModel->qty_available = $change['qty'];
        $isNew && $ebayListingsModel->sku = ''; // Not available in this call
        $ebayListingsModel->start_time = $change['startTime'];
        $ebayListingsModel->status = $change['status'];
        $ebayListingsModel->title = $change['itemTitle'];
        $ebayListingsModel->url = EbayHelper::getItemPath($change['itemId'], $accountMode, $change['site']);
        $ebayListingsModel->save();
    }

    /**
     * Synchronize eBay stock status for variation products
     *
     * @param int $sellingProductInformationId id of created selling product
     * @param array $eBayVariationData eBay variation data
     */
    protected function _variationDataSynchronization($sellingProductInformationId, $eBayVariationData)
    {
        $sellingProductVariation = new Selling_VariationsModel();
        $existingVarInfo = $sellingProductVariation->getVariationsList($sellingProductInformationId);
        $existingVarInfoDict = $existingVarInfo;

        foreach ($eBayVariationData as $changeVariations) {
            $existVarKey = VariationHelper::variationSearch($existingVarInfoDict, $changeVariations['options']);


            // Recalculate eBay qty and qty that has been synchronized
            $qtyVarSyncChange = $changeVariations['qtySold'] - $existingVarInfo[$existVarKey]['ebay_sold_qty'];
            $existingVarInfo[$existVarKey]['ebay_qty'] = $changeVariations['qty'];
            $existingVarInfo[$existVarKey]['ebay_sold_qty'] +=$qtyVarSyncChange;


            $existingVarInfo[$existVarKey]['ebay_sold_qty_sync']+=$qtyVarSyncChange;
            $sellingProductVariation->updateVariationSynchronizeInfo($existingVarInfo[$existVarKey]);
        }
    }

}
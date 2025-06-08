<?php

/**
 * File EbayListingActionHelper.php
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
class EbayListingActionHelper
{

    public static function relistList($itemId)
    {
        // Try to find item on eBay listing
        $ebayListingModel = EbayListingsModel::loadByItemId($itemId);
        if (!$ebayListingModel) {
            return array(
                'success' => false,
                'warnings' => "",
                'errors' => L::t("Item not found"),
                'item' => array()
            );
        }

        $accountModel = new AccountsModel($ebayListingModel->account_id);
        if (!$accountModel->id) {
            return array(
                'success' => false,
                'warnings' => "",
                'errors' => L::t("eBay Account for that item not available"),
                'item' => array()
            );
        }

        // Check status for it
        // When status ok try to send stop request
        if (is_null($ebayListingModel->status) || $ebayListingModel->status == EbayListingsModel::STATUS_ACTIVE) {
            return array(
                'success' => false,
                'warnings' => "",
                'errors' => L::t("Item already active"),
                'item' => array()
            );
        }

        // eBay Request. Do quick relist
        ApiModel::getInstance()->reset();

        $relistItemToEbayResult = ApiModel::getInstance()->ebay->item->relistQuick(array(
                    'token' => $accountModel->token,
                    'itemId' => $itemId
                ))->post();

        $success = false;
        $itemInfo = array();

        if (isset($relistItemToEbayResult['success']) && $relistItemToEbayResult['success']) {

            $relistItemToEbayResult['item_info'] += array(
                'ebay_start_time' => date("Y-m-d H:i:s", strtotime($relistItemToEbayResult['item_info']['ebay_start_date_raw'])),
                'ebay_end_time' => date("Y-m-d H:i:s", strtotime($relistItemToEbayResult['item_info']['ebay_end_date_raw']))
            );

            $newUrl = str_replace($itemId, $relistItemToEbayResult['item_info']['ebay_id'], $ebayListingModel->url);

            $relistItemToEbayResult['item_info']['item_path'] = $newUrl;

            $relistItemToEbayResult['item_info']['product_qty'] = $ebayListingModel->qty;

            $relistItemToEbayResult['item_info']['product_price'] = $ebayListingModel->buy_price;

            $relistItemToEbayResult['item_info']['product_name'] = $ebayListingModel->title;
            $relistItemToEbayResult['item_info']['ebay_sold_qty'] = 0;
            $relistItemToEbayResult['item_info']['ebay_sold_qty_sync'] = 0;
            $relistItemToEbayResult['item_info']['product_qty_change'] = 0;

            // Update connected eBay Listing information
            $ebayListingModel->setData(array(
                'qty_available' => $ebayListingModel->qty,
                'status' => EbayListingsModel::STATUS_ACTIVE,
                'url' => $newUrl,
                'item_id' => $relistItemToEbayResult['item_info']['ebay_id'],
                'start_time' => $relistItemToEbayResult['item_info']['ebay_start_time']
            ));
            $ebayListingModel->id = null; // new object
            $ebayListingModel->save();

            $success = true;
            $itemInfo = $relistItemToEbayResult['item_info'];
        }

        return array(
            'success' => $success,
            'warnings' => ApiModel::getInstance()->getWarningsAsHtml(),
            'errors' => ApiModel::getInstance()->getErrorsAsHtml(),
            'item' => $itemInfo
        );
    }

    public static function stopList($itemId)
    {
        // Try to find item on eBay listing
        $ebayListingModel = EbayListingsModel::loadByItemId($itemId);
        if (!$ebayListingModel) {
            return array(
                'success' => false,
                'warnings' => "",
                'errors' => L::t("Item not found"),
                'item' => array()
            );
        }

        $accountModel = new AccountsModel($ebayListingModel->account_id);
        if (!$accountModel->id) {
            return array(
                'success' => false,
                'warnings' => "",
                'errors' => L::t("eBay Account for that item not available"),
                'item' => array()
            );
        }

        // Check status for it
        // When status ok try to send stop request
        if (is_null($ebayListingModel->status) || $ebayListingModel->status != EbayListingsModel::STATUS_ACTIVE) {
            return array(
                'success' => false,
                'warnings' => "",
                'errors' => L::t("Item not listed or not active"),
                'item' => array()
            );
        }

        // eBay Request
        ApiModel::getInstance()->reset();
        ApiModel::getInstance()->setSkipBreakOutput(true);
        $tokenValue = $accountModel->token;

        $endItemToEbayResult = ApiModel::getInstance()->ebay->item->end(array(
                    'itemId' => $ebayListingModel->item_id,
                    'token' => $tokenValue
                ))->post();

        $success = false;
        $itemInfo = array();
        if (isset($endItemToEbayResult['status']) && $endItemToEbayResult['status'] == EbayListingsModel::STATUS_FINISHED) {
            // On success update information to eBay Listing product.
            // On fail it's display log
            $ebayListingModel->setData(array(
                'status' => EbayListingsModel::STATUS_FINISHED))->save();

            $itemInfo = array(
                'item_path' => $ebayListingModel->url,
                'ebay_id' => $ebayListingModel->item_id
            );

            $success = (ApiModel::getInstance()->getErrorsAsHtml() != "") ? false : true;
        }

        if ($success) {
            $message = L::t("Item Successfull Stoped");
        }

        return array(
            'success' => $success,
            'warnings' => ApiModel::getInstance()->getWarningsAsHtml(),
            'errors' => ApiModel::getInstance()->getErrorsAsHtml(),
            'item' => $itemInfo
        );
    }

    protected static function _showErrorMessages($errorsAsArray)
    {
        $errorHtml = "";
        foreach ($errorsAsArray as $error) {
            $errorHtml.=$error . "<br/>";
        }
        return array(
            'success' => false,
            'warnings' => "",
            'errors' => $errorHtml,
            'item' => array()
        );
    }

}
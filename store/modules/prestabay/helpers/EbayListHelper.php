<?php

/**
 * File EbayListHelper.php
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
class EbayListHelper
{

    const MODE_FULL = 0;
    const MODE_QTY = 1;
    const MODE_PRICE = 2;
    const MODE_QTY_PRICE = 3;


    public static function sendList($sellingId, $sellingProductId)
    {
        $sellingProductModel = new Selling_ProductsModel($sellingProductId);
        if ($sellingProductModel->status == Selling_ProductsModel::STATUS_ACTIVE) {
            return array(
                'success' => false,
                'warnings' => "",
                'errors' => L::t("Item already active"),
                'item' => array()
            );
        }

        $sellingModel = new Selling_ListModel($sellingId);

        $profileProduct = new ProfileProductModel($sellingModel, $sellingProductModel);

        // Helper class that convert profile product to list array
        $listService = new Services_Item_List($profileProduct);
        $errors = $listService->validate();
        if (!empty($errors)) {
            return self::_showErrorMessages($errors);
        }

        try {
            $requestDataArray = $listService->getData();
        } catch (Exception $ex) {
            return array(
                'success' => false,
                'warnings' => "",
                'errors' => $ex->getMessage(),
                'item' => array()
            );
        }

        $accountModel = new AccountsModel($profileProduct->getProfile()->ebay_account);
        if (!is_null($accountModel->id)) {
            $requestDataArray['token'] = $accountModel->token;
        }

        ApiModel::getInstance()->reset();
        $sendItemToEbayResult = ApiModel::getInstance()->ebay->item->send($requestDataArray)->post();

        $success = false;
        $itemInfo = array();

        if (isset($sendItemToEbayResult['success']) && $sendItemToEbayResult['success']) {
            $sendItemToEbayResult['item_info'] += array(
                'ebay_start_time' => date("Y-m-d H:i:s", strtotime($sendItemToEbayResult['item_info']['ebay_start_date_raw'])),
                'ebay_end_time' => date("Y-m-d H:i:s", strtotime($sendItemToEbayResult['item_info']['ebay_end_date_raw']))
            );

            $sendItemToEbayResult['item_info']['item_path'] = EbayHelper::getItemPath(
                            $sendItemToEbayResult['item_info']['ebay_id'],
                            $accountModel->mode,
                            $profileProduct->getProfile()->ebay_site);
            // On success update information to selling product
            // Put information about PrestaShop Product
            $productModel = new Product($sellingProductModel->product_id, false, $sellingModel->language);
            // @todo probably some problem with QTY synchronization
            $sendItemToEbayResult['item_info']['product_qty'] = Product::getQuantity((int) $productModel->id,
                    (int) $sellingProductModel->product_id_attribute > 0 ? (int) $sellingProductModel->product_id_attribute : null);

            $sendItemToEbayResult['item_info']['product_price'] = $listService->getProfileProduct()->getStartPrice();
            $sendItemToEbayResult['item_info']['ebay_sold_qty'] = 0;
            $sendItemToEbayResult['item_info']['ebay_sold_qty_sync'] = 0;


            $sellingProductModel->setData($sendItemToEbayResult['item_info'])->save();
            if ($listService->isVariationListing()) {
                // Remove all variation info and save it againe
                $sellingVariationModel = new Selling_VariationsModel();
                $sellingVariationModel->deleteVariationInfo($sellingProductId);
                $sellingVariationModel->insertVariationInfo($sellingProductId, $profileProduct->getVariations());
            }

            $success = true;
            $itemInfo = $sendItemToEbayResult['item_info'];
            Selling_ConnectionsModel::appendNewConnection((int) $sellingProductModel->product_id, (int) $sellingProductModel->product_id_attribute, (int) $sellingModel->language, $itemInfo['ebay_id']);

            // Save list item fee information
            if (isset($itemInfo['ebay_id']) && !empty($itemInfo['fee'])) {
                FeeSaveHelper::saveFee($itemInfo['ebay_id'], $accountModel->id, Selling_FeeModel::ACTION_LIST, $sellingProductModel->id, $itemInfo['fee']);
            }
        }

        self::appendListLog($sellingId, $sellingProductId, Log_SellingModel::LOG_ACTION_SEND);

        if ($success && isset($itemInfo['ebay_id'])) {
            $itemPath = EbayHelper::getItemPath($itemInfo['ebay_id'], $accountModel->mode, $profileProduct->getProfile()->ebay_site);
            $message = L::t("Item Send To eBay") . ". " . L::t("Item ID") . ": " . $itemInfo['ebay_id'] . " - <a href='{$itemPath}'>" . L::t("View on eBay") . "</a>";
            $sellingLogModel = new Log_SellingModel();
            $sellingLogModel->addSuccessLog($sellingId, $sellingProductId, Log_SellingModel::LOG_ACTION_SEND, $message);
        }

        // Return result to progress form
        return array(
            'success' => $success,
            'warnings' => ApiModel::getInstance()->getWarningsAsHtml(),
            'errors' => ApiModel::getInstance()->getErrorsAsHtml(),
            'item' => $itemInfo
        );
    }

    public static function reviseList($sellingId, $sellingProductId, $mode = self::MODE_FULL)
    {
        $sellingProductModel = new Selling_ProductsModel($sellingProductId);
        $sellingModel = new Selling_ListModel($sellingId);

        if (is_null($sellingProductModel->status) || $sellingProductModel->status != Selling_ProductsModel::STATUS_ACTIVE) {
            return array(
                'success' => false,
                'warnings' => "",
                'errors' => L::t("Item not active"),
                'item' => array()
            );
        }
        $profileProduct = new ProfileProductModel($sellingModel, $sellingProductModel);

        $isGTC = $profileProduct->getProfile()->isGTC();
        $isOOSC = Configuration::get('INVEBAY_SYNC_TASK_OOSC');
        $isStopAllowed = !($isGTC && $isOOSC);

        // Helper class that convert profile product to list array
        $reviseService = new Services_Item_Revise($profileProduct);

        if ($isStopAllowed && $profileProduct->getQty(true) <= 0) {
            // Product QTY less or equal to zero, we need to stop this product.
            $resultOfStop = self::stopList($sellingId, $sellingProductId);
            $resultOfStop['warnings'] != "" && $resultOfStop['warnings'].="<br/>";
            $resultOfStop['warnings'] .= L::t("This item has zero QTY") . ". " . L::t("Stopped on eBay");

            return $resultOfStop;
        }

        $sellingVariationModel = new Selling_VariationsModel();

        try {
            if ($reviseService->isVariationListing()) {
                // compare existing variation to variation data that we send to eBay
                // attribute product that deleted from PS need to be send every time as QTY = 0
                $existingVariationInfo = $sellingVariationModel->getPrestaBayVariationInfo($sellingProductId);
                $newVariationInfo = $profileProduct->getVariations();
                $variationInfo = VariationHelper::concatinateVariationArray($newVariationInfo, $existingVariationInfo);
                $profileProduct->setCalculatedVariation($variationInfo);
            }
            $reviseDataArray = $reviseService->getModeRelatedData($mode);
        } catch (Exception $ex) {
            return array(
                'success' => false,
                'warnings' => "",
                'errors' => $ex->getMessage(),
                'item' => array()
            );
        }

        $accountModel = new AccountsModel($profileProduct->getProfile()->ebay_account);
        if (!is_null($accountModel->id)) {
            $reviseDataArray['token'] = $accountModel->token;
        }

        // Sending request to server to revise item
        ApiModel::getInstance()->reset();
        ApiModel::getInstance()->setSkipBreakOutput(true);
        if ($mode != self::MODE_FULL) {
            $reviseItemToEbayResult = ApiModel::getInstance()->ebay->item->reviseQuick(
                array(
                    'itemId' => $sellingProductModel->ebay_id
                ) +
                    $reviseDataArray
            )->post();
        } else {
            $reviseItemToEbayResult = ApiModel::getInstance()->ebay->item->revise(
                array(
                    'itemId' => $sellingProductModel->ebay_id
                ) +
                    $reviseDataArray
            )->post();
        }

        $success = false;
        $itemInfo = array();

        if ($mode == self::MODE_FULL) {
            // If we do full revise, reset flag in DB
            $sellingProductModel->full_revise = 0;
        }

        if (isset($reviseItemToEbayResult['success']) && $reviseItemToEbayResult['success']) {
            // On success update information to selling product
            $reviseItemToEbayResult['item_info'] += array(
                'ebay_start_time' => date("Y-m-d H:i:s", strtotime($reviseItemToEbayResult['item_info']['ebay_start_date_raw'])),
                'ebay_end_time' => date("Y-m-d H:i:s", strtotime($reviseItemToEbayResult['item_info']['ebay_end_date_raw']))
            );
            $reviseItemToEbayResult['item_info']['item_path'] = EbayHelper::getItemPath(
                            $reviseItemToEbayResult['item_info']['ebay_id'],
                            $accountModel->mode,
                            $profileProduct->getProfile()->ebay_site);

            // Update information about PrestaShop Product
            $productModel = new Product($sellingProductModel->product_id, false, $sellingModel->language);
            $reviseItemToEbayResult['item_info']['product_qty'] = Product::getQuantity((int) $productModel->id,
                    (int) $sellingProductModel->product_id_attribute > 0 ? (int) $sellingProductModel->product_id_attribute : null);

            $reviseItemToEbayResult['item_info']['product_price'] = $reviseService->getProfileProduct()->getStartPrice();

//                Product::getPriceStatic((int) $productModel->id, true,
//                            (int) $sellingProductModel->product_id_attribute > 0 ? (int) $sellingProductModel->product_id_attribute : null);

//            $reviseItemToEbayResult['item_info']['product_name'] = $productModel->name;

            // This information can be not available
            if (isset($reviseItemToEbayResult['item_info']['ebay_qty'])) {
                $reviseItemToEbayResult['item_info']['ebay_qty'] = $reviseItemToEbayResult['item_info']['ebay_qty'] + $sellingProductModel->ebay_sold_qty;
            }

            // Save updated information about Selling Product (eBay + Presta Information)
            $sellingProductModel->setData($reviseItemToEbayResult['item_info'])->save();
            if ($reviseService->isVariationListing()) {
                // Update Variation information
                $sellingVariationModel = new Selling_VariationsModel();
                $sellingVariationModel->updateVariationInfo($sellingProductId, $profileProduct->getVariations());
            }
            $success = true;
            $itemInfo = $reviseItemToEbayResult['item_info'];
        } else if (isset($reviseItemToEbayResult['success']) && !$reviseItemToEbayResult['success'] &&
                isset($reviseItemToEbayResult['item_info']) && $reviseItemToEbayResult['item_info']['status'] != Selling_ProductsModel::STATUS_ERROR) {
            // When receive error from server that indicate about already ended item. Stop item
            $sellingProductModel->setData(array('status' => Selling_ProductsModel::STATUS_FINISHED))->save();
        }

        self::appendListLog($sellingId, $sellingProductId, Log_SellingModel::LOG_ACTION_REVISE);

        if ($success) {
            // Save revise item fee information only if revise fee > 0
            if (isset($itemInfo['ebay_id']) && !empty($itemInfo['fee']['total']) && $itemInfo['fee']['total'] > 0) {
                FeeSaveHelper::saveFee($itemInfo['ebay_id'], $accountModel->id, Selling_FeeModel::ACTION_REVISE, $sellingProductId, $itemInfo['fee']);
            }

            $message = L::t("Item Revised on eBay");
            $sellingLogModel = new Log_SellingModel();
            $sellingLogModel->addSuccessLog($sellingId, $sellingProductId, Log_SellingModel::LOG_ACTION_REVISE, $message);
        }

        // Return result
        return array(
            'success' => $success,
            'warnings' => ApiModel::getInstance()->getWarningsAsHtml(),
            'errors' => ApiModel::getInstance()->getErrorsAsHtml(),
            'item' => $itemInfo
        );
    }

    public static function relistList($sellingId, $sellingProductId, $denyRelistActive = false)
    {
        $sellingProductModel = new Selling_ProductsModel($sellingProductId);
        $sellingModel = new Selling_ListModel($sellingId);

        if ($denyRelistActive && is_null($sellingProductModel->status) || $sellingProductModel->status == Selling_ProductsModel::STATUS_ACTIVE) {
            return array(
                'success' => false,
                'warnings' => "",
                'errors' => L::t("Item already active"),
                'item' => array()
            );
        }

        // Helper class that convert profile product to relist array
        $profileProduct = new ProfileProductModel($sellingModel, $sellingProductModel);
        $listService = new Services_Item_Relist($profileProduct);
        $errors = $listService->validate();
        if (!empty($errors)) {
            return self::_showErrorMessages($errors);
        }

        try {
            $requestDataArray = $listService->getData();
        } catch (Exception $ex) {
            return array(
                'success' => false,
                'warnings' => "",
                'errors' => $ex->getMessage(),
                'item' => array()
            );
        }

        $accountModel = new AccountsModel($profileProduct->getProfile()->ebay_account);
        if (!is_null($accountModel->id)) {
            $requestDataArray['token'] = $accountModel->token;
        }

        ApiModel::getInstance()->reset();
        $relistItemToEbayResult = ApiModel::getInstance()->ebay->item->relist(
                        array('itemId' => $sellingProductModel->ebay_id) +
                        $requestDataArray)->post();

        $success = false;
        $itemInfo = array();

        if (isset($relistItemToEbayResult['success']) && $relistItemToEbayResult['success']) {
            // On success update information to selling product
            $relistItemToEbayResult['item_info'] += array(
                'ebay_start_time' => date("Y-m-d H:i:s", strtotime($relistItemToEbayResult['item_info']['ebay_start_date_raw'])),
                'ebay_end_time' => date("Y-m-d H:i:s", strtotime($relistItemToEbayResult['item_info']['ebay_end_date_raw']))
            );

            $relistItemToEbayResult['item_info']['item_path'] = EbayHelper::getItemPath(
                            $relistItemToEbayResult['item_info']['ebay_id'],
                            $accountModel->mode,
                            $profileProduct->getProfile()->ebay_site);

            // Update information about PrestaShop Product
            $productModel = new Product($sellingProductModel->product_id, false, $sellingModel->language);

            $relistItemToEbayResult['item_info']['product_qty'] = Product::getQuantity((int) $productModel->id,
                    (int) $sellingProductModel->product_id_attribute > 0 ? (int) $sellingProductModel->product_id_attribute : null);

            $relistItemToEbayResult['item_info']['product_price'] = $listService->getProfileProduct()->getStartPrice();

            $relistItemToEbayResult['item_info']['product_name'] = $requestDataArray['title'];
            $relistItemToEbayResult['item_info']['ebay_sold_qty'] = 0;
            $relistItemToEbayResult['item_info']['ebay_sold_qty_sync'] = 0;
            $relistItemToEbayResult['item_info']['product_qty_change'] = 0;

            $sellingProductModel->setData($relistItemToEbayResult['item_info'])->save();
            if ($listService->isVariationListing()) {
                // Update Variation information
                $sellingVariationModel = new Selling_VariationsModel();
                $sellingVariationModel->resetVariationInfo($sellingProductId);
            }
            Selling_ConnectionsModel::appendNewConnection((int) $sellingProductModel->product_id, (int) $sellingProductModel->product_id_attribute, (int) $sellingProductModel->product_id_attribute, (int) $sellingModel->language, $relistItemToEbayResult['item_info']['ebay_id']);

            $success = true;
            $itemInfo = $relistItemToEbayResult['item_info'];
        }

        self::appendListLog($sellingId, $sellingProductId, Log_SellingModel::LOG_ACTION_RELIST);

        if ($success && isset($itemInfo['ebay_id'])) {
            $itemPath = EbayHelper::getItemPath($itemInfo['ebay_id'], $accountModel->mode, $profileProduct->getProfile()->ebay_site);
            $message = L::t("Item Reslisted on eBay") . ". " . L::t("Item ID") . ": " . $itemInfo['ebay_id'] . " - <a href='{$itemPath}'>" . L::t("View on eBay") . "</a>";
            $sellingLogModel = new Log_SellingModel();
            $sellingLogModel->addSuccessLog($sellingId, $sellingProductId, Log_SellingModel::LOG_ACTION_RELIST, $message);

            // Save relist item fee information
            if (!empty($itemInfo['fee'])) {
                FeeSaveHelper::saveFee($itemInfo['ebay_id'], $accountModel->id, Selling_FeeModel::ACTION_RELIST, $sellingProductId, $itemInfo['fee']);
            }
        }

        return array(
            'success' => $success,
            'warnings' => ApiModel::getInstance()->getWarningsAsHtml(),
            'errors' => ApiModel::getInstance()->getErrorsAsHtml(),
            'item' => $itemInfo
        );
    }

    /**
     * @param $sellingId
     * @param $sellingProductId
     * @param bool $stopFromSynchronization does this stop call from automatic synchronization
     * @return array
     */
    public static function stopList($sellingId, $sellingProductId, $stopFromSynchronization = false)
    {
        $sellingProductModel = new Selling_ProductsModel($sellingProductId);
        $sellingModel = new Selling_ListModel($sellingId);


        if (is_null($sellingProductModel->status) || $sellingProductModel->status != Selling_ProductsModel::STATUS_ACTIVE) {
            return array(
                'success' => false,
                'warnings' => "",
                'errors' => L::t("Item not listed or not active"),
                'item' => array()
            );
        }

        $profileModel = new ProfilesModel($sellingModel->profile);
        $accountModel = new AccountsModel($profileModel->ebay_account);
        $tokenValue = null;
        if (!is_null($accountModel->id)) {
            $tokenValue = $accountModel->token;
        }

        if ($stopFromSynchronization) {
            // This can work with Out-of-stock control + GTC listings
            $isGTC = $profileModel->isGTC();
            $isOOSC = Configuration::get('INVEBAY_SYNC_TASK_OOSC');
            $isStopNotAllowed = ($isGTC && $isOOSC);

            if ($isStopNotAllowed) {
                return array(
                    'success' => false,
                    'skipped' => true,
                    'warnings' => "",
                    'errors' => "",
                    'item' => array()
                );
            }
        }

        // eBay Request
        ApiModel::getInstance()->reset();
        ApiModel::getInstance()->setSkipBreakOutput(true);

        $endItemToEbayResult = ApiModel::getInstance()->ebay->item->end(array(
                    'itemId' => $sellingProductModel->ebay_id,
                    'token' => $tokenValue
                ))->post();

        $success = false;
        $itemInfo = array();
        if (isset($endItemToEbayResult['status']) && $endItemToEbayResult['status'] == Selling_ProductsModel::STATUS_FINISHED) {
            // On success update information to selling product.
            // On fail it's display log
            $sellingProductModel->setData(array('status' => Selling_ProductsModel::STATUS_FINISHED))->save();

            $itemInfo = $sellingProductModel->getFields();

            if (isset($itemInfo['ebay_id'])) {
                $itemInfo['item_path'] = EbayHelper::getItemPath(
                                $itemInfo['ebay_id'],
                                $accountModel->mode,
                                $profileModel->ebay_site);
            }

            $success = (ApiModel::getInstance()->getErrorsAsHtml() != "") ? false : true;
        }

        self::appendListLog($sellingId, $sellingProductId, Log_SellingModel::LOG_ACTION_STOP);

        if ($success) {
            $message = L::t("Item Successfull Stoped");
            $sellingLogModel = new Log_SellingModel();
            $sellingLogModel->addSuccessLog($sellingId, $sellingProductId, Log_SellingModel::LOG_ACTION_STOP, $message);
        }

        return array(
            'success' => $success,
            'warnings' => ApiModel::getInstance()->getWarningsAsHtml(),
            'errors' => ApiModel::getInstance()->getErrorsAsHtml(),
            'item' => $itemInfo
        );
    }

    public static function appendListLog($sellingId, $sellingProductId, $action)
    {
        // Write log messages to DB
        $sellingLogModel = new Log_SellingModel();
        if (count(ApiModel::getInstance()->getWarnings()) > 0) {
            $sellingLogModel->writeLogMessages($sellingId, $sellingProductId, $action, Log_SellingModel::LOG_LEVEL_WARNING, ApiModel::getInstance()->getWarnings());
        }

        if (count(ApiModel::getInstance()->getErrors()) > 0) {
            $sellingLogModel->writeLogMessages($sellingId, $sellingProductId, $action, Log_SellingModel::LOG_LEVEL_ERROR, ApiModel::getInstance()->getErrors());
        }
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
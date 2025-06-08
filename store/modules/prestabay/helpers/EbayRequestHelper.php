<?php


class EbayRequestHelper
{

    const ACTION_LIST = 'list';
    const ACTION_RELIST = 'relist';
    const ACTION_STOP = 'stop';
    const ACTION_REVISE = 'revise';

    protected $allowedActions = array(self::ACTION_LIST);

    /**
     * Perform an action with item to ebay
     *
     * @param array $itemsToApplyChanges items list to apply action
     * @param string $action selected action to apply
     *
     * @return array result of action
     *
     * @throws Exception
     */
    public function itemAction($itemsToApplyChanges, $action)
    {
        if (!in_array($action, $this->allowedActions)) {
            throw new Exception('Invalid action');
        }

        $apiAction = $action;
        if ($apiAction == 'list') {
            $apiAction = 'send';
        }

        $sellingProductIds = array();
        foreach ($itemsToApplyChanges as $row) {
            $sellingProductIds[$row['index']] = $row['id'];
        }

        $actionClassName = 'Services_Item_'.ucfirst($action);

        $response = array();
        $dataToRequest = array();
        $accountToUse = false;

        foreach ($sellingProductIds as $index => $sellingProductId) {
            $response[$sellingProductId] = array(
                'index' => $index
            );
            $sellingProductModel = new Selling_ProductsModel($sellingProductId);
            if (!$sellingProductModel->id) {
                $response[$sellingProductId] += $this->generateResponse($apiAction, false, false, false, array(), L::t('Item not found'));
                continue;
            }
            $sellingId = $sellingProductModel->selling_id;

            $response[$sellingProductId]['sellingId'] = $sellingId;
            $response[$sellingProductId]['sellingProductId'] = $sellingProductId;

            $sellingModel = new Selling_ListModel($sellingId);
            $profileProduct = new ProfileProductModel($sellingModel, $sellingProductModel);

            if (!$accountToUse) {
                $accountToUse = $profileProduct->getProfile()->ebay_account;
            } elseif ($profileProduct->getProfile()->ebay_account != $accountToUse) {
                $response[$sellingProductId] += $this->generateResponse(
                    $apiAction,
                    false,
                    $sellingId,
                    $sellingProductId,
                    array(),
                    L::t('Item from other seller account')
                );
                continue;
            }

            /** @var Services_Request $actionService */
            $actionService = new $actionClassName($profileProduct);

            // Do service validation
            $validationErrors = $actionService->validate();
            if (!empty($validationErrors)) {
                $response[$sellingProductId] += $this->generateResponse(
                    $apiAction,
                    false,
                    $sellingId,
                    $sellingProductId,
                    array(),
                    $validationErrors
                );
                continue;
            }

            try {
                $singleRequestData = $actionService->getData();
                $singleRequestData['id'] = $sellingProductId;
                $dataToRequest[$sellingProductId] = $singleRequestData;
            } catch (Exception $ex) {
                $response[$sellingProductId] += $this->generateResponse(
                    $apiAction,
                    false,
                    $sellingId,
                    $sellingProductId,
                    array(),
                    $ex->getMessage()
                );
                continue;
            }
        } // end of selling products foreach

        if (empty($dataToRequest)) {
            // no data to send
            return $response;
        }

        $requestDataArray = array(
            'items' =>   $dataToRequest
        );

        $accountModel = new AccountsModel($accountToUse);
        if (!is_null($accountModel->id)) {
            $requestDataArray['token'] = $accountModel->token;
        }

        ApiModel::getInstance()->reset();

        $requestResponse = ApiModel::getInstance()->ebay->multi->$apiAction($requestDataArray)->post();
        if ($requestResponse == false) {
            // Full request failed
            throw new Exception(ApiModel::getInstance()->getErrorsAsHtml());
        }
        $responseHandlerMethod = $action.'ResponseHandler';
        foreach ($requestResponse as $responseSellingProductId => $apiResponseData) {
            $isSuccess = empty($apiResponseData['errors']);

            $response[$responseSellingProductId] += $this->generateResponse(
                $apiAction,
                $isSuccess,
                $response[$responseSellingProductId]['sellingId'],
                $responseSellingProductId,
                array(), // items info empty by default
                $apiResponseData['errors'],
                $apiResponseData['warnings']
            );

            // Save list item fee information
            if (isset($apiResponseData['item_info']['ebay_id']) && !empty($apiResponseData['item_info']['fee'])) {
                FeeSaveHelper::saveFee($apiResponseData['item_info']['ebay_id'], $accountModel->id, Selling_FeeModel::ACTION_LIST, $responseSellingProductId, $apiResponseData['item_info']['fee']);
            }

            if ($isSuccess) {
                 $handlerResult = call_user_func(
                    array($this, $responseHandlerMethod),
                    $responseSellingProductId,
                    $apiResponseData,
                    $response[$responseSellingProductId],
                    $dataToRequest[$responseSellingProductId],
                    $accountModel
                );

                $response[$responseSellingProductId] = $handlerResult + $response[$responseSellingProductId];
            }
        }

        return $response;
    }

    /**
     * Response parse handler for list item action
     *
     * @param $sellingProductId
     * @param $apiResponseData
     * @param $itemResponse
     * @param $requestData
     * @param $accountModel
     *
     * @return mixed
     */
    protected function listResponseHandler($sellingProductId, $apiResponseData, $itemResponse, $requestData, $accountModel)
    {
        $itemInfo = $apiResponseData['item_info'];
        $itemInfo['ebay_start_time'] = date("Y-m-d H:i:s", strtotime($apiResponseData['item_info']['ebay_start_date_raw']));
        $itemInfo['ebay_end_time'] = date("Y-m-d H:i:s", strtotime($apiResponseData['item_info']['ebay_end_date_raw']));

        $marketplaceId = MarketplacesModel::getMarketplaceIdByCode($requestData['listing']['site']);

        $itemInfo['item_path'] = EbayHelper::getItemPath(
            $apiResponseData['item_info']['ebay_id'],
            $accountModel->mode,
            $marketplaceId
        );

        $sellingProductModel = new Selling_ProductsModel($sellingProductId);
        $sellingId = $sellingProductModel->selling_id;
        $sellingModel = new Selling_ListModel($sellingId);

        // On success update information to selling product
        // Put information about PrestaShop Product
        $productModel = new Product($sellingProductModel->product_id, false, $sellingModel->language);
        // @todo probably some problem with QTY synchronization
        $itemInfo['product_qty'] = Product::getQuantity((int) $productModel->id,
            (int) $sellingProductModel->product_id_attribute > 0 ? (int) $sellingProductModel->product_id_attribute : null);

        $itemInfo['product_price'] = $requestData['price']['start'];
        $itemInfo['ebay_sold_qty'] = 0;
        $itemInfo['ebay_sold_qty_sync'] = 0;

        $sellingProductModel->setData($itemInfo)->save();
        $isVariation = isset($requestData['listing']['variations']) && count($requestData['listing']['variations']) > 0;

        if ($isVariation) {
            // Remove all variation info and save it againe
            $sellingVariationModel = new Selling_VariationsModel();
            $sellingVariationModel->deleteVariationInfo($sellingProductId);
            $sellingVariationModel->insertVariationInfo($sellingProductId, $requestData['listing']['variations']);
        }

        $message = L::t("Item Send To eBay") . ". " . L::t("Item ID") . ": " . $itemInfo['ebay_id'] . " - <a href='{$itemInfo['item_path']}'>" . L::t("View on eBay") . "</a>";
        $sellingLogModel = new Log_SellingModel();
        $sellingLogModel->addSuccessLog($sellingId, $sellingProductId, Log_SellingModel::LOG_ACTION_SEND, $message);

        Selling_ConnectionsModel::appendNewConnection(
            (int) $sellingProductModel->product_id,
            (int) $sellingProductModel->product_id_attribute,
            (int) $sellingModel->language, $itemInfo['ebay_id']
        );
        $itemResponse['item'] = $itemInfo;
        $itemResponse['newState'] = L::t('Active');

        return $itemResponse;
    }

    /**
     * Generate response
     *
     * @param boolean $result execution result
     * @param array  $item item data
     * @param string $errors errors texts
     * @param string $warnings warnigns texts
     *
     * @return array
     */
    protected function generateResponse($apiAction, $result, $sellingId, $sellingProductId, $item = array(), $errors = array(), $warnings = array())
    {
        $warningsText = $warnings;
        $errorsText = $errors;

        if (is_array($errors)) {
            $errorsText = $this->convertErrorsArrayToString($errors);
        }
        if (is_array($warnings)) {
            $warningsText = $this->convertErrorsArrayToString($warnings);
        }
        // Append logs to selling and selling products id
        $sellingLogModel = new Log_SellingModel();
        if (!is_null($sellingId) && !is_null($sellingProductId)) {
            if (count($warnings) > 0) {
                $sellingLogModel->writeLogMessages($sellingId, $sellingProductId, $apiAction, Log_SellingModel::LOG_LEVEL_WARNING, $warnings);
            }

            if (count($errors) > 0) {
                $sellingLogModel->writeLogMessages($sellingId, $sellingProductId, $apiAction, Log_SellingModel::LOG_LEVEL_ERROR, $errors);
            }
        }

        return array(
            'success' => $result,
            'item' => $item,
            'warnings' => $warningsText,
            'errors' => $errorsText,
        );
    }

    /**
     * Convert errors array to html string
     *
     * @param array $errorsAsArray
     *
     * @return string concatenated array errors
     */
    protected function convertErrorsArrayToString($errorsAsArray)
    {
        $messagesList = array();
        foreach ($errorsAsArray as $row) {
            if (is_array($row) && isset($row['message'])) {
                $messagesList[] = $row['message'];
            } else {
                $messagesList[] = $row;
            }

        }

        return implode("<br/>", $messagesList);
    }

    /**
     * Convert response Errors/Warnings into string
     *
     * @param array $messageAsArray
     *
     * @return string html texts of errors
     */
    protected function convertMessageResponseArrayToString($messageAsArray)
    {
        $responseAsText = '';
        foreach ($messageAsArray as $row) {
            $responseAsText .= $row['message'] . "<br/>";
        }
        return $responseAsText;
    }

}
<?php

/**
 * File EbayListingsController.php
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
class EbayListingsController extends BaseAdminController
{

    /**
     * Show grid with all eBay Listings
     */
    public function indexAction()
    {
        if (UrlHelper::getGet("submitDownload", false)) {
            UrlHelper::redirect('ebayListings/downloadSelectAccount');
        }
        
        if (UrlHelper::getGet("submitStop", false)) {
            $this->ebayAction('stop');
            return;
        }
        
        if (UrlHelper::getGet("submitRelist", false)) {
            $this->ebayAction("relist");
            return;
        }

        $moveTo = (int)UrlHelper::getGet("moveTo", false);
        $massactionSubmit = UrlHelper::getGet("massaction-submit", false);

        if ($moveTo > 0 && $massactionSubmit == 1) {
            $this->moveToSellingList($moveTo);
            return;
        } elseif ($massactionSubmit == 2) {
            // Autodetect
            $this->detectByTitleSku();
            return;
        } elseif ($massactionSubmit == 3) {
            // Autodetect
            $this->detectAllByTitleSku();
            return;
        }

        $selectedAccountId = UrlHelper::getGet("account_select", false);
        $myGrid = new Grids_EBayListings();
        if ($selectedAccountId) {
            $myGrid->setAccountFilter($selectedAccountId);
            $myGrid->init();
        }
        $grid = $myGrid->getHtml(false);

        $accountsModel = new AccountsModel();
        $sellingListModel = new Selling_ListModel();

        $this->view('ebayListings/index.phtml', array(
            'grid' => $grid,
            'accountsList' => $accountsModel->getSelect()->getItems(),
            'sellingList' => $sellingListModel->getSelect()->getItems(),
            'selectedAccountId' => $selectedAccountId));
    }

    /**
     * Show form to select account for witch download eBay listings
     */
    public function downloadSelectAccountAction()
    {
        $selectedAccountId = UrlHelper::getPost('ebay_account', false);
        if (!$selectedAccountId) {
            $accountsModel = new AccountsModel();
            $this->view('ebayListings/selectAccount.phtml', array('accounts' => $accountsModel->getSelect()->getItems()));
        } else {
            UrlHelper::redirect('ebayListings/downloadListings', array('accountId' => $selectedAccountId));
        }
    }

    public function downloadListingsAction()
    {
        $accountId = UrlHelper::getGet('accountId', false);
        if (!$accountId) {
            RenderHelper::addError(L::t("Invalid account"));
            UrlHelper::redirect('ebayListings/index');
            return;
        }
        $accountsModel = new AccountsModel($accountId);
        $this->view('ebayListings/downloadListings.phtml', array('account' => $accountsModel));
    }

    public function importEbayListingsAjaxAction()
    {
        RenderHelper::cleanOutput();

        $accountId = UrlHelper::getPost('accountId', false);
        $page = UrlHelper::getPost('page', false);

        if (!$accountId || !$page) {
            echo json_encode(array(
                'success' => false,
                'errors' => L::t('Incorrect Request')
            ));
            return false;
        }
        $accountModel = new AccountsModel($accountId);

        ApiModel::getInstance()->reset();
        $result = ApiModel::getInstance()->ebay->account->getActiveListings(array(
            'token' => $accountModel->token,
            'pageNumber' => $page
        ))->post();

        if (!$result || !isset($result['totalPages']) || !isset($result['totalItems'])
                || !isset($result['items'])) {
            echo json_encode(array(
                'success' => false,
                'errors' => ApiModel::getInstance()->getErrorsAsHtml(),
                'warnings' => ApiModel::getInstance()->getWarningsAsHtml(),
            ));
            return false;
        }
        
        // Try to import some product
        $ebayListingsModel = new EbayListingsModel();
        list($importedCount, $existInPrestaBay, $existInEbayListings, $importErrorsHtml) = $ebayListingsModel->importListings($accountId, $result['items']);
        $apiErrorsHtml = ApiModel::getInstance()->getErrorsAsHtml();
        $errorsHtml = $apiErrorsHtml;
        $errorsHtml != "" && $importErrorsHtml != "" && $errorsHtml .= "<br/>";
        $errorsHtml .= $importErrorsHtml;
        
        echo json_encode(array(
            'success' => true,
            'errors' => $errorsHtml,
            'warnings' => ApiModel::getInstance()->getWarningsAsHtml(),
            'totalPages' => $result['totalPages'],
            'totalItems' => $result['totalItems'],
            'totalImport' => $importedCount,
            'skipPrestaBay' => $existInPrestaBay,
            'skipEbayListing' => $existInEbayListings));
    }


    public function ebayAction($actionName)
    {
        $idsList = UrlHelper::getGet("ebay_listingsBox", array());
        if ($idsList == array()) {
            RenderHelper::addError(L::t("Please Select at least One Product"));
            UrlHelper::redirect("ebayListings/index");
            return;
        }

        $productsInfo = EbayListingsModel::getListingsInfo($idsList);

        $this->view("ebayListings/ebayAction.phtml", array(
            'ebayListingsIds' => $productsInfo,
            'action' => $actionName
        ));
    }

    /**
     * Stop Item in eBay Ajax Action.
     *
     * @return json of result sending
     */
    public function stopAjaxAction()
    {
        RenderHelper::cleanOutput();

        $itemId = UrlHelper::getPost("ebayListingsId", null);

        $result = EbayListingActionHelper::stopList($itemId);

        echo json_encode($result);
        return;
    }

   /**
     * Relsit Item on eBay Site
     *
     * @return json of result sending
     */
    public function relistAjaxAction()
    {
        RenderHelper::cleanOutput();

        $itemId = UrlHelper::getPost("ebayListingsId", null);


        echo json_encode(EbayListingActionHelper::relistList($itemId));
        return;
    }

    /**
     *
     */
    public function setProductIdModalAction()
    {
        RenderHelper::cleanOutput();

        $id = UrlHelper::getGet('rowid', false);
        $ebayListing = new EbayListingsModel($id);

        $this->view('ebayListings/setProductIdModal.phtml', array(
            'ebayListing' => $ebayListing
        ));
    }

    public function sendProductIdSetAjaxAction()
    {
        RenderHelper::cleanOutput();
        $request = UrlHelper::getPost();
        if (!isset($request['id']) || !isset($request['product_id'])) {
            json_encode(array(
                'success' => false,
                'message' => 'Invalid Request'
            ));
            return false;
        }

        $ebayListingsModel = new EbayListingsModel((int)$request['id']);
        if (!$ebayListingsModel->id) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Invalid Listings id'
            ));
            return false;
        }

        $ebayListingsModel->product_id = $request['product_id'];
        if (!$ebayListingsModel->save()) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Failed save product id'
            ));
            return false;

        }

        echo json_encode(array(
            'success' => true,
        ));

        return;
    }


    /**
     * Handle moving items to Selling List
     */
    protected function moveToSellingList($sellingListId)
    {
        $idsList = UrlHelper::getGet("ebay_listingsBox", array());
        if (empty($idsList)) {
            RenderHelper::addError(L::t("Please Select at least One Product"));
            UrlHelper::redirect("ebayListings/index");
            return;
        }

        $sellingList = new Selling_ListModel($sellingListId);
        if (!$sellingList->id) {
            RenderHelper::addError(L::t("Selling List not found"));
            UrlHelper::redirect("ebayListings/index");
            return;
        }

        $totalImport = $sellingList->moveProductsFromEbayListings($idsList);
        if ($totalImport == 0) {
            RenderHelper::addError(L::t("Move Products failed"));
            UrlHelper::redirect("ebayListings/index");
            return;
        }

        if ($totalImport == count($idsList)) {
            RenderHelper::addSuccess(sprintf(L::t("Total %s Items successfully moved"), $totalImport));
        } else {
            RenderHelper::addWarning(sprintf(L::t("Not all items moved to Selling List. Moved – %s, not moved – %s item(s)"),  $totalImport, count($idsList) -  $totalImport));
        }

        UrlHelper::redirect("ebayListings/index");
    }

    /**
     * Detect for selected ebay listings possible product in PS.
     * Detect by Title & SKU
     */
    protected function detectByTitleSku()
    {
        $idsList = UrlHelper::getGet("ebay_listingsBox", array());
        if (empty($idsList)) {
            RenderHelper::addError(L::t("Please Select at least One Product"));
            UrlHelper::redirect("ebayListings/index");
            return;
        }

        $model = new EbayListingsModel();
        $totalDetected = $model->detectByTitleSku($idsList);
        RenderHelper::addSuccess(sprintf(L::t("Autodetect finished. Finded %s from %s items"), $totalDetected, count($idsList)));
        UrlHelper::redirect("ebayListings/index");
        return;
    }

    /**
     * Detect all not mapped ebay listings to possible product in PS.
     * Detect by Title & SKU & PrestaShop ID (presta_$id)
     */
    protected function detectAllByTitleSku()
    {
        $model = new EbayListingsModel();
        list($totalToDetect, $totalDetected) = $model->detectAllByTitleSku();
        RenderHelper::addSuccess(sprintf(L::t("Autodetect finished. Finded %s from %s items"), $totalDetected, $totalToDetect));
        UrlHelper::redirect("ebayListings/index");
        return;
    }
}

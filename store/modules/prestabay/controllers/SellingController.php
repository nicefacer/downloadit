<?php
/**
 * File SellingController.php
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

class SellingController extends BaseAdminController
{

    public function indexAction()
    {
        if (UrlHelper::getGet("massaction-submit", false) == 1) {
            // User select massaction block
            $sellingId = UrlHelper::getGet('sellings_listBox', false);
            if (empty($sellingId)) {
                RenderHelper::addError(L::t("Please select at least one Selling List"));
            } else {
                $massactionsType = UrlHelper::getGet('grid-massactions', false);
                if ($massactionsType) {
                    $this->sellingMassaction($massactionsType, $sellingId);
                    return;
                }
            }

        }
        $grid = new Grids_SellingList();
        $grid->getHtml();

    }

    /**
     * Output PrestaShop products with number of listings at ebay
     * This functionality can be useful to understand not listed items
     */
    public function listedProductsAction()
    {
        $listedProductsGrid = new Grids_ListedProducts((int) (Configuration::get('PS_LANG_DEFAULT')));
        $listedProductsGrid->getHtml();
    }

    protected function sellingMassaction($massactionType, $sellingIds)
    {
        $isQuick = false;
        $ebayAction = false;
        switch ($massactionType)
        {
            case Grids_SellingList::MASSACTION_QTY_REVISE:
                $isQuick = "qty";
                $ebayAction = "revise";
                break;
            case Grids_SellingList::MASSACTION_PRICE_REVISE:
                $isQuick  = "price";
                $ebayAction = "revise";
                break;
            case Grids_SellingList::MASSACTION_QTY_PRICE_REVISE:
                $isQuick = "qty_price";
                $ebayAction = "revise";
                break;

            case Grids_SellingList::MASSACTION_FULL_REVISE:
                $ebayAction = "revise";
                break;

            case Grids_SellingList::MASSACTION_RELIST_AVAILABLE:
                $ebayAction = "relistWithQty";
                break;

            case Grids_SellingList::MASSACTION_STOP_ALL_ACTIVE:
                $ebayAction = "stop";
                break;

            case Grids_SellingList::MASSACTION_STOP_ZERO_QTY:
                $ebayAction = "stopQty0";
                break;
        }
        $sellingModel = new Selling_ListModel();
        $idsList = $sellingModel->getAllProductsIdsPreparedForAction($ebayAction, $sellingIds);

        if ($idsList == false) {
            RenderHelper::addError(L::t("No products prepared to revise action"));
            UrlHelper::redirect("selling/index");
            return;
        }
        $ebayAction == "stopQty0" && $ebayAction = "stop";
        $ebayAction == "relistWithQty" && $ebayAction = "relist";

        $this->view("selling/edit/action.phtml", array(
                'sellingId' => null,
                'sellingProductIds' => $idsList,
                'action' => $ebayAction,
                'quick' => $isQuick
            ));
    }

    public function newAction()
    {
        $profilesModel = new ProfilesModel();
        $duplicateId = UrlHelper::getGet('duplicateId', null);
        $duplicateData = array();
        if (!is_null($duplicateId)) {
            $sellingListModel = new Selling_ListModel($duplicateId);
            if ($sellingListModel->id) {
                $duplicateData = $sellingListModel->getFields();
                if ($sellingListModel->mode == Selling_ListModel::MODE_CATEGORY) {
                    $duplicateData['category_id'] = Selling_CategoriesModel::getCategoriesMapped($sellingListModel->id);
                }
            }
        }

        $this->view("selling/create/step1.phtml",
                array(
                    'duplicateId'   => $duplicateId,
                    'editId'        => null,
                    'duplicateData' => $duplicateData,
                    'languageList'  => Language::getLanguages(true),
                    'profilesList'  => $profilesModel->getProfiles(),
                    'defaultLangId' => (int) (Configuration::get('PS_LANG_DEFAULT'))
        ));
    }

    public function editDetailsAction()
    {
        $profilesModel = new ProfilesModel();
        $id            = UrlHelper::getGet('id', null);
        if (is_null($id)) {
            RenderHelper::addError(L::t("Unknown Selling List"));
            UrlHelper::redirect("selling/index");
            return;
        }

        $sellingListModel = new Selling_ListModel($id);
        if (!$sellingListModel->id) {
            RenderHelper::addError(L::t("Unknown Selling List"));
            UrlHelper::redirect("selling/index");
            return;
        }
        $sellingData = $sellingListModel->getFields();
        if ($sellingListModel->mode == Selling_ListModel::MODE_CATEGORY) {
            $sellingData['category_id'] = Selling_CategoriesModel::getCategoriesMapped($sellingListModel->id);
        }

        $this->view(
            "selling/create/step1.phtml",
            array(
                'editId'        => $id,
                'duplicateId'   => null,
                'duplicateData' => $sellingData,
                'languageList'  => Language::getLanguages(true),
                'profilesList'  => $profilesModel->getProfiles(),
                'defaultLangId' => (int) (Configuration::get('PS_LANG_DEFAULT'))
            )
        );
    }

    /**
     * Show item on eBay
     */
    public function ebayViewAction()
    {
        $selliingId = UrlHelper::getGet("selling", null);
        $itemId = UrlHelper::getGet("item", null);
        if (is_null($selliingId) || is_null($itemId)) {
            RenderHelper::addError(L::t("Unknown Item"));
            UrlHelper::redirect("selling/index");
            return;
        }

        $sellingProduct = new Selling_ProductsModel($itemId);
        if ($sellingProduct->ebay_id == "" ||
                is_null($sellingProduct->ebay_id) ||
                $sellingProduct->ebay_id == 0) {
            RenderHelper::addError(L::t("Item not in eBay"));
            UrlHelper::redirect("selling/edit", array('id' => $selliingId));
            return;
        }


        $sellingModel = new Selling_ListModel($selliingId);
        $profileModel = new ProfilesModel($sellingModel->profile);
        $accountModel = new AccountsModel($profileModel->ebay_account);

        UrlHelper::redirectExternal(EbayHelper::getItemPath($sellingProduct->ebay_id, $accountModel->mode, $profileModel->ebay_site));
        return;
    }

    public function deleteAction()
    {
        $id = UrlHelper::getGet("id", null);
        if (is_null($id)) {
            RenderHelper::addError(L::t("Please Specify 'Selling List' Id"));
            UrlHelper::redirect("selling/index");
            return;
        }

        $sellingListModel = new Selling_ListModel();
        $result = $sellingListModel->deleteAllInformation($id);
        if ($result === false) {
            RenderHelper::addError(L::t("Can't delete selected 'Selling List'"));
        } else {
            RenderHelper::addSuccess(L::t("'Selling List' successfuly deleted"));
        }

        UrlHelper::redirect("selling/index");
    }

    public function saveStep1Action()
    {
        // Saving result of firt step

        $step1Values = array(
            'name' => UrlHelper::getPost("name"),
            'profile' => UrlHelper::getPost("profile"),
            'language' => UrlHelper::getPost("language"),
            'mode' => UrlHelper::getPost("mode"),
            'attribute_mode' => UrlHelper::getPost('attribute_mode'),
            'duplicate_protect_mode' => UrlHelper::getPost("duplicate_protect_mode"),
        );

        if ($step1Values['mode'] == Selling_ListModel::MODE_CATEGORY) {
            $step1Values['category_id'] = UrlHelper::getPost("category_id");
            $step1Values['category_send_product'] = UrlHelper::getPost('category_send_product', Selling_ListModel::CATEGORY_SEND_PRODUCT_NO);
        }
        $_SESSION['selling_step1'] = serialize($step1Values);

        if (($duplicateId = UrlHelper::getPost('duplicateId', false)) > 0) {
            // Duplicate listing
            $sellingProductModel = new Selling_ProductsModel();
            $sellingProductListIds = $sellingProductModel->getSellingProductsIdsBySellingId($duplicateId);

            $sellingListModel = new Selling_ListModel();
            $sellingListModel->setData($step1Values);
            $result = $sellingListModel->save();

            if ($result == false) {
                die(Tools::displayError());
            }


            if (!is_array($sellingProductListIds)) {
                $sellingProductListIds = array();
            }
            if (!is_null($sellingListModel->id)) {
                $originalSellingList = new Selling_ListModel($duplicateId);
                if ($originalSellingList->mode == Selling_ListModel::MODE_CATEGORY &&
                        $sellingListModel->mode == Selling_ListModel::MODE_CATEGORY) {
                    // When selected Category mapped we need to also move mapped category ids
                    $mappedCategoriesIds = Selling_CategoriesModel::getCategoriesMapped($duplicateId);
                    Selling_CategoriesModel::appendCategoriesConnection($sellingListModel->id, $mappedCategoriesIds);
                }

                if ($sellingListModel->appendsProducts($sellingProductListIds)) {
                    RenderHelper::addSuccess(L::t("Selling List Successfully Saved"));
                } else {
                    RenderHelper::addError(L::t("Can't save Selling List. DB Error."));
                }

                UrlHelper::redirect("selling/edit", array('id' => $sellingListModel->id));
            }

            return;
        }

        if ($step1Values['mode'] == Selling_ListModel::MODE_PRODUCT) {
            // This is for product mode
            $this->saveAction();
        } else {
            //  This is for category mode
            $this->saveCategory();
        }
    }

    public function saveEditDetailsAction()
    {
        $editId = UrlHelper::getPost("editId", null);
        if (is_null($editId)) {
            RenderHelper::addError(L::t("Unknown Item"));
            UrlHelper::redirect("selling/index");
            return;
        }
        $sellingListModel = new Selling_ListModel($editId);
        if (!$sellingListModel->id) {
            RenderHelper::addError(L::t("Unknown Selling List"));
            UrlHelper::redirect("selling/index");
            return;
        }

        $values = array(
            'name' => UrlHelper::getPost("name"),
            'profile' => UrlHelper::getPost("profile"),
            'language' => UrlHelper::getPost("language"),
            'duplicate_protect_mode' => UrlHelper::getPost("duplicate_protect_mode"),
        );

        $sellingListModel->setData($values);
        $sellingListModel->save();

        if ($sellingListModel->mode == Selling_ListModel::MODE_CATEGORY) {
            $mappedCategories = Selling_CategoriesModel::getCategoriesMapped($editId);
            $newMappedCategories = UrlHelper::getPost("category_id", array());
            $newAddedCategories = array_diff($newMappedCategories, $mappedCategories);
            if (count($newAddedCategories) > 0) {
                Selling_CategoriesModel::appendCategoriesConnection($editId, $newAddedCategories);
                $idProductsToAppend = Selling_CategoriesModel::getProductsForCategories($newAddedCategories);
                $sellingListModel->appendsProducts($idProductsToAppend);
            }
        }

        RenderHelper::addSuccess(L::t("Selling List details has been update"));
        UrlHelper::redirect("selling/edit", array('id' => $editId));

        return;
    }

    public function saveAction()
    {
        // Save recive data and forward to edit action
        // OR show grid

        $addSelected = UrlHelper::getGet("addProduct", null);
        $selectedProductsId = UrlHelper::getGet("productsBox", null);

        $sellingData = unserialize($_SESSION['selling_step1']);

        if (!is_null($addSelected) && $addSelected && is_array($selectedProductsId) && isset($_SESSION['selling_step1'])) {
            // Saving
            $sellingListModel = new Selling_ListModel();
            $sellingListModel->setData($sellingData);

            $result = $sellingListModel->save();
            if ($result == false) {
                die(Tools::displayError());
            }

            if (!is_null($sellingListModel->id)) {
                if ($sellingListModel->appendsProducts($selectedProductsId)) {
                    RenderHelper::addSuccess(L::t("Selling List Successfully Saved"));
                } else {
                    RenderHelper::addError(L::t("Can't save Selling List. DB Error."));
                }

                UrlHelper::redirect("selling/edit", array('id' => $sellingListModel->id));
            }

            return;
        }
        if (count($_POST) > 0 && !is_null($addSelected) && $addSelected ) {
            RenderHelper::addError(L::t("Please Select at least One Product"));
        }

        // display second step form
        $grid = new Grids_Products($sellingData['language']);
        $grid->getHtml();
    }

    /**
     * Get all product from specify category and adding it to selling profile
     */
    public function saveCategory()
    {
        $sellingData = unserialize($_SESSION['selling_step1']);

        $categoriesIds = $sellingData['category_id'];
        unset($sellingData['category_id']);

        $idProducts = Selling_CategoriesModel::getProductsForCategories($categoriesIds);

        if ($idProducts == false || count($idProducts) <= 0) {
            RenderHelper::addError(L::t("Selected Category don't have any active product"));
            UrlHelper::redirect("selling/new");
            return;
        }

        $sellingListModel = new Selling_ListModel();

        $sellingListModel->setData($sellingData);
        $result = $sellingListModel->save();
        if ($result == false) {
            die(Tools::displayError());
        }
        if (is_null($sellingListModel->id)) {
            die(L::t("Problem Saving Selling List"));
        }
        // try to save list of categories
        if (!Selling_CategoriesModel::appendCategoriesConnection($sellingListModel->id, $categoriesIds)) {
            die(L::t("Problem Saving Category Connection"));
        }

        if ($sellingListModel->appendsProducts($idProducts)) {
            RenderHelper::addSuccess(L::t("Selling List Successfully Saved"));
            if (isset($sellingData['category_send_product']) && $sellingData['category_send_product'] == Selling_ListModel::CATEGORY_SEND_PRODUCT_YES) {
                RenderHelper::addSuccess(L::t("Start sending products to eBay"));
                $this->allAction("send", $sellingListModel->id);
            } else {
                UrlHelper::redirect("selling/edit", array('id' => $sellingListModel->id));
            }
        } else {
            RenderHelper::addError(L::t("Can't save Selling List. DB Error."));
            UrlHelper::redirect("selling/edit", array('id' => $sellingListModel->id));
        }
        return;
    }

    public function editAction()
    {
        if (CoreHelper::isPS16() && Configuration::get('INVEBAY_NEW_SELLING_DEACTIVATE') !== '1') {
                // New Ajax Selling Edit grid for PS 1.6 and if it not deactivated
                UrlHelper::redirect('sellingAjaxEdit/index', array('id' => UrlHelper::getGet("id", null)));
        }

        if (!is_null(UrlHelper::getGet("submitSendToEbay", null))) {
            $this->ebayAction("send");
            return;
        }

        if (!is_null(UrlHelper::getGet("submitStop", null))) {
            $this->ebayAction("stop");
            return;
        }

        if (!is_null(UrlHelper::getGet("submitRelist", null))) {
            $this->ebayAction("relist");
            return;
        }

        if (!is_null(UrlHelper::getGet("submitRevise", null))) {
            $this->ebayAction("revise");
            return;
        }

        if (!is_null(UrlHelper::getGet("submitPriceQtyRevise", null))) {
            $this->ebayAction("revise", "qty_price");
            return;
        }

        if (!is_null(UrlHelper::getGet("submitRemove", null))) {
            $this->removeSellingProduct();
            return;
        }

        if (!is_null(UrlHelper::getGet("submitDuplicate", null))) {
            UrlHelper::redirect('selling/new', array('duplicateId' => UrlHelper::getGet("id", null)));
            return;
        }

        $sellingId = UrlHelper::getGet("id", null);
        if (is_null($sellingId)) {
            RenderHelper::addError(L::t("Selling Not Found"));
            UrlHelper::redirect("selling/index");
        }

        if (!is_null($moveToId = UrlHelper::getGet("moveTo", null)) && $moveToId > 0) {
            $this->moveToSellingList($sellingId, $moveToId);
        }

        // display second step form
        $grid = new Grids_SellingEdit($sellingId);
        $gridHtml = $grid->getHtml(false);
        $this->view("selling/edit/edit.phtml", array('gridHtml' => $gridHtml));
    }

    /**
     * Ajax action of get categories depending from choosen language
     */
    public function getCategoriesOptionsAjaxAction()
    {
        RenderHelper::cleanOutput();

        $languageId = UrlHelper::getPost("languageId", null);
        $categories = Category::getCategories((int) ($languageId), false);

        HtmlHelper::recurseCategory($categories, $categories[0][1], 1, UrlHelper::getPost("selectedId", 1));
        return;
    }

    /**
     * Show log grid connected to selected Selling
     */
    public function logAction()
    {
        $sellingId = UrlHelper::getGet("id", null);
        $sellingProductId = UrlHelper::getGet("productId", null);
        $grid = new Grids_LogSelling($sellingId, $sellingProductId);
        $grid->getHtml();
    }

    /**
     * Show full logs for all items in Selling Lists
     */
    public function itemsLogAction()
    {
        $grid = new Grids_LogSelling(null, null);
        $grid->getHtml();
    }

    public function appendAction()
    {
        $sellingId = UrlHelper::getGet("id", null);
        if (is_null($sellingId)) {
            RenderHelper::addError(L::t("Please select Selling ID."));
            UrlHelper::redirect("selling/index");
            return;
        }

        $sellingListModel = new Selling_ListModel($sellingId);

        $addSelected = UrlHelper::getGet("addProduct", null);
        $selectedProductsId = UrlHelper::getGet("productsBox", null);

        if (!is_null($addSelected) && $addSelected && is_array($selectedProductsId)) {
            // Append Products
            if (!is_null($sellingId)) {
                if ($sellingListModel->appendsProducts($selectedProductsId)) {
                    RenderHelper::addSuccess(L::t("Selling List Successfully Updated"));
                } else {
                    RenderHelper::addError(L::t("Can't update Selling List. DB Error."));
                }
                UrlHelper::redirect("selling/edit", array('id' => $sellingId));
            }

            return;
        }
        if (count($_POST) > 0 && !is_null($addSelected) && $addSelected) {
            RenderHelper::addError(L::t("Please Select at least One Product"));
        }

        // display second step form
        $grid = new Grids_ProductsAppend($sellingId, $sellingListModel->language);
        $grid->getHtml();
    }

    public function moveToSellingList($sellingFromId, $sellingMoveToId)
    {
        if (($idsList = UrlHelper::getGet("sellings_productsBox", array())) == array()) {
            RenderHelper::addError(L::t("Please Select at least One Product"));
            UrlHelper::redirect("selling/edit", array('id' => $sellingFromId));
            return;
        }

        if (!Selling_ListModel::moveSellingProducts($idsList, $sellingMoveToId)) {
            RenderHelper::addError(L::t("Failed move product to Selling List"));
            UrlHelper::redirect("selling/edit", array('id' => $sellingFromId));
            return;
        } else {
            RenderHelper::addSuccess(L::t("Items successfully moved to Selling List"));
            UrlHelper::redirect("selling/edit", array('id' => $sellingMoveToId));
            return;
        }

    }

    public function ebayAction($actionName, $isQuick = false)
    {
        $sellingId = UrlHelper::getGet("id", null);
        $idsList = UrlHelper::getGet("sellings_productsBox", array());
        if ($idsList == array()) {
            RenderHelper::addError(L::t("Please Select at least One Product"));
            UrlHelper::redirect("selling/edit", array('id' => $sellingId));
            return;
        }

        $productsInfo = Selling_ProductsModel::getFullProductInfo($idsList);
        $productsInfo = Selling_ProductsModel::getShortInfoByFull($productsInfo);

        $this->view("selling/edit/action.phtml", array(
            'sellingId' => $sellingId,
            'sellingProductIds' => $productsInfo,
            'action' => $actionName,
            'quick' => $isQuick
        ));
    }

    public function allAction($inputAction = null, $inputId = null)
    {
        $actionName = UrlHelper::getGet("action", $inputAction);
        $sellingId = UrlHelper::getGet("id", $inputId);
        if (is_null($actionName)) {
            RenderHelper::addError(L::t("Please choose correct action"));
            UrlHelper::redirect("selling/edit", array('id' => $sellingId));
            return;
        }
        if (is_null($sellingId)) {
            RenderHelper::addError(L::t("Please choose correct selling list"));
            UrlHelper::redirect("selling/index");
            return;
        }
        $sellingModel = new Selling_ListModel($sellingId);
        $productsInfo = $sellingModel->getAllProductsIdsPreparedForAction($actionName);

        if ($productsInfo == false) {
            RenderHelper::addError(L::t("No products prepared to specify action"));
            UrlHelper::redirect("selling/edit", array('id' => $sellingId));
            return;
        }

        $this->view("selling/edit/action.phtml", array(
            'sellingId' => $sellingId,
            'sellingProductIds' => $productsInfo,
            'action' => $actionName
        ));
    }

    /**
     * Send Item to eBay Ajax Action.
     *
     * @return json of result sending
     */
    public function sendAjaxAction()
    {
        RenderHelper::cleanOutput();

        $sellingId = UrlHelper::getPost("sellingId", null);
        $sellingProductId = UrlHelper::getPost("sellingProductId", null);

        echo json_encode(EbayListHelper::sendList($sellingId, $sellingProductId));
        return;
    }

    /**
     * Stop Item in eBay Ajax Action.
     *
     * @return json of result sending
     */
    public function stopAjaxAction()
    {
        RenderHelper::cleanOutput();

        $sellingId = UrlHelper::getPost("sellingId", null);
        $sellingProductId = UrlHelper::getPost("sellingProductId", null);

        if (is_null($sellingId) || $sellingId == 0) {
            // When selling id not specify get it from product information
            $sellingProductModel = new Selling_ProductsModel($sellingProductId);
            $sellingId = $sellingProductModel->selling_id;
        }

        $result = EbayListHelper::stopList($sellingId, $sellingProductId, false);
        if ($result['success']) {
            // On success eBay stop change status to stop manualy. To avoid relist
            $sellingProductModel = new Selling_ProductsModel($sellingProductId);
            $sellingProductModel->status = Selling_ProductsModel::STATUS_STOPED;
            $sellingProductModel->save();
        }

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

        $sellingId = UrlHelper::getPost("sellingId", null);
        $sellingProductId = UrlHelper::getPost("sellingProductId", null);

        if (is_null($sellingId) || $sellingId == 0) {
            // When selling id not specify get it from product information
            $sellingProductModel = new Selling_ProductsModel($sellingProductId);
            $sellingId = $sellingProductModel->selling_id;
        }
        echo json_encode(EbayListHelper::relistList($sellingId, $sellingProductId, false));
        return;
    }

    /**
     * Send Item to eBay Ajax Action.
     *
     * @return json of result sending
     */
    public function reviseAjaxAction()
    {
        RenderHelper::cleanOutput();

        $sellingId = UrlHelper::getPost("sellingId", null);
        $sellingProductId = UrlHelper::getPost("sellingProductId", null);
        $isQuickMode = UrlHelper::getPost('isQuick', false);

        $listMode = EbayListHelper::MODE_FULL;
        $isQuickMode == "qty" && $listMode = EbayListHelper::MODE_QTY;
        $isQuickMode == "price" && $listMode = EbayListHelper::MODE_PRICE;
        $isQuickMode == "qty_price" && $listMode = EbayListHelper::MODE_QTY_PRICE;

        if (is_null($sellingId) || $sellingId == 0) {
            // When selling id not specify get it from product information
            $sellingProductModel = new Selling_ProductsModel($sellingProductId);
            $sellingId = $sellingProductModel->selling_id;
        }

        if (is_null($sellingId)) {
            // when still not found selling, return error and skip this product
            return array(
                'success' => false,
                'warnings' => "",
                'errors' => L::t("Selling Not Found"),
                'item' => array()
            );
        }

        echo json_encode(EbayListHelper::reviseList($sellingId, $sellingProductId, $listMode));
        return;
    }

    public function removeSellingProduct()
    {
        $sellingId = UrlHelper::getGet("id", null);
        $idsList = UrlHelper::getGet("sellings_productsBox", array());
        if ($idsList == array()) {
            RenderHelper::addError(L::t("Please Select at least One Product"));
            UrlHelper::redirect("selling/edit", array('id' => $sellingId));
            return;
        }

        foreach ($idsList as $id) {
            $model = new Selling_ProductsModel($id);
            if ($model->status == Selling_ProductsModel::STATUS_ACTIVE) {
                RenderHelper::addWarning(sprintf(L::t("Product '%s' still active on ebay"), $model->product_name));
            }
            Selling_ProductsModel::deleteSellingProductById($id);
            RenderHelper::addSuccess(sprintf(L::t("Product '%s' was successfully deleted from 'Selling List'"), $model->product_name));
        }

        UrlHelper::redirect("selling/edit", array('id' => $sellingId));
        return;
    }
}

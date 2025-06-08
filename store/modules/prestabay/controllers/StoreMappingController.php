<?php

/**
 * File StoreMappingController.php
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
class StoreMappingController extends BaseAdminController
{

    public function indexAction()
    {
        $myGrid = new Grids_StoreMapping();
        $myGrid->getHtml();
    }

    public function newAction()
    {
        $this->editAction();
    }

    public function editAction()
    {
        $id          = UrlHelper::getGet("id", 0);
        $isEdit      = false;
        $mappingList = array();
        if ($id > 0) {
            $model            = new Mapping_EbayStoreModel($id);
            $mappingListModel = new Mapping_EbayStoreCategoriesModel();
            $mappingList      = $mappingListModel->getMappingList($model->id);
            $isEdit           = true;
        } else {
            $model = new Mapping_EbayStoreModel();
        }
        $languageId = (int) (Configuration::get('PS_LANG_DEFAULT'));
        $categories = Category::getCategories((int) ($languageId), false);

        $accountsModel = new AccountsModel();
        $this->view(
            "storeMapping/edit.phtml",
            array(
                'model'       => $model,
                'mappingList' => $mappingList,
                'accountList' => $accountsModel->getSelect()->getItems(),
                'isEdit'      => $isEdit,
                'categories' => $categories
            )
        );
    }

    public function saveAction()
    {
        $id = UrlHelper::getPost("storeMappingId", null);
        $mappingInformation = json_decode(UrlHelper::getPost("mappingInformation", "[]"), true);

        $mappingName = UrlHelper::getPost("name", "");
        $mappingAccount = UrlHelper::getPost("account_id", 0);


        if (count($mappingInformation) > 0) {
            // Save mapping
            $mappingModel = new Mapping_EbayStoreModel($id);
            $mappingModel->name = $mappingName;
            $mappingModel->account_id = $mappingAccount;
            $mappingModel->save();
            $id = $mappingModel->id;
            $mappingModel->removeAllMappingCategories();
            $mappingCategoryModel = new Mapping_EbayStoreCategoriesModel();
            foreach ($mappingInformation as $lineMapping) {
                $mappingCategoryModel->addMappingLine($mappingModel->id, $lineMapping);
            }
        } else {
            RenderHelper::addError(L::t("Please add at least one mapping"));
            UrlHelper::redirect("storeMapping/edit", array('id' => (int) $id));
            return;
        }

        RenderHelper::addSuccess(L::t("Mapping successfully saved"));

        if (UrlHelper::getPost("save-and-continue", false) === "") {
            UrlHelper::redirect("storeMapping/edit", array('id' => (int) $id));
        } else {
            UrlHelper::redirect("storeMapping/index");
        }
    }

    public function deleteAction()
    {
        $id = UrlHelper::getGet("id", null);
        if (is_null($id)) {
            RenderHelper::addError(L::t("Please select correct Store Mapping"));
            UrlHelper::redirect("storeMapping/index");
            return;
        }
        $model = new Mapping_EbayStoreModel($id);
        $model->remove();

        RenderHelper::addSuccess(L::t("Store Mapping successfully removed"));
        UrlHelper::redirect("storeMapping/index");
        return;
    }

}
<?php

/**
 * File CategoryMappingController.php
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
class CategoryMappingController extends BaseAdminController
{

    public function indexAction()
    {
        $myGrid = new Grids_CategoryMapping();
        $myGrid->getHtml();
    }

    public function newAction()
    {
        $this->editAction();
    }

    public function editAction()
    {
        $id     = UrlHelper::getGet("id", 0);
        $isEdit = false;
        if ($id > 0) {
            $model = new Mapping_CategoryModel($id);
            if (!$model->id) {
                RenderHelper::addError(L::t("Category Mapping not found"));
                UrlHelper::redirect("categoryMapping/index");
                return;
            }
            $isEdit = true;
        } else {
            $model = new Mapping_CategoryModel();
        }

        $this->view(
            "categoryMapping/edit.phtml",
            array(
                'model'  => $model,
                'isEdit' => $isEdit,
                'id'     => $id
            )
        );
    }

    public function saveAction()
    {
        RenderHelper::cleanOutput();

        $id = UrlHelper::getPost("id", null);
        $mapping = UrlHelper::getPost("mapping", array());
        $redirect = UrlHelper::getPost("redirect", false);

        $mappingName = UrlHelper::getPost("name", "");
        $mappingMarketplace = UrlHelper::getPost("marketplace", 0);

        if (count($mapping) > 0) {
            // Save mapping
            $mappingModel = new Mapping_CategoryModel($id);
            $mappingModel->name = $mappingName;
            $mappingModel->marketplace_id = $mappingMarketplace;
            $mappingModel->save();

            $id = $mappingModel->id;
            $mappingModel->removeAllMappingCategories();

            foreach ($mapping as $lineMapping) {
                Mapping_CategoryLineModel::addMappingLine($id, $lineMapping);
            }
        } else {
            echo json_encode(array('success' => false, 'message' => L::t("Please add at least one mapping")));
            return;
        }

        $successMessage =  L::t("Mapping successfully saved");
        if ($redirect) {
            RenderHelper::addSuccess($successMessage);
        }

        echo json_encode(array('success' => true, 'message' => $successMessage));
    }

    public function infoAction()
    {
        RenderHelper::cleanOutput();

        $id = UrlHelper::getGet('id');
        $model = new Mapping_CategoryModel($id);

        $lines = Mapping_CategoryLineModel::getMappingLines($id);

        $mapping = Mapping_CategoryCategoriesModel::loadCategoriesForLines($lines);

        $languageId = (int) (Configuration::get('PS_LANG_DEFAULT'));
        $categories = Category::getCategories((int) ($languageId), false);

        echo json_encode(array(
            'name' => $model->name,
            'marketplace' => $model->marketplace_id,
            'mapping' => $mapping,
            'categories' => HtmlHelper::recurseCategoryArray($categories, $categories[0][1], 1)
        ));

        return;
    }


    public function deleteAction()
    {
        $id = UrlHelper::getGet("id", null);
        if (is_null($id)) {
            RenderHelper::addError(L::t("Please select correct Category Mapping"));
            UrlHelper::redirect("categoryMapping/index");
            return;
        }
        $model = new Mapping_CategoryModel($id);
        $model->remove();

        RenderHelper::addSuccess(L::t("Category Mapping successfully removed"));
        UrlHelper::redirect("categoryMapping/index");
        return;
    }


}
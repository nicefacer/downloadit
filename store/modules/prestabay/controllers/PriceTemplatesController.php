<?php

/**
 * File PriceTemplatesController.php
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
class PriceTemplatesController extends BaseAdminController
{

    public function indexAction()
    {
        $myGrid = new Grids_PriceTemplates();
        $myGrid->getHtml();
    }

    public function newAction()
    {
        // Forward to edit action
        $this->editAction();
    }

    public function editAction()
    {
        $id = UrlHelper::getGet("id", 0);
        $isEdit = false;
        $conditionList = array();
        if ($id > 0) {
            $model = new Price_TemplateModel($id);
            $conditionsModel = new Price_ConditionsModel();
            $conditionList = $conditionsModel->getList($id);
            $isEdit = true;
        } else {
            $model = new Price_TemplateModel();
        }
        $this->view("priceTemplate/edit.phtml", array('model' => $model, 'conditions' => $conditionList, 'isEdit' => $isEdit));
    }

    public function saveAction()
    {
        $id = UrlHelper::getPost("priceTemplateId", null);
        $templateName = UrlHelper::getPost("name", 0);
        $conditionsCount = UrlHelper::getPost("conditionsCount", 0);
        $conditionsList = array();

        $sourceList = UrlHelper::getPost("source", array());
        $priceTypeList = UrlHelper::getPost("price_type", array());
        $priceFromList = UrlHelper::getPost("price_from", array());
        $priceToList = UrlHelper::getPost("price_to", array());
        $priceSourceList = UrlHelper::getPost("price_source", array());
        $priceCustomValueList = UrlHelper::getPost("price_custom_value", array());
        $priceRatioList = UrlHelper::getPost("price_ratio", array());

        for ($i = 1; $i <= $conditionsCount; $i++) {
            if (!isset($priceTypeList[$i])) {
                continue;
            }
            $conditionsList[] = array(
                'source' => $sourceList[$i],
                'type' => $priceTypeList[$i],
                'price_from' => isset($priceFromList[$i]) ? $priceFromList[$i] : 0,
                'price_to' => isset($priceToList[$i]) ? $priceToList[$i] : 0,
                'price_source' => isset($priceSourceList[$i]) ? $priceSourceList[$i] : Price_ConditionsModel::CONDITION_SOURCE_PRODUCT,
                'price_custom_value' => isset($priceCustomValueList[$i]) ? $priceCustomValueList[$i] : 0,
                'price_ratio' => isset($priceRatioList[$i]) ? $priceRatioList[$i] : 'x1',
            );
        }

        if (count($conditionsList) > 0) {
            // Save template
            $templateModel = new Price_TemplateModel($id);
            $templateModel->name = $templateName;
            $templateModel->save();
            $templateModel->removeAllConditions();
            $id = $templateModel->id;
            $conditionModel = new Price_ConditionsModel();
            foreach ($conditionsList as $condition) {
                $conditionModel->addCondition($id, $condition);
            }
        } else {
            RenderHelper::addError(L::t("Please add at least one conditions"));
            UrlHelper::redirect("priceTemplates/edit", array('id' => (int)$id));
            return;
        }

        RenderHelper::addSuccess(L::t("Price Template successfully saved"));

        if (UrlHelper::getPost("priceTemplateId", false)) {
            UrlHelper::redirect("priceTemplates/edit", array('id' => (int)$id));
        } else {
            UrlHelper::redirect("priceTemplates/index");
        }
    }

    public function deleteAction()
    {
        $id = UrlHelper::getGet("id", null);
        if (is_null($id)) {
            RenderHelper::addError(L::t("Please select correct Price Template"));
            UrlHelper::redirect("priceTemplates/index");
            return;
        }
        $templateModel = new Price_TemplateModel($id);
        $templateModel->remove();
        RenderHelper::addSuccess(L::t("Price Template successfully removed"));
        UrlHelper::redirect("priceTemplates/index");
        return;
    }

}

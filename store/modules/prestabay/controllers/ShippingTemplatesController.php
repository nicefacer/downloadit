<?php
/**
 * File ShippingTemplatesController.php
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

class ShippingTemplatesController extends BaseAdminController
{

    public function indexAction()
    {
        $myGrid = new Grids_ShippingTemplates();
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
            $model = new Shipping_TemplateModel($id);
            $conditionsModel = new Shipping_ConditionsModel();
            $conditionList = $conditionsModel->getList($id);
            $isEdit = true;
        } else {
            $model = new Shipping_TemplateModel();
        }
        $this->view("shippingTemplate/edit.phtml", array('model' => $model, 'conditions' => $conditionList, 'isEdit' => $isEdit));
    }

    public function saveAction()
    {
        $id = UrlHelper::getPost("shippingTemplateId", null);
        $templateName = UrlHelper::getPost("name", "");
        $templateMode = UrlHelper::getPost("mode", 0);
        $notInRange = (int)UrlHelper::getPost("remove_not_in_range", 0);

        $conditionsCount = UrlHelper::getPost("conditionsCount", 0);
        $conditionsList = array();

        $valueFromList = UrlHelper::getPost("value_from", array());
        $valueToList = UrlHelper::getPost("value_to", array());

        $plainList = UrlHelper::getPost("plain", array());
        $additionalList = UrlHelper::getPost("additional", array());


        for ($i = 1; $i <= $conditionsCount; $i++) {
            if (!isset($valueFromList[$i]) || !isset($valueToList[$i]) || !isset($plainList[$i]) || !isset($additionalList[$i])) {
                continue;
            }
            $conditionsList[] = array(
                'value_from' => isset($valueFromList[$i]) ? $valueFromList[$i] : 0,
                'value_to' => isset($valueToList[$i]) ? $valueToList[$i] : 999,
                'plain' => isset($plainList[$i]) ? $plainList[$i] : 0,
                'additional' => isset($additionalList[$i]) ? $additionalList[$i] : 0,
            );
        }

        if (count($conditionsList) > 0) {
            $conditionsCount = count($conditionsList);
            // Save template
            $templateModel = new Shipping_TemplateModel($id);
            $templateModel->name = $templateName;
            $templateModel->mode = $templateMode;
            $templateModel->remove_not_in_range = $notInRange;
            $templateModel->save();
            $templateModel->removeAllConditions();
            $id = $templateModel->id;
            $conditionModel = new Shipping_ConditionsModel();
            foreach ($conditionsList as $condition) {
                $conditionModel->addCondition($id, $condition);
            }
        } else {
            RenderHelper::addError(L::t("Please add at least one conditions"));
            UrlHelper::redirect("shippingTemplates/edit", array('id' => (int) $id));
            return;
        }

        RenderHelper::addSuccess(L::t("Shipping Template successfully saved"));

        if (UrlHelper::getPost("save-and-continue", false) === "") {
            UrlHelper::redirect("shippingTemplates/edit", array('id' => (int) $id));
        } else {
            UrlHelper::redirect("shippingTemplates/index");
        }
    }

    public function deleteAction()
    {
        $id = UrlHelper::getGet("id", null);
        if (is_null($id)) {
            RenderHelper::addError(L::t("Please select correct Shipping Template"));
            UrlHelper::redirect("shippingTemplates/index");
            return;
        }
        $templateModel = new Shipping_TemplateModel($id);
        $templateModel->remove();
        RenderHelper::addSuccess(L::t("Shipping Template successfully removed"));
        UrlHelper::redirect("shippingTemplates/index");
        return;
    }

}

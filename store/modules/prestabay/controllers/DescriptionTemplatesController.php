<?php

/**
 * File DescriptionTemplatesController.php
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
class DescriptionTemplatesController extends BaseAdminController
{

    public function indexAction()
    {
        $myGrid = new Grids_DescriptionTemplates();
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
            $model = new Description_TemplateModel($id);
            $isEdit = true;
        } else {
            $model = new Description_TemplateModel();
        }
        $this->view("descriptionTemplate/edit.phtml", array('model' => $model, 'isEdit' => $isEdit));
    }

    public function saveAction()
    {
        $id = UrlHelper::getPost("descriptionTemplateId", null);
        $templateName = UrlHelper::getPost("name", "");
        $templateContent = UrlHelper::getPost("template", "");
        if ($templateName == "") {
            RenderHelper::addError(L::t("Please enter correct 'Name'"));
            UrlHelper::redirect("descriptionTemplates/edit", array('id' => (int) $id));
            return;
        }

        if ($templateContent == "") {
            RenderHelper::addError(L::t("Please enter correct 'Description'"));
            UrlHelper::redirect("descriptionTemplates/edit", array('id' => (int) $id));
            return;
        }

        // Save template
        $templateModel = new Description_TemplateModel($id);
        $templateModel->name = $templateName;
        $templateModel->template = $templateContent;
        $templateModel->save();

        $id = $templateModel->id;

        RenderHelper::addSuccess(L::t("Description Template successfully saved"));

        if (UrlHelper::getPost("save-and-continue", false) === "") {
            UrlHelper::redirect("descriptionTemplates/edit", array('id' => (int) $id));
        } else {
            UrlHelper::redirect("descriptionTemplates/index");
        }
    }

    public function previewAction()
    {
        $templateId = UrlHelper::getGet('id', null);
        if (!$templateId) {
            RenderHelper::cleanOutput();
            echo L::t('Not possible to generate preview');
            return;
        }

        $productId = UrlHelper::getPost('productId', null);
        $productId == "" && $productId = false;
        if (UrlHelper::getPost("button-random", false) === "" || !$productId) {
            // Get random product ID
            $productId = Description_TemplateModel::generateRandomProductId();
        }

        $languageId = UrlHelper::getPost('languageId', (int) (Configuration::get('PS_LANG_DEFAULT')));
        
        $profileModel = new ProfilesModel();
        $items = $profileModel->getSelect()->getItems();
        if (is_array($items)) {
            $defaultProfileId = reset($items);
        } else {
            $defaultProfileId = array();
        }
        if (isset($defaultProfileId['id'])) {
            $defaultProfileId = (int)$defaultProfileId['id'];
        } else {
            $defaultProfileId = false;
        }
        $profileId = UrlHelper::getPost('profileId', $defaultProfileId);
        $generatedContent = '';
        RenderHelper::cleanOutput();
        if ($productId && $profileId) {
            $templateModel = new Description_TemplateModel($templateId);

            $selectedProfile = new ProfilesModel($profileId);
            $profileProduct = new ProfileProductModel(null, null);
            $profileProduct->setLangId($languageId);
            $profileProduct->setProduct($productId);
            $profileProduct->setProfile($selectedProfile);
                    
            $generatedContent = ReplaceHelper::parseAttributes($templateModel->template, $profileProduct);
        } else {
            echo "<h2>".L::t("Please create Selling Profile for use preview")."</h2>";
        }
        
        
        header('Content-Type: text/html; charset=utf-8');
        $this->view("descriptionTemplate/preview.phtml", array(
            'templateId' => $templateId,
            'productId' => $productId,
            'generatedContent' => $generatedContent,
            'languageList' => Language::getLanguages(true),
            'languageId' => $languageId,
            'profileId' => $profileId,
        ));
        
    }
    public function deleteAction()
    {
        $id = UrlHelper::getGet("id", null);
        if (is_null($id)) {
            RenderHelper::addError(L::t("Please select correct Description Template"));
            UrlHelper::redirect("descriptionTemplates/index");
            return;
        }
        $templateModel = new Description_TemplateModel($id);
        $templateModel->remove();
        RenderHelper::addSuccess(L::t("Description Template successfully removed"));
        UrlHelper::redirect("descriptionTemplates/index");
        return;        
    }



}

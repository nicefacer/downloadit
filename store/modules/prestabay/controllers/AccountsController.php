<?php

/**
 * File AccountsController.php
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
class AccountsController extends BaseAdminController
{

    /**
     * Show grid with all assigned accounts
     */
    public function indexAction()
    {
        $myGrid = new Grids_Accounts();
        $myGrid->getHtml();
    }

    /**
     * Process form information and send user to eBay
     */
    public function beforeGetTokenAction()
    {
        // Retrvie form information, send to eBay
        $_SESSION['form_account_id'] = (int) UrlHelper::getPost("id", 0); // existing account id
        $_SESSION['form_account_name'] = UrlHelper::getPost("name", "");
        $_SESSION['form_account_mode'] = $accountMode = (int) UrlHelper::getPost("mode", 0); // 0 - sandbox, 1 - production

        ApiModel::getInstance()->reset();
        $redirectUrl = ApiModel::getInstance()->ebay->account
                        ->tokenSession(array(
                            'name' => $_SESSION['form_account_name'],
                            'mode' => $_SESSION['form_account_mode'], // 0 - sandbox, 1 - production
                            'url' => base64_encode(UrlHelper::getUrl('accounts/afterGetToken'))
                        ))->post();

        if ($redirectUrl) {
            UrlHelper::redirectExternal($redirectUrl['result']);
        } else {
            RenderHelper::addError(ApiModel::getInstance()->getErrorsAsHtml());
            UrlHelper::redirect("accounts/edit", array('id' => $_SESSION['form_account_id']));
        }
    }

    /**
     * Redirect from eBay to form 
     */
    public function afterGetTokenAction()
    {
        // Get ebay session id
        //-------------------------------
        if (isset($_GET['skey'])) {
            $sKey = $_GET['skey'];

            ApiModel::getInstance()->reset();
            $result = ApiModel::getInstance()->ebay->account->tokenValue(array('sKey' => $sKey))->post();
        } else {
            RenderHelper::addError(L::t("Can't get account information"));
            UrlHelper::redirect("accounts/index");
            return;
        }

        if ($result == false || !isset($_SESSION['form_account_id']) || !isset($_SESSION['form_account_name']) || !isset($_SESSION['form_account_mode'])) {
            RenderHelper::addError(L::t("Can't get account information"));
            UrlHelper::redirect("accounts/index");
            return;
        }

        $responseArray['id'] = $_SESSION['form_account_id'];
        $responseArray['name'] = $_SESSION['form_account_name'];
        $responseArray['mode'] = $_SESSION['form_account_mode'];
        $responseArray['token'] = $result['token'];
        $responseArray['exp_date'] = $result['expired'];

        unset($_SESSION['form_account_id']);
        unset($_SESSION['form_account_name']);
        unset($_SESSION['form_account_mode']);

        $accountModel = new AccountsModel();
        $accountModel->setData($responseArray);
        $accountModel->save(); // update or add new

        RenderHelper::addSuccess(L::t("Account Successfully Saved"));

        UrlHelper::redirect("accounts/edit", array('id' => $accountModel->id));
    }

    /**
     * Show form to assign new account
     */
    public function newAction()
    {
        $this->editAction();
    }

    /**
     * Edit assign account details
     * Also used for create new
     */
    public function editAction()
    {
        // Get Request
        // Check ID, and try to load model
        // When success load set edit flag
        // Send model to form
        $isEdit = false;

        $id = UrlHelper::getGet("id", 0);
        if ($id > 0) {
            $model = new AccountsModel($id);
            if ($model->id) {
                $isEdit = true;
            }
        } else {
            $model = new AccountsModel();
        }


        $this->view("accounts/edit.phtml", array("model" => $model, "isEdit" => $isEdit));
    }

    public function saveAction()
    {
        $id = UrlHelper::getPost("id", 0);

        $data = array(
            'name' => UrlHelper::getPost("name", ""),
            'mode' => (int) UrlHelper::getPost("mode", 0)
        );

        if ($id > 0) {
            $model = new AccountsModel($id);
        } else {
            $model = new AccountsModel();
        }

        $model->setData($data);

        // Important we dom't save token

        if (!$model->save()) {
            RenderHelper::addError(L::t("Can't save account information"));
        } else {
            RenderHelper::addSuccess(L::t("Account information success saved"));
        }

        UrlHelper::redirect("accounts/index");
    }

    public function deleteAction()
    {
        $id = UrlHelper::getGet("id", 0);

        if ($id > 0) {
            $model = new AccountsModel($id);
            if (!$model->delete()) {
                RenderHelper::addError(L::t("Can't delete selected account"));
            } else {
                RenderHelper::addSuccess(L::t("Account Successfully Deleted"));
            }
        } else {
            RenderHelper::addError(L::t("Can't find account"));
        }

        UrlHelper::redirect("accounts/index");
    }

}

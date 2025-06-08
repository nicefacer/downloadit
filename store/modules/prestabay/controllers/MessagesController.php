<?php

/**
 * File MessagesController.php
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
class MessagesController extends BaseAdminController
{
    /**
     * List of user feedback
     */
    public function indexAction()
    {
        // view order list
        $grid = new Grids_Messages();

        $selectedAccountId = UrlHelper::getGet("account_select", false);

        if ($selectedAccountId) {
            $grid->setAccountFilter($selectedAccountId);
            $grid->init();
        }
        $gridHtml = $grid->getHtml(false);

        $accountsModel = new AccountsModel();
        $this->view(
            'messages/index.phtml',
            array(
                'grid'              => $gridHtml,
                'accountsList'      => $accountsModel->getSelect()->getItems(),
                'selectedAccountId' => $selectedAccountId
            )
        );

    }

    /**
     * Show modal to leave response
     */
    public function responseModalAction()
    {
        $id = UrlHelper::getGet('rowid', false);
        $message = new Messages_MessagesModel($id);

        $this->view('messages/responseModal.phtml', array(
                'message' => $message
            ));
    }

    /**
     * @return json response
     */
    public function writeResponseAjaxAction()
    {
        RenderHelper::cleanOutput();
        $request = UrlHelper::getPost();
        if (!isset($request['id']) || !isset($request['message'])) {
            json_encode(array(
                    'success' => false,
                    'message' => 'Invalid Request'
                ));
            return;
        }

        echo json_encode(MessageHelper::writeResponse($request['id'], $request['message'], array()));
    }
}
<?php

/**
 * File FeedbackController.php
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
class FeedbackController extends BaseAdminController
{
    /**
     * List of user feedback
     */
    public function indexAction()
    {
        // view order list
        $grid = new Grids_Feedbacks();

        $selectedAccountId = UrlHelper::getGet("account_select", false);

        if ($selectedAccountId) {
            $grid->setAccountFilter($selectedAccountId);
            $grid->init();
        }
        $gridHtml = $grid->getHtml(false);

        $accountsModel = new AccountsModel();
        $this->view(
            'feedback/index.phtml',
            array(
                'grid'              => $gridHtml,
                'accountsList'      => $accountsModel->getSelect()->getItems(),
                'selectedAccountId' => $selectedAccountId
            )
        );

    }

    /**
     * Show modal to leave feedback
     */
    public function leaveModalAction()
    {
        $id = UrlHelper::getGet('rowid', false);
        $feedback = new Feedbacks_FeedbacksModel($id);

        $this->view('feedback/leaveModal.phtml', array(
                'feedback' => $feedback
            ));
    }

    /**
     * Send request to ebay with new feedback
     */
    public function sendFeedbackAjaxAction()
    {
        RenderHelper::cleanOutput();
        $request = UrlHelper::getPost();
        if (!isset($request['id']) || !isset($request['message']) || !isset($request['type'])) {
            json_encode(array(
                    'success' => false,
                    'message' => 'Invalid Request'
                ));
            return false;
        }

        echo json_encode(FeedbackHelper::leaveFeedback($request['id'], $request['type'], $request['message']));

    }

    /**
     * List layout show
     */
    public function templatesAction()
    {
        $this->view("feedback/templates.phtml", array());
    }

    /**
     * Get list of all available template using Ajax
     */
    public function templateListAjaxAction()
    {
        RenderHelper::cleanOutput();
        echo json_encode(array(
            'positive' => Feedbacks_TemplatesModel::getTemplatesList(Feedbacks_FeedbacksModel::TYPE_POSITIVE),
            'neutral' => Feedbacks_TemplatesModel::getTemplatesList(Feedbacks_FeedbacksModel::TYPE_NEUTRAL),
        ));
    }

    /**
     * Add new template
     *
     * @return bool
     */
    public function saveTemplateAjaxAction()
    {
        RenderHelper::cleanOutput();
        $request = UrlHelper::getPost();

        if (!isset($request['feedback_type']) || !isset($request['message'])) {
            echo json_encode(array('success' => false));
            return false;
        }

        $feedbackModel = new Feedbacks_TemplatesModel();
        $feedbackModel->setData(array(
                'feedback_type' => $request['feedback_type'],
                'message' => $request['message']
            ));
        $feedbackModel->save();

        echo json_encode(array('success' => true, 'id' => $feedbackModel->id));
    }

    /**
     * Remove template using ajax
     */
    public function removeTemplateAjaxAction()
    {
        RenderHelper::cleanOutput();
        $request = UrlHelper::getPost();

        $id = isset($request['id'])?$request['id']:false;
        if ($id > 0) {
            $model = new Feedbacks_TemplatesModel($id);
            if (!$model->delete()) {
                echo json_encode(array('success' => false, 'message' => L::t("Can't delete selected template")));
            } else {
                echo json_encode(array('success' => true));
            }
        } else {
            echo json_encode(array('success' => false, 'message' => L::t("Can't find template")));
        }
    }

}
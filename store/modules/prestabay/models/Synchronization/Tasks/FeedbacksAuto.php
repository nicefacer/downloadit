<?php

/**
 * File FeedbacksAuto.php
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
class Synchronization_Tasks_FeedbacksAuto extends Synchronization_BaseTask
{

    protected $_syncType = Log_SyncModel::LOG_TASK_FEEDBACKS_AUTO;

    protected function _execute()
    {
        $templatesList = Feedbacks_TemplatesModel::getTemplatesList(Feedbacks_FeedbacksModel::TYPE_POSITIVE);
        if (count($templatesList) == 0) {
            $this->_appendError("Feedback Templates not found. Please add at least one.");
            return false;
        }

        $autoFeedbackId = (int)Configuration::get('INVEBAY_SYNC_FEEDBACK_AUTO_ID');
        $feedbacksLeft = 0;

        $feedbackList = Feedbacks_FeedbacksModel::getAllResponsibleFeedbacksMoreId($autoFeedbackId);
        foreach ($feedbackList as $feedbackRow) {
            $randomTemplateKey = array_rand($templatesList);
            $result = FeedbackHelper::leaveFeedback($feedbackRow['id'], Feedbacks_FeedbacksModel::TYPE_POSITIVE, $templatesList[$randomTemplateKey]['message']);

            if ($result['success'] == false && isset($result['message'])) {
                $this->_appendError($result['message']);
            } else if ($result['success'] == true) {
                $feedbacksLeft++;
            }
        }
        if ($feedbacksLeft > 0) {
            $this->_appendSucces(sprintf(L::t("%s feedbacks has been left"), $feedbacksLeft));
        }

        Configuration::updateValue('INVEBAY_SYNC_FEEDBACK_AUTO_ID', Feedbacks_FeedbacksModel::getMaxId());

    }

}

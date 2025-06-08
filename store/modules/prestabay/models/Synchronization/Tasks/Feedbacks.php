<?php

/**
 * File Order.php
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
class Synchronization_Tasks_Feedbacks extends Synchronization_BaseTask
{

    protected $_syncType = Log_SyncModel::LOG_TASK_FEEDBACKS;

    protected $_totalImport = 0;

    protected function _execute()
    {
        $accountsModel = new AccountsModel();
        foreach ($accountsModel->getSelect()->getItems() as $account) {
            $buyerDate = Feedbacks_FeedbacksModel::getBuyerMaxTime($account['id']);
            $sellerDate = Feedbacks_FeedbacksModel::getSellerMaxTime($account['id']);

            if (strtotime($sellerDate) < strtotime('2001-01-02')) {
                $sellerDate = false;
            }
            if (strtotime($buyerDate) < strtotime('2001-01-02')) {
                $buyerDate = false;
            }

            ApiModel::getInstance()->reset();
            $result = ApiModel::getInstance()->ebay->feedback->getList(
                array(
                    'token'         => $account['token'],
                    'transactionId' => false,
                    'itemId'        => false,
                    'sellerDate'    => $sellerDate,
                    'buyerDate'     => $buyerDate,
                )
            )->post();

            if (count(ApiModel::getInstance()->getWarnings()) > 0) {
                $this->_appendWarning(ApiModel::getInstance()->getWarningsAsHtml(), array('ebay_account_id' => $account['id']));
            }

            if (count(ApiModel::getInstance()->getErrors()) > 0 || $result == false || !isset($result['feedbacks'])) {
                if (count(ApiModel::getInstance()->getErrors()) > 0) {
                    $this->_appendError(ApiModel::getInstance()->getErrorsAsHtml(), array('ebay_account_id' => $account['id']));
                    $this->_hasErrors = true;
                }
                continue;
            }

            $this->_processAccountFeedbacks($result['feedbacks'], $account['id']);
        }

        if ($this->_totalImport > 0) {
            $this->_appendSucces(sprintf(L::t('%s feedbacks have been imported/updated into PrestaBay'), $this->_totalImport));
        }

    }

    /**
     * @param $feedbacksList
     * @param $accountId
     */
    protected function _processAccountFeedbacks($feedbacksList, $accountId)
    {
        foreach ($feedbacksList as $feedback) {
            $this->_totalImport += Feedbacks_FeedbacksModel::importUpdateFeedbackRow($feedback, $accountId);
        }
    }

}

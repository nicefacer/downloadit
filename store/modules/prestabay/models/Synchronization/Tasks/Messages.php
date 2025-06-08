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
class Synchronization_Tasks_Messages extends Synchronization_BaseTask
{

    protected $_syncType = Log_SyncModel::LOG_TASK_MESSAGES;

    protected $_totalImport = 0;

    protected function _execute()
    {
        $accountsModel = new AccountsModel();
        foreach ($accountsModel->getSelect()->getItems() as $account) {
            $dateFrom = Messages_MessagesModel::getMaxMessageDate($account['id']);

            if (strtotime($dateFrom) < strtotime('2001-01-02')) {
                $dateFrom = false;
            }

            ApiModel::getInstance()->reset();
            $result = ApiModel::getInstance()->ebay->messages->getList(
                array(
                    'token'         => $account['token'],
                    'startFromTime' => $dateFrom
                )
            )->post();

            if (count(ApiModel::getInstance()->getWarnings()) > 0) {
                $this->_appendWarning(ApiModel::getInstance()->getWarningsAsHtml(), array('ebay_account_id' => $account['id']));
            }

            if (count(ApiModel::getInstance()->getErrors()) > 0 || $result == false) {
                if (count(ApiModel::getInstance()->getErrors()) > 0) {
                    $this->_appendError(ApiModel::getInstance()->getErrorsAsHtml(), array('ebay_account_id' => $account['id']));
                    $this->_hasErrors = true;
                }
                continue;
            }
            if (isset($result['messages']))
            $this->_processAccountMessages($result['messages'], $account['id']);
        }

        if (count($result['messages']) > 0) {
            $this->_appendSucces(sprintf(L::t('%s messages have been imported, %s updated into PrestaBay'), $this->_totalImport, count($result['messages']) - $this->_totalImport));
        }

    }

    /**
     * @param $messagesList
     * @param $accountId
     */
    protected function _processAccountMessages($messagesList, $accountId)
    {
        foreach ($messagesList as $message) {
            $this->_totalImport += Messages_MessagesModel::importUpdateMessageRow($message, $accountId);
        }
    }

}

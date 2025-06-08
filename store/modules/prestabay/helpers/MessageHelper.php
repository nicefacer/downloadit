<?php

/**
 * File MessageHelper.php
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
class MessageHelper
{

    /**
     *
     * @param $id
     * @param $message
     * @param $options
     *
     * @return bool
     */
    public static function writeResponse($id, $message, $options)
    {
        $messageModel = new Messages_MessagesModel($id);

        $requestDataArray = array(
            'parentMessageId' => (string)$messageModel->message_id,
            'message' => $message,
            'destination' => array(
                'recipient' => $messageModel->sender,
            ),
            'options' => array(
                'cc_to_sender' => isset($options['cc_to_sender'])?(bool)$options['cc_to_sender']:false,
                'public_display' => isset($options['public_display'])?(bool)$options['cc_to_sender']:false,
            )
        );

        $accountModel = new AccountsModel($messageModel->account_id);
        if (!is_null($accountModel->id)) {
            $requestDataArray['token'] = $accountModel->token;
        }

        ApiModel::getInstance()->reset();
        $writeMessageResult = ApiModel::getInstance()->ebay->messages->response($requestDataArray)->post();

        if (isset($writeMessageResult['success']) && $writeMessageResult['success']) {
            $messageModel->status = Messages_MessagesModel::MESSAGE_STATUS_ANSWERED;
            $messageModel->update();
            return array(
                'success' => true
            );
        }

        return array('success' => false, 'message' => ApiModel::getInstance()->getErrorsAsHtml());
    }
}
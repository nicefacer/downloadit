<?php

class FeedbackHelper
{

    /**
     * @param $id
     * @param $feedbackType
     * @param $message
     *
     * @return bool
     */
    public static function leaveFeedback($id, $feedbackType, $message)
    {
        $feedbackModel = new Feedbacks_FeedbacksModel($id);

        $requestDataArray = array(
            'type' => $feedbackType,
            'message' => $message,
            'targetUser' => $feedbackModel->buyer_name,
            'itemId' => $feedbackModel->item_id,
            'transactionId' => $feedbackModel->transaction_id,
        );

        $accountModel = new AccountsModel($feedbackModel->account_id);
        if (!is_null($accountModel->id)) {
            $requestDataArray['token'] = $accountModel->token;
        }


        ApiModel::getInstance()->reset();
        $setFeedbackResult = ApiModel::getInstance()->ebay->feedback->set($requestDataArray)->post();

        if (isset($setFeedbackResult['success']) && $setFeedbackResult['success']) {
            $feedbackModel->seller_comment = $message;
            $feedbackModel->seller_type = $feedbackType;
            $feedbackModel->update();
            return array(
                'success' => true
            );
        }

        return array('success' => false, 'message' => ApiModel::getInstance()->getErrorsAsHtml());
    }
}
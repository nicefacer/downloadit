<?php

/**
 * File FeedbacksModel.php
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
 * eBay Listener Integration with PrestaShop e-commerce platform.
 * Adding possibility list PrestaShop Product directly to eBay.
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2015 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
class Feedbacks_FeedbacksModel extends AbstractModel
{
    const TYPE_NEUTRAL = "Neutral";

    const TYPE_NEGATIVE = "Negative";

    const TYPE_POSITIVE = "Positive";

    const ROLE_BUYER = 'Buyer';

    const ROLE_SELLER = 'Seller';


    public $account_id;

    public $item_id;

    public $transaction_id;

    public $title;

    public $buyer_feedback_id;

    public $buyer_name;

    public $buyer_time;

    public $buyer_comment;

    public $buyer_type;

    public $seller_feedback_id;

    public $seller_time;

    public $seller_comment;

    public $seller_type;

    public $date_upd;

    public function __construct($id = null, $id_lang = null)
    {
        $this->table      = "prestabay_feedbacks";
        $this->identifier = "id";

        $this->fieldsRequired = array('account_id', 'item_id', 'transaction_id');
        $this->fieldsSize     = array();
        $this->fieldsValidate = array();

        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();
        $returnArray = array(
            'account_id'         => (int) $this->account_id,
            'item_id'            => (float) $this->item_id,
            'transaction_id'     => (float) $this->transaction_id,
            'title'              => pSQL($this->title),
            'buyer_feedback_id'  => (float) $this->buyer_feedback_id,
            'buyer_name'         => pSQL($this->buyer_name),
            'buyer_time'         => pSQL($this->buyer_time),
            'buyer_comment'      => pSQL($this->buyer_comment),
            'buyer_type'         => pSQL($this->buyer_type),
            'seller_feedback_id' => (float) $this->seller_feedback_id,
            'seller_time'        => pSQL($this->seller_time),
            'seller_comment'     => pSQL($this->seller_comment),
            'seller_type'        => pSQL($this->seller_type),
            'date_upd'           => $this->date_upd,
        );

        return $returnArray;
    }


    /**
     * @param $accountId
     *
     * @return mixed
     */
    public static function getBuyerMaxTime($accountId)
    {
        $sql = "SELECT MAX(buyer_time) as max_buyer_time FROM " . _DB_PREFIX_ . "prestabay_feedbacks
            WHERE account_id = " . $accountId;

        $row = Db::getInstance()->getRow($sql, false);

        return isset($row['max_buyer_time']) ? $row['max_buyer_time'] : false;
    }

    /**
     * @param $accountId
     *
     * @return array
     */
    public static function getSellerMaxTime($accountId)
    {
        $sql = "SELECT MAX(seller_time) as max_seller_time FROM " . _DB_PREFIX_ . "prestabay_feedbacks
            WHERE account_id = " . $accountId;

        $row = Db::getInstance()->getRow($sql, false);

        return isset($row['max_seller_time']) ? $row['max_seller_time'] : false;

    }

    public static function retrieveFeedback($accountId, $itemId, $transactionId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_feedbacks
            WHERE account_id = " . (int) $accountId . " AND item_id = " . pSQL($itemId) . " AND transaction_id = " . pSQL($transactionId);

        $row = Db::getInstance()->getRow($sql, false);
        if ($row) {
            $feedbackModel = new Feedbacks_FeedbacksModel();
            $feedbackModel->setData($row);

            return $feedbackModel;
        }

        return false;
    }

    /**
     * @param $feedback
     * @param $accountId
     *
     * @return int
     */
    public static function importUpdateFeedbackRow($feedback, $accountId)
    {
        $countNewFeedbacks = 0;

        $feedBackDbInfo = array(
            'account_id'     => $accountId,
            'item_id'        => $feedback['item_id'],
            'transaction_id' => $feedback['transaction_id']
        );

        if ($feedback['title'] != '') {
            $feedBackDbInfo['title'] = $feedback['title'];
        }

        if ($feedback['from'] == Feedbacks_FeedbacksModel::ROLE_BUYER) {
            $feedBackDbInfo['buyer_feedback_id'] = $feedback['feedback_id'];
            $feedBackDbInfo['buyer_name']        = $feedback['user'];
            $feedBackDbInfo['buyer_comment']     = $feedback['comment'];
            $feedBackDbInfo['buyer_time']        = $feedback['time'];
            $feedBackDbInfo['buyer_type']        = $feedback['type'];
        } else {
            $feedBackDbInfo['seller_feedback_id'] = $feedback['feedback_id'];
            $feedBackDbInfo['seller_comment']     = $feedback['comment'];
            $feedBackDbInfo['seller_time']        = $feedback['time'];
            $feedBackDbInfo['seller_type']        = $feedback['type'];
        }

        $existingFeedback = self::retrieveFeedback($accountId, $feedback['item_id'], $feedback['transaction_id']);

        $newFeedback = new Feedbacks_FeedbacksModel();

        if ($existingFeedback) {
            $newFeedback = $existingFeedback;

            if ($feedback['from'] == self::ROLE_BUYER && !$existingFeedback->buyer_feedback_id) {
                $countNewFeedbacks++;
            }
            if ($feedback['from'] == self::ROLE_SELLER && !$existingFeedback->seller_feedback_id) {
                $countNewFeedbacks++;
            }
        } else {
            $countNewFeedbacks++;
        }

        $newFeedback->setData($feedBackDbInfo);
        $newFeedback->save();

        return $countNewFeedbacks;
    }


    /**
     * @param bool $full
     *
     * @return array(id, value)
     */
    public static function getTypesList($full = false)
    {
        $types = array();
        if ($full) {
            $types[] = array(
                'id'   => self::TYPE_NEGATIVE,
                'name' => L::t(self::TYPE_NEGATIVE),
            );

            $types[] = array(
                'id'   => self::TYPE_NEUTRAL,
                'name' => L::t(self::TYPE_NEUTRAL),
            );

            $types[] = array(
                'id'   => self::TYPE_POSITIVE,
                'name' => L::t(self::TYPE_POSITIVE),
            );
        } else {

        $types[] = array(
            'id'   => self::TYPE_POSITIVE,
            'name' => L::t(self::TYPE_POSITIVE),
        );
        $types[] = array(
            'id'   => self::TYPE_NEUTRAL,
            'name' => L::t(self::TYPE_NEUTRAL),
        );
        }

        return $types;
    }

    /**
     * Get max feedback ID.
     */
    public static function getMaxId()
    {
        $sql = "SELECT MAX(id) as max_id FROM " . _DB_PREFIX_ . "prestabay_feedbacks";

        $row = Db::getInstance()->getRow($sql, false);

        return isset($row['max_id']) ? $row['max_id'] : 0;
    }

    /**
     * Get all feedback for witch we can set positive feedback with not check old feedbacks
     *
     * @param $id
     *
     * @return array|boolean
     */
    public static function getAllResponsibleFeedbacksMoreId($id)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_feedbacks WHERE
                id > {$id} AND buyer_type = 'Positive' and seller_type = ''";

        $result = Db::getInstance()->ExecuteS($sql, true, false);
        if (!is_array($result)) {
            $result = array();
        }

        return $result;
    }
}
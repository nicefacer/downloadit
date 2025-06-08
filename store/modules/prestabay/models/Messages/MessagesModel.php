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
class Messages_MessagesModel extends AbstractModel
{
    const MESSAGE_TYPE_ASQ = 'AskSellerQuestion';

    const MESSAGE_TYPE_CTP = 'ContactTransactionPartner';

    const MESSAGE_TYPE_CMB = 'ContactMyBidder';

    const MESSAGE_TYPE_CEM = 'ContactEbayMember';

    const MESSAGE_TYPE_RESPONSE_TO_ASQ = 'ResponseToASQQuestion';

    const MESSAGE_TYPE_RESPONSE_TO_CBM = 'ResponseToContacteBayMember';


    const QUESTION_TYPE_NONE = 'None';

    const QUESTION_TYPE_MULTIPLE_ITEM_SHIPPING = 'MultipleItemShipping';

    const QUESTION_TYPE_GENERAL = 'General';

    const QUESTION_TYPE_PAYMENT = 'Payment';

    const QUESTION_TYPE_SHIPPING = 'Shipping';

    const QUESTION_TYPE_CUSTOMIZED_SUBJECT = 'CustomizedSubject';

    const MESSAGE_STATUS_ANSWERED = 'Answered';

    const MESSAGE_STATUS_UNANSWERED = 'Unanswered';

    /*

    //-----------------------
    $dbSelect = $connRead->select()
    ->from($tableMessages,new Zend_Db_Expr('MAX(`message_date`)'))
    ->where('`account_id` = ?',(int)$account->getId());
    $maxDate = $connRead->fetchOne($dbSelect);
    if (is_null($maxDate)) {
    $tempDate = new DateTime('-30 days');
    $maxDate = $tempDate->format('Y-m-d H:i:s');
    }
    //-----------------------

    // Update messages
    //-----------------------
    $paramsConnector = array('since_time' => $maxDate);

    $resultReceive = Mage::getModel('M2ePro/Messages')->receiveMessages($account,$paramsConnector);

    $this->_profiler->addTitle('Total received messages from eBay: '.$resultReceive['total']);
    $this->_profiler->addTitle('Total only new messages from eBay: '.$resultReceive['new']);


    =====
                $message['id']            = (double) $messageLine->Question->MessageID;
            $message['item']          = (double) $messageLine->Item->ItemID;
            $message['title']         = (string) $messageLine->Item->Title;
            $message['message_type']  = (string) $messageLine->Question->MessageType;
            $message['question_type'] = (string) $messageLine->Question->QuestionType;
            $message['sender']        = (string) $messageLine->Question->SenderID;
            $message['recipient']        = (string) $messageLine->Question->RecipientID;
            $message['subject']       = (string) $messageLine->Question->Subject;
            $message['text']          = (string) $messageLine->Question->Body;
            $message['status']        = (string) $messageLine->MessageStatus;
            $message['date']          = EbayHelper::convertEbayTimeToMysqlDateTime((string) $messageLine->CreationDate);

     */

    public $account_id;

    public $message_id;

    public $item_id;

    public $title;

    public $message_type;

    public $question_type;

    public $sender;

    public $subject;

    public $text;

    public $status;

    public $date;

    public $replay;

    public $date_upd;

    public function __construct($id = null, $id_lang = null)
    {
        $this->table      = "prestabay_messages";
        $this->identifier = "id";

        $this->fieldsRequired = array('account_id', 'message_id');
        $this->fieldsSize     = array();
        $this->fieldsValidate = array();

        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();
        $returnArray = array(
            'account_id'    => (int) $this->account_id,
            'message_id'    => pSQL($this->message_id),
            'item_id'       => pSQL($this->item_id),
            'title'         => pSQL($this->title),
            'message_type'  => pSQL($this->message_type),
            'question_type' => pSQL($this->question_type),
            'sender'        => pSQL($this->sender),
            'subject'       => pSQL($this->subject),
            'text'          => pSQL($this->text),
            'status'        => pSQL($this->status),
            'date'          => pSQL($this->date),
            'replay'        => pSQL($this->replay),
            'date_upd'      => $this->date_upd,
        );

        return $returnArray;
    }

    /**
     * Get replay list for selected table row
     *
     * @return array|mixed
     */
    public function getReplay()
    {
        if (!$this->id) {
            return array();
        }

        return json_decode($this->replay, true);
    }

    /**
     * @param array $replay
     *
     * @return $this
     */
    public function setReplay($replay)
    {
        $this->replay = json_encode($replay);

        return $this;
    }

    /**
     * Get latest message from our db
     *
     * @return bool|Date
     */
    public static function getMaxMessageDate()
    {
        $sql = "SELECT MAX(date) as max_date FROM " . _DB_PREFIX_ . "prestabay_messages";

        $row = Db::getInstance()->getRow($sql, false);

        return isset($row['max_date']) ? $row['max_date'] : false;

    }

    /**
     * @param $accountId
     * @param $messageId
     *
     * @return bool|int
     */
    public static function retrieveMessage($accountId, $messageId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_messages
            WHERE account_id = " . (int) $accountId . " AND message_id = " . pSQL($messageId);

        $row = Db::getInstance()->getRow($sql, false);
        if ($row) {
            return $row['id'];
        }

        return false;

    }

    public static function importUpdateMessageRow($message, $accountId)
    {
        $existingMessageId = self::retrieveMessage($accountId, $message['message_id']);

        $newMessage = new Messages_MessagesModel();

        $messageDbInfo = array(
            'account_id'    => $accountId,
            'message_id'    => $message['message_id'],
            'item_id'       => $message['item'],
            'title'         => $message['title'],
            'message_type'  => $message['message_type'],
            'question_type' => $message['question_type'],
            'sender'        => $message['sender'],
            'subject'       => $message['subject'],
            'text'          => $message['text'],
            'status'        => $message['status'],
            'date'          => $message['date'],
        );

        $isNew = 1;
        if ($existingMessageId) {
            $newMessage->id = $existingMessageId;
            $isNew          = 0;
        }

        $newMessage->setData($messageDbInfo);
        $newMessage->setReplay(isset($message['replay']) ? $message['replay'] : array());
        $newMessage->save();

        return $isNew;
    }
}
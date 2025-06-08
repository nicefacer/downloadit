<?php

class NotificationsModel extends AbstractModel
{
    const LEVEL_NOTICE = 'notice';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';

    const TYPE_SYSTEM = 'system';
    const TYPE_API = 'api';

    const READ_NO = 0;
    const READ_YES = 1;

    public $message_id;
    public $title;
    public $message;
    public $level;
    public $type;
    public $is_read = self::READ_NO;
    public $date;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_notifications";
        $this->identifier = "id";
        $this->fieldsRequired = array('message_id', 'title', 'message', 'level', 'type', 'is_read', 'date');
        $this->fieldsSize = array();
        $this->fieldsValidate = array();

        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'message_id' => (int)$this->message_id,
            'title' => pSQL($this->title),
            'message' => pSQL($this->message, true),
            'level' => pSQL($this->level),
            'type' => pSQL($this->type),
            'is_read' => (int)$this->is_read,
            'date' => pSQL($this->date)
        );
    }

    /**
     * Return first unread notification
     *
     * @return array
     */
    public function getFirstUnread()
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . $this->table .
            " WHERE `is_read` = 0 ORDER BY `date` DESC";

        return Db::getInstance()->getRow($sql, false);
    }

    /**
     * Return latest notification message date that we receive
     *
     * @return string|bool
     */
    public function getLatestNotificationDate()
    {
        $sql = "SELECT `date` FROM " . _DB_PREFIX_ . $this->table .
            " WHERE `type` = '" . pSQL(self::TYPE_API) . "' ORDER BY `date` DESC";

        $row = Db::getInstance()->getRow($sql, false);

        return isset($row['date']) ? $row['date'] : false;
    }

    public function getMessageByMessageId($messageId)
    {
        $sql = "SELECT id FROM " . _DB_PREFIX_ . $this->table .
            " WHERE message_id = " . (int)$messageId;

        $row = Db::getInstance()->getRow($sql, false);

        return isset($row['id']) ? $row['id'] : false;
    }

    /**
     * Update notification list
     *
     * @param array $notificationList
     */
    public function updateNotificationList($notificationList, $type = self::TYPE_API)
    {
        foreach ($notificationList as $row) {
            $row['message_id'] = $row['id'];
            unset($row['id']);
            if ($this->getMessageByMessageId($row['message_id'])) {
                // Skip existing messages
                continue;
            }
            $row['type'] = $type;

            $toUpdateModel = new NotificationsModel();
            $toUpdateModel->setData($row);
            $toUpdateModel->save();
        }
    }
}
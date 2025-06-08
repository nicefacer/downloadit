<?php

/**
 * File SyncModel.php
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
class Log_SyncModel extends AbstractModel
{
    /** Not defined task, default */
    const LOG_TASK_UNKNOWN = 0;
    /** Default Synchronization - update status of listings */
    const LOG_TASK_DEFAULT = 1;
    /** Update Stock Level PrestaShop <=> eBay */
    const LOG_TASK_STOCK_LEVEL = 2;
    /** Auto-relist In Stock product */
    const LOG_TASK_RELIST = 3;
    /** Auto-end Out of Stock PrestaShop Product */
    const LOG_TASK_END = 4;
    /** Synchronization Task Order */
    const LOG_TASK_ORDER = 5;
    /** Synchronization task Price */
    const LOG_TASK_PRICE = 6;
    /** Resynchornize QTY in PrestaShop and PrestaBay */
    const LOG_TASK_RESYNCHRONIZE_QTY = 7;
    /** Resynchornize Price in PrestaShop and PrestaBay */
    const LOG_TASK_RESYNCHRONIZE_PRICE = 8;
    /** Resynchornize Catalog products in PrestaShop and PrestaBay */
    const LOG_TASK_RESYNCHRONIZE_CATALOG = 9;
    /** Synchronization task List Not Active */
    const LOG_TASK_LIST = 10;
    /** Synchronization task Feedbacks */
    const LOG_TASK_FEEDBACKS = 11;
    /** Synchronization task Auto response to Feedbacks */
    const LOG_TASK_FEEDBACKS_AUTO = 12;
    /** Synchronization task on import ebay messages */
    const LOG_TASK_MESSAGES = 13;
    /** Synchronize task Full revise marked products */
    const LOG_TASK_FULL_REVISE = 14;

    const LOG_LEVEL_ERROR = 'error';
    const LOG_LEVEL_WARNING = 'warning';
    const LOG_LEVEL_NOTICE = 'notice';

    public $task = self::LOG_TASK_UNKNOWN;
    public $level = self::LOG_LEVEL_NOTICE;
    public $message;
    public $date_add;
    public $selling_product_id;
    public $ps_product_id;
    public $pb_order_id;
    public $ebay_item_id;
    public $ebay_account_id;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_log_sync";
        $this->identifier = "id";

        $this->fieldsRequired = array('task', 'message');

        $this->fieldsValidate = array(
            'task' => 'isInt',
            'level' => 'isGenericName',
        );

        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'task' => pSQL($this->task),
            'message' => pSQL($this->message, true),
            'level' => pSQL($this->level),
            'date_add' => $this->date_add,
            'selling_product_id' => $this->selling_product_id,
            'ps_product_id' => $this->ps_product_id,
            'pb_order_id' => $this->pb_order_id,
            'ebay_item_id' => $this->ebay_item_id,
            'ebay_account_id' => $this->ebay_account_id,
        );
    }

    public static function appendSuccess($message, $task = self::LOG_TASK_UNKNOWN, $addParams = array())
    {
        self::appendLog(self::LOG_LEVEL_NOTICE, $message, $task, $addParams);
    }

    public static function appendWarning($message, $task = self::LOG_TASK_UNKNOWN, $addParams = array())
    {
        self::appendLog(self::LOG_LEVEL_WARNING, $message, $task, $addParams);
    }

    public static function appendError($message, $task = self::LOG_TASK_UNKNOWN, $addParams = array())
    {
        self::appendLog(self::LOG_LEVEL_ERROR, $message, $task, $addParams);
    }

    public static function appendLog($level, $message, $task = self::LOG_TASK_UNKNOWN, $addParams = array())
    {
        if (empty($message)) {
            return false;
        }
        
        $logSyncModel = new Log_SyncModel();
        $logSyncModel->setData(array(
            'task' => $task,
            'level' => $level,
            'message' => $message,
        ) + $addParams);
        $logSyncModel->save();
    }

    /**
     * Clear all log that older that specify days.
     * @param int $intervalDays number of days for save log, 0 - for clear all
     */
    public static function clearLog($intervalDays = 0)
    {
        $removeSql = "DELETE FROM " . _DB_PREFIX_ . "prestabay_log_sync";
        if ($intervalDays > 0) {
            $daysToReduce = (int)$intervalDays;
            $removeSql .= " WHERE date_add <= NOW() - INTERVAL {$daysToReduce} DAY";
        }
        Db::getInstance()->Execute($removeSql);
    }

}
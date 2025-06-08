<?php
/**
 * File SellingModel.php
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

class Log_SellingModel extends AbstractModel
{
    const LOG_ACTION_SEND = 'send';
    const LOG_ACTION_RELIST = 'relist';
    const LOG_ACTION_REVISE = 'revise';
    const LOG_ACTION_STOP = 'stop';

    const LOG_LEVEL_ERROR = 'error';
    const LOG_LEVEL_WARNING = 'warning';
    const LOG_LEVEL_NOTICE = 'notice';

    public $selling_id;
    public $selling_product_id;
    public $action;
    public $level = self::LOG_LEVEL_NOTICE;
    public $message;
    public $date_add;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_log_selling";
        $this->identifier = "id";

        $this->fieldsRequired = array('selling_id', 'selling_product_id', 'action', 'message');

        $this->fieldsValidate = array(
            'selling_id' => 'isInt',
            'selling_product_id' => 'isInt',
            'action' => 'isGenericName',
            'level' => 'isGenericName',
        );


        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'selling_id' => (int) ($this->selling_id),
            'selling_product_id' => (int) ($this->selling_product_id),
            'action' => pSQL($this->action),
            'message' => pSQL($this->message, true),
            'level' => pSQL($this->level),
            'date_add' => $this->date_add,
        );
    }

    public function writeLogMessages($sellingId, $sellingProductId, $action, $level, $messages)
    {
        $this->setData(array(
            'selling_id' => $sellingId,
            'selling_product_id' => $sellingProductId,
            'action' => $action,
            'level' => $level
        ));

        foreach ($messages as $message) {
            $this->id = 0; // we need always create new
            $messageText = $message;
            if (is_array($message) && isset($message['message'])) {
                $messageText = $message['message'];
            }
            $this->message = $messageText;
            if (!$this->save()) {
                // failed save
            }
        }
    }

    public function addSuccessLog($sellingId, $sellingProductId, $action, $message)
    {
        $this->setData(array(
            'id' => 0,
            'selling_id' => $sellingId,
            'selling_product_id' => $sellingProductId,
            'action' => $action,
            'level' => self::LOG_LEVEL_NOTICE,
            'message' => $message
        ));

        $this->save();
    }

    // #####################
    // Filters for grid only

    public function filterBySelling($sellingId)
    {
        $this->_filter = "`mt`.selling_id = {$sellingId}";
        return $this;
    }

    public function filterBySellingProduct($sellingProductId)
    {
        $this->_filter = "`mt`.selling_product_id = {$sellingProductId}";
        return $this;
    }

}
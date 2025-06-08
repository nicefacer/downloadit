<?php

/**
 * File LogModel.php
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
class Order_LogModel extends AbstractModel
{

    public $prestabay_order_id;
    public $message;
    public $date_add;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_order_log";
        $this->identifier = "id";

        $this->fieldsRequired = array();

        $this->fieldsSize = array();

        $this->fieldsValidate = array();
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();
        return array(
            'prestabay_order_id' => (int) $this->prestabay_order_id,
            'message' => pSQL($this->message, true),
            'date_add' => $this->date_add,
        );
    }

    public static function addLogMessage($prestaBayOrderId, $message)
    {
        $orderLogModel = new Order_LogModel();
        $orderLogModel->setData(array(
                'prestabay_order_id' => $prestaBayOrderId,
                'message' => $message
        ));
        $orderLogModel->save();
    }

}
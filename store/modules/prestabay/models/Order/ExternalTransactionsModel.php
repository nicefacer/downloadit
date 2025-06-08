<?php

/**
 * File ExternalTransactionModel.php
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
class Order_ExternalTransactionsModel extends AbstractModel
{

    public $transaction_id;
    public $prestabay_order_id;
    public $time;
    public $fee;
    public $total;
    public $refund;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_order_external_transactions";
        $this->identifier = "id";

        $this->fieldsRequired = array();

        $this->fieldsSize = array();

        $this->fieldsValidate = array();
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        return array(
            'transaction_id' => pSQL($this->transaction_id),
            'prestabay_order_id' => (int) $this->prestabay_order_id,
            'time' => $this->time,
            'fee' => (float)$this->fee,
            'total' => (float)$this->total,
            'refund' => $this->refund
        );
    }

    public function removeTransactionRelatedToOrder($prestaShopOrderId)
    {
        $removeSql = "DELETE FROM " . _DB_PREFIX_ . $this->table . " WHERE prestabay_order_id = " . (int)$prestaShopOrderId;
        Db::getInstance()->Execute($removeSql);
    }

}
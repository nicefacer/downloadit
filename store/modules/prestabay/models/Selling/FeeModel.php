<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 *  It is available through the world-wide-web at this URL:
 *  http://involic.com/license.txt
 *  If you are unable to obtain it through the world-wide-web,
 *  please send an email to license@involic.com so
 *  we can send you a copy immediately.
 *
 *  PrestaBay - eBay Integration with PrestaShop e-commerce platform.
 *  Adding possibilty list PrestaShop Product dirrectly to eBay.
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011- 2016 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
class Selling_FeeModel extends AbstractModel
{
    const ACTION_LIST = 'list';
    const ACTION_REVISE = 'revise';
    const ACTION_RELIST = 'relist';

    public $ebay_id;
    public $account_id;
    public $selling_product_id;
    public $product_id;
    public $product_id_attribute;
    public $action;
    public $fee_total;
    public $fee_currency;
    public $fee_list;

    public $date_add;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_selling_fee";
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
            'ebay_id' => $this->ebay_id,
            'account_id' => (int)$this->account_id,
            'selling_product_id' => (int)$this->selling_product_id,
            'product_id' => (int)$this->product_id,
            'product_id_attribute' => (int)$this->product_id_attribute,
            'action' => pSQL($this->action),
            'fee_total' => (float)$this->fee_total,
            'fee_currency' => pSQL($this->fee_currency),
            'fee_list' => pSQL($this->fee_list),
            'date_add' => $this->date_add,
        );
    }

    public function getFeeList()
    {
        return json_decode($this->fee_list, true);
    }

    public function setFeeList($feeList = array())
    {
        $this->fee_list = json_encode($feeList);
    }
}
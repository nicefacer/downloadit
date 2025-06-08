<?php
/**
 * File AccountsModel.php
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

class AccountsModel extends AbstractModel
{
    const ACCOUNT_MODE_SANDBOX = 0;
    const ACCOUNT_MODE_LIVE = 1;

    public $name;
    public $token;
    public $exp_date;
    public $mode;
    public $date_add;
    public $date_upd;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_accounts";
        $this->identifier = "id";

        $this->fieldsRequired = array('name', 'token', 'exp_date', 'mode');

        $this->fieldsSize = array('name' => 255, 'token' => 999);

        $this->fieldsValidate = array(
            'name' => 'isGenericName',
            'token' => 'isString',
            'exp_date' => 'isDate',
            'mode' => 'isInt',
        );
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'name' => pSQL($this->name),
            'token' => pSQL($this->token),
            'exp_date' => pSQL($this->exp_date),
            'mode' => (int) ($this->mode),
            'date_add' => $this->date_add,
            'date_upd' => $this->date_upd,
        );
    }

    public function getAccountsList()
    {
        
    }

}
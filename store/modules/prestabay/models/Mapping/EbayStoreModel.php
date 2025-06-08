<?php

/**
 * File EbayStoreModel.php
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
class Mapping_EbayStoreModel extends AbstractModel
{
    public $name;

    public $account_id;

    public function __construct($id = null, $id_lang = null)
    {
        $this->table      = "prestabay_ebay_store_mapping";
        $this->identifier = "id";

        $this->fieldsRequired = array('name', 'account_id');

        $this->fieldsSize = array('name' => 255);

        $this->fieldsValidate = array(
            'name'       => 'isGenericName',
            'account_id' => 'isInt',
        );
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'name'       => pSQL($this->name),
            'account_id' => (int) $this->account_id,
        );
    }

    public function removeAllMappingCategories()
    {
        if (is_null($this->id)) {
            throw new Exception("Please Load Mapping Model");
        }

        $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'prestabay_ebay_store_mapping_categories WHERE mapping_id = ' . $this->id;
        Db::getInstance()->Execute($sql);
    }


    public static function getMappingList()
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_ebay_store_mapping";

        return Db::getInstance()->ExecuteS($sql);
    }

    public function remove()
    {
        if (is_null($this->id)) {
            throw new Exception("Please Load Mapping Model");
        }

        $this->removeAllMappingCategories();

        $sql = 'DELETE FROM ' . _DB_PREFIX_ . $this->table . ' WHERE id = ' . $this->id;
        Db::getInstance()->Execute($sql);
    }
}

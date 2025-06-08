<?php

/**
 * File ProductEbayDataModel.php
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
 * @copyright   Copyright (c) 2011-2013 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
class ProductEbayDataModel extends AbstractModel
{
    public $product_id; // isInt
    public $store_id; // isInt
    public $item_title; // isString
    public $subtitle; // isString
    public $description; // isText
    public $item_qty_value; // isInt
    public $price_value; // isFloat
    public $ebay_store_category_main_id;
    public $ebay_store_category_secondary_id;

    public function __construct($id = null, $id_lang = null)
    {
        $this->table      = "prestabay_product_ebay_data";
        $this->identifier = "id";

        $this->fieldsRequired = array();

        $this->fieldsSize = array();

        $this->fieldsValidate = array();

        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        $totalFields = array(
            'product_id'                       => (int)$this->product_id,
            'store_id'                         => (int)$this->store_id,
            'item_title'                       => pSQL($this->item_title),
            'subtitle'                         => pSQL($this->subtitle),
            'description'                      => pSQL($this->description, true),
            'item_qty_value'                   => (int)$this->item_qty_value,
            'price_value'                      => (float)$this->price_value,
            'ebay_store_category_main_id'      => (float)$this->ebay_store_category_main_id,
            'ebay_store_category_secondary_id' => (float)$this->ebay_store_category_secondary_id,
        );

        return $totalFields;
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return bool|ProductEbayDataModel
     */
    public static function loadByProductStoreId($productId, $storeId = 0)
    {
        $sql = "SELECT id FROM " . _DB_PREFIX_ . "prestabay_product_ebay_data
            WHERE product_id = " . (int)$productId . " AND store_id=" . (int)$storeId;
        $row = Db::getInstance()->getRow($sql, false);

        $id = null;
        if (isset($row['id'])) {
            $id = (int)$row['id'];
        }

        return new ProductEbayDataModel($id);
    }
}
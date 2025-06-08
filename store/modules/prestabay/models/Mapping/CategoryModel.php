<?php

/**
 * File CategoryModel.php
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
class Mapping_CategoryModel extends AbstractModel
{
    public $name;

    public $marketplace_id;

    public function __construct($id = null, $id_lang = null)
    {
        $this->table      = "prestabay_category_mapping";
        $this->identifier = "id";

        $this->fieldsRequired = array('name', 'marketplace_id');

        $this->fieldsSize = array('name' => 255);

        $this->fieldsValidate = array(
            'name'       => 'isGenericName',
            'marketplace_id' => 'isInt',
        );
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'name'       => pSQL($this->name),
            'marketplace_id' => (int) $this->marketplace_id,
        );
    }

    public function removeAllMappingCategories()
    {
        if (is_null($this->id)) {
            throw new Exception("Please Load Mapping Model");
        }

        $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'prestabay_category_mapping_categories WHERE mapping_id = ' . $this->id;
        Db::getInstance()->Execute($sql);

        $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'prestabay_category_mapping_line WHERE mapping_id = ' . $this->id;
        Db::getInstance()->Execute($sql);
    }


    public static function getMappingList()
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_category_mapping";

        return Db::getInstance()->ExecuteS($sql);
    }

    /**
     * @param int $marketplaceId
     *
     * @return array with list of mapping
     */
    public static function getMarketplaceMappingList($marketplaceId)
    {
        $sql = "SELECT id, name as label FROM " . _DB_PREFIX_ . "prestabay_category_mapping WHERE marketplace_id = ". (int) $marketplaceId;

        return Db::getInstance()->ExecuteS($sql);
    }

    /**
     * Remove category mapping
     */
    public function remove()
    {
        if (is_null($this->id)) {
            throw new Exception(L::t("Please Load Category Mapping Model"));
        }
        $this->removeAllMappingCategories();
        $sql = 'DELETE FROM ' . _DB_PREFIX_ . $this->table . ' WHERE id = ' . $this->id;
        Db::getInstance()->Execute($sql);
    }


    /**
     * @param $mappingId
     * @param $psCategoryId
     *
     * @return bool|array
     */
    public static function getMappingRow($mappingId, $psCategoryId)
    {
        $sql = "SELECT * FROM "._DB_PREFIX_."prestabay_category_mapping_categories WHERE mapping_id = ". (int)$mappingId." and category_id = ". (int)$psCategoryId;

        $mappingData = Db::getInstance()->getRow($sql, false);
        if (!isset($mappingData['mapping_line_id'])) {
            return false;
        }

        $sql = "SELECT * FROM "._DB_PREFIX_."prestabay_category_mapping_line WHERE id = ". (int)$mappingData['mapping_line_id'];

        $mappingLineData = Db::getInstance()->getRow($sql, false);
        if (!isset($mappingData['id'])) {
            return false;
        }

        return array(
            'primary' => $mappingLineData['ebay_primary_category_value'],
            'secondary' => $mappingLineData['ebay_secondary_category_value'],
            'condition' => json_decode($mappingLineData['item_condition'], true),
            'condition_description' => json_decode($mappingLineData['item_condition_description'], true),
            'product_specifics' => json_decode($mappingLineData['product_specifics'], true),
            'product_specifics_custom' => json_decode($mappingLineData['product_specifics_custom'], true),
        );

    }
}
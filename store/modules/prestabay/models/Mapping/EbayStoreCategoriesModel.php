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
class Mapping_EbayStoreCategoriesModel extends AbstractModel
{
    public $mapping_id;

    public $ebay_store_category_id;

    public $ebay_secondary_category_id;

    public $ps_category_id;

    public function __construct($id = null, $id_lang = null)
    {
        $this->table      = "prestabay_ebay_store_mapping_categories";
        $this->identifier = "id";

        $this->fieldsRequired = array('mapping_id', 'ebay_store_category_id', 'ps_category_id');

        $this->fieldsSize = array();

        $this->fieldsValidate = array();
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'mapping_id'                 => (int) $this->mapping_id,
            'ebay_store_category_id'     => (float) $this->ebay_store_category_id,
            'ebay_secondary_category_id' => (float) $this->ebay_secondary_category_id,
            'ps_category_id'             => (int) $this->ps_category_id,

        );
    }

    public function addMappingLine($mappingId, $mappingLine)
    {
        $this->mapping_id                 = $mappingId;
        $this->ebay_store_category_id     = $mappingLine['primary'];
        $this->ebay_secondary_category_id = $mappingLine['secondary'];

        foreach ($mappingLine['categories'] as $categoryId) {
            $this->id             = null;
            $this->ps_category_id = $categoryId;
            $this->save();
        }

    }

    public function getMappingList($mappingId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . $this->table . " WHERE mapping_id = " . $mappingId;
        $mappedCategories = Db::getInstance()->ExecuteS($sql, true, false);
        $formattedMapping = array();
        foreach ($mappedCategories as $row) {
            if (!isset($formattedMapping[$row['ebay_store_category_id']])) {
                $formattedMapping[$row['ebay_store_category_id']] = array(
                    'primary' => $row['ebay_store_category_id'],
                    'secondary' => $row['ebay_secondary_category_id'],
                    'categories' => array()
                );
            }
            $formattedMapping[$row['ebay_store_category_id']]['categories'][] = $row['ps_category_id'];
        }

        return array_values($formattedMapping);
    }

    public static function getMappingRow($mappingId, $categoryId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_ebay_store_mapping_categories WHERE mapping_id = " . $mappingId.
            " AND ps_category_id = ".$categoryId;

        return Db::getInstance()->getRow($sql, false);
    }

}
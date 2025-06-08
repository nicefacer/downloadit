<?php

/**
 * File CategoryLineModel.php
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
class Mapping_CategoryLineModel extends AbstractModel
{
    public $mapping_id;
    public $ebay_primary_category_name;
    public $ebay_primary_category_value;
    public $ebay_secondary_category_name;
    public $ebay_secondary_category_value;
    public $item_condition;
    public $item_condition_description;
    public $product_specifics;
    public $product_specifics_custom;


    public function __construct($id = null, $id_lang = null)
    {
        $this->table      = "prestabay_category_mapping_line";
        $this->identifier = "id";

        $this->fieldsRequired = array('mapping_id', 'ebay_primary_category_name', 'ebay_primary_category_value');

        $this->fieldsSize = array();

        $this->fieldsValidate = array();
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'mapping_id' => (int) $this->mapping_id,
            'ebay_primary_category_name' => pSQL($this->ebay_primary_category_name),
            'ebay_primary_category_value' => (float) $this->ebay_primary_category_value,
            'ebay_secondary_category_name' => pSQL($this->ebay_secondary_category_name),
            'ebay_secondary_category_value' => (float) $this->ebay_secondary_category_value,
            'item_condition' =>  pSQL($this->item_condition),
            'item_condition_description' =>  pSQL($this->item_condition_description),
            'product_specifics' => pSQL($this->product_specifics),
            'product_specifics_custom' => pSQL($this->product_specifics_custom),
        );
    }

    /**
     * @param $mappingId
     * @param $mappingLine
     */
    public static function addMappingLine($mappingId, $mappingLine)
    {
        $model = new Mapping_CategoryLineModel();
        $model->mapping_id = $mappingId;
        $model->ebay_primary_category_name = $mappingLine['ebay_primary_category_name'];
        $model->ebay_primary_category_value = $mappingLine['ebay_primary_category_value'];
        $model->ebay_secondary_category_name = $mappingLine['ebay_secondary_category_name'];
        $model->ebay_secondary_category_value = $mappingLine['ebay_secondary_category_value'];

        $model->item_condition = json_encode($mappingLine['item_condition']);
        $model->item_condition_description = json_encode($mappingLine['item_condition_description']);
        $model->product_specifics = json_encode($mappingLine['product_specifics']);
        $model->product_specifics_custom = json_encode($mappingLine['product_specifics_custom']);
        $model->save();

        Mapping_CategoryCategoriesModel::addMappingCategories($mappingId, $model->id, $mappingLine['categories']);
    }

    /**
     * @param $mappingId
     *
     * @return array
     */
    public static function getMappingLines($mappingId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_category_mapping_line WHERE mapping_id = " . $mappingId;

        $lineList = Db::getInstance()->ExecuteS($sql, true, false);
        foreach ($lineList as &$line) {
            $line['item_condition'] = json_decode($line['item_condition'], true);
            $line['item_condition_description'] = json_decode($line['item_condition_description'], true);
            $line['product_specifics'] = json_decode($line['product_specifics'], true);
            $line['product_specifics_custom'] = json_decode($line['product_specifics_custom'], true);
        }

        return $lineList;
    }


}
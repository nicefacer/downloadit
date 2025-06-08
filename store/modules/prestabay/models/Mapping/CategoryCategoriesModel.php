<?php

/**
 * File CategoryCategoriesModel.php
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
class Mapping_CategoryCategoriesModel extends AbstractModel
{
    public $mapping_id;

    public $mapping_line_id;

    public $category_id;

    public $category_name;

    public $category_path;

    public function __construct($id = null, $id_lang = null)
    {
        $this->table      = "prestabay_category_mapping_categories";
        $this->identifier = "id";

        $this->fieldsRequired = array('mapping_id', 'mapping_line_id', 'category_id', 'category_name');

        $this->fieldsSize = array();

        $this->fieldsValidate = array();
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'mapping_id'      => (int) $this->mapping_id,
            'mapping_line_id' => (int) $this->mapping_line_id,
            'category_id'     => (int) $this->category_id,
            'category_name'   => pSQL($this->category_name),
            'category_path'   => pSQL($this->category_path),
        );
    }

    /**
     * @param $lines
     *
     * @return mixed
     */
    public static function loadCategoriesForLines($lines)
    {
        foreach ($lines as &$line) {
            $line['categories'] = self::loadCategoryListForMappingLine($line['id']);
        }

        return $lines;
    }

    /**
     * @param $mappingLineId
     *
     * @return array
     */
    public static function loadCategoryListForMappingLine($mappingLineId)
    {
        $sql = "SELECT id, category_id, category_name, category_path FROM " . _DB_PREFIX_ . "prestabay_category_mapping_categories WHERE mapping_line_id = " . $mappingLineId;

        return Db::getInstance()->ExecuteS($sql, true, false);
    }


    public static function addMappingCategories($mappingId, $mappingLineId, $categories)
    {
        foreach ($categories as $categoryLine) {
            $model = new Mapping_CategoryCategoriesModel();
            $model->mapping_id = $mappingId;
            $model->mapping_line_id = $mappingLineId;
            $model->category_id = $categoryLine['category_id'];
            $model->category_name = $categoryLine['category_name'];
            $model->category_path = $categoryLine['category_path'];
            $model->save();
        }
    }


}
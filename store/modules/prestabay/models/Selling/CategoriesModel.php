<?php

/**
 * File CategoriesModel.php
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
class Selling_CategoriesModel extends AbstractModel
{

    public $selling_id;
    public $category_id;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_selling_categories";
        $this->identifier = "id";

        $this->fieldsRequired = array('selling_id', 'category_id');

        $this->fieldsSize = array();

        $this->fieldsValidate = array(
            'selling_id' => 'isInt',
            'category_id' => 'isInt',
        );
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'selling_id' => (int) ($this->selling_id),
            'category_id' => (int) ($this->category_id),
        );
    }

    /**
     * Get list of categories mapped to specific selling list
     * @param int $sellingId
     * @return array list of categories ids mapped to selling list
     */
    public static function getCategoriesMapped($sellingId)
    {
        $sql = "SELECT category_id FROM " . _DB_PREFIX_ . "prestabay_selling_categories
                         WHERE selling_id = {$sellingId}";

        $queryResult = Db::getInstance()->executeS($sql, true, false);
        $categoryIds = array();
        if (!$queryResult) {
            return $categoryIds;
        }

        foreach ($queryResult as $row) {
            $categoryIds[] = $row['category_id'];
        }

        return $categoryIds;
    }

    /**
     * Create new connection to categories
     *
     * @param int $sellingId
     * @param array $categoriesIds
     * @return boolean
     */
    public static function appendCategoriesConnection($sellingId, $categoriesIds)
    {
        $insertCommand = "INSERT INTO `" . _DB_PREFIX_ . 'prestabay_selling_categories' . "` (`selling_id`, `category_id`) VALUES ";
        $hasValues = false;
        foreach ($categoriesIds as $categoryId) {
            $hasValues && $insertCommand.=",";

            $insertCommand .="({$sellingId}, {$categoryId})";
            $hasValues = true;
        }
        if ($hasValues) {
            $insertCommand.=";";
            return Db::getInstance()->Execute($insertCommand);
        }

        return false;
    }

    /**
     * Find selling list use category mapping and mapped to target category list
     *
     * @param array $categoryIdList category Id list in PrestaShop
     * @return array with selling products
     */
    public static function getSellingIdsMappedToCategories($categoriesIds)
    {
        $categoryIdsString = implode(", ", $categoriesIds);
        if (empty($categoryIdsString)) {
            return array();
        }
        $sql = "SELECT selling_id FROM " . _DB_PREFIX_ . 'prestabay_selling_categories
                               WHERE category_id IN (' . $categoryIdsString . ')';
        $idSellings = Db::getInstance()->ExecuteS($sql);
        if (!$idSellings) {
            return array();
        }

        $selectedSellingIds = array();
        foreach ($idSellings as $singleRow) {
            $selectedSellingIds[] = $singleRow['selling_id'];
        }
        return $selectedSellingIds;
    }

    /**
     * Get list of products that put into specific category
     *
     * @param array $categoriesIds
     * @param boolean $onlyDefault
     * @return array list of product ids
     */
    public static function getProductsForCategories($categoriesIds, $onlyDefault = true)
    {

        !is_array($categoriesIds) && $categoriesIds = array($categoriesIds);

        $categoryIdsString = implode(", ", $categoriesIds);
        if (empty($categoryIdsString)) {
            $idProducts = array();
        } else {
            if (!$onlyDefault) {
                // Get list of products ids that apply to our selling list
                // Please notice we get only active product
                $sql = 'SELECT cp.id_product FROM ' . _DB_PREFIX_ . 'category_product cp ' .
                        'RIGHT JOIN ' . _DB_PREFIX_ . 'product pp ON pp.id_product = cp.id_product ' .
                        'WHERE pp.active = 1 AND cp.id_category IN (' . $categoryIdsString . ')';
            } else {
                // Get product with selected default category
                $sql = 'SELECT pp.id_product FROM ' . _DB_PREFIX_ . 'product pp
                                    WHERE pp.active = 1 AND pp.id_category_default IN (' . $categoryIdsString . ')';
            }
            $idProducts = Db::getInstance()->ExecuteS($sql);
        }

        if (empty($idProducts)) {
            return false;
        }

        $selectedProductsId = array();
        foreach ($idProducts as $singleRow) {
            $selectedProductsId[] = $singleRow['id_product'];
        }

        return $selectedProductsId;
    }


}
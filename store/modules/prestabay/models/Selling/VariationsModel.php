<?php

/**
 * File VariationsModel.php
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

/**
 * Information related to variation data for Selling Product. General information
 * for one variation set 
 */
class Selling_VariationsModel extends AbstractModel
{

    public $selling_product_id;
    public $product_id_attribute;
    public $qty;
    public $qty_change;
    public $price;
    public $price_change;
    public $sku;
    public $ebay_qty;
    public $ebay_sold_qty;
    public $ebay_sold_qty_sync;

    public function __construct($id = null, $id_lang = null)
    {
        $this->table = "prestabay_selling_variations";
        $this->identifier = "id";

        $this->fieldsRequired = array('selling_product_id', 'product_id_attribute',
            'qty', 'price', 'ebay_qty', 'ebay_sold_qty', 'ebay_sold_qty_sync');

        $this->fieldsSize = array();

        $this->fieldsValidate = array();
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'selling_product_id' => (int) $this->selling_product_id,
            'product_id_attribute' => (int) $this->product_id_attribute,
            'qty' => (int) $this->qty,
            'qty_change' => (int) $this->qty_change,
            'price' => (float) $this->price,
            'price_change' => (float) $this->price_change,
            'sku' => pSQL($this->sku),
            'ebay_qty' => (int) $this->ebay_qty,
            'ebay_sold_qty' => (int) $this->ebay_sold_qty,
            'ebay_sold_qty_sync' => (int) $this->ebay_sold_qty_sync,
        );
    }

    /**
     * Insert new variation information
     *
     * @param int $sellingProductId identify for Selling Product for witch apply variations
     * @param array $variationInfo information about variation
     * @return boolen result of insert
     */
    public function insertVariationInfo($sellingProductId, $variationInfo)
    {
        foreach ($variationInfo as $singleVariation) {
            if (!$this->_createVariationRow($sellingProductId, $singleVariation)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Update information related to variation
     * Call on Item Revise
     */
    public function updateVariationInfo($sellingProductId, $variationInfo)
    {
        $db = Db::getInstance();
        foreach ($variationInfo as $singleVariation) {
            $sql = "SELECT * FROM " . _DB_PREFIX_ . $this->table . " WHERE " .
                    "`selling_product_id` = " . $sellingProductId . " AND " .
                    "`product_id_attribute` = " . $singleVariation['id_product_attribute'];

            $existingVariationInfo = $db->getRow($sql, true, false);
            if (!$existingVariationInfo) {
                // Create new variation row
                if (!$this->_createVariationRow($sellingProductId, $singleVariation)) {
                    return false;
                }
            } else {
                $result = $db->autoExecute(_DB_PREFIX_ . $this->table, array(
                            'qty' => (int) $singleVariation['product_qty'],
                            'price' => (float) $singleVariation['price'],
                            'sku' => pSQL($singleVariation['sku']),
                            'ebay_qty' => (int) ($singleVariation['qty'] + $existingVariationInfo['ebay_sold_qty']),
                                ),
                                'UPDATE',
                                "`id` = " . ((int) $existingVariationInfo['id']));

                if (!$result) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getPrestaBayVariationInfo($sellingProductId)
    {
        $db = Db::getInstance();
        $sql = "SELECT v.*, o.variation_id, o.key, o.value FROM "._DB_PREFIX_ . $this->table." as v
                    INNER JOIN "._DB_PREFIX_ . "prestabay_selling_variations_options o ON o.variation_id = v.id
                    WHERE v.selling_product_id = ".$sellingProductId;

        $existingVariationInfo = $db->ExecuteS($sql, true, false);
        if (!$existingVariationInfo) {
            return array();
        }

        $variationArray = array();
        foreach ($existingVariationInfo as $row) {
            if (!isset($variationArray[$row['id']])) {
                $variationArray[$row['id']] = array(
                  'id_product_attribute' => $row['product_id_attribute'],
                  'qty' => $row['qty'],
                  'price' => $row['price'],
                  'sku' => $row['sku'],
                  'options' => array()
                );
            }
            $variationArray[$row['id']]['options'][$row['key']] = $row['value'];
        }
        foreach ($variationArray as $key => $singleElement) {
            ksort($variationArray[$key]['options']);
        }

        return $variationArray;
//    array
//      'id' => string '75' (length=2)
//      'selling_product_id' => string '65' (length=2)
//      'product_id_attribute' => string '70' (length=2)
//      'qty' => string '30' (length=2)
//      'qty_change' => string '0' (length=1)
//      'price' => string '43' (length=2)
//      'price_change' => string '0' (length=1)
//      'sku' => string '' (length=0)
//      'ebay_qty' => string '30' (length=2)
//      'ebay_sold_qty' => string '0' (length=1)
//      'ebay_sold_qty_sync' => string '0' (length=1)
//      'variation_id' => string '75' (length=2)
//      'key' => string 'Color' (length=5)
//      'value' => string 'Purple' (length=6)

//  0 =>
//    array
//      'id_product_attribute' => string '70' (length=2)
//      'qty' => string '30' (length=2)
//      'price' => float 43
//      'sku' => string '' (length=0)
//      'options' =>
//        array
//          'Color' => string 'Purple' (length=6)
//          'US Shoe Size (Men's)' => string '17' (length=2)


    }

    public function updateVariationSynchronizeInfo($singleVariationInfo)
    {
        $db = Db::getInstance();
        $result = $db->autoExecute(_DB_PREFIX_ . $this->table, array(
                    'ebay_qty' => (int) $singleVariationInfo['ebay_qty'],
                    'ebay_sold_qty' => (int) $singleVariationInfo['ebay_sold_qty'],
                    'ebay_sold_qty_sync' => (int) $singleVariationInfo['ebay_sold_qty_sync'],
                        ),
                        'UPDATE',
                        "`id` = " . ((int) $singleVariationInfo['id']));

        if (!$result) {
            return false;
        }
    }

    /**
     * Remove all information assigned to product variation information
     *
     * @param int $sellingProductId
     * @return boolean result of remove
     */
    public function deleteVariationInfo($sellingProductId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . $this->table .
                " WHERE `selling_product_id` = " . $sellingProductId;
        $variationList = Db::getInstance()->ExecuteS($sql, true, false);

        // Delete all options connected to variations
        foreach ($variationList as $singleVariation) {
            $variationOptionsModel = new Selling_Variations_OptionsModel();
            $result = $variationOptionsModel->deleteOptions($singleVariation['id']);
            if ($result === false) {
                return false;
            }
        }
        // Finialy delete varations
        $sqlToDelete = "DELETE FROM " . _DB_PREFIX_ . $this->table . "
                        WHERE `selling_product_id` = " . $sellingProductId;

        return Db::getInstance()->Execute($sqlToDelete);
    }

    public function getVariationsList($sellingProductId)
    {
        $sql = "SELECT vo.key, vo.value, v.* 
                    FROM " . _DB_PREFIX_ . "prestabay_selling_variations_options as vo
                    LEFT JOIN " . _DB_PREFIX_ . "prestabay_selling_variations as v ON v.id = vo.variation_id
                    WHERE v.selling_product_id = " . $sellingProductId;
        $variationQueryResult = Db::getInstance()->ExecuteS($sql, true, false);
        $variationInfoList = array();
        foreach ($variationQueryResult as $row) {
            if (!isset($variationInfoList[$row['id']])) {
                $variationInfoList[$row['id']] = array(
                    'id' => $row['id'],
                    'ebay_qty' => $row['ebay_qty'],
                    'ebay_sold_qty' => $row['ebay_sold_qty'],
                    'ebay_sold_qty_sync' => $row['ebay_sold_qty_sync'],
                    'options' => array()
                );
            }
            $variationInfoList[$row['id']]['options'][$row['key']] = $row['value'];
        }

//        $hashedVariationInfoList = array();
//        foreach ($variationInfoList as $singleList) {
//            $hashedVariationInfoList[md5(serialize($singleList['options']))] = $singleList;
//        }
        return $variationInfoList; // $hashedVariationInfoList;
    }

    /**
     * Return variation information for target product and attribute id
     * @param int $sellingId target product id
     * @param int $attributeId target attribute id
     * @return array with variation information
     */
    public function getVariationBySellingAndAttribute($sellingId, $attributeId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_selling_variations
                    WHERE selling_product_id = " . $sellingId . " AND
                          product_id_attribute = " . $attributeId;
        return Db::getInstance()->getRow($sql, false);
    }

    /**
     * Get list of variation products that have unsynchronized qty.
     *
     * @return array list of variation selling products
     */
    public function getEbayChangedProducts($sellingProductId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . $this->table . " WHERE ebay_sold_qty_sync > 0 AND selling_product_id = " . $sellingProductId;
        return Db::getInstance()->ExecuteS($sql, true, false);
    }

    /**
     * Return list of variation attributes product that QTY has been changed on
     * PrestaShop.
     * Return only one Variation Attributes Product for each Selling Product.
     * 
     * @return array list of product
     */
    public function getPrestashopChangedProductsAttributes()
    {
        $sql = "SELECT sv.*, sp.selling_id, sp.product_id, sp.ebay_id FROM " . _DB_PREFIX_ . $this->table . " sv
                        RIGHT JOIN " . _DB_PREFIX_ ."prestabay_selling_products sp ON sv.selling_product_id = sp.id
                        WHERE sv.qty_change != 0 GROUP BY sv.selling_product_id";
        return Db::getInstance()->ExecuteS($sql, true, false);
    }

    /**
     * Return list of variation attributes product that PRICE has been changed on
     * PrestaShop.
     * Return only one Variation Attributes Product for each Selling Product.
     *
     * @return array list of product
     */
    public function getPrestashopPriceChangedProductsAttributes()
    {
        $sql = "SELECT sv.*, sp.selling_id, sp.product_id, sp.ebay_id FROM " . _DB_PREFIX_ . $this->table . " sv
                        RIGHT JOIN " . _DB_PREFIX_ ."prestabay_selling_products sp ON sv.selling_product_id = sp.id
                        WHERE sv.price_change != 0 GROUP BY sv.selling_product_id";
        return Db::getInstance()->ExecuteS($sql, true, false);
    }

    /**
     * Reset PrestaShop variation product qty change.
     * Used for synchronize stock level PrestaShop <=> eBay
     * @param int $sellingProductId PrestaBay product identify
     * @return boolean result of query execution
     */
    public function resetProductQTYChange($sellingProductId)
    {
        $db = Db::getInstance();
        $result = $db->autoExecute(_DB_PREFIX_ . $this->table, array(
                    'qty_change' => 0,
                        ),
                        'UPDATE',
                        "`selling_product_id` = " . ((int) $sellingProductId));

        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * Reset PrestaShop variation product price change.
     * Used for synchronize price level from PrestaShop => eBay
     * @param int $sellingProductId PrestaBay product identify
     * @return boolean result of query execution
     */
    public function resetProductPriceChange($sellingProductId)
    {
        $db = Db::getInstance();
        $result = $db->autoExecute(_DB_PREFIX_ . $this->table, array(
                    'price_change' => 0,
                        ),
                        'UPDATE',
                        "`selling_product_id` = " . ((int) $sellingProductId));

        if (!$result) {
            return false;
        }
        return true;
    }


    /**
     * Reset PRICE and QTY change for specific product
     * @param $sellingProductId
     * @return bool
     */
    public function resetProductPriceQTYChange($sellingProductId)
    {
        $db = Db::getInstance();
        $result = $db->autoExecute(_DB_PREFIX_ . $this->table, array(
                'price_change' => 0,
                'qty_change' => 0,
            ),
            'UPDATE',
            "`selling_product_id` = " . ((int) $sellingProductId));

        if (!$result) {
            return false;
        }
        return true;
    }



    /**
     * Reset eBay information releated to all variation from single Selling Product.
     *
     * @param int $sellingProductId PrestaBay product id
     */
    public function resetVariationInfo($sellingProductId)
    {
        $db = Db::getInstance();
        $result = $db->autoExecute(_DB_PREFIX_ . $this->table, array(
                    'ebay_sold_qty' => 0,
                    'ebay_sold_qty_sync' => 0,
                    'qty_change' => 0,
                    'price_change' => 0,
                        ),
                        'UPDATE',
                        "`selling_product_id` = " . ((int) $sellingProductId));

        if (!$result) {
            return false;
        }
        return true;
    }

    protected function _createVariationRow($sellingProductId, $singleVariation)
    {
        $db = Db::getInstance();
        $sellingVariationOptionsModel = new Selling_Variations_OptionsModel();
        $result = $db->autoExecute(_DB_PREFIX_ . $this->table, array(
                    'selling_product_id' => (int) $sellingProductId,
                    'product_id_attribute' => (int) $singleVariation['id_product_attribute'],
                    'qty' => (int) $singleVariation['product_qty'],
                    'price' => (float) $singleVariation['price'],
                    'sku' => pSQL($singleVariation['sku']),
                    'ebay_qty' => (int) $singleVariation['qty'],
                    'ebay_sold_qty' => 0,
                    'ebay_sold_qty_sync' => 0,), 'INSERT');

        if (!$result) {
            return false;
        }
        if (!$sellingVariationOptionsModel->insertOptions($db->Insert_ID(), $singleVariation['options'])) {
            return false;
        }
        return true;
    }

}
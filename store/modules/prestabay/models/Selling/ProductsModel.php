<?php

/**
 * File ProductsModel.php
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
 * Information about product that prepared(listed) on ebay connected with selling
 * profile
 */
class Selling_ProductsModel extends AbstractModel
{

    const STATUS_ERROR = -1;
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_FINISHED = 2; // Status recived from eBay
    const STATUS_STOPED = 3; // Manual stop (or error on relist/revise/end)

    public $selling_id;
    public $product_id;
    public $product_id_attribute;
    public $product_name;
    public $product_price;
    public $product_price_change;
    public $product_qty;
    public $product_qty_change;
    public $ebay_id;
    public $ebay_name;
    public $ebay_price;
    public $ebay_qty;
    public $ebay_sold_qty;
    public $ebay_sold_qty_sync; // qty that need to be dec presta product
    public $ebay_start_time;
    public $ebay_end_time;
    public $full_revise;
    public $status;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_selling_products";
        $this->identifier = "id";

        $this->fieldsRequired = array('selling_id', 'product_id', 'product_name', 'product_price', 'product_qty');

        $this->fieldsSize = array('product_name' => 255, 'ebay_name' => 80);

        $this->fieldsValidate = array(
//            'selling_id' => 'isInt',
//            'product_id' => 'isInt',
//            'product_name' => 'isGenericName',
//            'product_price' => 'isFloat',
//            'product_qty' => 'isInt',
//
//            'ebay_id' => 'isAnything',
////            'ebay_name' => 'isGenericName',
//            'ebay_price' => 'isFloat',
//            'ebay_qty' => 'isInt',
//
//            'status' => 'isInt',
        );
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        $this->ebay_name = substr($this->ebay_name, 0,80);

        parent::validateFields();

        return array(
            'selling_id' => (int) $this->selling_id,
            'product_id' => (int) $this->product_id,
            'product_id_attribute' => (int) $this->product_id_attribute,
            'product_name' => pSQL($this->product_name),
            'product_price' => (float) $this->product_price,
            'product_price_change' => (float) $this->product_price_change,
            'product_qty' => (int) $this->product_qty,
            'product_qty_change' => (int) $this->product_qty_change,
            'ebay_id' => $this->ebay_id, // this is not int. not feet to int size
            'ebay_name' => pSQL($this->ebay_name),
            'ebay_price' => (float) $this->ebay_price,
            'ebay_qty' => (int) $this->ebay_qty,
            'ebay_sold_qty' => (int) $this->ebay_sold_qty,
            'ebay_sold_qty_sync' => (int) $this->ebay_sold_qty_sync,
            'ebay_start_time' => $this->ebay_start_time,
            'ebay_end_time' => $this->ebay_end_time,
            'full_revise' => (int) $this->full_revise,
            'status' => (int) $this->status
        );
    }

    public function filterBySelling($id)
    {
        $this->_filter = "selling_id = {$id}";
        return $this;
    }

    public function deleteProductsForSelling($id)
    {
        $sqlToDelete = "DELETE FROM " . _DB_PREFIX_ . $this->table . "
                        WHERE selling_id = " . $id;

        return Db::getInstance()->Execute($sqlToDelete);
    }

    /**
     * Return DB row object related to connected eBay Id
     *
     * @param int $ebayId ebay item number
     * @return mixed false on not found, array when found
     */
    public function getSellingProductByEbayId($ebayId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . $this->table . " WHERE ebay_id='" . pSQL($ebayId) . "'";
        return Db::getInstance()->getRow($sql, false);
    }

    /**
     * Return array list of product related to specific selling
     *
     * @param int $sellingId selling number
     * @return mixed false on not found, array when found
     */
    public function getSellingProductsIdsBySellingId($sellingId)
    {
        $sql = "SELECT product_id FROM " . _DB_PREFIX_ . $this->table . " WHERE selling_id=" . $sellingId;
        $result = Db::getInstance()->ExecuteS($sql, true, false);
        $idsList = array();
        foreach ($result as $row) {
            $idsList[] = $row['product_id'];
        }
        return array_unique($idsList);
    }

    /**
     *
     * @param int $productId
     * @return array
     */
    public function getSellingProductsByProductId($productId, $attributeId = false)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . $this->table . " WHERE product_id=" . $productId;
        $attributeId && $sql.=" AND product_id_attribute=" . $attributeId;
        return Db::getInstance()->ExecuteS($sql, true, false);
    }

    /**
     * Get Selling Products list by PrestaShop product ID, sellingID and
     * attributeID if available
     *
     * @param int $productId
     * @return array
     */
    public function getSellingProductsByProductIdsSellingId($productIds, $sellingId, $attributeId = false)
    {
        !is_array($productIds) && $productIds = array($productIds);
        $sql = "SELECT * FROM " . _DB_PREFIX_ . $this->table . " WHERE product_id in (" . implode(', ', $productIds) . ") " .
                " AND selling_id = {$sellingId}";

        $attributeId && $sql.=" AND product_id_attribute=" . $attributeId;
        return Db::getInstance()->ExecuteS($sql, true, false);
    }

    /**
     * Return Selling Product rows for specific productId and sellingId.
     * Used to verify of product exists in Selling List
     *
     * @param int $productId
     * @param int $sellingId
     *
     * @return array|false data about product in Selling List
     */
    public function getSellingProductsByProductIdSellingId($sellingId, $productId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . $this->table . " WHERE product_id = " . (int) $productId .
            " AND selling_id = ". (int)$sellingId;

        return Db::getInstance()->ExecuteS($sql, true, false);
    }

    /**
     * Get list of selling products that have unsynchronized qty.
     * 
     * @return array list of selling products
     */
    public function getEbayChangedProducts()
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . $this->table . " WHERE ebay_sold_qty_sync != 0";
        return Db::getInstance()->ExecuteS($sql, true, false);
    }

    /**
     * Get List of active 'Selling Products' that have qty changes in connected PrestaBay Product.
     * Looking only for product that have qty on eBay equal to qty on PrestaShop
     * @return array
     */
    public function getPrestashopChangedProducts()
    {

        $sql = "SELECT `sp`.* FROM " . _DB_PREFIX_ . $this->table . " sp
            LEFT JOIN " . _DB_PREFIX_ . "prestabay_selling_list sl ON `sp`.selling_id = `sl`.id
            LEFT JOIN " . _DB_PREFIX_ . "prestabay_profiles pr ON `sl`.profile = `pr`.id
            WHERE `sp`.product_qty_change != 0 AND `sp`.status = " . self::STATUS_ACTIVE .
            " AND (`pr`.item_qty_mode = " . ProfilesModel::ITEM_QTY_MODE_PRODUCT. " OR ".
            "`pr`.item_qty_mode = " . ProfilesModel::ITEM_QTY_MODE_NOT_MORE_THAT. " OR ".
            "`pr`.item_qty_mode = " . ProfilesModel::ITEM_QTY_MODE_RESERVED_VALUE .")";

        return Db::getInstance()->ExecuteS($sql, true, false);
    }

    /**
     * Execute resynchronize QTY between PrestaShop product QTY and PrestaBay
     * QTY saved values. This very usable when QTY update from import module.
     *
     */
    public function resynchronizeQTY()
    {
        if (CoreHelper::isPS15()) {
            $sql = "SELECT p.id, p.product_qty, sa.quantity FROM " . _DB_PREFIX_ . $this->table . " p
                        INNER JOIN " . _DB_PREFIX_ . "stock_available sa ON 
                            sa.id_product = p.product_id AND sa.id_product_attribute = p.product_id_attribute
                        WHERE sa.quantity != p.product_qty";
            $resynchronizedList = Db::getInstance()->ExecuteS($sql, true, false);
        } else {
            /** @todo test this on PS 1.4 */
            $sql1 = "SELECT p.id, p.product_qty, pp.quantity FROM " . _DB_PREFIX_ . $this->table . " p
                        INNER JOIN " . _DB_PREFIX_ . "product pp ON pp.id_product = p.product_id
                        WHERE pp.quantity != p.product_qty AND p.product_id_attribute = 0";

            $resynchronizedListDefault = Db::getInstance()->ExecuteS($sql1, true, false);
            !is_array($resynchronizedListDefault) && $resynchronizedListDefault = array();
            $sql2 = "SELECT p.id, p.product_qty, pa.quantity FROM " . _DB_PREFIX_ . $this->table . " p
                        INNER JOIN " . _DB_PREFIX_ . "product_attribute pa ON pa.id_product_attribute = p.product_id_attribute
                        WHERE pa.quantity != p.product_qty AND p.product_id_attribute > 0";

            $resynchronizedListAttribute = Db::getInstance()->ExecuteS($sql2, true, false);
            !is_array($resynchronizedListAttribute) && $resynchronizedListAttribute = array();
            $resynchronizedList = array();
            foreach ($resynchronizedListDefault as $rldItem) {
                $resynchronizedList[$rldItem['id']] = $rldItem;
            }

            foreach ($resynchronizedListAttribute as $rlaItem) {
                $resynchronizedList[$rldItem['id']] = $rlaItem;
            }
            $resynchronizedList = array_values($resynchronizedList);
        }

        if (!is_array($resynchronizedList)) {
            $resynchronizedList = array();
        }

        $numberOfUpdatedFields = 0;
        foreach ($resynchronizedList as $row) {
            $qtyChange = $row['quantity'] - $row['product_qty'];

            $updateSql = "UPDATE " . _DB_PREFIX_ . $this->table . " SET
                    `product_qty` = " . (int) $row['quantity'] . ",
                    `product_qty_change` = " . (int) $qtyChange . "
                    WHERE `id` = " . (int) $row['id'];

            Db::getInstance()->Execute($updateSql);
            $numberOfUpdatedFields++;
        }
        return $numberOfUpdatedFields;
    }

    /**
     * Execute resynchronize QTY between PrestaShop combination product QTY
     * and PrestaBay variation QTY saved values.
     * This very usable when QTY update from import module.
     *
     * Only for attributes
     */
    public function resynchronizeVariationQTY()
    {
        if (CoreHelper::isPS15()) {
            $sql = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_selling_variations v
                    INNER JOIN " . _DB_PREFIX_ . "stock_available sa ON sa.id_product_attribute = v.product_id_attribute
                WHERE sa.quantity != v.qty";
        } else {
            $sql = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_selling_variations v
                    INNER JOIN " . _DB_PREFIX_ . "product_attribute pa ON pa.id_product_attribute = v.product_id_attribute
                WHERE pa.quantity != v.qty";
        }

        $resynchronizedList = Db::getInstance()->ExecuteS($sql, true, false);
        if (!is_array($resynchronizedList)) {
            $resynchronizedList = array();
        }
        $numberOfUpdatedFields = 0;
        foreach ($resynchronizedList as $row) {
            $qtyChange = $row['quantity'] - $row['qty'];

            $updateSql = "UPDATE " . _DB_PREFIX_ . "prestabay_selling_variations" . " SET
                    `qty` = " . (int) $row['quantity'] . ",
                    `qty_change` = " . (int) $qtyChange . "
                    WHERE `id` = " . (int) $row['id'];

            Db::getInstance()->Execute($updateSql);
            $numberOfUpdatedFields++;
        }
        return $numberOfUpdatedFields;
    }

    /**
     * Resynchornize Price value saved on PrestaBay and that have product on PS
     * @return int
     */
    public function resynchronizePrice()
    {
        $sql = "SELECT p.id, p.product_price, pp.price FROM " . _DB_PREFIX_ . $this->table . " p
                        INNER JOIN " . _DB_PREFIX_ . "product pp ON pp.id_product = p.product_id
                        WHERE abs(pp.price - p.product_price) > 0.01 AND p.product_id_attribute = 0";

        $resynchronizedListDefault = Db::getInstance()->ExecuteS($sql, true, false);
        !is_array($resynchronizedListDefault) && $resynchronizedListDefault = array();
        $sql = "SELECT p.id, p.product_price, pa.price FROM " . _DB_PREFIX_ . $this->table . " p
                       INNER JOIN " . _DB_PREFIX_ . "product pp ON pp.id_product = p.product_id
                       INNER JOIN " . _DB_PREFIX_ . "product_attribute pa ON pa.id_product_attribute = p.product_id_attribute
                       WHERE abs((pp.price + pa.price) - p.product_price) > 0.01 AND p.product_id_attribute > 0";

        $resynchronizedListAttribute = Db::getInstance()->ExecuteS($sql, true, false);
        !is_array($resynchronizedListAttribute) && $resynchronizedListAttribute = array();
        $resynchronizedList = array();
        foreach ($resynchronizedListDefault as $rldItem) {
            $resynchronizedList[$rldItem['id']] = $rldItem;
        }

        foreach ($resynchronizedListAttribute as $rlaItem) {
            $resynchronizedList[$rlaItem['id']] = $rlaItem;
        }

        $resynchronizedList = array_values($resynchronizedList);

        if (!is_array($resynchronizedList)) {
            $resynchronizedList = array();
        }

        $numberOfUpdatedFields = 0;
        foreach ($resynchronizedList as $row) {
            $priceChange = $row['price'] - $row['product_price'];

            $updateSql = "UPDATE " . _DB_PREFIX_ . $this->table . " SET
                    `product_price` = " . (float) $row['price'] . ",
                    `product_price_change` = " . (float) $priceChange . "
                    WHERE `id` = " . (int) $row['id'];

            Db::getInstance()->Execute($updateSql);
            $numberOfUpdatedFields++;
        }
        return $numberOfUpdatedFields;
    }

    /**
     * Get List of active 'Selling Products' that have price changes in connected PrestaBay Product.
     * @return array
     */
    public function getPrestashopPriceChangedProducts()
    {

        $sql = "SELECT `sp`.* FROM " . _DB_PREFIX_ . $this->table . " sp
            LEFT JOIN " . _DB_PREFIX_ . "prestabay_selling_list sl ON `sp`.selling_id = `sl`.id
            WHERE `sp`.product_price_change != 0 AND `sp`.status = " . self::STATUS_ACTIVE;

        return Db::getInstance()->ExecuteS($sql, true, false);
    }

    /**
     * Get ended of all finished products that "In Stock" into PrestaShop
     * 
     * @return array with list of products
     */
    public function getFinishInStockProducts()
    {
        $sql = "SELECT sp.* FROM " . _DB_PREFIX_ . $this->table . " sp
            INNER JOIN " . _DB_PREFIX_ . "prestabay_selling_list sl ON sl.id = sp.selling_id
            INNER JOIN " . _DB_PREFIX_ . "prestabay_profiles pp ON sl.profile = pp.id
            WHERE sp.status = " . self::STATUS_FINISHED . " AND ((sp.product_qty > 0 AND ".
            "(pp.item_qty_mode = ". ProfilesModel::ITEM_QTY_MODE_PRODUCT. " OR pp.item_qty_mode = ". ProfilesModel::ITEM_QTY_MODE_NOT_MORE_THAT. "))
            OR (sp.product_qty > pp.item_qty_value AND pp.item_qty_mode = ". ProfilesModel::ITEM_QTY_MODE_RESERVED_VALUE. "))";


        return  Db::getInstance()->ExecuteS($sql, true, false);
    }

    /**
     * Retrive list of eBay active Product that "Out of Stock" in PrestaShop
     *
     * Please notice that we get only product that have QTY mode for QTY or NOT MORE THAT
     * @return array with list of products
     */
    public function getActiveOutOfStockProducts()
    {
        $sql =  "SELECT sp.* FROM " . _DB_PREFIX_ . $this->table . " sp
                    INNER JOIN " . _DB_PREFIX_ . "prestabay_selling_list sl ON sl.id = sp.selling_id
                    INNER JOIN " . _DB_PREFIX_ . "prestabay_profiles pp ON sl.profile = pp.id
                    WHERE sp.product_qty <= 0 AND sp.status = " . self::STATUS_ACTIVE . " AND ".
            "(pp.item_qty_mode = ". ProfilesModel::ITEM_QTY_MODE_PRODUCT. " OR pp.item_qty_mode = ". ProfilesModel::ITEM_QTY_MODE_NOT_MORE_THAT. "
                    OR  pp.item_qty_mode = ". ProfilesModel::ITEM_QTY_MODE_RESERVED_VALUE.")";

        return Db::getInstance()->ExecuteS($sql, true, false);
    }

    /**
     * Get list of Not Active Listings that In Stock in PrestaShop
     *
     * @return array with list of products
     */
    public function getNotActiveInStockProducts()
    {
        $sql = "SELECT sp.* FROM " . _DB_PREFIX_ . $this->table . " sp
            INNER JOIN " . _DB_PREFIX_ . "prestabay_selling_list sl ON sl.id = sp.selling_id
            INNER JOIN " . _DB_PREFIX_ . "prestabay_profiles pp ON sl.profile = pp.id
            WHERE sp.status = " . self::STATUS_NOT_ACTIVE . " AND ((sp.product_qty > 0 AND ".
            "(pp.item_qty_mode = ". ProfilesModel::ITEM_QTY_MODE_PRODUCT. " OR pp.item_qty_mode = ". ProfilesModel::ITEM_QTY_MODE_NOT_MORE_THAT. "))
            OR (sp.product_qty > pp.item_qty_value AND pp.item_qty_mode = ". ProfilesModel::ITEM_QTY_MODE_RESERVED_VALUE. "))";

        return  Db::getInstance()->ExecuteS($sql, true, false);
    }


    /**
     * Check that product exist on provided selling list
     * 
     * @param int $sellingId Selling list id
     * @param int $productId PrestaShop product id
     * @return bool return true when product exist
     */
    public static function isProductExistOnSelling($sellingId, $productId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_selling_products WHERE selling_id = " . $sellingId . " AND product_id = " . $productId;
        $rows = Db::getInstance()->ExecuteS($sql, true, false);
        if ($rows != false && count($rows) > 0) {
            // Product already on selling list
            return true;
        }

        // No product
        return false;
    }

    public function isVariationListing($sellingProductId)
    {
        // Perfrom check is current selling product is listed as variatio on ebay
        $sql = "SELECT count(*) as total FROM " . _DB_PREFIX_ . "prestabay_selling_variations WHERE selling_product_id = " . $sellingProductId;
        $rows = Db::getInstance()->getRow($sql, false);
        if ($rows && isset($rows['total']) && $rows['total'] > 0) {
            return true;
        }
        return false;
    }

    /**
     * Get list of selling products prepared for full revise
     *
     * @return array
     */
    public function getProductsForFullRevise()
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . $this->table . ' sp WHERE sp.full_revise = 1 AND  sp.status = ' . self::STATUS_ACTIVE;

        return  Db::getInstance()->ExecuteS($sql, true, false);
    }

    /**
     * Delete product from Selling List by it ID in Selling List (not PrestaShop Id)
     * @param int $sellingProductId selling product id
     * @return bool
     */
    public static function deleteSellingProductById($sellingProductId)
    {
        $sqlToDelete = "DELETE FROM " . _DB_PREFIX_ . "prestabay_selling_products
                        WHERE id = " . $sellingProductId;

        return Db::getInstance()->Execute($sqlToDelete);
    }

    public static function getAllNotListedProducts($sellingId = null)
    {
        $sqlToGetList = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_selling_products
            WHERE status = " . self::STATUS_NOT_ACTIVE;

        if (!is_array($sellingId) && $sellingId > 0) {
            $sqlToGetList .=" AND selling_id = " . $sellingId;
        } else if (is_array($sellingId)) {
            $sqlToGetList .=" AND selling_id IN (" . implode(",", $sellingId).")";
        }

        return Db::getInstance()->ExecuteS($sqlToGetList, true, false);
    }

    public static function getAllListedProducts($sellingId = null)
    {

        $sqlToGetList = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_selling_products
            WHERE status = " . self::STATUS_ACTIVE;

        if (!is_array($sellingId) && $sellingId > 0) {
            $sqlToGetList .=" AND selling_id = " . $sellingId;
        } else if (is_array($sellingId)) {
            $sqlToGetList .=" AND selling_id IN (" . implode(",", $sellingId).")";
        }

        return Db::getInstance()->ExecuteS($sqlToGetList, true, false);
    }

    public static function getAllListedProductsWithQty0($sellingId = null)
    {
        $sqlToGetList = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_selling_products
            WHERE status = " . self::STATUS_ACTIVE . " AND product_qty <= 0";

        if (!is_array($sellingId) && $sellingId > 0) {
            $sqlToGetList .=" AND selling_id = " . $sellingId;
        } else if (is_array($sellingId)) {
            $sqlToGetList .=" AND selling_id IN (" . implode(",", $sellingId).")";
        }
        return Db::getInstance()->ExecuteS($sqlToGetList, true, false);
    }

    /**
     * Get list of products that was finished on eBay
     * @param <type> $sellingId
     * @return array
     */
    public static function getAllFinishedOrStoppedProducts($sellingId = null)
    {
        $sqlToGetList = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_selling_products
            WHERE (status = " . self::STATUS_FINISHED . " OR status = " . self::STATUS_STOPED . ")";

        if (!is_array($sellingId) && $sellingId > 0) {
            $sqlToGetList .=" AND selling_id = " . $sellingId;
        } else if (is_array($sellingId)) {
            $sqlToGetList .=" AND selling_id IN (" . implode(",", $sellingId).")";
        }

        return Db::getInstance()->ExecuteS($sqlToGetList, true, false);
    }

    /**
     * Get list of products that was finished on eBay
     * @param <type> $sellingId
     * @return array
     */
    public static function getAllFinishedOrStoppedWithQtyProducts($sellingId = null)
    {
        $sqlToGetList = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_selling_products
            WHERE (status = " . self::STATUS_FINISHED . " OR status = " . self::STATUS_STOPED . ") AND product_qty > 0";

        if (!is_array($sellingId) && $sellingId > 0) {
            $sqlToGetList .=" AND selling_id = " . $sellingId;
        } else if (is_array($sellingId)) {
            $sqlToGetList .=" AND selling_id IN (" . implode(",", $sellingId).")";
        }

        return Db::getInstance()->ExecuteS($sqlToGetList, true, false);
    }

    /**
     * Get full information about listed items based on selling product ids
     * 
     * @param array $idList list of ids for each required return full information
     * @return array 
     */
    public static function getFullProductInfo($idList)
    {
        $sqlToGetList = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_selling_products
            WHERE id IN (" . implode(",", $idList) . ")";
        return Db::getInstance()->ExecuteS($sqlToGetList, true, false);
    }

    /**
     * Cut not used information from full list product information. Return only
     * key, name values
     * @param array $productsInformation
     * @return array
     * @todo refactoring, very poor style of code
     */
    public static function getShortInfoByFull($productsInformation)
    {
        $idsList = array();
        foreach ($productsInformation as $productRow) {
            $idsList[] = array(
                'id' => $productRow['id'],
                'product_name' => $productRow['product_name']
            );
        }
        return $idsList;
    }

}

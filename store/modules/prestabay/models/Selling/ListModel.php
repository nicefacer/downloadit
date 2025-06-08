<?php

/**
 * File ListModel.php
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
class Selling_ListModel extends AbstractModel
{
    const MODE_PRODUCT = 0;
    const MODE_CATEGORY = 1;

    const INCLUDE_SUBDIR_NO = 0;
    const INCLUDE_SUBDIR_YES = 1;

    const ATTRIBUTE_MODE_ONE_LISTING = 0;
    const ATTRIBUTE_MODE_SEPARATE_LISTINGS = 1;

    const CATEGORY_SEND_PRODUCT_NO = 0;
    const CATEGORY_SEND_PRODUCT_YES = 1;

    const DUPLICATE_PROTECT_MODE_NO = 0;
    const DUPLICATE_PROTECT_MODE_SINGLE_PRODUCT_LIST = 1;

    public $name;
    public $language;
    public $profile;
    public $mode;
    public $category_id;
    public $attribute_mode;
    public $category_send_product;

    public $duplicate_protect_mode;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_selling_list";
        $this->identifier = "id";

        $this->fieldsRequired = array('name', 'language', 'profile', 'mode');

        $this->fieldsSize = array('name' => 255);

        $this->fieldsValidate = array(
            'name' => 'isGenericName',
            'profile' => 'isInt',
            'language' => 'isInt',
            'mode' => 'isInt',
            'attribute_mode' => 'isInt',
        );
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'name' => pSQL($this->name),
            'profile' => (int)($this->profile),
            'language' => (int)($this->language),
            'mode' => (int)($this->mode),
            'category_id' => (int)($this->category_id),
            'attribute_mode' => (int)($this->attribute_mode),
            'category_send_product' => (int)($this->category_send_product),
            'duplicate_protect_mode' => (int)($this->duplicate_protect_mode),
        );
    }

    /**
     * Remove all information related to selected selling list. Also remove all
     * products. Import product not stoped from eBay
     *
     * @param int $id Selling List to remove for
     * @return bool sucess or not removing
     */
    public function deleteAllInformation($id)
    {
        $modelProducts = new Selling_ProductsModel();
        $result = $modelProducts->deleteProductsForSelling($id);
        if ($result === false) {
            return false;
        }

        $sqlToDelete = "DELETE FROM " . _DB_PREFIX_ . $this->table . "
                        WHERE id = " . $id;

        return Db::getInstance()->Execute($sqlToDelete);
    }

    /**
     * Append array of products to selling list
     *
     * @see appendProduct
     *
     * @param array $productsIds list of PrestaShop products Id that need to be
     * apply to current selling list
     * @return boolean success or not wil be adding
     */
    public function appendsProducts($productsIds)
    {
        if (is_null($this->id)) {
            return false;
        }

        $sellingLogModel = new Log_SellingModel();

        $resultOfAdding = true;

        foreach ($productsIds as $productId) {
            $newSellingProductId = $this->appendProduct($productId);
            if ($newSellingProductId == false) {
                // Problem adding product to selling
                // Write log message
                $sellingLogModel->writeLogMessages($this->id, 0, Log_SellingModel::LOG_ACTION_SEND,
                    Log_SellingModel::LOG_LEVEL_ERROR, array(L::t("Can't append product to Selling. DB Error. Product Id") . ": {$productId}"));

                $resultOfAdding = false;
            }

            if ($newSellingProductId === -1) {
                $sellingLogModel->writeLogMessages($this->id, 0, Log_SellingModel::LOG_ACTION_SEND,
                    Log_SellingModel::LOG_LEVEL_ERROR, array(L::t("Can't append product to Selling. Already exist. Product id ") . ": {$productId}"));
            }
        }
        return $resultOfAdding;
    }

    /**
     * Append single product to current selling list
     *
     * @param int $productId product id in PrestaShop
     * @return int|bool number of product in Selling List or false or error adding
     */
    public function appendProduct($productId)
    {
        if (is_null($this->id)) {
            return false;
        }
        $resultOfAdding = true;

        $sellingProducts = new Selling_ProductsModel();

        if ($this->duplicate_protect_mode == self::DUPLICATE_PROTECT_MODE_SINGLE_PRODUCT_LIST) {
            // Check that $productId is not exist in Selling List
            $row = $sellingProducts->getSellingProductsByProductIdSellingId($this->id, $productId);
            if (!empty($row)) {
                return -1; // product skipped to adding, due to duplication
            }
        }

        /**
         * @var ProductCore $productModel
         */
        $productModel = new Product($productId, true, $this->language);

        if (is_null($productModel->id)) {
            RenderHelper::addError(
                "PrestaShop product DB contain not existing products. This product has skipped. Product id: " . $productId
            );

            return false;
        }

        $price = $productModel->getPrice();
        if (empty($price)) {
            $price = 0;
        }
        $productName = $productModel->name;
        if (CoreHelper::isPS15()) {
            if (empty($productName)) {
                $productName = Product::getProductName($this->id, null, $this->language);
            }
        }
        if (empty($productName)) {
            // Problem with name
            return false;
        }

        if ($this->attribute_mode == self::ATTRIBUTE_MODE_ONE_LISTING) {
            $sellingProductData = array(
                'selling_id' => $this->id,
                'product_id' => $productModel->id,
                'product_name' => $productName,
                'product_price' => $price,
                'product_qty' => Product::getQuantity($productModel->id),
            );
            if (!$sellingProducts->setData($sellingProductData)->save()) {
                return false;
            }
        } else {
            if ($this->attribute_mode == self::ATTRIBUTE_MODE_SEPARATE_LISTINGS) {
                $combinations = VariationHelper::generateProductCombinationList($productModel, $this->language);
                if (empty($combinations)) {
                    // Combination mode but no combination for product
                    $sellingProductData = array(
                        'selling_id' => $this->id,
                        'product_id' => $productModel->id,
                        'product_name' => $productName,
                        'product_price' => $price,
                        'product_qty' => Product::getQuantity($productModel->id),
                    );

                    if (!$sellingProducts->setData($sellingProductData)->save()) {
                        $resultOfAdding = false;
                    }
                } else {
                    // Combination mode and have combinations
                    foreach ($combinations as $singleCombination) {
                        $sellingProductData = array(
                            'selling_id' => $this->id,
                            'product_id' => $productModel->id,
                            'product_id_attribute' => $singleCombination['id_product_attribute'],
                            'product_name' => $productName . ' [' . VariationHelper::variationFlatName(
                                    $singleCombination
                                ) . ']',
                            'product_price' => $singleCombination['price'],
                            'product_qty' => $singleCombination['qty'],
                        );
                        $sellingProducts = new Selling_ProductsModel();
                        if (!$sellingProducts->setData($sellingProductData)->save()) {
                            $resultOfAdding = false;
                            break;
                        }
                    }
                }
            }
        }

        return ($resultOfAdding) ? $sellingProducts->id : false;
    }


    /**
     * Move items from ebay listings to Selling Profile
     *
     * @param array $ebayListingsIds ebay listings product ids
     *
     * @return bool result of moving
     */
    public function moveProductsFromEbayListings($ebayListingsIds)
    {
        $totalImported = 0;
        foreach ($ebayListingsIds as $ebayListingId) {
            $ebayListingModel = new EbayListingsModel($ebayListingId);
            if (!$ebayListingModel->id) {
                continue;
            }

            if (!($ebayListingModel->product_id > 0)) {
                // we should have product_id to import
                continue;
            }
            $insertResult = $this->insertMapping(
                $ebayListingModel->title,
                $ebayListingModel->product_id,
                $ebayListingModel->item_id,
                $ebayListingModel->qty,
                $ebayListingModel->qty_available,
                $ebayListingModel->buy_price,
                $ebayListingModel->status,
                $ebayListingModel->start_time
            );

            if ($insertResult) {
                $ebayListingModel->delete();
                $totalImported++;
            }
        }

        return $totalImported;
    }

    /**
     * Insert new mapping to Selling List
     *
     * @param string $ebayTitle
     * @param int $productId
     * @param bigint $itemId
     * @param int $qty
     * @param int $qtyAvailable
     * @param float $price
     * @param int $status
     * @param string $ebayStartTime
     *
     * @return boolean result of add mapping
     */
    protected function insertMapping($ebayTitle, $productId, $itemId, $qty, $qtyAvailable, $price, $status, $ebayStartTime)
    {
        if (!$this->id) {
            return false;
        }

        /**
         * @var ProductCore $productModel
         */
        $productModel = new Product($productId, true, $this->language);

        if (is_null($productModel->id)) {
            return false;
        }

        $productName = $productModel->name;
        if (CoreHelper::isPS15() && empty($productName)) {
            $productName = Product::getProductName($productId, null, $this->language);
        }
        if (empty($productName)) {
            // Problem with name
            return false;
        }

        if ($this->attribute_mode == self::ATTRIBUTE_MODE_ONE_LISTING) {
            // For now we support only One-Listing mode
            $sellingProductData = array(
                'selling_id' => $this->id,
                'product_id' => $productId,
                'product_name' => $productName,
                'product_price' => Product::getPriceStatic((int) $productId, true),
                'product_qty' => Product::getQuantity($productModel->id),
                'ebay_name' => $ebayTitle,
                'ebay_id' => $itemId,
                'ebay_price' => $price,
                'ebay_start_time' => $ebayStartTime,
                'ebay_end_time' => '0000-00-00 00:00:00',
                'ebay_qty' => $qty,
                'ebay_qty_sold' => $qty - $qtyAvailable,
                'status' => $status, // we have same code number, just map it
            );
            $sellingProducts = new Selling_ProductsModel();
            if (!$sellingProducts->setData($sellingProductData)->save()) {
                $errorMessage = DB::getInstance()->getMsgError();
                return false;
            }

            Selling_ConnectionsModel::appendNewConnection((int) $productId, 0, (int) $this->language, $itemId);

            return $sellingProducts->id;
        }

        return false;
    }

    /**
     * Move selling products to specific selling profile
     *
     * @param array $sellingProductsIds ids of selling products
     * @param int $newSellingId where move selling products
     * @return boolean result of move
     */
    public static function moveSellingProducts($sellingProductsIds, $newSellingId)
    {
        $updateSql = 'UPDATE ' . _DB_PREFIX_ . 'prestabay_selling_products SET selling_id = ' . $newSellingId .
            ' WHERE id in (' . implode(',', $sellingProductsIds) . ')';
        return Db::getInstance()->Execute($updateSql, false);
    }

    /**
     * Check for selected PrestaShop product have or not another category
     * connection except current specified category.
     * This function used to determinate PrestaShop product that change it main
     * category id and reguiried move/remove from category
     *
     * @param int $categoryIdList
     * @param int $productId
     * @return bool|array false when connection to another category not found else return array with selling id, selling product id and status
     */
    public static function getSellingProductMappedToAnotherCategoryAndTargetProduct($categoryIdList, $productId)
    {
        $categoryListStr = implode(',', $categoryIdList);
        if (empty($categoryListStr)) {
            $selingListProduct = array();
        } else {
            // 1) Get list of allowed selling id connected to category
            $sql = 'SELECT p.selling_id FROM ' . _DB_PREFIX_ . 'prestabay_selling_products p
                        INNER JOIN ' . _DB_PREFIX_ . 'prestabay_selling_list l ON l.id = p.selling_id
                        INNER JOIN ' . _DB_PREFIX_ . 'prestabay_selling_categories sc ON sc.selling_id = p.selling_id
                        WHERE
                            l.mode = ' . Selling_ListModel::MODE_CATEGORY . ' AND
                            sc.category_id in (' . $categoryListStr . ')
                    GROUP BY p.selling_id';
            $selingListProduct = Db::getInstance()->ExecuteS($sql, true, false);
        }


        $allowedSellingId = array();
        if (count($selingListProduct) > 0) {
            foreach ($selingListProduct as $sp) {
                $allowedSellingId[] = $sp['selling_id'];
            }
        }
        $selingListProductToStop = array();
        if (count($allowedSellingId) > 0) {
            // 2) Get list of all not allowed selling id for specific product
            $sql = 'SELECT p.id, p.selling_id, p.product_id, p.status FROM ' . _DB_PREFIX_ . 'prestabay_selling_products p
                    INNER JOIN ' . _DB_PREFIX_ . 'prestabay_selling_list l ON l.id = p.selling_id
                    INNER JOIN ' . _DB_PREFIX_ . 'prestabay_selling_categories sc ON sc.selling_id = p.selling_id
                    WHERE
                        l.mode = ' . Selling_ListModel::MODE_CATEGORY . ' AND ' .
                (count($allowedSellingId) > 0 ? ' sc.selling_id not in (' . implode(',', $allowedSellingId) . ') AND ' : '') .
                ' p.product_id = ' . $productId;

            $selingListProductToStop = Db::getInstance()->ExecuteS($sql, true, false);
        }

        if ($selingListProductToStop == false || count($selingListProductToStop) <= 0) {
            return false;
        }

        return $selingListProductToStop;
    }

    public static function getSellingIdWithCategoryMode()
    {
        $sql = 'SELECT id FROM ' . _DB_PREFIX_ . 'prestabay_selling_list WHERE mode = ' . Selling_ListModel::MODE_CATEGORY;
        $sellingIdRows = Db::getInstance()->ExecuteS($sql, true, false);
        $sellingIds = array();
        if (empty($sellingIdRows) || !is_array($sellingIdRows)) {
            return array();
        }
        foreach ($sellingIdRows as $row) {
            $sellingIds[] = $row['id'];
        }
        return $sellingIds;

    }

    /**
     * Return product list prepared for specific action.
     * For example for action "Send" return all not listed product.
     * For action "Revise" all listed products.
     * For action "Relist" all finished products
     *
     * @param string $actionName
     */
    public function getAllProductsIdsPreparedForAction($actionName, $sellingListIds = false)
    {
        $sellingId = $this->id;

        if (is_null($this->id)) {
            $sellingId = false; // All Products without assigned to specific Selling
        }
        if (is_array($sellingListIds)) {
            $sellingId = $sellingListIds;
        }


        $productList = array();
        switch ($actionName) {
            case "send":
                $productList = Selling_ProductsModel::getAllNotListedProducts($sellingId);
                break;
            case "relist":
                $productList = Selling_ProductsModel::getAllFinishedOrStoppedProducts($sellingId);
                break;
            case "revise":
            case "stop":
                $productList = Selling_ProductsModel::getAllListedProducts($sellingId);
                break;
            case "stopQty0":
                $productList = Selling_ProductsModel::getAllListedProductsWithQty0($sellingId);
                break;
            case "relistWithQty":
                $productList = Selling_ProductsModel::getAllFinishedOrStoppedWithQtyProducts($sellingId);
                break;
        }

        if (count($productList) == 0) {
            return false;
        }

        return Selling_ProductsModel::getShortInfoByFull($productList);
    }

}
<?php

/**
 * File AccountsModel.php
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
class EbayListingsModel extends AbstractModel
{

    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_FINISHED = 2; // Status recived from eBay
    const STATUS_STOPED = 3; // Manual stop (or error on relist/revise/end)
    const LISTING_TYPE_CHINESE = 1;
    const LISTING_TYPE_FIXEDPRICE = 2;

    public $account_id;
    public $item_id;
    public $product_id;
    public $title;
    public $start_time;
    public $buy_price;
    public $currency;
    public $qty;
    public $qty_available;
    public $url;
    public $picture_url;
    public $sku;
    public $listing_type;
    public $listing_duration;
    public $status;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_ebay_listings";
        $this->identifier = "id";

        $this->fieldsRequired = array('account_id', 'item_id', 'title', 'status');

        $this->fieldsSize = array();

        $this->fieldsValidate = array();
        parent::__construct($id, $id_lang);
    }

    /**
     *
     * @param type $itemId
     * @return boolean|\EbayListingsModel
     */
    public static function loadByItemId($itemId)
    {
        $sqlToGetItem = "SELECT id FROM " . _DB_PREFIX_ . "prestabay_ebay_listings
            WHERE item_id = {$itemId}";
        $row = Db::getInstance()->getRow($sqlToGetItem, false);

        if (!isset($row['id'])) {
            return false;
        }
        return new EbayListingsModel($row['id']);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'account_id' => (int) $this->account_id,
            'item_id' => pSQL($this->item_id),
            'product_id' => (int)$this->product_id,
            'title' => pSQL($this->title),
            'start_time' => $this->start_time,
            'buy_price' => (float) $this->buy_price,
            'currency' => pSQL($this->currency),
            'qty' => (int) $this->qty,
            'qty_available' => (int) $this->qty_available,
            'url' => pSQL($this->url),
            'picture_url' => pSQL($this->picture_url),
            'sku' => pSQL($this->sku),
            'listing_type' => (int) $this->listing_type,
            'listing_duration' => pSQL($this->listing_duration),
            'status' => (int) $this->status,
        );
    }

    /**
     * Import eBay listings into PrestaBay DB
     *
     * @param int $accountId
     * @param array $items
     */
    public function importListings($accountId, $items)
    {
        $defaultLanguageId = (int) (Configuration::get('PS_LANG_DEFAULT'));

        $errorMessages = '';
        $totalInsert = 0;
        $totalExistingsInPrestaBay = 0;
        $totalExistingInEbayListings = 0;
        foreach ($items as $item) {
            // Try to find that itemId into PrestaBay listings
            $existingItem = Selling_ConnectionsModel::getPrestaConnectionByEbayId($item['itemId']);
            if ($existingItem) {
                $totalExistingsInPrestaBay++;
                continue;
            }

            // Try to find in eBay Listings table
            $existInEBayListings = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . $this->table.' WHERE item_id = ' . $item['itemId']);
            if (isset($existInEBayListings['id']) && $existInEBayListings['id'] > 0) {
                $totalExistingInEbayListings++ ;
                continue;
            }

            // Try to find mapping to PrestaShop Product
            $productId = $this->findPrestaShopMapping($item['title'], $item['sku'], $defaultLanguageId);

            // List not found in PrestaBay and in eBay Listings start import
            $listingModel = new EbayListingsModel();
            $listingModel->setData(array(
                'account_id' => $accountId,
                'item_id' => $item['itemId'],
                'product_id' => $productId,
                'title' => $item['title'],
                'start_time' => $item['startTime'],
                'buy_price' => $item['buyPrice'],
                'currency' => $item['currency'],
                'qty' => $item['qty'],
                'qty_available' => $item['qtyAvailable'],
                'url' => $item['viewItemUrl'],
                'picture_url' => $item['picture'],
                'sku' => $item['sku'],
                'listing_type' => $item['listingType'],
                'listing_duration' => $item['listingDuration'],
                'status' => self::STATUS_ACTIVE,
            ));
            if (!$listingModel->save()) {
                $errorMessages != "" && $errorMessages .= "<br/>";
                $errorMessages .= L::t(sprintf("Item %s can't be added to DB", $item['itemId']));
                continue;
            }
            $totalInsert++;

        }
        return array($totalInsert, $totalExistingsInPrestaBay, $totalExistingInEbayListings, $errorMessages);
    }

    /**
     * Try to find product in PrestaShop that match title or SKU
     *
     * @param string $title
     * @param string $sku
     *
     * @return int product id, null if not found
     */
    public function findPrestaShopMapping($title, $sku, $languageId)
    {
        if (empty($title)) {
            return null;
        }

        $productTitle = preg_replace('/[<>;=#{}]+/', ' ', $title);
        $productIdThatFound = null;

        if (!empty($sku)) {
            // When we have SKU, try to search by it first
            $result = Db::getInstance()->getRow(
                "SELECT * FROM " . _DB_PREFIX_ . "product WHERE reference = '" . pSQL($sku) . "'",
                false
            );

            if (isset($result['id_product'])) {
                $productIdThatFound = $result['id_product'];
            }

            if (!$productIdThatFound && strpos($sku, 'prestashop-') === 0) {
                // This is PS ebay module product, so second part is ID
                $productIdThatFound = (int)substr($sku, 11);
            }
        }

        if (!$productIdThatFound) {
            // When product by SKU not found, search by title
            $existingProduct = $this->searchProductByName($productTitle, $languageId);
            if (is_array($existingProduct) && count($existingProduct) > 0) {
                // Find product,
                $existingProductInfo = reset($existingProduct);
                $productIdThatFound = $existingProductInfo['id_product'];
            }
        }

        return $productIdThatFound;
    }

    /**
     * Try to find PS product by name
     *
     * @param string $productTitle product title
     * @param int $languageId language used for search. Currently used only Default language in PS
     *
     * @return array|bool list of product or false if not found
     *
     */
    protected function searchProductByName($productTitle, $languageId)
    {
        $sql = "SELECT pl.id_product, pl.name FROM " . _DB_PREFIX_ . "product_lang pl
            WHERE pl.id_lang = " . (int)$languageId . " AND pl.name = '" . pSQL($productTitle) . "'";

        $result = Db::getInstance()->getRow($sql, false);
        if (!isset($result['id_product'])) {
            return null;
        }

        return array($result);
    }

    public static function getListingsInfo($idList)
    {
        $sqlToGetList = "SELECT item_id as id, title as product_name FROM " . _DB_PREFIX_ . "prestabay_ebay_listings
            WHERE id IN (" . implode(",", $idList) . ")";
        return Db::getInstance()->ExecuteS($sqlToGetList, true, false);
    }

    /**
     * Try to detect ids list of ebay listings by title/sku
     *
     * @param array $idsList list of ids to detect
     *
     * @return int number of detected items
     */
    public function detectByTitleSku($idsList)
    {
        if (empty($idsList)) {
            return 0;
        }

        $defaultLanguageId = (int) (Configuration::get('PS_LANG_DEFAULT'));
        $totalDetected = 0;

        foreach ($idsList as $id) {
            $ebayListingModel = new self($id);
            if (!$ebayListingModel->id) {
                // Not found
                continue;
            }
            if ($ebayListingModel->product_id > 0) {
                // Already detected
                continue;
            }

            $productId = $this->findPrestaShopMapping($ebayListingModel->title, $ebayListingModel->sku, $defaultLanguageId);
            if ($productId > 0) {
                $ebayListingModel->product_id = $productId;
                $ebayListingModel->save();
                $totalDetected ++;
            }
        }

        return $totalDetected;
    }

    public function detectAllByTitleSku()
    {
        $sql = 'SELECT id FROM ' . _DB_PREFIX_ . 'prestabay_ebay_listings WHERE product_id is NULL or product_id = 0';

        $rows = Db::getInstance()->ExecuteS($sql, true, false);
        $ids = array();
        foreach ($rows as $row) {
            $ids[] = $row['id'];
        }

        return array(count($ids), $this->detectByTitleSku($ids));
    }

}
<?php

/**
 * File ProfileProductModel.php
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
 * Class used for generate api request array for Prestashop product item.
 *
 * When provied parameters to contructor loaded Product and Profile model
 */
class ProfileProductModel
{

    /**
     * Loaded profile model
     * @var ProfilesModel
     */
    protected $_profileModel = null;

    /**
     * Loaded product model
     * @var ProductCore
     */
    protected $_productModel = null;

    /** @var Selling_ListModel */
    protected $_sellingModel = null;

    /**
     * @var Selling_ProductsModel
     */
    protected $_sellingProductModel = null;

    /**
     * @var MarketplacesModel
     */
    protected $marketplace = null;

    protected $_langId = null;


    /* Cache for some variables */
    protected $_savedTitle = null;
    protected $_savedSubtitle = null;
    protected $_savedQty = null;
    protected $_linkInstance = null;
    protected $_calculatedVariation = null;
    protected $_calculatedVariationImages = null;

    /** @var ProductEbayDataModel  */
    protected $_productPrestaBayInformation = null;

    /**
     *
     * @param Selling_ListModel $sellingModel sellected list model
     * @param Selling_ProductsModel $sellingProductModel selling product model
     */
    public function __construct($sellingModel, $sellingProductModel)
    {

        if (!$sellingModel || !$sellingProductModel) {
            return;
        }

        $this->_sellingModel = $sellingModel;

        $this->_sellingProductModel = $sellingProductModel;

        $this->_profileModel = new ProfilesModel($sellingModel->profile);

        $this->_productModel = new Product($sellingProductModel->product_id, $sellingModel->language);

        $marketplaceId = $this->_profileModel->ebay_site;

        $this->marketplace = new MarketplacesModel($marketplaceId);

        $shopId = 0;
        if (CoreHelper::isPS15()) {
            $shopId = (int)Context::getContext()->shop->id;
        }

        $this->_productPrestaBayInformation = ProductEbayDataModel::loadByProductStoreId($sellingProductModel->product_id, $shopId);

        $this->_langId = $sellingModel->language;
        // this we load specific model for profile and product
    }

    public function setLangId($langId)
    {
        $this->_langId = $langId;
    }

    public function setProduct($productId, $langId = null)
    {
        $usedLangId = is_null($langId) ? $this->getLangId() : $langId;
        $this->_productModel = new Product($productId, $usedLangId);

        $shopId = 0;
        if (CoreHelper::isPS15()) {
            $shopId = (int)Context::getContext()->shop->id;
        }

        $this->_productPrestaBayInformation = ProductEbayDataModel::loadByProductStoreId($productId, $shopId);
    }

    public function setProfile($profile)
    {
        $this->_profileModel = $profile;
    }

    /**
     *
     * @return ProfilesModel
     */
    public function getProfile()
    {
        return $this->_profileModel;
    }

    /**
     * @return ProductCore
     */
    public function getProduct()
    {
        return $this->_productModel;
    }

    /**
     * Return assigned  marketplace for Selling List / Selling Profile
     *
     * @return MarketplacesModel
     */
    public function getMarketplace()
    {
        return $this->marketplace;
    }

    /**
     * @return ProductEbayDataModel
     */
    public function getProductPrestaBayInformation()
    {
        return $this->_productPrestaBayInformation;
    }

    /**
     *
     * @return Selling_ListModel
     */
    public function getSellingList()
    {
        return $this->_sellingModel;
    }

    /**
     *
     * @return Selling_ProductsModel
     */
    public function getSellingProduct()
    {
        return $this->_sellingProductModel;
    }

    public function getLangId()
    {
        return $this->_langId;
    }

    /**
     * Check that product we try to list is one of combination
     */
    public function isAttributeListing()
    {
        return $this->getSellingList() && $this->getSellingList()->attribute_mode == Selling_ListModel::ATTRIBUTE_MODE_SEPARATE_LISTINGS
        && $this->getSellingProduct()->product_id_attribute > 0;
    }

    // ######################################################################
    // Profile variables

    public function getTitle()
    {
        if (is_null($this->_savedTitle)) {
            $this->_savedTitle = $this->parseAttributes($this->getProfile()->item_title);
            if ($this->getProfile()->remove_more_80 == 1) {
                $this->_savedTitle = mb_substr($this->_savedTitle, 0, 80);
            }
        }

        return $this->_savedTitle;
    }

    public function getSubtitle()
    {
        if (is_null($this->_savedSubtitle)) {
            $this->_savedSubtitle = $this->parseAttributes($this->getProfile()->subtitle);
        }

        return $this->_savedSubtitle;
    }

    public function getSku()
    {
        $skuSetting = $this->getProfile()->item_sku;
        if ($skuSetting == ProfilesModel::ITEM_SKU_MODE_REFERENCE) {
            if (!$this->isAttributeListing()) {
                return $this->getProduct()->reference;
            } else {
                // Attribute listing
                $combinations = CoreHelper::getAttributeCombinationsById($this->getProduct(),
                    $this->getSellingProduct()->product_id_attribute,
                    $this->_langId);
                if (count($combinations) > 0) {
                    /// Get first combination item
                    $firstCombination = reset($combinations);
                    if (isset($firstCombination['reference']) && $firstCombination['reference'] != "") {
                        return $firstCombination['reference'];
                    }
                }
            }
        }
        return "";
    }

    public function getListingType()
    {
        $auctionType = $this->getProfile()->auction_type;
        switch ($auctionType) {
            case ProfilesModel::AUCTION_TYPE_FIXEDPRICE:
                return "FixedPriceItem";
            case ProfilesModel::AUCTION_TYPE_CHINESE:
                return "Chinese";
        }
        throw new Exception(L::t("Invalid Auction Type"));
    }

    public function getQty($skipValidation = false)
    {
        if (is_null($this->_savedQty)) {
            switch ($this->getProfile()->item_qty_mode) {
                case ProfilesModel::ITEM_QTY_MODE_SINGLE:
                    if ($this->getProfile()->auction_type == ProfilesModel::AUCTION_TYPE_CHINESE) {
                        $originProductQTY = $this->_getPrestaProductQTY();
                        $customValue = 1;
                        $this->_savedQty = $originProductQTY < $customValue ? $originProductQTY : $customValue;
                    } else {
                        $this->_savedQty = 1;
                    }
                    break;
                case ProfilesModel::ITEM_QTY_MODE_CUSTOM;
                    $this->_savedQty = (int) $this->getProfile()->item_qty_value;
                    break;

                case ProfilesModel::ITEM_QTY_MODE_NOT_MORE_THAT;
                    $originProductQTY = $this->_getPrestaProductQTY();

                    $customValue = (int) $this->getProfile()->item_qty_value;

                    $this->_savedQty = $originProductQTY < $customValue ? $originProductQTY : $customValue;
                    break;

                case ProfilesModel::ITEM_QTY_MODE_RESERVED_VALUE;
                    $originProductQTY = $this->_getPrestaProductQTY();

                    $customValue = (int) $this->getProfile()->item_qty_value;

                    $resultQty = $originProductQTY - $customValue;
                    $this->_savedQty = $resultQty < 0 ? 0 : $resultQty;
                    break;

                    break;
                default:
                case ProfilesModel::ITEM_QTY_MODE_PRODUCT:
                    $this->_savedQty = $this->_getPrestaProductQTY();
            }
        }
//        if (!$skipValidation && $this->_savedQty == 0) {
//            throw new Exception(L::t("This item has zero QTY"));
//        }
        return $this->_savedQty;
    }

    protected function _getPrestaProductQTY()
    {
        if (!$this->isAttributeListing()) {
            $originProductQTY = Product::getQuantity($this->getProduct()->id, (int) $this->getSellingProduct()->product_id_attribute > 0 ? (int) $this->getSellingProduct()->product_id_attribute : null);

            if (isset($this->getProduct()->minimal_quantity) && $this->getProduct()->minimal_quantity > 1) {
                $originProductQTY = floor($originProductQTY / $this->getProduct()->minimal_quantity);
            }
            return $originProductQTY;
        } else {
            $variaions = VariationHelper::generateProductCombinationList($this->getProduct(), $this->_langId);
            return isset($variaions[$this->getSellingProduct()->product_id_attribute]) ? $variaions[$this->getSellingProduct()->product_id_attribute]['qty'] : 0;
        }
    }

    public function getDescription()
    {
        switch ($this->getProfile()->item_description_mode) {
            case ProfilesModel::ITEM_DESCRIPTION_MODE_CUSTOM:
                return $this->parseAttributes($this->getProfile()->item_description_custom);

            case ProfilesModel::ITEM_DESCRIPTION_MODE_PRODUCT:
                return $this->getProduct()->description[$this->_langId];

            case ProfilesModel::ITEM_DESCRIPTION_MODE_TEMPLATE:
                $descriptionTemplate = new Description_TemplateModel($this->getProfile()->description_template_id);
                return $this->parseAttributes($descriptionTemplate->template);
        }
        return "";
    }

    public function getStartPrice()
    {
        $ppPrice = $this->_getPrestaShopProductPrice();

        switch ($this->getProfile()->price_start) {
            case ProfilesModel::PRICE_MODE_ORIGINAL_PRICE:
                return $this->getOriginalPrestaShopProductPrice() * $this->getProfile()->price_start_multiply;

            case ProfilesModel::PRICE_MODE_PRODUCT;
                return $ppPrice * $this->getProfile()->price_start_multiply;

            case ProfilesModel::PRICE_MODE_WHOLESALE_PRICE:
                return $this->getProduct()->wholesale_price * $this->getProfile()->price_start_multiply;

            case ProfilesModel::PRICE_MODE_CUSTOM:
                return $this->getProfile()->price_start_custom * $this->getProfile()->price_start_multiply;

            case ProfilesModel::PRICE_MODE_TEMPLATE:

                return $this->getProfile()->price_start_multiply * Price_TemplateModel::getParsedPrice($this->getProfile()->price_start_template, $ppPrice, $this->getProduct()->weight);
                break;
        }

        return 0;
    }

    public function getReservePrice()
    {
        if ($this->getProfile()->auction_type == ProfilesModel::AUCTION_TYPE_FIXEDPRICE) {
            return 0;
        }
        $ppPrice = $this->_getPrestaShopProductPrice();

        switch ($this->getProfile()->price_reserve) {
            case ProfilesModel::PRICE_MODE_ORIGINAL_PRICE:
                return $this->getOriginalPrestaShopProductPrice() * $this->getProfile()->price_reserve_multiply;

            case ProfilesModel::PRICE_MODE_PRODUCT;
                return $ppPrice * $this->getProfile()->price_reserve_multiply;

            case ProfilesModel::PRICE_MODE_WHOLESALE_PRICE:
                return $this->getProduct()->wholesale_price * $this->getProfile()->price_reserve_multiply;

            case ProfilesModel::PRICE_MODE_CUSTOM:
                return $this->getProfile()->price_reserve_custom * $this->getProfile()->price_reserve_multiply;
        }

        return 0;
    }

    public function getBuynowPrice()
    {

        if ($this->getProfile()->auction_type == ProfilesModel::AUCTION_TYPE_FIXEDPRICE) {
            return 0;
        }

        $ppPrice = $this->_getPrestaShopProductPrice();

        switch ($this->getProfile()->price_buynow) {
            case ProfilesModel::PRICE_MODE_ORIGINAL_PRICE:
                return $this->getOriginalPrestaShopProductPrice() * $this->getProfile()->price_buynow_multiply;

            case ProfilesModel::PRICE_MODE_PRODUCT;
                return $ppPrice * $this->getProfile()->price_buynow_multiply;

            case ProfilesModel::PRICE_MODE_WHOLESALE_PRICE:
                return $this->getProduct()->wholesale_price * $this->getProfile()->price_buynow_multiply;

            case ProfilesModel::PRICE_MODE_CUSTOM:
                return $this->getProfile()->price_buynow_custom * $this->getProfile()->price_buynow_multiply;
        }

        return 0;
    }

    protected function getOriginalPrestaShopProductPrice()
    {
        $originalPrice = Db::getInstance()->getValue("
                			SELECT price FROM `" . _DB_PREFIX_ . "product`
                    			WHERE `id_product` = " . (int)$this->getProduct()->id, false);

        return $originalPrice;
    }

    protected function _getPrestaShopProductPrice()
    {
        $originalPrice = $this->getProduct()->getPrice(true, $this->isAttributeListing() ? $this->getSellingProduct()->product_id_attribute : null);
        if (isset($this->getProduct()->minimal_quantity) && $this->getProduct()->minimal_quantity > 1) {
            // If used minimal qty then we sell on eBay pack of product with
            // qty = qty/min.qty. So such each pack have price equal number of item in pack
            $originalPrice = $originalPrice * $this->getProduct()->minimal_quantity;
        }

        return $originalPrice;
    }

    public function getImages()
    {
        $coverImage = null;
        $productImages = array();
        $currentCount = 1;
        $maxCount = $this->getProfile()->item_image_count;
        if (!$this->isAttributeListing()) {
            // Prestashop product images objects
            $images = $this->getProduct()->getImages($this->_langId);

            $usedIds = array();
            foreach ($images AS $k => $image) {
                $imgId = (int) ($this->getProduct()->id) . '-' . $image['id_image'];
                if (isset($usedIds[$imgId])) {
                    continue;
                }
                $usedIds[$imgId] = 1;
                $pathToImage = $this->_getImageLink($this->getProduct()->link_rewrite, $imgId, $this->getProfile()->ps_image_type);

                if ($image['cover'] && $this->getProfile()->item_image == ProfilesModel::IMAGE_MODE_MAIN) {
                    $coverImage = $pathToImage;
                } else {
                    if ($currentCount < $maxCount) {
                        $productImages[] = $pathToImage;
                        $currentCount++;
                    }
                }
            }
        } else {
            // Attribute list mode. We need to get only specific product attribute.
            $attributeImages = Product::_getAttributeImageAssociations($this->getSellingProduct()->product_id_attribute);
            if (empty($attributeImages)) {
                // no specific attributes image
                // so get default one
                $images = $this->getProduct()->getImages($this->_langId);

                foreach ($images AS $k => $image) {
                    if ($image['cover']) {
                        $imgId = (int) ($this->getProduct()->id) . '-' . $image['id_image'];
                        $pathToImage = $this->_getImageLink($this->getProduct()->link_rewrite, $imgId, $this->getProfile()->ps_image_type);
                        $coverImage = $pathToImage;
                    }
                }
            } else {
                // We have specific attribute image so work with it
                foreach ($attributeImages as $singleAttributeImageId) {
                    $imgId = (int) ($this->getProduct()->id) . '-' . $singleAttributeImageId;

                    $pathToImage = $this->_getImageLink($this->getProduct()->link_rewrite, $imgId, $this->getProfile()->ps_image_type);
                    if (!$coverImage) {
                        $coverImage = $pathToImage;
                    } else {
                        if ($currentCount < $maxCount) {
                            $productImages[] = $pathToImage;
                            $currentCount++;
                        }
                    }
                }
            }
        }

//        $isSupersize = count($productImages) > 1;
        return array(
            'cover' => $coverImage,
            'gallery' => $productImages,
            'galleryType' => $this->getProfile()->gallery_type,
            'photoDisplay' => $this->getProfile()->photo_display,
//            'supersize' => $isSupersize
        );
    }

    // ########################################################################
    // Template parser

    public function parseAttributes($inputString)
    {
        return ReplaceHelper::parseAttributes($inputString, $this);
    }

    public function getProductCoverImageLink()
    {
        // Prestashop product images objects
        $images = $this->getProduct()->getImages($this->_langId);

        foreach ($images AS $k => $image) {
            if ($image['cover']) {
                $imgId = (int) ($this->getProduct()->id) . '-' . $image['id_image'];
                return $this->_getImageLink($this->getProduct()->link_rewrite, $imgId, $this->getProfile()->ps_image_type);
            }
        }
        return '';
    }

    public function getProductImageNumber($index = 1, $imageType = null)
    {
        // Prestashop product images objects
        $images = $this->getProduct()->getImages($this->_langId);
        $currentIndex = 1;
        $usedIds = array();
        foreach ($images AS $k => $image) {
            $imgId = (int) ($this->getProduct()->id) . '-' . $image['id_image'];
            if (isset($usedIds[$imgId])) {
                continue;
            }
            $usedIds[$imgId] = 1;

            if ($index == $currentIndex) {
                if (is_null($imageType)) {
                    $imageType = $this->getProfile()->ps_image_type;
                }
                return $this->_getImageLink($this->getProduct()->link_rewrite, $imgId, $imageType);
            }
            $currentIndex++;
        }
        return '';
    }

    /**
     * Get Product images list prepared for use in gallery build in into "Description"
     *
     * @return array
     */
    public function getProductImagesList($totalImages, $coverType, $previewType)
    {
        $imagesList = array();
        $cover = null;
        $images = $this->getProduct()->getImages($this->_langId);
        $imagesAdded = 0;
        $usedIds = array();

        foreach ($images AS $k => $image) {
            $imgId = (int) ($this->getProduct()->id) . '-' . $image['id_image'];
            if (isset($usedIds[$imgId])) {
                continue;
            }
            $usedIds[$imgId] = 1;
            if ($imagesAdded < $totalImages || $totalImages == 0) {
                $imagesList[] = array(
                    'small' => $this->_getImageLink($this->getProduct()->link_rewrite, $imgId, $previewType),
                    'big' => $this->_getImageLink($this->getProduct()->link_rewrite, $imgId, $coverType),
                );
                $imagesAdded++;
            }
            if ($image['cover']) {
                $cover = $this->_getImageLink($this->getProduct()->link_rewrite, $imgId, $coverType);
            }
        }

        if (!$cover && isset($imagesList[0]['big'])) {
            $cover = $imagesList[0]['big'];
        }

        return array(
            'cover' => $cover,
            'list' => $imagesList
        );
    }

    public function getVariations()
    {
        if (!is_null($this->_calculatedVariation)) {
            return $this->_calculatedVariation;
        }

        return $this->_calculatedVariation = VariationHelper::getProductCombinationList(
            $this->getProduct(), $this->_langId, $this->getProfile());
    }

    public function getVariationsImages()
    {
        if (!is_null($this->_calculatedVariationImages)) {
            return $this->_calculatedVariationImages;
        }

        $variationImages = VariationHelper::getVariationImages(
            $this->getProduct(), $this->_langId);

        if (count($variationImages) == 0) {
            return array();
        }

        $returnArray = array();
        $imagesIds = reset($variationImages);
        $indexKey = key($variationImages);
        $returnArray[$indexKey] = array();
        foreach ($imagesIds as $groupKey => $groupImages) {
            foreach ($groupImages as $singleAttributeImageId) {
                $imgId = (int) ($this->getProduct()->id) . '-' . $singleAttributeImageId;
                $pathToImage = $this->_getImageLink($this->getProduct()->link_rewrite, $imgId, $this->getProfile()->ps_image_type);
                $returnArray[$indexKey][$groupKey][] = $pathToImage;
            }
        }

        return $this->_calculatedVariationImages = $returnArray;

    }

    public function setCalculatedVariation($calculatedVariation)
    {
        $this->_calculatedVariation = $calculatedVariation;
    }

    /**
     * Calculate Product specific that need to be send to eBay.
     * Checking for set custom value or some predefined values from product field
     * information.
     *
     * @return array()
     */
    public function getProductSpecifics($specificsList, $customValues)
    {
        $specificToSend = array();
        if (!is_array($specificsList) || empty($specificsList)) {
            return $specificToSend;
        }
        foreach ($specificsList as $specificKey => $specificValue) {
            if (is_array($specificValue)) {
                // Standard multi-select
                $specificToSend[$specificKey] = $specificValue;
                $specificValueArray = array();
                foreach ($specificValue as $value) {
                    $specificValueArray[] = $this->getSpecificByValue($value, $customValues, $specificKey);
                }
                $specificToSend[$specificKey] = $specificValueArray;
            } else {
                $specificToSend[$specificKey] = $this->getSpecificByValue($specificValue, $customValues, $specificKey);
            }
        }

        return $specificToSend;
    }

    public function getSpecificByValue($specificValue, $customValues, $specificKey)
    {
        $sv = "";
        if ($specificValue === ProfilesModel::SPECIFIC_CUSTOM_VALUE_KEY) {
            // Custom value that set into edit box
            if (isset($customValues[$specificKey])) {
                $sv = $customValues[$specificKey];
            }
        } else if (strpos($specificValue, ProfilesModel::SPECIFIC_CUSTOM_FEATURE_PREFIX) === 0) {
            // Found feature
            $key = substr($specificValue, strlen(ProfilesModel::SPECIFIC_CUSTOM_FEATURE_PREFIX));
            $value = ReplaceHelper::getFeatureValue($key, $this);
            if (!is_null($value) && $value != "") {
                $sv = $value;
            }
        } else if (strpos($specificValue, ProfilesModel::SPECIFIC_CUSTOM_ATTRIBUTE_PREFIX) === 0) {
            // Custom attribute found key
            $key = substr($specificValue, strlen(ProfilesModel::SPECIFIC_CUSTOM_ATTRIBUTE_PREFIX));
            $value = ReplaceHelper::getAttributeValue($key, $this);
            if (!is_null($value) && $value != "") {
                $sv = $value;
            }
        } else {
            // Standard
            $value = ReplaceHelper::parseAttributes($specificValue, $this);
            $sv = $value;
        }
        return $sv;
    }

    protected function _getImageLink($name, $ids, $type = null)
    {
        if (is_null($this->_linkInstance)) {
            if (CoreHelper::isPS15()) {
                $this->_linkInstance = new Link(null, Tools::getProtocol());
            } else {
                $this->_linkInstance = new Link();
            }
        }

        $imgPathValue = $this->_linkInstance->getImageLink($name, $ids, $type);
        if (CoreHelper::isPS15()) {
//            if (strpos($imgPathValue, "www.") === false) {
//                $imgPathValue = str_replace("http://", "http://www.", $imgPathValue);
//            }
        }

        if (strpos($imgPathValue, 'http://') === false && strpos($imgPathValue, 'https://') == false) {
            // Not full path to image. Possible it's PrestaShop 1.3?
            $imgPathValue = _PS_BASE_URL_ . $imgPathValue;
        }

//        $imgPathValue = str_replace("http://", "https://", $imgPathValue);

        return $imgPathValue;
    }

    public function getCalculatedShipping()
    {
        $profileData = $this->getProfile();
        $calculatedData = array(
            'measurement' => $profileData->shipping_calculated_measurement,
            'package' => $profileData->shipping_calculated_package,
            'depth' => $this->_getDepth(),
            'length' => $this->_getLength(),
            'width' => $this->_getWidth(),
            'weight' => $this->_getWeight(),
            'postal' => $profileData->shipping_calculated_postal,
        );

        if ($profileData->shipping_local_type == ProfilesModel::SHIPPING_TYPE_CALCULATED) {
            $calculatedData['handlingLocal'] = (double)$profileData->shipping_calculated_local_handling_cost;
        }

        if ($profileData->shipping_int_type == ProfilesModel::SHIPPING_TYPE_CALCULATED) {
            $calculatedData['handlingInt'] = (double)$profileData->shipping_calculated_int_handling_cost;
        }

        return $calculatedData;
    }

    protected function _getDepth()
    {
        if ($this->getProfile()->shipping_calculated_depth == ProfilesModel::SHIPPING_CALCULATED_DEPTH_MODE_CUSTOM) {
            return $this->getProfile()->shipping_calculated_depth_custom;
        }

        return $this->getProduct()->depth;
    }

    protected function _getLength()
    {
        if ($this->getProfile()->shipping_calculated_length == ProfilesModel::SHIPPING_CALCULATED_LENGTH_MODE_CUSTOM) {
            return $this->getProfile()->shipping_calculated_length_custom;
        }

        return $this->getProduct()->height;
    }

    protected function _getWidth()
    {
        if ($this->getProfile()->shipping_calculated_width == ProfilesModel::SHIPPING_CALCULATED_WIDTH_MODE_CUSTOM) {
            return $this->getProfile()->shipping_calculated_width_custom;
        }

        return $this->getProduct()->width;
    }

    protected function _getWeight()
    {
        if ($this->getProfile()->shipping_calculated_weight == ProfilesModel::SHIPPING_CALCULATED_WEIGHT_MODE_CUSTOM) {
            return $this->getProfile()->shipping_calculated_weight_custom;
        }

        return $this->getProduct()->weight;
    }

}
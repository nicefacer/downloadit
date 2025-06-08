<?php

/**
 * File Abstract.php
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
abstract class Services_Item_Abstract implements Services_Request
{

    /**
     * @var ProfileProductModel
     */
    protected $_profileProduct;

    public function __construct(ProfileProductModel $profileProduct)
    {
        $this->_profileProduct = $profileProduct;
    }

    /**
     *
     * @return ProfileProductModel
     */
    public function getProfileProduct()
    {
        return $this->_profileProduct;
    }

    /**
     * Return true when current listing is variation
     *
     * @return boolean
     */
    public function isVariationListing()
    {
        if ($this->getProfileProduct()->getProfile()->use_multivariation) {
            $variationList = $this->getProfileProduct()->getVariations();
            if ($variationList != array()) {
                return true;
            }
        }
        return false;
    }

    /* Helper function for get information about eBay product */

    protected function _getTitle()
    {
        return array('title' => $this->getProfileProduct()->getTitle());
    }

    protected function _getSubtitle()
    {
        return array('subtitle' => $this->getProfileProduct()->getSubtitle());
    }

    protected function _getPrivate()
    {
        return array('private' => (int) $this->getProfileProduct()->getProfile()->private_listing);
    }

    protected function _getSku()
    {
        return array('sku' => $this->getProfileProduct()->getSku());
    }

    protected function _getCurrency()
    {
        return array('currency' => $this->getProfileProduct()->getProfile()->item_currency);
    }

    protected function _getItemFrom()
    {
        return array('from' => array(
                'country' => $this->getProfileProduct()->getProfile()->shipping_country,
                'street' => $this->getProfileProduct()->getProfile()->shipping_location
        ));
    }

    protected function _getListingType()
    {
        return array('type' => $this->getProfileProduct()->getListingType());
    }

    protected function _getListingDuration()
    {
        return array("duration" => $this->getProfileProduct()->getProfile()->auction_duration);
    }

    protected function _getCrossBorderTrade()
    {
        return array('cross_border_trade' => $this->getProfileProduct()->getProfile()->cross_border_trade);
    }

    protected function _getProductSpecific()
    {
        if ($this->getProfileProduct()->getProfile()->ebay_category_mode == ProfilesModel::EBAY_CATEGORY_MODE_MAPPING) {
            $categoryMappingInfo = $this->getCategoryMappingModeInfo($this->getProfileProduct()->getProfile()->ebay_category_mapping_id, $this->getProfileProduct()->getProduct()->id_category_default);
            $specificsList = isset($categoryMappingInfo['product_specifics'])? $categoryMappingInfo['product_specifics']: array();
            $customValues = isset($categoryMappingInfo['product_specifics_custom']) ? $categoryMappingInfo['product_specifics_custom']: array();
        } else {
            $specificsList = $this->getProfileProduct()->getProfile()->getProductSpecifics();
            $customValues = $this->getProfileProduct()->getProfile()->getProductSpecificsCustom();
        }

        if ($this->getProfileProduct()->isAttributeListing()) {
            $combinations = CoreHelper::getAttributeCombinationsById($this->getProfileProduct()->getProduct(),
                $this->getProfileProduct()->getSellingProduct()->product_id_attribute,
                $this->getProfileProduct()->getLangId());
            foreach ($combinations as $singleCombination) {
                if (isset($specificsList[$singleCombination['group_name']]) && ($specificsList[$singleCombination['group_name']] == "" || is_array($specificsList[$singleCombination['group_name']]))) {
                    if (is_array($specificsList[$singleCombination['group_name']])) {
                        $specificsList[$singleCombination['group_name']] = array();
                        $specificsList[$singleCombination['group_name']][] = $singleCombination['attribute_name'];
                    } else {
                        $specificsList[$singleCombination['group_name']] = $singleCombination['attribute_name'];
                    }
                }
            }
        }

        return array('specifics' => $this->getProfileProduct()->getProductSpecifics($specificsList, $customValues));
    }

    protected function _getProductSpecificAttribute()
    {
        return array(
            'specifics_attribute' => array(
                'attribute_set_id' => $this->getProfileProduct()->getProfile()->attribute_set_id,
                'list' => $this->getProfileProduct()->getProfile()->getProductSpecificsAttribute()
            )
        );
    }

    protected $categoryMappingInfo = null;

    protected function getCategoryMappingModeInfo($ebayCategoryMappingId, $psCategoryId)
    {
        if (!$this->categoryMappingInfo) {
            $this->categoryMappingInfo = Mapping_CategoryModel::getMappingRow($ebayCategoryMappingId, $psCategoryId);

        }
        if (!$this->categoryMappingInfo) {
            // Generate global error
            throw new Exception(L::t("Category mapping not found for product category"));
        }

        return $this->categoryMappingInfo;
    }

    protected function _getCatalogCategory()
    {
        $return = array();

        if ($this->getProfileProduct()->getProfile()->ebay_category_mode == ProfilesModel::EBAY_CATEGORY_MODE_MAPPING) {
            $categoryMappingInfo = $this->getCategoryMappingModeInfo($this->getProfileProduct()->getProfile()->ebay_category_mapping_id, $this->getProfileProduct()->getProduct()->id_category_default);
            $return['primary'] = $categoryMappingInfo['primary'];

            if ($categoryMappingInfo['secondary'] > 0) {
                $return['secondary'] = $categoryMappingInfo['secondary'];
            }
        } else {
            // Profile mapping mode
            $return['primary'] = $this->getProfileProduct()->getProfile()->ebay_primary_category_value;
            $secondaryCategoryValue = $this->getProfileProduct()->getProfile()->ebay_secondary_category_value;
            if ($secondaryCategoryValue > 0) {
                $return['secondary'] = $secondaryCategoryValue;
            }
        }

        return array('category' => $return);
    }

    protected function _getStoreCategories()
    {
        $return         = array();
        $primaryStore   = 0;
        $secondaryStore = 0;

        if ($this->getProfileProduct()->getProfile()->ebay_store_mode == ProfilesModel::EBAY_STORE_MODE_PROFILE) {
            $primaryStore   = $this->getProfileProduct()->getProfile()->ebay_store_category_main;
            $secondaryStore = $this->getProfileProduct()->getProfile()->ebay_store_category_secondary;
        } else  if ($this->getProfileProduct()->getProfile()->ebay_store_mode == ProfilesModel::EBAY_STORE_MODE_PRODUCT) {
            $primaryStore   = $this->getProfileProduct()->getProductPrestaBayInformation()->ebay_store_category_main_id;
            $secondaryStore = $this->getProfileProduct()->getProductPrestaBayInformation()->ebay_store_category_secondary_id;
        } else if ($this->getProfileProduct()->getProfile()->ebay_store_mode == ProfilesModel::EBAY_STORE_MODE_MAPPING) {
            $mappingRow = Mapping_EbayStoreCategoriesModel::getMappingRow($this->getProfileProduct()->getProfile()->ebay_store_mapping_id, $this->getProfileProduct()->getProduct()->id_category_default);

            $primaryStore   = isset($mappingRow['ebay_store_category_id'])?$mappingRow['ebay_store_category_id']:0;
            $secondaryStore   = isset($mappingRow['ebay_secondary_category_id'])?$mappingRow['ebay_secondary_category_id']:0;
        }

        if ($primaryStore > 0) {
            $return['primary'] = $primaryStore;
        }
        if ($secondaryStore > 0) {
            $return['secondary'] = $secondaryStore;
        }

        return array('store_category' => $return);
    }

    protected function _getQuantity()
    {
        return array('qty' => $this->getProfileProduct()->getQty());
    }

    protected function _getPrice()
    {
        $returnPriceArray = array();
        if ($this->getProfileProduct()->getProfile()->auction_type == ProfilesModel::AUCTION_TYPE_CHINESE) {
            $returnPriceArray = array('start' => $this->getProfileProduct()->getStartPrice(),
                'reserve' => $this->getProfileProduct()->getReservePrice(),
                'buynow' => $this->getProfileProduct()->getBuynowPrice()
            );
        } else if ($this->getProfileProduct()->getProfile()->auction_type == ProfilesModel::AUCTION_TYPE_FIXEDPRICE) {
            $returnPriceArray = array(
                'start' => $this->getProfileProduct()->getStartPrice(),
                'retailPrice' => 0
            );

            $priceDiscount = $this->getProfileProduct()->getProfile()->price_discount;
            if ($priceDiscount > 0 && $priceDiscount <100) {
                $returnPriceArray['retailPrice'] = $returnPriceArray['start'];
                $returnPriceArray['start'] = $returnPriceArray['start'] * (1 - $priceDiscount / 100);
            }
        }

        return $returnPriceArray;
    }

    protected function _getBestOffer()
    {
        $returnArray = array();
        if ($this->getProfileProduct()->getProfile()->best_offer_enabled == ProfilesModel::BEST_OFFER_YES) {
            $returnArray = array(
                'best_offer' => true
            );

            if ((float) $this->getProfileProduct()->getProfile()->best_offer_minimum_price > 0) {
                !is_array($returnArray['best_offer']) && $returnArray['best_offer'] = array();

                $returnArray['best_offer']['minimum_price'] = (float) $this->getProfileProduct()->getProfile()->best_offer_minimum_price;
            }

            if ((float) $this->getProfileProduct()->getProfile()->best_offer_auto_accept_price > 0) {
                !is_array($returnArray['best_offer']) && $returnArray['best_offer'] = array();

                $returnArray['best_offer']['auto_accept_price'] = (float) $this->getProfileProduct()->getProfile()->best_offer_auto_accept_price;
            }
        }
        return $returnArray;
    }

    /**
     * Get listing enhancement
     */
    protected function _getEnhancement()
    {
        return array(
            'enhancement' => $this->getProfileProduct()->getProfile()->getEnhancement()
        );
    }

    /**
     * Get EAN number
     *
     * @return array
     */
    protected function _getEAN()
    {
        $profileProduct = $this->getProfileProduct();
        if ($profileProduct->getProfile()->ean != ProfilesModel::EAN_INCLUDE_YES) {
            return array();
        }

        $product = $profileProduct->getProduct();

        $eanValue = '';
        if ($profileProduct->isAttributeListing()) {
            $combinations = CoreHelper::getAttributeCombinationsById($product,
                $profileProduct->getSellingProduct()->product_id_attribute,
                $profileProduct->getLangId());
            if (count($combinations) > 0) {
                // Get first combination item
                $firstCombination = reset($combinations);
                if (isset($firstCombination['ean13'])) {
                    $eanValue = $firstCombination['ean13'];
                }
            }
        } else {
            $eanValue = $product->ean13;
        }

        if (!empty($eanValue) && $eanValue != 0) {
            return array(
                'ean' => $eanValue
            );
        }

        if ($profileProduct->getProfile()->identify_not_available == ProfilesModel::IDENTIFY_NOT_AVAILABLE_YES) {
            return array(
                'ean' => $profileProduct->getMarketplace()->identify_unavailable_text
            );
        }

        return array();
    }

    /**
     * Get UPC
     *
     * @return array
     */
    protected function _getUPC()
    {
        $profileProduct = $this->getProfileProduct();
        if ($profileProduct->getProfile()->upc != ProfilesModel::UPC_INCLUDE_YES) {
            return array();
        }
        $product = $profileProduct->getProduct();
        $upcValue = '';

        if ($profileProduct->isAttributeListing()) {
            $combinations = CoreHelper::getAttributeCombinationsById($product,
                $profileProduct->getSellingProduct()->product_id_attribute,
                $profileProduct->getLangId());
            if (count($combinations) > 0) {
                // Get first combination item
                $firstCombination = reset($combinations);
                if (isset($firstCombination['upc'])) {
                    $upcValue = $firstCombination['upc'];
                }
            }
        } else {
            $upcValue = $product->upc;
        }

        if (!empty($upcValue) && $upcValue != 0) {
            return array(
                'upc' => $upcValue
            );
        }

        if ($profileProduct->getProfile()->identify_not_available == ProfilesModel::IDENTIFY_NOT_AVAILABLE_YES) {
            return array(
                'upc' => $profileProduct->getMarketplace()->identify_unavailable_text
            );

        }

        return array();
    }

    /**
     * Get ISBN
     *
     * @return array
     */
    protected function _getISBN()
    {
        if ($this->getProfileProduct()->getProfile()->isbn != ProfilesModel::ISBN_INCLUDE_YES) {
            return array();
        }

        $data = AttributesDataModel::loadByProductId($this->getProfileProduct()->getProduct()->id);
        if (isset($data['isbn']) && !empty($data['isbn'])) {
            return array(
                'isbn' => $data['isbn']
            );
        }

        if ($this->getProfileProduct()->getProfile()->identify_not_available == ProfilesModel::IDENTIFY_NOT_AVAILABLE_YES) {
            return array(
                'isbn' => $this->getProfileProduct()->getMarketplace()->identify_unavailable_text
            );
        }

        return array();
    }

    /**
     * Get MPN
     *
     * @return array
     */
    protected function _getMPN()
    {
        if ($this->getProfileProduct()->getProfile()->mpn != ProfilesModel::MPN_INCLUDE_YES) {
            return array();
        }

        $data = AttributesDataModel::loadByProductId($this->getProfileProduct()->getProduct()->id);
        if (isset($data['mpn']) && !empty($data['mpn'])) {
            return array(
                'mpn' => $data['mpn'],
                'mpnBrand' => $this->getProfileProduct()->getProduct()->manufacturer_name
            );
        }

        if ($this->getProfileProduct()->getProfile()->identify_not_available == ProfilesModel::IDENTIFY_NOT_AVAILABLE_YES) {
            return array(
                'mpn' => $this->getProfileProduct()->getMarketplace()->identify_unavailable_text
            );
        }

        return array();
    }

    protected function _getGiftService()
    {
        $profile = $this->getProfileProduct()->getProfile();

        if ($profile->gift_icon == ProfilesModel::GIFT_ICON_YES) {
            return array(
                'gift' => array(
                    'icon' => true,
                    'services' => $profile->getGiftServices()
                )
            );
        }
        return array();
    }

    protected function _getUnitInfo()
    {
        $profile = $this->getProfileProduct()->getProfile();
        $product = $this->getProfileProduct()->getProduct();
        if ($profile->unit_include == ProfilesModel::UNIT_INCLUDE_YES && $product->unit_price_ratio > 0) {
            return array(
                'unit' => array(
                    'type' => $profile->unit_type,
                    'value' => $product->unit_price_ratio
                )
            );
        }
        return array();
    }

    protected function _getReturnPolicy()
    {
        return array(
            'accepted_option' => $this->getProfileProduct()->getProfile()->returns_accepted,
            'refund' => $this->getProfileProduct()->getProfile()->refund,
            'return_with' => $this->getProfileProduct()->getProfile()->returns_within,
            'shipping_paid_by' => $this->getProfileProduct()->getProfile()->shipping_cost_paid_by,
            'restock_fee' => $this->getProfileProduct()->getProfile()->restock_fee,
            'description' => $this->getProfileProduct()->getProfile()->refund_description,
        );
    }

    protected function _getShippingDetails()
    {
        $returnArray = array(
            'dispatch_time' => $this->getProfileProduct()->getProfile()->shipping_dispatch
        );

        $localShipping = $this->getProfileProduct()->getProfile()->getLocalShipping();
        $intShipping = $this->getProfileProduct()->getProfile()->getInternationalShipping();

        if (count($localShipping) > 0) {
            $localShippingArray = array();
            foreach ($localShipping as $shippingService) {
                $localShippingArray[$shippingService['name']] = array(
                    'service' => $shippingService['name'],
                    'priority' => $shippingService['priority'],
                );
                if (!isset($shippingService['mode']) || $shippingService['mode'] == ProfilesModel::SHIPPING_MODE_CUSTOM_PRICE) {
                    $localShippingArray[$shippingService['name']]+= array(
                        'cost' => $shippingService['plain'],
                        'additional' => $shippingService['additional']
                    );
                } else {
                    // Proccess custom shipping template
                    $priceToShippingCalculate = $this->getProfileProduct()->getProduct()->getPrice();
                    $weightToShippingCalculate = $this->getProfileProduct()->getProduct()->weight;

                    $product = $this->getProfileProduct()->getProduct();
                    if (isset($product->minimal_quantity) && $product->minimal_quantity > 1) {
                        $priceToShippingCalculate = $priceToShippingCalculate * $product->minimal_quantity;
                        $weightToShippingCalculate = $weightToShippingCalculate * $product->minimal_quantity;
                    }
                    $calculationResult = Shipping_TemplateModel::calculateShippingCost(
                        (int)$shippingService['mode'],
                        $priceToShippingCalculate,
                        $weightToShippingCalculate
                    );

                    if ($calculationResult == false || !is_array($calculationResult)) {
                        // Remove this shipping from sending to eBay
                        unset($localShippingArray[$shippingService['name']]);
                        continue;
                    }

                    list($plain, $additional) = $calculationResult;
                    $localShippingArray[$shippingService['name']]+= array(
                        'cost' => $plain,
                        'additional' => $additional
                    );
                }
            }
            $returnArray['local'] = array_values($localShippingArray);
        }

        if (count($intShipping) > 0) {
            $intShippingArray = array();
            $index = 0;
            foreach ($intShipping as $intShippingService) {
                $key = $intShippingService['name']."_".$index;
                $index++;
                $intShippingArray[$key] = array(
                    'service' => $intShippingService['name'],
                    'priority' => $intShippingService['priority'],
                    'location' => isset($intShippingService['locations'])?$intShippingService['locations']:array(),
                );
                if (!isset($intShippingService['mode']) || $intShippingService['mode'] == ProfilesModel::SHIPPING_MODE_CUSTOM_PRICE) {
                    $intShippingArray[$key]+= array(
                        'cost' => $intShippingService['plain'],
                        'additional' => $intShippingService['additional']
                    );
                } else {
                    // Proccess custom shipping template
                    $priceToShippingCalculate = $this->getProfileProduct()->getProduct()->getPrice();
                    $weightToShippingCalculate = $this->getProfileProduct()->getProduct()->weight;

                    $product = $this->getProfileProduct()->getProduct();
                    if (isset($product->minimal_quantity) && $product->minimal_quantity > 1) {
                        $priceToShippingCalculate = $priceToShippingCalculate * $product->minimal_quantity;
                        $weightToShippingCalculate = $weightToShippingCalculate * $product->minimal_quantity;
                    }
                    $calculationResult = Shipping_TemplateModel::calculateShippingCost(
                        (int) $intShippingService['mode'],
                        $priceToShippingCalculate,
                        $weightToShippingCalculate
                    );

                    if ($calculationResult == false || !is_array($calculationResult)) {
                        // Remove this shipping from sending to eBay
                        unset($intShippingArray[$key]);
                        continue;
                    }

                    list($plain, $additional) = $calculationResult;
                    $intShippingArray[$key]+= array(
                        'cost' => $plain,
                        'additional' => $additional
                    );
                }
            }
            $returnArray['international'] = array_values($intShippingArray);
        }

        if (in_array($this->getProfileProduct()->getProfile()->ebay_site, array(1, 100, 2, 15, 210))) {
            $isLocalCalculated = $this->getProfileProduct()->getProfile()->shipping_local_type == ProfilesModel::SHIPPING_TYPE_CALCULATED;
            $isIntCalculated = $this->getProfileProduct()->getProfile()->shipping_int_type == ProfilesModel::SHIPPING_TYPE_CALCULATED;
            $calculatedMode = array(
               'islocalMode' => $isLocalCalculated,
               'isIntCalculated' =>$isIntCalculated,
            );
            if ($isLocalCalculated || $isIntCalculated) {
                $calculatedMode += $this->getProfileProduct()->getCalculatedShipping();
            }
            $returnArray['calculated'] = $calculatedMode;
        } else {
            $returnArray['calculated'] = false;
        }

        // Exclude location list
        $returnArray['exclude_location'] = $this->getProfileProduct()->getProfile()->getShippingExcludeLocation();
        $returnArray['allowed_location'] = $this->getProfileProduct()->getProfile()->getShippingAllowedLocation();

        $profile = $this->getProfileProduct()->getProfile();

        $returnArray['get_it_fast'] = $profile->get_it_fast;
        if (in_array($profile->ebay_site, array(1, 3))) {
            // Only for US & UK
            $returnArray['global_shipping'] = $profile->global_shipping;
        }

        if (in_array($profile->ebay_site, array(15, 71, 101))) {
            // Only for AU, IT, FR
            $returnArray['insurance'] = array(
                'fee' => $profile->insurance_fee,
                'option' => $profile->insurance_option,
                'international_fee' => $profile->insurance_international_fee,
                'international_option' => $profile->insurance_international_option,
            );
        }

        // Promotional shipping
        $returnArray['promotional'] = array(
           'local_include' => $profile->promotional_shipping_discount,
           'int_include' => $profile->promotional_int_shipping_discount,
           'local_profile' => $profile->shipping_discount_profile_id,
           'int_profile' => $profile->int_shipping_discount_profile_id,
        );

        return $returnArray;
    }

    protected function _getPaymentMethods()
    {

        $returnArray = array();
        $methods = $this->getProfileProduct()->getProfile()->getPaymentMethods();
        if (!is_array($methods)) {
            $methods = array();
        }

        $methodList = array();

        foreach ($methods as $method) {
            $methodList[] = $method;
        }
        $returnArray['methods'] = $methodList;

        if (in_array('PayPal', $methods)) {
            $returnArray['paypal_email'] = $this->getProfileProduct()->getProfile()->payment_paypal_email;
            $returnArray['autopay'] = $this->getProfileProduct()->getProfile()->autopay;
        }

        if (in_array('COD', $methods)) {
            $returnArray['cod_cost_italy'] = $this->getProfileProduct()->getProfile()->cod_cost_italy;
        }

        $returnArray['payment_instruction'] = $this->getProfileProduct()->getProfile()->payment_instruction;

        return $returnArray;
    }

    protected function _getSite()
    {
        return array('site' => $this->getProfileProduct()->getProfile()->getSiteKey());
    }

    protected function _getConditionID()
    {
        $conditionDescription = false;

        if ($this->getProfileProduct()->getProfile()->ebay_category_mode == ProfilesModel::EBAY_CATEGORY_MODE_MAPPING) {
            $categoryMappingInfo = $this->getCategoryMappingModeInfo(
                $this->getProfileProduct()->getProfile()->ebay_category_mapping_id,
                $this->getProfileProduct()->getProduct()->id_category_default
            );

            $mappingConditionId = isset($categoryMappingInfo['condition']['id']) ? $categoryMappingInfo['condition']['id'] : false;
            if ($mappingConditionId == ProfilesModel::ITEM_CONDITION_PRODUCT_DATA) {
                $mappingConditionId = $this->convertProductConditionToEbayConditionId($this->getProfileProduct()->getProduct()->condition);
            }

            if (!empty($categoryMappingInfo['condition_description'])) {
                $conditionDescription = $this->getProfileProduct()->getSpecificByValue(
                    $categoryMappingInfo['condition_description'],
                    array(),
                    false
                );
            }

            return array('condition' => $mappingConditionId, 'condition_description' => $conditionDescription);
        }

        // Profile mode
        $mappingConditionId = $this->getProfileProduct()->getProfile()->item_condition;
        if ($mappingConditionId == ProfilesModel::ITEM_CONDITION_PRODUCT_DATA) {
            $mappingConditionId = $this->convertProductConditionToEbayConditionId($this->getProfileProduct()->getProduct()->condition);
        }

        if (!empty($this->getProfileProduct()->getProfile()->item_condition_description)) {
            $conditionDescription = $this->getProfileProduct()->getSpecificByValue(
                $this->getProfileProduct()->getProfile()->item_condition_description,
                array(),
                false
            );
        }

        return array('condition' => $mappingConditionId, 'condition_description' => $conditionDescription);
    }

    /**
     * Convert PrestaShop product condition key value to Ebay condition ID
     *
     * @return int ebay condition id
     */
    protected function convertProductConditionToEbayConditionId($productConditionValue)
    {
        switch ($productConditionValue) {
            case 'new':
                return ProfilesModel::ITEM_CONDITION_NEW;
            case 'used':
                return ProfilesModel::ITEM_CONDITION_USED;
            case 'refurbished':
                return ProfilesModel::ITEM_CONDITION_REFURBISHED;
            default:
                return false;
        }
    }

    protected function _getVatPercent()
    {
        return array('vat' => $this->getProfileProduct()->getProfile()->item_vat);
    }

    protected function _getMultiVariations()
    {
        if ($this->getProfileProduct()->getProfile()->use_multivariation) {
            return array(
                'variations' => $this->getProfileProduct()->getVariations(),
                'variationsImages' => $this->getProfileProduct()->getProfile()->multivariation_images ? $this->getProfileProduct()->getVariationsImages(): array(),
                'variationSku' => $this->getProfileProduct()->getProfile()->sku_variation,
            );
        }

        return array();
    }

    protected function _getItemDescription()
    {
        return array('description' => $this->getProfileProduct()->getDescription());
    }

    protected function _getPictureDetails()
    {
        return array('images' => $this->getProfileProduct()->getImages());
    }

    protected function _getHitCounter()
    {
        return array('hit_counter' => $this->getProfileProduct()->getProfile()->hit_counter);
    }

    // -------------------------------------------------------------------------
    /** Validator Part */
    protected function _isTitleEmpty()
    {
        if ($this->getProfileProduct()->getTitle() == "") {
            return true;
        }
        return false;
    }

    protected function _isDescriptionEmpty()
    {
        if ($this->getProfileProduct()->getDescription() == "") {
            return true;
        }
        return false;
    }

    protected function _isPriceEmpty()
    {
        if ($this->getProfileProduct()->getStartPrice() <= 0) {
            return true;
        }
    }

    protected function _checkCorrectVariations()
    {
        if ($this->getProfileProduct()->getProfile()->use_multivariation) {
            $variationList = $this->getProfileProduct()->getVariations();
            if ($variationList == array()) {
                return false;
            }
            $numVariationOption = $this->_getNumVariationOptions($variationList);
            foreach ($variationList as $singleVariation) {
                if ($numVariationOption != count($singleVariation['options'])) {
                    return L::t('Variation Listings need to have products with the same number and type of attributes.
                       For example first combination product has attribute "Color" and "Disk space".
                       But second combination product have only "Color".');
                }
            }
        }
        return false;
    }

    protected function _getNumVariationOptions($variation)
    {
        $specifics = array();
        foreach ($variation as $option) {
            foreach ($option['options'] as $value => $option) {
                if (!isset($specifics[$value])) {
                    $specifics[$value] = 1;
                }
            }
        }
        return count($specifics);
    }

}
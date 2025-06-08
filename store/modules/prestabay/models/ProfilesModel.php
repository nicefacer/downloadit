<?php

/**
 * File ProfilesModel.php
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
class ProfilesModel extends AbstractModel
{

    const AUCTION_TYPE_CHINESE = 1;
    const AUCTION_TYPE_FIXEDPRICE = 2;

    const PRICE_MODE_PRODUCT = 1;
    const PRICE_MODE_CUSTOM = 2;
    const PRICE_MODE_TEMPLATE = 3;
    const PRICE_MODE_WHOLESALE_PRICE = 4;
    const PRICE_MODE_ORIGINAL_PRICE = 5;

    const ITEM_SKU_MODE_NONE = 0;
    const ITEM_SKU_MODE_REFERENCE = 1;

    const ITEM_QTY_MODE_SINGLE = 1;
    const ITEM_QTY_MODE_PRODUCT = 2;
    const ITEM_QTY_MODE_CUSTOM = 3;
    const ITEM_QTY_MODE_NOT_MORE_THAT = 4;
    const ITEM_QTY_MODE_RESERVED_VALUE = 5;

    const ITEM_DESCRIPTION_MODE_PRODUCT = 1;
    const ITEM_DESCRIPTION_MODE_CUSTOM = 2;
    const ITEM_DESCRIPTION_MODE_TEMPLATE = 3;

    const RETURN_ACCEPTED_EBAY_CONST = "ReturnsAccepted";
    const RETURN_NOT_ACCEPTED_EBAY_CONST = "ReturnsNotAccepted";

    const IMAGE_MODE_NONE = 1;
    const IMAGE_MODE_MAIN = 2;

    const SPECIFIC_STYLE_TEXT = 1;
    const SPECIFIC_STYLE_SELECT = 2;
    const SPECIFIC_STYLE_MULTIPLE = 4;

    const SPECIFIC_CUSTOM_VALUE_KEY = '--';
    const SPECIFIC_CUSTOM_ATTRIBUTE_PREFIX = 'specific-';
    const SPECIFIC_CUSTOM_FEATURE_PREFIX = 'specific-feature-';

    const USE_MULTI_VARIATION_NO = 0;
    const USE_MULTI_VARIATION_YES = 1;

    const VARIATION_IMAGES_NO = 0;
    const VARIATION_IMAGES_YES = 1;

    const PRIVATE_LISTING_NO = 0;
    const PRIVATE_LISTING_YES = 1;

    const GET_IT_FAST_NO = 0;
    const GET_IT_FAST_YES = 1;

    const GLOBAL_SHIPPING_NO = 0;
    const GLOBAL_SHIPPING_YES = 1;

    const SHIPPING_MODE_CUSTOM_PRICE = 0;

    const AUTOPAY_NO = 0;
    const AUTOPAY_YES = 1;

    const CROSS_BORDER_RATE_NO = 0;
    const CROSS_BORDER_RATE_YES = 1;

    const SHIPPING_CALCULATED_MEASUREMENT_ENGLISH = "English";
    const SHIPPING_CALCULATED_MEASUREMENT_METRIC = "Metric";

    const SHIPPING_CALCULATED_DEPTH_MODE_PRODUCT = 0;
    const SHIPPING_CALCULATED_DEPTH_MODE_CUSTOM = 1;

    const SHIPPING_CALCULATED_LENGTH_MODE_PRODUCT = 0;
    const SHIPPING_CALCULATED_LENGTH_MODE_CUSTOM = 1;

    const SHIPPING_CALCULATED_WIDTH_MODE_PRODUCT = 0;
    const SHIPPING_CALCULATED_WIDTH_MODE_CUSTOM = 1;

    const SHIPPING_CALCULATED_WEIGHT_MODE_PRODUCT = 0;
    const SHIPPING_CALCULATED_WEIGHT_MODE_CUSTOM = 1;

    const SHIPPING_TYPE_FLAT = 0;
    const SHIPPING_TYPE_CALCULATED = 1; // Only US, CA, CAFR, AU

    const BEST_OFFER_NO = 0;
    const BEST_OFFER_YES = 1;

    const INSURANCE_OPTION_INCLUDED_IN_SHIPPING_HANDLING = "IncludedInShippingHandling";
    const INSURANCE_OPTION_NOT_OFFERED = "NotOffered";
    const INSURANCE_OPTION_OPTIONAL = "Optional";
    const INSURANCE_OPTION_REQUIRED = "Required";

    const GIFT_ICON_NO = 0;
    const GIFT_ICON_YES = 1;

    const GIFT_SERVICE_EXPRESS_SHIPPING = "GiftExpressShipping";
    const GIFT_SERVICE_SHIP_TO_RECIPIENT = "GiftShipToRecipient";
    const GIFT_SERVICE_WRAP = "GiftWrap";

    const EAN_INCLUDE_NO = 0;
    const EAN_INCLUDE_YES = 1;

    const UPC_INCLUDE_NO = 0;
    const UPC_INCLUDE_YES = 1;

    const MPN_INCLUDE_NO = 0;
    const MPN_INCLUDE_YES = 1;

    const ISBN_INCLUDE_NO = 0;
    const ISBN_INCLUDE_YES = 1;

    const IDENTIFY_NOT_AVAILABLE_NO = 0;
    const IDENTIFY_NOT_AVAILABLE_YES = 1;

    const IDENTIFY_FOR_VARIATION_NO = 0;
    const IDENTIFY_FOR_VARIATION_YES = 1;

    const SKU_FOR_VARIATION_NO = 0;
    const SKU_FOR_VARIATION_YES = 1;

    const UNIT_INCLUDE_NO = 0;
    const UNIT_INCLUDE_YES = 1;

    const UNIT_TYPE_NONE = '';
    const UNIT_TYPE_KG = 'Kg';
    const UNIT_TYPE_100G = '100g';
    const UNIT_TYPE_10G = '10g';
    const UNIT_TYPE_L = 'L';
    const UNIT_TYPE_100ML = '100ml';
    const UNIT_TYPE_10ML = '10ml';
    const UNIT_TYPE_M = 'M';
    const UNIT_TYPE_M2 = 'M2';
    const UNIT_TYPE_M3 = 'M3';
    const UNIT_TYPE_UNIT = 'Unit';

    const PROMOTIONAL_SHIPPING_DISCOUNT_NO = 0;
    const PROMOTIONAL_SHIPPING_DISCOUNT_YES = 1;

    const EBAY_STORE_MODE_PROFILE = 0;
    const EBAY_STORE_MODE_PRODUCT = 1;
    const EBAY_STORE_MODE_MAPPING = 2;

    const EBAY_CATEGORY_MODE_PROFILE = 0;
    const EBAY_CATEGORY_MODE_MAPPING = 1;

    const ITEM_CONDITION_NEW = 1000;
    const ITEM_CONDITION_REFURBISHED = 2500;
    const ITEM_CONDITION_USED = 3000;
    const ITEM_CONDITION_PRODUCT_DATA = -1000;

    public $ebay_account; // isInt
    public $ebay_site; // isInt
    public $profile_name; // isString
    public $ebay_category_mode; // isInt
    public $ebay_category_mapping_id; // isInt
    public $ebay_primary_category_name; // isString
    public $ebay_primary_category_value; // isInt
    public $ebay_secondary_category_name; // isString
    public $ebay_secondary_category_value; // isString
    public $cross_border_trade; // isInt
    public $auction_type; // isInt
    public $auction_duration; // isString
    public $item_title; // isString
    public $subtitle; // isString
    public $remove_more_80; // isInt
    public $item_sku; // isInt
    public $item_condition; // isInt
    public $item_condition_description; // isInt
    public $item_qty_mode; // isInt
    public $item_qty_value; // isInt
    public $item_image; // isInt
    public $item_image_count; // isInt
    public $hit_counter; // isString
    public $item_description_mode; // isInt
    public $item_description_custom; // isString
    public $description_template_id; // isInt
    public $item_currency; // isString
    public $item_vat; // isDouble
    public $price_start; // isInt
    public $price_start_multiply; // isString
    public $price_start_custom; // isString
    public $price_start_template;
    public $price_reserve; // isInt
    public $price_reserve_multiply; // ` double NOT NULL default '1',
    public $price_reserve_custom; //` double NOT NULL default '0',
    public $price_reserve_template;
    public $price_buynow; // isInt
    public $price_buynow_multiply; //` double NOT NULL default '1',
    public $price_buynow_custom; //` double NOT NULL default '0',
    public $price_buynow_template;

    public $price_discount; // double NOT NULL default '0'

    public $ebay_store_mode; // tinyint
    public $ebay_store_mapping_id; // int
    public $ebay_store_category_main; // bigint
    public $ebay_store_category_secondary; // bigint
    public $payment_methods; // isString
    public $payment_paypal_email; // isString
    public $autopay; // isInt
    public $shipping_country; // isString
    public $shipping_location; // isString
    public $shipping_dispatch; // isString
    public $shipping_local; // isString
    public $shipping_int; // isString
    public $shipping_to_location; // isString
    public $shipping_exclude_location; // isString
    public $shipping_allowed_location; // isString
    public $cod_cost_italy; // double
    public $returns_accepted; // isString
    public $refund; // isString
    public $returns_within; // isString
    public $shipping_cost_paid_by; // isString
    public $refund_description; // isString
    public $restock_fee; // isString
    public $date_add;
    public $date_upd;
    public $product_specifics;
    public $product_specifics_attribute;
    public $product_specifics_custom;
    public $attribute_set_id;
    public $use_multivariation;
    public $multivariation_images;
    public $best_offer_enabled;
    public $best_offer_minimum_price;
    public $best_offer_auto_accept_price;

    public $enhancement; // isString
    public $ps_image_type; // isString
    public $gallery_type; // isString
    public $photo_display; // isString

    public $private_listing;
    public $get_it_fast;
    public $global_shipping;

    public $shipping_calculated_measurement;
    public $shipping_calculated_package;
    public $shipping_calculated_depth;
    public $shipping_calculated_depth_custom;
    public $shipping_calculated_length;
    public $shipping_calculated_length_custom;
    public $shipping_calculated_width;
    public $shipping_calculated_width_custom;
    public $shipping_calculated_weight;
    public $shipping_calculated_weight_custom;
    public $shipping_calculated_postal;
    public $shipping_calculated_local_handling_cost;
    public $shipping_calculated_int_handling_cost;

    public $shipping_local_type;
    public $shipping_int_type;

    public $ean;
    public $upc;
    public $mpn;
    public $isbn;
    public $identify_not_available;
    public $identify_variation;
    public $sku_variation;

    public $payment_instruction;
    public $gift_icon;
    public $gift_services;
    public $insurance_fee;
    public $insurance_option;
    public $insurance_international_fee;
    public $insurance_international_option;
    public $unit_include;
    public $unit_type;

    public $promotional_shipping_discount;
    public $promotional_int_shipping_discount;
    public $shipping_discount_profile_id;
    public $int_shipping_discount_profile_id;

    /**
     * @var array Cached values for available images types in PrestaShop
     */
    protected static $_availableImageTypes = null;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_profiles";
        $this->identifier = "id";

        $this->fieldsRequired = array();

        $this->fieldsSize = array();

        $this->fieldsValidate = array();

        parent::__construct($id, $id_lang);
    }

    public function getFieldsWihoutValidation()
    {

        return array(
            'ebay_account' => (int) $this->ebay_account,
            'ebay_site' => (int) $this->ebay_site,
            'profile_name' => pSQL($this->profile_name),
            'ebay_category_mode' => (int) $this->ebay_category_mode,
            'ebay_category_mapping_id' => (int) $this->ebay_category_mapping_id,
            'ebay_primary_category_name' => pSQL($this->ebay_primary_category_name),
            'ebay_primary_category_value' => (float)$this->ebay_primary_category_value,
            'ebay_secondary_category_name' => pSQL($this->ebay_secondary_category_name),
            'ebay_secondary_category_value' => (float)$this->ebay_secondary_category_value,
            'cross_border_trade' => (int) $this->cross_border_trade,
            'auction_type' => (int) $this->auction_type,
            'auction_duration' => pSQL($this->auction_duration),
            'item_title' => pSQL($this->item_title),
            'subtitle' => pSQL($this->subtitle),
            'remove_more_80' => (int) ($this->remove_more_80),
            'item_sku' => (int) ($this->item_sku),
            'item_condition' => (int) $this->item_condition,
            'item_condition_description' => pSQL($this->item_condition_description),
            'item_qty_mode' => (int) $this->item_qty_mode,
            'item_qty_value' => (int) $this->item_qty_value,
            'item_image' => (int) $this->item_image,
            'item_image_count' => (int) $this->item_image_count,
            'hit_counter' => pSQL($this->hit_counter),
            'item_description_mode' => (int) $this->item_description_mode,
            'item_description_custom' => pSQL($this->item_description_custom, true),
            'description_template_id' => (int) $this->description_template_id,
            'item_currency' => pSQL($this->item_currency),
            'item_vat' => (double) $this->item_vat,
            'price_start' => (int) $this->price_start,
            'price_start_multiply' => (double) $this->price_start_multiply,
            'price_start_custom' => (double) $this->price_start_custom,
            'price_start_template' => (int)$this->price_start_template,
            'price_reserve' => (int) $this->price_reserve,
            'price_reserve_multiply' => (double) $this->price_reserve_multiply,
            'price_reserve_custom' => (double) $this->price_reserve_custom,
            'price_reserve_template' => (int) $this->price_reserve_template,
            'price_buynow' => (int) $this->price_buynow,
            'price_buynow_multiply' => (double) $this->price_buynow_multiply,
            'price_buynow_custom' => (double) $this->price_buynow_custom,
            'price_buynow_template' => (int) $this->price_buynow_template,
            'price_discount' => (double) $this->price_discount,
            'ebay_store_mode' => (int) $this->ebay_store_mode,
            'ebay_store_mapping_id' => (int) $this->ebay_store_mapping_id,
            'ebay_store_category_main' => (float) $this->ebay_store_category_main,
            'ebay_store_category_secondary' => (float) $this->ebay_store_category_secondary,
            'payment_methods' => pSQL($this->payment_methods),
            'payment_paypal_email' => pSQL($this->payment_paypal_email),
            'autopay' => (int) $this->autopay,
            'shipping_country' => pSQL($this->shipping_country),
            'shipping_location' => pSQL($this->shipping_location),
            'shipping_exclude_location' => pSQL($this->shipping_exclude_location),
            'shipping_allowed_location' => pSQL($this->shipping_allowed_location),
            'shipping_dispatch' => pSQL($this->shipping_dispatch),
            'shipping_local' => pSQL($this->shipping_local),
            'shipping_int' => pSQL($this->shipping_int),
            'shipping_to_location' => pSQL($this->shipping_to_location),
            'cod_cost_italy' => (double) $this->cod_cost_italy,
            'returns_accepted' => pSQL($this->returns_accepted),
            'refund' => pSQL($this->refund),
            'returns_within' => pSQL($this->returns_within),
            'shipping_cost_paid_by' => pSQL($this->shipping_cost_paid_by),
            'refund_description' => pSQL($this->refund_description),
            'restock_fee' => pSQL($this->restock_fee),
            'product_specifics' => pSQL($this->product_specifics),
            'product_specifics_attribute' => pSQL($this->product_specifics_attribute),
            'product_specifics_custom' => pSQL($this->product_specifics_custom),
            'attribute_set_id' => (int) $this->attribute_set_id,
            'use_multivariation' => (int) $this->use_multivariation,
            'multivariation_images' => (int) $this->multivariation_images,
            'best_offer_enabled' => (int) $this->best_offer_enabled,
            'best_offer_minimum_price' => (int) $this->best_offer_minimum_price,
            'best_offer_auto_accept_price' => (int) $this->best_offer_auto_accept_price,
            'enhancement' => pSQL($this->enhancement),
            'ps_image_type' => pSQL($this->ps_image_type),
            'gallery_type' => pSQL($this->gallery_type),
            'photo_display' => pSQL($this->photo_display),
            'private_listing' => (int) $this->private_listing,
            'get_it_fast' => (int) $this->get_it_fast,
            'global_shipping' => (int) $this->global_shipping,
            'shipping_local_type' => (int) $this->shipping_local_type,
            'shipping_int_type' => (int) $this->shipping_int_type,
            'shipping_calculated_measurement' => pSQL($this->shipping_calculated_measurement),
            'shipping_calculated_package' => pSQL($this->shipping_calculated_package),
            'shipping_calculated_depth' => (int) $this->shipping_calculated_depth,
            'shipping_calculated_depth_custom' => (double) $this->shipping_calculated_depth_custom,
            'shipping_calculated_length' => (int) $this->shipping_calculated_length,
            'shipping_calculated_length_custom' => (double) $this->shipping_calculated_length_custom,
            'shipping_calculated_width' => (int) $this->shipping_calculated_width,
            'shipping_calculated_width_custom' => (double) $this->shipping_calculated_width_custom,
            'shipping_calculated_weight' => (int) $this->shipping_calculated_weight,
            'shipping_calculated_weight_custom' => (double) $this->shipping_calculated_weight_custom,
            'shipping_calculated_postal' => pSQL($this->shipping_calculated_postal),
            'shipping_calculated_local_handling_cost' => (double) $this->shipping_calculated_local_handling_cost,
            'shipping_calculated_int_handling_cost' => (double) $this->shipping_calculated_int_handling_cost,
            'ean' => (int)$this->ean,
            'upc' => (int)$this->upc,
            'mpn' => (int)$this->mpn,
            'isbn' => (int)$this->isbn,
            'identify_not_available' => (int)$this->identify_not_available,
            'identify_variation' => (int)$this->identify_variation,
            'sku_variation' => (int)$this->sku_variation,
            'payment_instruction' => pSQL($this->payment_instruction),
            'gift_icon' => (int)$this->gift_icon,
            'gift_services' => pSQL($this->gift_services),
            'insurance_fee' => (double) $this->insurance_fee,
            'insurance_option' => pSQL($this->insurance_option),
            'insurance_international_fee' => (double) $this->insurance_international_fee,
            'insurance_international_option' => pSQL($this->insurance_international_option),
            'unit_include' => (int)$this->unit_include,
            'unit_type' => pSQL($this->unit_type),
            'promotional_shipping_discount' => (int)$this->promotional_shipping_discount,
            'promotional_int_shipping_discount' => (int)$this->promotional_int_shipping_discount,
            'shipping_discount_profile_id' => pSQL($this->shipping_discount_profile_id),
            'int_shipping_discount_profile_id' => pSQL($this->int_shipping_discount_profile_id),
        );
    }

    public function getFields()
    {
        parent::validateFields();
        $totalFields = $this->getFieldsWihoutValidation() + array(
            'date_add' => $this->date_add,
            'date_upd' => $this->date_upd,
        );

        return $totalFields;
    }

    public function getProfiles()
    {
        return Db::getInstance()->ExecuteS('SELECT id, profile_name FROM ' . _DB_PREFIX_ . $this->table . ' ORDER BY profile_name ASC');
    }

    public function getSiteKey()
    {
        $marketplace = new MarketplacesModel($this->ebay_site);
        return $marketplace->code;
    }

    public function getEnhancement()
    {
        return is_null($this->id) ? false : json_decode($this->enhancement, true);
    }

    public function getGiftServices()
    {
        return is_null($this->id) ? false : json_decode($this->gift_services, true);
    }

    public function getLocalShipping()
    {
        if (is_null($this->id)) {
            return false;
        }
        return unserialize($this->shipping_local);
    }

    public function getInternationalShipping()
    {
        if (is_null($this->id)) {
            return false;
        }
        return unserialize($this->shipping_int);
    }

    public function getPaymentMethods()
    {
        if (is_null($this->id)) {
            return false;
        }
        return unserialize($this->payment_methods);
    }

    public function getShippingToLocation()
    {
        if (is_null($this->id)) {
            return false;
        }
        return unserialize($this->shipping_to_location);
    }

    public function getShippingExcludeLocation()
    {
        if (is_null($this->id)) {
            return false;
        }
        return unserialize($this->shipping_exclude_location);
    }

    public function getShippingAllowedLocation()
    {
        if (is_null($this->id)) {
            return false;
        }
        return unserialize($this->shipping_allowed_location);
    }

    public function getProductSpecifics()
    {
        if (is_null($this->id)) {
            return false;
        }

        if (get_magic_quotes_gpc() === 1) {
            $product_specifics = stripslashes(str_replace('\\', '', $this->product_specifics));
        } else {
            $product_specifics = $this->product_specifics;
        }

        return unserialize($product_specifics);
    }

    public function getProductSpecificsAttribute()
    {
        if (is_null($this->id)) {
            return false;
        }
        return unserialize($this->product_specifics_attribute);
    }

    public function getProductSpecificsCustom()
    {
        if (is_null($this->id)) {
            return false;
        }
        return unserialize($this->product_specifics_custom);
    }

    /**
     * Does Profile is for GTC listings
     *
     * @return bool
     */
    public function isGTC()
    {
        if (is_null($this->id)) {
            return false;
        }

        return $this->auction_duration === 'GTC';
    }

    public function deleteProfileWithCheck($id)
    {
        $sqlToGetInfo = "SELECT * FROM " . _DB_PREFIX_ . 'prestabay_selling_list' . "
                        WHERE profile = " . $id;
        $r = Db::getInstance()->ExecuteS($sqlToGetInfo);
        if (is_array($r) && count($r) > 0) {
            // Have information that connected to profile (lists created)
            return false;
        } else if ($r == array()) {
            // No connected information
            $sqlToDelete = "DELETE FROM " . _DB_PREFIX_ . $this->table . " WHERE id = " . $id;
            return Db::getInstance()->Execute($sqlToDelete);
        }
        return false;
    }

    public static $COUNTRY_LIST = array(
        "US" => "United States",
        "GB" => "United Kingdom",
        "FR" => "France",
        "ES" => "Spain",
        "IT" => "Italy",
        "AD" => "Andorra",
        "AE" => "United Arab Emirates",
        "AF" => "Afghanistan",
        "AG" => "Antigua and Barbuda",
        "AI" => "Anguilla",
        "AL" => "Albania",
        "AM" => "Armenia",
        "AN" => "Netherlands Antilles",
        "AO" => "Angola",
        "AQ" => "Antarctica",
        "AR" => "Argentina",
        "AS" => "American Samoa",
        "AT" => "Austria",
        "AU" => "Australia",
        "AW" => "Aruba",
        "AX" => "Åland Islands",
        "AZ" => "Azerbaijan",
        "BA" => "Bosnia and Herzegovina",
        "BB" => "Barbados",
        "BD" => "Bangladesh",
        "BE" => "Belgium",
        "BF" => "Burkina Faso",
        "BG" => "Bulgaria",
        "BH" => "Bahrain",
        "BI" => "Burundi",
        "BJ" => "Benin",
        "BL" => "Saint Barthélemy",
        "BM" => "Bermuda",
        "BN" => "Brunei",
        "BO" => "Bolivia",
        "BR" => "Brazil",
        "BS" => "Bahamas",
        "BT" => "Bhutan",
        "BV" => "Bouvet Island",
        "BW" => "Botswana",
        "BY" => "Belarus",
        "BZ" => "Belize",
        "CA" => "Canada",
        "CC" => "Cocos [Keeling] Islands",
        "CD" => "Congo - Kinshasa",
        "CF" => "Central African Republic",
        "CG" => "Congo - Brazzaville",
        "CH" => "Switzerland",
        "CI" => "Côte d’Ivoire",
        "CK" => "Cook Islands",
        "CL" => "Chile",
        "CM" => "Cameroon",
        "CN" => "China",
        "CO" => "Colombia",
        "CR" => "Costa Rica",
        "CU" => "Cuba",
        "CV" => "Cape Verde",
        "CX" => "Christmas Island",
        "CY" => "Cyprus",
        "CZ" => "Czech Republic",
        "DE" => "Germany",
        "DJ" => "Djibouti",
        "DK" => "Denmark",
        "DM" => "Dominica",
        "DO" => "Dominican Republic",
        "DZ" => "Algeria",
        "EC" => "Ecuador",
        "EE" => "Estonia",
        "EG" => "Egypt",
        "EH" => "Western Sahara",
        "ER" => "Eritrea",
        "ET" => "Ethiopia",
        "FI" => "Finland",
        "FJ" => "Fiji",
        "FK" => "Falkland Islands",
        "FM" => "Micronesia",
        "FO" => "Faroe Islands",
        "GA" => "Gabon",
        "GD" => "Grenada",
        "GE" => "Georgia",
        "GF" => "French Guiana",
        "GG" => "Guernsey",
        "GH" => "Ghana",
        "GI" => "Gibraltar",
        "GL" => "Greenland",
        "GM" => "Gambia",
        "GN" => "Guinea",
        "GP" => "Guadeloupe",
        "GQ" => "Equatorial Guinea",
        "GR" => "Greece",
        "GS" => "South Georgia and the South Sandwich Islands",
        "GT" => "Guatemala",
        "GU" => "Guam",
        "GW" => "Guinea-Bissau",
        "GY" => "Guyana",
        "HK" => "Hong Kong SAR China",
        "HM" => "Heard Island and McDonald Islands",
        "HN" => "Honduras",
        "HR" => "Croatia",
        "HT" => "Haiti",
        "HU" => "Hungary",
        "ID" => "Indonesia",
        "IE" => "Ireland",
        "IL" => "Israel",
        "IM" => "Isle of Man",
        "IN" => "India",
        "IO" => "British Indian Ocean Territory",
        "IQ" => "Iraq",
        "IR" => "Iran",
        "IS" => "Iceland",
        "JE" => "Jersey",
        "JM" => "Jamaica",
        "JO" => "Jordan",
        "JP" => "Japan",
        "KE" => "Kenya",
        "KG" => "Kyrgyzstan",
        "KH" => "Cambodia",
        "KI" => "Kiribati",
        "KM" => "Comoros",
        "KN" => "Saint Kitts and Nevis",
        "KP" => "North Korea",
        "KR" => "South Korea",
        "KW" => "Kuwait",
        "KY" => "Cayman Islands",
        "KZ" => "Kazakhstan",
        "LA" => "Laos",
        "LB" => "Lebanon",
        "LC" => "Saint Lucia",
        "LI" => "Liechtenstein",
        "LK" => "Sri Lanka",
        "LR" => "Liberia",
        "LS" => "Lesotho",
        "LT" => "Lithuania",
        "LU" => "Luxembourg",
        "LV" => "Latvia",
        "LY" => "Libya",
        "MA" => "Morocco",
        "MC" => "Monaco",
        "MD" => "Moldova",
        "ME" => "Montenegro",
        "MF" => "Saint Martin",
        "MG" => "Madagascar",
        "MH" => "Marshall Islands",
        "MK" => "Macedonia",
        "ML" => "Mali",
        "MM" => "Myanmar [Burma]",
        "MN" => "Mongolia",
        "MO" => "Macau SAR China",
        "MP" => "Northern Mariana Islands",
        "MQ" => "Martinique",
        "MR" => "Mauritania",
        "MS" => "Montserrat",
        "MT" => "Malta",
        "MU" => "Mauritius",
        "MV" => "Maldives",
        "MW" => "Malawi",
        "MX" => "Mexico",
        "MY" => "Malaysia",
        "MZ" => "Mozambique",
        "NA" => "Namibia",
        "NC" => "New Caledonia",
        "NE" => "Niger",
        "NF" => "Norfolk Island",
        "NG" => "Nigeria",
        "NI" => "Nicaragua",
        "NL" => "Netherlands",
        "NO" => "Norway",
        "NP" => "Nepal",
        "NR" => "Nauru",
        "NU" => "Niue",
        "NZ" => "New Zealand",
        "OM" => "Oman",
        "PA" => "Panama",
        "PE" => "Peru",
        "PF" => "French Polynesia",
        "PG" => "Papua New Guinea",
        "PH" => "Philippines",
        "PK" => "Pakistan",
        "PL" => "Poland",
        "PM" => "Saint Pierre and Miquelon",
        "PN" => "Pitcairn Islands",
        "PR" => "Puerto Rico",
        "PS" => "Palestinian Territories",
        "PT" => "Portugal",
        "PW" => "Palau",
        "PY" => "Paraguay",
        "QA" => "Qatar",
        "RE" => "Réunion",
        "RO" => "Romania",
        "RS" => "Serbia",
        "RU" => "Russia",
        "RW" => "Rwanda",
        "SA" => "Saudi Arabia",
        "SB" => "Solomon Islands",
        "SC" => "Seychelles",
        "SD" => "Sudan",
        "SE" => "Sweden",
        "SG" => "Singapore",
        "SH" => "Saint Helena",
        "SI" => "Slovenia",
        "SJ" => "Svalbard and Jan Mayen",
        "SK" => "Slovakia",
        "SL" => "Sierra Leone",
        "SM" => "San Marino",
        "SN" => "Senegal",
        "SO" => "Somalia",
        "SR" => "Suriname",
        "ST" => "São Tomé and Príncipe",
        "SV" => "El Salvador",
        "SY" => "Syria",
        "SZ" => "Swaziland",
        "TC" => "Turks and Caicos Islands",
        "TD" => "Chad",
        "TF" => "French Southern Territories",
        "TG" => "Togo",
        "TH" => "Thailand",
        "TJ" => "Tajikistan",
        "TK" => "Tokelau",
        "TL" => "Timor-Leste",
        "TM" => "Turkmenistan",
        "TN" => "Tunisia",
        "TO" => "Tonga",
        "TR" => "Turkey",
        "TT" => "Trinidad and Tobago",
        "TV" => "Tuvalu",
        "TW" => "Taiwan",
        "TZ" => "Tanzania",
        "UA" => "Ukraine",
        "UG" => "Uganda",
        "UM" => "U.S. Minor Outlying Islands",
        "UY" => "Uruguay",
        "UZ" => "Uzbekistan",
        "VA" => "Vatican City",
        "VC" => "Saint Vincent and the Grenadines",
        "VE" => "Venezuela",
        "VG" => "British Virgin Islands",
        "VI" => "U.S. Virgin Islands",
        "VN" => "Vietnam",
        "VU" => "Vanuatu",
        "WF" => "Wallis and Futuna",
        "WS" => "Samoa",
        "YE" => "Yemen",
        "YT" => "Mayotte",
        "ZA" => "South Africa",
        "ZM" => "Zambia",
        "ZW" => "Zimbabwe"
    );

    public static function getEnhancementList()
    {
        return array(
            'BoldTitle' => L::t('Bold Title'),
            'Border' => L::t('Border'),
            'Featured' => L::t('Featured'),
            'Highlight' => L::t('Highlight'),
            'HomePageFeatured' => L::t('Home Page Featured'),
            'ValuePackBundle' => L::t('Value Pack Bundle')
        );
    }

    public static function getGalleryTypeList()
    {
        return array(
            'None' => L::t('None'),
            'Featured' => L::t('Featured'),
            'Gallery' => L::t('Gallery'),
            'Plus' => L::t('Gallery Plus'),
        );
    }

    public static function getPhotoDisplayList()
    {
        return array(
            'None' => L::t('None'),
            'PicturePack' => L::t('Picture Pack'),
            'SuperSize' => L::t('Super Size'),
        );
    }

    public static function getAvailableImageTypes()
    {
        if (is_null(self::$_availableImageTypes)) {
            $productImages = ImageType::getImagesTypes('products');
            self::$_availableImageTypes = array();
            foreach ($productImages as $singleImage) {
                self::$_availableImageTypes[$singleImage['name']] = $singleImage['name'] . " ({$singleImage['width']}x{$singleImage['height']})";
            }
        }
        return self::$_availableImageTypes;
    }

}
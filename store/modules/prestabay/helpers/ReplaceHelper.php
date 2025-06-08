<?php

/**
 * File ReplaceHelper.php
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
class ReplaceHelper
{
    const TEMPLATE_ATTR_PRODUCT_ID = "product_id";

    const TEMPLATE_ATTR_PRODUCT_NAME = "product_name";
    const TEMPLATE_ATTR_PRODUCT_ORIGINAL_NAME = "product_original_name";
    const TEMPLATE_ATTR_PRODUCT_COMBINATION_NAME = "product_combination_name";

    const TEMPLATE_ATTR_PRODUCT_DESCRIPTION = "product_description";
    const TEMPLATE_ATTR_PRODUCT_DESCRIPTION_SHORT = "product_description_short";
    const TEMPLATE_ATTR_PRODUCT_QTY = "product_qty";
    const TEMPLATE_ATTR_PRODUCT_MIN_QTY = "product_min_qty";
    const TEMPLATE_ATTR_PRODUCT_PRICE = "product_price";
    const TEMPLATE_ATTR_PRODUCT_UNIT_PRICE = "product_unit_price";
    const TEMPLATE_ATTR_PRODUCT_UNIT_PRICE_RATIO = "product_unit_price_ratio";
    const TEMPLATE_ATTR_PRODUCT_UNIT_TYPE = "product_unit_type";

    const TEMPLATE_ATTR_PRODUCT_MAIN_IMAGE = "product_main_image";
    const TEMPLATE_ATTR_PRODUCT_SPECIFIC_IMAGE = "product_image";

    const TEMPLATE_ATTR_PRODUCT_GALLERY = "product_gallery";

    const TEMPLATE_ATTR_PRODUCT_REFERENCE = "product_reference";

    const TEMPLATE_ATTR_PRODUCT_SUPPLIER_REFERENCE = "product_supplier_reference";
    const TEMPLATE_ATTR_PRODUCT_EAN13 = "product_ean13";
    const TEMPLATE_ATTR_PRODUCT_UPC = "product_upc";
    const TEMPLATE_ATTR_PRESTA_ATTRIBUTES_MPN = "presta_atributes_mpn";
    const TEMPLATE_ATTR_PRESTA_ATTRIBUTES_ISBN = "presta_atributes_isbn";

    const TEMPLATE_ATTR_PRODUCT_FEATURE = "product_feature";
    const TEMPLATE_ATTR_PRODUCT_MANUFACTURER = "product_manufacturer";
    const TEMPLATE_ATTR_PRODUCT_SUPPLIER = "product_supplier";
    const TEMPLATE_ATTR_PRODUCT_TAGS = "product_tags";

    const TEMPLATE_ATTR_PRODUCT_COMBINATIONS = "product_combinations";
    const TEMPLATE_ATTR_PRODUCT_MANUFACTURER_ID = "manufacturer_id";

    const TEMPLATE_ATTR_PRESTABAY_TITLE = "prestabay_title";
    const TEMPLATE_ATTR_PRESTABAY_SUBTITLE = "prestabay_subtitle";
    const TEMPLATE_ATTR_PRESTABAY_DESCRIPTION = "prestabay_description";

    /**
     *
     * @var ProfileProductModel
     */
    protected static $_profileProductModel = null;

    public static function parseAttributes($inputString, ProfileProductModel $profileProductModel)
    {
        self::$_profileProductModel = $profileProductModel;
        return preg_replace_callback('/\[(.+?)(\((.+?)?\))?\]/', array('ReplaceHelper', 'replaceCallback'), $inputString);
    }

    public static function getAttributeValue($attributeKey, ProfileProductModel $profileProductModel)
    {
        self::$_profileProductModel = $profileProductModel;
        return self::replaceCallback(array(0 => '', 1 => $attributeKey));
    }

    public static function getFeatureValue($featureKey, ProfileProductModel $profileProductModel)
    {
        self::$_profileProductModel = $profileProductModel;
        return self::replaceCallback(array(0 => '', 1 => self::TEMPLATE_ATTR_PRODUCT_FEATURE, 3 => $featureKey . ',n/a'));
    }

    public static function replaceCallback($matches)
    {
        $langId = self::$_profileProductModel->getLangId();
        if (isset($matches[1])) {
            switch ($matches[1]) {
                case self::TEMPLATE_ATTR_PRODUCT_ID:
                    return self::$_profileProductModel->getProduct()->id;
                case self::TEMPLATE_ATTR_PRODUCT_MANUFACTURER_ID:
                    return self::$_profileProductModel->getProduct()->id_manufacturer;

                case self::TEMPLATE_ATTR_PRODUCT_NAME:
                    $customLangId = isset($matches[3]) ? $matches[3] : false;
                    if ($customLangId && isset(self::$_profileProductModel->getProduct()->name[$customLangId])) {
                        $langId = $customLangId;
                    }
                    $baseProductName = self::$_profileProductModel->getProduct()->name[$langId];

                    if (self::$_profileProductModel->getSellingProduct() && self::$_profileProductModel->getSellingProduct()->product_id_attribute > 0) {
                        $productModel = self::$_profileProductModel->getProduct();
                        $combinations = CoreHelper::getAttributeCombinationsById($productModel,
                                self::$_profileProductModel->getSellingProduct()->product_id_attribute,
                                $langId);
                        $totalOption = "";
                        foreach ($combinations as $combination) {
                            $totalOption != "" && $totalOption .= ", ";
                            $totalOption .= $combination['group_name'] . ": " . $combination['attribute_name'];
                        }
                        $baseProductName .= " [" . $totalOption . "]";

                    }
                    // self::$_profileProductModel->getSellingProduct()->product_id_attribute;
                    return $baseProductName;

                case self::TEMPLATE_ATTR_PRODUCT_ORIGINAL_NAME:
                    $customLangId = isset($matches[3]) ? $matches[3] : false;
                    if ($customLangId && isset(self::$_profileProductModel->getProduct()->name[$customLangId])) {
                        $langId = $customLangId;
                    }
                    $baseProductName = self::$_profileProductModel->getProduct()->name[$langId];

                    return $baseProductName;

                case self::TEMPLATE_ATTR_PRODUCT_COMBINATION_NAME:
                    $customLangId = isset($matches[3]) ? $matches[3] : false;
                    if ($customLangId && isset(self::$_profileProductModel->getProduct()->name[$customLangId])) {
                        $langId = $customLangId;
                    }
                    $baseCombinationName = "";

                    if (self::$_profileProductModel->getSellingProduct() && self::$_profileProductModel->getSellingProduct()->product_id_attribute > 0) {
                        $productModel = self::$_profileProductModel->getProduct();
                        $combinations = CoreHelper::getAttributeCombinationsById($productModel,
                            self::$_profileProductModel->getSellingProduct()->product_id_attribute,
                            $langId);
                        $totalOption = "";
                        foreach ($combinations as $combination) {
                            $totalOption != "" && $totalOption .= ", ";
                            $totalOption .= $combination['group_name'] . ": " . $combination['attribute_name'];
                        }
                        $baseCombinationName .=  $totalOption;
                    }

                    return $baseCombinationName;

                case self::TEMPLATE_ATTR_PRODUCT_DESCRIPTION:
                    $customLangId = isset($matches[3]) ? $matches[3] : false;
                    if ($customLangId && isset(self::$_profileProductModel->getProduct()->description[$customLangId])) {
                        $langId = $customLangId;
                    }
                    return self::$_profileProductModel->getProduct()->description[$langId];

                case self::TEMPLATE_ATTR_PRODUCT_DESCRIPTION_SHORT:
                    $customLangId = isset($matches[3]) ? $matches[3] : false;
                    if ($customLangId && isset(self::$_profileProductModel->getProduct()->description[$customLangId])) {
                        $langId = $customLangId;
                    }
                    return self::$_profileProductModel->getProduct()->description_short[$langId];

                case self::TEMPLATE_ATTR_PRODUCT_QTY:
                    return Product::getQuantity(self::$_profileProductModel->getProduct()->id);

                case self::TEMPLATE_ATTR_PRODUCT_MIN_QTY:
                    return self::$_profileProductModel->getProduct()->minimal_quantity;

                case self::TEMPLATE_ATTR_PRODUCT_PRICE:
                    return number_format(round(self::$_profileProductModel->getProduct()->getPrice(), 2), 2);

                case self::TEMPLATE_ATTR_PRODUCT_UNIT_PRICE:
                    $product = self::$_profileProductModel->getProduct();

                    return number_format(round($product->unit_price, 2), 2);

                case self::TEMPLATE_ATTR_PRODUCT_UNIT_PRICE_RATIO:
                    $product = self::$_profileProductModel->getProduct();

                    return number_format(round($product->unit_price_ratio, 2), 2);

                case self::TEMPLATE_ATTR_PRODUCT_UNIT_TYPE:
                    $product = self::$_profileProductModel->getProduct();

                    return $product->unity;

                case self::TEMPLATE_ATTR_PRODUCT_MAIN_IMAGE:
                    $imageLink = self::$_profileProductModel->getProductCoverImageLink();
                    if ($imageLink == "") {
                        return '';
                    } else {
                        // " . self::$_profileProductModel->getTitle() . "
                        return "<img src='{$imageLink}' style='max-width:600px;' alt=''/>";
                    }

                case self::TEMPLATE_ATTR_PRODUCT_SPECIFIC_IMAGE:
                    $imageOption = isset($matches[3]) ? $matches[3] : 1;

                    $imageIndex = 1;
                    $imageType = 'large_default';

                    $dataImage = explode(",", $imageOption);
                    if (isset($dataImage[0])) {
                        $imageIndex = $dataImage[0];
                    }
                    if (isset($dataImage[1])) {
                        $imageType = $dataImage[1];
                    }

                    $imageLink = self::$_profileProductModel->getProductImageNumber(trim($imageIndex), trim($imageType));
                    if ($imageLink == "" || !$imageLink) {
                        return '';
                    } else {
                        return "<img src='{$imageLink}' width='600' alt=''/>";
                    }

                case self::TEMPLATE_ATTR_PRODUCT_GALLERY:
                    $params = array();
                    if (isset($matches[3])) {
                        $params = explode(":", $matches[3]);
                    }
                    return self::_parseGallery($params);

                case self::TEMPLATE_ATTR_PRODUCT_REFERENCE:
                    return self::$_profileProductModel->getProduct()->reference;

                case self::TEMPLATE_ATTR_PRODUCT_SUPPLIER_REFERENCE:
                    return self::$_profileProductModel->getProduct()->supplier_reference;

                case self::TEMPLATE_ATTR_PRODUCT_EAN13:
                    return self::$_profileProductModel->getProduct()->ean13;

                case self::TEMPLATE_ATTR_PRODUCT_UPC:
                    return self::$_profileProductModel->getProduct()->upc;

                case self::TEMPLATE_ATTR_PRESTA_ATTRIBUTES_MPN:
                    $data = AttributesDataModel::loadByProductId(self::$_profileProductModel->getProduct()->id);
                    return !empty($data['mpn']) ? $data['mpn'] : '';

                case self::TEMPLATE_ATTR_PRESTA_ATTRIBUTES_ISBN:
                    $data = AttributesDataModel::loadByProductId(self::$_profileProductModel->getProduct()->id);
                    return !empty($data['isbn']) ? $data['isbn'] : '';

                case self::TEMPLATE_ATTR_PRODUCT_MANUFACTURER:
                    return Manufacturer::getNameById((int) self::$_profileProductModel->getProduct()->id_manufacturer);

                case self::TEMPLATE_ATTR_PRODUCT_SUPPLIER:
                    return Supplier::getNameById((int) self::$_profileProductModel->getProduct()->id_supplier);

                case self::TEMPLATE_ATTR_PRODUCT_FEATURE:
                    return self::_parseFeature((int) isset($matches[3]) ? $matches[3] : '0,n/a');

                case self::TEMPLATE_ATTR_PRODUCT_COMBINATIONS:
                    return self::_getProductCombinations();

                case self::TEMPLATE_ATTR_PRODUCT_TAGS:
                    return self::$_profileProductModel->getProduct()->getTags($langId);

                case self::TEMPLATE_ATTR_PRESTABAY_TITLE:
                    return self::$_profileProductModel->getProductPrestaBayInformation()->item_title;

                case self::TEMPLATE_ATTR_PRESTABAY_SUBTITLE:
                    return self::$_profileProductModel->getProductPrestaBayInformation()->subtitle;

                case self::TEMPLATE_ATTR_PRESTABAY_DESCRIPTION:
                    return self::$_profileProductModel->getProductPrestaBayInformation()->description;

                default:
                    return "[" . $matches[1] . "]";
            }
        }

        return isset($matches[0]) ? $matches[0] : $matches;
    }

    public static function getAllSupportedVariablesOptions()
    {
        $normalOptionArray = array(
            self::TEMPLATE_ATTR_PRODUCT_NAME => L::t('Name'),
            self::TEMPLATE_ATTR_PRODUCT_DESCRIPTION => L::t('Description'),
            self::TEMPLATE_ATTR_PRODUCT_DESCRIPTION_SHORT => L::t('Short Description'),
            self::TEMPLATE_ATTR_PRODUCT_QTY => L::t('QTY'),
            self::TEMPLATE_ATTR_PRODUCT_PRICE => L::t('Price'),
            self::TEMPLATE_ATTR_PRODUCT_MAIN_IMAGE => L::t('Main Image'),
            self::TEMPLATE_ATTR_PRODUCT_GALLERY => L::t('Gallery'),
            self::TEMPLATE_ATTR_PRODUCT_REFERENCE => L::t('Reference'),
            self::TEMPLATE_ATTR_PRODUCT_SUPPLIER_REFERENCE => L::t('Supplier Reference'),
            self::TEMPLATE_ATTR_PRODUCT_EAN13 => L::t('EAN13'),
            self::TEMPLATE_ATTR_PRODUCT_UPC => L::t('UPC'),
            self::TEMPLATE_ATTR_PRODUCT_UNIT_PRICE => L::t('Unit price'),
            self::TEMPLATE_ATTR_PRODUCT_UNIT_PRICE_RATIO => L::t('Unit price ratio'),
            self::TEMPLATE_ATTR_PRODUCT_UNIT_TYPE => L::t('Unit type'),
            self::TEMPLATE_ATTR_PRESTA_ATTRIBUTES_MPN => L::t('MPN (PrestaAttributes)'),
            self::TEMPLATE_ATTR_PRESTA_ATTRIBUTES_ISBN => L::t('ISBN (PrestaAttributes)'),
            self::TEMPLATE_ATTR_PRODUCT_MANUFACTURER => L::t('Manufacturer'),
            self::TEMPLATE_ATTR_PRODUCT_SUPPLIER => L::t('Supplier'),
            self::TEMPLATE_ATTR_PRODUCT_TAGS => L::t('Tags'),
            self::TEMPLATE_ATTR_PRODUCT_COMBINATIONS => L::t('Combinations'),
            self::TEMPLATE_ATTR_PRESTABAY_TITLE => L::t('Product ebay title'),
            self::TEMPLATE_ATTR_PRESTABAY_SUBTITLE => L::t('Product ebay Subtitle'),
            self::TEMPLATE_ATTR_PRESTABAY_DESCRIPTION => L::t('Product ebay Description'),
        );

        $totalHtml = '';
        foreach ($normalOptionArray as $optionKey => $optionValue) {
            $totalHtml .= '<option value="[' . $optionKey . ']">' . $optionValue . '</option>';
        }
        $totalHtml .= '<optgroup label="' . L::t("Product Image") . '">';
        for ($i = 0; $i < 5; $i++) {
            $indexImage = $i + 1;
            $totalHtml .= '<option value="[' . self::TEMPLATE_ATTR_PRODUCT_SPECIFIC_IMAGE . '(' . $indexImage . ')]">' . L::t("Image") ." # {$indexImage}" . '</option>';
        }
        $totalHtml .= '</optiongroup>';
        
        $idLang = defined('_USER_ID_LANG_') ? _USER_ID_LANG_ : Configuration::get('PS_LANG_DEFAULT');
        $featuresList = Feature::getFeatures($idLang);
        if (count($featuresList) > 0) {
            $totalHtml .= '<optgroup label="' . L::t("Feature List") . '">';
            foreach ($featuresList as $feature) {
                $totalHtml .= '<option value="[' . self::TEMPLATE_ATTR_PRODUCT_FEATURE . '(' . $feature['id_feature'] . ',' . $feature['name'] . ')]">' . $feature['name'] . '</option>';
            }
            $totalHtml .= '</optiongroup>';
        }
        return $totalHtml;
    }

    public static function getAllSupportedSpecificOptions($selectedValue = '', $includeCustomValueIndicator = true)
    {
        $normalOptionArray = array(
            self::TEMPLATE_ATTR_PRODUCT_QTY => L::t('QTY'),
            self::TEMPLATE_ATTR_PRODUCT_REFERENCE => L::t('Reference'),
            self::TEMPLATE_ATTR_PRODUCT_SUPPLIER_REFERENCE => L::t('Supplier Reference'),
            self::TEMPLATE_ATTR_PRODUCT_EAN13 => L::t('EAN13'),
            self::TEMPLATE_ATTR_PRODUCT_UPC => L::t('UPC'),
            self::TEMPLATE_ATTR_PRODUCT_MANUFACTURER => L::t('Manufacturer'),
            self::TEMPLATE_ATTR_PRODUCT_SUPPLIER => L::t('Supplier'),
            self::TEMPLATE_ATTR_PRODUCT_UNIT_PRICE => L::t('Unit price'),
            self::TEMPLATE_ATTR_PRODUCT_UNIT_PRICE_RATIO => L::t('Unit price ratio'),
            self::TEMPLATE_ATTR_PRODUCT_UNIT_TYPE => L::t('Unit type'),
            self::TEMPLATE_ATTR_PRESTABAY_TITLE => L::t('Product ebay title'),
            self::TEMPLATE_ATTR_PRESTABAY_SUBTITLE => L::t('Product ebay Subtitle'),
            self::TEMPLATE_ATTR_PRESTABAY_DESCRIPTION => L::t('Product ebay Description'),
            self::TEMPLATE_ATTR_PRESTA_ATTRIBUTES_MPN => L::t('MPN (PrestaAttributes)'),
            self::TEMPLATE_ATTR_PRESTA_ATTRIBUTES_ISBN => L::t('ISBN (PrestaAttributes)'),
        );

        if (!is_array($selectedValue)) {
            $selectedValue = array($selectedValue);
        }

        if ($includeCustomValueIndicator) {
            $totalHtml = '<optgroup label="' . L::t("Custom Value") . '">';
            $totalHtml .= '<option value="' . ProfilesModel::SPECIFIC_CUSTOM_VALUE_KEY . '"' . (in_array(ProfilesModel::SPECIFIC_CUSTOM_VALUE_KEY, $selectedValue) ? ' selected="selected"' : '') . '>' . L::t("Choose your own") . '</option>';
        }

        foreach ($normalOptionArray as $optionKey => $optionValue) {
            $isSelectedHtml = in_array(ProfilesModel::SPECIFIC_CUSTOM_ATTRIBUTE_PREFIX . $optionKey, $selectedValue) ? ' selected="selected"' : '';
            $totalHtml .= '<option value="' . ProfilesModel::SPECIFIC_CUSTOM_ATTRIBUTE_PREFIX . $optionKey . '"' . $isSelectedHtml . '>' . $optionValue . '</option>';
        }

        $idLang = defined('_USER_ID_LANG_') ? _USER_ID_LANG_ : Configuration::get('PS_LANG_DEFAULT');
        $featuresList = Feature::getFeatures($idLang);

        if (count($featuresList) > 0) {
            $totalHtml .= '<option value="" class="specific-feature" disabled="disabled">&nbsp;' . L::t("Feature List") . '</option>';
            foreach ($featuresList as $feature) {
                $isSelectedHtml = in_array(ProfilesModel::SPECIFIC_CUSTOM_FEATURE_PREFIX . $feature['id_feature'], $selectedValue) ? ' selected="selected"' : '';
                $totalHtml .= '<option value="' . ProfilesModel::SPECIFIC_CUSTOM_FEATURE_PREFIX . $feature['id_feature'] . '"' . $isSelectedHtml . '>&nbsp;&nbsp;&nbsp;&nbsp;' . $feature['name'] . '</option>';
            }
        }
        if ($includeCustomValueIndicator) {
            $totalHtml .= '</optiongroup>';
        }

        return $totalHtml;
    }

    protected static function _parseGallery($params = array())
    {
        $totalImages = (int) (isset($params[0]) ? $params[0] : 0);
        $coverType = isset($params[1]) ? $params[1] : self::$_profileProductModel->getProfile()->ps_image_type;
        $previewType = 'medium';
        if (CoreHelper::isPS15()) {
            if (strpos($coverType, "_default") !== false) {
                $previewType = "medium_default";
            } 
        }

        if (isset($params[2])) {
            $previewType = $params[2];
        }

        $defaultCoverInfo = array(
            'width' => '300',
            'height' => '300',
        );
        $defaultPreviewInfo = array(
            'width' => '20',
            'height' => '20',
        );
        $coverInfo = ImageType::getByNameNType($coverType, 'products');
        $previewInfo = ImageType::getByNameNType($previewType, 'products');

        if (!$coverInfo) {
            $coverInfo = $defaultCoverInfo;
        }
        if (!$previewInfo) {
            $previewInfo = $defaultPreviewInfo;
        }

        return RenderHelper::view('gallery/base.phtml', array(
            'images' => self::$_profileProductModel->getProductImagesList($totalImages, $coverType, $previewType),
            'coverParams' => $coverInfo,
            'previewParams' => $previewInfo,
                ), false);
    }

    protected static function _parseFeature($featureInfo = '0,n/a')
    {
        $featureInfo = explode(',', $featureInfo);
        $featureId = isset($featureInfo[0]) ? (int) $featureInfo[0] : 0;
        if ($featureId <= 0) {
            return '';
        }

        $featuresList = self::$_profileProductModel->getProduct()->getFrontFeatures(self::$_profileProductModel->getLangId());

        // Go throw all feature to get our required feature
        // This is more quickly that DB request for each feature
        foreach ($featuresList as $feature) {
            if ($feature['id_feature'] == $featureId) {
                return $feature['value'];
            }
        }

        return '';
    }

    /**
     * Get all combination and provide list of available.
     * Returned as ul - li list. When only one option available
     * show also title with main option (for example Color)
     * @return string 
     */
    protected static function _getProductCombinations()
    {
        $product = self::$_profileProductModel->getProduct();
        $langId = self::$_profileProductModel->getLangId();
        $profile = self::$_profileProductModel->getProfile();
        $combinations = VariationHelper::getProductCombinationList($product, $langId, null);

        $totalCombinationString = '';
        $optionKey = '';
        foreach ($combinations as $singleCombination) {
            $totalCombinationString.='<li>';
            ksort($singleCombination['options']);
            $optionsString = '';
            foreach ($singleCombination['options'] as $keyName => $keyValue) {
                if (count($singleCombination['options']) > 1) {
                    $totalCombinationString.=$keyName . ': ' . $keyValue . ', ';
                } else {
                    $totalCombinationString.=$keyValue;
                    $optionKey = $keyName;
                }
            }
            $totalCombinationString = rtrim($totalCombinationString, ', ');
            $totalCombinationString.='</li>';
        }

        $totalCombinationString != '' && $totalCombinationString = '<ul>' . $totalCombinationString . '</ul>';

        $totalCombinationString != '' && $optionKey != '' && $totalCombinationString = '<h4>' . $optionKey . '</h4>' . $totalCombinationString;

        return $totalCombinationString;
    }

}
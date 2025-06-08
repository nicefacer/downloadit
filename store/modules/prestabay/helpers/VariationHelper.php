<?php

/**
 * File VariationHelper.php
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
class VariationHelper
{

    /**
     * Get product attribute combination list
     * @param ProductCore $product
     * @param int $langId language identify
     * @param ProfilesModel $profile
     * @return <type>
     */
    public static function getProductCombinationList($product, $langId, $profile = null)
    {
        $variations = self::generateProductCombinationList($product, $langId, $profile);

        if (empty($variations)) {
            return array();
        }
        // options containt list of option for single ebay variation
        // @todo validate number of options for each combination. Remove
        // combination that different from mostly of option
        $variation = array_values($variations);
        $totalOptions = 0;
        foreach ($variation as $sVariation) {
            $totalOptions+= count($sVariation['options']);
        }
        $avgOptions = round($totalOptions / count($variation));
        foreach ($variation as $key => $sVariation) {
            if (count($sVariation['options']) != $avgOptions) {
                // Unset variation count of options not match avg value
                unset($variation[$key]);
                continue;
            }
            ksort($variation[$key]['options']);
        }

        return $variation;
    }

    /**
     * @param $product
     * @param $langId
     * @param ProfilesModel $profile
     * @return array
     */
    public static function generateProductCombinationList($product, $langId, $profile = null)
    {
        if (CoreHelper::isPS15()) {
            $combinations = $product->getAttributeCombinations($langId);
        } else {
            $combinations = $product->getAttributeCombinaisons($langId);
        }

        $originalPrice = Db::getInstance()->getValue("
                			SELECT price FROM `"._DB_PREFIX_."product`
                    			WHERE `id_product` = ". (int)$product->id, false);

        $doesNotApplyText = '';
        $sendIdentify = false;
        if ($profile) {
            if ($profile->identify_variation == ProfilesModel::IDENTIFY_FOR_VARIATION_YES) {
                $marketplace = new MarketplacesModel((int)$profile->ebay_site);
                if ($profile->identify_not_available == ProfilesModel::IDENTIFY_NOT_AVAILABLE_YES) {
                    $doesNotApplyText = $marketplace->identify_unavailable_text;
                }

                if ($profile->ean == ProfilesModel::EAN_INCLUDE_YES) {
                    $sendIdentify = 'ean';
                } else if ($profile->upc == ProfilesModel::UPC_INCLUDE_YES) {
                    $sendIdentify = 'upc';
                }
            }
        }
        $variations = array();
        $priceDiscount = 0;
        if (!is_null($profile) && $profile->price_discount > 0 && $profile->price_discount < 100) {
            $priceDiscount = $profile->price_discount;
        }
        if (isset($combinations)) {
            foreach ($combinations as $singleCombination) {
                $uniqKey = $singleCombination['id_product_attribute'];
                if (!isset($variations[$uniqKey])) {
                    $variationQtyValue = ($profile != null) ? self::_variationQty($singleCombination['quantity'], $profile) : $singleCombination['quantity'];

                    if (!is_null($profile) && $profile->price_start == ProfilesModel::PRICE_MODE_ORIGINAL_PRICE) {
                        $combinationPrice = $originalPrice; // Force to use original DB field price
                    } else {
                        $combinationPrice = Product::getPriceStatic((int)$singleCombination['id_product'], true, (int)$singleCombination['id_product_attribute']);
                    }
                    $variationPriceValue = ($profile != null) ? self::_variationPrice($combinationPrice, $profile, isset($singleCombination['wholesale_price'])?$singleCombination['wholesale_price']:0) : $combinationPrice;
                    if (isset($singleCombination['minimal_quantity']) && $singleCombination['minimal_quantity'] > 1) {
                        $variationPriceValue = $variationPriceValue * $singleCombination['minimal_quantity'];
                        $variationQtyValue = floor($variationQtyValue / $singleCombination['minimal_quantity']);
                    }

                    $variations[$uniqKey] = array(
                        'id_product_attribute' => $singleCombination['id_product_attribute'],
                        'qty' => $variationQtyValue,
                        'product_qty' => $singleCombination['quantity'],
                        'price' => round($variationPriceValue, 2),
                        'sku' => $singleCombination['reference'],
                        'retailPrice' => 0,
                    );

                    if ($priceDiscount > 0 && $priceDiscount < 100) {
                        $variations[$uniqKey]['retailPrice'] = $variations[$uniqKey]['price'];
                        $variations[$uniqKey]['price'] = $variations[$uniqKey]['price'] * (1 - $priceDiscount / 100);
                    }

                    if ($sendIdentify == 'ean') {
                        if (empty($singleCombination['ean13']) || $singleCombination['ean13'] == '0') {
                            $variations[$uniqKey]['ean'] = $doesNotApplyText;
                        } else {
                            $variations[$uniqKey]['ean'] = $singleCombination['ean13'];
                        }
                    } else if ($sendIdentify == 'upc') {
                        if (empty($singleCombination['upc']) || $singleCombination['upc'] == '0') {
                            $variations[$uniqKey]['upc'] = $doesNotApplyText;
                        } else {
                            $variations[$uniqKey]['upc'] = $singleCombination['upc'];
                        }
                    }
                }
                $variations[$uniqKey]['options'][$singleCombination['group_name']] = $singleCombination['attribute_name'];
            }
        }

        return $variations;
    }

    public static function concatinateVariationArray($newArray, $existingArray)
    {
        $newArray = self::_generateUniqueKeyArray($newArray);

//        exit;
        $existingArray = self::_generateUniqueKeyArray($existingArray);
        foreach ($existingArray as $key => $item) {
            if (!isset($newArray[$key])) {
                $item['qty'] = 0;
                $newArray[$key] = $item;
            }
        }

        return array_values($newArray);
    }

    public static function variationFlatName($singleVariation)
    {
        $name = '';
        foreach ($singleVariation['options'] as $atrributeName => $attributeValue) {
            $name != '' && $name.=', ';
            $name.=$atrributeName.' - '.$attributeValue;
        }
        return $name;
    }

    protected static function _generateUniqueKeyArray($variationArray)
    {
        $returnArray = array();
        foreach ($variationArray as $item) {
            $key = md5(serialize($item['options']));
            $returnArray[$key] = $item;
        }
        return $returnArray;
    }

    /**
     * Get Price for single combination
     * 
     * @param float $combinationPrice
     * @param ProfilesModel $profile
     * @return float
     */
    protected static function _variationPrice($combinationPrice, $profile, $wholesalePrice = 0)
    {
        switch ($profile->price_start) {
            default:
            case ProfilesModel::PRICE_MODE_PRODUCT;
                return $combinationPrice * $profile->price_start_multiply;
            case ProfilesModel::PRICE_MODE_WHOLESALE_PRICE:
                if ($wholesalePrice == 0) {
                    $wholesalePrice = $combinationPrice;
                }
                return $wholesalePrice * $profile->price_start_multiply;
            case ProfilesModel::PRICE_MODE_CUSTOM:
                return $profile->price_start_custom * $profile->price_start_multiply;
            case ProfilesModel::PRICE_MODE_TEMPLATE:
                return $profile->price_start_multiply * Price_TemplateModel::getParsedPrice($profile->price_start_template, $combinationPrice);
        }
    }

    /**
     * Get QTY for single combination
     * 
     * @param int $combinationQty
     * @param ProfilesModel $profile
     * @return int
     */
    protected static function _variationQty($combinationQty, $profile)
    {
        switch ($profile->item_qty_mode) {
            case ProfilesModel::ITEM_QTY_MODE_SINGLE:
                return 1;
                break;
            case ProfilesModel::ITEM_QTY_MODE_CUSTOM:
                return $profile->item_qty_value;
                break;
            case ProfilesModel::ITEM_QTY_MODE_NOT_MORE_THAT:
                return $combinationQty < $profile->item_qty_value ? $combinationQty : $profile->item_qty_value;
                break;

            case ProfilesModel::ITEM_QTY_MODE_RESERVED_VALUE:
                $currentValue = $combinationQty - $profile->item_qty_value;
                return $currentValue < 0 ? 0: $currentValue;
                break;

            default:
            case ProfilesModel::ITEM_QTY_MODE_PRODUCT:
                return $combinationQty;
        }
    }

    public static function variationSearch($searchIn, $needToFind)
    {
        if (empty($needToFind) || empty($searchIn)) {
            return false;
        }
        $searchInNew = array();
        foreach ($searchIn as $key => $value) {
            $nv = array();
            foreach ($value['options'] as $k => $o) {
                $nv[trim($k)] = $o;
            }
            $searchInNew[trim($key)] = $value;
            $searchInNew[trim($key)]['options'] =  $nv;
        }
        $searchIn = $searchInNew;


        foreach ($searchIn as $key => $value) {
            $exists = true;
            foreach ($needToFind as $skey => $svalue) {
                $exists = ($exists && isset($searchIn[$key]['options'][$skey]) && trim($searchIn[$key]['options'][$skey]) == trim($svalue));
            }
            if ($exists) {
                return $key;
            }
        }

        return false;
    }

    public static function getVariationImages($product, $langId)
    {
        if (CoreHelper::isPS15()) {
            $combinations = $product->getAttributeCombinations($langId);
        } else {
            $combinations = $product->getAttributeCombinaisons($langId);
        }
        $variationsOptions = array();
        $colorGroups       = array();
        if (!isset($combinations) || count($combinations) == 0) {
            return array();
        }
        foreach ($combinations as $singleCombination) {
            $variationsOptions[$singleCombination['group_name']][$singleCombination['attribute_name']][] = $singleCombination['id_product_attribute'];
            if ($singleCombination['is_color_group'] && !in_array($singleCombination['group_name'], $colorGroups)) {
                $colorGroups[] = $singleCombination['group_name'];
            }
        }
        if (empty($variationsOptions)) {
            return array();
        }

        if (count($colorGroups) > 0) {
            $index         = reset($colorGroups);
            $selectedGroup = $variationsOptions[$index];
        } else {
            reset($variationsOptions);
            $index         = key($variationsOptions);
            $selectedGroup = $variationsOptions[$index];
        }

        $variationImages = array();
        foreach ($selectedGroup as $groupKey => $groupOptions) {
            foreach ($groupOptions as $productAttributeId) {
                $attributeImages = Product::_getAttributeImageAssociations($productAttributeId);
                foreach ($attributeImages as $imageId) {
                    $variationImages[$index][$groupKey][$imageId] = $imageId;
                }
            }
        }
        return $variationImages;


    }

}
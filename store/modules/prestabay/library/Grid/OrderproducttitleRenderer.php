<?php

/**
 * File OrderProductTitleRenderer.php
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
class Grid_OrderproducttitleRenderer extends Grid_TextRenderer
{

    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {
        $baseTitle = isset($row[$fieldKey]) ? $row[$fieldKey] : "";
        if (isset($row['variation_info']) && !is_null($row['variation_info'])) {
            $variationList = unserialize($row['variation_info']);
            $advVariationInfo = "";
            if (is_array($variationList)) {
                foreach ($variationList as $variationKey => $variationValue) {
                    $advVariationInfo != "" && $advVariationInfo .= ", ";

                    $advVariationInfo .= $variationKey . ' : ' . $variationValue;
                }
                $baseTitle .= ' - ' . $advVariationInfo;
            }
        } else if (isset($row['presta_attr_id']) && $row['presta_attr_id'] > 0) {
            $productModel = new Product($row['presta_id'], $row['presta_lang_id']);
            $combinations = CoreHelper::getAttributeCombinationsById($productModel,
                $row['presta_attr_id'], $row['presta_lang_id']);
            $totalOption = "";
            foreach ($combinations as $combination) {
                $totalOption != "" && $totalOption .= ", ";
                $totalOption .= $combination['group_name'] . ": " . $combination['attribute_name'];
            }
            $baseTitle .= " [" . $totalOption . "]";
        }

        if (!empty($row['presta_id'])) {
            $baseTitle .= " — <a class='desoration-underline' href='" .
                UrlHelper::getPrestaUrl(
                    "AdminProducts",
                    array(
                        'id_product' => $row['presta_id'],
                        'updateproduct' => null
                    )
                ) .
                "'>" . L::t("PrestaShop") . "</a>";
        }

        if (isset($row['item_id'])) {
            $baseTitle .= ' — <a href="' .
                EbayHelper::getItemPath($row['item_id'], AccountsModel::ACCOUNT_MODE_LIVE) . '">' .
                L::t('ebay') .
                '</a>';
        }
        return $baseTitle;
    }
}
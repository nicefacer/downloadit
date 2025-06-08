<?php
/**
 * File ProfilesHelper.php
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


class ProfilesHelper
{

    public static function getPriceOptions($priceValue, $priceTemplateValue = null)
    {
        $totalHtml = '';
        $totalHtml .= '<option value="' . ProfilesModel::PRICE_MODE_PRODUCT . '"' . (($priceValue == ProfilesModel::PRICE_MODE_PRODUCT) ? " selected='selected'" : "") . '>'.L::t("Product Price").'</option>';
        $totalHtml .= '<option value="' . ProfilesModel::PRICE_MODE_ORIGINAL_PRICE . '"' . (($priceValue == ProfilesModel::PRICE_MODE_ORIGINAL_PRICE) ? " selected='selected'" : "") . '>'.L::t("Original Product Price").'</option>';
        $totalHtml .= '<option value="' . ProfilesModel::PRICE_MODE_WHOLESALE_PRICE . '"' . (($priceValue == ProfilesModel::PRICE_MODE_WHOLESALE_PRICE) ? " selected='selected'" : "") . '>'.L::t("Wholesale Product Price").'</option>';
        $totalHtml .= '<option value="' . ProfilesModel::PRICE_MODE_CUSTOM . '"' . (($priceValue == ProfilesModel::PRICE_MODE_CUSTOM) ? " selected='selected'" : "") . '>'.L::t("Custom Value").'</option>';
        $templatesList = Price_TemplateModel::getTemplatesList();
        if (count($templatesList) > 0) {
            $totalHtml .= '<optgroup label="'.L::t("Price Templates").'">';
            foreach ($templatesList as $template) {
                $totalHtml .= '<option value="p-' . $template['id'] . '" ' . (($priceTemplateValue == $template['id']) ? 'selected="selected"' : '') . '>' . $template['name'] . '</option>';
            }
            $totalHtml .= '</optiongroup>';
        }
        return $totalHtml;
    }

    public static function getDescriptionTemplatesOptions($templateId = null)
    {
        $totalHtml = '';
        $templatesList = Description_TemplateModel::getTemplatesList();
        if (count($templatesList) > 0) {
            $totalHtml .= '<optgroup label="'.L::t("Description Templates").'">';
            foreach ($templatesList as $template) {
                $totalHtml .= '<option value="d-' . $template['id'] . '" ' . (($templateId == $template['id']) ? 'selected="selected"' : '') . '>' . $template['name'] . '</option>';
            }
            $totalHtml .= '</optiongroup>';
        }
        return $totalHtml;
    }

    public static function getStoreMappingOptions($mappingId = null)
    {
        $totalHtml = '';
        $mappingList = Mapping_EbayStoreModel::getMappingList();
        if (count($mappingList) > 0) {
            $totalHtml .= '<optgroup label="'.L::t("Ebay Store Mappings").'">';
            foreach ($mappingList as $mapping) {
                $totalHtml .= '<option value="d-' . $mapping['id'] . '" ' . (($mappingId == $mapping['id']) ? 'selected="selected"' : '') . '>' . $mapping['name'] . '</option>';
            }
            $totalHtml .= '</optiongroup>';
        }
        return $totalHtml;
    }

    public static function reformatKeyLabel($keyLabelArray)
    {
        if (!is_array($keyLabelArray)) {
            $keyLabelArray = array();
        }
        $reformatedArray = array();
        foreach ($keyLabelArray as $arrayItem) {
            $reformatedArray[$arrayItem['id']] = $arrayItem['label'];
        }
        return $reformatedArray;
    }

}
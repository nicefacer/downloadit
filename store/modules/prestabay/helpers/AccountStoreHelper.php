<?php
/**
 * File AccountStoreHelper.php
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


class AccountStoreHelper
{
    const STORE_NOT_AVAILABLE = 'no-store';

    public static function getAccountStoreInformation($accountId)
    {
        $accountModel = new AccountsModel($accountId);
        if (!$accountModel->id) {
            return false;
        }
        $storeInfo = PrestabayCache::get("account-store-" . $accountModel->id);
        if (!$storeInfo) {
            ApiModel::getInstance()->reset();
            $storeInfo = ApiModel::getInstance()->ebay->account->getStoreInformation(array('token' => $accountModel->token))->post();
            if ($storeInfo == false) {
                $storeInfo = self::STORE_NOT_AVAILABLE;
            }
            PrestabayCache::set("account-store-" . $accountModel->id, $storeInfo);
        }
        return $storeInfo;
    }

   public static function getAccountShippingDiscountProfiles($accountId)
    {
        $accountModel = new AccountsModel($accountId);
        if (!$accountModel->id) {
            return false;
        }
        $shippingDiscount = PrestabayCache::get("account-shipping-discount-" . $accountModel->id);
        if (!$shippingDiscount) {
            ApiModel::getInstance()->reset();
            $shippingDiscount = ApiModel::getInstance()->ebay
                    ->account
                    ->getShippingDiscountProfiles(array('token' => $accountModel->token))->post();
            
            if ($shippingDiscount == false) {
                $shippingDiscount = array();
            }
            $discountList = array();
            foreach ($shippingDiscount as $key => $item) {
                $discountList[] = array(
                    'id' => $key,
                    'label' => $item
                );
            }

            $shippingDiscount = $discountList;
            PrestabayCache::set("account-shipping-discount-" . $accountModel->id, $shippingDiscount);
        }
        return $shippingDiscount;
    }

    public static function getCategoryAsOptions($accountId, $addEmpty = false, $selectedOptionId = false)
    {
        $storeInfo = self::getAccountStoreInformation($accountId);
        if ($storeInfo == self::STORE_NOT_AVAILABLE) {
            return '';
        }
        $optionList = self::_parseCategoryTree($storeInfo['categories'], 0, $selectedOptionId);
        if ($addEmpty) {
            $optionList = '<option value=""></option>' . $optionList;
        }
        return $optionList;
    }

    protected static function _parseCategoryTree($tree, $level = 0, $selectedId = false, $parentId = 0)
    {
        $returnString = "";

        $level++;
        foreach ($tree as $item) {
            $clearCategoryName = htmlspecialchars_decode($item['name']);
            $optionName = str_repeat("&nbsp;&nbsp;", $level - 1) . $clearCategoryName;

            if (isset($item['leaf']) && !$item['leaf']) {
                $returnString.="<optgroup label='{$optionName}' parentId='".$parentId."' value='".$item['id']."' categoryName='".$clearCategoryName."'>";
            } else {
                $returnString.="<option value='{$item['id']}'" . ($selectedId == $item['id'] ? ' selected="selected"' : '') . " parentId='".$parentId."' categoryName='".$clearCategoryName."'>" . $optionName . "</option>";
            }
            if (isset($item['leaf']) && !$item['leaf'] && isset($item['children'])) {
                $returnString.= self::_parseCategoryTree($item['children'], $level, $selectedId, $item['id']);
            }
            if (isset($item['leaf']) && !$item['leaf']) {
                $returnString.="</optiongoup>";
            }
        }
        return $returnString;
    }

}

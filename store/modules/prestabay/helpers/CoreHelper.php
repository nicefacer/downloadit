<?php

/**
 * File CoreHelper.php
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
class CoreHelper
{

    protected static $_is16 = null;
    protected static $_is15 = null;
    protected static $_is14 = null;
    protected static $_is13 = null;

    public static function isPS16()
    {
        if (is_null(self::$_is16)) {
            self::$_is16 = substr(_PS_VERSION_, 0, 3) == '1.6';
        }
        return self::$_is16;
    }

    public static function isPS15()
    {
        return self::isOnlyPS15() || self::isPS16();
    }

    public static function isOnlyPS15()
    {
        if (is_null(self::$_is15)) {
            self::$_is15 = substr(_PS_VERSION_, 0, 3) == '1.5';
        }

        return self::$_is15;
    }

    public static function isPS14()
    {
        if (is_null(self::$_is14)) {
            self::$_is14 = substr(_PS_VERSION_, 0, 3) == '1.4';
        }
        return self::$_is14;
    }

    public static function isPS13()
    {
        if (is_null(self::$_is13)) {
            self::$_is13 = substr(_PS_VERSION_, 0, 3) == '1.3';
        }
        return self::$_is13;
    }

    public static function createMultiLangField($field)
    {
        $languages = Language::getLanguages(false);
        $res = array();
        foreach ($languages AS $lang)
            $res[$lang['id_lang']] = $field;
        return $res;
    }

    public static function getAttributeCombinationsById($productModel, $idAttribute, $idLanguage)
    {
        if (self::isPS15()) {
            return $productModel->getAttributeCombinationsById($idAttribute, $idLanguage);
        } else {
            $sql = 'SELECT pa.*, ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, al.`name` AS attribute_name,
					a.`id_attribute`, pa.`unit_price_impact`
				FROM `' . _DB_PREFIX_ . 'product_attribute` pa
				LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $idLanguage . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $idLanguage . ')
				WHERE pa.`id_product` = ' . (int) $productModel->id . '
				AND pa.`id_product_attribute` = ' . (int) $idAttribute . '
				GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
				ORDER BY pa.`id_product_attribute`';
            return Db::getInstance()->ExecuteS($sql);
        }
    }

}

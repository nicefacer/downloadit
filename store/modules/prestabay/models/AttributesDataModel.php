<?php

/**
 * File: AttributesDataModel
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
 * PrestaAttributes additional attributes to PrestaShop product.
 * Add MPN and ISBN attributes
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2015 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
class AttributesDataModel
{
    protected static $cache = array();

    protected static $status = null;

    public static function loadByProductId($productId)
    {
        if (ConfigurationCore::get('INVATTR_VERSION_DATA') === false) {
            return array(
                'mpn' => false,
                'isbn' => false,
            );
        }

        if (isset(self::$cache[$productId])) {
            return self::$cache[$productId];
        }

        $sql = "SELECT attribute_mpn, attribute_isbn FROM " . _DB_PREFIX_ . "product
            WHERE id_product = " . (int)$productId;

        $row = Db::getInstance()->getRow($sql, false);

        self::$cache[$productId] = array(
            'mpn' => isset($row['attribute_mpn']) ? $row['attribute_mpn'] : false,
            'isbn' => isset($row['attribute_isbn']) ? $row['attribute_isbn'] : false,
        );

        return self::$cache[$productId];
    }
}
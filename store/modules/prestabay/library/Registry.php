<?php

/**
 * File Registry.php
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
class Registry
{

    protected static $_fieldsList = array();

    public static function has($key)
    {
        return isset(self::$_fieldsList[$key]);
    }

    public static function set($key, $value)
    {
        if (self::has($key)) {
            return false;
        }
        self::$_fieldsList[$key] = $value;
        return true;
    }

    public static function get($get)
    {
        if (!self::has($key)) {
            return false;
        }
        return self::$_fieldsList[$key];
    }

    public static function clear($key)
    {
        if (!self::has($key)) {
            return false;
        }
        unset(self::$_fieldsList[$key]);
        return true;
    }

}
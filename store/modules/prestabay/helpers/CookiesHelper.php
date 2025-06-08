<?php

/**
 * File CookiesHelper.php
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
class CookiesHelper
{

    protected static $_savedCookies = array();

    public static function set($name, $value)
    {
        self::$_savedCookies[$name] = $value;

        if (!is_string($value)) {
            $value = json_encode($value);
        }

        setcookie($name, $value);
    }

    public static function get($name, $unjson = false)
    {
        $value = isset(self::$_savedCookies[$name]) ? self::$_savedCookies[$name] : (isset($_COOKIE[$name]) ? $_COOKIE[$name] : false);
        if ($value && !is_array($value) && $unjson) {            
            $value = json_decode($value, true);
        }

        return $value;
    }

    public static function clear($name)
    {
        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
        }

        if (isset(self::$_savedCookies[$name])) {
            unset(self::$_savedCookies[$name]);
        }
    }

}
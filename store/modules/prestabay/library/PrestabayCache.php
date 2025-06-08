<?php
/**
 * File PrestabayCache.php
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

class PrestabayCache
{

    protected static $_cacheDir = 'prestabay/var/cache';

    protected static function _checkAndCreateDir($cacheDir)
    {
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir);
            @chmod($cacheDir, 0755);
        }
    }

    public static function set($key, $value, $expired = 0)
    {
        $cacheDir = _PS_MODULE_DIR_ . self::$_cacheDir;
        self::_checkAndCreateDir($cacheDir);

        $cacheValue = array(
            'expired' => $expired,
            'value' => $value,
        );
        $cacheFile = $cacheDir . "/" . $key . ".dat";
        if (!file_put_contents($cacheFile, serialize($cacheValue))) {
            return false;
        }
        return true;
    }

    public static function get($key)
    {
        $cacheDir = _PS_MODULE_DIR_ . self::$_cacheDir;
        self::_checkAndCreateDir($cacheDir);
        $cacheFile = $cacheDir . "/" . $key . ".dat";
        if (!file_exists($cacheFile)) {
            return false;
        }
        $cachedData = file_get_contents($cacheFile);
        if (!is_string($cachedData)) {
            return false;
        }

        $cachedData = unserialize($cachedData);
        if (!is_array($cachedData)) {
            return false;
        }

        if (!isset($cachedData['expired']) || !isset($cachedData['value']) || ($cachedData['expired'] != 0 && $cachedData['expired'] < time())) {
            self::delete($key);
            return false;
        }

        return $cachedData['value'];
    }

    public static function delete($key)
    {
        $cacheDir = _PS_MODULE_DIR_ . self::$_cacheDir;
        self::_checkAndCreateDir($cacheDir);
        $cacheFile = $cacheDir . "/" . $key . ".dat";
        if (!file_exists($cacheFile)) {
            return false;
        }
        return unlink($cacheFile);
    }

}
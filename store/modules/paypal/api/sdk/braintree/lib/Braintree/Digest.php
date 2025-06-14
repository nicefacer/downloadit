<?php
/**
 *  2007-2024 PayPal
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 2007-2024 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Braintree;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Digest encryption module
 * Digest creates an HMAC-SHA1 hash for encrypting messages
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
class Digest
{
    public static function hexDigestSha1($key, $string)
    {
        if (function_exists('hash_hmac')) {
            return self::_builtInHmacSha1($string, $key);
        } else {
            return self::_hmacSha1($string, $key);
        }
    }

    public static function hexDigestSha256($key, $string)
    {
        return hash_hmac('sha256', $string, hash('sha256', $key, true));
    }

    public static function secureCompare($left, $right)
    {
        if (strlen($left) != strlen($right)) {
            return false;
        }

        $leftBytes = unpack('C*', $left);
        $rightBytes = unpack('C*', $right);

        $result = 0;
        for ($i = 1; $i <= count($leftBytes); ++$i) {
            $result = $result | ($leftBytes[$i] ^ $rightBytes[$i]);
        }

        return $result == 0;
    }

    public static function _builtInHmacSha1($message, $key)
    {
        return hash_hmac('sha1', $message, sha1($key, true));
    }

    public static function _hmacSha1($message, $key)
    {
        $pack = 'H40';
        $keyDigest = sha1($key, true);
        $innerPad = str_repeat(chr(0x36), 64);
        $outerPad = str_repeat(chr(0x5C), 64);

        for ($i = 0; $i < 20; ++$i) {
            $innerPad[$i] = $keyDigest[$i] ^ $innerPad[$i];
            $outerPad[$i] = $keyDigest[$i] ^ $outerPad[$i];
        }

        return sha1($outerPad . pack($pack, sha1($innerPad . $message)));
    }
}
class_alias('Braintree\Digest', 'Braintree_Digest');

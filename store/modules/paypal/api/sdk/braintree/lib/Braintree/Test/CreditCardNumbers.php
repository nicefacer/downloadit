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

namespace Braintree\Test;

if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * Credit card information used for testing purposes
 *
 * The constants contained in the Test\CreditCardNumbers class provide
 * credit card numbers that should be used when working in the sandbox environment.
 * The sandbox will not accept any credit card numbers other than the ones listed below.
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
class CreditCardNumbers
{
    public static $amExes = [
        '378282246310005',
        '371449635398431',
        '378734493671000',
        ];
    public static $carteBlanches = ['30569309025904'];
    public static $dinersClubs = ['38520000023237'];
    public static $discoverCards = [
        '6011111111111117',
        '6011000990139424',
        ];
    public static $JCBs = [
        '3530111333300000',
        '3566002020360505',
        ];

    public static $masterCard = '5555555555554444';
    public static $masterCardInternational = '5105105105105100';
    public static $masterCards = [
        '5105105105105100',
        '5555555555554444',
        ];

    public static $visa = '4012888888881881';
    public static $visaInternational = '4009348888881881';
    public static $visas = [
        '4009348888881881',
        '4012888888881881',
        '4111111111111111',
        '4000111111111115',
        ];

    public static $unknowns = [
        '1000000000000008',
        ];

    public static $failsSandboxVerification = [
        'AmEx' => '378734493671000',
        'Discover' => '6011000990139424',
        'MasterCard' => '5105105105105100',
        'Visa' => '4000111111111115',
        ];

    public static $amexPayWithPoints = [
        'Success' => '371260714673002',
        'IneligibleCard' => '378267515471109',
        'InsufficientPoints' => '371544868764018',
        ];

    public static function getAll()
    {
        return array_merge(
                self::$amExes,
                self::$discoverCards,
                self::$masterCards,
                self::$visas
                );
    }
}
class_alias('Braintree\Test\CreditCardNumbers', 'Braintree_Test_CreditCardNumbers');

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
if (!defined('_PS_VERSION_')) {
    exit;
}

class ConfigurationMap
{
    const ENABLE_BNPL = 'PAYPAL_ENABLE_BNPL';

    const ENABLE_INSTALLMENT = 'PAYPAL_ENABLE_INSTALLMENT';

    const ADVANCED_OPTIONS_INSTALLMENT = 'PAYPAL_ADVANCED_OPTIONS_INSTALLMENT';

    const PRODUCT_PAGE = 'PAYPAL_INSTALLMENT_PRODUCT_PAGE';

    const HOME_PAGE = 'PAYPAL_INSTALLMENT_HOME_PAGE';

    const CART_PAGE = 'PAYPAL_INSTALLMENT_CART_PAGE';

    const CHECKOUT_PAGE = 'PAYPAL_INSTALLMENT_CHECKOUT_PAGE';

    const CATEGORY_PAGE = 'PAYPAL_INSTALLMENT_CATEGORY_PAGE';

    const COLOR = 'PAYPAL_INSTALLMENT_COLOR';

    const COLOR_BLUE = 'blue';

    const COLOR_GRAY = 'gray';

    const COLOR_BLACK = 'black';

    const COLOR_WHITE = 'white';

    const COLOR_MONOCHROME = 'monochrome';

    const COLOR_GRAYSCALE = 'grayscale';

    const ALL_COLORS = [
        self::COLOR_BLACK,
        self::COLOR_BLUE,
        self::COLOR_WHITE,
        self::COLOR_GRAY,
        self::COLOR_GRAYSCALE,
        self::COLOR_MONOCHROME,
    ];

    const CLIENT_ID = 'PAYPAL_CLIENT_ID_INSTALLMENT';

    const SECRET_ID = 'PAYPAL_SECRET_ID_INSTALLMENT';

    const PAGE_TYPE_PRODUCT = 'product-details';

    const PAGE_TYPE_CART = 'cart';

    const PAGE_TYPE_CHECKOUT = 'checkout';

    public static function getColorGradient($color)
    {
        $gradientMap = [
            self::COLOR_BLUE => '#023188',
            self::COLOR_BLACK => '#000000',
            self::COLOR_WHITE => '#ffffff',
            self::COLOR_MONOCHROME => '#ffffff',
            self::COLOR_GRAYSCALE => '#ffffff',
            self::COLOR_GRAY => '#ebecee',
        ];

        return isset($gradientMap[$color]) ? $gradientMap[$color] : $gradientMap[self::COLOR_BLUE];
    }

    /**
     * @return string
     */
    public static function getClientId()
    {
        if ((int) Configuration::get('PAYPAL_SANDBOX')) {
            return (string) Configuration::get(self::CLIENT_ID . '_SANDBOX');
        } else {
            return (string) Configuration::get(self::CLIENT_ID . '_LIVE');
        }
    }
    /**
     * @return string
     */
    public static function getSecretId()
    {
        if ((int) Configuration::get('PAYPAL_SANDBOX')) {
            return (string) Configuration::get(self::SECRET_ID . '_SANDBOX');
        } else {
            return (string) Configuration::get(self::SECRET_ID . '_LIVE');
        }
    }

    /**
     * @param string $clientId
     *
     * @return bool
     */
    public static function setClientId($clientId)
    {
        if ((int) Configuration::get('PAYPAL_SANDBOX')) {
            return Configuration::updateValue(self::CLIENT_ID . '_SANDBOX', $clientId);
        } else {
            return Configuration::updateValue(self::CLIENT_ID . '_LIVE', $clientId);
        }
    }
    /**
     * @param string $secretId
     *
     * @return bool
     */
    public static function setSecretId($secretId)
    {
        if ((int) Configuration::get('PAYPAL_SANDBOX')) {
            return Configuration::updateValue(self::SECRET_ID . '_SANDBOX', $secretId);
        } else {
            return Configuration::updateValue(self::SECRET_ID . '_LIVE', $secretId);
        }
    }

    /**
     * @return array
     */
    public static function getAllowedCountries()
    {
        return ['fr', 'de', 'gb', 'us', 'au'];
    }

    public static function getLanguageCurrencyMap()
    {
        return [
            ['fr' => 'eur'],
            ['fr' => 'gbp'],
            ['fr' => 'usd'],
            ['fr' => 'aud'],
            ['de' => 'eur'],
            ['de' => 'gbp'],
            ['de' => 'aud'],
            ['de' => 'usd'],
            ['gb' => 'gbp'],
            ['gb' => 'usd'],
            ['gb' => 'aud'],
            ['gb' => 'eur'],
            ['en' => 'gbp'],
            ['en' => 'usd'],
            ['en' => 'aud'],
            ['en' => 'eur'],
        ];
    }

    public static function getBnplLanguageCurrencyMap()
    {
        return [
            ['fr' => 'eur'],
            ['fr' => 'gbp'],
            ['fr' => 'usd'],
            ['fr' => 'aud'],
            ['de' => 'eur'],
            ['de' => 'gbp'],
            ['de' => 'aud'],
            ['de' => 'usd'],
            ['gb' => 'gbp'],
            ['gb' => 'usd'],
            ['gb' => 'aud'],
            ['gb' => 'eur'],
            ['en' => 'gbp'],
            ['en' => 'usd'],
            ['en' => 'aud'],
            ['en' => 'eur'],
            ['it' => 'eur'],
            ['it' => 'gbp'],
            ['it' => 'usd'],
            ['it' => 'aud'],
            ['es' => 'eur'],
            ['es' => 'gbp'],
            ['es' => 'usd'],
            ['es' => 'aud'],
        ];
    }

    public static function getBnplAvailableCountries()
    {
        return ['fr', 'de', 'gb', 'us', 'au', 'it', 'es'];
    }
}

<?php
/**
 * 2007-2024 PayPal
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
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  @copyright PayPal
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class FormatterPaypal
{
    protected $module;

    public function __construct()
    {
        $this->module = Module::getInstanceByName('paypal');
    }
    public function formatPaypalString($str)
    {
        return Tools::substr($str, 0, 126);
    }

    public function formatPrice($price, $isoCurrency = null, $convert = true)
    {
        $context = Context::getContext();
        $context_currency = $context->currency;

        if ($convert && $id_currency_to = $this->module->needConvert()) {
            $currency_to_convert = new Currency($id_currency_to);
            $price = Tools::convertPriceFull($price, $context_currency, $currency_to_convert);
        }

        return number_format($price, $this->getDecimal($isoCurrency), '.', '');
    }

    public function formatLocale($locale)
    {
        $locale = str_replace('-', '_', $locale);
        $parts = explode('_', $locale);

        if (count($parts) != 2) {
            return $locale;
        }

        return implode(
            '_',
            [
                strtolower($parts[0]),
                strtoupper($parts[1]),
            ]
        );
    }

    protected function getDecimal($isoCurrency = null)
    {
        $currency_wt_decimal = ['HUF', 'JPY', 'TWD'];

        if ($isoCurrency === null || Currency::exists($isoCurrency) === false) {
            $isoCurrency = $this->module->getPaymentCurrencyIso();
        }

        if (in_array($isoCurrency, $currency_wt_decimal) || (_PS_PRICE_DISPLAY_PRECISION_ == 0)) {
            return (int) 0;
        } else {
            return (int) 2;
        }
    }
}

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

require_once 'Banner.php';
require_once __DIR__ . '/../Services/CurrencyConverter.php';

class BannerManager
{
    /** @var Banner */
    protected $banner;

    /** @var Context */
    protected $context;

    public function __construct()
    {
        $this->banner = new Banner();
        $this->context = Context::getContext();
    }

    /**
     * @return bool
     */
    public function isBannerAvailable()
    {
        $badLanguageCurrencyContext = true;

        foreach (ConfigurationMap::getLanguageCurrencyMap() as $langCurrency) {
            $isoLang = Tools::strtolower($this->context->language->iso_code);
            $isoCurrency = Tools::strtolower($this->context->currency->iso_code);
            if (isset($langCurrency[$isoLang]) && $langCurrency[$isoLang] == $isoCurrency) {
                $badLanguageCurrencyContext = false;
            }
        }

        if ($badLanguageCurrencyContext) {
            return false;
        }

        if (false === (bool) Configuration::get(ConfigurationMap::ENABLE_INSTALLMENT)) {
            return false;
        }

        if ($this->context->controller instanceof CartController
            && false === (bool) Configuration::get(ConfigurationMap::CART_PAGE)) {
            return false;
        }

        if ($this->context->controller instanceof OrderController
            && false === (bool) Configuration::get(ConfigurationMap::CHECKOUT_PAGE)) {
            return false;
        }

        if ($this->context->controller instanceof ProductController
            && false === (bool) Configuration::get(ConfigurationMap::PRODUCT_PAGE)) {
            return false;
        }

        if ($this->context->controller instanceof IndexController
            && false === (bool) Configuration::get(ConfigurationMap::HOME_PAGE)) {
            return false;
        }

        if ($this->context->controller instanceof CategoryController
            && false === (bool) Configuration::get(ConfigurationMap::CATEGORY_PAGE)) {
            return false;
        }

        $isoCountryDefault = Country::getIsoById(
            (int) Configuration::get(
                'PS_COUNTRY_DEFAULT',
                null,
                null,
                $this->context->shop->id
            )
        );

        if (false === in_array(Tools::strtolower($isoCountryDefault), ConfigurationMap::getAllowedCountries())) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function renderForHomePage()
    {
        return $this->banner
            ->setPlacement('home')
            ->setLayout('flex')
            ->setTemplate(_PS_MODULE_DIR_ . 'paypal/views/templates/installmentBanner/home-banner.tpl')
            ->render();
    }

    /**
     * @return string
     */
    public function renderForCartPage()
    {
        $amount = $this->getCurrencyConverter()->convert($this->context->cart->getOrderTotal(true));

        return $this->banner
            ->setPlacement('cart')
            ->setLayout('text')
            ->setAmount($amount)
            ->setPageTypeAttribute(ConfigurationMap::PAGE_TYPE_CART)
            ->setTemplate(_PS_MODULE_DIR_ . 'paypal/views/templates/installmentBanner/cart-banner.tpl')
            ->render();
    }

    /**
     * @return string
     */
    public function renderForCheckoutPage()
    {
        $amount = $this->getCurrencyConverter()->convert($this->context->cart->getOrderTotal(true));

        return $this->banner
            ->setPlacement('payment')
            ->setLayout('text')
            ->setAmount($amount)
            ->setPageTypeAttribute(ConfigurationMap::PAGE_TYPE_CHECKOUT)
            ->setTemplate(_PS_MODULE_DIR_ . 'paypal/views/templates/installmentBanner/checkout-banner.tpl')
            ->render();
    }

    /**
     * @return string
     */
    public function renderForProductPage()
    {
        return $this->banner
            ->setPlacement('product')
            ->setLayout('text')
            ->setPageTypeAttribute(ConfigurationMap::PAGE_TYPE_PRODUCT)
            ->setTemplate(_PS_MODULE_DIR_ . 'paypal/views/templates/installmentBanner/product-banner.tpl')
            ->render();
    }

    public function getCurrencyConverter()
    {
        return new CurrencyConverter();
    }
}

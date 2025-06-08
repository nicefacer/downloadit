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

require_once __DIR__ . '/../../Builder/BuilderInterface.php';
require_once __DIR__ . '/../../Builder/OrderBuilder.php';
require_once __DIR__ . '/../../Services/Token.php';
require_once __DIR__ . '/../../Services/FormatterPaypal.php';
require_once __DIR__ . '/../ConfigurationMap.php';

class BnplButton
{
    protected $module;

    /** @var BuilderInterface*/
    protected $orderBuilder;

    protected $formatter;

    public function __construct(BuilderInterface $builder = null)
    {
        $this->module = Module::getInstanceByName('paypal');
        $this->formatter = new FormatterPaypal();

        if (is_null($builder)) {
            $this->orderBuilder = $this->initDefaultOrderBuilder();
        }
    }

    public function render()
    {
        return Context::getContext()->smarty
            ->assign('JSvars', $this->getJsVars())
            ->assign($this->getTplVars())
            ->assign('JSscripts', $this->getJS())
            ->fetch($this->getTemplate());
    }

    protected function getJsVars()
    {
    }

    protected function getTplVars()
    {
        return [
            'sdkNamespace' => $this->getSdkNamespace(),
            'order' => $this->orderBuilder->build(),
            'validationController' => Context::getContext()->link->getModuleLink($this->module->name, 'validateBnpl', ['token' => Token::generateByCart(Context::getContext()->cart)])
        ];
    }

    protected function getJS()
    {
        $JSscripts = [];

        $params = [
            'client-id' => ConfigurationMap::getClientId(),
            'intent' => 'capture',
            'currency' => $this->module->getPaymentCurrencyIso(),
            'locale' => $this->formatter->formatLocale(Context::getContext()->language->language_code),
            'components' => 'buttons',
            'enable-funding' => 'paylater',
        ];

        if ($this->module->isSandbox()) {
            if ($buyerCountry = $this->getBuyerCountry()) {
                $params['buyer-country'] = $buyerCountry;
            }
        }

        $JSscripts['tot-paypal-bnpl-sdk'] = [
            'src' => 'https://www.paypal.com/sdk/js?' . http_build_query($params),
            'data-namespace' => $this->getSdkNamespace(),
        ];
        $JSscripts['bnpl'] = [
            'src' => __PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/js/bnpl.js?v=' . $this->module->version,
        ];

        return $JSscripts;
    }

    protected function getTemplate()
    {
        return _PS_MODULE_DIR_ . $this->module->name . '/views/templates/bnpl/bnpl-button.tpl';
    }

    protected function initDefaultOrderBuilder()
    {
        return new OrderBuilder(Context::getContext());
    }

    protected function getBuyerCountry()
    {
        $buyerCountry = Tools::strtoupper(Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT')));
        // https://developer.paypal.com/docs/regional/th/checkout/reference/customize-sdk/
        // According a documentation the available countries are following 'US', 'CA', 'GB', 'DE', 'FR', 'IT', 'ES'
        // But an error was occurring using 'US', 'CA', 'GB' during the test
        if (in_array($buyerCountry, ['DE', 'FR', 'IT', 'ES'])) {
            return $buyerCountry;
        }

        return '';
    }

    protected function getSdkNamespace()
    {
        return 'totPaypalBnplSdkButtons';
    }
}
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

require_once 'ConfigurationMap.php';

class Banner
{
    /** @var \PayPal */
    protected $module;

    /** @var string */
    protected $placement;

    /** @var string */
    protected $layout;

    /** @var float */
    protected $amount;

    /** @var string */
    protected $template;

    /** @var array */
    protected $jsVars;

    /** @var array */
    protected $tplVars;

    /** @var string */
    protected $pageTypeAttribute;

    public function __construct()
    {
        $this->module = Module::getInstanceByName('paypal');
        $this->setTemplate(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/installmentBanner/banner.tpl');
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
        $vars = [];
        if ((int) Configuration::get(ConfigurationMap::ADVANCED_OPTIONS_INSTALLMENT)) {
            $vars['color'] = Configuration::get(ConfigurationMap::COLOR);
        } else {
            $vars['color'] = ConfigurationMap::COLOR_GRAY;
        }

        $vars['placement'] = $this->getPlacement();
        $vars['layout'] = $this->getLayout();

        if ($this->getAmount()) {
            $vars['amount'] = $this->getAmount();
        }

        if (empty($this->jsVars) === false) {
            foreach ($this->jsVars as $name => $value) {
                $vars[$name] = $value;
            }
        }

        return $vars;
    }

    protected function getJS()
    {
        $query = [
            'components' => 'messages',
            'client-id' => ConfigurationMap::getClientId(),
        ];
        $js = [
            'paypal-lib' => [
                'src' => 'https://www.paypal.com/sdk/js?' . http_build_query($query),
                'data-namespace' => 'paypalMessages',
                'data-page-type' => $this->getPageTypeAttribute(),
                'enable-funding' => 'paylater',
            ],
        ];

        if (false === defined('_PS_ADMIN_DIR_')) {
            $js['paypal-lib']['data-partner-attribution-id'] = $this->getPartnerAttributeID();
        }

        return $js;
    }

    /**
     * @return string
     */
    public function getPlacement()
    {
        return $this->placement ? $this->placement : 'home';
    }

    /**
     * @return self
     */
    public function setPlacement($placement)
    {
        $this->placement = (string) $placement;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return (float) $this->amount;
    }

    /**
     * @param float $amount
     *
     * @return Banner
     */
    public function setAmount($amount)
    {
        $this->amount = (float) $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout ? $this->layout : 'flex';
    }

    /**
     * @param string $layout
     *
     * @return Banner
     */
    public function setLayout($layout)
    {
        $this->layout = (string) $layout;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     *
     * @return Banner
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return Banner
     */
    public function addJsVar($name, $value)
    {
        if (is_array($this->jsVars) === false) {
            $this->jsVars = [];
        }

        $this->jsVars[$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    protected function getTplVars()
    {
        if (is_array($this->tplVars)) {
            return $this->tplVars;
        }

        return [];
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return Banner
     */
    public function addTplVar($name, $value)
    {
        if (is_array($this->tplVars) === false) {
            $this->tplVars = [];
        }

        $this->tplVars[$name] = $value;

        return $this;
    }

    /**
     * @return string
     */
    protected function getPartnerAttributeID()
    {
        return (getenv('PLATEFORM') == 'PSREAD') ? 'PrestaShop_Cart_Ready_EC' : 'PRESTASHOP_Cart_SPB';
    }

    /**
     * @return string
     */
    public function getPageTypeAttribute()
    {
        return (string) $this->pageTypeAttribute;
    }

    /**
     * @return Banner
     */
    public function setPageTypeAttribute($pageTypeAttribute)
    {
        if (is_string($pageTypeAttribute)) {
            $this->pageTypeAttribute = $pageTypeAttribute;
        }

        return $this;
    }
}

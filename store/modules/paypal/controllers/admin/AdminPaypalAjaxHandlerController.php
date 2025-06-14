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

class AdminPaypalAjaxHandlerController extends ModuleAdminControllerCore
{
    public function __construct()
    {
        parent::__construct();
    }

    public function displayAjaxHandlePsCheckoutAction()
    {
        $action = Tools::getValue('actionHandled');
        $response = [];

        switch ($action) {
            case 'close':
                $this->module->setPsCheckoutMessageValue(true);
                break;
            case 'install':
                if (is_dir(_PS_MODULE_DIR_ . 'ps_checkout') == false) {
                    $response = [
                        'redirect' => true,
                        'url' => 'https://addons.prestashop.com/en/payment-card-wallet/46347-prestashop-checkout-built-with-paypal.html',
                    ];
                } else {
                    if ($this->installPsCheckout()) {
                        $response = [
                            'redirect' => true,
                            'url' => $this->context->link->getAdminLink('AdminModules') . '&configure=ps_checkout',
                        ];
                    } else {
                        $response = [
                            'redirect' => false,
                            'url' => 'someUrl',
                        ];
                    }
                }
                break;
        }

        exit(json_encode($response));
    }

    protected function installPsCheckout()
    {
        if (Module::isInstalled('ps_checkout')) {
            return true;
        }

        $module = Module::getInstanceByName('ps_checkout');

        return $module->install();
    }
}

<?php
/*
 * 2007-2024 PayPal
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 2007-2024 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  @copyright PayPal
 *
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'paypal/classes/SDK/InjectorInterface.php';
require_once _PS_MODULE_DIR_ . 'paypal/classes/SDK/HttpRequestInterface.php';

class BnCodeInjector implements InjectorInterface
{
    public function inject(&$object)
    {
        if (false === $object instanceof HttpRequestInterface) {
            return;
        }

        $headers = $object->getHeaders();

        if (isset($headers['PayPal-Partner-Attribution-Id'])) {
            return;
        }

        $headers['PayPal-Partner-Attribution-Id'] = 'PRESTASHOP_Cart_BNPLforPS16';
        $object->setHeaders($headers);
    }
}

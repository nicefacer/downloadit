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
class AddOnGateway
{
    /**
     * @var Gateway
     */
    private $_gateway;

    /**
     * @var Configuration
     */
    private $_config;

    /**
     * @var Http
     */
    private $_http;

    /**
     * @param Gateway $gateway
     */
    public function __construct($gateway)
    {
        $this->_gateway = $gateway;
        $this->_config = $gateway->config;
        $this->_config->assertHasAccessTokenOrKeys();
        $this->_http = new Http($gateway->config);
    }

    /**
     * @return AddOn[]
     */
    public function all()
    {
        $path = $this->_config->merchantPath() . '/add_ons';
        $response = $this->_http->get($path);

        $addOns = ['addOn' => $response['addOns']];

        return Util::extractAttributeAsArray(
            $addOns,
            'addOn'
        );
    }
}
class_alias('Braintree\AddOnGateway', 'Braintree_AddOnGateway');

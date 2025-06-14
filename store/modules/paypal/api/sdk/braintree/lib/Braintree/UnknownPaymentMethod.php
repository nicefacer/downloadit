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
 * Braintree UnknownPaymentMethod module
 *
 * @category   Resources
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */

/**
 * Manages Braintree UnknownPaymentMethod
 *
 * <b>== More information ==</b>
 *
 * @category   Resources
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property string $token
 * @property string $imageUrl
 */
class UnknownPaymentMethod extends Base
{
    /**
     *  factory method: returns an instance of UnknownPaymentMethod
     *  to the requesting method, with populated properties
     *
     * @ignore
     *
     * @return UnknownPaymentMethod
     */
    public static function factory($attributes)
    {
        $instance = new self();
        $values = array_values($attributes);
        $instance->_initialize(array_shift($values));

        return $instance;
    }

    /* instance methods */

    /**
     * returns false if default is null or false
     *
     * @return bool
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * sets instance properties from an array of values
     *
     * @param array $unknownPaymentMethodAttribs array of unknownPaymentMethod data
     *
     * @return void
     */
    protected function _initialize($unknownPaymentMethodAttribs)
    {
        // set the attributes
        $this->imageUrl = 'https://assets.braintreegateway.com/payment_method_logo/unknown.png';
        $this->_attributes = $unknownPaymentMethodAttribs;
    }
}
class_alias('Braintree\UnknownPaymentMethod', 'Braintree_UnknownPaymentMethod');

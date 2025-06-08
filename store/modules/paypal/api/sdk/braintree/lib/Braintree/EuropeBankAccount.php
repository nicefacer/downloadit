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
 * Braintree EuropeBankAccount module
 * Creates and manages Braintree Europe Bank Accounts
 *
 * <b>== More information ==</b>
 *
 * See {@link https://developers.braintreepayments.com/javascript+php}<br />
 *
 * @category   Resources
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property string $account-holder-name
 * @property string $bic
 * @property string $customerId
 * @property string $default
 * @property string $image-url
 * @property string $mandate-reference-number
 * @property string $masked-iban
 * @property string $token
 */
class EuropeBankAccount extends Base
{
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
     *  factory method: returns an instance of EuropeBankAccount
     *  to the requesting method, with populated properties
     *
     * @ignore
     *
     * @return EuropeBankAccount
     */
    public static function factory($attributes)
    {
        $defaultAttributes = [
        ];

        $instance = new self();
        $instance->_initialize(array_merge($defaultAttributes, $attributes));

        return $instance;
    }

    /**
     * sets instance properties from an array of values
     *
     * @param array $europeBankAccountAttribs array of EuropeBankAccount properties
     *
     * @return void
     */
    protected function _initialize($europeBankAccountAttribs)
    {
        $this->_attributes = $europeBankAccountAttribs;
    }
}
class_alias('Braintree\EuropeBankAccount', 'Braintree_EuropeBankAccount');

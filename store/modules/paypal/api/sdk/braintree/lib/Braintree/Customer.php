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
 * Braintree Customer module
 * Creates and manages Customers
 *
 * <b>== More information ==</b>
 *
 * For more detailed information on Customers, see {@link http://www.braintreepayments.com/gateway/customer-api http://www.braintreepaymentsolutions.com/gateway/customer-api}
 *
 * @category   Resources
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property array  $addresses
 * @property array  $paymentMethods
 * @property string $company
 * @property string $createdAt
 * @property array  $creditCards
 * @property array  $paypalAccounts
 * @property array  $applePayCards
 * @property array  $androidPayCards
 * @property array  $amexExpressCheckoutCards
 * @property array  $venmoAccounts
 * @property array  $coinbaseAccounts
 * @property array  $customFields custom fields passed with the request
 * @property string $email
 * @property string $fax
 * @property string $firstName
 * @property string $id
 * @property string $lastName
 * @property string $phone
 * @property string $updatedAt
 * @property string $website
 */
class Customer extends Base
{
    /**
     * @return Customer[]
     */
    public static function all()
    {
        return Configuration::gateway()->customer()->all();
    }

    /**
     * @param string $query
     * @param int[] $ids
     *
     * @return Customer|Customer[]
     */
    public static function fetch($query, $ids)
    {
        return Configuration::gateway()->customer()->fetch($query, $ids);
    }

    /**
     * @param array $attribs
     *
     * @return Customer
     */
    public static function create($attribs = [])
    {
        return Configuration::gateway()->customer()->create($attribs);
    }

    /**
     * @param array $attribs
     *
     * @return Customer
     */
    public static function createNoValidate($attribs = [])
    {
        return Configuration::gateway()->customer()->createNoValidate($attribs);
    }

    /**
     * @deprecated since version 2.3.0
     *
     * @param string $queryString
     *
     * @return Result\Successful
     */
    public static function createFromTransparentRedirect($queryString)
    {
        return Configuration::gateway()->customer()->createFromTransparentRedirect($queryString);
    }

    /**
     * @deprecated since version 2.3.0
     *
     * @return string
     */
    public static function createCustomerUrl()
    {
        return Configuration::gateway()->customer()->createCustomerUrl();
    }

    /**
     * @throws Exception\NotFound
     *
     * @param string $id customer id
     *
     * @return Customer
     */
    public static function find($id)
    {
        return Configuration::gateway()->customer()->find($id);
    }

    /**
     * @param int $customerId
     * @param array $transactionAttribs
     *
     * @return Result\Successful|Result\Error
     */
    public static function credit($customerId, $transactionAttribs)
    {
        return Configuration::gateway()->customer()->credit($customerId, $transactionAttribs);
    }

    /**
     * @throws Exception\ValidationError
     *
     * @param type $customerId
     * @param type $transactionAttribs
     *
     * @return Transaction
     */
    public static function creditNoValidate($customerId, $transactionAttribs)
    {
        return Configuration::gateway()->customer()->creditNoValidate($customerId, $transactionAttribs);
    }

    /**
     * @throws Exception on invalid id or non-200 http response code
     *
     * @param int $customerId
     *
     * @return Result\Successful
     */
    public static function delete($customerId)
    {
        return Configuration::gateway()->customer()->delete($customerId);
    }

    /**
     * @param int $customerId
     * @param array $transactionAttribs
     *
     * @return Transaction
     */
    public static function sale($customerId, $transactionAttribs)
    {
        return Configuration::gateway()->customer()->sale($customerId, $transactionAttribs);
    }

    /**
     * @param int $customerId
     * @param array $transactionAttribs
     *
     * @return Transaction
     */
    public static function saleNoValidate($customerId, $transactionAttribs)
    {
        return Configuration::gateway()->customer()->saleNoValidate($customerId, $transactionAttribs);
    }

    /**
     * @throws InvalidArgumentException
     *
     * @param string $query
     *
     * @return ResourceCollection
     */
    public static function search($query)
    {
        return Configuration::gateway()->customer()->search($query);
    }

    /**
     * @throws Exception\Unexpected
     *
     * @param int $customerId
     * @param array $attributes
     *
     * @return Result\Successful|Result\Error
     */
    public static function update($customerId, $attributes)
    {
        return Configuration::gateway()->customer()->update($customerId, $attributes);
    }

    /**
     * @throws Exception\Unexpected
     *
     * @param int $customerId
     * @param array $attributes
     *
     * @return CustomerGateway
     */
    public static function updateNoValidate($customerId, $attributes)
    {
        return Configuration::gateway()->customer()->updateNoValidate($customerId, $attributes);
    }

    /**
     * @deprecated since version 2.3.0
     *
     * @return string
     */
    public static function updateCustomerUrl()
    {
        return Configuration::gateway()->customer()->updateCustomerUrl();
    }

    /**
     * @deprecated since version 2.3.0
     *
     * @param string $queryString
     *
     * @return Result\Successful|Result\Error
     */
    public static function updateFromTransparentRedirect($queryString)
    {
        return Configuration::gateway()->customer()->updateFromTransparentRedirect($queryString);
    }

    /* instance methods */

    /**
     * sets instance properties from an array of values
     *
     * @ignore
     *
     * @param array $customerAttribs array of customer data
     */
    protected function _initialize($customerAttribs)
    {
        $this->_attributes = $customerAttribs;

        $addressArray = [];
        if (isset($customerAttribs['addresses'])) {
            foreach ($customerAttribs['addresses'] as $address) {
                $addressArray[] = Address::factory($address);
            }
        }
        $this->_set('addresses', $addressArray);

        $creditCardArray = [];
        if (isset($customerAttribs['creditCards'])) {
            foreach ($customerAttribs['creditCards'] as $creditCard) {
                $creditCardArray[] = CreditCard::factory($creditCard);
            }
        }
        $this->_set('creditCards', $creditCardArray);

        $coinbaseAccountArray = [];
        if (isset($customerAttribs['coinbaseAccounts'])) {
            foreach ($customerAttribs['coinbaseAccounts'] as $coinbaseAccount) {
                $coinbaseAccountArray[] = CoinbaseAccount::factory($coinbaseAccount);
            }
        }
        $this->_set('coinbaseAccounts', $coinbaseAccountArray);

        $paypalAccountArray = [];
        if (isset($customerAttribs['paypalAccounts'])) {
            foreach ($customerAttribs['paypalAccounts'] as $paypalAccount) {
                $paypalAccountArray[] = PayPalAccount::factory($paypalAccount);
            }
        }
        $this->_set('paypalAccounts', $paypalAccountArray);

        $applePayCardArray = [];
        if (isset($customerAttribs['applePayCards'])) {
            foreach ($customerAttribs['applePayCards'] as $applePayCard) {
                $applePayCardArray[] = ApplePayCard::factory($applePayCard);
            }
        }
        $this->_set('applePayCards', $applePayCardArray);

        $androidPayCardArray = [];
        if (isset($customerAttribs['androidPayCards'])) {
            foreach ($customerAttribs['androidPayCards'] as $androidPayCard) {
                $androidPayCardArray[] = AndroidPayCard::factory($androidPayCard);
            }
        }
        $this->_set('androidPayCards', $androidPayCardArray);

        $amexExpressCheckoutCardArray = [];
        if (isset($customerAttribs['amexExpressCheckoutCards'])) {
            foreach ($customerAttribs['amexExpressCheckoutCards'] as $amexExpressCheckoutCard) {
                $amexExpressCheckoutCardArray[] = AmexExpressCheckoutCard::factory($amexExpressCheckoutCard);
            }
        }
        $this->_set('amexExpressCheckoutCards', $amexExpressCheckoutCardArray);

        $venmoAccountArray = [];
        if (isset($customerAttribs['venmoAccounts'])) {
            foreach ($customerAttribs['venmoAccounts'] as $venmoAccount) {
                $venmoAccountArray[] = VenmoAccount::factory($venmoAccount);
            }
        }
        $this->_set('venmoAccounts', $venmoAccountArray);

        $this->_set('paymentMethods', array_merge(
            $this->creditCards,
            $this->paypalAccounts,
            $this->applePayCards,
            $this->coinbaseAccounts,
            $this->androidPayCards,
            $this->amexExpressCheckoutCards,
            $this->venmoAccounts
        ));
    }

    /**
     * returns a string representation of the customer
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . '[' .
                Util::attributesToString($this->_attributes) . ']';
    }

    /**
     * returns false if comparing object is not a Customer,
     * or is a Customer with a different id
     *
     * @param object $otherCust customer to compare against
     *
     * @return bool
     */
    public function isEqual($otherCust)
    {
        return !($otherCust instanceof Customer) ? false : $this->id === $otherCust->id;
    }

    /**
     * returns an array containt all of the customer's payment methods
     *
     * @deprecated since version 3.1.0 - use the paymentMethods property directly
     *
     * @return array
     */
    public function paymentMethods()
    {
        return $this->paymentMethods;
    }

    /**
     * returns the customer's default payment method
     *
     * @return CreditCard|PayPalAccount
     */
    public function defaultPaymentMethod()
    {
        $defaultPaymentMethods = array_filter($this->paymentMethods, 'Braintree\Customer::_defaultPaymentMethodFilter');

        return current($defaultPaymentMethods);
    }

    public static function _defaultPaymentMethodFilter($paymentMethod)
    {
        return $paymentMethod->isDefault();
    }

    /* private class properties  */

    /**
     * @var array registry of customer data
     */
    protected $_attributes = [
        'addresses' => '',
        'company' => '',
        'creditCards' => '',
        'email' => '',
        'fax' => '',
        'firstName' => '',
        'id' => '',
        'lastName' => '',
        'phone' => '',
        'createdAt' => '',
        'updatedAt' => '',
        'website' => '',
        ];

    /**
     *  factory method: returns an instance of Customer
     *  to the requesting method, with populated properties
     *
     * @ignore
     *
     * @param array $attributes
     *
     * @return Customer
     */
    public static function factory($attributes)
    {
        $instance = new Customer();
        $instance->_initialize($attributes);

        return $instance;
    }
}
class_alias('Braintree\Customer', 'Braintree_Customer');

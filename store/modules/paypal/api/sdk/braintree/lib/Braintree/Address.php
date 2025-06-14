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
 * Braintree Address module
 * PHP Version 5
 * Creates and manages Braintree Addresses
 *
 * An Address belongs to a Customer. It can be associated to a
 * CreditCard as the billing address. It can also be used
 * as the shipping address when creating a Transaction.
 *
 * @copyright 2015 Braintree, a division of PayPal, Inc.
 *
 * @property string $company
 * @property string $countryName
 * @property string $createdAt
 * @property string $customerId
 * @property string $extendedAddress
 * @property string $firstName
 * @property string $id
 * @property string $lastName
 * @property string $locality
 * @property string $postalCode
 * @property string $region
 * @property string $streetAddress
 * @property string $updatedAt
 */
class Address extends Base
{
    /**
     * returns false if comparing object is not a Address,
     * or is a Address with a different id
     *
     * @param object $other address to compare against
     *
     * @return bool
     */
    public function isEqual($other)
    {
        return !($other instanceof self) ?
            false :
            ($this->id === $other->id && $this->customerId === $other->customerId);
    }

    /**
     * create a printable representation of the object as:
     * ClassName[property=value, property=value]
     *
     * @ignore
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . '[' .
                Util::attributesToString($this->_attributes) . ']';
    }

    /**
     * sets instance properties from an array of values
     *
     * @ignore
     *
     * @param array $addressAttribs array of address data
     *
     * @return void
     */
    protected function _initialize($addressAttribs)
    {
        // set the attributes
        $this->_attributes = $addressAttribs;
    }

    /**
     *  factory method: returns an instance of Address
     *  to the requesting method, with populated properties
     *
     * @ignore
     *
     * @return Address
     */
    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);

        return $instance;
    }

    // static methods redirecting to gateway

    /**
     * @param array $attribs
     *
     * @return Address
     */
    public static function create($attribs)
    {
        return Configuration::gateway()->address()->create($attribs);
    }

    /**
     * @param array $attribs
     *
     * @return Address
     */
    public static function createNoValidate($attribs)
    {
        return Configuration::gateway()->address()->createNoValidate($attribs);
    }

    /**
     * @param Customer|int $customerOrId
     * @param int $addressId
     *
     * @throws InvalidArgumentException
     *
     * @return Result\Successful
     */
    public static function delete($customerOrId = null, $addressId = null)
    {
        return Configuration::gateway()->address()->delete($customerOrId, $addressId);
    }

    /**
     * @param Customer|int $customerOrId
     * @param int $addressId
     *
     * @throws Exception\NotFound
     *
     * @return Address
     */
    public static function find($customerOrId, $addressId)
    {
        return Configuration::gateway()->address()->find($customerOrId, $addressId);
    }

    /**
     * @param Customer|int $customerOrId
     * @param int $addressId
     * @param array $attributes
     *
     * @throws Exception\Unexpected
     *
     * @return Result\Successful|Result\Error
     */
    public static function update($customerOrId, $addressId, $attributes)
    {
        return Configuration::gateway()->address()->update($customerOrId, $addressId, $attributes);
    }

    public static function updateNoValidate($customerOrId, $addressId, $attributes)
    {
        return Configuration::gateway()->address()->updateNoValidate($customerOrId, $addressId, $attributes);
    }
}
class_alias('Braintree\Address', 'Braintree_Address');

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

use DateTime;
use InvalidArgumentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Braintree Utility methods
 * PHP version 5
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
class Util
{
    /**
     * extracts an attribute and returns an array of objects
     *
     * extracts the requested element from an array, and converts the contents
     * of its child arrays to objects of type $attributeName, or returns
     * an array with a single element containing the value of that array element
     *
     * @param array $attribArray attributes from a search response
     * @param string $attributeName indicates which element of the passed array to extract
     *
     * @return array array of $attributeName objects, or a single element array
     */
    public static function extractAttributeAsArray(&$attribArray, $attributeName)
    {
        if (!isset($attribArray[$attributeName])) {
            return [];
        }

        // get what should be an array from the passed array
        $data = $attribArray[$attributeName];
        // set up the class that will be used to convert each array element
        $classFactory = self::buildClassName($attributeName) . '::factory';
        if (is_array($data)) {
            // create an object from the data in each element
            $objectArray = array_map($classFactory, $data);
        } else {
            return [$data];
        }

        unset($attribArray[$attributeName]);

        return $objectArray;
    }

    /**
     * throws an exception based on the type of error
     *
     * @param string $statusCode HTTP status code to throw exception from
     * @param string|null $message
     *
     * @throws Exception multiple types depending on the error
     *
     * @return void
     */
    public static function throwStatusCodeException($statusCode, $message = null)
    {
        switch ($statusCode) {
        case 401:
            throw new Exception\Authentication();
            break;
        case 403:
            throw new Exception\Authorization($message);
            break;
        case 404:
            throw new Exception\NotFound();
            break;
        case 426:
            throw new Exception\UpgradeRequired();
            break;
        case 500:
            throw new Exception\ServerError();
            break;
        case 503:
            throw new Exception\DownForMaintenance();
            break;
        default:
            throw new Exception\Unexpected('Unexpected HTTP_RESPONSE #' . $statusCode);
            break;
        }
    }

    /**
     * @param string $className
     * @param object $resultObj
     *
     * @return object returns the passed object if successful
     *
     * @throws Exception\ValidationsFailed
     */
    public static function returnObjectOrThrowException($className, $resultObj)
    {
        $resultObjName = self::cleanClassName($className);
        if ($resultObj->success) {
            return $resultObj->$resultObjName;
        } else {
            throw new Exception\ValidationsFailed();
        }
    }

    /**
     * removes the  header from a classname
     *
     * @param string $name ClassName
     *
     * @return camelCased classname minus  header
     */
    public static function cleanClassName($name)
    {
        $classNamesToResponseKeys = [
            'Braintree\CreditCard' => 'creditCard',
            'Braintree_CreditCard' => 'creditCard',
            'Braintree\CreditCardGateway' => 'creditCard',
            'Braintree_CreditCardGateway' => 'creditCard',
            'Braintree\Customer' => 'customer',
            'Braintree_Customer' => 'customer',
            'Braintree\CustomerGateway' => 'customer',
            'Braintree_CustomerGateway' => 'customer',
            'Braintree\Subscription' => 'subscription',
            'Braintree_Subscription' => 'subscription',
            'Braintree\SubscriptionGateway' => 'subscription',
            'Braintree_SubscriptionGateway' => 'subscription',
            'Braintree\Transaction' => 'transaction',
            'Braintree_Transaction' => 'transaction',
            'Braintree\TransactionGateway' => 'transaction',
            'Braintree_TransactionGateway' => 'transaction',
            'Braintree\CreditCardVerification' => 'verification',
            'Braintree_CreditCardVerification' => 'verification',
            'Braintree\CreditCardVerificationGateway' => 'verification',
            'Braintree_CreditCardVerificationGateway' => 'verification',
            'Braintree\AddOn' => 'addOn',
            'Braintree_AddOn' => 'addOn',
            'Braintree\AddOnGateway' => 'addOn',
            'Braintree_AddOnGateway' => 'addOn',
            'Braintree\Discount' => 'discount',
            'Braintree_Discount' => 'discount',
            'Braintree\DiscountGateway' => 'discount',
            'Braintree_DiscountGateway' => 'discount',
            'Braintree\Plan' => 'plan',
            'Braintree_Plan' => 'plan',
            'Braintree\PlanGateway' => 'plan',
            'Braintree_PlanGateway' => 'plan',
            'Braintree\Address' => 'address',
            'Braintree_Address' => 'address',
            'Braintree\AddressGateway' => 'address',
            'Braintree_AddressGateway' => 'address',
            'Braintree\SettlementBatchSummary' => 'settlementBatchSummary',
            'Braintree_SettlementBatchSummary' => 'settlementBatchSummary',
            'Braintree\SettlementBatchSummaryGateway' => 'settlementBatchSummary',
            'Braintree_SettlementBatchSummaryGateway' => 'settlementBatchSummary',
            'Braintree\Merchant' => 'merchant',
            'Braintree_Merchant' => 'merchant',
            'Braintree\MerchantGateway' => 'merchant',
            'Braintree_MerchantGateway' => 'merchant',
            'Braintree\MerchantAccount' => 'merchantAccount',
            'Braintree_MerchantAccount' => 'merchantAccount',
            'Braintree\MerchantAccountGateway' => 'merchantAccount',
            'Braintree_MerchantAccountGateway' => 'merchantAccount',
            'Braintree\OAuthCredentials' => 'credentials',
            'Braintree_OAuthCredentials' => 'credentials',
            'Braintree\PayPalAccount' => 'paypalAccount',
            'Braintree_PayPalAccount' => 'paypalAccount',
            'Braintree\PayPalAccountGateway' => 'paypalAccount',
            'Braintree_PayPalAccountGateway' => 'paypalAccount',
        ];

        return $classNamesToResponseKeys[$name];
    }

    /**
     * @param string $name className
     *
     * @return string ClassName
     */
    public static function buildClassName($name)
    {
        $responseKeysToClassNames = [
            'creditCard' => 'Braintree\CreditCard',
            'customer' => 'Braintree\Customer',
            'subscription' => 'Braintree\Subscription',
            'transaction' => 'Braintree\Transaction',
            'verification' => 'Braintree\CreditCardVerification',
            'addOn' => 'Braintree\AddOn',
            'discount' => 'Braintree\Discount',
            'plan' => 'Braintree\Plan',
            'address' => 'Braintree\Address',
            'settlementBatchSummary' => 'Braintree\SettlementBatchSummary',
            'merchantAccount' => 'Braintree\MerchantAccount',
        ];

        return (string) $responseKeysToClassNames[$name];
    }

    /**
     * convert alpha-beta-gamma to alphaBetaGamma
     *
     * @param string $string
     * @param string|null $delimiter
     *
     * @return string modified string
     */
    public static function delimiterToCamelCase($string, $delimiter = '[\-\_]')
    {
        // php doesn't garbage collect functions created by create_function()
        // so use a static variable to avoid adding a new function to memory
        // every time this function is called.
        static $callback = null;
        if ($callback === null) {
            $callback = create_function('$matches', 'return strtoupper($matches[1]);');
        }

        return preg_replace_callback('/' . $delimiter . '(\w)/', $callback, $string);
    }

    /**
     * convert alpha-beta-gamma to alpha_beta_gamma
     *
     * @param string $string
     *
     * @return string modified string
     */
    public static function delimiterToUnderscore($string)
    {
        return preg_replace('/-/', '_', $string);
    }

    /**
     * find capitals and convert to delimiter + lowercase
     *
     * @param string $string
     * @param string|null $delimiter
     *
     * @return string modified string
     */
    public static function camelCaseToDelimiter($string, $delimiter = '-')
    {
        return strtolower(preg_replace('/([A-Z])/', "$delimiter\\1", $string));
    }

    public static function delimiterToCamelCaseArray($array, $delimiter = '[\-\_]')
    {
        $converted = [];
        foreach ($array as $key => $value) {
            if (is_string($key)) {
                $key = self::delimiterToCamelCase($key, $delimiter);
            }

            if (is_array($value)) {
                // Make an exception for custom fields, which must be underscore (can't be
                // camelCase).
                if ($key === 'customFields') {
                    $value = self::delimiterToUnderscoreArray($value);
                } else {
                    $value = self::delimiterToCamelCaseArray($value, $delimiter);
                }
            }
            $converted[$key] = $value;
        }

        return $converted;
    }

    public static function camelCaseToDelimiterArray($array, $delimiter = '-')
    {
        $converted = [];
        foreach ($array as $key => $value) {
            if (is_string($key)) {
                $key = self::camelCaseToDelimiter($key, $delimiter);
            }
            if (is_array($value)) {
                $value = self::camelCaseToDelimiterArray($value, $delimiter);
            }
            $converted[$key] = $value;
        }

        return $converted;
    }

    public static function delimiterToUnderscoreArray($array)
    {
        $converted = [];
        foreach ($array as $key => $value) {
            $key = self::delimiterToUnderscore($key);
            $converted[$key] = $value;
        }

        return $converted;
    }

    /**
     * @param array $array associative array to implode
     * @param string $separator (optional, defaults to =)
     * @param string $glue (optional, defaults to ', ')
     *
     * @return bool
     */
    public static function implodeAssociativeArray($array, $separator = '=', $glue = ', ')
    {
        // build a new array with joined keys and values
        $tmpArray = null;
        foreach ($array as $key => $value) {
            if ($value instanceof DateTime) {
                $value = $value->format('r');
            }
            $tmpArray[] = $key . $separator . $value;
        }
        // implode and return the new array
        return (is_array($tmpArray)) ? implode($glue, $tmpArray) : false;
    }

    public static function attributesToString($attributes)
    {
        $printableAttribs = [];
        foreach ($attributes as $key => $value) {
            if (is_array($value)) {
                $pAttrib = self::attributesToString($value);
            } elseif ($value instanceof DateTime) {
                $pAttrib = $value->format(DateTime::RFC850);
            } else {
                $pAttrib = $value;
            }
            $printableAttribs[$key] = sprintf('%s', $pAttrib);
        }

        return self::implodeAssociativeArray($printableAttribs);
    }

    /**
     * verify user request structure
     *
     * compares the expected signature of a gateway request
     * against the actual structure sent by the user
     *
     * @param array $signature
     * @param array $attributes
     */
    public static function verifyKeys($signature, $attributes)
    {
        $validKeys = self::_flattenArray($signature);
        $userKeys = self::_flattenUserKeys($attributes);
        $invalidKeys = array_diff($userKeys, $validKeys);
        $invalidKeys = self::_removeWildcardKeys($validKeys, $invalidKeys);

        if (!empty($invalidKeys)) {
            asort($invalidKeys);
            $sortedList = join(', ', $invalidKeys);
            throw new InvalidArgumentException('invalid keys: ' . $sortedList);
        }
    }

    /**
     * flattens a numerically indexed nested array to a single level
     *
     * @param array $keys
     * @param string $namespace
     *
     * @return array
     */
    private static function _flattenArray($keys, $namespace = null)
    {
        $flattenedArray = [];
        foreach ($keys as $key) {
            if (is_array($key)) {
                $theKeys = array_keys($key);
                $theValues = array_values($key);
                $scope = $theKeys[0];
                $fullKey = empty($namespace) ? $scope : $namespace . '[' . $scope . ']';
                $flattenedArray = array_merge($flattenedArray, self::_flattenArray($theValues[0], $fullKey));
            } else {
                $fullKey = empty($namespace) ? $key : $namespace . '[' . $key . ']';
                $flattenedArray[] = $fullKey;
            }
        }
        sort($flattenedArray);

        return $flattenedArray;
    }

    private static function _flattenUserKeys($keys, $namespace = null)
    {
        $flattenedArray = [];

        foreach ($keys as $key => $value) {
            $fullKey = empty($namespace) ? $key : $namespace;
            if (!is_numeric($key) && $namespace != null) {
                $fullKey .= '[' . $key . ']';
            }
            if (is_numeric($key) && is_string($value)) {
                $fullKey .= '[' . $value . ']';
            }
            if (is_array($value)) {
                $more = self::_flattenUserKeys($value, $fullKey);
                $flattenedArray = array_merge($flattenedArray, $more);
            } else {
                $flattenedArray[] = $fullKey;
            }
        }
        sort($flattenedArray);

        return $flattenedArray;
    }

    /**
     * removes wildcard entries from the invalid keys array
     *
     * @param array $validKeys
     * @param <array $invalidKeys
     *
     * @return array
     */
    private static function _removeWildcardKeys($validKeys, $invalidKeys)
    {
        foreach ($validKeys as $key) {
            if (stristr($key, '[_anyKey_]')) {
                $wildcardKey = str_replace('[_anyKey_]', '', $key);
                foreach ($invalidKeys as $index => $invalidKey) {
                    if (stristr($invalidKey, $wildcardKey)) {
                        unset($invalidKeys[$index]);
                    }
                }
            }
        }

        return $invalidKeys;
    }
}
class_alias('Braintree\Util', 'Braintree_Util');

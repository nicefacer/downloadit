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

namespace Braintree\Error;

use Braintree\Collection;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * collection of errors enumerating all validation errors for a given request
 *
 * <b>== More information ==</b>
 *
 * For more detailed information on Validation errors, see {@link http://www.braintreepayments.com/gateway/validation-errors http://www.braintreepaymentsolutions.com/gateway/validation-errors}
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property array $errors
 * @property array $nested
 */
class ValidationErrorCollection extends Collection
{
    private $_errors = [];
    private $_nested = [];

    /**
     * @ignore
     */
    public function __construct($data)
    {
        foreach ($data as $key => $errorData) {
            // map errors to new collections recursively
            if ($key == 'errors') {
                foreach ($errorData as $error) {
                    $this->_errors[] = new Validation($error);
                }
            } else {
                $this->_nested[$key] = new ValidationErrorCollection($errorData);
            }
        }
    }

    public function deepAll()
    {
        $validationErrors = array_merge([], $this->_errors);
        foreach ($this->_nested as $nestedErrors) {
            $validationErrors = array_merge($validationErrors, $nestedErrors->deepAll());
        }

        return $validationErrors;
    }

    public function deepSize()
    {
        $total = sizeof($this->_errors);
        foreach ($this->_nested as $_nestedErrors) {
            $total = $total + $_nestedErrors->deepSize();
        }

        return $total;
    }

    public function forIndex($index)
    {
        return $this->forKey('index' . $index);
    }

    public function forKey($key)
    {
        return isset($this->_nested[$key]) ? $this->_nested[$key] : null;
    }

    public function onAttribute($attribute)
    {
        $matches = [];
        foreach ($this->_errors as $key => $error) {
            if ($error->attribute == $attribute) {
                $matches[] = $error;
            }
        }

        return $matches;
    }

    public function shallowAll()
    {
        return $this->_errors;
    }

    /**
     * @ignore
     */
    public function __get($name)
    {
        $varName = "_$name";

        return isset($this->$varName) ? $this->$varName : null;
    }

    /**
     * @ignore
     */
    public function __toString()
    {
        $output = [];

        // TODO: implement scope
        if (!empty($this->_errors)) {
            $output[] = $this->_inspect($this->_errors);
        }
        if (!empty($this->_nested)) {
            foreach ($this->_nested as $key => $values) {
                $output[] = $this->_inspect($this->_nested);
            }
        }

        return join(', ', $output);
    }

    /**
     * @ignore
     */
    private function _inspect($errors, $scope = null)
    {
        $eOutput = '[' . __CLASS__ . '/errors:[';
        foreach ($errors as $error => $errorObj) {
            $outputErrs[] = "({$errorObj->error['code']} {$errorObj->error['message']})";
        }
        $eOutput .= join(', ', $outputErrs) . ']]';

        return $eOutput;
    }
}
class_alias('Braintree\Error\ValidationErrorCollection', 'Braintree_Error_ValidationErrorCollection');

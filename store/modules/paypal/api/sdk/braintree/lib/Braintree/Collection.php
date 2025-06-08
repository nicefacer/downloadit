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

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use OutOfRangeException;

if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * Braintree Generic collection
 *
 * PHP Version 5
 *
 * Based on Generic Collection class from:
 * {@link http://codeutopia.net/code/library/CU/Collection.php}
 *
 * @copyright 2015 Braintree, a division of PayPal, Inc.
 */
class Collection implements Countable, IteratorAggregate, ArrayAccess
{
    /**
     * @var array collection storage
     */
    protected $_collection = [];

    /**
     * Add a value into the collection
     *
     * @param string $value
     */
    public function add($value)
    {
        $this->_collection[] = $value;
    }

    /**
     * Set index's value
     *
     * @param int $index
     * @param mixed $value
     *
     * @throws OutOfRangeException
     */
    public function set($index, $value)
    {
        if ($index >= $this->count()) {
            throw new OutOfRangeException('Index out of range');
        }

        $this->_collection[$index] = $value;
    }

    /**
     * Remove a value from the collection
     *
     * @param int $index index to remove
     *
     * @throws OutOfRangeException if index is out of range
     */
    public function remove($index)
    {
        if ($index >= $this->count()) {
            throw new OutOfRangeException('Index out of range');
        }

        array_splice($this->_collection, $index, 1);
    }

    /**
     * Return value at index
     *
     * @param int $index
     *
     * @return mixed
     *
     * @throws OutOfRangeException
     */
    public function get($index)
    {
        if ($index >= $this->count()) {
            throw new OutOfRangeException('Index out of range');
        }

        return $this->_collection[$index];
    }

    /**
     * Determine if index exists
     *
     * @param int $index
     *
     * @return bool
     */
    public function exists($index)
    {
        if ($index >= $this->count()) {
            return false;
        }

        return true;
    }

    /**
     * Return count of items in collection
     * Implements countable
     *
     * @return int
     */
    public function count()
    {
        return count($this->_collection);
    }

    /**
     * Return an iterator
     * Implements IteratorAggregate
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_collection);
    }

    /**
     * Set offset to value
     * Implements ArrayAccess
     *
     * @see set
     *
     * @param int $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Unset offset
     * Implements ArrayAccess
     *
     * @see remove
     *
     * @param int $offset
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * get an offset's value
     * Implements ArrayAccess
     *
     * @see get
     *
     * @param int $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Determine if offset exists
     * Implements ArrayAccess
     *
     * @see exists
     *
     * @param int $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }
}
class_alias('Braintree\Collection', 'Braintree_Collection');

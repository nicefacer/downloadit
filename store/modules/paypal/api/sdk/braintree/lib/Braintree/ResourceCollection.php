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

use Iterator;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Braintree ResourceCollection
 * ResourceCollection is a container object for result data
 *
 * stores and retrieves search results and aggregate data
 *
 * example:
 * <code>
 * $result = Customer::all();
 *
 * foreach($result as $transaction) {
 *   print_r($transaction->id);
 * }
 * </code>
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
class ResourceCollection implements Iterator
{
    private $_index;
    private $_batchIndex;
    private $_items;
    private $_pageSize;
    private $_pager;

    /**
     * set up the resource collection
     *
     * expects an array of attributes with literal keys
     *
     * @param array $response
     * @param array $pager
     */
    public function __construct($response, $pager)
    {
        $this->_pageSize = $response['searchResults']['pageSize'];
        $this->_ids = $response['searchResults']['ids'];
        $this->_pager = $pager;
    }

    /**
     * returns the current item when iterating with foreach
     */
    public function current()
    {
        return $this->_items[$this->_index];
    }

    /**
     * returns the first item in the collection
     *
     * @return mixed
     */
    public function firstItem()
    {
        $ids = $this->_ids;
        $page = $this->_getPage([$ids[0]]);

        return $page[0];
    }

    public function key()
    {
        return null;
    }

    /**
     * advances to the next item in the collection when iterating with foreach
     */
    public function next()
    {
        ++$this->_index;
    }

    /**
     * rewinds the testIterateOverResults collection to the first item when iterating with foreach
     */
    public function rewind()
    {
        $this->_batchIndex = 0;
        $this->_getNextPage();
    }

    /**
     * returns whether the current item is valid when iterating with foreach
     */
    public function valid()
    {
        if ($this->_index == count($this->_items) && $this->_batchIndex < count($this->_ids)) {
            $this->_getNextPage();
        }

        if ($this->_index < count($this->_items)) {
            return true;
        } else {
            return false;
        }
    }

    public function maximumCount()
    {
        return count($this->_ids);
    }

    private function _getNextPage()
    {
        if (empty($this->_ids)) {
            $this->_items = [];
        } else {
            $this->_items = $this->_getPage(array_slice($this->_ids, $this->_batchIndex, $this->_pageSize));
            $this->_batchIndex += $this->_pageSize;
            $this->_index = 0;
        }
    }

    /**
     * requests the next page of results for the collection
     *
     * @return void
     */
    private function _getPage($ids)
    {
        $object = $this->_pager['object'];
        $method = $this->_pager['method'];
        $methodArgs = [];
        foreach ($this->_pager['methodArgs'] as $arg) {
            array_push($methodArgs, $arg);
        }
        array_push($methodArgs, $ids);

        return call_user_func_array(
            [$object, $method],
            $methodArgs
        );
    }
}
class_alias('Braintree\ResourceCollection', 'Braintree_ResourceCollection');

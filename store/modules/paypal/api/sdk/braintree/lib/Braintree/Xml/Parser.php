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

namespace Braintree\Xml;

use Braintree\Util;
use DateTime;
use DateTimeZone;
use DOMDocument;
use DOMElement;
use DOMText;

if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * Braintree XML Parser
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
class Parser
{
    /**
     * Converts an XML string into a multidimensional array
     *
     * @param string $xml
     *
     * @return array
     */
    public static function arrayFromXml($xml)
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->loadXML($xml);

        $root = $document->documentElement->nodeName;

        return Util::delimiterToCamelCaseArray([
            $root => self::_nodeToValue($document->childNodes->item(0)),
        ]);
    }

    /**
     * Converts a node to an array of values or nodes
     *
     * @param DOMNode @node
     *
     * @return mixed
     */
    private static function _nodeToArray($node)
    {
        $type = null;
        if ($node instanceof DOMElement) {
            $type = $node->getAttribute('type');
        }

        switch ($type) {
        case 'array':
            $array = [];
            foreach ($node->childNodes as $child) {
                $value = self::_nodeToValue($child);
                if ($value !== null) {
                    $array[] = $value;
                }
            }

            return $array;
        case 'collection':
            $collection = [];
            foreach ($node->childNodes as $child) {
                $value = self::_nodetoValue($child);
                if ($value !== null) {
                    if (!isset($collection[$child->nodeName])) {
                        $collection[$child->nodeName] = [];
                    }
                    $collection[$child->nodeName][] = self::_nodeToValue($child);
                }
            }

            return $collection;
        default:
            $values = [];
            if ($node->childNodes->length === 1 && $node->childNodes->item(0) instanceof DOMText) {
                return $node->childNodes->item(0)->nodeValue;
            } else {
                foreach ($node->childNodes as $child) {
                    if (!$child instanceof DOMText) {
                        $values[$child->nodeName] = self::_nodeToValue($child);
                    }
                }

                return $values;
            }
        }
    }

    /**
     * Converts a node to a PHP value
     *
     * @param DOMNode $node
     *
     * @return mixed
     */
    private static function _nodeToValue($node)
    {
        $type = null;
        if ($node instanceof DOMElement) {
            $type = $node->getAttribute('type');
        }

        switch ($type) {
        case 'datetime':
            return self::_timestampToUTC((string) $node->nodeValue);
        case 'date':
            return new DateTime((string) $node->nodeValue);
        case 'integer':
            return (int) $node->nodeValue;
        case 'boolean':
            $value = (string) $node->nodeValue;
            if (is_numeric($value)) {
                return (bool) $value;
            } else {
                return ($value !== 'true') ? false : true;
            }
            // no break
        case 'array':
        case 'collection':
            return self::_nodeToArray($node);
        default:
            if ($node->hasChildNodes()) {
                return self::_nodeToArray($node);
            } elseif (trim($node->nodeValue) === '') {
                return null;
            } else {
                return $node->nodeValue;
            }
        }
    }

    /**
     * Converts XML timestamps into DateTime instances
     *
     * @param string $timestamp
     *
     * @return DateTime
     */
    private static function _timestampToUTC($timestamp)
    {
        $tz = new DateTimeZone('UTC');
        $dateTime = new DateTime($timestamp, $tz);
        $dateTime->setTimezone($tz);

        return $dateTime;
    }
}
class_alias('Braintree\Xml\Parser', 'Braintree_Xml_Parser');

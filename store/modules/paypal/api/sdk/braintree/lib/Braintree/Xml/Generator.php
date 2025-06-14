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
use XMLWriter;

if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * PHP version 5
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */

/**
 * Generates XML output from arrays using PHP's
 * built-in XMLWriter
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
class Generator
{
    /**
     * arrays passed to this method should have a single root element
     * with an array as its value
     *
     * @param array $aData the array of data
     *
     * @return string XML string
     */
    public static function arrayToXml($aData)
    {
        $aData = Util::camelCaseToDelimiterArray($aData, '-');
        // set up the XMLWriter
        $writer = new XMLWriter();
        $writer->openMemory();

        $writer->setIndent(true);
        $writer->setIndentString(' ');
        $writer->startDocument('1.0', 'UTF-8');

        // get the root element name
        $aKeys = array_keys($aData);
        $rootElementName = $aKeys[0];
        // open the root element
        $writer->startElement($rootElementName);
        // create the body
        self::_createElementsFromArray($writer, $aData[$rootElementName], $rootElementName);

        // close the root element and document
        $writer->endElement();
        $writer->endDocument();

        // send the output as string
        return $writer->outputMemory();
    }

    /**
     * Construct XML elements with attributes from an associative array.
     *
     * @static
     *
     * @param object $writer XMLWriter object
     * @param array $aData contains attributes and values
     *
     * @return void
     */
    private static function _createElementsFromArray(&$writer, $aData)
    {
        if (!is_array($aData)) {
            if (is_bool($aData)) {
                $writer->text($aData ? 'true' : 'false');
            } else {
                $writer->text($aData);
            }

            return;
        }
        foreach ($aData as $elementName => $element) {
            // handle child elements
            $writer->startElement($elementName);
            if (is_array($element)) {
                if (array_key_exists(0, $element) || empty($element)) {
                    $writer->writeAttribute('type', 'array');
                    foreach ($element as $ignored => $itemInArray) {
                        $writer->startElement('item');
                        self::_createElementsFromArray($writer, $itemInArray);
                        $writer->endElement();
                    }
                } else {
                    self::_createElementsFromArray($writer, $element);
                }
            } else {
                // generate attributes as needed
                $attribute = self::_generateXmlAttribute($element);
                if (is_array($attribute)) {
                    $writer->writeAttribute($attribute[0], $attribute[1]);
                    $element = $attribute[2];
                }
                $writer->text($element);
            }
            $writer->endElement();
        }
    }

    /**
     * convert passed data into an array of attributeType, attributeName, and value
     * dates sent as DateTime objects will be converted to strings
     *
     * @param mixed $value
     *
     * @return array attributes and element value
     */
    private static function _generateXmlAttribute($value)
    {
        if ($value instanceof DateTime) {
            return ['type', 'datetime', self::_dateTimeToXmlTimestamp($value)];
        }
        if (is_int($value)) {
            return ['type', 'integer', $value];
        }
        if (is_bool($value)) {
            return ['type', 'boolean', ($value ? 'true' : 'false')];
        }
        if ($value === null) {
            return ['nil', 'true', $value];
        }
    }

    /**
     * converts datetime back to xml schema format
     *
     * @param object $dateTime
     *
     * @return string XML schema formatted timestamp
     */
    private static function _dateTimeToXmlTimestamp($dateTime)
    {
        $dateTime->setTimeZone(new DateTimeZone('UTC'));

        return $dateTime->format('Y-m-d\TH:i:s') . 'Z';
    }

    private static function _castDateTime($string)
    {
        try {
            if (empty($string)) {
                return false;
            }
            $dateTime = new DateTime($string);

            return self::_dateTimeToXmlTimestamp($dateTime);
        } catch (Exception $e) {
            // not a datetime
            return false;
        }
    }
}
class_alias('Braintree\Xml\Generator', 'Braintree_Xml_Generator');

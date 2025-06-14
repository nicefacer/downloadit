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
include_once _PS_MODULE_DIR_ . 'paypal/api/paypal_connect.php';

define('PAYPAL_API_VERSION', '106.0');

if (!defined('_PS_VERSION_')) {
    exit;
}

class PaypalLib
{
    private $enable_log = false;
    private $_logs = [];
    protected $paypal = null;

    public function __construct()
    {
        $this->paypal = new PayPal();
    }

    public function getLogs()
    {
        return $this->_logs;
    }

    public function makeCall($host, $script, $method_name, $data, $method_version = '')
    {
        // Making request string
        $method_version = (!empty($method_version)) ? $method_version : PAYPAL_API_VERSION;

        $params = [
            'METHOD' => $method_name,
            'VERSION' => $method_version,
            'PWD' => Configuration::get('PAYPAL_API_PASSWORD'),
            'USER' => Configuration::get('PAYPAL_API_USER'),
            'SIGNATURE' => Configuration::get('PAYPAL_API_SIGNATURE'),
        ];

        $request = http_build_query($params, '', '&');
        $request .= '&' . (!is_array($data) ? $data : http_build_query($data, '', '&'));

        // Making connection
        $result = $this->makeSimpleCall($host, $script, $request, true);
        $response = explode('&', $result);
        $logs_request = $this->_logs;
        $return = [];

        if ($this->enable_log === true) {
            $handle = fopen(dirname(__FILE__) . '/Results.txt', 'a+');
            fwrite($handle, 'Host : ' . print_r($host, true) . "\r\n");
            fwrite($handle, 'Request : ' . print_r($request, true) . "\r\n");
            fwrite($handle, 'Result : ' . print_r($result, true) . "\r\n");
            fwrite($handle, 'Logs : ' . print_r($this->_logs, true) . "\r\n");
            fclose($handle);
        }

        foreach ($response as $value) {
            $tmp = explode('=', $value);
            $return[$tmp[0]] = urldecode(!isset($tmp[1]) ? $tmp[0] : $tmp[1]);
        }

        if (!Configuration::get('PAYPAL_DEBUG_MODE')) {
            $this->_logs = [];
        }

        $to_exclude = ['TOKEN', 'SUCCESSPAGEREDIRECTREQUESTED', 'VERSION', 'BUILD', 'ACK', 'CORRELATIONID'];
        $this->_logs[] = '<b>' . $this->paypal->l('PayPal response:') . '</b>';

        foreach ($return as $key => $value) {
            if (!Configuration::get('PAYPAL_DEBUG_MODE') && in_array($key, $to_exclude)) {
                continue;
            }

            $this->_logs[] = $key . ' -> ' . $value;
        }

        // if (count($this->_logs) <= 2) {
        //     $this->_logs = array_merge($this->_logs, $logs_request);
        // }

        return $return;
    }

    public function makeSimpleCall($host, $script, $request, $simple_mode = false)
    {
        // Making connection
        $paypal_connect = new PayPalConnect();

        $result = $paypal_connect->makeConnection($host, $script, $request, $simple_mode);
        $this->_logs = $paypal_connect->getLogs();

        return $result;
    }
}

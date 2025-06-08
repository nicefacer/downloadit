<?php
/**
 * File ApiModel.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * It is available through the world-wide-web at this URL:
 * http://involic.com/license.txt
 * If you are unable to obtain it through the world-wide-web,
 * please send an email to license@involic.com so
 * we can send you a copy immediately.
 *
 * eBay Listener Itegration with PrestaShop e-commerce platform.
 * Adding possibilty list PrestaShop Product dirrectly to eBay.
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2015 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */

class ApiModel
{
    const API_URL = 'http://api.involic.com/api';
    const API_VERSION = '2.0.0';
    const USER_AGENT = "Prestashop-EbayAgent";
    const SOCKET_TIMEOUT = 300;

    /**
     * @var ApiModel
     */
    static private $_instance = NULL;
    protected $_publicApiKey = "he2h5p7trAcrutucreW2tHudef9apr2phapa5ePas49veBruj4gan55eReb8es4u";
    protected $_licenseKey = null;
    protected $_serverHost = null;
    protected $_requestMethod = null;
    protected $_requestParameters = array();
    protected $_errorList = array();
    protected $_warningList = array();
    protected $_skipBreakOutput = false;

    function __construct()
    {
        $dir = __PS_BASE_URI__;
        if (CoreHelper::isPS15()) {
            $dir = Context::getContext()->shop->getBaseURI();
        }

        $this->_serverHost = $_SERVER['HTTP_HOST'] . $dir;
        $this->_licenseKey = ($configValue = Configuration::get('INVEBAY_LICENSE_KEY')) ? $configValue : "";
    }

    /**
     * @return ApiModel
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new ApiModel();
        }
        return self::$_instance;
    }

    /**
     * Reset stored values for current shortcut request
     */
    public function reset()
    {
        $this->_requestMethod = null;
        $this->_requestParameters = null;
        $this->_errorList = array();
        $this->_warningList = array();
        $this->_skipBreakOutput = false;
        return $this;
    }

    public function setSkipBreakOutput($value)
    {
        $this->_skipBreakOutput = $value;
    }

    public function __call($name, $arguments)
    {
        $this->_requestMethod .= "." . $name;
        $requestParams = array();
        if (isset($arguments[0])) {
            $requestParams = $arguments[0];
        }
        $this->_requestParameters = $requestParams;
        return $this;
    }

    public function __get($name)
    {
        if (is_null($this->_requestMethod)) {
            $this->_requestMethod = $name;
        } else {
            $this->_requestMethod .= "." . $name;
        }
        return $this;
    }

    /**
     * Shortcut for get requests.
     *
     * Example of use $api->posts->list(array('limit' => 100))->get();
     *
     * @return array of stdClass
     */
    public function get()
    {
        return $this->call($this->_requestMethod, $this->_requestParameters);
    }

    /**
     * Shortcut for post requests.
     *
     * Example of use $api->threads->close('thread' => $id)->post();
     *
     * @return array execution resule
     */
    public function post()
    {
        return $this->call($this->_requestMethod, $this->_requestParameters, true);
    }

    /**
     * General method for sending request into API server
     *
     * @param string $method calling method from selected category
     * @param array $arguments arguments for request.
     * @param boolean $post identify that is's post request
     * @return array|false data result of request of false on problem. Error can be retrive by
     * getErrors() and getWarnings() function
     */
    public function call($method, $arguments = array(), $post = false)
    {
        $cursor = null;
        $data = false;

        // @todo error handler
        $result = $this->_sendRequest($method, $arguments, $post);
        if ($result && ($result['ask'] == "success" || $result['ask'] == "warning" || $this->_skipBreakOutput)) {
            // On completed request or skiped output
            $data = $result['data'];
        }

        if ($result['ask'] != "success") {
            if (isset($result['warnings'])) {
                foreach ($result['warnings'] as $warning) {
                    $this->addWarning($warning['message']);
                }
            }

            if (isset($result['errors'])) {
                foreach ($result['errors'] as $error) {
                    $this->addError($error['message']);
                }
            }
        }

        return $data;
    }

    /**
     * Low level method that send request to API server
     *
     * @param string $method
     * @param array $arguments
     * @param boolean $post
     * @return array|mixed
     */
    protected function _sendRequest($method, $arguments = array(), $post = false)
    {

        // Append general arguments
        if (!isset($arguments['apikey']) && !is_null($this->_publicApiKey)) {
            $arguments['apikey'] = $this->_publicApiKey;
        }

        if (!isset($arguments['license']) && !is_null($this->_licenseKey)) {
            $arguments['license'] = $this->_licenseKey;
        }

        // Convert arguments to query string
        $queryString = $this->_getAttributesQueryString($arguments);

        // Build url send reguest to
        $requestUrl = self::API_URL . "/method/" . $method;

        $connection = curl_init();
        $curlOptions = array(
            CURLOPT_USERAGENT => self::USER_AGENT,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => ($post ? 1 : 0),
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => $this->_getHeaders(),
            CURLOPT_TIMEOUT => self::SOCKET_TIMEOUT
        );

        if ($post) {
            $curlOptions[CURLOPT_POSTFIELDS] = $queryString;
//            $requestUrl .= '?XDEBUG_SESSION_START=PRESTABAY';
        } else {
            // For get append request url with attributes values
            $requestUrl.="?" . $queryString; // . '&XDEBUG_SESSION_START=PRESTABAY';
        }
        $curlOptions[CURLOPT_URL] = $requestUrl;

        curl_setopt_array($connection, $curlOptions);

        $data = curl_exec($connection);
        $response = false;
//        if ($data) {
        $response['data'] = $data;
        $response['code'] = curl_getinfo($connection, CURLINFO_HTTP_CODE);
//        }
        curl_close($connection);

        return $this->_validateResponse($response);
    }

    protected function _getHeaders()
    {
        return array(
            'X-API-VERSION: ' . self::API_VERSION,
            'X-API-HOST: ' . $this->_serverHost,
        );
    }

    protected function _getAttributesQueryString($attributes)
    {
        return str_replace( "&amp;", "&", http_build_query($attributes));
    }

    protected function _buildResponse($data)
    {
        if ($data == false || $data == null) {
            return false;
        }
        // Separate header from content data
        list($headers, $response['data']) = explode("\r\n\r\n", $data, 2);
        $headers = array_slice(explode("\r\n", $headers), 1);

        // Convert headers into associative array.
        $headersInfo = array();
        foreach ($headers as $header) {
            $header = explode(':', $header);
            $headersInfo[strtolower(trim($header[0]))] = trim($header[1]);
        }
        $response['headers'] = $headersInfo;

        return $response;
    }

    protected function _validateResponse($response)
    {
        if ($response == false || !isset($response['code'])) {
            $this->addError(L::t('Unable to connect to the API servers'));
            return false;
        }

        $data = json_decode($response['data'], true);
        if (!$data) {
            $this->addError(L::t('No valid JSON content returned from Api Server').":".var_export($response,true));
            return false;
        }

        return $data;
    }

    protected function addError($text)
    {
        $this->_errorList[] = $text;
    }

    protected function addWarning($text)
    {
        $this->_warningList[] = $text;
    }

    public function getErrors()
    {
        return $this->_errorList;
    }

    public function getWarnings()
    {
        return $this->_warningList;
    }

    public function getErrorsAsHtml()
    {
        $errors = "";
        foreach (ApiModel::getInstance()->getErrors() as $message) {
            $errors .= $message . "<br/>";
        }
        return $errors;
    }

    public function getWarningsAsHtml()
    {
        $warnings = "";
        foreach (ApiModel::getInstance()->getWarnings() as $message) {
            $warnings .= $message . "<br/>";
        }
        return $warnings;
    }

}

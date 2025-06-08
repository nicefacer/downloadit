<?php
/**
 * Implements some generic methods that can be shared between various shopsystems for the
 * Http Model
 */
abstract class ML_Shop_Model_Http_Abstract {
    
    static protected $sImagePath = null;
    
    /**
     * Implodes the params in standard behavior  (name1=value1&name2=value2...)
     * concrete class can use this 
     * if concrete class use mod_rewrite, dont use me
     * @param type $aParams
     * @return type 
     */
    public function getUrl($aParams = array()){
        $sPrefix = MLSetting::gi()->get('sRequestPrefix');
        if ($sPrefix != '') {
            $aParams=array($sPrefix => $aParams);
        }
        return urldecode(http_build_query($aParams, '', '&'));
    }
    
    /**
     * Returns only request data that has been prefixed by our prefix (currently ml).
     *
     * If sRequestPrefix is setted (MLSetting::gi()->sRequestPrefix = 'ml') it just returns the defined array value
     * eg. 
     *    sRequestPrefix= 'ml'
     *    param $aArray = array('key' => 'value', 'session' => '535jkhk345jkh34', 'ml[module]' => 'tools', ml['tools'] => 'config' )
     *    => return array('module' => 'tools', 'tools' => 'config');
     * Otherwise full array
     * @return array
     */
    protected function filterRequest($aArray){
        $sPrefix = MLSetting::gi()->get('sRequestPrefix');
        if ($sPrefix != '') {
            $aArray = isset($aArray[$sPrefix]) ? $aArray[$sPrefix] : array();
        }
        return $aArray;
    }
    
    /**
     * Wraps a field name with the ml prefix.
     * @return string
     */
    public function parseFormFieldName($sString) {
        $sPrefix = MLSetting::gi()->get('sRequestPrefix');
        if ($sPrefix != '') {
            $iPos = strpos($sString, '[');
            if ($iPos !== false) {
                $sOut = $sPrefix.'['.substr($sString, 0, $iPos).']'.substr($sString, strpos($sString, '['));
            } else {
                $sOut = $sPrefix.'['.$sString.']';
            }
        } else {
            $sOut = $sString;
        }
        return $sOut;
    }
    
    /**
     * Redirects to url
     * @var string $mUrl complete url
     * @var array $mUrl use $this->getUrl($mUrl);
     * @var int $iStatus
     */
    public function redirect($mUrl, $iStatus = 302) {
        if (is_array($mUrl)) {
            $sUrl = $this->getUrl($mUrl);
        } else {
            $sUrl = $mUrl;
        }  
        if (function_exists('header_remove')) {
            header_remove(); //(PHP 5 >= 5.3.0)
        }
        header('Location: '.$sUrl, true, $iStatus);
        exit();
    }
    
    /**
     * Returns true if the current request is an ajax request.
     * @return bool
     */
    public function isAjax() {
        $aServer = $this->getServerRequest();
        if (
            MLRequest::gi()->data('ajax')
            || (isset($aServer['HTTP_X_REQUESTED_WITH']) && ($aServer['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'))
        ) {
            return true;
        }
    }
    
    /**
     * return directory or path (file system) of specific shop images
     * @param string $sFiles
     */
    public function getImagePath($sFile) {
        if(self::$sImagePath === null ){
            $sShopwareImagePath = $this->getShopImagePath();
            if(file_exists($sShopwareImagePath) && is_writable($sShopwareImagePath)){
                if(!file_exists($sShopwareImagePath.'magnalister/')){
                    mkdir($sShopwareImagePath.'magnalister/');
                }
                self::$sImagePath = $sShopwareImagePath.'magnalister/';
            }else{
                MLMessage::gi()->addError(MLI18n::gi()->get('sError_pathNotWriteable', array(
                    'path' => $sShopwareImagePath
                  )));
                throw new Exception('cannot create images');
            }
        }
        return self::$sImagePath.$sFile;
    }
    
    /**
   * return url of specific shop images
   * @param string $sFiles
   */
    public function getImageUrl($sFile){
        if(self::$sImagePath === null || self::$sImagePath === false ){
            throw new Exception('cannot create images ');
        }else{
            return $this->getShopImageUrl().'magnalister/'.$sFile;
        }
    }  
    
    /**
     * return current url
     * @param array $aParams
     * @return string
     */
    public function getCurrentUrl($aParams=array(),$aParameters=array('controller')){
        $aDefault=array();
        foreach($aParameters as $sKey){
            $sRequest=  MLRequest::gi()->data($sKey);
            if($sRequest!==null){
                $aDefault[$sKey]=$sRequest;
            }
        }
        $aParams = array_merge($aDefault,$aParams);
        return $this->getUrl($aParams);
    }
        
    
    /**
     * return directory or path (file system) of specific shop images
     * @param string $sFiles
     */
    abstract public function getShopImagePath() ;
    
    /**
     * return url of specific shop images
     * @param string $sFiles
     */
    abstract public function getShopImageUrl() ;
    
    /**
     * Gets the url to a file in the resources folder.
     * @param string $sFile
     *    Filename
     * @param bool $blAbsolute
     *
     * @return string
     */
    abstract public function getResourceUrl($sFile = '', $blAbsolute = true);
    
    /**
     * Gets the baseurl of the shopsystem.
     * @return string
     */
    abstract public function getBaseUrl();
        
    /**
     * Gets the magnalister cache FS url.
     * @return string
     */
    abstract public function getCacheUrl($sFile = '');
    
    /**
     * Gets the frontend url of the magnalister app.
     * @param array $aParams
     * @return string
     */
    abstract public function getFrontendDoUrl($aParams = array());
    
    /**
     * Returns _SERVER.
     * @return array
     */
    abstract public function getServerRequest();
    
    /**
     * Gets the request params merged from _POST and _GET.
     * @return array
     */
    abstract public function getRequest();
    
    /**
     * Parse hidden fields that are wanted by different shop systems for security measurements.
     * @return array
     *    Assoc of hidden neccessary form fields array(name => value, ...)
     */
    abstract public function getNeededFormFields();

}

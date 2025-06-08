<?php
/**
 * Prestashop specific implementation of the Http Model.
 */
class ML_Prestashop_Model_Http extends ML_Shop_Model_Http_Abstract {
    
    protected $sAdminPath = null;
    
    /**
     * Gets the baseurl of the shopsystem.
     * @return string
     */
    public function getBaseUrl() {        
        $oShopShop = new ShopCore(Shop::CONTEXT_SHOP);
        $oShopUrl = null;
        $aShopUrls = $oShopShop->getUrls();
        //find main url
        foreach ($aShopUrls as $aUrl){
            if($aUrl['main'] == 1){
                $oShopUrl = new ShopUrlCore($aUrl['id_shop_url']);
            }
        }

        //if there is no main url we use first url
        if($oShopUrl === null){
            $aUrl = current($aShopUrls);
            $oShopUrl = new ShopUrlCore($aUrl['id_shop_url']);
        }
        $useSSL = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($useSSL) ? 'https://' : 'http://';  
        $sFinalUrl =  $protocol_content . $oShopUrl->domain. $oShopUrl->physical_uri;
        return $sFinalUrl;
    }
    
	/**
     * return url that currently used to log in to prestashop backend
     * @return string
     */
    public function getBackendBaseUrl(){
        $useSSL = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($useSSL) ? 'https://' : 'http://';
        return $protocol_content . Tools::getHttpHost() . __PS_BASE_URI__;
    }
    /**
     * Gets the backend url of the magnalister app.
     * @param array $aParams
     *    name => value
     * @return string
     */
    public function getUrl($aParams = array()) {
        if($this->sAdminPath === null && defined('_PS_ADMIN_DIR_')){
            $admin_webpath = (defined('_PS_HOST_MODE_') && _PS_HOST_MODE_) ? 'backoffice': preg_replace('/^' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', '', str_ireplace(_PS_ROOT_DIR_, '', _PS_ADMIN_DIR_));
            $this->sAdminPath = $this->getBackendBaseUrl() . $admin_webpath . '/';
        }elseif(!defined('_PS_ADMIN_DIR_')){
            $this->sAdminPath = '';
        }
        $sParent = parent::getUrl($aParams);
        if(defined('_PS_HOST_MODE_') && _PS_HOST_MODE_) {
            $sParent = str_replace(array('[',']'), array('%5B','%5D'), $sParent);
        }
        return $this->sAdminPath . 'index.php?token=' . $this->getToken() . '&controller=AdminMagnalister' . ($sParent == '' ? '' : '&' . $sParent);
    }
    
    /**
     * Gets the prestashop security token for the url of the current page.
     * 
     * @global type $cookie
     * @return type string security url token
     */
    protected function getToken() {
        global $cookie;
        return md5(pSQL(_COOKIE_KEY_ . 'AdminMagnalister' . (int) Tab::getIdFromClassName('AdminMagnalister') . (int) $cookie->id_employee));
    }
    
    /**
     * Gets the request params merged from _POST and _GET.
     * @return array
     */
    public function getRequest() {
        $_mlGet = $_GET;
        $_mlPost = $_POST;
        if (get_magic_quotes_gpc()) {
            $_mlGet  = $this->stripslashes_deep($_mlGet);
            $_mlPost = $this->stripslashes_deep($_mlPost);
        }
        $aOut = MLHelper::getArrayInstance()->mergeDistinct($_mlGet, $_mlPost);
        return $this->filterRequest($aOut);
    }
    
    /**
     * Helper-Method to strip all slashes.
     * @return array
     */
    protected function stripslashes_deep($array, $topLevel = true) {
        $newArray = array();
        foreach ($array as $key => $value) {
            if (!$topLevel) {
                $newKey = stripslashes($key);
                if ($newKey !== $key) {
                    unset($array[$key]);
                }
                $key = $newKey;
            }
            $newArray[$key] = is_array($value) ? $this->stripslashes_deep($value, false) : stripslashes($value);
        }
        return $newArray;
    }
    
    /**
     * Returns _SERVER.
     * @return array
     */
    public function getServerRequest() {
        return $_SERVER;
    }
    
    /**
     * Parse hidden fields that are wanted by different shop systems for security measurements.
     * @return array
     *    Assoc of hidden neccessary form fields array(name => value, ...)
     */
    public function getNeededFormFields() {
        return array();
    }
    
    /**
     * Gets the frontend url of the magnalister app.
     * @param array $aParams
     * @return string
     */
    public function getFrontendDoUrl($aParams = array()) {
        $sParent = parent::getUrl($aParams);
		$oLink = new Link();
        return $oLink->getPageLink('do', (bool) (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()), null, 'fc=module&module=magnalister' . ($sParent == '' ? '' : '&' . $sParent),false,Shop::CONTEXT_SHOP);
    }
    
    /**
     * Gets the url to a file in the resources folder.
     * @return string
     */
    public function getCacheUrl($sFile = '') {
        return $this->getBackendBaseUrl() . 'modules/magnalister/writable/cache/' . $sFile;
    }
    
    /**
     * @bugs ** it works if default view store is enable OR if 'Add Store Code to Urls' is 'Yes' it works in all situation ** But if default view store is disable and if 'Add Store Code to Urls' is 'No' it doesn't work 
     * @param type $blAbsolute
     * @return type 
     */
    public function getResourceUrl($sFile = '', $blAbsolute = true) {
        $sExt = pathinfo($sFile, PATHINFO_EXTENSION);
        $aExt = explode('?', $sExt);//separate extention of file from url query that most of the time is current version of magnalister
        try{
            $aResource = empty($sFile) ? array('path' => '') : MLFilesystem::gi()->findResource('resource_' . $sFile);  
        }  catch (Exception $oExc){//if file was not found , try to find resource by its type
            try{
                $aResource = MLFilesystem::gi()->findResource('resource_'.$aExt[0].'_' . $sFile); 
            }  catch (Exception $oExc){
                return '';//no file is found
            }
        }
       
        $sUrl = ($blAbsolute ?  substr($this->getBackendBaseUrl(), 0, -1) :''). '/modules/magnalister/lib/' . substr($aResource['path'], strlen(MLFilesystem::getLibPath()));
        if(count($aExt)>1){//add url query part if exist 
            $sUrl .= '?'.$aExt[1];
        }
        $sUrl = str_replace('\\', '/', $sUrl);//replace backslashes in windows 
        return $sUrl;
    }
    
    /**
     * return directory or path (file system) of specific shop images
     * @param string $sFiles
     */
    public function getShopImagePath() {
        return _PS_ROOT_DIR_.'/img/';
    }

    /**
     * return url of specific shop images
     * @param string $sFiles
     */
    public function getShopImageUrl() {
        return $this->getBackendBaseUrl() . 'img/';
    }

}

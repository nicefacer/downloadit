<?php
/**
 * Magento specific implementation of the Http Model.
 */
class ML_ZzzzDummy_Model_Http extends ML_Shop_Model_Http_Abstract {
    /**
     * Gets the url to a file in the resources folder.
     *
     * @bugs 
     *   * it works if default view store is enable OR if 'Add Store Code to Urls' is 'Yes' it works in all situation
     *   * But if default view store is disable and if 'Add Store Code to Urls' is 'No' it doesn't work 
     * @param type $blAbsolute
     * @return string
     */
    public function getResourceUrl($sFile = '', $blAbsolute = true) {
        $sUrl = '';
        if ($blAbsolute) {
            $sUrl.= $this->getBaseUrl();;
        }
        $aResource = empty($sFile) ? array('path' => '') : MLFilesystem::gi()->findResource('resource_'.$sFile);
        $sUrl .= '/magnalister/Lib/'.substr($aResource['path'], strlen(MLFilesystem::getLibPath()));
        return str_replace('\\', '/', $sUrl);
    }
    
    /**
     * Gets the baseurl of the shopsystem.
     * @return string
     */
    public function getBaseUrl() {
        $aServer = $this->getServerRequest();
        return 'http'.(!isset($aServer['HTTPS']) ? '' : 's').'://'.$aServer['HTTP_HOST'].dirname($aServer['SCRIPT_NAME']).'/';
    }
    
    /**
     * Gets the magnalister cache FS url.
     * @return string
     */
    public function getCacheUrl($sFile = '') {
        $sBaseUrl = '';
        return $sBaseUrl . 'magnalister/writable/cache/' . $sFile;
    }
    
    /**
     * Gets the backend url of the magnalister app.
     * @param array $aParams
     *    name => value
     * @return string
     */
    public function getUrl($aParams = array()) {
        $sParent = parent::getUrl($aParams);
        return $this->getBaseUrl() . (($sParent == '') ? '' : '?' . $sParent);
    }
    
    /**
     * Gets the frontend url of the magnalister app.
     * @param array $aParams
     * @return string
     */
    public function getFrontendDoUrl($aParams = array()) {
        return $this->getUrl($aParams);
    }
    
    /**
     * Gets the request params merged from _POST and _GET.
     * @return array
     */
    public function getRequest() {
        $aOut = MLHelper::getArrayInstance()->mergeDistinct($_GET, $_POST);
        return $this->filterRequest($aOut);
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
     * return directory or path (file system) of specific shop images
     * @param string $sFiles
     */
    public function getShopImagePath() {
        return getcwd().'/_images/';
    }

    /**
     * return url of specific shop images
     * @param string $sFiles
     */
    public function getShopImageUrl() {
        return $this->getBaseUrl().'_images/';
    }

}

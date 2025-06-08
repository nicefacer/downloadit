<?php
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2015 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

/**
 * Magento specific implementation of the Http Model.
 */
class ML_Magento_Model_Http extends ML_Shop_Model_Http_Abstract {
    
    /**
     * cache magento url-parts to initialize magento-admin-store only once
     * @var array
     */
    protected $aHttpArray = null;
    
    /**
     * initilazes magento-admin-store and caches needed values for http-class
     * @param string $sType
     * @return mixed
     */
    protected function getMagentoHttpClassType($sType) {
        if ($this->aHttpArray === null) {
            $oShop = MLShop::gi()->initMagentoStore('0');
            $oAdminBlock = new Mage_Adminhtml_Block_Template();
            if (Mage::app()->getRequest()->getControllerName() == 'adminhtml_magnalister') { // @deprecated (1454502689-admin-routing)
                $sUrl = Mage::helper("adminhtml")->getUrl("magnalister/adminhtml_magnalister");
            } else {
                $sUrl = Mage::helper("adminhtml")->getUrl("adminhtml/magnalister");
            }
            $this->aHttpArray = array(
                'aNeededFormFields' => array('form_key' => $oAdminBlock->getFormKey()),
                'sShopImagePath' => Mage::getBaseDir('media').'/',
                'aServerRequest' => Mage::app()->getRequest()->getServer(),
                'aRequest' => MLHelper::getArrayInstance()->mergeDistinct(Mage::app()->getRequest()->getParams(), Mage::app()->getRequest()->getPost()),
                'sFrontendDoUrl' => $oShop->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL)),
                'sUrl' => $sUrl,
                'sBaseUrl' => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB),
                'sCacheUrl' => $oShop->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK),
                'sResourceUrl' => $oShop->getUrl('magnalister/magnalister/resource/'),
            );
            if (Mage::app()->getRequest()->getControllerName() == 'adminhtml_magnalister') { // @deprecated (1454502689-admin-routing)
                if (!$this->isAjax()) {
                    MLMessage::gi()->addInfo(MLI18n::gi()->get('Magento_CleanShopCacheMessage'));
                }
            }
        }
        return isset($this->aHttpArray[$sType]) ? $this->aHttpArray[$sType] : null;
    }
    
    /**
     * Gets the url to a file in the resources folder.
     *
     * @param type $blAbsolute
     * @return string
     */
    public function getResourceUrl($sFile = '', $blAbsolute = true) {
        if ($sFile == '') {
            return 
                $blAbsolute
                ? Mage::getDesign()->getSkinUrl(null, array('_area' => 'adminhtml', '_package' => 'default' ,'_theme' => 'default'))
                : ''
            ;
        }
        $aResource = MLFilesystem::gi()->findResource('resource_'.$sFile);
        $sRelLibPath = substr($aResource['path'], strlen(MLFilesystem::getLibPath().'Codepool'));
        $sResourceType = strtolower(preg_replace('/^.*\/resource\/(.*)\/.*$/Uis', '$1', $sRelLibPath));
        $sDstPath = (
                $sResourceType == 'js'
                ? Mage::getBaseDir().'/js'
                : Mage::getDesign()->getSkinBaseDir(array('_area' => 'adminhtml', '_package' => 'default' ,'_theme' => 'default'))       
        ).'/magnalister'.$sRelLibPath;
        if (!file_exists($sDstPath)) {// we copy complete resource-type-folder if one file not exists
            $sSubPath = preg_replace('/^(.*\/resource\/.*)\/.*$/Uis', '$1', $sRelLibPath);
            $sSrcPath = substr($aResource['path'], 0, stripos($aResource['path'], $sSubPath) + strlen($sSubPath) + 1);
            $sDstPath = substr($sDstPath, 0, stripos($sDstPath, $sSubPath) + strlen($sSubPath) + 1);
            try {
                MLHelper::getFilesystemInstance()->cp($sSrcPath, $sDstPath);
            } catch (Exception $oEx) {
                MLMessage::gi()->addDebug($oEx, array(
                    '$sSrcPath' => $sSrcPath, 
                    '$sDstPath' => $sDstPath, 
                    '$sSubPath' => $sSubPath
                ));
                MLMessage::gi()->addError(MLI18n::gi()->get('sMessageCannotLoadResource'));
                MLSetting::gi()->set('blInlineResource', true, true);
            }
        }
        if ($blAbsolute) {
            if ($sResourceType == 'js') {
                $sUrl = $this->getMagentoHttpClassType('sBaseUrl').'js/magnalister'.$sRelLibPath;
            } else {
                $sUrl = Mage::getDesign()->getSkinUrl('magnalister'.$sRelLibPath, array('_area' => 'adminhtml', '_package' => 'default' ,'_theme' => 'default'));
            }
        } else {
            $sUrl = 'magnalister'.$sRelLibPath;
        }
        return $sUrl;
    }
    
    /**
     * Gets the magnalister cache FS url.
     * @return string
     */
    public function getCacheUrl($sFile = '') {
        return $this->getMagentoHttpClassType('sCacheUrl').'magnalister/magnalister/writable/cache/' . $sFile;
    }
    
    /**
     * Gets the baseurl of the shopsystem.
     * @return string
     */
    public function getBaseUrl() {
        return $this->getMagentoHttpClassType('sBaseUrl');
    }
    
    /**
     * Gets the backend url of the magnalister app.
     * @param array $aParams
     *    name => value
     * @return string
     */
    public function getUrl($aParams = array()) {
        $sParent = parent::getUrl($aParams);
        return $this->getMagentoHttpClassType('sUrl') . (($sParent == '') ? '' : '?' . $sParent);
    }
    
    /**
     * Gets the frontend url of the magnalister app.
     * @param array $aParams
     * @return string
     */
    public function getFrontendDoUrl($aParams = array()) {
        $sParent  = parent::getUrl($aParams);
        return $this->getMagentoHttpClassType('sFrontendDoUrl') . 'magnalister/magnalister/do/' . ($sParent == '' ? '' : '?' . $sParent);
    }
    
    /**
     * Gets the request params merged from _POST and _GET.
     * @return array
     */
    public function getRequest() {
        $aOut = $this->getMagentoHttpClassType('aRequest');
        if (isset($aOut['key'])) { //magentospecific
            unset($aOut['key']);
        }
        return $this->filterRequest($aOut);
    }
    
    /**
     * Returns _SERVER.
     * @return array
     */
    public function getServerRequest() {
        return $this->getMagentoHttpClassType('aServerRequest');
    }
    
    /**
     * Parse hidden fields that are wanted by different shop systems for security measurements.
     * @return array
     *    Assoc of hidden neccessary form fields array(name => value, ...)
     */
    public function getNeededFormFields() {
        return $this->getMagentoHttpClassType('aNeededFormFields');
    }
    
    /**
     * return directory or path (file system) of specific shop images
     * @param string $sFiles
     */
    public function getShopImagePath() {
        return $this->getMagentoHttpClassType('sShopImagePath');
    }

    /**
     * return url of specific shop images
     * @param string $sFiles
     */
    public function getShopImageUrl() {
        return $this->getBaseUrl() . 'media/';
    }

}

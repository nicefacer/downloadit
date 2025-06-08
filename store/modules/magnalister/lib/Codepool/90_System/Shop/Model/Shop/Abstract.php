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
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
abstract class ML_Shop_Model_Shop_Abstract {
    
    /**
     * if get shopinfo request have exception it will redirect to configuration
     * @var bool 
     */
    protected $blRedirectInException = true;
    
    /**
     * can change default behavior if you don't want to redirect if there is any exception
     * @param bool $blRedirect
     * @return ML_Shop_Model_Shop_Abstract
     */
//    public function setRedirectInException($blRedirect){
//        $this->blRedirectInException = $blRedirect;
//        return $this;
//    }
    
    /**
     * get magnalisters shop id from current shop
     * @return int shop-id
     * @return null no shop found
     */
	public function getShopId() {
        try {
            $aInfo = $this->getShopInfo();
            return isset($aInfo['DATA']) && isset($aInfo['DATA']['ShopID']) && !empty($aInfo['DATA']['ShopID']) ? $aInfo['DATA']['ShopID'] : null;
        } catch (MagnaException $oEx) {
            return null;
        }
    }
    
    /**
     * get magnalisters customer id from current shop
     * @return int shop-id
     * @return null no customer found
     */
	public function getCustomerId() {
        try {
            $aInfo = $this->getShopInfo();
            return isset($aInfo['DATA']) && isset($aInfo['DATA']['CustomerID']) && !empty($aInfo['DATA']['CustomerID']) ? $aInfo['DATA']['CustomerID'] : null;
        } catch (MagnaException $oEx) {
            return null;
        }
    }
    
    /**
     * get marketplaces for shop as array(marketplaceid => marketplacename)
     * @return array
     */
    public function getMarketplaces () {
        try {
            $aInfo = $this->getShopInfo();
            $aMarketplaces = array();
            if (isset($aInfo['DATA']) && isset($aInfo['DATA']['Marketplaces'])) {
                foreach ($aInfo['DATA']['Marketplaces'] as $aMarketplace) {
                    $aMarketplaces[$aMarketplace['ID']] = $aMarketplace['Marketplace'];
                }
            }
            return $aMarketplaces;
        } catch (MagnaException $oEx) {
            return array();
        }
    }
    
    public function addonBooked($sAddonSku) {
        $sAddonSku = strtolower($sAddonSku);
        $aAddons = $this->getAddons();
        foreach ($aAddons as $aAddon) {
            if (strtolower($aAddon['SKU']) == $sAddonSku) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * get marketplaces for shop as array(marketplaceid => marketplacename)
     * @return array
     */
    public function getAddons () {
        try {
            $aInfo = $this->getShopInfo();
            return array_key_exists('DATA', $aInfo) && array_key_exists('Addons', $aInfo['DATA']) ? $aInfo['DATA']['Addons'] : array();
        } catch (MagnaException $oEx) {
            MLMessage::gi()->addDebug($oEx);
            return array();
        }
    }
    /**
     * loads shopinfo from magnalister-server
     * @return array
     */
    public function getShopInfo($blPurge = false) {
        if (!ML::isInstalled()) {
            return;
        }
        try {
            $aSession = MLSession::gi()->get('setting');
            $blSession = isset($aSession['sApiUrl']) ? true : false;
        } catch (Exception $oEx) {
            $blSession = false;
        }
        if (!$blSession && MLCache::gi()->exists(__CLASS__.'__sApiUrl.txt')) { // sApiUrl cache2setting
            MLSetting::gi()->set('sApiUrl', MLCache::gi()->get(__CLASS__.'__sApiUrl.txt'), true);
        }
        try {
            $aProps = MagnaConnector::gi()->getAddRequestsProps();
            MagnaConnector::gi()->setAddRequestsProps(array());
            $aShop = MagnaConnector::gi()->submitRequestCached(
                array(
                    'SUBSYSTEM' => 'Core',
                    'ACTION' => 'GetShopInfo',
                    'CALLBACKURL' => MLHttp::gi()->getFrontendDoUrl(array())
                ),
                30 * 60,
                $blPurge
            );
            MagnaConnector::gi()->setAddRequestsProps($aProps);
        } catch (MagnaException $oEx) {
            $aErrorArray = $oEx->getErrorArray();
            $aErrors = MLRequest::gi()->data('mp') != 'configuration' && isset($aErrorArray['ERRORS']) && is_array($aErrorArray['ERRORS']) ? $aErrorArray['ERRORS'] : array();
            foreach($aErrors as $aError) {
                if (
                        
                    isset($aError['ERRORLEVEL']) && $aError['ERRORLEVEL'] == 'FATAL'
                    && 
                    isset($aError['APIACTION']) && $aError['APIACTION'] == 'CheckAuthentification'
                    &&
                    'guide' != MLRequest::gi()->data('controller')
                    &&
                    !preg_match('/main_tools_*/', MLRequest::gi()->data('controller'))
                    &&
					!MLHttp::gi()->isAjax()
                ) {
                    MLHttp::gi()->redirect(array('controller' => 'configuration'));
                    exit();
                }
            }
            throw $oEx;
        }
        if (
            !$blSession
            && isset($aShop['DATA']) && isset($aShop['DATA']['APIUrl']) && !empty($aShop['DATA']['APIUrl'])
            && $aShop['DATA']['APIUrl'] != MLSetting::gi()->get('sApiUrl')
        ) {
            $sApiUrlBackup = MLSetting::gi()->get('sApiUrl');
            MLSetting::gi()->set('sApiUrl', $aShop['DATA']['APIUrl'], true);// set responsed api-url
            try {
                $aPing = MagnaConnector::gi()->submitRequest(array(
                    'SUBSYSTEM' => 'Core',
                    'ACTION' => 'Ping',
                ));
                if (!isset($aPing['STATUS']) || $aPing['STATUS'] !== 'SUCCESS') { //API dont work, rollback
                    MLSetting::gi()->set('sApiUrl', $sApiUrlBackup, true);
                } else { // set cache
                    MLCache::gi()->set(__CLASS__.'__sApiUrl.txt', MLSetting::gi()->get('sApiUrl'));
                }
            } catch (MagnaException $oEx) {
                MLSetting::gi()->set('sApiUrl', $sApiUrlBackup, true);
            }
        }
        return $aShop;
    }
    
    public function apiAccessAllowed () {
        try {
            $aShop = $this->getShopInfo();
            if (
                    isset($aShop['DATA'])
                    && isset($aShop['DATA']['IsAccessAllowed'])
            ) {
                if ($aShop['DATA']['IsAccessAllowed'] == 'no') {
                    MLMessage::gi()->addFatal(MLi18n::gi()->get('ML_ERROR_ACCESS_DENIED_TO_SERVICE_LAYER_TEXT'));
                    return false;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        } catch (Exception $oEx) {
            return false;
        }
    }

    
    public function needConvertToTargetCurrency(){
        return true;
    }

    /**
     * version of plugin in specific shop system
     */
    public function getPluginVersion(){
        return MLSetting::gi()->get('sClientVersion');
    }
    
    /**
     * Gets the name of the shop system.
     * @return string
     */
    abstract public function getShopSystemName();
    
    /**
     * Returns the database connection details.
     * @return array 
     *     Format: array('host' => string, 'user' => string, 'password' => string, 'database' => string, persistent => bool)
     */
    abstract public function getDbConnection();
    
    /**
     * initialize database like charset etc.
     * @return $this
     */
    abstract public function initializeDatabase();
    
    /**
     * Get a list of products with missing or double assigned SKUs.
     * @return array
     */
    abstract public function getProductsWithWrongSku();
    
    /**
     * Returns statistic information of orders.
     * @param string $sDateBack 
     *     Beginning date to get order info up to now.
     * @return array
     */
    abstract public function getOrderSatatistic($sDateBack);
    
    /**
     * Returns the current session id.
     * @return ?string
     */
    abstract public function getSessionId();
    
    /**
     * will be triggered after plugin update for shop-spec. stuff
     * eg. clean shop-cache
     * @param bool $blExternal if true external files (outside of plugin-folder) was updated
     * @return $this
     */
    abstract public function triggerAfterUpdate($blExternal);
    
}

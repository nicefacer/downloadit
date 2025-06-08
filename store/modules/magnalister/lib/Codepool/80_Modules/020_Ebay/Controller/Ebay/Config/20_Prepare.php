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
MLFilesystem::gi()->loadClass('Form_Controller_Widget_Form_ConfigAbstract');
class ML_Ebay_Controller_Ebay_Config_Prepare extends ML_Form_Controller_Widget_Form_ConfigAbstract {
    public static function getTabTitle () {
        return MLI18n::gi()->get('ebay_config_account_prepare');
    }
    
    public static function getTabActive() {
        return self::calcConfigTabActive(__class__, false);
    }
    public function renderAjax() {
        if ($this->getRequest('what') === 'topTenConfig') {
            $aGet = MLRequest::gi()->data();
            $sDs = DIRECTORY_SEPARATOR;
            require_once MLFilesystem::getOldLibPath('php' . $sDs . 'modules' . $sDs . 'ebay' . $sDs . 'classes' . $sDs . 'ebayTopTen.php');
            $oTopTen = new ebayTopTen();
            $oTopTen->setMarketPlaceId(MLModul::gi()->getMarketPlaceId());
            
            if ($this->getRequest('tab') === null) {
                echo $oTopTen->renderConfig();
            } elseif ($this->getRequest('tab') == 'init') {
                echo $oTopTen->renderConfigCopy((isset($aGet['executeTT'])) && ($aGet['executeTT'] == 'true'));
            } elseif ($this->getRequest('tab') == 'delete') {
                echo $oTopTen->renderConfigDelete(
                        isset($aGet['delete']) ? $aGet['delete'] : array()
                );
            }
            die();
        } else {
            parent::renderAjax();
        }
    }
    public function render() {
        if(!MLHttp::gi()->isAjax()){
            MLSetting::gi()->add('aCss','magnalister.ebayshippingservice.css', true);
        }
        parent::render();
    }
}

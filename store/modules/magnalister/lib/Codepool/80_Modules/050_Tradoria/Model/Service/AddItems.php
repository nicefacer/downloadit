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
class ML_Tradoria_Model_Service_AddItems extends ML_Modul_Model_Service_AddItems_Abstract {

    protected function getProductArray() {
        /* @var $oHelper ML_Tradoria_Helper_Model_Service_Product */
        $oHelper = MLHelper::gi('Model_Service_Product');
        $aMasterProducts = array();
        foreach ($this->oList->getList() as $oProduct) {
            /* @var $oProduct ML_Shop_Model_Product_Abstract */
            $oHelper->setProduct($oProduct);
            foreach ($this->oList->getVariants($oProduct) as $oVariant) {
                /* @var $oVariant ML_Shop_Model_Product_Abstract */
                if ($this->oList->isSelected($oVariant)) {
                    $oHelper->resetData();
                    $aMasterProducts[$oVariant->get('id')] = $oHelper->setVariant($oVariant)->getData();
                }
            }
        }


        return $aMasterProducts;
    }

    protected function uploadItems() {
        // Do nothing, API server doesn't support UploadItems. Items are directly uploaded to Rakuten when AddItems request is submitted
    }

    protected function addItems() {
        MagnaConnector::gi()->setTimeOutInSeconds(120);
        $mParent = parent::addItems();
        MagnaConnector::gi()->resetTimeOut();
        return $mParent;
    }    
    
    /**
     * for tradoria shop ,it is possible that we get error in MARKETPLACEERRORS 
     * @param array $aResponse
     */
    protected function additionalErrorManagement($aResponse) {        
        if (isset($aResponse["MARKETPLACEERRORS"]) && !empty($aResponse['MARKETPLACEERRORS'])) {
            foreach ($aResponse['MARKETPLACEERRORS'] as $aError) {
                MLMessage::gi()->addWarn($aError['ErrorMessage'], '', false);
                $aMessage = array();
                if(isset($aError['ErrorMessage'])){
                    $aMessage['ERRORMESSAGE'] = $aError['ErrorMessage'];
                }
                $aMessage['ERRORDATA'] = $aError;
                MLErrorLog::gi()->addApiError($aMessage);
            }
        }
    }
}

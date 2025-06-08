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
abstract class ML_Modul_Model_Service_SyncInventory_Abstract extends ML_Modul_Model_Service_Abstract {
    
    protected $aRequestTimeouts = array(
        'iUpdateItemsTimeout' => 5,
        'iUploadItemsTimeout' => 30,
        'iSyncInventoryTimeout' => 60,
    );
    
    protected $iSyncInventoryLimit = 100;
    
    public function __construct() {
        $oModul = MLModul::gi();
        if ($oModul->getConfig('currency') !== null && (boolean)$oModul->getConfig('exchangerate_update')) {
            MLCurrency::gi()->updateCurrencyRate($oModul->getConfig('currency'));
        }
        require_once MLFilesystem::getOldLibPath('php/lib/classes/SimplePrice.php');
        MLDatabase::getDbInstance()->logQueryTimes(false);
        MagnaConnector::gi()->setTimeOutInSeconds(600);
        @set_time_limit(60 * 10); // 10 minutes per module
        parent::__construct();
    }
    
    public function __destruct() {
        MagnaConnector::gi()->resetTimeOut();
        MLDatabase::getDbInstance()->logQueryTimes(true);
    }
    
    
    
    
    
    
    
    protected function syncIsEnabled () {
        $blStockSync = $this->stockSyncIsEnabled();
        $blPriceSync = $this->priceSyncIsEnabled();
        $blSync =  $blPriceSync || $blStockSync;
        $this->log(($blSync ? ('sync ['.($blStockSync ? 'stock, ' : '').($blPriceSync ? 'price' : '').']') : 'nosync' ), self::LOG_LEVEL_LOW);
        return $blSync;
    }
    
    protected function stockSyncIsEnabled () {
        $sStockSync = MLModul::gi()->getConfig('stocksync.tomarketplace');
        $blSync = ($sStockSync == 'auto') || ($sStockSync === 'auto_fast');
        return $blSync;
    }
    
    protected function priceSyncIsEnabled () {
        $blSync = MLModul::gi()->getConfig('inventorysync.price') == 'auto';
        return $blSync;
    }
        
    /**
     * @param ML_Shop_Model_Product_Abstract $oProduct
     * @param array $aResponse api-response of current product
     * @return array for request eg. array('price' => (float))
     */
    protected function getPrice (ML_Shop_Model_Product_Abstract $oProduct, $aResponse) {
        $aPrice = array();
        if (isset($aResponse['Price'])) {
            $aPrice['Price'] = $oProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject(), true);
        }
        return $aPrice;
    }
    
    /**
     * 
     * @param ML_Shop_Model_Product_Abstract $oProduct
     * @param array $aResponse api-response of current product
     * return array() for request eg. array('Quantity' => (int))
     */
    protected function getStock (ML_Shop_Model_Product_Abstract $oProduct, $aResponse) {
        return array(
            'Quantity' => $oProduct->getSuggestedMarketplaceStock(
                MLModul::gi()->getConfig('quantity.type'), 
                MLModul::gi()->getConfig('quantity.value'), 
                MLModul::gi()->getConfig('maxquantity')//only ebay (... till now)
            )
        );
    }
    
    protected function getSyncInventoryRequest () {
        $aRequest = array(
            'ACTION' => 'GetInventory',
            'MODE' => 'SyncInventory',
            'OFFSET' => (ctype_digit(MLRequest::gi()->data('offset'))) ? (int) MLRequest::gi()->data('offset') : 0,
            'LIMIT' => ((int) MLRequest::gi()->data('maxitems') > 0) ? (int)MLRequest::gi()->data('maxitems') : $this->iSyncInventoryLimit,
        );
        if ((int)MLRequest::gi()->data('steps') > 0) {
            $aRequest['steps'] = (int)MLRequest::gi()->data('steps');
        }
        if (MLRequest::gi()->data('SEARCH') !== null) {
            $aRequest['SEARCH'] = (int)MLRequest::gi()->data('SEARCH');
        }
        return $aRequest;
    }
    
    protected function getItemRequestData($oProduct, $aItem) {
        $aUpdateRequest = array();
        if ($this->priceSyncIsEnabled()) {
            foreach ($this->getPrice($oProduct, $aItem) as $sPriceType => $fPriceValue) {
                $fPriceValue = number_format($fPriceValue, 4, '.', '');
                if (isset($aItem[$sPriceType]) && $aItem[$sPriceType] != $fPriceValue) {
                    $aUpdateRequest[$sPriceType] = $fPriceValue;
                }
            }
        }
        if ($this->stockSyncIsEnabled()) {
            foreach ($this->getStock($oProduct, $aItem) as $sStockType => $iStockValue) {
                if (isset($aItem[$sStockType]) && $aItem[$sStockType] != $iStockValue) {
                    $aUpdateRequest[$sStockType] = $iStockValue;
                }
            }
        }
        return $aUpdateRequest;
    }


    public function execute() {
        if ($this->syncIsEnabled()) {
            $aRequest = $this->getSyncInventoryRequest();
            $this->log('FetchInventory', self::LOG_LEVEL_LOW);
            try {
                do {
                    MagnaConnector::gi()->setTimeOutInSeconds($this->aRequestTimeouts['iSyncInventoryTimeout']);
                    $aResponse = MagnaConnector::gi()->submitRequest($aRequest);
                    $this->log(
                        'Received ' . count($aResponse['DATA']) . ' items ' .
                        '(' . ($aRequest['OFFSET'] + count($aResponse['DATA'])) . ' of ' . $aResponse['NUMBEROFLISTINGS'] . ') ' .
                        'in ' . microtime2human($aResponse['Client']['Time']),
                        self::LOG_LEVEL_LOW
                    );
                    $aResponse['DATA'] = empty($aResponse['DATA']) ? array() : $aResponse['DATA'];
                    $aUpdateRequest = array();
                    foreach ($aResponse['DATA'] as $iItem => $aItem) {
                        $this->log('currentItem: '.json_encode($aItem), self::LOG_LEVEL_HIGH);
                        try {
                            $oProduct = MLProduct::factory()->getByMarketplaceSKU($aItem['SKU']);
                            if ($oProduct->exists()) {
                                $this->log(
                                    'SKU: ' . $aItem['SKU'] . ' (' . $aItem['Title'] . ') found (' .
                                    'MP-SKU: ' . $oProduct->get('MarketplaceIdentSku') . '; '.
                                    'MP-ID: ' . $oProduct->get('MarketplaceIdentId') .'; '.
                                    'Shop-SKU: '.$oProduct->get('ProductsSku').'; '.
                                    'Shop-ID: '.$oProduct->get('ProductsId').
                                    ')',
                                    $iItem % 10 === 0 ? self::LOG_LEVEL_NONE : self::LOG_LEVEL_MEDIUM //log every 10th item to have continues output
                                );
                                @set_time_limit(180);
                                $aCurrentUpdateRequest = $this->getItemRequestData($oProduct, $aItem);
                                if (!empty($aCurrentUpdateRequest)) {
                                    $aUpdateRequest[$iItem] = $aCurrentUpdateRequest;
                                } 
                                if (isset($aUpdateRequest[$iItem])) {
                                    $this->log(
                                        'SKU: ' . $aItem['SKU'] . ' (' . $aItem['Title'] . ') new data ('. json_encode($aUpdateRequest[$iItem]).')', 
                                        self::LOG_LEVEL_MEDIUM
                                    );
                                    $aUpdateRequest[$iItem]['SKU'] = $aItem['SKU'];
                                }
                            } else {
                                $this->log('SKU: ' . $aItem['SKU'] . ' (' . $aItem['Title'] . ') not found');
                            }
                        } catch (Exception $oEx) {
                            $this->log('SKU: ' . $aItem['SKU'] . ' (' . $aItem['Title'] . ') throws Exception ('.$oEx->getMessage().')', self::LOG_LEVEL_LOW);
                        }
                    }
                    if (empty($aUpdateRequest)) {
                        $blNext = true;
                        $this->log('Nothing to update in this batch.', self::LOG_LEVEL_LOW);
                    } else {
                        $this->log('do UpdateRequest', self::LOG_LEVEL_LOW);
                        $this->log('UpdateRequest : '.  json_encode($aUpdateRequest), self::LOG_LEVEL_HIGH);
                        MagnaConnector::gi()->setTimeOutInSeconds($this->aRequestTimeouts['iUpdateItemsTimeout']);
                        try {
                            $this->log(
                                'UpdateResponse : '. json_encode(
                                    MagnaConnector::gi()->submitRequest(array (
                                        'ACTION' => 'UpdateItems',
                                        'DATA' => $aUpdateRequest
                                    ))
                                ), 
                                self::LOG_LEVEL_HIGH
                            );
                            $blNext = true;
                        } catch (Exception $oEx) {                    
                            $blNext = false;
                            $this->log($oEx->getMessage(), self::LOG_LEVEL_MEDIUM);
                            if ($oEx->getCode() == MagnaException::TIMEOUT) {
                                $oEx->setCriticalStatus(false);
                                $blNext = true;
                            }
                        }
                    }
                    if ($blNext) {
                        $aRequest['OFFSET'] += $aRequest['LIMIT'];
                        if (isset($aRequest['steps'])) {
                            $aRequest['steps']--;
                        }
                    }
                    if ($aRequest['OFFSET'] < $aResponse['NUMBEROFLISTINGS']) {
                        $this->out(array(
                            'Done' => (int) $aRequest['OFFSET'],
                            'Step' => isset($aRequest['steps']) ? $aRequest['steps'] : false,
                            'Total' => $aResponse['NUMBEROFLISTINGS'],
                        ));
                    } else {
                        $blNext = false;
                    }
                    if (isset($aRequest['steps']) && $aRequest['steps'] <=1 ) {
                        $blNext = false;
                    }
                } while ($blNext);       
            } catch (MagnaExeption $oEx) {
                $this->log($oEx->getMessage(), self::LOG_LEVEL_MEDIUM);
            }  
        }
        if (!isset($aRequest['steps']) || $aRequest['steps'] <= 1) {
            $this->uploadItems();
            $this->out(array(
                'Complete' => 'true',
            ));
        }
        return $this;
    }
    
    protected function uploadItems() {
        $this->log('upload items', self::LOG_LEVEL_LOW);
        MagnaConnector::gi()->setTimeOutInSeconds($this->aRequestTimeouts['iUploadItemsTimeout']);
        try {
            $this->log(
                'UpdateResponse : '. json_encode(
                    MagnaConnector::gi()->submitRequest(array (
                        'ACTION' => 'UploadItems'
                    ))
                ), 
                self::LOG_LEVEL_HIGH
            );
        } catch (MagnaException $oEx) {
            $this->log($oEx->getMessage(), self::LOG_LEVEL_MEDIUM);
            if ($oEx->getCode() == MagnaException::TIMEOUT) {
                $oEx->setCriticalStatus(false);
            }
        }
        return $this;
    }
    
    protected function out($mValue) {
        if(!MLHttp::gi()->isAjax()){
            echo is_array($mValue) ? "\n{#".base64_encode(json_encode(array_merge(array('Marketplace' => MLModul::gi()->getMarketPlaceName(), 'MPID' => MLModul::gi()->getMarketPlaceId(),), $mValue)))."#}\n\n": $mValue."\n";
            flush();
        }else{//in ajax call in pluin we break maxitems and steps of each request ,so we don't have lang request ,so we don't need echo any output
//            MLLog::gi()->add('SyncInventory_'.MLModul::gi()->getMarketPlaceId(), $mValue);
        }
        return $this;
    }
    
}
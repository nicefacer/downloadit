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
MLFilesystem::gi()->loadClass('Productlist_Controller_Widget_ProductList_Selection');

class ML_Cdiscount_Controller_Cdiscount_Checkin_Summary extends ML_Productlist_Controller_Widget_ProductList_Selection {

    protected $aParameters = array('controller');

    public function getPriceObject(ML_Shop_Model_Product_Abstract $oProduct) {
        return MLModul::gi()->getPriceObject();
    }

    public function getPrepareData(ML_Shop_Model_Product_Abstract $oProduct) {
        if (!isset($this->aPrepare[$oProduct->get('id')])) {
            $this->aPrepare[$oProduct->get('id')] = MLDatabase::factory('cdiscount_prepare')->set('products_id', $oProduct->get('id'));
        }

        return $this->aPrepare[$oProduct->get('id')];
    }

    public function getPrice(ML_Shop_Model_Product_Abstract $oProduct) {
        return $oProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject(), true, false);
    }

    public function getStock(ML_Shop_Model_Product_Abstract $oProduct) {
        $aStockConf = MLModul::gi()->getStockConfig();
        return $oProduct->getSuggestedMarketplaceStock($aStockConf['type'], $aStockConf['value']);
    }

    protected function callAjaxCheckinAdd() {
        return $this->addItems(false);
    }

    protected function callAjaxCheckinPurge() {
        return $this->addItems(true);
    }

    protected function addItems($blPurge) {
        $oList = $this->getProductList();
        
        $iOffset = $this->getRequest('offset');
        $iOffset = ($iOffset === null) ? 0 : $iOffset;
        $iLimit = 1; //min from list
        $oList->setLimit(0, $iLimit); //offset is 0, because uploaded products will be deleted from selections
        $aStatistic = $oList->getStatistic();
        $iTotal = (int) $aStatistic['iCountTotal'];
        $blPurge = ($blPurge && $iOffset == 0);
        $oService = MLService::getAddItemsInstance();
        
        // In case slection list is empty, send back success response.
        if ($oList->getList()->count() === 0) {
            MLSetting::gi()->add(
                'aAjax', array(
                    'success' => true,
                    'error' => '',
                    'offset' => 0,
                    'info' => array(
                        'total' => 0,
                        'current' => 0,
                        'purge' => false,
                    ),
                )
            );
            
            return $this;
        }
        
        try {
            $oService->setProductList($oList)->setPurge($blPurge)->execute();
            $blSuccess = true;
        } catch (Exception $oEx) {
            $blSuccess = false;
        }

        if ($this->getRequest('saveSelection') != 'true') {
            MLSetting::gi()->add(
                'aAjax', array(
                    'success' => $blSuccess,
                    'error' => $oService->haveError(),
                    'offset' => $iOffset + count($oList->getMasterIds(true)),
                    'info' => array(
                        'total' => $iTotal + $iOffset,
                        'current' => $iOffset + count($oList->getMasterIds(true)),
                        'purge' => $blPurge,
                    ),
                )
            );
            $oSelection = MLDatabase::factory('selection');
            foreach ($oList->getList() as $oProduct) {
                foreach ($oList->getVariants($oProduct) as $oChild) {
                    $oSelection->init()->loadByProduct($oChild, 'checkin')->delete();
                }
            }
        } else {
            MLSetting::gi()->add(
                'aAjax', array(
                    'success' => false,
                    'error' => $oService->haveError(),
                    'offset' => $iOffset,
                    'info' => array(
                        'total' => $iTotal + $iOffset,
                        'current' => $iOffset,
                        'purge' => $blPurge,
                    ),
                )
            );
        }

        return $this;
    }

    public function getProductListWidget() {
        parent::getProductListWidget();
        $aStatistic = $this->getProductList()->getStatistic();
        if ($aStatistic['iCountTotal'] == 0) {
            MLHttp::gi()->redirect($this->getParentUrl());
        }
    }
}

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
MLFilesystem::gi()->loadClass('Shopware_Model_ProductList_Abstract');

class ML_ShopwareBepado_Model_ProductList_Bepado_Checkin extends ML_Shopware_Model_ProductList_Abstract {

    public function getSelectionName() {
        return 'checkin';
    }

//    protected function initList() {
//        $this->oList = MLHelper::gi('model_productlist_bepado_list');
//    }

    protected function executeFilter(){
         $this->oFilter
            ->registerDependency('searchfilter')
            ->limit()
            ->registerDependency('categoryfilter')
            ->registerDependency('preparestatusfilter', array('blPrepareMode' => false))
            ->registerDependency('lastpreparedfilter')
            ->registerDependency('productstatusfilter', array('blPrepareMode' => false))
            ->registerDependency('manufacturerfilter')
        ;
        return $this;
    }
    protected function executeList(){
        $this->oList
            ->image()
            ->product()         
            ->priceShop()
            ->priceMarketplace()
//            ->categoryMarketplace()
//            ->ShopwareAttribute('description')//example for additional fields
//            ->ShopwareAttribute('short_description')//example for additional fields
            ->preparedType()
        ;
    }
}

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

MLFilesystem::gi()->loadClass('Prestashop_Model_ProductList_Abstract');
class ML_Prestashop_Model_ProductList_Ricardo_Checkin extends ML_Prestashop_Model_ProductList_Abstract {
    
    protected function executeFilter(){
        $this->oFilter
            ->registerDependency('searchfilter')
            ->limit()
            ->registerDependency('categoryfilter')
            ->registerDependency('preparestatusfilter', array('blPrepareMode' => false))
            ->registerDependency('lastpreparedfilter')
            ->registerDependency('productstatusfilter', array('blPrepareMode' => false))
            ->registerDependency('manufacturerfilter')
            ->registerDependency('prestashopproducttypefilter')
        ;
        return $this;
    }
    protected function executeList(){
        $this->oList
                 ->image()
                 ->product() ;
         $sMfValue = MLDatabase::factory('config')->set('mpid' , 0)->set('mkey' , 'general.manufacturerpartnumber')->get('value') ;
         if ( !empty($sMfValue) ) {
             $this->oList->manufacturer() ;
         }
         $sEanValue = MLDatabase::factory('config')->set('mpid' , 0)->set('mkey' , 'general.ean')->get('value') ;
         if ( !empty($sEanValue) ) {
             $this->oList->ean13() ;
         }
         $this->oList                 
            ->priceShop()
//            ->priceMarketplace()
//            ->preparedType()
                 ;
    }
    public function getSelectionName() {
        return 'checkin';
    }
    
}
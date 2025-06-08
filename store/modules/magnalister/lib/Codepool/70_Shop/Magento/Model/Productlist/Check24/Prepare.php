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

MLFilesystem::gi()->loadClass('Magento_Model_ProductList_Abstract');
class ML_Magento_Model_ProductList_Check24_Prepare extends ML_Magento_Model_ProductList_Abstract{
    protected function executeFilter(){
        $this->oFilter
            ->registerDependency('magentonovariantsfilter')
            ->registerDependency('searchfilter')
            ->limit()
            ->registerDependency('categoryfilter')
            ->registerDependency('preparestatusfilter')
            ->registerDependency('magentoattributesetfilter')
            ->registerDependency('productstatusfilter')
            ->registerDependency('manufacturerfilter')
            ->registerDependency('magentoproducttypefilter')
            ->registerDependency('magentosaleablefilter')
        ;
        return $this;
    }
    protected function executeList(){
        $this->oList
            ->image()
            ->product()
        ;
        $sValue = MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.manufacturerpartnumber')->get('value');
        if(!empty($sValue)){
            $this->oList->magentoAttribute($sValue,true,MLI18n::gi()->get('Productlist_Header_Field_sManufacturerpartnumber'));
        }
        $sValue = MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.ean')->get('value');
        if(!empty($sValue)){
            $this->oList->magentoAttribute($sValue,true,MLI18n::gi()->get('Productlist_Header_Field_sEan'));
        }
        $this->oList
            ->priceShop()
            ->priceMarketplace()
//            ->magentoAttribute('description')//example for additional fields
//            ->magentoAttribute('short_description')//example for additional fields
            ->preparedStatus()
        ;
    }
    public function getSelectionName() {
        return 'match';
    }
}
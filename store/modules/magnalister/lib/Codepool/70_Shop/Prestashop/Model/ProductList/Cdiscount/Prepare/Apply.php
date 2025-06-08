<?php

MLFilesystem::gi()->loadClass('Prestashop_Model_ProductList_Abstract');

/**
 * select all products 
 * cdiscount-config:
 *  - cdiscount.lang isset
 *  - amzon.prepare.ean !=''
 */
class ML_Prestashop_Model_ProductList_Cdiscount_Prepare_Apply extends ML_Prestashop_Model_ProductList_Abstract {

    protected function executeFilter() {
        $this->oFilter
            ->registerDependency('searchfilter')
            ->limit()
            ->registerDependency('categoryfilter')
            ->registerDependency('preparestatusfilter')
            ->registerDependency('cdiscountpreparetypefilter',array('PrepareType'=>'apply'))
            ->registerDependency('productstatusfilter')
            ->registerDependency('manufacturerfilter')
        ;
        return $this;
    }

    protected function executeList() {
        $this->oList
            ->image()
            ->product()
            ->manufacturer()                 
            ->priceShop()
//            ->priceMarketplace()
            ->preparedStatus()
        ;
        
    }

    public function getSelectionName() {
        return 'apply';
    }

}

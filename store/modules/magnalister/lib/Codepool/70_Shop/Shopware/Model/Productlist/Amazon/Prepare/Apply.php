<?php

MLFilesystem::gi()->loadClass('Shopware_Model_ProductList_Abstract');

/**
 * select all products 
 * amazon-config: 
 *  - amazon.lang isset
 *  - amzon.prepare.ean !=''
 */
class ML_Shopware_Model_ProductList_Amazon_Prepare_Apply extends ML_Shopware_Model_ProductList_Abstract {

    protected function executeFilter() {
        $this->oFilter
            ->registerDependency('searchfilter')
            ->limit()
            ->registerDependency('categoryfilter')
            ->registerDependency('preparestatusfilter')
            ->registerDependency('amazonpreparetypefilter',array('PrepareType'=>'apply'))
            ->registerDependency('productstatusfilter')
            ->registerDependency('manufacturerfilter')
        ;
        return $this;
    }

    protected function executeList() {
        $this->oList
            ->image()
            ->product()
        ;
        $sValue = MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.manufacturerpartnumber')->get('value');
        if(!empty($sValue)){
            $this->oList->ShopwareAttribute($sValue);
        }
        $sValue = MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.ean')->get('value');
        if(!empty($sValue)){
            $this->oList->ShopwareAttribute($sValue);
        }
        $this->oList->ShopwareAttribute('categories')            
            ->priceShop()
            ->priceMarketplace()
            ->preparedStatus()
        ;
    }

    public function getSelectionName() {
        return 'apply';
    }

}

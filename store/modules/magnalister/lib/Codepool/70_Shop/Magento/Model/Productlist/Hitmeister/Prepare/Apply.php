<?php

MLFilesystem::gi()->loadClass('Magento_Model_ProductList_Abstract');

/**
 * select all products 
 * hitmeister-config: 
 *  - hitmeister.lang isset
 *  - amzon.prepare.ean !=''
 */
class ML_Magento_Model_ProductList_Hitmeister_Prepare_Apply extends ML_Magento_Model_ProductList_Abstract {

    protected function executeFilter() {
        $this->oFilter
            ->registerDependency('magentonovariantsfilter')
            ->registerDependency('searchfilter')
            ->limit()
            ->registerDependency('categoryfilter')
            ->registerDependency('preparestatusfilter')
            ->registerDependency('magentoattributesetfilter')    
            ->registerDependency('hitmeisterpreparetypefilter',array('PrepareType'=>'apply'))
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
//            ->priceMarketplace()
//            ->magentoAttribute('description')//example for additional fields
//            ->magentoAttribute('short_description')//example for additional fields
            ->preparedStatus()
        ;
    }

    public function getSelectionName() {
        return 'apply';
    }

}

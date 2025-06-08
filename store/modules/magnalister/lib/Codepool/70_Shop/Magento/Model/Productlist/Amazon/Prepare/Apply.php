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
/**
 * select all products 
 * amazon-config: 
 *  - amazon.lang isset
 *  - amzon.prepare.ean !=''
 */
class ML_Magento_Model_ProductList_Amazon_Prepare_Apply extends ML_Magento_Model_ProductList_Abstract {
    protected function executeFilter(){
        $this->oFilter
            ->registerDependency('magentonovariantsfilter')
            ->registerDependency('searchfilter')
            ->limit()
            ->registerDependency('categoryfilter')
            ->registerDependency('preparestatusfilter')
            ->registerDependency('amazonpreparetypefilter', array('PrepareType'=>'apply'))
            ->registerDependency('magentoattributesetfilter')
            ->registerDependency('productstatusfilter')
            ->registerDependency('manufacturerfilter')
            ->registerDependency('magentoproducttypefilter')
            ->registerDependency('magentosaleablefilter')
        ;
//        $this->oCollection->addAttributeToFilter(
//            MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.ean')->get('value'),array('neq'=>'')//not equals
//        );
        // get all products with custom options
        $aExcludeIds=array();
        foreach(MLDatabase::getDbInstance()->fetchArray("select distinct product_id from ".Mage::getSingleton('core/resource')->getTableName('catalog_product_option')." where is_require=1")as $aRow){
            $aExcludeIds[]=$aRow['product_id'];
        }
        if(!empty($aExcludeIds)){
            $this->oCollection->getSelectSql()->where("e.entity_id not in('".implode("', '",array_unique($aExcludeIds))."')");
        }
        return $this;
    }
    protected function executeList(){
        $this->oList
            ->image()
            ->product()
        ;
        $sValue = MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.manufacturerpartnumber')->get('value');
        if(!empty($sValue)){
            $this->oList->magentoAttribute($sValue);
        }
        $sValue = MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.ean')->get('value');
//        $sValue=MLModul::gi()->getConfig('prepare.ean');
        if(!empty($sValue)){
            $this->oList->magentoAttribute($sValue);
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
        return 'apply';
    }
}
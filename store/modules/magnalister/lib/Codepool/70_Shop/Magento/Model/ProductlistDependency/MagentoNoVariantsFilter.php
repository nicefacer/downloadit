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
class ML_Magento_Model_ProductListDependency_MagentoNoVariantsFilter extends ML_Productlist_Model_ProductListDependency_Abstract {
    
    /**
     * filters all products which are variants of a other product
     * @param Mage_Catalog_Model_Resource_Product_Collection $mQuery
     * @return void
     */
    public function manipulateQuery($mQuery) {
        $aFilterIds = array();
        // filter products with multiple options (only allow dropdown or radio)
        $oOptions = Mage::getModel('catalog/product_option')->getCollection();
        /* @var $oOptions Mage_Catalog_Model_Resource_Product_Option_Collection */
        $oOptions->getSelect()->where("type not in('drop_down', 'radio')");
        foreach ($oOptions as $oOption) {
            $aFilterIds[] = $oOption->getProductId();
        }
        // filter products who are in catalog_super_link (are parts of configurable)
        foreach (MLDatabase::getDbInstance()->fetchArray("select distinct product_id from ".Mage::getSingleton('core/resource')->getTableName('catalog_product_super_link')) as $aRow) {
            $aFilterIds[] = $aRow['product_id'];
        }
        if (!empty($aFilterIds)) {
            $mQuery->getSelectSql()->where("e.entity_id not in('".implode("', '", array_unique($aFilterIds))."')");
        }
    }

}

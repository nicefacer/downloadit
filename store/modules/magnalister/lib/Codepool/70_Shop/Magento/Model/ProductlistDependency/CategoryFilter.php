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
MLFilesystem::gi()->loadClass('Shop_Model_ProductListDependency_CategoryFilter_Abstract');
class ML_Magento_Model_ProductListDependency_CategoryFilter extends ML_Shop_Model_ProductListDependency_CategoryFilter_Abstract {
    
    /**
     * key=>value for filtering (eg. validation and form-select)
     * @var array|null
     */
    protected $aFilterValues = null;
    
    /**
     * all categories
     * @var array|null
     */
    protected $aCategories = null;


    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $mQuery
     * @return void
     */
    public function manipulateQuery($mQuery) {
        $sFilterValue = $this->getFilterValue();
        if (
            !empty($sFilterValue) 
            && $sFilterValue !== 1 //root-category
            && array_key_exists($sFilterValue, $this->getFilterValues())
        ) {
            $aCats = array();
            foreach ($this->getMagentoCategories() as $aCat) {
                if (preg_match('/\/'.$sFilterValue.'\//', '/'.$aCat['path'].'/')) {
                    $aCats[] = $aCat['entity_id'];
                }
            } 
            if (empty($aCats)) {
                $mQuery->getSelectSql()->where('false');
            } else {
                $mQuery
                    ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'inner')
                    ->addAttributeToFilter('category_id', $aCats, 'inner')
                ;
            }
        }
    }
    
    protected function getMagentoCategories () {
        if ($this->aCategories === null) {
            $this->aCategories = Mage::getModel('catalog/category')->getTreeModelInstance()->load()->getCollection()->exportToArray();
        }
        return $this->aCategories;
    }

    /**
     * key=>value for categories
     * @return array
     */
    protected function getFilterValues() {
        if ($this->aFilterValues === null) {
            $iStoreId = MLModul::gi()->getConfig('lang');
            try {
                $iRootCat = Mage::app()->getStore($iStoreId)->getRootCategoryId();
            } catch (Mage_Core_Model_Store_Exception $oEx) {//store not exists
                $iRootCat = Mage::app()->getStore()->getRootCategoryId();
            }
            $aCats = $this->getMagentoCategories();
            $aSort = array();
            foreach ($aCats as $aCurrent) {
                if ($iRootCat == $aCurrent['entity_id']) {
                    $sFilterKey = '_' . $aCurrent['path'];
                }
                $aSort['_' . $aCurrent['path']] = array(
                    'value' => $aCurrent['entity_id'],
                    'label' => str_repeat('&nbsp;&nbsp;', substr_count($aCurrent['path'], '/')) . ($aCurrent['entity_id'] == 1 ? sprintf(MLI18n::gi()->get('Productlist_Filter_sEmpty'), MLI18n::gi()->get('Productlist_Filter_sCategory')) : $aCurrent['name']),
                );
            }
            ksort($aSort);
            $aFilterValues = array();
            foreach ($aSort as $sKey => $aValue) {
                if (
                        substr($sKey, 0, strlen($sFilterKey)) == $sFilterKey ||
                        $sKey == '_1'//root
                ) {
                    $aFilterValues[$aValue['value']] = $aValue;
                }
            }
            $this->aFilterValues = $aFilterValues;
        }
        return $this->aFilterValues;
    }

}

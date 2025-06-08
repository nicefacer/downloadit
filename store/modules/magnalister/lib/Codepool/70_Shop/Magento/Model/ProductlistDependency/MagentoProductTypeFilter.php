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
MLFilesystem::gi()->loadClass('ProductList_Model_ProductListDependency_SelectFilter_Abstract');
class ML_Magento_Model_ProductListDependency_MagentoProductTypeFilter extends ML_ProductList_Model_ProductListDependency_SelectFilter_Abstract {
    
    protected $aFilterValues = null;
    
    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $mQuery
     * @return void
     */
    public function manipulateQuery($mQuery) {
        $sFilterValue = $this->getFilterValue();
        if (
           !empty($sFilterValue)
           && array_key_exists($sFilterValue, $this->getFilterValues())
        ) {
            $mQuery->addAttributeToFilter('type_id', $sFilterValue);
        } else {
            $mQuery->addAttributeToFilter('type_id', array_keys($this->getFilterValues()));// we support only this types
        }
    }
    
    /**
     * key=>value for producttype
     * @return array
     */
    protected function getFilterValues() {
        if ($this->aFilterValues === null) {
            $aOut = array(
                '' => array(
                    'value' => '',
                    'label' => sprintf(MLI18n::gi()->get('Productlist_Filter_sEmpty'), MLI18n::gi()->get('Magento_Productlist_Filter_sProductType')),
                )
            );
            foreach(Mage::getModel('catalog/product_type')->getTypes() as $sKey => $aType){
                if (in_array($sKey, array('simple', 'configurable', 'virtual'))) {
                    $aOut[$sKey] = array(
                        'value' => $sKey,
                        'label' => $aType['label'],
                    );
                }
            }
            $this->aFilterValues = $aOut;
        }
        return $this->aFilterValues;
    }

}

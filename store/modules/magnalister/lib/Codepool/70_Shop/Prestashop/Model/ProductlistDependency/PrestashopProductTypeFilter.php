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
class ML_Prestashop_Model_ProductListDependency_PrestashopProductTypeFilter extends ML_ProductList_Model_ProductListDependency_SelectFilter_Abstract {
    
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
            $mQuery->where(" p.`$sFilterValue` = 1");
        }
    }
    
    /**
     * key=>value for producttype
     * @return array
     */
    protected function getFilterValues() {
        if ($this->aFilterValues === null) {
            $this->aFilterValues = array (
                '' => array (
                    'value' => '' ,
                    'label' => sprintf(MLI18n::gi()->get('Productlist_Filter_sEmpty') , MLI18n::gi()->get('Prestashop_Productlist_Filter_sProductType')) ,
                ),
                'is_virtual' => array (
                    'value' => 'is_virtual' ,
                    'label' => MLI18n::gi()->get('Prestashop_Productlist_Header_sVirtual')
                ),
                'cache_is_pack' => array (
                    'value' => 'cache_is_pack',
                    'label' => MLI18n::gi()->get('Prestashop_Productlist_Header_sPack')
                )
            );
        }
        return $this->aFilterValues;
    }
    
}

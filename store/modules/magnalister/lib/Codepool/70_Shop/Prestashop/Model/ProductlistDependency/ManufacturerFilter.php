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
MLFilesystem::gi()->loadClass('Shop_Model_ProductListDependency_ManufacturerFilter_Abstract');
class ML_Prestashop_Model_ProductListDependency_ManufacturerFilter extends ML_Shop_Model_ProductListDependency_ManufacturerFilter_Abstract {
    
    /**
     * possible filter values
     * @var null | array key => value
     */
    protected $aFilterValues = null;
    
    /**
     * @param ML_Database_Model_Query_Select $mQuery
     * @return void
     */
    public function manipulateQuery($mQuery) {
        $sFilterValue = $this->getFilterValue();
        if (
            !empty($sFilterValue)
            && array_key_exists($sFilterValue, $this->getFilterValues())
        ) {
            $mQuery->where("p.`id_manufacturer` = $sFilterValue");
        }
    }

    /**
     * key => value for manufacturers
     * @return array
     */
    protected function getFilterValues() {
        if ($this->aFilterValues === null) {
            $aOut = array (
                '' => array (
                    'value' => '' ,
                    'label' => sprintf(MLI18n::gi()->get('Productlist_Filter_sEmpty') , 'Manufacturer')
                )
            ) ;
            foreach (Manufacturer::getManufacturers() as $aManufacturer ) {
                if ($aManufacturer['id_manufacturer'] != '') {
                    $aOut[$aManufacturer['id_manufacturer']] = array (
                        'value' => $aManufacturer['id_manufacturer'] ,
                        'label' => $aManufacturer['name'] ,
                    ) ;
                }
            }
            $this->aFilterValues = $aOut;
        }
        return $this->aFilterValues;
    }

}

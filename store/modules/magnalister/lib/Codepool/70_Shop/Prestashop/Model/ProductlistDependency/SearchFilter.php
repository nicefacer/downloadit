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
MLFilesystem::gi()->loadClass('Shop_Model_ProductListDependency_SearchFilter_Abstract');
class ML_Prestashop_Model_ProductListDependency_SearchFilter extends ML_Shop_Model_ProductListDependency_SearchFilter_Abstract {
    
    /**
     * @param ML_Database_Model_Query_Select $mQuery
     * @return void 
     */
    public function manipulateQuery($mQuery) {
        $sFilterValue = $this->getFilterValue();
        if (!empty($sFilterValue)) {
            $aIds = array();
            foreach( MLDatabase::factorySelectClass()->select('id_product')->from(_DB_PREFIX_."product_attribute")->where(array ('`reference`' ,"LIKE", "%{$sFilterValue}%" ))->getResult() as $aRow) {
                $aIds[] = $aRow['id_product'];
            }
            $mQuery->where(
                array(
                    'or' => array (
                        array ('p.`id_product`',"=" , "$sFilterValue" ) ,
                        array ('p.`reference`' ,"LIKE", "%{$sFilterValue}%") ,
                        array ('pl.`name`' ,"LIKE", "%{$sFilterValue}%" ),
                        (
                            count($aIds) > 0 
                            ? array ('pl.`id_product`' ,'in', '('.  implode(',', $aIds).')') 
                            : 'false'
                        )
                    )
                )
            ) ;
        }
    }
    
}

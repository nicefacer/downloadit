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
class ML_Shopware_Model_ProductListDependency_SearchFilter extends ML_Shop_Model_ProductListDependency_SearchFilter_Abstract {
    
    /**
     * @param ML_Database_Model_Query_Select $mQuery
     * @return void
     */
    public function manipulateQuery($mQuery) {
        $sFilterValue = $this->getFilterValue();
        if (!empty($sFilterValue)) {
            $aConf = MLModul::gi()->getConfig();
            $iLangId = $aConf['lang'];
            $mQuery
                ->join(array('s_articles_translations', 't', 't.`articleID` = p.`id` AND t.languageID='.(int)$iLangId), 1)
                ->where(
                    array(
                        'or' => array (
                            array ('details.`id`',"=" , "$sFilterValue" ) ,
                            array ('details.`ordernumber`' ,"LIKE", "%{$sFilterValue}%") ,
                            array ('t.`name`' ,"LIKE", "%{$sFilterValue}%" ),
                            array ('p.`name`' ,"LIKE", "%{$sFilterValue}%" )
                        )
                    )
                )
            ;
        }
    }
    
}

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
abstract class ML_Productlist_Model_ProductList_Abstract{
    /**
     * set request $_REQUEST['filter']
     * including 
     *  meta=>array(
     *      'page'=>int, 
     *      'order'=>%fieldName%_%asc|desc%, 
     *      'additional stuff'=>'' 
     * )
     * @return $this
     */
    abstract public function setFilters($aFilter);
    
    /**
     * get list of ML_Magnalister_Model_Shop_Product
     * ML_Shop_Model_Product_Abstract::getMixedData('@key'=>array(mo optional data, need the same key like getHead),..)
     * @see /Extensions/System/Magnalister/View/widget/productlist/filter/%typeValue%.php
     * @return iterator
     */
    abstract public function getList();
    
    /**
     * get list of filters
     *  array(
     *      array(//filter
     *          //type: defines template for render.
     *          //@see /Extensions/System/Magnalister/View/widget/productlist/filter/%typeValue%.php
     *          'type'=>'typeValue',
     *          'depend'=>'on',
     *          'typeValue'=>'template',
     *      ),
     *      ...
     *  )
     * @return array
     */
    abstract public function getFilters();
    
    /**
     * get statistic of list
     * array(
     *  'blPagination'=>bool,//optional, if false no pagination
     *  'iCountPerPage'=>0
     *  'iCurrentPage'=>0
     *  'iCountTotal'=>0,
     *  'aOrder'=>array(
     *      'name'=>''
     *      'direction'=>''
     *  )
     * )
     * @return array 
     */
    abstract public function getStatistic();
    
    /**
     * get columns of productlist (thead)
     * array(
     *  '@key'=>array(
     *      'title'=>'th-element',
     *      'type'=>'for th-class'
     *      'order'=>'order-name'//if isset will be possible order asc or desc
     *  ),
     *  ---
     * )
     * @return array 
     */
    abstract public function getHead();
    
    /**
     * array(
     *      //single row
     *      //@see /Extensions/System/Magnalister/View/widget/productlist/row/%type%.php
     *      'type'=> '',//defines template for render.
     *      'type_variant'=>'',//defines template for render. optional
     *      'width_variant'=>'',//difines colspan. optional
     *      'title'=>'',...
     * );
     * @return array
     * @param $oProduct ML_Shop_Model_Product_Abstract for manipulating $oProduct->__set();
     */
    abstract public function additionalRows(ML_Shop_Model_Product_Abstract $oProduct);
    
    /**
     * 
     * @param ML_Shop_Model_Product_Abstract $oProduct
     * @param string $sKey col-index
     */
    abstract public function getMixedData(ML_Shop_Model_Product_Abstract $oProduct, $sKey);
    /**
     * 
     * @param ML_Shop_Model_Product_Abstract $oProduct
     * @return bool
     */
    abstract public function variantInList(ML_Shop_Model_Product_Abstract $oProduct);
    /**
     * 
     * @param type $iFrom
     * @param type $iCount
     * @return ML_Productlist_Model_ProductList_Abstract
     */
    abstract public function setLimit($iFrom, $iCount);
    /**
     * @param bool $blPage ? current page : complete list
     * @return array 
     */
    abstract public function getMasterIds($blPage = false);
    /**
     * return array of ML_Shop_Model_Product_Abstract
     */
    public function getVariants(ML_Shop_Model_Product_Abstract $oProduct){
        $aVariants=array();
        foreach ($oProduct->getVariants() as $oVariant){
            if($this->variantInList($oVariant)){
                $aVariants[]=$oVariant;
            }
        }
        return $aVariants;
    }
    
    /**
     * returns prepared info of current product
     * 
     * @param ML_Shop_Model_Product_Abstract $oProduct
     * @return array array('color'=>'','title'=>'')
     */
    public function getPreparedFieldData($oProduct) {
        $aI18n = MLDatabase::getPrepareTableInstance()->getPreparedProductListValues();
        $sPreparedType = MLDatabase::getPrepareTableInstance()->getPreparedTypeFieldName();
        if ($oProduct->get('parentid') == 0) {
            $sQuery = "
                SELECT COUNT(*) AS count,
                       ".MLDatabase::getPrepareTableInstance()->getPreparedStatusFieldName()."
                       ".(($sPreparedType === null) ? '' : ', '.$sPreparedType)."
                  FROM magnalister_products
            INNER JOIN ".MLDatabase::getPrepareTableInstance()->getTableName()." ON magnalister_products.id = ".MLDatabase::getPrepareTableInstance()->getTableName().".".MLDatabase::getPrepareTableInstance()->getProductIdFieldName()."
                 WHERE     ".MLDatabase::getPrepareTableInstance()->getMarketplaceIdFieldName()."='".MLModul::gi()->getMarketPlaceId()."'
                       AND magnalister_products.parentid='".$oProduct->get('id')."'
              GROUP BY ".MLDatabase::getPrepareTableInstance()->getPreparedStatusFieldName()."
                       ".(($sPreparedType === null) ? '' : ', '.$sPreparedType)."
            ";
            $aGrouped = MLDatabase::getDbInstance()->fetchArray($sQuery);
            $aOut = array();
            $iCount = 0;
            $iGroup = 0;
            foreach ($aGrouped as $iGroup => $aSelected) {
                if (   isset($aSelected[MLDatabase::getPrepareTableInstance()->getPreparedStatusFieldName()])
                    && array_key_exists($aSelected[MLDatabase::getPrepareTableInstance()->getPreparedStatusFieldName()], $aI18n)
                ) {
                    $aOut[$iGroup] = $aI18n[$aSelected[MLDatabase::getPrepareTableInstance()->getPreparedStatusFieldName()]];
                    $aOut[$iGroup]['status'] = $aSelected[MLDatabase::getPrepareTableInstance()->getPreparedStatusFieldName()];
                    $aTypeI18n = MLI18n::gi()->get(ucfirst(MLModul::gi()->getMarketPlaceName()).'_Productlist_Cell_aPreparedType');
                    if ($sPreparedType !== null) {
                        $aOut[$iGroup]['type'] =
                            isset($aTypeI18n[$aSelected[$sPreparedType]])
                                ? $aTypeI18n[$aSelected[$sPreparedType]]
                                : $aSelected[$sPreparedType];
                    }
                    $aOut[$iGroup]['count'] = $aSelected['count'];
                    $iCount += $aSelected['count'];
                }
            }
            if ($oProduct->getVariantCount() > $iCount) {
                $iGroup++;
                $aOut[$iGroup] = MLI18n::gi()->getGlobal('Productlist_Cell_aNotPreparedStatus');
                $aOut[$iGroup]['status'] = 'not';
                if ($sPreparedType !== null) {
                    $aOut[$iGroup]['type'] = MLI18n::gi()->get('Productlist_Cell_sNotPreparedType');
                }
                $aOut[$iGroup]['count'] = $oProduct->getVariantCount() - $iCount;
            }
            return $aOut;
        } else {
            $aSelected = MLDatabase::getDbInstance()->fetchRow("
                SELECT ".MLDatabase::getPrepareTableInstance()->getPreparedStatusFieldName()."
                       ".(($sPreparedType === null) ? '' : ', '.$sPreparedType)."
                  FROM ".MLDatabase::getPrepareTableInstance()->getTableName()."
                 WHERE     ".MLDatabase::getPrepareTableInstance()->getMarketplaceIdFieldName()."='".MLModul::gi()->getMarketPlaceId()."'
                       AND ".MLDatabase::getPrepareTableInstance()->getProductIdFieldName()."='".$oProduct->get('id')."'
            ");
            if (   isset($aSelected[MLDatabase::getPrepareTableInstance()->getPreparedStatusFieldName()])
                && array_key_exists($aSelected[MLDatabase::getPrepareTableInstance()->getPreparedStatusFieldName()], $aI18n)
            ) {
                $aOut = $aI18n[$aSelected[MLDatabase::getPrepareTableInstance()->getPreparedStatusFieldName()]];
                $aOut['status'] = $aSelected[MLDatabase::getPrepareTableInstance()->getPreparedStatusFieldName()];
                $aTypeI18n = MLI18n::gi()->get(ucfirst(MLModul::gi()->getMarketPlaceName()).'_Productlist_Cell_aPreparedType');
                if ($sPreparedType !== null) {
                    $aOut['type'] =
                        isset($aTypeI18n[$aSelected[$sPreparedType]])
                            ? $aTypeI18n[$aSelected[$sPreparedType]]
                            : $aSelected[$sPreparedType];
                }
                return $aOut;
            } else {
                $aOut = MLI18n::gi()->getGlobal('Productlist_Cell_aNotPreparedStatus');
                $aOut['status'] = 'not';
                if ($sPreparedType !== null) {
                    $aOut['type'] = MLI18n::gi()->get('Productlist_Cell_sNotPreparedType');
                }
                return $aOut;
            }
        }
    }
}
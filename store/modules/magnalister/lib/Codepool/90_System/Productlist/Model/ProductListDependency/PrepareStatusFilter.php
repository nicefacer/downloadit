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
class ML_ProductList_Model_ProductListDependency_PrepareStatusFilter extends ML_ProductList_Model_ProductListDependency_SelectFilter_Abstract {
    
    protected $aFilterValues = null;
    
    protected $aConfig = array(
        'blPrepareMode' => true,//if true, it displays select-box for user interaction
    );
        
    /**
     * render current filter form-field
     * @param ML_Core_Controller_Abstract $oController
     * @param string $sFilterName
     * @return string rendered HTML
     */
    public function renderFilter(ML_Core_Controller_Abstract $oController, $sFilterName) {
        return $this->getConfig('blPrepareMode') ? parent::renderFilter($oController, $sFilterName) : '';
    }
    
    /**
     * Return possible values for filtering
     * @return array array('filter-value' => 'translated-filter-value')
     */
    protected function getFilterValues () {
        if ($this->aFilterValues === null) {
            $aValues = array();
            foreach (array_merge(
                array( 'all' => MLI18n::gi()->get('Productlist_Filter_aPreparedStatus_all')),
                MLDatabase::getPrepareTableInstance()->getPreparedFieldFilterValues(),
                array('not' => MLI18n::gi()->get('Productlist_Filter_aPreparedStatus_not'))
            ) as $sFilterKey => $sFilterValue) {
                $aValues[$sFilterKey] = array('value' => $sFilterKey, 'label' => $sFilterValue);
            }
            $this->aFilterValues = $aValues;
        }
        return $this->aFilterValues;
    }

    /**
     * sets the current value for filter products
     * @param string $sValue
     */
    public function setFilterValue($sValue) {
        return parent::setFilterValue($this->getConfig('blPrepareMode') ? $sValue : MLDatabase::getPrepareTableInstance()->getIsPreparedValue());
    }

    /**
     * check if variant is in filter or not
     * @param ML_Shop_Model_Product_Abstract $oProduct
     * @return boolean
     */
    public function variantIsActive (ML_Shop_Model_Product_Abstract $oProduct) {
        $sValue = $this->getFilterValue();
        if (in_array($sValue, array_keys(MLDatabase::getPrepareTableInstance()->getPreparedFieldFilterValues()))) {
            return MLDatabase::getDbInstance()->fetchOne("
                SELECT count(*)
                FROM ".MLDatabase::getPrepareTableInstance()->getTableName()."
                WHERE ".MLDatabase::getPrepareTableInstance()->getMarketplaceIdFieldName()."='".MLModul::gi()->getMarketPlaceId()."'
                AND ".MLDatabase::getPrepareTableInstance()->getPreparedStatusFieldName()."='".MLDatabase::getDbInstance()->escape($sValue)."'
                AND ".MLDatabase::getPrepareTableInstance()->getProductIdFieldName()."='".$oProduct->get('id')."'
            ") > 0 ? true : false;
        } elseif ($sValue == 'not') {
            return MLDatabase::getDbInstance()->fetchOne("
                SELECT count(*)
                FROM ".MLDatabase::getPrepareTableInstance()->getTableName()."
                WHERE ".MLDatabase::getPrepareTableInstance()->getMarketplaceIdFieldName()."='".MLModul::gi()->getMarketPlaceId()."'
                AND ".MLDatabase::getPrepareTableInstance()->getProductIdFieldName()."='".$oProduct->get('id')."'
            ") == 0 ? true : false;
        }
        return true;
    }
    
    /**
     * returns array with in or not in ident-type query-values
     * @return array array('in' => (array||null), 'notIn' => (array||null)) if null, filter-part is not active
     */
    public function getMasterIdents () {
        $sValue = $this->getFilterValue();
        if (in_array($sValue, array_keys(MLDatabase::getPrepareTableInstance()->getPreparedFieldFilterValues()))) {
            $sProductTable = MLDatabase::getTableInstance('product')->getTableName();
            // get masterarticles which have prepared variants
            $sSql = "
                SELECT master.".(
                    MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.keytype')->get('value') == 'pID' 
                    ? 'productsid' 
                    : 'productssku'
                )."
                FROM ".MLDatabase::getPrepareTableInstance()->getTableName()." prepare
                INNER JOIN ".$sProductTable." variant ON prepare.".MLDatabase::getPrepareTableInstance()->getProductIdFieldName()." = variant.id
                INNER JOIN ".$sProductTable." master ON variant.parentid = master.id
                WHERE prepare.".MLDatabase::getPrepareTableInstance()->getPreparedStatusFieldName()."='".$sValue."'
                AND prepare.".MLDatabase::getPrepareTableInstance()->getMarketplaceIdFieldName()."='".MLModul::gi()->getMarketPlaceId()."'
                GROUP BY master.id
            ";
            return array(
                'in' => MLDatabase::getDbInstance()->fetchArray($sSql, true),
                'notIn' => null,
            );
        } elseif ($sValue == 'not') {
            $sProductTable = MLDatabase::getTableInstance('product')->getTableName();
            // get masterarticles which have no/missing prepared variant
            $sSql = "
                SELECT master.".(
                    MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.keytype')->get('value') == 'pID' 
                    ? 'productsid' 
                    : 'productssku'
                )."
                FROM ".MLDatabase::getPrepareTableInstance()->getTableName()." prepare
                INNER JOIN ".$sProductTable." variant ON prepare.".MLDatabase::getPrepareTableInstance()->getProductIdFieldName()." = variant.id
                INNER JOIN ".$sProductTable." master ON variant.parentid = master.id
                INNER JOIN ".$sProductTable." variantTotal ON master.id = variantTotal.parentid
                WHERE prepare.".MLDatabase::getPrepareTableInstance()->getMarketplaceIdFieldName()."='".MLModul::gi()->getMarketPlaceId()."'
                GROUP BY master.id
                HAVING COUNT(DISTINCT variant.id) >= COUNT(DISTINCT variantTotal.id) 
            ";
            return array(
                'in' => null,
                'notIn' => MLDatabase::getDbInstance()->fetchArray($sSql, true),
            );
        } else {
            return array(
                'in' => null,
                'notIn' => null,
            );
        }
    }
    
}

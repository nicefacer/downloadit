<?php
abstract class ML_ZzzzDummy_Model_ProductList_Abstract extends ML_Productlist_Model_ProductList_Selection {
   
    protected $aRequestedFilters = array();
    
    protected $aDependencys = array();
    
    
    protected $aLimit = array();
    
    protected $oShopList = null;
    
    protected $oList = null;
    
    protected $aFilters = null;
    
    protected $aHeaders = null;
    
    protected $aOrder = array();
    
    /**
     * default filters for all productlists
     * @return array
     */
    protected function getZzzzDummyConfig ($sType) {
        if ($sType == 'filter') {
            return array(
                'search' => MLProductList::dependencyInstance('searchfilter'),
                'limit' => array(
                    'type' => 'select',
                    'values' => array(
                        '5' => array('value' => 5, 'label' => sprintf(MLI18n::gi()->get('Productlist_Filter_sLimit'), '5')),
                        '10' => array('value' => 10, 'label' => sprintf(MLI18n::gi()->get('Productlist_Filter_sLimit'), '10')),
                        '25' => array('value' => 25, 'label' => sprintf(MLI18n::gi()->get('Productlist_Filter_sLimit'), '25')),
                        '50' => array('value' => 50, 'label' => sprintf(MLI18n::gi()->get('Productlist_Filter_sLimit'), '50')),
                        '75' => array('value' => 75, 'label' => sprintf(MLI18n::gi()->get('Productlist_Filter_sLimit'), '75')),
                        '100' => array('value' => 100, 'label' => sprintf(MLI18n::gi()->get('Productlist_Filter_sLimit'), '100')),
                    )
                )
            );
        } else {
            return array(
                'image' => array(
                    'title' => MLI18n::gi()->get('Productlist_Header_sImage'),
                    'type' => 'image',
                    'order' => false, 
                ),
                'name' => array(
                    'title' => MLI18n::gi()->get('Productlist_Header_sProduct'),
                    'type' => 'product',
                    'order' => 'name',
                ),
                'priceShop' => array(
                    'title' =>  MLI18n::gi()->get('Productlist_Header_sPriceShop'),
                    'order' => 'price',
                    'type' => 'priceShop'
                ),
            );
        }
    }
    
    public function getFilters() {
        if ($this->aFilters === null) {
            $aFilters = array();
            foreach ($this->getZzzzDummyConfig('filter') as $sName => $mConfig) {
                if (is_object($mConfig)) {
                    $this->aDependencys[$sName] = $mConfig->setFilterValue(isset($this->aRequestedFilters[$sName]) ? $this->aRequestedFilters[$sName] : '');
                    $aFilters[$sName] = $mConfig;
                } elseif (!empty($mConfig['type'])) {
                    $mConfig['name'] = $sName;
                    if (
                        array_key_exists($sName, $this->aRequestedFilters)
                        && (!array_key_exists('values', $mConfig) || array_key_exists($this->aRequestedFilters[$sName], $mConfig['values']))
                    ) {
                        $mConfig['value'] = $this->aRequestedFilters[$sName];
                    } elseif (array_key_exists('values', $mConfig)) {
                        $aCurrentValue = current($mConfig['values']);
                        $mConfig['value'] = $aCurrentValue['value'];
                    } else {
                        $mConfig['value'] = '';
                    }
                    $aFilters[$sName] = $mConfig;
                }
            }
            $this->setLimit(isset($this->aRequestedFilters['meta']) && isset($this->aRequestedFilters['meta']['page']) ? $this->aRequestedFilters['meta']['page'] * $aFilters['limit']['value'] : 0, $aFilters['limit']['value']);
            $this->aFilters = $aFilters;
        }
        return $this->aFilters;
    }
    
    public function setFilters($aFilter) {
        $aFilter = is_array($aFilter) ? $aFilter : array();
        $this->aRequestedFilters = $aFilter;
        return $this;
    }
    

    public function getHead() {
        if ($this->aHeaders === null) {
            $aHeaders = array();
            foreach ($this->getZzzzDummyConfig('header') as $sName => $aConfig) {
                $aHeaders[$sName] = $aConfig;
            }
            $this->aHeaders = $aHeaders;
        }
        return $this->aHeaders;
    }
    
    
    protected function getShopList () {
        if ($this->oShopList === null) {
            $aFilters = $this->getFilters();
            $oShopTable = MLDatabase::factory('ZzzzDummyShopProduct');
            if (
                isset($aFilters['search']) && 
                is_object($aFilters['search'])
            ) {
                $sFilterValue = $aFilters['search']->getFilterValue();
                if (
                    !empty($sFilterValue) &&
                    $sFilterValue == MLDatabase::getDbInstance()->escape($sFilterValue) &&
                    strpos($sFilterValue, '-') === false) 
                {
                    // generate product
                    $oShopTable->zzzzDummyProduct($aFilters['search']->getFilterValue());
                }
            }
            $oShopList = $oShopTable->getList();
            $oShopQuery = $oShopList->getQueryObject();
            $oShopQuery->where('parentid is null');
            foreach ($aFilters as $aFilter) {
               if (array_key_exists('sql', $aFilter) && !empty($aFilter['value'])) {
                   $oShopQuery->where(str_replace('{value}', MLDatabase::getDbInstance()->escape($aFilter['value']), $aFilter['sql']));
               }
            }
            foreach ($this->aDependencys as $oDependency) {
                $oDependency->manipulateQuery($oShopQuery);
                 $aIdentFilter = $oDependency->getMasterIdents();
                 if ($aIdentFilter['in'] !== null || $aIdentFilter['notIn'] !== null) {
                    $sField = MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.keytype')->get('value') == 'pID' ? 'id' : 'sku';
                    foreach (array('in' => 'IN', 'notIn' => 'NOT IN') as $sSqlKey => $sSqlValue) {
                        if ($aIdentFilter[$sSqlKey] !== null) {
                            $oShopQuery->where($sField." ".$sSqlValue."('".implode("', '", array_unique(MLDatabase::getDbInstance()->escape($aIdentFilter[$sSqlKey])))."')");
                        }
                    }
                 }
            }
            if (isset($this->aLimit['from']) && isset($this->aLimit['count'])) {
                $oShopQuery->limit($this->aLimit['from'], $this->aLimit['count']);
            }
            $sOrder = null;
            if (isset($this->aRequestedFilters['meta']) && isset($this->aRequestedFilters['meta']['order'])) {
                $aOrder = explode('_', $this->aRequestedFilters['meta']['order']);
                foreach ($this->getHead() as $aHead) {
                    if (isset($aHead['order']) && $aHead['order'] !== false) {
                        if ($aOrder[0] == $aHead['order']) {
                            $this->aOrder = $aOrder;
                            break;
                        }
                    }
                }
            }
            if (empty($this->aOrder)) {
                foreach ($this->getHead() as $aHead) {
                    if (isset($aHead['order']) && $aHead['order'] !== false) {
                        $this->aOrder = array($aHead['order'], 'ASC');
                    }
                }
            }
            if (!empty($this->aOrder)) {
                $oShopQuery->orderBy(implode(' ', $this->aOrder));
            }
            $this->oShopList = $oShopList;
        }
        return $this->oShopList;
    }

    public function getList() {
        if ($this->oList === null) {
            $aOut = array();
            foreach ($this->getShopList()->getList() as $oShopProduct) {
                $oMlProduct = MLProduct::factory();
                /* @var $oProduct ML_Shop_Model_Product_Abstract */
                $oMlProduct->loadByShopProduct($oShopProduct);
                $aOut[$oMlProduct->get('id')] = $oMlProduct;
            }
            $this->oList = new ArrayIterator($aOut);
        }
        $this->oList->rewind();
        return $this->oList;
    }

    public function getMasterIds($blPage = false) {
        $aOut = array();
        if ($blPage) {
            foreach ($this->getList() as $oProduct) {
                $aOut[] = $oProduct->get('id');
            }
        } else {
            $oShopListClone = clone $this->getShopList();
            $oShopListClone->getQueryObject()->limit(null);
            foreach($oShopListClone->getList() as $oShopProduct) {
                $oMlProduct = MLProduct::factory();
                /* @var $oProduct ML_Shop_Model_Product_Abstract */
                $oMlProduct->loadByShopProduct($oShopProduct);
                $aOut[] = $oMlProduct->get('id');
            }
        }
        return $aOut;
    }

    public function getStatistic() {
        $aFilters = $this->getFilters();
        return array(
            'blPagination' => true,//optional, if false no pagination
            'iCountPerPage' => isset($aFilters['limit']) ? $aFilters['limit']['value'] : 5 ,
            'iCurrentPage' => isset($this->aRequestedFilters['meta']) && isset($this->aRequestedFilters['meta']['page']) ? $this->aRequestedFilters['meta']['page'] : 0,
            'iCountTotal' => $this->getShopList()->getQueryObject()->getCount(true),
            'aOrder' => array(
                'name' => $this->aOrder[0],
                'direction' => $this->aOrder[1],
            )
        );
    }
    
    public function setLimit($iFrom, $iCount) {
        $this->aLimit = array('from' => $iFrom, 'count' => $iCount);
        return $this;
    }

    public function variantInList(ML_Shop_Model_Product_Abstract $oProduct) {
        foreach ($this->aDependencys as $oDependency) {
            if (!$oDependency->variantIsActive($oProduct)) {
                return false;
            }
        }
        return true;
    }


    
    
    

    public function getMixedData(ML_Shop_Model_Product_Abstract $oProduct, $sKey) {
        
    }
    public function additionalRows(ML_Shop_Model_Product_Abstract $oProduct) {
        
    }
    
}

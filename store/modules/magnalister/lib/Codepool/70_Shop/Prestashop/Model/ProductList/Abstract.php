<?php

abstract class ML_Prestashop_Model_ProductList_Abstract extends ML_Productlist_Model_ProductList_Selection {

    protected $sPrefix = 'ml_';

    /**
     * filter
     * @var ML_Prestashop_Helper_Model_ProductList_Filter $oFilter
     */
    protected $oFilter = null;

    /**
     * list/result
     * @var ML_Prestashop_Helper_Model_ProductList_List $oList
     */
    protected $oList = null;
    protected $sOrder = '';
    protected $oCollection = null;

    /**
     * @var ML_Database_Model_Query_Select $oSelectQuery
     */
    protected $oSelectQuery = null;

    public function __construct() {
        $oSelectquery = MLHelper::gi("model_product")->getProductSelectQuery(true);
        $this->oSelectQuery = $oSelectquery;
        $this->oFilter = MLHelper::gi('model_productlist_filter')
            ->clear()
            ->setCollection($oSelectquery)
            ->setPrefix($this->sPrefix)
        ;
        $this->initList();
        $this->oList
            ->clear()
            ->setCollection($oSelectquery)
        ;
    }

    protected function initList() {
        $this->oList = MLHelper::gi('model_productlist_list');
    }

    public function setFilters($aFilter) {
        //echo( "<pre>".print_m($aFilter).'</pre>');
        if (is_array($aFilter)) {
            $this->oFilter
                ->setFilter($aFilter)
                ->setPage(isset($aFilter['meta']['page']) ? $aFilter['meta']['page'] : 0)
                ->setOffset(isset($aFilter['meta']['offset']) ? $aFilter['meta']['offset'] : 0)
                ->setOrder(isset($aFilter['meta']['order']) ? $aFilter['meta']['order'] : '')
            ;
        }
        $this->sOrder = isset($aFilter['meta']['order']) ? $aFilter['meta']['order'] : '';
        $this->executeList();
        $this->executeFilter();
    }

    public function getFilters() {
        //exit(print_m($this->oFilter->getOutput()));
        return $this->oFilter->getOutput();
    }

    public function getStatistic() {//exit(print_m($this->oFilter->getStatistic()));
        return $this->oFilter->getStatistic();
    }

    public function getMasterIds($blPage = false) {
        $aMainIds = array();
        if ($blPage) {
            $aMainIds = $this->oList->getLoadedList();
        } else {
            $aIdArrays = $this->oSelectQuery->getAll();
            foreach ($aIdArrays as $aItem) {
                $aMainIds[] = current($aItem);
            }
        }
        $aIds = array(); //array_unique($aMainIds);
        foreach ($aMainIds as $sId) {
            $oProduct = new Product($sId);
            $aIds[] = MLProduct::factory()->loadByShopProduct($oProduct)->get('id');
        }
        return $aIds;
    }

    abstract protected function executeFilter();

    abstract protected function executeList();

    public function getHead() {
        return $this->oList->getHeader();
    }

    public function getList() {
        return new ArrayIterator($this->oList->getList());
    }

    public function additionalRows(ML_Shop_Model_Product_Abstract $sId) {
        return array();
    }

    public function getMixedData(ML_Shop_Model_Product_Abstract $oProduct, $sKey) {
        return $this->oList->getMixedData($oProduct, $sKey);
    }

    public function variantInList(ML_Shop_Model_Product_Abstract $oProduct) {
        return $this->oFilter->variantInList($oProduct);
    }

    public function setLimit($iFrom, $iCount) {
        $this->oSelectQuery->limit($iFrom, $iCount);
        return $this;
    }
}

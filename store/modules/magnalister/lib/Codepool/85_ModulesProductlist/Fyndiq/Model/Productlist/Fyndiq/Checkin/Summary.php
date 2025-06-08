<?php

class ML_Fyndiq_Model_ProductList_Fyndiq_Checkin_Summary extends ML_Productlist_Model_ProductList_Selection {

    protected $aList = null;
    protected $iCountTotal = 0;
    protected $aMixedData = array();
    protected $iFrom = 0;
    protected $iCount = 5;

    public function additionalRows(ML_Shop_Model_Product_Abstract $oProduct) {
        return array();
    }

    public function getFilters() {
        return array();
    }

    public function getHead() {
        return array(
            'image' => array(
                'title' => '',
                'type' => 'image',
            ),
            'product' => array(
                'title' => MLI18n::gi()->get('Productlist_Header_sProduct'),
                'type' => 'product',
            ),
            'categoryPath' => array(
                'title' => MLI18n::gi()->get('Productlist_Header_Field_sCategoryPath'),
                'type' => 'categorypath',
                'type_variant' => 'fyndiq_form',
                'width_variant' => 2,
            ),
            'priceshop' => array(
                'title' => MLI18n::gi()->get('Productlist_Header_sPriceShop'),
                'type' => 'priceShop',
                'type_variant' => '',
            ),
        );
    }

    public function getList() {
        if ($this->aList === null) {
            $this->aList = array();
            $sSql = "
                SELECT %s
                  FROM magnalister_selection s, 
                       magnalister_products p 
                 WHERE s.pID=p.ID
                    AND s.session_id='" . MLShop::gi()->getSessionId() . "'
                    AND s.selectionname='checkin'
                    AND mpid='" . MLModul::gi()->getMarketPlaceId() . "'
            ";
            $this->iCountTotal = MLDatabase::getDbInstance()->fetchOne(sprintf($sSql, ' count(distinct p.ParentId) '));
            foreach (MLDatabase::getDbInstance()->fetchArray(sprintf($sSql, ' distinct p.ParentId ') . " limit " . $this->iFrom . ", " . $this->iCount) as $aRow) {
                $this->aList[$aRow['ParentId']] = MLProduct::factory()->set("id", $aRow['ParentId'])->load();
            }
        }

        return new ArrayIterator($this->aList);
    }

    public function getMasterIds($blPage = false) {
        $this->getList();
        return array_keys($this->aList);
    }

    public function getStatistic() {
        $this->getList();
        $aOut = array(
            'iCountPerPage' => $this->iCount,
            'iCurrentPage' => $this->iFrom / $this->iCount,
            'iCountTotal' => $this->iCountTotal,
            'aOrder' => array(
                'name' => '',
                'direction' => ''
            )
        );
        return $aOut;
    }

    public function setLimit($iFrom, $iCount) {
        $this->iFrom = (int) $iFrom;
        $this->iCount = ((int) $iCount > 0) ? ((int) $iCount) : 5;
        return $this;
    }

    public function setFilters($aFilter) {
        $iPage = isset($aFilter['meta']['page']) ? ((int) $aFilter['meta']['page']) : 0;
        $iPage = $iPage < 0 ? 0 : $iPage;
        $iFrom = $iPage * $this->iCount;
        $this->iFrom = $iFrom;
        return $this;
    }

    public function getMixedData(ML_Shop_Model_Product_Abstract $oProduct, $sKey) {
        return $oProduct->getModulField($sKey, substr($sKey, 0, strpos($sKey, '.')) == 'general');
    }

    public function variantInList(ML_Shop_Model_Product_Abstract $oProduct) {
        return MLDatabase::factory('selection')->loadByProduct($oProduct, 'checkin')->get('expires') === null ? false : true;
    }

    public function getSelectionName() {
        return 'checkin';
    }
}

<?php
class ML_MeinPaket_Model_ProductList_MeinPaket_Checkin_Summary extends ML_Productlist_Model_ProductList_Selection {
    protected $aList=null;
    protected $iCountTotal=0;
    protected $aMixedData=array();
    protected $iFrom=0;
    protected $iCount=5;
 
    
    public function additionalRows(ML_Shop_Model_Product_Abstract $oProduct) {
        return array();
    }

    public function getFilters() {
         return array();
    }

    public function getHead() {
        $aHead=array();
        $aHead['image']=array(
            'title'=>'',
            'type'=>'image'
        );
        $aHead['product']=array(
            'title'=>  MLI18n::gi()->get('Productlist_Header_sProduct'),
            'type'=>'product'
        );
        $sValue = MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.manufacturerpartnumber')->get('value');
        if(!empty($sValue)){
            $aHead['general.manufacturerpartnumber']=array(
                'title'=>  MLI18n::gi()->get('Productlist_Header_Field_sManufacturerpartnumber'),
                'type'=>'simpleText'
            );
        }
        $sValue = MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.ean')->get('value');
        if(!empty($sValue)){
             $aHead['general.ean']=array(
                'title'=>MLI18n::gi()->get('Productlist_Header_Field_sEan'),
                'type'=>'simpleText'
            );
        }
        $aHead['categoryPath']=array(
            'title'=>MLI18n::gi()->get('Productlist_Header_Field_sCategoryPath'),
            'type'=>'categorypath',
            'type_variant'=>'meinpaket_form',
            'width_variant'=>2
        );
        $aHead['priceshop']=array(
            'title'=>  MLI18n::gi()->get('Productlist_Header_sPriceShop'),
            'type'=>'priceShop',
            'type_variant'=>'',
        );
        return $aHead;
    }

    public function getList() {
        if($this->aList===null){
            $this->aList=array();
            $sSql="
                select %s
                from 
                    magnalister_selection s, 
                    magnalister_products p 
                where 
                    s.pID=p.ID
                    and
                    s.session_id='".MLShop::gi()->getSessionId()."'
                    and
                    s.selectionname='checkin'
                    and
                    mpid='".MLModul::gi()->getMarketPlaceId()."'
            ";
            $this->iCountTotal= MLDatabase::getDbInstance()->fetchOne(sprintf($sSql,' count(distinct p.ParentId) '));
            foreach(MLDatabase::getDbInstance()->fetchArray(sprintf($sSql,' distinct p.ParentId ')." limit ".$this->iFrom.", ".$this->iCount) as $aRow){
                $this->aList[$aRow['ParentId']]=MLProduct::factory()->set("id",$aRow['ParentId'])->load();
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
        $aOut= array(
            'iCountPerPage'=>$this->iCount,
            'iCurrentPage'=>$this->iFrom/$this->iCount,
            'iCountTotal'=>$this->iCountTotal,
            'aOrder'=>array(
                'name'=>'',
                'direction'=>''
            )
        );
        return $aOut;
    }
    public function setLimit($iFrom, $iCount){
        $this->iFrom=(int)$iFrom;
        $this->iCount=((int)$iCount>0)?((int)$iCount):5;
        return $this;
    }

    public function setFilters($aFilter) {
        $iPage=isset($aFilter['meta']['page'])?((int)$aFilter['meta']['page']):0;
        $iPage=$iPage<0?0:$iPage;
        $iFrom=$iPage*$this->iCount;
        $this->iFrom=$iFrom;
        return $this;
    }

    public function getMixedData(ML_Shop_Model_Product_Abstract $oProduct, $sKey) {
        return $oProduct->getModulField($sKey, substr($sKey,0,  strpos($sKey, '.'))=='general');
    }

    public function variantInList(ML_Shop_Model_Product_Abstract $oProduct) {
        return MLDatabase::factory('selection')->loadByProduct($oProduct, 'checkin')->get('expires')===null?false:true;
    }

    public function getSelectionName() {
        return 'checkin';
    }
}
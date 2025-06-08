<?php
MLFilesystem::gi()->loadClass('Productlist_Controller_Widget_ProductList_Selection');
class ML_Amazon_Controller_Amazon_Checkin_Summary extends ML_Productlist_Controller_Widget_ProductList_Selection {
    protected $aParameters = array('controller');
    protected $aPrepare=array();
    protected $aSelected=array();
    public function render(){
        $aStatistic=$this->getProductList()->getStatistic();
        if($aStatistic['iCountTotal']==0){
            MLHttp::gi()->redirect($this->getParentUrl());
        }
        return parent::render();
    }
    protected function callAjaxCheckinAdd() {
        return $this->addItems(false);
        
    }
    protected function callAjaxCheckinPurge() {
        return $this->addItems(true);
    }
    
    protected function addItems($blPurge) {
        $oList = $this->getProductList();
        $iOffset = $this->getRequest('offset');
        $iOffset = ($iOffset === null) ? 0 : $iOffset;
        $iLimit = 1;//min from list
        $oList->setLimit(0, $iLimit);//offset is 0, because uploaded products will be deleted from selections
        $aStatistic = $oList->getStatistic();
        $iTotal = (int)$aStatistic['iCountTotal'];
        $blPurge = ($blPurge && $iOffset == 0);
        $oService =  MLService::getAddItemsInstance();
        try {
            $oService->setProductList($oList)->setPurge($blPurge)->execute();
            $blSuccess = true;
        } catch (Exception $oEx) {//more
            $blSuccess = false;
        }
        if ($oService->haveError()) {
            foreach ($oService->getErrors() as $sServiceMessage) {
                MLSetting::gi()->add('aAjaxPlugin', array('dom' => array('#recursiveAjaxDialog .errorBox' => array('action' => 'appendifnotexists', 'content' => '<div>'.$sServiceMessage.'</div>'))));
            }
        }
        if ($this->getRequest('saveSelection') != 'true') {
            MLSetting::gi()->add(
                'aAjax',
                array(
                    'success' => $blSuccess,
                    'error' => $oService->haveError() ,
                    'offset' => $iOffset+count($oList->getMasterIds(true)),
                    'info' => array(
                        'total' => $iTotal+$iOffset,
                        'current' => $iOffset+count($oList->getMasterIds(true)),
                        'purge' => $blPurge,
                    ),
                )
            );
            $oSelection = MLDatabase::factory('selection');
            foreach ($oList->getList() as $oProduct) {
                foreach ($oList->getVariants($oProduct) as $oChild) {
                    $oSelection->init()->loadByProduct($oChild,'checkin')->delete();
                }
            }
        } else {
            MLSetting::gi()->add(
                'aAjax',
                array(
                    'success' => false,
                    'error' => $oService->haveError() ,
                    'offset' => $iOffset,
                    'info' => array(
                        'total' => $iTotal+$iOffset,
                        'current' => $iOffset,
                        'purge' => $blPurge,
                    ),
                )
            );
        }
        return $this;
    }
    public function getPrepareData(ML_Shop_Model_Product_Abstract $oProduct){
        if(!isset($this->aPrepare[$oProduct->get('id')])){
            $this->aPrepare[$oProduct->get('id')]=MLDatabase::factory('amazon_prepare')->set('productsid',$oProduct->get('id'));
        }
        return $this->aPrepare[$oProduct->get('id')];
    }
    public function getSelectedData(ML_Shop_Model_Product_Abstract $oProduct){
        if(!isset($this->aSelected[$oProduct->get('id')])){
            $this->aSelected[$oProduct->get('id')]=MLDatabase::factory('selection')->set('pid',$oProduct->get('id'))->set('selectionname','checkin');;
        }
        return $this->aSelected[$oProduct->get('id')];
    }
    public function getPrice(ML_Shop_Model_Product_Abstract $oProduct){
        $aSelect=$this->getSelectedData($oProduct)->get('data');
        if(isset($aSelect['price'])){
            return $aSelect['price'];
        }else{
            $oPrepare=$this->getPrepareData($oProduct);
            if($oPrepare->get('price')!==null){
                return $oPrepare->get('price');
            }else{
                return $oProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject(),true,false);
            }
        }
    }
    public function getStock(ML_Shop_Model_Product_Abstract $oProduct){
        $aSelect=$this->getSelectedData($oProduct)->get('data');
        if(isset($aSelect['stock'])){
            return $aSelect['stock'];
        }else{
            $oPrepare=$this->getPrepareData($oProduct);
            if($oPrepare->get('quantity')!==null){
                return $oPrepare->get('quantity');
            }else{
                $aStockConf=  MLModul::gi()->getStockConfig();
                return $oProduct->getSuggestedMarketplaceStock($aStockConf['type'],$aStockConf['value']);
            }
        }
    }
    public function getPriceObject(ML_Shop_Model_Product_Abstract $oProduct) {
        return MLModul::gi()->getPriceObject();
    }
}
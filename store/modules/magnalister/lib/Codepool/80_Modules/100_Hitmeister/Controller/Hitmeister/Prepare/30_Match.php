<?php
MLFilesystem::gi()->loadClass('Productlist_Controller_Widget_ProductList_Selection');
class ML_Hitmeister_Controller_Hitmeister_Prepare_Match extends ML_Productlist_Controller_Widget_ProductList_Selection {
    
    protected $aParameters = array('controller');
//    protected $aParameters = array('mp', 'mode', 'view');
    
    public static function getTabTitle() {
        return MLI18n::gi()->get('ML_AMAZON_PRODUCT_MATCHING');
    }

    public static function getTabActive() {
        return MLModul::gi()->isConfigured();
    }
    
    public static function getTabDefault () {
        $sValue = MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.ean')->get('value');
        return (empty($sValue)) ? true : false;
    }
    
    public function __construct() {
        parent::__construct();
        try{
            $sExecute=$this->oRequest->get('execute');
            if(in_array($sExecute,array('unprepare'))){
                $oModel=  MLDatabase::factory('hitmeister_prepare');
                $oList=MLDatabase::factory('selection')->set('selectionname', 'match')->getList();
                foreach($oList->get('pid') as $iPid){
                    $oModel
                        ->init()
                        ->set('products_id',$iPid)
                        ->delete()
                   ;
                }
            }
        }catch(Exception $oEx){
//            echo $oEx->getMessage();
        }
    }
    
    public function getProductListWidget() {
        $sSubView = MLRequest::gi()->get('controller');
        $aItem = explode('_', $sSubView);
        $sExecute = array_pop($aItem);
        try {
            return $this->getChildController($sExecute)->render();
        } catch (Exception $oEx) {
            if($sExecute !== 'match'){
                MLRequest::gi()->set('controller',str_replace('_'.$sExecute, '', $sSubView),true);
            }
            return parent::getProductListWidget();
        }
    }

    /**
     * only if ean-field is defined
     * @return boolean
     */
    public function useAutoMatching() {
        $sValue = MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.manufacturerpartnumber')->get('value');
        return !empty($sValue);
    }

    public function getPriceObject(ML_Shop_Model_Product_Abstract $oProduct) {
        return MLModul::gi()->getPriceObject();
    }
}
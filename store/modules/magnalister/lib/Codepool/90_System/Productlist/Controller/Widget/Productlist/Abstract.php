<?php
MLFilesystem::gi()->loadClass('Core_Controller_Abstract');
abstract class ML_Productlist_Controller_Widget_ProductList_Abstract extends ML_Core_Controller_Abstract {
    /**
     * render actual variants
     * @var bool $blRenderVariants
     */
    protected $blRenderVariants = false;
    /**
     * calclulates error of actual variants, variants will be loaded
     * @var bool $blRenderVariantsError
     */
    protected $blRenderVariantsError = false;
    /**
     * @var ML_Productlist_Model_ProductList_Abstract
     */
    protected $oList=null;
    /**
     * @throws Exception list ist not possible
     * @return string
     */
    protected function getListName(){
        return $this->getIdent();
    }
    /**
     * render product html
     * @param bool $blRenderVariants should variants rendered too... if master?
     * @return array
     */
    protected function callAjaxRenderProduct($blRenderVariants=true){
        $iProductId =  MLRequest::gi()->get('pid');
        $blRenderVariantsBackup = $this->blRenderVariants;
        $this->blRenderVariants = $blRenderVariants;
        $blRenderVariantsErrorBackup = $this->blRenderVariantsError;
        $this->blRenderVariantsError = true;
        $oProduct = MLProduct::factory()->set('id',$iProductId);
        MLSetting::gi()->add('aAjaxPlugin', array('dom' => array('#productlist-master-'.$oProduct->get('id') => $this->includeViewBuffered('widget_productlist_list_article', array('oList' => $this->getProductList(), 'oProduct' => $oProduct)))));
        $this->blRenderVariants = $blRenderVariantsBackup;
        $this->blRenderVariantsError = $blRenderVariantsErrorBackup;
        if (count(MLMessage::gi()->getObjectMessages($oProduct)) != 0) {//product have error
            MLSetting::gi()->add('aAjaxPlugin', array('success'=> false ));
        }
        return $this;
    }
    
    public function callAjaxDependency () {
        try {
            MLProductList::dependencyInstance(MLRequest::gi()->get('dependency'))->callAjax();
        } catch (Exception $oEx) {
        }
        return $this;
    }

    /**
     * sets productlist filter by request or session
     * save possible filters to session
     * @return $this
     */
    protected function setFilter(){
        $aRequestFilter=MLRequest::gi()->data('filter');
        $sIdent=  MLModul::gi()->getMarketPlaceId().'_'.$this->getIdent();
        $aFilters=array();
        if($aRequestFilter!==null){
            $aFilters[$sIdent]=$aRequestFilter;
        }
        $aSessionFilter=MLSession::gi()->get('PRODUCTLIST__filter.json');
        if(is_array($aSessionFilter)){
            foreach($aSessionFilter as $sController=>$aFilter){
                unset($aFilter['meta']);
                if(substr($sIdent, 0, strlen($sController))==$sController&&!isset($aFilters[$sController])){
                    $aFilters[$sController]=$aFilter;
                }
                if(
                    (
                        $aRequestFilter===null
                        ||
                        count($aRequestFilter)==1 && isset($aRequestFilter['meta'])
                    )
                    && $sController==$sIdent
                ){
                    if(isset($aRequestFilter['meta'])){
                        $aFilter['meta']=$aRequestFilter['meta'];
                    }
                    $aRequestFilter=$aFilter;
                }
            }
        }
        MLSession::gi()->set('PRODUCTLIST__filter.json',$aFilters);
        $this->getProductList()->setFilters($aRequestFilter);
        return $this;
    }


    public function __construct(){
        parent::__construct();                
        $oModul = MLModul::gi();
        if ($oModul->getConfig('currency') !== null && (boolean)$oModul->getConfig('exchangerate_update')) {           
            MLCurrency::gi()->updateCurrencyRate($oModul->getConfig('currency'));
        }
        MLSetting::gi()->add('productListProfile_'.$this->getIdent(), array('construct' => microtime(true)));
        $this->getProductList();//ML_Filesystem_Exception
        $this->setFilter();
    }
        
    /**
     * @return ML_Productlist_Model_ProductList_Abstract
     */
    public function getProductList(){
        if($this->oList===null){
            $this->oList=MLProductList::gi($this->getListName());
        }
        return $this->oList;
    }

    /**
     * includes View/widget/productlist.php
     */
    public function getProductListWidget() {
        $oList = $this->getProductList();
        $aDependencies = array();
        foreach ($oList->getFilters() as $oFilter) {
            if (is_object($oFilter)) {
                $aDependencies[get_class($oFilter)] = $oFilter->getFilterValue();
            }
        }
        $aStatistic = $oList->getStatistic();
        MLSetting::gi()->add('productListProfile_'.$this->getIdent(), array('endquery' => microtime(true)));
        $this->includeView('widget_productlist', array('oList' => $oList, 'aStatistic' => $aStatistic));
        MLSetting::gi()->add('productListProfile_'.$this->getIdent(), array('endrender' => microtime(true)));
        $aProfile = MLSetting::gi()->get('productListProfile_'.$this->getIdent());
        MLMessage::gi()->addDebug('ProductList Profile: '.$this->getIdent(), array(
            'time-query' => $aProfile['endquery']-$aProfile['construct'], 
            'time-render' => $aProfile['endrender'] - $aProfile['endquery'], 
            'statistic' => $oList->getStatistic(),
            'dependencies' => $aDependencies,
        ));
    }
    
    public function renderVariants() {
        return $this->blRenderVariants;
    }
    public function renderVariantsError() {
        return $this->blRenderVariantsError||$this->renderVariants();
    }
    
    public function getVariantCount($mProduct) {
        if (!($mProduct instanceof ML_Shop_Model_Product_Abstract)) {
            $mProduct = MLProduct::factory()->set('id',$mProduct);
        }
        return $mProduct->getVariantCount();
    }
    /**
     * gets form action for each row
     * @param $oProduct  ML_Shop_Model_Product_Abstract
     * @return string url
     */ 
    public function getRowAction(ML_Shop_Model_Product_Abstract $oProduct) {
        return $this->getCurrentUrl(array('ajax'=>true,'pid'=>$oProduct->get('id')));
    }
    
    /**
     * configure price for marketplace
     * maybe marketplace have differnt price-configs (eg. ebay) - so price can be depend on prepare-table
     * @param ML_Shop_Model_Product_Abstract $oProduct for seraching in prepare-table
     * @return ML_Shop_Model_Price_Interface
     */
    abstract public function getPriceObject(ML_Shop_Model_Product_Abstract $oProduct);
}
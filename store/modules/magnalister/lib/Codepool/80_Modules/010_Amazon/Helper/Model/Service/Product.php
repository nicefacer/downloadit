<?php
class ML_Amazon_Helper_Model_Service_Product {
    protected $aModul=null;
    /**
     *
     * @var ML_Database_Model_Table_Selection $oSelection 
     */
    protected $oSelection=null;
    protected $aSelectionData=array();
    /**
     *
     * @var ML_Amazon_Model_Table_Amazon_Prepare $oPrepare 
     */
    protected $oPrepare=null;
    /**
     *
     * @var ML_Shop_Model_Product_Abstract $oProduct
     */
    protected $oProduct=null;
    /**
     *
     * @var array  $aVariants of ML_Shop_Model_Product_Abstract
     */
    protected $aVariants=array();
    /**
     *
     * @var ML_Shop_Model_Product_Abstract $oCurrentProduct
     */
    protected $oCurrentProduct=null;
    
    protected $sPrepareType='';
    protected $aData=null;
    /**
     *
     * @var ML_Magnalister_Model_Modul $oMarketplace
     */
    protected $oMarketplace=null;    
    public function __call($sName,$mValue){
        return $sName.'()';
    }
    
    public function __construct() {
        $this->aModul = MLModul::gi()->getConfig();
        $this->oPrepare = MLDatabase::factory('amazon_prepare');
        $this->oSelection = MLDatabase::factory('selection');
        $this->oMarketplace = MLModul::gi();
    }
    public function setProduct(ML_Shop_Model_Product_Abstract $oProduct){
        $this->oProduct=$oProduct;
        $this->aVariants=array();
        $this->sPrepareType='';
        $this->aData=null;
        return $this;
    }
    public function addVariant(ML_Shop_Model_Product_Abstract $oProduct){
        $this->aVariants[]=$oProduct;
        return $this;
    }
    public function getData(){
        if($this->aData===null){
            $aData = $aApplyVariantsData =array();
            foreach ($this->aVariants as $oVariant){
                /* @var $oVariant ML_Shop_Model_Product_Abstract */
                $this->oPrepare->init()->set('productsid',$oVariant->get('id'));
                $this->oSelection->init()->set('pid',$oVariant->get('id'))->set('selectionname', 'checkin');
                $aSelectionData = $this->oSelection->data();
                $this->aSelectionData = $aSelectionData['data'];
                $this->setPrepareType($this->oPrepare->get('preparetype'));
                $this->oCurrentProduct = $oVariant;
                if($this->sPrepareType == 'apply'){
                    $aVariantData = array();
                    foreach(array(
                        'SKU', 
                        'Price', 
                        'Currency', 
                        'Quantity', 
                        'EAN',
                        'Variation', 
                        'ShippingTime',
                        'ManufacturerPartNumber', 
                        'Images',
                        'BasePrice',
                        'Weight'
                    ) as $sField){
                        $aVariantData[$sField]=$this->{'get'.$sField}();
                    }
                    foreach (array('BasePrice','Weight') as $sKey){
                        if(empty($aVariantData[$sKey])){
                            unset($aVariantData[$sKey]);
                        }
                    }
                    $aApplyVariantsData[]=$aVariantData;
                }else{//match
                    $aVariant=array();
                    foreach(array(
                        'Id',/*use as index in additem */ 
                        'SKU', 
                        'ASIN', 
                        'ConditionType', 
                        'Price', 
                        'Quantity',
                        'WillShipInternationally', 
                        'ConditionNote', 
                        'ShippingTime'
                    ) as $sField){
                        $aVariant[$sField]=$this->{'get'.$sField}();
                    }
                    $aData[]=$aVariant;
                }
            }
            if($this->sPrepareType=='apply'){//add master
                $this->oCurrentProduct=$this->oProduct;
//                $this->oPrepare->init()->set('productsid',$this->oProduct->get('id'));
                foreach(array(
                    'SKU', 
                    'Price', 
                    'Quantity', 
                    'ConditionType', 
                    'MainCategory',
                    'ProductType', 
                    'BrowseNodes', 
                    'ItemTitle', 
                    'Manufacturer', 
                    'Brand', 
                    'ManufacturerPartNumber', 
                    'EAN', 
                    'Images', 
                    'BulletPoints', 
                    'Description', 
                    'Keywords', 
                    'Attributes', 
                    'ConditionNote',
                    'BasePrice',
                    'Weight',
                    'ShippingTime',
                ) as $sField){
                    if(method_exists($this, 'getmaster'.$sField)){
                        $aData[$sField]=$this->{'getmaster'.$sField}($aApplyVariantsData);
                    }else{
                        $aData[$sField]=$this->{'get'.$sField}();
                    }
                } 
                foreach (array('BasePrice','Weight') as $sKey){
                    if(empty($aData[$sKey])){
                        unset($aData[$sKey]);
                    }
                }
                $aData['Variations'] = $aApplyVariantsData;
                if(count($aData['Variations'])==1 and count($aData['Variations'][0]['Variation'])==0){//only master
                    unset($aData['Variations']);
                }
            }
            $this->aData=$aData; 
        }
        return $this->aData;
    }
    protected function getMasterEan($aVariants){
        $aData=$this->oPrepare->get('applydata');
        return (
                isset($aData['EAN'])&& 
                count($this->aVariants) == 1 
                )?$aData['EAN']:$this->oProduct->getModulField('general.ean', true);
    }
    protected function getMasterSku($aVariants){
        return $this->oProduct->getMarketPlaceSku();
    }
    protected function getMasterItemTitle($aVariants){
        $aData=$this->oPrepare->get('applydata');
        return 
            isset($aData['ItemTitle']) 
            ? $aData['ItemTitle']
            : $this->oProduct->getName()
        ;
    }
    protected function getMasterDescription($aVariants){
        $aData=$this->oPrepare->get('applydata');
        return isset($aData['Description'])?$aData['Description']:$this->oProduct->getDescription();
    }
    protected function getMasterQuantity($aVariants){
        $iQty=0;
        foreach($aVariants as $aVariant){
            $iQty+=$aVariant['Quantity'];
        }
        return $iQty;
    }
    protected function getMainCategory(){
        return $this->oPrepare->get('maincategory');
    }
    protected function getProductType(){
        $aData=$this->oPrepare->get('applydata');
        return isset($aData['ProductType'])?$aData['ProductType']:'';
    }
    protected function getBrowseNodes(){
        $aData=$this->oPrepare->get('applydata');
        return isset($aData['BrowseNodes'])?$aData['BrowseNodes']:array('null','null');
    }
    protected function getItemTitle(){
        $aData=$this->oPrepare->get('applydata');
        return isset($aData['ItemTitle'])?$aData['ItemTitle']:$this->oCurrentProduct->getName();
    }
    
    protected function getBasePrice(){
        return $this->oCurrentProduct->getBasePrice();
    }
    
    protected function getWeight(){
        return $this->oCurrentProduct->getWeight();
    }
    
    protected function getMasterBasePrice(){
        return $this->oProduct->getBasePrice();
    }
    
    protected function getMasterWeight(){
        return $this->oProduct->getWeight();
    }
    protected function getImageSize(){        
        $sSize = MLModul::gi()->getConfig('imagesize');
        $iSize = $sSize == null ? 500 : (int)$sSize;
        return $iSize;
    }
    public function getBulletPoints(){
        $aData=$this->oPrepare->get('applydata');
        return isset($aData['BulletPoints'])?$aData['BulletPoints']:array('','','','','');
    }
    public function getDescription(){
        $aData=$this->oPrepare->get('applydata');
        return isset($aData['Description'])?$aData['Description']:$this->oCurrentProduct->getDescription();
    }
    public function getKeywords(){
        $aData=$this->oPrepare->get('applydata');
        return isset($aData['Keywords'])?$aData['Keywords']:array('','','','','');
    }
    public function getAttributes(){
        $aData=$this->oPrepare->get('applydata');
        return isset($aData['Attributes'])?$aData['Attributes']:array();
    }
    protected function getManufacturer(){
        $aData=$this->oPrepare->get('applydata');
        return (
                isset($aData['Manufacturer'])&& 
                count($this->aVariants) == 1 
                )?$aData['Manufacturer']:$this->oCurrentProduct->getModulField('manufacturer');
    }
    protected function getBrand(){
        $aData=$this->oPrepare->get('applydata');
        return isset($aData['Brand'])?$aData['Brand']:'';
    }
    
    protected function getManufacturerPartNumber(){
        $blSkuasmfrpartnoConfig = $this->oMarketplace->getConfig('checkin.skuasmfrpartno');
        if ($blSkuasmfrpartnoConfig) {
            return $this->oCurrentProduct->getSku();
        } else {
            return $this->oCurrentProduct->getManufacturerPartNumber();
        }
    }
    
    protected function getMasterManufacturerPartNumber(){
        $aData=$this->oPrepare->get('applydata');
        return (
                isset($aData['ManufacturerPartNumber'])
//                && 
//                count($this->aVariants) == 1 
                )
        ?$aData['ManufacturerPartNumber']:$this->oCurrentProduct->getModulField('manufacturerpartnumber');
    }
    protected function setPrepareType($sPrepareType){
        if($this->sPrepareType==''){
            $this->sPrepareType=$sPrepareType;
        }elseif($this->sPrepareType!=$sPrepareType){
            throw new Exception ('mixed preparetypes: '.$sPrepareType.'!='.$this->sPrepareType);
        }
        return $this;
    }
    public function getPrepareType(){
        $this->getData();
        return $this->sPrepareType;
    }
    protected function getSku(){
        return $this->oCurrentProduct->getMarketPlaceSku();
    }
    protected function getPrice(){
        if(isset($this->aSelectionData['price'])){
            return $this->aSelectionData['price'];
        }elseif($this->oPrepare->get('price')!==null){
            return $this->oPrepare->get('price');
        }else{
            return $this->oCurrentProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject());
        }
    }
    protected function getCurrency(){
        return $this->oMarketplace->getConfig('currency');
    }
    protected function getQuantity(){
        if(isset($this->aSelectionData['stock'])){
            return $this->aSelectionData['stock'];
        }elseif($this->oPrepare->get('quantity')!==null){
            return $this->oPrepare->get('quantity');
        }else{
            $aStockConf=  MLModul::gi()->getStockConfig();
            return $this->oCurrentProduct->getSuggestedMarketplaceStock($aStockConf['type'],$aStockConf['value']);
        }
    }
    protected function getEan(){
        return $this->oCurrentProduct->getModulField('general.ean', true);
    }
    protected function getVariation() {
        $aVariants = array();
        foreach ($this->oCurrentProduct->getVariatonData() as $aVariant) {
            $aVariants[] = array('Name' => $aVariant['name'],
                'Value' => $aVariant['value']);
        }
        return $aVariants;
    }
    
    
    protected function getMasterImages(){
        $aData=$this->oPrepare->get('applydata');
        $aImages = isset($aData['Images'])?$aData['Images']:array();
        $aOut=array();
        $iSize = $this->getImageSize();
        foreach($aImages as $sImage=>$blUpload){
            if($blUpload){
                try {
                    $aImage = MLImage::gi()->resizeImage($sImage, 'products', $iSize, $iSize);
                    $aOut[] = $aImage['url'];
                } catch (Exception $oExc) {
                    MLMessage::gi()->addDebug($oExc);
                }
            }
        }
        return $aOut;
    }
    
    protected function getImages() {
        $aOut = array();        
        $iSize = $this->getImageSize();
        $aMasterImages = $this->getMasterImages();
        foreach ($this->oCurrentProduct->getImages() as $sImage) {
            try {
                $aImage = MLImage::gi()->resizeImage($sImage, 'products', $iSize, $iSize);
                if(in_array($aImage['url'], $aMasterImages)){
                    $aOut[] = $aImage['url'];
                }
            } catch (Exception $oExc) {
                MLMessage::gi()->addDebug($oExc);
            }
        }
        return $aOut;
    }

    protected function getShippingTime() {
        if (isset($this->aSelectionData['shippingtime'])) {
            $mShippingTime = $this->aSelectionData['shippingtime'];
        } elseif ($this->oPrepare->get('shippingtime') !== null) {
            $mShippingTime = $this->oPrepare->get('shippingtime');
        } else {
            $mShippingTime = $this->oMarketplace->getConfig('leadtimetoship');
        }

        if ($mShippingTime == 0) {
            $mShippingTime = null;
        }

        return $mShippingTime;
    }

    protected function getAsin(){
        return $this->oPrepare->get('aidentid');
    }
    protected function getConditionType(){
        if($this->oPrepare->get('conditiontype')!=''){
            return $this->oPrepare->get('conditiontype');
        }else{
            return $this->oMarketplace->getConfig('itemcondition');
        }
    }
    protected function getWillShipInternationally(){
        if($this->oPrepare->get('shipping')!=''){
            return $this->oPrepare->get('shipping');
        }else{
            return $this->oMarketplace->getConfig('internationalshipping');
        }
    }
    protected function getConditionNote(){
        if($this->oPrepare->get('conditionnote')!=''){
            return $this->oPrepare->get('conditionnote');
        }else{
            return '';
        }
    }
    
    
    protected function getId(){
        return $this->oCurrentProduct->get('id');
    }
}
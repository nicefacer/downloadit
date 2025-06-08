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
 * (c) 2010 - 2015 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
class ML_Shopware_Helper_Model_ProductList_List{
    protected $oI18n=null;
     /** @var ML_Database_Model_Query_Select $oSelect   */
    protected $oSelect = null ;
    protected $aFields=array();
    protected $aHeader=array();
    protected $aList=array();
    
    protected $oLoadedProduct=null;
//    protected $oProduct=null;
    protected $aAttributes = array();
    
    protected $aMixedData=array();
    public function __construct() {
        $this->oI18n=  MLI18n::gi();
        $this->aAttributes=array(
        'categories'=> $this->oI18n->get('Shopware_Productlist_Filter_sCategory'),
        'ean'=> $this->oI18n->get('Shopware_Productlist_Filter_sEan'),
        'suppliernumber'=>$this->oI18n->get('Shopware_Productlist_Filter_sManufacturerId')
    );
        
    }
    public function clear(){
        $oRef=new ReflectionClass($this);
        foreach($oRef->getDefaultProperties() as $sKey=>$mValue){
            $this->$sKey=$mValue;
        }
        $this->__construct();
        return $this;
    }
    public function setCollection($oSelect){
        $this->oSelect=$oSelect;
        return $this;
    }
    public function getLoadedList() {
         if ( count($this->aList) <= 0 ) {
             $aIDs = array () ;
             $aPrducts = $this->oSelect->getResult() ;
             foreach ( $aPrducts as $aProduct ) {
                 $aIDs[] = $aProduct['id'] ;
             }
             return $aIDs ;
         } else {
             return array_keys($this->aList) ;
         }
     }
    public function getList(){
        $aProducts = $this->oSelect->getResult() ;
        foreach($aProducts as $aProduct){
           $oProduct = Shopware()->Models()->getRepository('Shopware\Models\Article\Article')->find((int) $aProduct['id']);
            
            $oMLProduct=  MLProduct::factory();
            /* @var $oMLProduct ML_Shopware_Model_Product */
            $oMLProduct->loadByShopProduct($oProduct);
            $this->oLoadedProduct=$oMLProduct;
            foreach($this->aFields as $sField){
                $mReturn=
                    method_exists($this, $sField)
                    ?$this->{$sField}()
                    :$this->ShopwareAttribute($sField)
                ;
                    if($mReturn!==null){
                        $this->aMixedData[$oMLProduct->get("id")][$sField]=$mReturn;
                    }
            }
            $this->aList[$oProduct->getId()]=$oMLProduct;
        }
        return $this->aList;
    }
    public function getMixedData($oProduct, $sKey){
        $this->oLoadedProduct=$oProduct;
        return $this->ShopwareAttribute($sKey);
    }

    public function getHeader(){
        return $this->aHeader;
    }
    
    public function priceShop($sTypeVariant=null){
        if($this->oLoadedProduct===null){
            if(!in_array(__function__, $this->aFields)){
                $this->aFields[]=__function__;
                $this->aHeader[__function__]=array(
                    'title'=>$this->oI18n->get('Productlist_Header_sPriceShop'),
                    'order'=>'price',
                    'type'=>'priceShop',
                    'type_variant'=>$sTypeVariant===null?'priceShop':$sTypeVariant
                );
            }
            return $this;
        }
    }
    public function priceMarketplace($sTypeVariant=null){
        if($this->oLoadedProduct===null){
            if(!in_array(__function__, $this->aFields)){
                $this->aFields[]=__function__;
                $this->aHeader[__function__]=array(
                    'title'=>sprintf($this->oI18n->get('Productlist_Header_sPriceMarketplace'), MLModul::gi()->getMarketPlaceName(false)),
                    'type'=>'priceMarketplace',
                    'type_variant'=>$sTypeVariant===null?'priceMarketplace':$sTypeVariant
                );
            }
            return $this;
        }
    }
    public function image($sTypeVariant=null){
        if($this->oLoadedProduct===null){
            if(!in_array(__function__, $this->aFields)){
                $this->aFields[]=__function__;
                $this->aHeader[__function__]=array(
                    'title'=>$this->oI18n->get('Productlist_Header_sImage'),
                    'type'=>'image',
                    'type_variant'=>$sTypeVariant===null?'image':$sTypeVariant
                );
            }
            return $this;
        }
    }
    public function ShopwareAttribute($sCode, $blUse = true, $sTitle = null, $sTypeVariant = null) {
        if ($this->oLoadedProduct === null) {
            if (!in_array($sCode, $this->aFields)) {
                $aArticle = Shopware()->Models()->getClassMetadata('Shopware\Models\Article\Article')->columnNames;
                $aDetail = Shopware()->Models()->getClassMetadata('Shopware\Models\Article\Detail')->columnNames;
                $aFieldList = array_merge($aArticle ,$aDetail);
                $sFieldOrder = isset($aFieldList[$sCode])?$aFieldList[$sCode]:$sCode;
                if(array_key_exists($sCode, $aArticle) &&array_key_exists($sCode, $aDetail)  ){
                    $sFieldOrder = 'details.'.$sFieldOrder;
                } 
                if ($blUse ) {
                    $this->aFields[] = '_' . $sCode;
                    $this->aHeader['_' . $sCode] = array(
                        'title' => $sTitle === null ? (isset($this->aAttributes[$sCode])?$this->aAttributes[$sCode]:ucfirst($sCode)) : $sTitle,
                        'order' => $sFieldOrder,
                        'type' => 'simpleText',
                        'type_variant' => $sTypeVariant === null ? 'simpleText' : $sTypeVariant
                    );
                }
            }
            return $this;
        } else {
            $sCode = substr($sCode, 1);
            return $this->oLoadedProduct->getProductField($sCode);
        }
    }
    public function product($sTypeVariant=null){
        if($this->oLoadedProduct===null){
            if(!in_array(__function__, $this->aFields)){
                $this->aFields[]=__function__;
                $this->aHeader[__function__]=array(
                    'title'=>$this->oI18n->get('Productlist_Header_sProduct'),
                    'order'=>'name',
                    'type'=>'product',
                    'type_variant'=>$sTypeVariant===null?'product':$sTypeVariant
                );
            }
            return $this;
        }
    }

    
    public function preparedStatus($sTypeVariant=null){
        if($this->oLoadedProduct===null){
            $this->aFields[]=__function__;
            $this->aHeader[__function__]=array(
                'title'=>$this->oI18n->get('Productlist_Header_sPreparedStatus'),
                'type'=>'preparedStatus',
                'type_variant'=>$sTypeVariant===null?'preparedStatus':$sTypeVariant
            );
            return $this;
        }
    }    
    public function preparedType($sTypeVariant=null){
        if($this->oLoadedProduct===null){
            $sTitle=$this->oI18n->get(ucfirst(MLModul::gi()->getMarketPlaceName()).'_Productlist_Header_sPreparedType');
            if($sTitle==ucfirst(MLModul::gi()->getMarketPlaceName()).'_Productlist_Header_sPreparedType'){
                $sTitle=$this->oI18n->get('Productlist_Header_sPreparedType');
            }
            $this->aFields[]=__function__;
            $this->aHeader[__function__]=array(
                'title'=>$sTitle,
                'type'=>'preparedType',
                'type_variant'=>$sTypeVariant===null?'preparedType':$sTypeVariant
            );
            return $this;
        }
    }
    public function addMLField($aHead){
        if($this->oLoadedProduct===null){
            $this->aHeader[]=$aHead;
            return $this;
        }
    }
}

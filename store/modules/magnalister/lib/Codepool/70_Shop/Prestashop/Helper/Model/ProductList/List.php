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
 class ML_Prestashop_Helper_Model_ProductList_List {

     protected $oI18n = null ;
     protected $aProducts = null ;

     /** @var ML_Database_Model_Query_Select $oSelect   */
     protected $oSelect = null ;
     protected $aFields = array () ;
     protected $aHeader = array () ;
     protected $aMagnalisterSelection = array () ;
     protected $sMagnalisterSelection = '' ;
     protected $aList = array () ;

     /** @var ML_Prestashop_Model_Shop_Product   */
     protected $oLoadedProduct = null ;

     /** @var Product $oProduct */
//     protected $oProduct = null ;
     protected $aProduct = null ;

     //protected $aAttributes = null ;

     public function __construct() {
         $this->oI18n = MLI18n::gi() ;
         //$this->aAttributes = $this->aProducts ;
     }

     /**
      * clear all property value of this class and refresh the class
      * @return \ML_Prestashop_Helper_Shop_ProductList_List
      */
     public function clear() {
         $oRef = new ReflectionClass($this) ;
         foreach ( $oRef->getDefaultProperties() as $sKey => $mValue ) {
             $this->$sKey = $mValue ;
         }
         $this->__construct() ;
         return $this ;
     }

     public function setCollection(ML_Database_Model_Query_Select $oSelect) {
         $this->oSelect = $oSelect ;
         return $this ;
     }

     public function getLoadedList() {
         if ( count($this->aList) <= 0 ) {
             $aIDs = array () ;
             $aPrducts = $this->oSelect->getResult() ;
             foreach ( $aPrducts as $aProduct ) {
                 $aIDs[] = $aProduct['id_product'] ;
             }
             return $aIDs ;
         } else {
             return array_keys($this->aList) ;
         }
     }

     public function getList() {
         $aProducts = $this->oSelect->getResult() ;// echo "<br>$this->oSelect<br>";
         foreach ( $aProducts as $aProduct ) {
             $oProduct = new Product($aProduct['id_product'] , true) ;
             //$this->oProduct = $oProduct ;

             /* @var $oMLProduct ML_Shop_Model_Product_Abstract */
             $oMLProduct = MLProduct::factory() ;
             $oMLProduct->loadByShopProduct($oProduct) ;


             $this->oLoadedProduct = $oMLProduct ;
             foreach ( $this->aFields as $sField ) {

                 $mReturn = method_exists($this , $sField)?$this->{$sField}():(isset($oProduct->$sField)?$oProduct->$sField:"my error : this field not set in product") ;
                 if ( $mReturn !== null ) {
                     $this->aMixedData[$oMLProduct->get("id")][$sField] = $mReturn ;
                 }
             }
             $this->aList[$oProduct->id] = $oMLProduct ;
         }
         return $this->aList ;
     }

     public function getMixedData($oProduct , $sKey) {
         $this->oLoadedProduct=$oProduct;
         if ( method_exists($this , $sKey) ) {
             return $this->{$sKey}() ;
         } else {
             throw new Exception("In oList there is not this function '$sKey()' in file list.php" , "12121212") ;
         }
     }

     public function prestashopAttribute($sCode , $blUse = true , $sTitle = null) {
         if ( $this->oLoadedProduct === null ) {
             if ( !in_array($sCode , $this->aFields) ) {
                 if ( $blUse && array_key_exists($sCode , $this->aAttributes) ) {
                     $this->aFields[] = '_' . $sCode ;
                     $this->aHeader['_' . $sCode] = array (
                         'title' => $sTitle === null?$this->aAttributes[$sCode]->frontendLabel:$sTitle ,
                         'order' => $sCode ,
                         'type' => 'simpleText'
                     ) ;
                 }
             }
             return $this ;
         } else {
             $sCode = substr($sCode , 1) ;
             return $this->oLoadedProduct->$sCode ;
         }
     }

     public function setMagnalisterSelection($sSelectionName) {
         $this->sMagnalisterSelection = $sSelectionName ;
         return $this ;
     }

     public function getHeader() {
         return $this->aHeader ;
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
    
    public function priceMarketplace($sTypeVariant=null) {
        if ($this->oLoadedProduct === null) {
            if (!in_array(__function__, $this->aFields)) {
                $this->aFields[] = __function__;
                $this->aHeader[__function__] = array(
                    'title' => sprintf($this->oI18n->get('Productlist_Header_sPriceMarketplace'), MLModul::gi()->getMarketPlaceName(false)),
                    'type' => 'priceMarketplace',
                    'type_variant' => ($sTypeVariant === null) ? 'priceMarketplace' : $sTypeVariant
                );
            }
            return $this;
        }
    }

     public function image($sTypeVariant=null){
         if ( $this->oLoadedProduct === null ) {
             if ( !in_array(__function__ , $this->aFields) ) {
                 $this->aFields[] = __function__ ;
                 $this->aHeader[__function__] = array (
                     'title' => $this->oI18n->get('Productlist_Header_sImage') ,
                     'type' => 'image',
                    'type_variant'=>$sTypeVariant===null?'image':$sTypeVariant
                         ) ;
             }
             return $this ;
         } else {
             return array () ;
         }
     }

     public function ean13($blUse = true) {
         if ( $this->oLoadedProduct === null ) {
             if ( !in_array(__function__ , $this->aFields) ) {
                 if ( $blUse ) {
                     $this->aFields[] = __function__ ;
                     $this->aHeader[__function__] = array (
                         'title' => 'ean13' ,
                         'order' => 'ean13' ,
                         'type' => 'simpleText'
                             ) ;
                 }
             }
             return $this ;
         } else {
             return $this->oLoadedProduct->ean13;
         }
     }

     public function manufacturer($blUse = true) {
         if ( $this->oLoadedProduct === null ) {
             if ( !in_array(__function__ , $this->aFields) ) {
                 if ( $blUse ) {
                     $this->aFields[] = __function__ ;
                     $this->aHeader[__function__] = array (
                         'title' => $this->oI18n->get('Prestashop_Productlist_Header_sManufacturer') ,
                         'order' => 'manufacturer' ,
                         'type' => 'simpleText'
                             ) ;
                 }
             }
             return $this ;
         } else {
             return $this->oLoadedProduct->manufacturer_name ;
         }
     }

     public function supplier($blUse = true) {
         if ( $this->oLoadedProduct === null ) {
             if ( !in_array(__function__ , $this->aFields) ) {
                 if ( $blUse ) {
                     $this->aFields[] = __function__ ;
                     $this->aHeader[__function__] = array (
                         'title' => $this->oI18n->get('Prestashop_Productlist_Header_sSupplier') ,
                         'order' => 'supplier' ,
                         'type' => 'simpleText'
                             ) ;
                 }
             }
             return $this ;
         } else {
             return array ('simpleText' => $this->oLoadedProduct->supplier_name) ;
         }
     }

     public function product($sTypeVariant=null){
         if ( $this->oLoadedProduct === null ) {
             if ( !in_array(__function__ , $this->aFields) ) {
                 $this->aFields[] = __function__ ;
                 $this->aHeader[__function__] = array (
                     'title' => $this->oI18n->get('Productlist_Header_sProduct') ,
                     'order' => 'name' ,
                     'type' => 'product',
                    'type_variant'=>$sTypeVariant===null?'product':$sTypeVariant
                         ) ;
             }
             return $this ;
         }
     }

     public function preparedStatus($sTypeVariant=null){
         if ( $this->oLoadedProduct === null ) {
             $this->aFields[] = __function__ ;
             $this->aHeader[__function__] = array (
                 'title' => $this->oI18n->get('Productlist_Header_sPreparedStatus') ,
                 'type' => 'preparedStatus',
                'type_variant'=>$sTypeVariant===null?'preparedStatus':$sTypeVariant
                     ) ;
             return $this ;
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

 
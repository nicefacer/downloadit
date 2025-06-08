<?php

 class ML_Prestashop_Helper_Model_ProductList_Filter {

     protected $sPrefix = '' ;
     protected $iPage=0;
     protected $iOffset=0;
     protected $aOrder = array ('name' => 'p.`id_product`' , 'direction' => 'DESC') ;

     /**
      *
      * @var ML_Database_Model_Query_Select $oSelect
      */
     protected $oSelect = null ;
     protected $oI18n = null ;
     protected $aFilterInput = array () ;
     protected $aFilterOutput = array () ;

     public function __construct() {
         $this->oI18n = MLI18n::gi() ;
     }

     public function clear() {
         $oRef = new ReflectionClass($this) ;
         foreach ( $oRef->getDefaultProperties() as $sKey => $mValue ) {
             $this->$sKey = $mValue ;
         }
         $this->__construct() ;
         return $this ;
     }

     public function setCollection($oSelect) {
         $this->oSelect = $oSelect ;
         return $this ;
     }

     public function setFilter($aFilterInput) {
         $this->aFilterInput = $aFilterInput ;
         return $this ;
     }

    public function setOffset($iOffset){
        $this->iOffset=(int)$iOffset;
        return $this;
    }
     public function setPage($iPage) {
         $this->iPage = (int)$iPage ;
         return $this ;
     }

     public function setOrder($sOrder) {
         $aOrder = explode('_' , $sOrder) ;
         if ( count($aOrder) == 2 && $aOrder[0] != '' && $aOrder[1] != '' ) {
             if ($aOrder[0] == 'price') {
                 $aOrder[0] = "p.price";
             } elseif ($aOrder[0] == 'manufacturer') {
                 $aOrder[0] = 'm.name';
                 $this->oSelect->join(array(_DB_PREFIX_ . 'manufacturer' , 'm' , '(m.`id_manufacturer` = p.`id_manufacturer`)'), ML_Database_Model_Query_Select::JOIN_TYPE_LEFT);
             }
             $this->aOrder = array ('name' => $aOrder[0] , 'direction' => $aOrder[1]) ;
             $this->oSelect->orderBy("{$aOrder[0]} {$aOrder[1]}") ;
         }
     }

     public function setPrefix($sPrefix) {
         $this->sPrefix = $sPrefix ;
         return $this ;
     }

     public function getOutput() {
         return $this->aFilterOutput ;
     }

     public function getStatistic() {
         $iCountTotal = (int)$this->oSelect->getCount() ;
         $iCountPerPage = isset($this->aFilterOutput[$this->sPrefix . 'limit']['value'])?$this->aFilterOutput[$this->sPrefix . 'limit']['value']:$iCountTotal ;
         return array (
             'iCountPerPage' => $iCountPerPage ,
             'iCurrentPage' => $this->iPage ,
             'iCountTotal' => $iCountTotal ,
             'aOrder' => array ('name' => $this->aOrder['name'] , 'direction' => $this->aOrder['direction']) ,
                 ) ;
     }

     protected function getDefaultValue($sName , $aPossibleValues) {
         $sValue = isset($this->aFilterInput[$sName])?$this->aFilterInput[$sName]:'' ;
         return array_key_exists($sValue , $aPossibleValues)?$sValue:key($aPossibleValues) ;
     }

     public function limit() {
         $sName = $this->sPrefix . __function__ ;
         if ( !isset($this->aFilterOutput[$sName]) ) {
             $oI18n = $this->oI18n ;
             $aValues = array () ;
             foreach ( array (5 , 10 , 25 , 50 , 75 , 100) as $iKey ) {
                 $aValues[$iKey] = array (
                     'value' => (string)$iKey ,
                     'label' => sprintf($oI18n->get('Productlist_Filter_sLimit') , (string)$iKey)
                         ) ;
             }
             $iValue = (int)$this->getDefaultValue($sName , $aValues) ;
            if($this->iPage==0){
                $iOffset=$this->iOffset;
            }else{
                $iOffset=$this->iPage*$iValue;
            }
             $this->oSelect->limit($iOffset,$iValue  ) ;
             $this->aFilterOutput[$sName] = array (
                 'name' => $sName ,
                 'type' => 'select' ,
                 'value' => $iValue ,
                 'values' => $aValues
                     ) ;
         }
         return $this ;
     }
    
    /**
     * adds a ML_Productlist_Model_ProductListDependency_Abstract instance to filter
     * @param string $sDependency ident-name of dependency
     * @param array $aDependecyConfig config for dependency
     * @return \ML_Magento_Helper_Model_ProductList_Filter
     */
    public function registerDependency ($sDependency, $aDependecyConfig = array()) {
        $oDependency = MLProductList::dependencyInstance($sDependency)->setConfig($aDependecyConfig);
        $sName = $this->sPrefix.$sDependency;
        if (!isset($this->aFilterOutput[$sName])) {
            $oDependency
                ->setFilterValue(isset($this->aFilterInput[$sName]) ? $this->aFilterInput[$sName] : null)
                ->manipulateQuery($this->oSelect)
            ;
            $this->aFilterOutput[$sName] = $oDependency;
            $aIdentFilter = $oDependency->getMasterIdents();
            if ($aIdentFilter['in'] !== null) {
                $sField = MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.keytype')->get('value') == 'pID' ? ML_Prestashop_Model_Product::ID_FIELD_NAME  : ML_Prestashop_Model_Product::SKU_FIELD_NAME;
                $this->oSelect->where($sField." in(N'".implode("', N'", array_unique(MLDatabase::getDbInstance()->escape($aIdentFilter['in'])))."')");
            }
            if ($aIdentFilter['notIn'] !== null) {
                $sField = MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.keytype')->get('value') == 'pID' ? ML_Prestashop_Model_Product::ID_FIELD_NAME  : ML_Prestashop_Model_Product::SKU_FIELD_NAME;
                $this->oSelect->where($sField." not in(N'".implode("', N'", array_unique(MLDatabase::getDbInstance()->escape($aIdentFilter['notIn'])))."')");
            }
        }
        return $this;
    }
    
    public function variantInList(ML_Shop_Model_Product_Abstract $oProduct) {
        foreach ($this->aFilterOutput as $oDependency) {
            if (is_object($oDependency) && !$oDependency->variantIsActive($oProduct)) {
                return false;
            }
        }
        return true;
    }

 }
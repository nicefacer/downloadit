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
MLFilesystem::gi()->loadClass('Database_Model_Table_Prepare_Abstract');
class ML_Dawanda_Model_Table_Dawanda_Prepare extends ML_Database_Model_Table_Prepare_Abstract {

    protected $sTableName = 'magnalister_dawanda_prepare';
    protected $aFields = array ( 
        'mpID' => array (
            'isKey' => true,
            'Type' => 'int(11) unsigned', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'products_id' => array (
            'isKey' => true,
            'Type' => 'int(11)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'PreparedTS'   => array (
            'isInsertCurrentTime' => true,
            'Type' => 'datetime', 'Null' => 'NO', 'Default' => '0000-00-00 00:00:00', 'Extra' => '', 'Comment'=>''
        ), 
        'ShippingService' => array (
            'Type' => 'int(16)', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'PrimaryCategory' => array (
            'Type' => 'text', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ),
        'TopPrimaryCategory' => array (
            'Type' => 'int(16)', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'SecondaryCategory' => array (
            'Type' => 'text', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>'' // dawanda currently only supports one marketplace category.
        ),
        'TopSecondaryCategory' => array (
            'Type' => 'int(16)', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'StoreCategory' => array (
            'Type' => 'text', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'TopStoreCategory' => array (
            'Type' => 'int(16)', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'ListingDuration' => array (
            'Type' => 'tinyint(4)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ),  
        'ProductType' => array (
            'Type' => 'int(11)', 'Null' => 'NO', 'Default' => '0', 'Extra' => '', 'Comment'=>''
        ),  
        'ReturnPolicy' => array (
            'Type' => 'int(11)', 'Null' => 'NO', 'Default' => '0', 'Extra' => '', 'Comment'=>''
        ), 
        'MpColors' => array (
            'Type' => 'text', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'Attributes' => array ( 
            'Type' => 'longtext', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'Verified' => array (
            'Type' => "enum('OK','ERROR','OPEN','EMPTY')", 'Null' => 'NO', 'Default' => 'OPEN', 'Extra' => '', 'Comment'=>''
        ), 
        'products_sku'  => array (
            'Type' => 'varchar(64)', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>'is not used in tables connection , but when magnalister_products become empty and ids are changed we can by this field recover products thoses are already prepared '
        ), 
    );
    protected $aTableKeys=array(
        'UniqueEntry' => array('Non_unique' => '0', 'Column_name' => 'mpID, products_id'),
    );
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDefaultValues() {
        try {
            $sId = MLRequest::gi()->get('mp');
            if (is_numeric($sId)) {
                $this->set('mpid', $sId);
            }
        } catch (Exception $oEx) {
            
        }
        return $this;
    }
    
    public function set($sName, $mValue) {
        $sName= strtolower($sName);
        if(
            $mValue !== NULL
            && in_array($sName, array('primarycategory', 'secondarycategory', 'storecategory'))
        ){
            $this->set('top'.$sName, $mValue);
        }
        return parent::set($sName, $mValue);
    }
    
    protected function getTopCategories ($sClass, $sField, $sWhere) {
        $aCats = array();
        $oCat = MLDatabase::factory($sClass);
        foreach (MLDatabase::getDbInstance()->fetchArray("
            SELECT ".$sField."
            FROM ".$this->sTableName." prepare
            INNER JOIN ".MLDatabase::factory('product')->getTableName()." product on product.id = prepare.products_id
            INNER JOIN ".$oCat->getTableName()." cat on cat.categoryid = ".$sField." AND cat.".$sWhere."
            WHERE prepare.mpid = ".MLModul::gi()->getMarketPlaceId()."
            GROUP BY ".$sField."
            ORDER BY count(".$sField.")/count(product.parentid)+count(distinct product.parentid)-1 desc
            LIMIT 10
        ", true) as $iCatId){
            $oCat->init(true)->set('categoryid', $iCatId);
            $sCat = '';
            foreach($oCat->getCategoryPath() as $oParentCat) {
                $sCat = $oParentCat->get('categoryname').' &gt; '.$sCat;
            }
            $aCats[$iCatId] = substr($sCat, 0, -6);
        }
        return $aCats;
    }
    
    protected function getTopMarketplaceCategories ($sClass, $sField) {
        $aField = array();
        MLHelper::gi('model_table_dawanda_configdata')->langsField($aField);
        foreach ($aField['valuessrc'] as $sMainLang => $aLang) {
            if ($aLang['required']) {
                break;
            }
            $sMainLang = null;
        }
        return $this->getTopCategories($sClass, $sField, "language = '".$sMainLang."'");
    }
    public function getTopPrimaryCategories () {
        return $this->getTopMarketplaceCategories('dawanda_categoriesmarketplace', 'topprimarycategory');
    }
    
    public function getTopSecondaryCategories () {
        return $this->getTopMarketplaceCategories('dawanda_categoriesmarketplace', 'topsecondarycategory');
    }
    
    public function getTopStoreCategories () {
        return $this->getTopCategories('dawanda_categoriesstore', 'topstorecategory', "mpid = '".MLModul::gi()->getMarketPlaceId()."'");
    }
    
}
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
class ML_Bepado_Model_Table_Bepado_Prepare extends ML_Database_Model_Table_Prepare_Abstract {

    protected $sTableName = 'magnalister_bepado_prepare';
    
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
        'Verified' => array (//todo
            'Type' => "enum('OK','ERROR','OPEN','EMPTY')", 'Null' => 'NO', 'Default' => 'OPEN', 'Extra' => '', 'Comment'=>''
        ), 
        'B2b_Price_Active' => array(
            'Type' => 'int(1)', 'Null' => 'NO', 'Default' => 0, 'Extra' => '', 'Comment'=>''
        ),
        'Category' => array(
            'Type' => 'text', 'Null' => 'NO', 'Default' => '', 'Extra' => '', 'Comment'=>''
        ),
        'TopCategory' => array (
            'Type' => 'text', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ),
        'ShippingTime' => array(
            'Type' => 'int(2)', 'Null' => 'NO', 'Default' => 1, 'Extra' => '', 'Comment'=>''
        ),
        'ShippingCountry' => array (
            'Type' => 'text', 'Null' => 'NO', 'Default' => '', 'Extra' => '', 'Comment'=>''
        ),
        'ShippingService' => array (
            'Type' => 'text', 'Null' => 'NO', 'Default' => '', 'Extra' => '', 'Comment'=>''
        ),
        'ShippingCost' => array (
            'Type' => 'text', 'Null' => 'NO', 'Default' => '', 'Extra' => '', 'Comment'=>''
        ),
    );
    protected $aTableKeys=array(
        'UniqueEntry' => array('Non_unique' => '0', 'Column_name' => 'mpID, products_id'),
    );
    
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
        $sName = strtolower($sName);
        if(
            $mValue !== NULL
            && in_array($sName, array('category'))
        ){
            $this->set('top'.$sName, $mValue);
        }
        return parent::set($sName, $mValue);
    }
    
    public function getTopCategories () {
        $sClass = 'bepado_categoriesmarketplace';
        $sField = 'topcategory';
        $sWhere = "language='de'";//todo
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
    
}
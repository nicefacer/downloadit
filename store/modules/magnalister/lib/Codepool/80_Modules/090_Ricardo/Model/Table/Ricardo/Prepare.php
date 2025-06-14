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

class ML_Ricardo_Model_Table_Ricardo_Prepare extends ML_Database_Model_Table_Prepare_Abstract {

    protected $sTableName = 'magnalister_ricardo_prepare';
    protected $aFields = array(
        'mpID' => array(
            'isKey' => true,
            'Type' => 'int(11) unsigned', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'products_id' => array(
            'isKey' => true, 
            'Type' => 'int(11)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'PrimaryCategory' => array(
            'Type' => 'varchar(15)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'TopPrimaryCategory' => array(
            'Type' => 'varchar(15)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'ListingLangs' => array(
            'Type' => 'text', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'DescriptionTemplate' => array(
            'Type' => 'tinyint', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'ArticleCondition' => array(
            'Type' => 'tinyint', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'DeTitle' => array(
            'Type' => 'varchar(40)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'FrTitle' => array(
            'Type' => 'varchar(40)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'DeSubtitle' => array(
            'Type' => 'varchar(60)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'FrSubtitle' => array(
            'Type' => 'varchar(60)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'DeDescription' => array(
            'Type' => 'text', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'FrDescription' => array(
            'Type' => 'text', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'Images' => array(
            'Type' => 'text', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'WarrantyCondition' => array(
            'Type' => 'tinyint', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'WarrantyDescription' => array(
            'Type' => 'text', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'BuyingMode' => array(
            'Type' => 'varchar(20)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'FixPrice' => array(
            'Type' => 'decimal(15,2)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'EnableBuyNowPrice' => array(
            'Type' => 'varchar(3)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'PriceForAuction' => array(
            'Type' => 'decimal(15,2)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'PriceIncrement' => array(
            'Type' => 'decimal(15,2)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'StartDate' => array(
            'Type' => 'datetime', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'EndDate' => array(
            'Type' => 'time', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'Duration' => array(
            'Type' => 'tinyint', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'MaxRelistCount' => array(
            'Type' => 'int', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'PaymentMethods' => array(
            'Type' => 'varchar(100)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'PaymentDescription' => array(
            'Type' => 'text', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'DeliveryCondition' => array(
            'Type' => 'tinyint', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'DeliveryPackage' => array(
            'Type' => 'tinyint', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'DeliveryDescription' => array(
            'Type' => 'text', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'DeliveryCost' => array(
            'Type' => 'decimal(15,2)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'Cumulative' => array(
            'Type' => "enum('0','1')", 'Null' => 'NO', 'Default' => Null, 'Extra' => '', 'Comment' => ''
        ),
        'Availability' => array(
            'Type' => 'int', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'FirstPromotion' => array (
            'Type' => 'smallint', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'SecondPromotion' => array (
            'Type' => 'tinyint', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'Verified' => array(
            'Type' => "enum('OK','ERROR','OPEN','EMPTY')", 'Null' => 'NO', 'Default' => 'OPEN', 'Extra' => '', 'Comment' => ''
        ),
        'PreparedTS'   => array (
            'isInsertCurrentTime' => true,
            'Type' => 'datetime', 'Null' => 'NO', 'Default' => '0000-00-00 00:00:00', 'Extra' => '', 'Comment' => ''
        ), 
    );

    protected $aTableKeys = array(
        'UniqueEntry' => array('Non_unique' => '0', 'Column_name' => 'mpID, products_id'),
    );

    public function set($sName, $mValue) {
        $sName = strtolower($sName);
        if ($mValue !== NULL && in_array($sName, array('primarycategory')) ) {
            $this->set('top' . $sName, $mValue);
        }

        return parent::set($sName, $mValue);
    }

    public function getTopPrimaryCategories() {
        $aCats = array();
        $sField = 'primarycategory';
        $oCat = MLDatabase::factory('ricardo_categoriesmarketplace');
        $sQuery = "
            SELECT $sField
            FROM " . $this->sTableName . " prepare
            INNER JOIN " . MLDatabase::factory('product')->getTableName() . " product on product.id = prepare.products_id
            INNER JOIN " . $oCat->getTableName() . " cat on cat.categoryid = " . $sField . "
            WHERE prepare.mpid = " . MLModul::gi()->getMarketPlaceId() . "
            GROUP BY $sField
            ORDER BY count($sField)/count(product.parentid)+count(distinct product.parentid)-1 desc
            LIMIT 10
        ";

        foreach (MLDatabase::getDbInstance()->fetchArray($sQuery, true) as $iCatId) {
            $oCat->init(true)->set('categoryid', $iCatId);
            $sCat = '';
            foreach ($oCat->getCategoryPath() as $oParentCat) {
                $sCat = $oParentCat->get('categoryname') . ' &gt; ' . $sCat;
            }

            $aCats[$iCatId] = substr($sCat, 0, -6);
        }

        return $aCats;
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
}

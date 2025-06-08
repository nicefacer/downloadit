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
class ML_Ebay_Model_Table_Ebay_Prepare extends ML_Database_Model_Table_Prepare_Abstract {

    protected $sTableName = 'magnalister_ebay_prepare';
    protected $aFields = array (
        'products_id'  => array (
            'isKey' => true,
            'Type' => 'int(11)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'PreparedTS'   => array (
            'isInsertCurrentTime' => true,
            'Type' => 'datetime', 'Null' => 'NO', 'Default' => '0000-00-00 00:00:00', 'Extra' => '', 'Comment'=>''
        ), 
        'StartTime'    => array (
            'Type' => 'datetime', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'mpID'         => array (
            'isKey' => true,
            'Type' => 'int(11) unsigned', 'Null' => 'NO', 'Default' => '0', 'Extra' => '', 'Comment'=>''
        ), 
        'Title'        => array (
            'Type' => 'varchar(80)', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'Subtitle'     => array (
            'Type' => 'varchar(55)', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'Description'  => array (
            'Type' => 'longtext', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'PictureURL'   => array (
            'Type' => 'text', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'VariationDimensionForPictures'   => array (
            'Type' => 'varchar(64)', 'Null' => 'NO', 'Default' => '', 'Extra' => '', 'Comment'=>''
        ), 
        'VariationPictures'   => array (
            'Type' => 'text', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'GalleryType'  => array (
            'Type' => 'enum(\'Gallery\',\'None\',\'Plus\')', 'Null' => 'NO', 'Default' => 'Gallery', 'Extra' => '', 'Comment'=>''
        ), 
        'ConditionID'  => array (
            'Type' => 'int(4)', 'Null' => 'NO', 'Default' => '0', 'Extra' => '', 'Comment'=>''
        ), 
        'StartPrice'   => array (
            'Type' => 'decimal(15,4)', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'BuyItNowPrice'                 => array (
            'Type' => 'decimal(15,4)', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'currencyID'   => array (
            'Type' => 'varchar(3)', 'Null' => 'NO', 'Default' => 'EUR', 'Extra' => '', 'Comment'=>''
         ), 
        'Site'         => array (
            'Type' => 'varchar(32)', 'Null' => 'NO', 'Default' => 'Germany', 'Extra' => '', 'Comment'=>''
        ), 
        'PrimaryCategory'               => array (
            'Type' => 'int(10)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'PrimaryCategoryName'           => array (
            'Type' => 'varchar(128)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'SecondaryCategory'             => array (
            'Type' => 'int(10)', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'SecondaryCategoryName'         => array (
            'Type' => 'varchar(128)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'StoreCategory'                 => array (
            'Type' => 'bigint(11)', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'StoreCategory2'                => array (
            'Type' => 'bigint(11)', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'PrimaryCategoryAttributes'     => array (
            'Type' => 'text', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'SecondaryCategoryAttributes'   => array (
            'Type' => 'text', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'ListingType'  => array (
            'Type' => 'enum(\'Chinese\',\'FixedPriceItem\',\'StoresFixedPrice\')', 'Null' => 'NO', 'Default' => 'FixedPriceItem', 'Extra' => '', 'Comment'=>''
        ), 
        'ListingDuration'               => array (
            'Type' => 'varchar(10)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'DispatchTimeMax'               => array (//cant make it to int for compatibility with older versions
            'Type' => 'varchar(10)', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'PrivateListing'                => array (
            'Type' => 'enum(\'0\',\'1\')', 'Null' => 'NO', 'Default' => '0', 'Extra' => '', 'Comment'=>''
        ), 
        'BestOfferEnabled'              => array (
            'Type' => 'enum(\'0\',\'1\')', 'Null' => 'NO', 'Default' => '0', 'Extra' => '', 'Comment'=>''
        ), 
        'HitCounter'   => array (
            'Type' => 'enum(\'NoHitCounter\',\'BasicStyle\',\'RetroStyle\',\'HiddenStyle\')', 'Null' => 'NO', 'Default' => 'NoHitCounter', 'Extra' => '', 'Comment'=>''
        ), 
        'PaymentMethods'                => array (
            'Type' => 'longtext', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'ShippingLocal'                 => array (
            'Type' => 'longtext', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'ShippingLocalProfile'          => array (
            'Type' => 'int(12)', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'ShippingLocalDiscount'         => array (
            'Type' => 'tinyint(1)', 'Null' => 'NO', 'Default' => '0', 'Extra' => '', 'Comment'=>''
        ), 
        'ShippingInternational'         => array (
            'Type' => 'longtext', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'ShippingInternationalProfile'  => array (
            'Type' => 'int(12)', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'ShippingInternationalDiscount' => array (
            'Type' => 'tinyint(1)', 'Null' => 'NO', 'Default' => '0', 'Extra' => '', 'Comment'=>''
        ), 
        'Transferred'  => array (
            'Type' => 'tinyint(1)', 'Null' => 'NO', 'Default' => '0', 'Extra' => '', 'Comment'=>''
        ), 
        'Verified'     => array (
            'Type' => 'enum(\'OK\',\'ERROR\',\'OPEN\')', 'Null' => 'NO', 'Default' => 'OPEN', 'Extra' => '', 'Comment'=>''
        ), 
        'Transferred'  => array (
            'Type' => 'tinyint(1)', 'Null' => 'NO', 'Default' => '0', 'Extra' => '', 'Comment'=>''
        ), 
        'deletedBy'    => array (
            'Type' => 'enum(\'\',\'empty\',\'Sync\',\'Button\',\'expired\',\'notML\')', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'topPrimaryCategory'            => array (
            'Type' => 'varchar(64)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'topSecondaryCategory'          => array (
            'Type' => 'varchar(64)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'topStoreCategory'              => array (
            'Type' => 'varchar(64)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'topStoreCategory2'             => array (
            'Type' => 'varchar(64)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'eBayPlus'             => array (
            'Type' => 'enum(\'false\',\'true\')', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        )
    );
    protected $aTableKeys=array(
        'UniqueEntry' => array('Non_unique' => '0', 'Column_name' => 'mpID, products_id'),
    );
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * StartPrice was before possible as "frozen-price". Now StartPrice is null, if not chinese
     * set StartPrice to null, if not chinese
     * @deprecated since 3488
     */
    protected function runOnceSession(){        
         if(MLDatabase::getDbInstance()->tableExists($this->sTableName)){
            MLDatabase::getDbInstance()->update($this->getTableName(), array('StartPrice'=>null), array('ListingType'=>'FixedPriceItem'));
            MLDatabase::getDbInstance()->update($this->getTableName(), array('StartPrice'=>null), array('ListingType'=>'StoresFixedPrice'));
         }
        parent::runOnceSession();
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
    public function save() {
        foreach (array('primarycategory', 'secondarycategory') as $sCatType) {
            if ($this->get($sCatType) !== null) {
                $this->set($sCatType.'name', MLDatabase::factory('ebay_categories')->set('categoryid', $this->get($sCatType))->getCategoryPath(false));
            }
        }
        parent::save();
    }
    public function set($sName, $mValue) {
        $sName = strtolower($sName);
        /*if (in_array($sName, array('primarycategory', 'secondarycategory'))) {
            if ($mValue !== null) {// this code is now during saving - its more performant @see self::save()
                $this->set($sName.'name', MLDatabase::factory('ebay_categories')->set('categoryid', $mValue)->getCategoryPath(false));
            }
        } else*/
        if ($sName == 'starttime' && $mValue !== null) {
            $iTime = strtotime(str_replace('/', '-', $mValue));
            $mValue = empty($iTime) ? null : date('Y-m-d H:i:s', $iTime);
        } elseif (in_array($sName, array('price','buyitnowprice')) && $mValue !== null) {
            $mValue = (float) str_replace(',', '.', $mValue);
        }
        return parent::set($sName, $mValue);
    } 
    
    public function getVariantCount($mProduct) {
        $iMasterProductId = (int)(
            $mProduct instanceof ML_Database_Model_Table_Abstract
            ? $mProduct->get('id')
            : $mProduct
        );
        $sSql = "
            select count(*) 
            from magnalister_products p
            inner join magnalister_ebay_prepare s on p.id=s.products_id
            where p.parentid='" . $iMasterProductId . "'
                and s.mpid='" . MLRequest::gi()->get('mp') . "'
                and s.verified='OK'
        ";
        return MLDatabase::getDbInstance()->fetchOne($sSql);
    }
    
    /**
     * field name for prepared-type if exists
     * @return string|null
     */
    public function getPreparedTypeFieldName () {
        return 'listingtype';
    }
    
}
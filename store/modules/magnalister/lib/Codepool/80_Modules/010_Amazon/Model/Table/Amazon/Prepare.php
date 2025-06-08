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
class ML_Amazon_Model_Table_Amazon_Prepare extends ML_Database_Model_Table_Prepare_Abstract {

    protected $sTableName = 'magnalister_amazon_prepare';
    //protected $aKeys = array('mpid', 'productsid');
    protected $aFields = array(
        'mpID'           => array(
             'isKey' => true,
             'Type' => 'int(8) unsigned',              'Null' => 'NO', 'Default' => NULL,  'Extra' => 'auto_increment','Comment'=>'marketplaceid'),
        'ProductsID'     => array(
             'isKey' => true,
             'Type' => 'int(11)',                      'Null' => 'NO', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'magnalister_products.id'),
        'PreparedTS'   => array (
            'isInsertCurrentTime' => true,
            'Type' => 'datetime', 'Null' => 'NO', 'Default' => '0000-00-00 00:00:00', 'Extra' => '', 'Comment'=>''
        ), 
        'PrepareType'    => array(
             'Type' => "enum('manual','auto','apply')",'Null' => 'NO', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>''),
        'AIdentID'       => array(
             'Type' => 'varchar(16)',                  'Null' => 'NO', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>''),
        'AIdentType'     => array(
             'Type' => "enum('ASIN','EAN')",           'Null' => 'NO', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>''),
        'Price'          => array(
             'Type' => 'decimal(15,2)',                'Null' => 'YES','Default' => NULL,  'Extra' => ''              ,'Comment'=>''),
        'LeadtimeToShip' => array(/** @deprecated now shippingtime*/
             'Type' => 'int(11)',                      'Null' => 'YES','Default' => NULL,  'Extra' => ''              ,'Comment'=>''),
        'ShippingTime' => array(
             'Type' => 'int(11)',                      'Null' => 'YES','Default' => NULL,  'Extra' => ''              ,'Comment'=>''),
        'Quantity'       => array(
             'Type' => 'int(11)',                      'Null' => 'YES','Default' => NULL,  'Extra' => ''              ,'Comment'=>''),
        'LowestPrice'    => array(
             'Type' => 'decimal(15,2)',                'Null' => 'NO', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'lowest price (amazon)'),
        'ConditionType'  => array(
             'Type' => 'varchar(50)',                  'Null' => 'YES', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'item condition'),
        'ConditionNote'  => array(
             'Type' => 'text',                         'Null' => 'YES', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'additional condition info'),
        'Shipping'       => array(
             'Type' => 'varchar(10)',                  'Null' => 'NO', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'old will ship internationally'),
        'MainCategory'   => array(
             'Type' => 'varchar(64)',                  'Null' => 'NO', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply'),
        'ProductType'   => array(
             'Type' => 'varchar(64)',                  'Null' => 'NO', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply'),
        'BrowseNodes'   => array(
             'Type' => 'varchar(64)',                  'Null' => 'NO', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply'),
        'Attributes'   => array(
             'Type' => 'text',                         'Null' => 'NO', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply'),
        'ItemTitle'   => array(
             'Type' => 'varchar(64)',                  'Null' => 'YES', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply'),
        'Manufacturer'   => array(
             'Type' => 'varchar(64)',                  'Null' => 'YES', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply'),
        'Brand'   => array(
             'Type' => 'varchar(64)',                  'Null' => 'YES', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply'),
        'ManufacturerPartNumber'   => array(
             'Type' => 'varchar(64)',                  'Null' => 'YES', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply'),
        'EAN'   => array(
             'Type' => 'varchar(64)',                  'Null' => 'YES', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply'),
        'Images'   => array(
             'Type' => 'text',                  'Null' => 'YES', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply'),
        'BulletPoints'   => array(
             'Type' => 'text',                  'Null' => 'YES', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply'),
        'Description'   => array(
             'Type' => 'text',                  'Null' => 'YES', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply'),
        'Keywords'   => array(
             'Type' => 'text',                  'Null' => 'YES', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply'),
        'TopMainCategory'=> array(
             'Type' => 'varchar(64)',                  'Null' => 'NO', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply, for top-ten-categories'),
        'TopProductType' => array(
             'Type' => 'varchar(64)',                  'Null' => 'NO', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply, for top-ten-categories'),
        'TopBrowseNode1' => array(
             'Type' => 'varchar(64)',                  'Null' => 'NO', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply, for top-ten-categories'),
        'TopBrowseNode2' => array(
             'Type' => 'varchar(64)',                  'Null' => 'NO', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply, for top-ten-categories'),
        'ApplyData'      => array(/** @deprecated */
             'Type' => 'text',                         'Null' => 'NO', 'Default' => NULL,  'Extra' => ''              ,'Comment'=>'only apply'),
        'IsComplete'     => array(
             'Type' => "enum('true','false')",         'Null' => 'NO', 'Default' =>'false','Extra' => ''              ,'Comment'=>'if matching, true'),
    );
    protected $aTableKeys = array(
        'UC_products_id'               => array('Non_unique' => '0', 'Column_name' => 'mpID, ProductsID'),
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
    public function getMissingFields(){
        $aMissing = array();
        if (in_array($this->get('preparetype'), array('auto', 'manual'))) {
            return $aMissing;
        } else {//apply
            $aData = $this->data();
            foreach(array (
                    MLI18n::gi()->get('ML_LABEL_MAINCATEGORY')              => empty($aData['maincategory']) || $aData['maincategory'] == 'null',
                    MLI18n::gi()->get('ML_AMAZON_LABEL_APPLY_BROWSENODES')  => empty($aData['applydata']['BrowseNodes'][0]) || $aData['applydata']['BrowseNodes'][0] == 'null',
                    MLI18n::gi()->get('ML_LABEL_PRODUCT_NAME')              => empty($aData['applydata']['ItemTitle']),
                    MLI18n::gi()->get('ML_GENERIC_MANUFACTURER_NAME')       => empty($aData['applydata']['Manufacturer']),
                    MLI18n::gi()->get('ML_LABEL_EAN')                       => ($aData['aidentid'] != '0'&& empty($aData['aidentid'])) 
            ) as $sMissing=>$blMissing){
                if ($blMissing) {
                    $aMissing[] = $sMissing;
                }
            }
            return $aMissing;
        }
    }
    public function save() {
        $aMissingFields = $this->getMissingFields();
        if (empty($aMissingFields)) {
            $blComplete = true;
        } else {
            $blComplete = false;
            MLMessage::gi()->addDebug($aMissingFields);
        }
        $this->set('iscomplete', $blComplete ? 'true' : 'false');
        return parent::save();
    }
    
    /**
     * get productid by asin or ean
     * 
     * @param type $sIdentValue
     * @param type $sIdentType could be asin or ean
     * @param type $iMpId
     */
    public function getByIdentifier($sIdentValue , $sIdentType , $iMpId = null) {
         $this->aKeys = array ('mpid' , 'aidenttype' , 'aidentid') ;
         if ( $iMpId === null ) {
             $iMpId =  MLModul::gi()->getMarketplaceId() ;
         }
         $this->set('aidenttype' , $sIdentType)
                 ->set('aidentid' , $sIdentValue);
         return $this->get('productsid') ;
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
            inner join magnalister_amazon_prepare s on p.id=s.productsid
            where p.parentid='" . $iMasterProductId . "'
                and s.mpid='" . MLRequest::gi()->get('mp') . "'
                and s.iscomplete='true'
        ";
        return MLDatabase::getDbInstance()->fetchOne($sSql);
    }
    
    public function resetTopTen($sType , $sValue){
        $oQuery = $this->getList()->getQueryObject();
        $oQuery->update($this->sTableName, array($sType=>''))->where("$sType = '$sValue'")->doUpdate();        
    }


    /**
     * field name for join magnalister_product.id
     * @return string
     */
    public function getProductIdFieldName() {
        return 'productsid';
    }
    
    /**
     * field name for prepared-status
     * @return string
     */
    public function getPreparedStatusFieldName() {
        return 'isComplete';
    }
    
    /**
     * field value for successfully prepared item of $this->getPreparedFieldName()
     * @return string
     */
    public function getIsPreparedValue() {
        return 'true';
    }
    
    /**
     * field name for prepared-type if exists
     * @return string|null
     */
    public function getPreparedTypeFieldName () {
        return 'PrepareType';
    }
    
}
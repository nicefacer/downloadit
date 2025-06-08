<?php
class ML_Ebay_Model_Table_Ebay_Errorlog extends ML_Database_Model_Table_Abstract {

    protected $sTableName = 'magnalister_ebay_errorlog';
    protected $aFields = array(
        'Timestamp'       => array(
             'isKey'=>true,
             'Type' => 'int(11)',        'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''),
        'SKU'            => array(
             'isKey'=>true,
             'Type' => 'varchar(64)',    'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''),
        'mpID'           => array(
             'Type' => "int(8) unsigned",'Null' => 'NO', 'Default' => NULL,  'Extra' => '' ,'Comment'=>''),
        'products_id'    => array(
             'Type' => 'int(11)',        'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''),
        'products_model' => array(
             'Type' => "varchar(64)",    'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''),
        'Title' => array(
             'Type' => 'varchar(54)',          'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''),
        'Subtitle'       => array(
             'Type' => 'varchar(54)',    'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''),
        'PictureURL'     => array(
             'Type' => 'varchar(255)',   'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''),
        'ConditionID'    => array(
             'Type' => "int(4)",         'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''),
        'Price'          => array(
             'Type' => 'decimal(15,4)',  'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''),
        'BuyItNowPrice'  => array(
             'Type' => "decimal(15,4)",  'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''),
        'currencyID'     => array(
             'Type' => "enum('AUD','CAD','CHF','CNY','EUR','GBP','HKD','INR','MYR','PHP','PLN','SEK','SGD','TWD','USD')", 'Null' => 'NO', 'Default' => NULL,  'Extra' => '' ,'Comment'=>''),
        'Site'           => array(
             'Type' => "enum('Australia','Austria','Belgium_Dutch','Belgium_French','Canada','CanadaFrench','China','CustomCode','eBayMotors','France','Germany','HongKong','India','Ireland','Italy','Malaysia','Netherlands','Philippines','Poland','Singapore','Spain','Sweden','Switzerland','Taiwan','UK','US')", 'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''),
        'CategoryID'     => array(
             'Type' => 'int(11)',        'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''),
        'CategoryName'   => array(
             'Type' => 'varchar(128)',   'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''),
        'Category2ID'    => array(
             'Type' => 'int(10)',        'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''),
        'Category2Name'  => array(
             'Type' => 'varchar(128)',   'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''),
        'Attributes'     => array(
             'Type' => 'text',           'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''),
        'Quantity'       => array(
             'Type' => 'int(9)',         'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''),
        'ListingType'    => array(
             'Type' => "enum('FixedPrice','Chinese')", 'Null' => 'NO', 'Default' => NULL,'Extra' => '','Comment'=>''),
        'Errors'         => array(
             'Type' => 'text',           'Null' => 'NO', 'Default' => NULL,  'Extra' => '','Comment'=>''), 	
    );
    protected $aTableKeys = array(
        'PRIMARY'               => array('Non_unique' => '0', 'Column_name' => 'Timestamp, SKU')
    );
   
    protected function setDefaultValues() {        
        return $this;
    }
}
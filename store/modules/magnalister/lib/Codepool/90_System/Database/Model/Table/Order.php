<?php

 class ML_Database_Model_Table_Order extends ML_Database_Model_Table_Abstract {

     protected $sTableName = 'magnalister_orders' ;
     //protected $aKeys = array ('orders_id') ;
     protected $aFields = array(
         'orders_id'      =>array(
             'Type' => 'varchar(32)',   'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>'orders-id for sync with magnalister'  ),
         'current_orders_id'      =>array(
             'isKey' => true,
             'Type' => 'varchar(32)',   'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>'orders-id for relation to shop'  ),
         'data'           =>array(
             'Type' => 'text',          'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''  ),
         'orderData'      =>array(
             'Type' => 'text',          'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''  ),
         'internaldata'   =>array(
             'Type' => 'longtext',      'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''  ),
         'special'        =>array(
             'Type' => 'varchar(100)',  'Null' => 'YES','Default' => NULL, 'Extra' => '', 'Comment'=>''  ),
         'platform'       =>array( 
             'Type' => 'varchar(20)',   'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''  ),
         'mpID'           =>array(
             'Type' => 'int(8)',        'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''  ),
         'status'         =>array(
             'Type' => 'varchar(32)',   'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''  ),   
         'insertTime'         =>array(
             'Type' => 'datetime',   'Null' => 'NO', 'Default' => '0000-00-00 00:00:00', 'Extra' => '', 'Comment'=>'',
             'isInsertCurrentTime' => true,
         ),
         'logo'        =>array(
             'Type' => 'varchar(50)',  'Null' => 'YES','Default' => NULL, 'Extra' => '', 'Comment'=>''  ),
     );
     protected $aTableKeys=array(
         'PRIMARY'    => array('Non_unique' => '0', 'Column_name' => 'orders_id'),
         'platform'   => array('Non_unique' => '1', 'Column_name' => 'platform'),
     );
     protected function setDefaultValues() {
         try{
            $oModul=MLModul::gi();
            $this->set('mpid', $oModul->getMarketPlaceId())->set('platform',$oModul->getMarketPlaceName());
         }catch(Exception $oEx){
             
         }
         return $this;
     }     

 }
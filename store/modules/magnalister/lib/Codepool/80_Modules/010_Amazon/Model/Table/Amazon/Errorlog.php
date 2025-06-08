<?php
class ML_Amazon_Model_Table_Amazon_Errorlog extends ML_Database_Model_Table_Abstract {
    protected $sTableName = 'magnalister_amazon_errorlog';
    protected $aFields = array(
        'id'              => array('Type' => 'int(10) unsigned', 'Null' => 'NO', 'Default' => null,  'Extra' => 'auto_increment', 'Comment'=>'', 'isKey' => true),
        'mpID'            => array('Type' => 'int(8) unsigned',  'Null' => 'NO', 'Default' => null,  'Extra' => ''              , 'Comment'=>''),
        'batchid'         => array('Type' => "varchar(50)",      'Null' => 'NO', 'Default' => null,  'Extra' => ''              , 'Comment'=>''),
        'dateadded'       => array('Type' => 'datetime',         'Null' => 'NO', 'Default' => null,  'Extra' => ''              , 'Comment'=>''),
        'errorcode'       => array('Type' => "varchar(30)",      'Null' => 'NO', 'Default' => '',    'Extra' => ''              , 'Comment'=>''),
        'errormessage'    => array('Type' => "text",             'Null' => 'NO', 'Default' => null,  'Extra' => ''              , 'Comment'=>''),
        'additionaldata'  => array('Type' => 'longtext',         'Null' => 'NO', 'Default' => null,  'Extra' => ''              , 'Comment'=>''),
    );
    protected $aTableKeys = array(
        'PRIMARY' => array('Non_unique' => '0', 'Column_name' => 'id'),
        'mpID'    => array('Non_unique' => '1', 'Column_name' => 'mpID'),
    );
   
    protected function setDefaultValues() {
        return $this;
    }
}

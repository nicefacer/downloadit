<?php
 class ML_Database_Model_Table_Config extends ML_Database_Model_Table_Abstract {

     protected static $aCache = array();
     
     protected $sTableName = 'magnalister_config' ;
//     protected $aKeys = array ( 'mpid' , 'mkey') ;
     protected $aFields = array(
         'mpID'      =>array(
             'isKey' => true,
             'Type' => 'int(8) unsigned', 'Null' => 'NO', 'Default' => 0,    'Extra' => '', 'Comment'=>''  ),
         'mkey'      =>array(
             'isKey' => true,
             'Type' => 'varchar(100)',    'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''  ),
         'value'     =>array(
             'Type' => 'longtext',        'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''  ),        
     );
     protected $aTableKeys=array(
         'UniqueKey' => array('Non_unique' => '0', 'Column_name' => 'mpID, mkey'),
     );
     
     protected function setDefaultValues() {
         return $this ;
     }
     
     public function load() {
        if (
            isset($this->aData['mpid']) && isset($this->aData['mkey'])
            && isset(self::$aCache[$this->aData['mpid']][$this->aData['mkey']]) 
            && $this->blLoaded === true
        ) {
            $this->aData['value'] = self::$aCache[$this->aData['mpid']][$this->aData['mkey']];
        } else {
            parent::load();
            if(isset($this->aData['value'])){
                self::$aCache[$this->aData['mpid']][$this->aData['mkey']] = $this->aData['value'];
            }
        }
        return $this;
     }
     
     public function save() {
        if (
            isset($this->aData['mpid']) && isset($this->aData['mkey']) && isset($this->aData['value'])
        ) {
            self::$aCache[$this->aData['mpid']][$this->aData['mkey']] = $this->aData['value'];
        }
        return parent::save();
     }

 }
<?php
class ML_Listings_Model_Table_Listings_Deleted extends ML_Database_Model_Table_Abstract {

    protected $sTableName = 'magnalister_listings_deleted';
    protected $aFields = array (
        'id'         => array (
            'isKey' => true,
            'Type' => 'int(10) unsigned', 'Null' => 'NO', 'Default' => NULL, 'Extra' => 'auto_increment', 'Comment'=>''
        ), 
        'mpID'         => array (
            'isKey' => true,
            'Type' => 'int(11) unsigned', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'productsId'  => array (
            'isKey' => true,
            'Type' => 'int(11)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'productsSku'   => array (
            'Type' => 'varchar(64)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'data'   => array (
            'Type' => 'text', 'Null' => 'YES', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ), 
        'price'    => array (
            'Type' => 'decimal(15,4)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ),
        'timestamp'        => array (
            'Type' => 'datetime', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        )
    );
    protected $aTableKeys=array(
        'PRIMARY' => array('Non_unique' => '0', 'Column_name' => 'id'),
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

}
<?php

/*abstract*/ class ML_Database_Model_Table_Product extends ML_Database_Model_Table_Abstract {

    protected $sTableName = 'magnalister_products';
//    protected $sBackupTableName = 'magnalister_products_history';
//    protected $aKeys = array('id');
    protected $aFields = array(
        'ID'                    => array(
             'isKey' => true,
             'Type' => 'int(11)',    'Null' => 'NO', 'Default' => NULL, 'Extra' => 'auto_increment','Comment'=>''),
        'ParentId'              => array(
             'Type' => 'int(11)',    'Null' => 'NO', 'Default' => NULL, 'Extra' => ''              ,'Comment'=>''),
        'ProductsId'            => array(
             'Type' => 'varchar(64)','Null' => 'NO', 'Default' => NULL, 'Extra' => ''              ,'Comment'=>''),
        'ProductsSku'           => array(
             'Type' => 'varchar(64)','Null' => 'NO', 'Default' => NULL, 'Extra' => ''              ,'Comment'=>''),
        'MarketplaceIdentId'    => array(
             'Type' => 'varchar(64)','Null' => 'NO', 'Default' => NULL, 'Extra' => ''              ,'Comment'=>''),
        'MarketplaceIdentSku'   => array(
             'Type' => 'varchar(64)','Null' => 'NO', 'Default' => NULL, 'Extra' => ''              ,'Comment'=>''),
        'LastUsed'              => array(
             'Type' => 'date',       'Null' => 'NO', 'Default' => NULL, 'Extra' => ''              ,'Comment'=>''),
        'Data'                  => array(
             'Type' => 'text',       'Null' => 'NO', 'Default' => NULL, 'Extra' => ''              ,'Comment'=>''),
        'ShopData'              => array(
             'Type' => 'text',      'Null' => 'NO', 'Default' => NULL, 'Extra' => ''              ,'Comment'=>''),
    );
    protected $aTableKeys = array(
        'PRIMARY'               => array('Non_unique' => '0', 'Column_name' => 'ID'),
        'ParentId'              => array('Non_unique' => '1', 'Column_name' => 'ParentId'),
        'MarketplaceIdentId'    => array('Non_unique' => '1', 'Column_name' => 'MarketplaceIdentId'),
        'MarketplaceIdentSku'   => array('Non_unique' => '1', 'Column_name' => 'MarketplaceIdentSku'),
    );

    protected function setDefaultValues() {
        return $this;
    }

    public function save() {
        if (!$this->load()->blLoaded) {
            $this->set('lastused', date('Y-m-d'));
        }
        return parent::save();
    }

    public function delete() {
        return parent::delete();
    }

}
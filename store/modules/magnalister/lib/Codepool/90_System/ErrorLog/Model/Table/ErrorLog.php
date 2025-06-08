<?php
class ML_ErrorLog_Model_Table_ErrorLog extends ML_Database_Model_Table_Abstract {

    protected $sTableName = 'magnalister_errorlog';
	
    protected $aFields = array (
        'id' => array (
			'isKey' => true,
            'Type' => 'int(10) unsigned', 'Null' => 'NO', 'Default' => NULL, 'Extra' => 'auto_increment', 'Comment'=>''
        ),
        'mpID' => array (
            'Type' => 'int(8) unsigned', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ),
        'products_id' => array (
            'Type' => 'int(11) unsigned', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ),
        'products_model' => array (
            'Type' => 'varchar(64)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ),
        'dateadded' => array (
            'isInsertCurrentTime' => true,
            'Type' => 'datetime', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ),
        'errormessage' => array (
            'Type' => 'text', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ),
        'data' => array (
            'Type' => 'longtext', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ),
    );
	
    protected $aTableKeys = array(
        'PRIMARY' => array('Non_unique' => '0', 'Column_name' => 'id'),
    );

    public function addError($productId, $sku, $message, $data) {
        $this->setDefaultValues();
        $this->set('id', null); //auto increment
        $this->set('products_id', $productId);
        $this->set('products_model', $sku);
        $this->set('errormessage', $message);
        $this->set('dateadded', date('Y-m-d H:i:s'));
        $this->set('data', $data);
        $this->save();
    }

    public function addApiError($aError) {
        $sSku = isset($aError['ERRORDATA']['SKU']) ? $aError['ERRORDATA']['SKU'] : null;
        if ($sSku === null && isset($aError['DETAILS']['SKU'])) {
            $sSku = $aError['DETAILS']['SKU'];
        }

        if ($sSku !== null) {
            $oProduct = MLProduct::factory()->getByMarketplaceSKU($sSku, true);
            if (!$oProduct->exists()) {
                $oProduct = MLProduct::factory()->getByMarketplaceSKU($sSku);
            }
            $iProductId = $oProduct->get('id');
            $sErrorMessage = $aError['ERRORMESSAGE'];

            $this->addError($iProductId, $sSku, $sErrorMessage, $aError);
        } else {
            $this->addError(0, 0, $aError['ERRORMESSAGE'], $aError);
        }
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
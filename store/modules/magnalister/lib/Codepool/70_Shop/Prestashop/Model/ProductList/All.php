<?php
MLFilesystem::gi()->loadClass('Prestashop_Model_ProductList_Abstract');
class ML_Prestashop_Model_ProductList_All extends ML_Prestashop_Model_ProductList_Abstract{
    protected function executeFilter() {
        return $this;
    }

    protected function executeList() {
        
    }

    public function getSelectionName() {
        
    }

}
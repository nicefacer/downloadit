<?php
MLFilesystem::gi()->loadClass('Shopware_Model_ProductList_Abstract');
class ML_Shopware_Model_ProductList_All extends ML_Shopware_Model_ProductList_Abstract{
    protected function executeFilter() {
        return $this;
    }

    protected function executeList() {
        
    }

    public function getSelectionName() {
        
    }

}
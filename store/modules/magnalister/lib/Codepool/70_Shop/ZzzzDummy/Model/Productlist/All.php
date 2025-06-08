<?php
MLFilesystem::gi()->loadClass('ZzzzDummy_Model_ProductList_Abstract');
/**
 * @todo
 */
class ML_ZzzzDummy_Model_ProductList_All extends ML_ZzzzDummy_Model_ProductList_Abstract {
    protected function executeFilter() {
        return $this;
    }

    protected function executeList() {
        
    }

    public function getSelectionName() {
        
    }

}
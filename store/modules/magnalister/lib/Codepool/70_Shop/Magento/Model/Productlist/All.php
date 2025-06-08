<?php
MLFilesystem::gi()->loadClass('Magento_Model_ProductList_Abstract');
class ML_Magento_Model_ProductList_All extends ML_Magento_Model_ProductList_Abstract{
    public function __construct() {
        parent::__construct();
    }
    protected function executeFilter() {
        return $this->oFilter
            ->registerDependency('magentonovariantsfilter')
            ->registerDependency('magentoproducttypefilter')
        ;
    }

    protected function executeList() {
        
    }

    public function getSelectionName() {
        
    }

}
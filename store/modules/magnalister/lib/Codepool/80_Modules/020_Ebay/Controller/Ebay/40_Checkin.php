<?php
MLFilesystem::gi()->loadClass('Productlist_Controller_Widget_ProductList_Selection');
class ML_Ebay_Controller_Ebay_Checkin extends ML_Productlist_Controller_Widget_ProductList_Selection {

    protected $aParameters = array('controller');
    
    
    public static function getTabTitle() {
        return MLI18n::gi()->get('ML_GENERIC_CHECKIN');
    }

    public static function getTabActive() {
        return MLModul::gi()->isConfigured();
    }
    
    public function getProductListWidget() {
        if ($this->isCurrentController()) {
            if (count($this->getProductList()->getMasterIds(true))==0) {//only check current page
                MLMessage::gi()->addInfo($this->__('ML_EBAY_TEXT_NO_MATCHED_PRODUCTS'));
            }
            return parent::getProductListWidget();
        } else {
            return $this->getChildController('summary')->render();
        }
    } 
    
    public function getPriceObject(ML_Shop_Model_Product_Abstract $oProduct) {
        
    }    
    /**
     * only prepared can be selected
     * @param ML_Database_Model_Table_Abstract $mProduct
     * @return type
     */
    public function getVariantCount($mProduct) {
        return MLDatabase::factory('ebay_prepare')->getVariantCount($mProduct);
    }
}
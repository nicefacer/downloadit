<?php
abstract class ML_Productlist_Model_ProductList_Selection extends ML_Productlist_Model_ProductList_Abstract {
    public function isSelected(ML_Shop_Model_Product_Abstract $oProduct){
        $i= MLDatabase::getDbInstance()->fetchOne("
            select 
                count(*)
            from 
                magnalister_selection s 
            where
                s.pid='".$oProduct->get('id')."'
                and 
                s.mpID='".MLModul::gi()->getMarketPlaceId()."'
                and
                s.selectionname='".$this->getSelectionName()."'
                and
                s.session_id='".  MLShop::gi()->getSessionId()."'
        ");
        return $i>0;
    }
    abstract public function getSelectionName();
}
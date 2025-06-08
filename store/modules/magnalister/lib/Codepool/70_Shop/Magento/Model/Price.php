<?php
class ML_Magento_Model_Price extends ML_Shop_Model_Price_Abstract implements ML_Shop_Model_Price_Interface{
    public function format($fPrice, $sCode){
        return Mage::getModel('directory/currency')->load($sCode)->format($fPrice);
    }
}
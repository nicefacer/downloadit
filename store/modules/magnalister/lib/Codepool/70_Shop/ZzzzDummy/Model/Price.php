<?php
class ML_ZzzzDummy_Model_Price extends ML_Shop_Model_Price_Abstract implements ML_Shop_Model_Price_Interface{
    /**
     * @todo
     */
    public function format($fPrice, $sCode){
        return $fPrice;
    }
}
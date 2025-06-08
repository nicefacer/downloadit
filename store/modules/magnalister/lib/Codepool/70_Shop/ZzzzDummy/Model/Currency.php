<?php
class ML_ZzzzDummy_Model_Currency extends ML_Shop_Model_Currency_Abstract {
    public function getList(){
        return array(
            'EUR' => array (
                'title' => 'Euro',
                'symbol_left' => '',
                'symbol_right' => '€',
                'decimal_point' => '.',
                'thousands_point' => '',
                'decimal_places' => 2,
                'value' => 1
            )
        );
    }

    public function getDefaultIso(){
        return 'EUR';
    }
    
    /**
     * @todo
     */
    public function updateCurrencyRate($sCurrency){
        return $this;
    }
}
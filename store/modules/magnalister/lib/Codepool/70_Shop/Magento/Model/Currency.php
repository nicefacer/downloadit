<?php
class ML_Magento_Model_Currency extends ML_Shop_Model_Currency_Abstract {
    
    /**
     * 
     * @todo method is a stub, make app without simple prict
     * @todo check if we really need foramtting option, guess its all done inside shpspecific classes.
     * @return array
     */
    public function getList(){
        $aCurrencies = array();
        foreach (Mage::getModel('directory/currency')->getConfigAllowCurrencies() as $sCurrency) {
            if ($sCurrency == 'EUR') {
                $aCurrencies['EUR'] = array (
                    'title' => 'Euro',
                    'symbol_left' => '',
                    'symbol_right' => '€',
                    'decimal_point' => ',',
                    'thousands_point' => '',
                    'decimal_places' => 2,
                    'value' => 1
                );
            } elseif ($sCurrency == 'USD') {
                $aCurrencies['USD'] = array (
                    'title' => 'USD',
                    'symbol_left' => '$',
                    'symbol_right' => '',
                    'decimal_point' => '.',
                    'thousands_point' => ',',
                    'decimal_places' => 2,
                    'value' => 1
                );
            }elseif ($sCurrency == 'GBP') {
                $aCurrencies['GBP'] = array (
                    'title' => 'Pound',
                    'symbol_left' => '£',
                    'symbol_right' => '',
                    'decimal_point' => '.',
                    'thousands_point' => ',',
                    'decimal_places' => 2,
                    'value' => 1
                );
            }elseif ($sCurrency == 'CHF') {
                $aCurrencies['CHF'] = array (
                    'title' => 'Swiss Franc',
                    'symbol_left' => '',
                    'symbol_right' => 'CHF',
                    'decimal_point' => ',',
                    'thousands_point' => '.',
                    'decimal_places' => 2,
                    'value' => 1
                );
            }
        }
        return $aCurrencies;
    }
    
    public function getDefaultIso(){
        try {
            $iLang = MLModul::gi()->getConfig('lang');
        } catch (Exception $oEx) {
            $iLang = 0;
        }
        return Mage::app()->getStore($iLang)->getCurrentCurrency()->getCode();
    }
    
    /**
     * magento updates all
     */
    public function updateCurrencyRate($sCurrency) {
        Mage::app()->getStore()->setConfig(Mage_Directory_Model_Observer::IMPORT_ENABLE, true);
        $oC=new Mage_Directory_Model_Observer;
        $oC->scheduledUpdateCurrencyRates('');
        return $this;
    }
    
}
<?php
class ML_Prestashop_Model_Currency extends ML_Shop_Model_Currency_Abstract {
    public function getList(){
        $aShopCurrencies = Currency::getCurrencies();
        $aCurrencies = array();
        foreach ($aShopCurrencies as $aCurrency) {
            $aCurrencies[$aCurrency[ 'iso_code' ]]=array(                
                'title' => $aCurrency[ 'name' ],
                'symbol_left' => '',
                'symbol_right' => $aCurrency[ 'sign' ],
                'decimal_point' => '.',
                'thousands_point' => '',
                'decimal_places' => 2,
                'value' => 1
            );
        }
        return $aCurrencies;
    }

    public function getDefaultIso() {
          $sDefaultCurrecny = Currency::getDefaultCurrency();
          return empty($sDefaultCurrecny->iso_code)?'EUR':$sDefaultCurrecny->iso_code;
    }     
    public function updateCurrencyRate($sCurrency) {
        try{
            MLCache::gi()->get('updateCurrencyRatelock');
        }catch(Exception $oExc){
            $sError = Currency::refreshCurrencies() ;
            if ( $sError != '' ){
                MLMessage::gi()->addError($sError) ;
            }else{
                MLCache::gi()->set('updateCurrencyRatelock',true , 60 *60);
            }
        }
         return $this ;
    }
    
    public function getCurrencyRate($sCurrency,$sTargetCurrencyCode){
        $aTargetCurrency = Currency::getCurrency(Currency::getIdByIsoCode($sTargetCurrencyCode));
        $aCurrency = Currency::getCurrency(Currency::getIdByIsoCode($sCurrency));
        if($aTargetCurrency['conversion_rate'] == 0){
            throw new Exception('Currency rate cannot be 0');
        }
        return round(((float)$aCurrency['conversion_rate']) /  ((float)$aTargetCurrency['conversion_rate']),2);
    }
   
    public function getShopCurrency($iShopId){
        if( $iShopId === null){
            $iCurrency = Configuration::get('PS_CURRENCY_DEFAULT');
        }else{
            $iCurrency = Configuration::get('PS_CURRENCY_DEFAULT', null, null, $iShopId);
        }
        $aCurrency = Currency::getCurrency($iCurrency);
        return $aCurrency['iso_code'];
    }
}
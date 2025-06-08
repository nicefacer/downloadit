<?php
class ML_Shopware_Model_Currency extends ML_Shop_Model_Currency_Abstract {
    public function getList(){
        $aCurrencyList = Shopware()->Models()->getRepository('Shopware\Models\Shop\Currency')->createQueryBuilder('Currency')->getQuery()->getArrayResult();        
        $aCurrencies = array();
        foreach($aCurrencyList as $aCurrency){
            $aCurrencies[$aCurrency['currency']]=array(
                'title' => $aCurrency['currency'],
                'symbol_left' => '',
                'symbol_right' =>  $aCurrency['symbol'],
                'decimal_point' => '.',
                'thousands_point' => '',
                'decimal_places' => 2,
                'value' => 1
            );
        }        
        return $aCurrencies;
    }

    public function getDefaultIso() {
        $oCurrency = Shopware()->Models()->getRepository('\Shopware\Models\Shop\Currency')->findOneBy(array("default" => 1));
        return $oCurrency->getCurrency();
    }

    public function updateCurrencyRate($sCurrency) {
        $sDefaultCurrency = $this->getDefaultIso();
        if($sDefaultCurrency != $sCurrency){
            try {
                $result = MagnaConnector::gi()->submitRequest(array(
                    'ACTION' => 'GetExchangeRate',
                    'SUBSYSTEM' => 'Core',
                    'FROM' => strtoupper($sDefaultCurrency),
                    'TO' => strtoupper($sCurrency),
                ));
                if ($result['EXCHANGERATE'] > 0) {
                    MLDatabase::getDbInstance()->query("UPDATE `" . Shopware()->Models()->getClassMetadata('Shopware\Models\Shop\Currency')->getTableName() ."` SET `factor` = '".$result['EXCHANGERATE'] . "' WHERE `currency` = '".$sCurrency."'");              
                }
            } catch (MagnaException $e) {
                throw new Exception('One Problem occured in updating Currency Rate');
            }
        }
        return $this;
    }
        
    public function getCurrencyRate($sCurrency,$sTargetCurrencyCode){
        $oCurrency = Shopware()->Models()->getRepository('\Shopware\Models\Shop\Currency')->findOneBy(array("currency" => $sCurrency));
        $oTargetCurrency = Shopware()->Models()->getRepository('\Shopware\Models\Shop\Currency')->findOneBy(array("currency" => $sTargetCurrencyCode));
        if($oTargetCurrency->getFactor() == 0){
            throw new Exception('Currency rate cannot be 0');
        }
        $fRate = round((float)($oCurrency->getFactor() / $oTargetCurrency->getFactor()),2);
        return $fRate;
        
    } 
        
    public function getShopCurrency($iShopId = null){
        if($iShopId !== null){
            $oShop = Shopware()->Models()->getRepository('\Shopware\Models\Shop\Shop')->find($iShopId);
        }else{
            $oShop = MLShop::gi()->getDefaultShop();
        }
        /* @var $oShop \Shopware\Models\Shop\Shop */
        return $oShop->getCurrency()->getCurrency();
    }
}

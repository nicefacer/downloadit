<?php

class ML_Shopware_Model_Price extends ML_Shop_Model_Price_Abstract implements ML_Shop_Model_Price_Interface {

    public function format($fPrice, $sCode) {
        $oCurrency = Shopware()->Models()->getRepository('\Shopware\Models\Shop\Currency')->findOneBy(array("currency" => $sCode));
         if ($oCurrency->getFactor()) {
            $price = floatval($price) * floatval($oCurrency->getFactor());
        }
        return  MLHelper::gi('model_price')->getPriceByCurrency($fPrice, $sCode,true);
    }

}
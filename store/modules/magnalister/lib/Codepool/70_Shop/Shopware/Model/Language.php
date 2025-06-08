<?php
class ML_Shopware_Model_Language extends ML_Shop_Model_Language_Abstract {
    public function getCurrentIsoCode() {
        return Shopware()->Locale()->getLanguage();
    }

    public function getCurrentCharset() {
        return 'UTF-8';
    }
    
}
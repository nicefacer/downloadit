<?php
class ML_Prestashop_Model_Language extends ML_Shop_Model_Language_Abstract {
    public function getCurrentIsoCode() {
        return Context::getContext()->language->iso_code;
    }

    public function getCurrentCharset() {
        return 'UTF-8';
    }
//    public function getList(){
//        $aLangs = Language::getLanguages();
//        foreach($aLangs as &$aLang){
//            $aLang= $aLang['name'];
//        }
//    }
}
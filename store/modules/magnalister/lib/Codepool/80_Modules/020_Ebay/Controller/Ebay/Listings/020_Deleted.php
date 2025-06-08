<?php
MLFilesystem::gi()->loadClass('Core_Controller_Abstract');
class ML_Ebay_Controller_Ebay_Listings_Deleted extends ML_Core_Controller_Abstract {

    protected $aParameters = array('controller');

    public static function getTabTitle() {
        return MLI18n::gi()->get('ML_GENERIC_DELETED');
    }

    public static function getTabActive() {
        return MLModul::gi()->isConfigured();
    }
	
    public function execute(){
        include MLFilesystem::getOldLibPath('php/modules/ebay/listings.php');
        MLSetting::gi()->add('aCss', array('magnalister.productlist.css'), true);
    }
}
<?php
MLFilesystem::gi()->loadClass('Core_Controller_Abstract');
class ML_Amazon_Controller_Amazon_Listings_Inventory extends ML_Core_Controller_Abstract {
   
    protected $aParameters = array('controller');

    public static function getTabTitle() {
        return MLI18n::gi()->get('ML_GENERIC_INVENTORY');
    }

    public static function getTabActive() {
        return MLModul::gi()->isConfigured();
    }
	
    public function execute(){
        include MLFilesystem::getOldLibPath('php/modules/amazon/classes/InventoryView.php');
        MLSetting::gi()->add('aCss', array('magnalister.productlist.css'), true);
        $aGet = MLRequest::gi()->data();
        
        $_url['mode'] = 'listings';

        $iV = new InventoryView();
        $iV->includeView('widget_listings_misc_listingbox');
        echo $iV->renderView();

        if (array_key_exists('GetErrorLog', $aGet) && preg_match('/^[0-9]*$/', $aGet['GetErrorLog'])) {
            $request = array();
            $request['ACTION'] = 'GetErrorLog';
            $request['BATCHID'] = $aGet['GetErrorLog'];

            try {
                $result = MagnaConnector::gi()->submitRequest($request);
                echo print_m($result, 'GetErrorLog');
            } catch (MagnaException $e) {

            }
        }

    }
}
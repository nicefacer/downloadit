<?php
/**
 * @todo
 */
class ML_ZzzzDummy_Model_Order extends ML_Shop_Model_Order_Abstract {
    protected $oCurrentOrder = null;
    
	protected function getShopOrder() {
        return $this->oCurrentOrder;
    }
	
    public function getShopOrderStatus() {
        return '';
    }
	
    public function getShopOrderLastChangedDate() {
        return '';
    }

    public static function getOutOfSyncOrdersArray() {
        $aOut = array();
        return $aOut;
    }

    public function getShippingDateTime() {
        return '';
    }

    public function getShippingDate() {
        return '';
    }

    public function getShippingCarrier() {
        return '';
    }

    public function getShippingTrackingCode() {
        return '';
    }

    public function getEditLink() {
        return '';
    }

    public function getShopOrderStatusName() {
        return '';
    }

    public function shopOrderByMagnaOrderData($aData) {
        
    }

    public function setSpecificAcknowledgeField(&$aOrderParameters, $aOrder) {
        
    }

}

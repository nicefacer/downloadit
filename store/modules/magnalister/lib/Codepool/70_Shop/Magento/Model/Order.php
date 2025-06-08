<?php
//http://www.excellencemagentoblog.com/useful-code-snippets
class ML_Magento_Model_Order extends ML_Shop_Model_Order_Abstract {
    protected $oCurrentOrder=null;
    /**
     * 
     * @return Mage_Sales_Model_Order
     */
    protected function getShopOrder(){
        if($this->oCurrentOrder===null){
            $this->oCurrentOrder=Mage::getModel('sales/order')->loadByIncrementId($this->get('current_orders_id'));
        }
        return $this->oCurrentOrder;
    }
    public function getShopOrderStatus(){
        return strtolower($this->getShopOrder()->getStatus());
    }
    public function getShopOrderLastChangedDate(){
        return $this->getShopOrder()->getUpdatedAt();
    }

    public static function getOutOfSyncOrdersArray($iOffset = 0 ,$blCount = false){
        $oCollection = Mage::getModel('sales/order')->getCollection();
        /* @var $oCollection Mage_Sales_Model_Resource_Order_Collection */
        $oSelect = $oCollection->getSelect();
        /* @var $oSelect Varien_Db_Select */
        $oSelect
            ->joinRight(array('magnalister_orders' => 'magnalister_orders'), 'main_table.increment_id = magnalister_orders.current_orders_id')
            ->where("main_table.status != magnalister_orders.status and mpID = '".MLModul::gi()->getMarketPlaceId()."'");
            
        if($blCount){
            return $oCollection->count();
        }  else {
            $oSelect->limit(100 ,$iOffset);
            $aOut = array();
            foreach ($oCollection as $oShopOrder) {
                $aOut[] = $oShopOrder->getIncrementId();
            }
            return $aOut;
        }
    }
    public function getShippingDateTime() {
        $sShipDate = $this->getShopOrder()->getShipmentsCollection()->getLastItem()->getUpdatedAt();
        if (empty($sShipDate)) {//no shipping found, walk status history
            foreach ($this->getShopOrder()->getStatusHistoryCollection()->getItemsByColumnValue('status', $this->getShopOrderStatus()) as $oHistory) {
                $sShipDate = empty($sShipDate) ? $oHistory->getCreatedAt() : $sShipDate;
                if ($oHistory->getEntityName() == 'shipment') {//is shipment, force!
                    $sShipDate = $oHistory->getCreatedAt();
                    break;
                }
            }
        }
        return empty($sShipDate) ? date('Y-m-d H:i:s') : $sShipDate;
    }

    public function getShippingDate() {
        return substr($this->getShippingDateTime(),0,10);
    }
    public function getShippingCarrier(){
        $oOrder=$this->getShopOrder();
        $oShip=$oOrder->getShipmentsCollection()->getLastItem();
        $oTrack=$oShip->getTracksCollection()->getLastItem();
        return $oTrack->getTitle()!==null?$oTrack->getTitle():$this->getModul()->getConfig('orderstatus.carrier.default');
    }
    public function getShippingTrackingCode(){
        $oOrder=$this->getShopOrder();
        $oShip=$oOrder->getShipmentsCollection()->getLastItem();
        $oTrack=$oShip->getTracksCollection()->getLastItem();
        return $oTrack->getNumber()!==null?$oTrack->getNumber():$this->getModul()->getConfig('orderstatus.carrier.additional');
    }

    public function getEditLink() {
        $oShopOrder=$this->getShopOrder();
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/sales_order/view/order_id/'.$oShopOrder->getId());
    }
    public function shopOrderByMagnaOrderData($aData) {
        MLShop::gi()->initMagentoStore(MLModul::gi()->getConfig('orderimport.shop'));
        $aOrder = MLHelper::gi('model_shoporder')
            ->init()
            ->setMlOrder($this)
            ->setCurrentOrderData($aData)
            ->execute()
        ;
        $this->oCurrentOrder = null;// to reload after creating
        return $aOrder;
    }

    public function getShopOrderStatusName() {
        return '';
    }

    public function setSpecificAcknowledgeField(&$aOrderParameters, $aOrder) {
        
    }

}

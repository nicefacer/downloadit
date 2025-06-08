<?php

MLFilesystem::gi()->loadClass('Shop_Model_Order_Abstract');
class ML_Prestashop_Model_Order extends ML_Shop_Model_Order_Abstract {
    /**  @var OrderCore */
    protected $oShopOrder = null;
    
    /**
     * find order in shop and return order object
     * @return OrderCore
     * @throws Exception
     */
    protected function getShopOrder(){
        if($this->oShopOrder===null){
            $this->oShopOrder = new Order($this->get('current_orders_id'));
        }
        if(!$this->oShopOrder instanceof Order){
            throw new Exception("order is not found. shop order id : ".$this->get('current_orders_id'));
        }
        return $this->oShopOrder;
    }
    public function getShopOrderStatus() {
        return $this->getShopOrder()->current_state."";//convert status id to string
    }

    public function getEditLink() {
        return (isset(Context::getContext()->employee) && is_object(Context::getContext()->employee))?'index.php?controller=AdminOrders&vieworder&id_order=' . $this->get('current_orders_id') .
                '&token=' . Tools::getAdminToken('AdminOrders' . (int) Tab::getIdFromClassName('AdminOrders') . (int) Context::getContext()->employee->id):'';
    }

    public function getShippingCarrier() {
        $oOrder = new Order($this->get('current_orders_id'));
        $oCarrier = new Carrier($oOrder->id_carrier);
        return isset($oCarrier->name) ? $oCarrier->name : $this->getModul()->getConfig('orderstatus.carrier.default');
    }

    public function getShippingDateTime() {
        $oOrder = $this->getShopOrder();
        $aOrderHistory = $oOrder->getHistory(
                $this->getModul()->getConfig('lang'), 
                (int)($this->getModul()->getConfig('orderstatus.shipped')), 
                FALSE);
        $sShippedOrder = '';
        if(count($aOrderHistory)>0){
            $aShippedOrder = current($aOrderHistory);
            $sShippedOrder = (string)$aShippedOrder['date_add'];
        }else{
            $sShippedOrder = $oOrder->date_upd;
        }        
         #return substr($sShippedOrder,0,10);
         return $sShippedOrder;
    }

    public function getShippingDate() {
         return substr($this->getShippingDateTime(),0,10);
    }

    public function getShippingTrackingCode() {                
        $aTracking = MLDatabase::factorySelectClass()->select('tracking_number') 
                ->from(_DB_PREFIX_.'order_carrier')
                ->where('id_order ='.$this->get('current_orders_id'))
                ->getResult();
        if(count($aTracking)>0){
            return $aTracking[0]['tracking_number'];
        }else{
            return $this->getModul()->getConfig('orderstatus.carrier.additional');
        }
    }

    public function getShopOrderLastChangedDate(){
        $oOrder = new Order($this->get('current_orders_id'));
        if(!isset($oOrder->date_upd)){
            throw new Exception("order update date is empty ");
        }else{
            return $oOrder->date_upd;
        }    
    }

    public static function getOutOfSyncOrdersArray($iOffset = 0,$blCount = false){
        $oQueryBuilder = MLDatabase::factorySelectClass()->select('id_order')
                        ->from(_DB_PREFIX_ . 'orders','po')
                        ->join(array('magnalister_orders', 'mo', 'po.id_order = mo.current_orders_id'), ML_Database_Model_Query_Select::JOIN_TYPE_LEFT)
                        ->where("po.current_state != mo.status AND mo.mpID = '".MLModul::gi()->getMarketPlaceId()."'")
                        ;
                        
        if($blCount){
            return $oQueryBuilder->getCount();
        }else{
            $aOrders = $oQueryBuilder->limit($iOffset,100)
                ->getResult();
            $aOut = array();
            foreach ($aOrders as $aOrder) {
                $aOut[] = $aOrder['id_order'];
            }
            return $aOut;
        }
    }

    public function shopOrderByMagnaOrderData($aData) {        
        return MLHelper::gi('model_shoporder')
            ->setOrder($this)
            ->setNewOrderData($aData)
            ->shopOrder()
        ;
    }

    public function getShopOrderStatusName() {
        $oState = new OrderState($this->getShopOrder()->current_state);
        /* @var $oState OrderStateCore */
        return $oState->name[Context::getContext()->language->id];
    }

    public function setSpecificAcknowledgeField(&$aOrderParameters, $aOrder) {
        
    }

}

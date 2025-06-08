<?php

MLFilesystem::gi()->loadClass('Shop_Model_Order_Abstract');

class ML_Shopware_Model_Order extends ML_Shop_Model_Order_Abstract {

    /**
     * 
     * @return Shopware\Models\Order\Order
     * @throws Exception
     */
    public function getShopOrderObject() {
        $oOrder = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->find($this->get('current_orders_id'));
        if (is_object($oOrder)) {
            return $oOrder;
        } else {
            throw new Exception("this order is not found in shop");
        }
    }

    public function getShopOrderStatus() {
        try{
            $oOrder = $this->getShopOrderObject();
        /* @var $oOrder Shopware\Models\Order\Order */
                      return $oOrder->getOrderStatus()->getId().'';//convert status id to string
        }  catch (Exception $oExc){
            return null;
        }
    }
    
    
    public function getShopPaymentStatus() {
        try{
            $oOrder = $this->getShopOrderObject();
        /* @var $oOrder Shopware\Models\Order\Order */
                      return $oOrder->getPaymentStatus()->getId().'';//convert status id to string
        }  catch (Exception $oExc){
            return null;
        }
    }
    
    public function getShopOrderStatusName() {
        try{
            $oOrder = $this->getShopOrderObject();
        /* @var $oOrder Shopware\Models\Order\Order */        
              return $oOrder->getOrderStatus()->getDescription() ;
        } catch (Exception $oExc){
            return null;
        }
    }
    public function getEditLink() {
        return $this->get('current_orders_id');
    }

    public function getShippingCarrier() {
        $sDefaultCarrier = $this->getModul()->getConfig('orderstatus.carrier.default');
        if ($sDefaultCarrier == '-1') {
            try {
                $oOrder = $this->getShopOrderObject();
                /* @var $oOrder Shopware\Models\Order\Order */
                $sCarrier = $oOrder->getDispatch()->getName();
                return empty($sCarrier) ? null : $sCarrier;
            } catch (Exception $oEx) {
                return null;
            }
        } elseif ($sDefaultCarrier == '') {
            return null;
        } else {
            return $sDefaultCarrier;
        }
    }

    public function getShippingDateTime() {
            $oSelect = MLDatabase::factorySelectClass();
            $aChnageDate = $oSelect->from(Shopware()->Models()->getClassMetadata('Shopware\Models\Order\History')->getTableName())
                    ->where("orderid = '".$this->get('current_orders_id')."'  AND order_status_id='".MLModul::gi()->getConfig('orderstatus.shipped')."'")                    
                    ->orderBy('change_date DESC')
                    ->getResult();                    
            $oOrderHistory = current($aChnageDate);
        if (!isset($oOrderHistory['change_date']) ) {
            return date('Y-m-d H:i:s');
        } else {
            return $oOrderHistory['change_date'];
        }
    }

    public function setSpecificAcknowledgeField(&$aOrderParameters,$aOrder){        
        try{
            $aOrderParameters['ShopOrderIDPublic'] = $this->getShopOrderObject()->getNumber();
        }  catch (Exception $oEx){//if order deosn't exist in shopware
            $aData = $this->get('orderdata');
            if (array_key_exists('ShopwareOrderNumber',$aOrder)) {//
                $aOrderParameters['ShopOrderIDPublic'] = $aOrder['ShopwareOrderNumber'];
            }else if(array_key_exists('ShopwareOrderNumber',$aData)){//if order existed see in existed orderdata
                $aOrderParameters['ShopOrderIDPublic'] = $aData['ShopwareOrderNumber'];
            }
        }        
    }
    
    public function getShippingDate() {
        return substr($this->getShippingDateTime(),0,10);
    }

    public function getShippingTrackingCode() {
        try {
            $oOrder = $this->getShopOrderObject($this->get('current_orders_id'));
            $sTrackingCode = $oOrder->getTrackingCode();
            if (empty($sTrackingCode)) {
                throw new Exception('Empty Tracking code');
            }
            return $sTrackingCode;
        } catch (Exception $exc) {
            return $this->getModul()->getConfig('orderstatus.carrier.additional');
        }
    }

    public function getShopOrderLastChangedDate() {
        $oOrderRep = Shopware()->Models()->getRepository('Shopware\Models\Order\Order');
        /* @var $oOrderRep \Shopware\Models\Order\Repository */
        $oOrder = $oOrderRep->getOrderStatusHistoryListQuery($this->get('current_orders_id'), array(array('property' => 'history.changeDate', 'direction' => 'ASC')), NULL, 1)
                ->getOneOrNullResult();
        if (!is_object($oOrder['changeDate'])) {
            $oOrder = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->find($this->get('current_orders_id'));
            /* @var $oOrder Shopware\Models\Order\Order */
            return $oOrder->getOrderTime()->format('Y-m-d h:i:s');
        } else {
            return $oOrder['changeDate']->format('Y-m-d h:i:s');
        }
    }

    public static function getOutOfSyncOrdersArray($iOffset = 0,$blCount = false) {
        $oQueryBuilder = MLDatabase::factorySelectClass()->select('id')
                        ->from(Shopware()->Models()->getClassMetadata('Shopware\Models\Order\Order')->getTableName(), 'so')
                        ->join(array('magnalister_orders', 'mo', 'so.id = mo.current_orders_id'), ML_Database_Model_Query_Select::JOIN_TYPE_LEFT)
                        ->where("so.status != mo.status AND mo.mpID='" . MLModul::gi()->getMarketPlaceId() . "'")
                        ;
        
        
        if($blCount){
            return $oQueryBuilder->getCount();
        }else{
            $aOrders = $oQueryBuilder->limit($iOffset,100)
                ->getResult();
            $aOut = array();
            foreach ($aOrders as $aOrder) {
                $aOut[] = $aOrder['id'];
            }
            return $aOut;
        }
    }

    public function shopOrderByMagnaOrderData($aData) {
        Shopware()->Models()->clear(); // clean doctrine objects for don't have confusing data in multiple merged order in one request
        $oDb = Shopware()->Db();
        try {
            $mAutoCommit = $oDb->fetchOne("SELECT @@autocommit");
            $oDb->query("SET autocommit = 0;");
            $oDb->query("SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;");
        } catch (Exception $oEx) {
            // no mysql - we don't care
        }
        $oDb->query("BEGIN");
        try {
            $mReturn = MLHelper::gi('model_shoporder')
                ->setOrder($this)
                ->setNewOrderData($aData)
                ->shopOrder()
            ;
            $oDb->query("commit");
            try {
                $oDb->query("SET autocommit = ".$mAutoCommit.";");
            } catch (Exception $oEx) {
                // no mysql - we don't care
            }
            return $mReturn;
        } catch (Exception $oEx) {
            $oDb->query("rollback");
            try {
                $oDb->query("SET autocommit = ".$mAutoCommit.";");
            } catch (Exception $oEx) {
                // no mysql - we don't care
            }
            throw $oEx;
        }
    }
    
    public function triggerAfterShopOrderByMagnaOrderData() {            
        MLHelper::gi('model_order_dhl')->fillMissingDhlData();
        return $this;
    }

}

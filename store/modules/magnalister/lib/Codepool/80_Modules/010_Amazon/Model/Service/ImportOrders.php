<?php

class ML_Amazon_Model_Service_ImportOrders extends ML_Modul_Model_Service_ImportOrders_Abstract {
    public function canDoOrder(ML_Shop_Model_Order_Abstract $oOrder,&$aOrder){
        if($oOrder->get('orders_id')===null){//only new orders
            return 'Create order';
        }else{
            //throw new Exception('Order aleready exists');
            throw MLException::factory('Model_Service_ImportOrders_OrderExist')->setShopOrder($oOrder);
        }
    }
}
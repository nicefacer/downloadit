<?php

MLFilesystem::gi()->loadClass('Ebay_Model_Service_UpdateOrders');

class ML_ShopwareEbay_Model_Service_UpdateOrders extends ML_Ebay_Model_Service_UpdateOrders {
    
    public function canDoOrder(ML_Shop_Model_Order_Abstract $oOrder, &$aOrder) {
        if ($oOrder->get('orders_id') !== null) { // only existing orders
            $oOrderHelper = MLHelper::gi('model_shoporder');
            /* @var $oOrderHelper ML_Shopware_Helper_Model_ShopOrder */
            $oOrderHelper->setOrder($oOrder)->setNewOrderData($aOrder)
                    ->updatePaymentMethod()
                    ->updateShippingMethod()
                    ->updatePaymentStatus()
                    ->updateShippingAddress()
              ;
            $aUpdateableStatusses = MLModul::gi()->getConfig('updateable.orderstatus');
            $aUpdateableStatusses = is_array($aUpdateableStatusses) ? $aUpdateableStatusses : array();
            if (!in_array($oOrder->getShopOrderStatus(), $aUpdateableStatusses, true)) {
                /**
                 * if orderstatus is not in updateable.orderstatus we don't update the order.
                 */
                $sStatusName = $oOrder->getShopOrderStatusName();
                throw new Exception("Order status cannot be updated because '".$oOrder->getShopOrderStatus().(!empty($sStatusName) ? " - ".$sStatusName : '')."' is not updateable");
            } else {
                return 'Update order';
            }
        } else {
            throw new Exception("Order doesn't exist");
        }
    }
}

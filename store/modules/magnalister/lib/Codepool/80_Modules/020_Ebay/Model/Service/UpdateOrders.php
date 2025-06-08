<?php
MLFilesystem::gi()->loadClass('Modul_Model_Service_ImportOrders_Abstract');

class ML_Ebay_Model_Service_UpdateOrders extends ML_Modul_Model_Service_ImportOrders_Abstract {
    protected $sGetOrdersApiAction = 'GetOrdersUpdates';
    protected $sAcknowledgeApiAction = 'AcknowledgeUpdatedOrders';
    protected $blUpdateMode = true;

    public function canDoOrder(ML_Shop_Model_Order_Abstract $oOrder, &$aOrder) {
        if ($oOrder->get('orders_id') !== null) { // only existing orders
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
        
    protected function isMutex() {
        return true;
    }

}
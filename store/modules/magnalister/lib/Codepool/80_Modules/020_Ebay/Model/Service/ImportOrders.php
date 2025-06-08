<?php

class ML_Ebay_Model_Service_ImportOrders extends ML_Modul_Model_Service_ImportOrders_Abstract {

//    /**
//     * @deprecated (1429879042) we don't take care of shipping data, but its deprecated, perhaps we need it
//     */
//    public function canDoOrderDeprecated(ML_Shop_Model_Order_Abstract $oOrder, &$aOrder) {
//        $aOrderData = $oOrder->get('data');
//        if ( //same address
//               isset($aOrderData['AddressId'])
//            && isset($aOrder['MPSpecific']['AddressId'])
//            && $aOrderData['AddressId'] == $aOrder['MPSpecific']['AddressId']
//        ) {
//            return 'Extend existing order - same email address';
//        } elseif ($oOrder->get('orders_id') === null) {
//            return 'Create order';
//        } else {
//            throw MLException::factory('Model_Service_ImportOrders_OrderExist')->setShopOrder($oOrder);
//        }
//    }

    public function canDoOrder(ML_Shop_Model_Order_Abstract $oOrder, &$aOrder) {
        $aOrderData = $oOrder->get('orderdata');
        $aOrderAllData = $oOrder->get('orderData');
        if (   isset($aOrderData['AddressSets']['Main']['EMail'])
            && isset($aOrder['AddressSets']['Main']['EMail'])
            && $aOrderData['AddressSets']['Main']['EMail'] == $aOrder['AddressSets']['Main']['EMail']
            && MLModul::gi()->getConfig('importonlypaid') != '1'
            && isset($aOrderAllData['Order']['Currency'])
            && isset($aOrder['Order']['Currency'])
            && $aOrderAllData['Order']['Currency'] == $aOrder['Order']['Currency']
        ) {
            return 'Extend existing order - same customer address';
        } elseif ($oOrder->get('orders_id') === null) {
            return 'Create order';
        } else {
            //throw new Exception('Order already exists');
            throw MLException::factory('Model_Service_ImportOrders_OrderExist')->setShopOrder($oOrder);
        }
    }

}

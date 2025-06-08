<?php

class ML_Amazon_Model_OrderLogo {

    public function getLogo(ML_Shop_Model_Order_Abstract $oModel) {
        $aData = $oModel->get('data');
        $sCancelledStatus = MLDatabase::factory('config')->set('mpid', $oModel->get('mpid'))->set('mkey', 'orderstatus.cancelled')->get('value');
        $sShippedStatus = MLDatabase::factory('config')->set('mpid', $oModel->get('mpid'))->set('mkey', 'orderstatus.shipped')->get('value');
        if ($aData['FulfillmentChannel'] != 'MFN') {
            $sLogo = 'amazon_fba_orderview';
        } else {
            $sStatus = $oModel->get('status');
            if (false) {//todo
                $sLogo = 'amazon_orderview_error';
            } elseif ($sCancelledStatus == $sStatus) {
                $sLogo = 'amazon_orderview_cancelled';
            } elseif ($sShippedStatus == $sStatus) {
                $sLogo = 'amazon_orderview_shipped';
            } else {
                $sLogo = 'amazon_orderview';
            }
        }
        return $sLogo . '.png';
    }

}

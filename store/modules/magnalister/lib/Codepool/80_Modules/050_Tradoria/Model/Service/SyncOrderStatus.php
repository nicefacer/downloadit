<?php

class ML_Tradoria_Model_Service_SyncOrderStatus extends ML_Modul_Model_Service_SyncOrderStatus_Abstract {

    protected function getCarrier($sCarrier) {
        $aCarriers = MLModul::gi()->getCarrier();
        if (in_array($sCarrier, $aCarriers)) { // if shipping method of order exists in valid list of carrier
            return $sCarrier;
        } elseif (MLModul::gi()->getConfig('orderstatus.carrier.default') != null) {
            return MLModul::gi()->getConfig('orderstatus.carrier.default');
        } else {
            throw new Exception('orderstatus.carrier.default is not correct configured');
        }
    }

    protected function postProcessError($aError, &$aModels) {
        $sFieldId = 'MOrderID';
        $sMarketplaceOrderId = null;
        if (isset($aError['DETAILS']) && isset($aError['DETAILS'][$sFieldId])) {
            $sMarketplaceOrderId = $aError['DETAILS'][$sFieldId];
        }
        if (empty($sMarketplaceOrderId)) {
            return;
        }

        if (
            isset($aError['DETAILS']['ErrorCode']) 
            && in_array ($aError['DETAILS']['ErrorCode'], array(
                3710, // 3710: order cant be updated (not in edit mode)
                3753, // 3753: order is already canceled
            ))
        ) {
            $this->saveOrderData($aModels[$sMarketplaceOrderId]);
            unset($aModels[$sMarketplaceOrderId]);
        }
    }

}

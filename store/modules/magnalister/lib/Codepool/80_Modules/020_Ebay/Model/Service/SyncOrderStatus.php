<?php

class ML_Ebay_Model_Service_SyncOrderStatus extends ML_Modul_Model_Service_SyncOrderStatus_Abstract {

    protected function getCarrier($sCarrier) {
        $aCarriers = MLModul::gi()->getCarrier();
        if (in_array($sCarrier, $aCarriers) || MLModul::gi()->getConfig('orderstatus.carrier.default') == null || MLModul::gi()->getConfig('orderstatus.carrier.default') == '-1') {
            return $sCarrier;
        } else {
            return MLModul::gi()->getConfig('orderstatus.carrier.default');
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

        // it will return if order don't belongs to customer or is to old
        if (isset($aError['ERRORCODE']) && $aError['ERRORCODE'] == 1450279354) {
                $this->saveOrderData($aModels[$sMarketplaceOrderId]);
                unset($aModels[$sMarketplaceOrderId]);
            }
        }
    }

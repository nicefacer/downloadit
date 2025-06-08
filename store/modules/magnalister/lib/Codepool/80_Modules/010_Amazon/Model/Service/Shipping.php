<?php

/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
class ML_Amazon_Model_Service_Shipping {

    protected $aOrders = array();

    public function setOrders($aOrders) {
        $this->aOrders = $aOrders;
        return $this;
    }

    public function getOrders() {
        return $this->aOrders;
    }

    public function getShippingService() {
        $aData = array();
        $aList = array();
        $aOrders = $this->getOrders();
        $aGlobalData = array();
        foreach ($aOrders as $oOrder) {
            $iOrderId = $oOrder->get('elementId');
            $aData[$iOrderId] = $oOrder->get('data');
            $aGlobalData[$iOrderId] = $aData[$iOrderId]['globalinfo'];
            unset($aData[$iOrderId]['globalinfo']);
            if (isset($aData['ShippingServiceId'])) {
                unset($aData['ShippingServiceId']);
            }
            $aData[$iOrderId]["PackageDimensions"]['Unit'] = MLModul::gi()->getConfig('shippinglabel.size.unit');
//            $aData[$iOrderId]["ShipFromAddress"] = $this->getDefaultAddress();
        }

        foreach ($aData as $sOrderId => $aOrder) {
            $aList[$sOrderId] = $aGlobalData[$sOrderId];
            try {
                $aResponse = MagnaConnector::gi()->submitRequest(
                        array(
                            'ACTION' => 'MFS_GetShippingServices',
                            'DATA' => $aOrder,
                ));
                if (!isset($aResponse['DATA']) || $aResponse['STATUS'] != 'SUCCESS' || !is_array($aResponse['DATA'])) {
                    throw new Exception('There is a problem to get list of orders');
                } else { 
//            $aList[$sOrderId]['shippingservice'] = $this->testShippingData();
                    $aList[$sOrderId]['shippingservice'] = $aResponse['DATA']['ShippingServices'];
                }
            } catch (Exception $ex) {
                $aList[$sOrderId]['shippingservice'] = array();
            } catch (MagnaException $ex) {
                $aList[$sOrderId]['shippingservice'] = array();
            }
        }
        return $aList;
    }

    public function confirmShipping() {
        $aOrders = $this->getOrders();
        $oOrder = current($aOrders);
        $aOriginalData = $oOrder->get('data');
        $aData = $aOriginalData;
        unset($aData['globalinfo']);
        $aData['ShippingServiceOptions']["DeliveryExperience"] = MLModul::gi()->getConfig('shippingservice.deliveryexpirience');
        $aData['ShippingServiceOptions']["CarrierWillPickUp"] = MLModul::gi()->getConfig('shippingservice.carrierwillpickup') == 'true';
        $aData["PackageDimensions"]['Unit'] = MLModul::gi()->getConfig('shippinglabel.size.unit');
//        $aData["ShipFromAddress"] = $this->getDefaultAddress();

        try {
            $aResponse = MagnaConnector::gi()->submitRequest(
                    array(
                        'ACTION' => 'MFS_CreateShipment',
                        'DATA' => $aData,
            ));
            $aOriginalData['originaldata']['ShipmentId'] = $aResponse['DATA']['ShipmentId'];
            $oOrder->set('data',$aOriginalData);
            if (!isset($aResponse['DATA']) || $aResponse['STATUS'] != 'SUCCESS' || !is_array($aResponse['DATA'])) {
                throw new Exception('There is a problem to get list of orders');
            }
        } catch (Exception $ex) {
            
        } catch (MagnaException $ex) {
            
        }
    }

    public function downloadShippingLabel() {
        $aOrders = $this->getOrders();
        $aData = array();
        foreach ($aOrders as $oOrder) {
            $aOrderData = $oOrder->get('data');
            $aData['ShipmentIds'][] = isset($aOrderData['originaldata']['ShipmentId']) ? $aOrderData['originaldata']['ShipmentId'] : $oOrder->get('elementId');
        }
        try {
            $aResponse = MagnaConnector::gi()->submitRequest(
                    array(
                        'ACTION' => 'MFS_DownloadShipment',
                        'DATA' => $aData,
            ));
            if (!isset($aResponse['DATA']) || $aResponse['STATUS'] != 'SUCCESS' || !is_array($aResponse['DATA'])) {
                throw new Exception('There is a problem to get list of orders');
            }else{
                return $aResponse['DATA']['DownloadLink'];
            }
        } catch (Exception $ex) {
            
        } catch (MagnaException $ex) {
            
        }
        return null;
    }


    /**
     * @todo Description
     * @return boolean
     */
    public function haveError() {
        return false;
    }

    protected function testShippingData() {
        return json_decode('[
            {
                "ShippingServiceName": "DHL Paket bis 5 kg",
                "CarrierName": "DHL",
                "ShippingServiceId": "DHL_PAKET_5KG",
                "ShippingServiceOfferId": "HDDUKqtQVFetpBZAqx5c16Djmo1yN1z4PZv53At2O\/Bl8pr\/Qdlc1hnDWrj7BC5+Oz0PcHd\/QunfoCfruJqa6dN+n0oXXBnmE0DDnQEcYOerpkh3y5nE2aRKOSBzpLTDQZqITWgT1hHQLuzLYQGLjQ==",
                "ShipDate": "2016-02-15 18:11:53",
                "EarliestEstimatedDeliveryDate": "2016-02-17 00:00:00",
                "LatestEstimatedDeliveryDate": "2016-02-18 00:00:00",
                "Rate": {
                    "CurrencyCode": "EUR",
                    "Amount": "5.99"
                },
                "ShippingServiceOptions": {
                    "DeliveryExperience": "DeliveryConfirmationWithoutSignature",
                    "CarrierWillPickUp": "false"
                }
            },
            {
                "ShippingServiceName": "DHL Paket bis 2 kg",
                "CarrierName": "DHL",
                "ShippingServiceId": "DHL_PAKET_2KG",
                "ShippingServiceOfferId": "HDDUKqtQVFetpBZAqx5c16Djmo1yN1z4PZv53At2O\/Bl8pr\/Qdlc1hnDWrj7BC5+xNVivXr4j1QE9yzdhrXT4EUaaKO1NMjUILep533\/mpCHzb\/XLYLcO\/92KNLEg0Acxj3GKTofkxaNe9oTG2IiZQ==",
                "ShipDate": "2016-02-15 18:11:53",
                "EarliestEstimatedDeliveryDate": "2016-02-17 00:00:00",
                "LatestEstimatedDeliveryDate": "2016-02-18 00:00:00",
                "Rate": {
                    "CurrencyCode": "EUR",
                    "Amount": "4.99"
                },
                "ShippingServiceOptions": {
                    "DeliveryExperience": "DeliveryConfirmationWithoutSignature",
                    "CarrierWillPickUp": "false"
                }
            }
        ]', true);
    }

}

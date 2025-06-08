<?php

/**
 * select all products 
 * amazon-config: 
 *  - amazon.lang isset
 * magnalister.selectionname=='match
 */
class ML_Amazon_Model_List_Amazon_Order_ShippingMethod {

    protected $aList = null;

    public function getList() {
        if ($this->aList === null) {
            $oService = ML::gi()->instance('model_service_shipping');
            /* @var $oService ML_Amazon_Model_Service_Shipping */
            $oSelection = MLDatabase::factory('globalselection')->set('selectionname', $this->getSelectionName())->getList();
            $oService->setOrders($oSelection->getList());
            $this->aList = $oService->getShippingService();             
        }
        return $this->aList;
    }

    protected $sSelectionName;
    public function setSelectionName($sSelectionName){
        $this->sSelectionName = $sSelectionName;
    }
    public function getSelectionName(){
        return $this->sSelectionName;
    }

    public function getOrdersIds() {
        $mlOrdersIds = array();
        foreach ($this->getList() as $aOrder) {
            $mlOrdersIds[] = $aOrder['AmazonOrderID'];
        }
        return $mlOrdersIds;
    }
    
    public function getHead() {
        $aHead = array();
        $aHead['CarrierName'] = array(
            'title' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_Form_CarrierName'),
            'type' => 'carriername'
        );
        $aHead['ShippingServiceName'] = array(
            'title' => MLI18n::gi()->get('ML_LABEL_MARKETPLACE_SHIPPING_METHOD'),
            'type' => 'servicename'
        );
        $aHead['DeliveryTime'] = array(
            'title' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_Shippinmethod_DeliveryTime'),
            'type' => 'deliverytime'
        );
        $aHead['Amount'] = array(
            'title' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_Shippinmethod_Amount'),
            'type' => 'price'
        );
        $aHead['Comment'] = array(
            'title' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_Shippinmethod_Comment'),
            'type' => 'comment',
        );
        return $aHead;
    }
}

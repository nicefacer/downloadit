<?php

/**
 * select all products 
 * amazon-config: 
 *  - amazon.lang isset
 * magnalister.selectionname=='match
 */
class ML_Amazon_Model_List_Amazon_Order {

    protected $aList = null;
    protected $iCountTotal = 0;
    protected $aMixedData = array();
    protected $iFrom = 0;
    protected $iCount = 5;
    protected $sOrder = '';
    protected $sSearch = '';
    protected $sStatus = 'all';

    public function getFilters() {
        $aFilter = array(
            'search' => array(
                'name' => 'search',
                'type' => 'search',
                'placeholder' => 'Productlist_Filter_sSearch',
                'value' => $this->sSearch,
            ),
            'status' => array(
                'name' => 'status',
                'type' => 'select',
                'value' => $this->sStatus,
                'values' => array(
                    array(
                        'value' => 'all', 'label' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_Filter_Status_Default')
                    ),
                    array(
                        'value' => 'full', 'label' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_Filter_Status_Full')
                    ),
                    array(
                        'value' => 'partly', 'label' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_Filter_Status_Partly')
                    ),
                    array(
                        'value' => 'not', 'label' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_Filter_Status_Not')
                    ),
                )
            ),
        );
        return $aFilter;
    }

    private function prepareRequest() {
        $aRequest = array(
            'ACTION' => 'GetOrdersAcknowledgeStateForDateRange',
            'BEGIN' => date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 30),
        );
        if ($this->sSearch != '') {
            $aRequest['SEARCH'] = $this->sSearch;
        }
        $aRequest['CompletelyShipped'] = $this->sStatus;

        if ($this->sOrder != '') {
            $aSorting = explode('_', $this->sOrder);
            $aRequest['ORDERBY'] = $aSorting[0];
            if ($aSorting[1] == 'desc') {
                $aRequest['SORTORDER'] = 'DESC';
            } else {
                $aRequest['SORTORDER'] = 'ASC';
            }
        } else {
            $aRequest['ORDERBY'] = 'PurchaseDate';
            $aRequest['SORTORDER'] = 'DESC';
        }
        return $aRequest;
    }

    public function getList() {
        if ($this->aList === null) {
            $this->iCountTotal = 0;
            try {
                $aResponse = MagnaConnector::gi()->submitRequestCached($this->prepareRequest(), 0);
            } catch (MagnaException $oExc) {
                
            }
            if (!isset($aResponse['DATA']) || $aResponse['STATUS'] != 'SUCCESS' || !is_array($aResponse['DATA'])) {
                throw new Exception('There is a problem to get list of orders');
            } else {
                $this->aList = $aResponse['DATA'];
                foreach ($this->aList as &$aOrder) {
                    $aOrder['isselected'] = $this->isSelected($aOrder['AmazonOrderID']);
                }
                $this->iCountTotal = count($this->aList);
            }
        }
        if ($this->iCount !== null) {
            return array_slice($this->aList, $this->iFrom, $this->iCount);
        } else {
            return $this->aList;
        }
    }

    public function getOrdersIds($blPage = false) {
        $mlOrdersIds = array();
        if (!$blPage) {
            $this->iCount = null;
        }
        foreach ($this->getList() as $aOrder) {
            $mlOrdersIds[] = $aOrder['AmazonOrderID'];
        }
        return $mlOrdersIds;
    }

    public function getStatistic() {
        $this->getList();
        $aOut = array(
            'iCountPerPage' => $this->iCount,
            'iCurrentPage' => $this->iFrom / $this->iCount,
            'iCountTotal' => $this->iCountTotal,
            'aOrder' => array(
                'name' => 'PurchaseDate',
                'direction' => 'desc'
            )
        );
        return $aOut;
    }

    public function setLimit($iFrom, $iCount) {
        $this->iFrom = (int) $iFrom;
        $this->iCount = ((int) $iCount > 0) ? ((int) $iCount) : 5;
        return $this;
    }

    public function setFilters($aFilter) {
        $iPage = isset($aFilter['meta']['page']) ? ((int) $aFilter['meta']['page']) : 0;
        $iPage = $iPage < 0 ? 0 : $iPage;
        $iFrom = $iPage * $this->iCount;
        $this->iFrom = $iFrom;

        $this->sOrder = isset($aFilter['meta']['order']) ? $aFilter['meta']['order'] : '';
        $this->sSearch = isset($aFilter['search']) ? $aFilter['search'] : '';
        $this->sStatus = isset($aFilter['status']) ? $aFilter['status'] : 'all';


        return $this;
    }

    public function isSelected($sMlOrderId) {
        $i = MLDatabase::factory('globalselection')
                ->set('elementId', $sMlOrderId)
                ->set('selectionname', $this->getSelectionName())->getList()
                ->getQueryObject()
                ->getCount()
        ;
        return $i > 0;
    }

    public function getHead() {
        $aHead = array();
        $aHead['PurchaseDate'] = array(
            'title' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_Orderlist_PurchaseDate'),
            'order' => 'PurchaseDate',
//            'type' => 'simpleText',
        );
        $aHead['AmazonOrderID'] = array(
            'title' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_Orderlist_AmazonOrderID'),
            'order' => 'AmazonOrderID',
//            'type' => 'simpleText'
        );
        $aHead['BuyerName'] = array(
            'title' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_Orderlist_BuyerName'),
            'order' => 'BuyerName',
//            'type' => 'simpleText'
        );
        $aHead['Value'] = array(
            'title' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_Orderlist_Price'),
            'type' => 'price'
        );
        $aHead['CurrentStatus'] = array(
            'title' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_Orderlist_CurrentStatus'),
            'order' => 'CurrentStatus',
            'type' => 'currentstatus',
        );
        $aHead['CompletelyShipped'] = array(
            'title' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_Orderlist_ShippingStatus'),
            'type' => 'preparedStatus',
        );
        return $aHead;
    }

    public function isSelectable() {
        return true;
    }
    
    protected $sSelectionName;
    public function setSelectionName($sSelectionName){
        $this->sSelectionName = $sSelectionName;
    }
    public function getSelectionName(){
        return $this->sSelectionName;
    }

}

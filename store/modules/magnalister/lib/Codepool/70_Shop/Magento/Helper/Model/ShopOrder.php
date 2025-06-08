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

class ML_Magento_Helper_Model_ShopOrder{
    
    /**
     * @var array $aExistingOrderData
     */
    protected $aExistingOrderData = array();
    
    /**
     * @var Mage_Sales_Model_Order $oExistingShopOrder
     */
    protected $oExistingShopOrder = null;
    
    /**
     * @var array $aCurrentOrderData
     */
    protected $aCurrentOrderData = array();
    
    /**
     * @var Mage_Sales_Model_Order $oCurrentShopOrder
     */
    protected $oCurrentShopOrder = null;
    
    /**
     * @var ML_Magento_Model_Order $oOrder (ML_Shop_Model_Order_Abstract)
     */
    protected $oMlOrder = null;
    
    /**
     * collected totals of each item and ml-totals
     * @var array $aTotals
     */
    protected $aTotals = array(
        'Subtotal' => array(),
        'SubtotalInclTax' => array(),
        'GrandTotal' => array(),
        'TaxAmount' => array()
    );
    
    public function init() {
        $this->aExistingOrderData = array();
        $this->oExistingShopOrder = null;
        $this->aCurrentOrderData = array();
        $this->oCurrentShopOrder = null;
        $this->oMlOrder = null;
        $this->aTotals = array(
            'Subtotal' => array(),
            'SubtotalInclTax' => array(),
            'GrandTotal' => array(),
            'TaxAmount' => array()
        );
        return $this;
    }
    
    public function setCurrentOrderData($aData){
        $this->aCurrentOrderData = is_array($aData) ? $aData : array();
        return $this;
    }
    
    public function setMlOrder($oOrder) {
        $this->oMlOrder = $oOrder;
        if ($this->oMlOrder->exists()) {
            $this->oExistingShopOrder = Mage::getModel('sales/order')->loadByIncrementId($oOrder->get('current_orders_id'));
        }
        $this->aExistingOrderData = $this->oMlOrder->get('orderdata');
        return $this;
    }
    
    public function execute(){
        if ($this->checkForUpdate()) {
            $this->aCurrentOrderData = MLHelper::gi('model_service_orderdata_merge')->mergeServiceOrderData($this->aCurrentOrderData, $this->aExistingOrderData, $this->oMlOrder);
            return $this->update(); 
        } else {
            $this->aCurrentOrderData = MLHelper::gi('model_service_orderdata_merge')->mergeServiceOrderData($this->aCurrentOrderData, $this->aExistingOrderData, $this->oMlOrder);
            return $this->createOrder();
        }
    }
    
    protected function checkForUpdate(){
        if(
            count($this->aExistingOrderData) == 0
            || count($this->aCurrentOrderData['Products']) > 0
        ) {
            return false;
        }
        foreach ($this->aCurrentOrderData['Totals'] as $aNewTotal) {
            $blFound = false;
            foreach ($this->aExistingOrderData['Totals'] as $aCurrentTotal) {
                if ($aNewTotal['Type'] == $aCurrentTotal['Type']) {
                    $blFound = true;
                    if(
                        (float)$aNewTotal['Value'] != 0
                        && (
                            (float)$aCurrentTotal['Value'] != (float)$aNewTotal['Value']
//                            || // we don't need to compare the Tax , because it is false in ebay and most of the marketplaces
//                            (float)$aCurrentTotal['Tax'] != (float)$aNewTotal['Tax']
                        )
                    ) {
                        return false;
                    }
                }
            }
            if (!$blFound) {
                return false;
            }
        }
        return true;
    }
    
    public function update() {//products are equal
        if ($this->aCurrentOrderData['Order']['Payed']) {
            $this->createInvoice($this->oExistingShopOrder);
        }
        if ($this->aCurrentOrderData['Order']['Shipped']) {
            $this->createShipping($this->oExistingShopOrder, $this->aCurrentOrderData);
        }
        $this->addCustomerToOrder($this->oExistingShopOrder, $this->aCurrentOrderData['AddressSets']['Main']);
        $this
            ->addAddressToOrder($this->oExistingShopOrder, $this->aCurrentOrderData['AddressSets']['Billing'], 'Billing')
            ->addAddressToOrder($this->oExistingShopOrder, $this->aCurrentOrderData['AddressSets']['Shipping'], 'Shipping')
        ;
        try {
            $oCollection = Mage::getResourceModel('sales/order_status_collection');
            $oCollection->joinStates();
            $oCollection->getSelect()
                ->where('state_table.state is not null')
                ->where('main_table.status = ?', $this->aCurrentOrderData['Order']['Status'])
            ;
            $sState = $oCollection->getFirstItem()->getState();
            if (!empty($sState)) {
                $this->oExistingShopOrder->setData('state', $sState);
            }
        } catch (Exception $oEx) {
            //state not found use default
        }
        $this->oExistingShopOrder->setStatus($this->aCurrentOrderData['Order']['Status'])->save();
        return $this->aCurrentOrderData;
    }
    
    /**
     * sets values for base currency by currency rate
     * @param object $oObject Magento-Object
     * @param array $aMethods array of methods executes on $oObject
     * @return ML_Magento_Helper_Model_ShopOrder
     */
    protected function setBaseValues($oObject, $aMethods) {
        $fBaseToOrderRate = $this->oCurrentShopOrder->getBaseToOrderRate();
        if (!empty($fBaseToOrderRate)) {// division by zero
            $iRate = 1 / $fBaseToOrderRate;
            foreach ($aMethods as $sMethod) {
                $oObject->{'setBase'.$sMethod}($iRate * $oObject->{'get'.$sMethod}());
            }
        }
        return $this;
    }
    
    /**
     * add magna-totals to order eg. shipping, payment uses max tax of all products
     * @return ML_Magento_Helper_Model_ShopOrder
     */
    protected function addTotalsToOrder() {
        $fMaxPercent = 0.0;
        foreach ($this->oCurrentShopOrder->getItemsCollection() as $oOrderItem) {
            $fMaxPercent = max($fMaxPercent, $oOrderItem->getTaxPercent());
        }
        foreach($this->aCurrentOrderData['Totals'] as &$aTotal){
            switch($aTotal['Type']){
                case 'Shipping': {
                    $aTotal['Tax'] = $fMaxPercent;
                    $this->addShippingToOrder($aTotal);
                    break;
                }
                case 'Payment': {
                    $aTotal['Tax'] = $fMaxPercent;
                    $this->addPaymentToOrder($aTotal);
                    break;
                }
                default: { //add as article
                    $this->addProductToOrder(array(
                        'ItemTitle' => $aTotal['Code'] == '' ? $aTotal['Type'] : $aTotal['Code'],
                        'SKU' => isset($aTotal['SKU']) ? $aTotal['SKU'] : '',
                        'Price' => $aTotal['Value'],
                        'Tax' => array_key_exists('Tax', $aTotal) ? $aTotal['Tax'] : $fMaxPercent,
                        'Data' => isset($aTotal['Data']) ? $aTotal['Data'] : array(),
                        'Quantity' => 1,
                    ));
                }
            }
        }
        return $this;
    }
    
    /**
     * creates magento order 
     * @return array
     */
    protected function createOrder(){
        try{
            $oTransaction = Mage::getModel('core/resource_transaction');
            $oStore = MLShop::gi()->initMagentoStore(MLModul::gi()->getConfig('orderimport.shop'));//Mage::app()->getStore(MLModul::gi()->getConfig('orderimport.shop'));
            $this->oCurrentShopOrder = Mage::getModel('sales/order');
            $sGlobalCurrencyCode  = Mage::app()->getBaseCurrencyCode();
            $oBaseCurrency = $oStore->getBaseCurrency();
            $this->oCurrentShopOrder
                ->setStoreId($oStore->getId())
                ->setExt_order_id($this->aCurrentOrderData['MPSpecific']['MOrderID'])
                    
                ->setBaseCurrencyCode($oBaseCurrency->getCode())                        // base - currency which is set for current website.
                ->setGlobalCurrencyCode($sGlobalCurrencyCode)                           // global - currency which is set for default in backend
                ->setOrderCurrencyCode($this->aCurrentOrderData['Order']['Currency'])   // order - currency which was selected by customer
                ->setStoreCurrencyCode($oBaseCurrency->getCode())                       // store - all the time it was currency of website
                    
                ->setBaseToGlobalRate($oBaseCurrency->getRate($sGlobalCurrencyCode))
                ->setBaseToOrderRate($oBaseCurrency->getRate($this->aCurrentOrderData['Order']['Currency']))
                ->setStoreToBaseRate($oBaseCurrency->getRate($sGlobalCurrencyCode))
                ->setStoreToOrderRate($oBaseCurrency->getRate($this->aCurrentOrderData['Order']['Currency']))
            ;
            
            $this->addCustomerToOrder($this->oCurrentShopOrder, $this->aCurrentOrderData['AddressSets']['Main']);
            $this
                ->addAddressToOrder($this->oCurrentShopOrder, $this->aCurrentOrderData['AddressSets']['Billing'], 'Billing')
                ->addAddressToOrder($this->oCurrentShopOrder, $this->aCurrentOrderData['AddressSets']['Shipping'], 'Shipping')
            ;
            foreach($this->aCurrentOrderData['Products'] as $aProduct){
                $this->addProductToOrder($aProduct);
            }
            $this->addTotalsToOrder();
            if (!empty($this->aCurrentOrderData['Order']['Comments'])) {
                $this->oCurrentShopOrder->addStatusHistoryComment(nl2br($this->aCurrentOrderData['Order']['Comments'], false));
            }
            if (!empty($this->aCurrentOrderData['MPSpecific']['InternalComment'])) {
                $this->oCurrentShopOrder->addStatusHistoryComment(nl2br($this->aCurrentOrderData['MPSpecific']['InternalComment'], false));
            }
            if(!$this->oCurrentShopOrder->getPayment()){
                $orderPayment = Mage::getModel('sales/order_payment')
                    ->setStoreId($this->oCurrentShopOrder->getStoreId())
                    ->setCustomerPaymentId(0)
                    ->setMethod('magnalister')
                ;
                $aPaymentinfo = array();
                $aShowInformationInInvoice = MLModul::gi()->getConfig('order.information');
                if($aShowInformationInInvoice !== null && current($aShowInformationInInvoice)){
                     $aPaymentinfo[] = $this->aCurrentOrderData['MPSpecific']['InternalComment'];
                }
                if (isset($this->aCurrentOrderData['MPSpecific']['Payment']) && isset($this->aCurrentOrderData['MPSpecific']['Payment']['Code'])) {
                    $aPaymentinfo[] = array('Code' => $this->aCurrentOrderData['MPSpecific']['Payment']['Code']);
                }
                $orderPayment->setAdditionalInformation($aPaymentinfo);
                $this->oCurrentShopOrder->setPayment($orderPayment);
            }
            if($this->oExistingShopOrder!==null){
                $iExistingIncrementId = $this->oExistingShopOrder->getIncrementId();//parent id
                $iExistingOriginalIncrementId = $this->oExistingShopOrder->getOriginalIncrementId();// main id
                $iCurrentIncrementId = 
                    $iExistingOriginalIncrementId == null
                    ? $iExistingIncrementId.'-1'
                    : (
                        $iExistingOriginalIncrementId.'-'.(
                            (string)(
                                (int)(
                                    substr(
                                        $iExistingIncrementId,
                                        strlen($iExistingOriginalIncrementId)+1,
                                        strlen($iExistingIncrementId)
                                    )+1
                                )
                            )
                        )
                    )
                ;
                $this->oCurrentShopOrder
                    ->setIncrementId($iCurrentIncrementId)
                    ->setOriginalIncrementId($iExistingOriginalIncrementId == null ? $iExistingIncrementId : $iExistingOriginalIncrementId)
                    ->setRelationParentId($this->oExistingShopOrder->getId())
                    ->setRelationParentRealId($iExistingIncrementId)
                ;
                $this->oExistingShopOrder->setRelationChildRealId($iCurrentIncrementId);
                MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                    'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                    'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                    'incrementIds' => array(
                        'original' => $iExistingOriginalIncrementId,
                        'existing' => $iExistingIncrementId,
                        'current' => $iCurrentIncrementId,
                    ),
                ));
                $this->oMlOrder->set('current_orders_id', $iCurrentIncrementId);//important
            } else {
                $iIncrementId = Mage::getSingleton('eav/config')->getEntityType('order')->fetchNewIncrementId($this->oCurrentShopOrder->getStoreId());
                MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                    'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                    'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                    'NewIncrementId' => array(
                        'Shop' => MLModul::gi()->getConfig('orderimport.shop'),
                        'OrderStoreId' => $this->oCurrentShopOrder->getStoreId(),
                        'StoreId' => $oStore->getId(),
                        'IncrementId' => $iIncrementId
                    )
                ));
                $this->oCurrentShopOrder->setIncrementId($iIncrementId);
                $this->oMlOrder
                    ->set('orders_id', $iIncrementId)
                    ->set('current_orders_id', $iIncrementId)
                ;//important
            }
            $oTransaction->addObject($this->oCurrentShopOrder);
            $oTransaction->addCommitCallback(array($this->oCurrentShopOrder, 'place'));
            $oTransaction->addCommitCallback(array($this->oCurrentShopOrder, 'save'));
            $oTransaction->save();
            if ($this->oExistingShopOrder !== null) {
                $this->oExistingShopOrder
                    ->setRelationChildId($this->oCurrentShopOrder->getId())
                    ->cancel()
                    ->save()
                ;
            }
            $this->oMlOrder->save();
            try {
                if($this->aCurrentOrderData['Order']['Payed']){
                    $this->createInvoice($this->oCurrentShopOrder);
                }
            } catch(Exception $oEx) {
                MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                    'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                    'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                    'Exception' => array(
                        'class' => get_class($oEx),
                        'message' => $oEx->getMessage(),
                    ),
                ));
            }
            try {
                if($this->aCurrentOrderData['Order']['Shipped']){
                    $this->createShipping($this->oCurrentShopOrder, $this->aCurrentOrderData);
                }
            } catch(Exception $oEx) {
                MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                    'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                    'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                    'Exception' => array(
                        'class' => get_class($oEx),
                        'message' => $oEx->getMessage(),
                    ),
                ));
            }
            
            try {
                $oCollection = Mage::getResourceModel('sales/order_status_collection');
                $oCollection->joinStates();
                $oCollection->getSelect()
                    ->where('state_table.state is not null')
                    ->where('main_table.status = ?', $this->aCurrentOrderData['Order']['Status'])
                ;
                $sState = $oCollection->getFirstItem()->getState();
                
                if (!empty($sState)) {
                    $this->oCurrentShopOrder->setData('state', $sState);
                }
            } catch (Exception $oEx) {
                //state not found use default
            }
            $this->oCurrentShopOrder->setStatus($this->aCurrentOrderData['Order']['Status'])->setCreatedAt($this->aCurrentOrderData['Order']['DatePurchased'])->save();
        } catch(Exception $oEx) {
            MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                'Exception' => array(
                    'class' => get_class($oEx),
                    'message' => $oEx->getMessage(),
                ),
            ));
            throw $oEx;
        }
        return $this->aCurrentOrderData;
    }
    
    protected function createShipping($oOrder, $aData){
        try {
            if ($oOrder->canShip()) {
                //Create shipment
                $iShippingId = Mage::getModel('sales/order_shipment_api')
                     ->create($oOrder->getIncrementId(), array())
                ;
                foreach ($aData['Totals'] as $aTotal) {
                    if ($aTotal['Type'] == 'Shipping') {
                        if (isset($aTotal['Data']['Carrier'])) {
                            $sCarrier = $aTotal['Data']['Carrier'];
                        } else {
                            $sCarrier = MLModul::gi()->getMarketPlaceName().' ('.MLModul::gi()->getMarketPlaceId().')';
                            $sCarrierType = 'custom';
                        }
                        $sCarrierType = strtolower($sCarrier) == 'dhl' ? 'dhlint' : 'custom';
                        $sTracking = isset($aTotal['Data']['Trackingcode']) ? $aTotal['Data']['Trackingcode'] : $aData['MPSpecific']['MOrderID'];
                        break;
                    }
                }
                //Add tracking information
                Mage::getModel('sales/order_shipment_api')
                    ->addTrack($iShippingId, $sCarrierType ,$sCarrier, $sTracking)
                ;
            }
        } catch (Mage_Core_Exception $oEx) {
            MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                'Exception' => array(
                    'class' => get_class($oEx),
                    'message' => $oEx->getMessage(),
                ),
            ));
        }
        return $this;
    }
    
    protected function addCustomerToOrder($oOrder, $aData){
        $oCustomer = Mage::getModel('customer/customer');/* @var $oCustomer Mage_Customer_Model_Customer */
        $sCustomerGroupId = 
            MLModul::gi()->getConfig('CustomerGroup') === null 
            ? MLModul::gi()->getConfig('customergroup') 
            : MLModul::gi()->getConfig('CustomerGroup')
        ;
        $blGuest = empty($sCustomerGroupId);
        $oCustomer->setStore($oOrder->getStore());
        if (!$blGuest) {
            $oCustomer->loadByEmail($aData['EMailIdent']);
        }
        if ($oCustomer->isObjectNew()) {
            $aData['Password'] = uniqid('', true);
            foreach (array(
                'Gender'        => 'gender',
                'Firstname'     => 'firstname',
                'Lastname'      => 'lastname',
                'EMail'         => 'email',
                'Password'      => 'password',
             ) as $sInput => $sMethod) {
                if ($sInput == 'Gender') {
                    $aData[$sInput] = $this->translateMagnaToMagento('gender', $aData[$sInput]);
                }
                $oCustomer->{'set'.$sMethod}($aData[$sInput]);
            }
            $oCustomer->setStore($oOrder->getStore());
            $oCustomer->setGroupId($sCustomerGroupId);
            $oAddress = Mage::getModel('customer/address');
            foreach (array(
                'Firstname'     => 'firstname',
                'Lastname'      => 'lastname',
                'Company'       => 'company',
                'StreetAddress' => 'street',
                'Postcode'      => 'postcode',
                'City'          => 'city',
                'Suburb'        => 'region',
                'CountryCode'   => 'country_id',
                'Phone'         => 'telephone',
             ) as $sInput => $sMethod) {
                if ($sInput == 'Suburb') {
                    $aData[$sInput] = $this->translateMagnaToMagento('region', array('name' => $aData[$sInput], 'country' => $aData['CountryCode']));
                }
                $oAddress->{'set'.$sMethod}($aData[$sInput]);
            }
            $oCustomer->addAddress($oAddress);
            MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                'Info' => 'newCustomer'
            ));
        } else {
            $oCustomer->setEmail($aData['EMail']);
        }
        if (!$blGuest) {
            $oCustomer->save();
        }
        $oOrder                    
            ->setCustomer_email($aData['EMail'])
            ->setCustomerFirstname($aData['Firstname'])
            ->setCustomerLastname($aData['Lastname'])
            ->setCustomerGroupId($oCustomer->getGroupId())
            ->setCustomer_is_guest($blGuest ? 1 : 0)
            ->setCustomer($oCustomer)
        ;
        $this->aCurrentOrderData['AddressSets']['Main'] = $aData;
        return $this;
    }
    
    protected function addAddressToOrder($oOrder, $aData, $sAddressType){
        $aData['address_type'] = strtolower($sAddressType);
        $oAddress = $oOrder->{'get'.$sAddressType.'Address'}();
        if (!is_object($oAddress)) {
            $oAddress = Mage::getModel('sales/order_address'); 
        }
        /* @var $oAddress Mage_Sales_Model_Order_Address */
        foreach (array(
            //'Gender'=>
            'address_type'  => 'address_type',
            'Firstname'     => 'firstname',
            'Lastname'      =>'lastname',
            'Company'       =>'company',
            'StreetAddress' =>'street',
            'Postcode'      =>'postcode',
            'City'          =>'city',
            'Suburb'        =>'region',
            'CountryCode'   =>'country_id',
            'Phone'         =>'telephone',
            'EMail'         =>'email',
            //'DateAdded'=>
            //'LastModified'=>
            
        ) as $sInput => $sMethod) {
            if($sInput == 'Suburb') {
                $aData[$sInput] = $this->translateMagnaToMagento('region', array('name' => $aData[$sInput], 'country' => $aData['CountryCode']));
            }
            $oAddress->{'set'.$sMethod}($aData[$sInput]);
        }
        $oOrder->{'set'.$sAddressType.'Address'}($oAddress);
        return $this;
    }
    
    protected function addProductToOrder($aProduct){
        $oProduct = MLProduct::factory();
        $oOrderItem = Mage::getModel('sales/order_item')
            ->setStoreId($this->oCurrentShopOrder->getStoreId())
            ->setQuoteItemId(0)
            ->setQuoteParentItemId(NULL)
            ->setQtyBackordered(NULL)
            ->setName($aProduct['ItemTitle'])
            ->setWeeeTaxApplied(serialize(array()))
            ->setQtyOrdered($aProduct['Quantity'])
        ;
        if (isset($aProduct['SKU'])) {
            $oOrderItem->setSku($aProduct['SKU']);
        }
        if (isset($aProduct['SKU']) && $oProduct->getByMarketplaceSKU($aProduct['SKU'])->exists()) {
            if (isset($aProduct['StockSync']) && $aProduct['StockSync']) {
                $oProduct->setStock($oProduct->getStock() - $aProduct['Quantity']);
            }
            $fTaxPercent = $oProduct->getTax($this->aCurrentOrderData['AddressSets']['Shipping']);
            if (
                $oProduct->get('parentid') 
                && $oProduct->get('ProductsId') != $oProduct->getParent()->get('ProductsId')
            ) {
                $oParentOrderItem = clone $oOrderItem;
                $oParentProduct = $oProduct->getParent();
                $oParentOrderItem
                    ->setProductId($oParentProduct->entity_id)
                    ->setProductType($oParentProduct->type_id)
                    ->setSku($oParentProduct->getSku());
                ;            
                $this->oCurrentShopOrder->addItem($oParentOrderItem);
                $oOrderItem->setParentItem($oParentOrderItem);
            }
            $oOrderItem
                ->setProductId($oProduct->entity_id)
                ->setProductType($oProduct->type_id)
            ;
        } else {
            $oOrderItem->setProductType('simple');
            $fDefaultProductTax = 
                MLModul::gi()->getConfig('mwst.fallback') === null 
                ? MLModul::gi()->getConfig('mwstfallback') 
                : MLModul::gi()->getConfig('mwst.fallback')
            ;
            $fTaxPercent = $aProduct['Tax'] === false ? $fDefaultProductTax : $aProduct['Tax'];
        }
        /* @var $oOrderItem mage_sales_model_order_item */
        if (isset($aProduct['Data'])) {//adding some options (data is only set by totals)
            $aOptions = array();
            foreach ($aProduct['Data'] as $sOptionKey => $sOptionValue) {
                $aOptions[] = array(
                    'label' => $sOptionKey,
                    'value' => $sOptionValue,
                    'print_value' => $sOptionValue,
                    'option_value' => $sOptionValue,
                    'optione_type' => 'field',
                    'custom_view' => false
                );
            }
            $oOrderItem->setProductOptions(array('options' => $aOptions));
        }
        //price
        $fGros = $aProduct['Price'];
        $fNet = MLPrice::factory()->calcPercentages($fGros, null, $fTaxPercent);
        
        $this->oCurrentShopOrder->addItem($oOrderItem);
        $oPriceOrderItem = isset($oParentOrderItem) ? $oParentOrderItem : $oOrderItem;
        $oPriceOrderItem
            // global
            ->setTaxPercent($fTaxPercent)
            // price for current currency
            ->setOriginalPrice($fNet)
            ->setPrice($fNet)
            ->setPriceInclTax($fGros)
            ->setTaxAmount(($fGros - $fNet) * $aProduct['Quantity'])
            ->setRowTotal($fNet * $aProduct['Quantity'])
            ->setRowTotalInclTax($fGros * $aProduct['Quantity'])
        ;
        $this->setBaseValues($oPriceOrderItem, array('OriginalPrice', 'Price', 'PriceInclTax', 'TaxAmount', 'RowTotal', 'RowTotalInclTax'));
        $this->addTotals($this->oCurrentShopOrder, $oPriceOrderItem->getRowTotal(), $oPriceOrderItem->getRowTotalInclTax(), $oPriceOrderItem->getRowTotalInclTax(), $oPriceOrderItem->getTaxAmount());
        return $this;
    }
    
    protected function addTotals ($oOrder, $fSubtotal = 0.0, $fSubtotalInclTax = 0.0, $fGrandTotal = 0.0, $fTaxAmount = 0.0) {
        $this->aTotals['Subtotal'][] = $fSubtotal;
        $this->aTotals['SubtotalInclTax'][] = $fSubtotalInclTax;
        $this->aTotals['GrandTotal'][] = $fGrandTotal;
        $this->aTotals['TaxAmount'][] = $fTaxAmount;
        $oOrder
            ->setSubtotal       (array_sum($this->aTotals['Subtotal']))
            ->setSubtotalInclTax(array_sum($this->aTotals['SubtotalInclTax']))
            ->setGrandTotal     (array_sum($this->aTotals['GrandTotal']))
            ->setTaxAmount      (array_sum($this->aTotals['TaxAmount']))
        ;
        $this->setBaseValues($oOrder, array('Subtotal', 'SubtotalInclTax', 'GrandTotal', 'TaxAmount'));
        return $this;
    }
    
    /**
     * 
     * @param type $oOrder
     * @param type $aData
     * @return $this
     */
    protected function addShippingToOrder($aData){
        $fGross = (float)max($aData['Value'], $this->oCurrentShopOrder->getShippingInclTax());
        $fPercent = $aData['Tax'];
        try {
            $fNet = MLPrice::factory()->calcPercentages($fGross, null, $fPercent);
            $this->oCurrentShopOrder
                ->setShipping_method('flatrate_flatrate')
                ->setShippingDescription($aData['Code'])
                ->setShippingTaxAmount($fGross - $fNet)
                ->setShippingInclTax($fGross)
                ->setShippingAmount($fNet)
            ;
            $this->setBaseValues($this->oCurrentShopOrder, array('ShippingAmount', 'ShippingTaxAmount', 'ShippingInclTax'));
            $this->addTotals($this->oCurrentShopOrder, 0.0, 0.0, $this->oCurrentShopOrder->getShippingInclTax(), $this->oCurrentShopOrder->getShippingTaxAmount());
        } catch(Exception $oEx) {
            MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                'Exception' => array(
                    'class' => get_class($oEx),
                    'message' => $oEx->getMessage(),
                ),
            ));
        }
        return $this;
    }
    
    /**
     * add payment to order, if payment have cost, add as product
     * @param array $aData
     * @return ML_Magento_Helper_Model_ShopOrder
     */
    protected function addPaymentToOrder($aData){
        if ($aData['Value'] != 0) {
            foreach ($this->oCurrentShopOrder->getItemsCollection() as $oItem) {
                if ( $oItem->getName()=='Payment' ) {
                    if( $aData['Value'] < $oItem->getBasePrice() + $oItem->getTaxAmount()) {
                        $aData['Value'] = $oItem->getBasePrice() + $oItem->getTaxAmount();
                        $aData['Tax'] = $oItem->getTaxPercent();
                    }
                    $oItem->delete();
                }
            }
            return $this->addProductToOrder(array(
                'ItemTitle' => 'Payment',
                'Price' => $aData['Value'],
                'Quantity' => 1,
                'Tax' => $aData['Tax']
            ));
        }
        return $this;
    }
    
    protected function createInvoice($oOrder){
        try {
            if (!$oOrder->canInvoice()) {
                Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
            }
            $oInvoice = Mage::getModel('sales/service_order', $oOrder)->prepareInvoice();
            
            if (!$oInvoice->getTotalQty()) {
                Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
            }

            $oInvoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
            //Or you can use
            //$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
            $oInvoice->register();
            $oTransaction = Mage::getModel('core/resource_transaction')
                ->addObject($oInvoice)
                ->addObject($oInvoice->getOrder())
            ;
            $oTransaction->save();
        } catch (Mage_Core_Exception $oEx) {
            MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                'Exception' => array(
                    'class' => get_class($oEx),
                    'message' => $oEx->getMessage(),
                ),
            ));
        }
        return $this;
    }
    
    protected function translateMagnaToMagento ($sType, $mValue) {
        if ($sType == 'region') {
            $oRegion = Mage::getModel('directory/region');/* @var Mage_Directory_Model_Region $oRegion */
            $oRegion->loadByName($mValue['name'], $mValue['country']);
            return $oRegion->getRegionId();
        } elseif ($sType == 'gender') {
            switch ($mValue) {
                case 'male': {
                    $iGender = 1;
                    break;
                }
                case 'female': {
                    $iGender = 2;
                }
                default: {
                    $iGender = 0;
                }
            }
            return $iGender;
        }
        
    }
    
}
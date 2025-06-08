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
 * (c) 2010 - 2015 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

class ML_Shopware_Helper_Model_ShopOrder {

    /**
     * @var Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    protected $oShopwareDB = null;

    /**
     * @var int $iCustomerId
     */
    protected $iCustomerId = null;

    /**
     * @var array $aCurrentData
     */
    protected $aCurrentData = array();

    /**
     * @var \Shopware\Models\Order\Order $oCurrentOrder
     */
    protected $oCurrentOrder = null;

    /**
     * @var array $aNewData
     */
    protected $aNewData = array();

    /**
     * @var array $aNewProduct
     */
    protected $aNewProduct = array();

    /**
     * @var \Shopware\Models\Order\Order $oNewOrder
     */
    protected $oNewOrder = null;

    /**
     * @var ML_Shopware_Model_Order $oOrder
     */
    protected $oOrder = null;

    /**
     * @var ML_Shopware_Model_Price $oPrice
     */
    protected $oPrice = null;

    /*
     * construct
     */
    public function __construct() {
        $this->oPrice = MLPrice::factory();
    }

    /**
     * @return Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    protected function getShopwareDb() {
        if ($this->oShopwareDB === null) {
            $this->oShopwareDB = Shopware()->Db();
        }
        return $this->oShopwareDB;
    }

    /**
     * get configured shop from module configuration and set oShop object
     *  if there comes a error: Fatal error:
     *      Call to a member function toArray() on a non-object in
     *      ./engine/Shopware/Plugins/Default/Core/System/Bootstrap.php on line 95
     *  perhaps we need to init doctrine "Shopware()->Models()->Clear()"
     *      $system->sCurrency = $shop->getCurrency()->toArray(); //in this case $shop->getCurrency() === null
     */
    public function selectShop() {
        $oModul = MLModul::gi();
        $iShopId = $oModul->getConfig('orderimport.shop');
        if( $iShopId === null ){
            $iShopId = $oModul->getConfig('lang');
        }
        $oShop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->find($iShopId);
        Shopware()->Bootstrap()->registerResource('Shop', $oShop);
    }
    
    /**
     * Sql method for extended logging
     * @param string $sQuery
     * @return \ML_Shopware_Helper_Model_ShopOrder
     * @throws Exception rethrow Exception
     */
    protected function executeSql($sQuery, $aArray = array()) {
        try {
//            MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
//                'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
//                'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
//                'Query' => array($sQuery => $aArray)
//            ));
            $this->getShopwareDb()->query($sQuery, $aArray);
            return true;
        } catch (Exception $oEx) {
            MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                'Exception' => $oEx->getMessage(),
                'Query' => array($sQuery => $aArray),
            ));
            throw $oEx;
        }
    }

    /**
     * set new order data
     */
    public function setNewOrderData($aData) {
        $this->aNewData = is_array($aData) ? $aData : array();
        return $this;
    }


    /**
     * set oder object in initializing the order helper
     * @param type $oOrder
     * @return \ML_Shopware_Helper_Model_ShopOrder
     */
    public function setOrder($oOrder) {
        $this->oOrder = $oOrder;
        $this->oCurrentOrder = null;
        $this->iCustomerId = null;
        if ($this->oOrder->exists()) {
            $this->oCurrentOrder = Shopware()->Models()->getRepository('\Shopware\Models\Order\Order')->find($oOrder->get('current_orders_id'));
        }
        $this->aCurrentData = $oOrder->get('orderdata');
        return $this;
    }

    /**
     * initializing order import and update
     * @return array
     */
    public function shopOrder() {
        if (count($this->aCurrentData) == 0) {
            $aReturnData = $this->createOrder();
        } elseif (!is_object($this->oCurrentOrder)) {// if order doesn't exist in shop  we create new order 
            $this->aNewData = MLHelper::gi('model_service_orderdata_merge')->mergeServiceOrderData($this->aNewData, $this->aCurrentData, $this->oOrder);
            $aReturnData =  $this->createOrder();
        } else {//update order if exist
            $this->iCustomerId = $this->oCurrentOrder->getCustomer()->getId();
            if ($this->checkForUpdate()) {
                $this->aNewData = MLHelper::gi('model_service_orderdata_merge')->mergeServiceOrderData($this->aNewData, $this->aCurrentData, $this->oOrder);
                $this // updatePaymentMethod()  and updatePaymentStatus()  executed in ML_ShopwareEbay_Model_Service_UpdateOrders                       
                        ->updateOrderStatus();
                $aReturnData = $this->aNewData;
            } else {
                $this->aNewProduct = $this->aNewData['Products'];
                 $this->aNewData = MLHelper::gi('model_service_orderdata_merge')->mergeServiceOrderData($this->aNewData, $this->aCurrentData, $this->oOrder);
                 $aReturnData = $this->updateOrder();
            }
        }
        return $aReturnData;
    }

    /**
     * check if order should be updated or should we added or extended
     * @return boolean
     */
    protected function checkForUpdate() {
        if (count($this->aNewData['Products']) > 0) {
            return false;
        }
        foreach (array('Shipping', 'Billing') as $sAddressType) {//in shopware we use just shipping and billing address , we use just email of main address 
            foreach (array('Gender', 'Firstname', 'Company', 'Street', 'Housenumber', 'Postcode', 'City', 'Suburb', 'CountryCode', 'Phone', 'EMail', 'DayOfBirth',) as $sField) {
                if (    (isset($this->aNewData['AddressSets'][$sAddressType][$sField]) && !isset($this->aCurrentData['AddressSets'][$sAddressType][$sField]))
                    || (isset($this->aNewData['AddressSets'][$sAddressType][$sField]) && $this->aNewData['AddressSets'][$sAddressType][$sField] != $this->aCurrentData['AddressSets'][$sAddressType][$sField])
                ) {
                    return false;
                }
            }
        }
        foreach ($this->aNewData['Totals'] as $aNewTotal) {
            $blFound = false;
            foreach ($this->aCurrentData['Totals'] as $aCurrentTotal) {
                if ($aNewTotal['Type'] == $aCurrentTotal['Type']) {
                    $blFound = true;
                    if (   (float) $aCurrentTotal['Value'] != (float) $aNewTotal['Value']
                        //|| // we don't need to compare the Tax , because it is false in ebay and most of the marketplaces
                        //(float) $aCurrentTotal['Tax'] != (float) $aNewTotal['Tax']
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

    /**
     * get random number as transactionid , we have this function individually because some customer need to change this behavior by overriding this function
     * @return string
     */
    protected function getTransactionId() {
        $aPayment = $this->getTotal('Payment');
        if(/*isset($aPayment['Code']) && $aPayment['Code'] == 'PayPal' && *///we cannot check for Code because it is already changed in normalize class
                isset($aPayment['ExternalTransactionID']) && !empty($aPayment['ExternalTransactionID'])){
            return $aPayment['ExternalTransactionID'];
        }else{
            return md5(uniqid(mt_rand(), true));
        }
    }

    /**
     * update payment method
     */
    public function updatePaymentMethod() {
        if (is_object($this->oCurrentOrder)) {//payment can update from ML_ShopwareEbay_Model_Service_UpdateOrders so we should check existing of order
            $sSql = 'UPDATE `s_order` SET' ;
            $aPayment = $this->getTotal('Payment');
            if(/*isset($aPayment['Code']) && $aPayment['Code'] == 'PayPal' && *///we cannot check for Code because it is already changed in normalize class
             isset($aPayment['ExternalTransactionID']) && !empty($aPayment['ExternalTransactionID'])){
                $sSql .= ' transactionID = ' . $this->getShopwareDB()->quote($aPayment['ExternalTransactionID']) .',' ;
            }
            
            $sSql .= ' paymentID = ' . $this->getPaymentMethod($aPayment['Code']) .
                    ' WHERE id = ' . $this->oCurrentOrder->getId();
            
            $this->executeSql(
                    $sSql
            );
            return $this;
        } else {
            throw new Exception('order doesn\'t exist in shop');
        }
    }

    /**
     * update shipping method
     */
    public function updateShippingMethod() {
        if (is_object($this->oCurrentOrder)) {//shipping can update from ML_ShopwareEbay_Model_Service_UpdateOrders so we should check existing of order                        
            $iDispatchId = $this->getDispatch();
            //updating shipping method
            $this->executeSql(
                'UPDATE `s_order` SET' .
                ' dispatchID = ' . $iDispatchId .
                ' WHERE id = ' . $this->oCurrentOrder->getId()
            );
            return $this;
        } else {
            throw new Exception('order doesn\'t exist in shop');
        }
    }
    
    /**
     * update payment status
     * @return \ML_Shopware_Helper_Model_ShopOrder
     * @throws Exception
     */
    public function updatePaymentStatus() {
        if (is_object($this->oCurrentOrder)) {
            $aIsActiv = MLModul::gi()->getConfig('update.paymentstatus');
            if (
                    !isset($aIsActiv) //if someone didn't set this configuration , it update payment status
                    || 
                    ($aIsActiv['val'] && in_array($this->oOrder->getShopPaymentStatus(), MLModul::gi()->getConfig('updateable.paymentstatus'), true))
                ) {
                try {
                    $aData = $this->aNewData;
                    $oShopwareOrder = $this->oCurrentOrder;
                    //updating payment status
                    if (isset($aData['Order']['PaymentStatus']) && !empty($aData['Order']['PaymentStatus']) && ((int) $aData['Order']['PaymentStatus']) !== $oShopwareOrder->getPaymentStatus()->getId()) {
                        Shopware()->Modules()->Order()->setPaymentStatus($oShopwareOrder->getId(), (int) $aData['Order']['PaymentStatus'], false);
                    }
                } catch (Exception $oExc) {
                    MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'),  array(
                        'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                        'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                        'Exception' => $oExc->getMessage()
                    ));
                }
            }
        } else {
            throw new Exception('order doesn\'t exist in shop');
        }
        return $this;
    }

    /**
     * update order status
     */
    public function updateOrderStatus() {
        try {
            Shopware()->Models()->clear();
            Zend_Session::$_unitTestEnabled = true; //if it is not true , it make problem in session creation in fronturl call
            $aData = $this->aNewData;
            if (is_object($this->oCurrentOrder)) {
                $oShopwareOrder = $this->oCurrentOrder;
                //updating order status
                $iNewOrderStatus = (int) $aData['Order']['Status'];
                if ($iNewOrderStatus !== $oShopwareOrder->getOrderStatus()->getId()) {
                    Shopware()->Modules()->Order()->setOrderStatus($oShopwareOrder->getId(), $iNewOrderStatus, false);
                }
            }
        } catch (Exception $oExc) {
            MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'),  array(
                'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                'Exception' => $oExc->getMessage()
            ));
        }
    }

    /**
     * this functions is only implemented to prevent errors for duplicated of code
     */
    protected function addProductsAndTotals($iOrderId, $sOrderNumber, &$aData, $oShopwareModel) {
        $fTotalTaxExcl = $fTotalTaxIncl = $fShippingTaxExcl = $fShippingTaxIncl = $fTotalPriceProductTaxIncl = $fTotalPriceProductTaxExcl = 0.00;

        //calculate quantity (when order is merged we can have several product with same sku)
        $aCurrentQty = array();
        foreach ($aData['Products'] as $aNewProduct){
            if (isset($aCurrentQty[$aNewProduct['SKU']])) {
                $aCurrentQty[$aNewProduct['SKU']] += (int)$aNewProduct['Quantity'];
            }else{
                $aCurrentQty[$aNewProduct['SKU']] = (int)$aNewProduct['Quantity'];
            }
        }
        // add products
        if (count($aData['Products']) > 0) {
            foreach ($aData['Products'] as $aProduct) {
                //reduction quantity in merged order if needed
                if($aCurrentQty[$aProduct['SKU']] != 0){
                    foreach (isset($this->aCurrentData['Products']) ? $this->aCurrentData['Products'] : array() as $aOldProduct) {
                        if ($aProduct['SKU'] == $aOldProduct['SKU']) {
                            $aCurrentQty[$aProduct['SKU']] -= $aOldProduct['Quantity'];
                        }
                    }
                }
                $fPPrice = (float)$aProduct['Price'];
                $iPQty = (int)$aProduct['Quantity'];
                $fTotalTaxIncl += $fPPrice * $iPQty;
                $fTotalPriceProductTaxExcl += $fPPrice * $iPQty;
                $aProduct['Modus'] = 0;
                $fProductPriceWithoutTax = $this->addProductToOrder($iOrderId, $sOrderNumber, $aProduct, $aData['Order']['Status'], $aCurrentQty[$aProduct['SKU']]);
                $aCurrentQty[$aProduct['SKU']] = 0;//in merged order we set quantity for each Sku just one time
                $fTotalTaxExcl += $fProductPriceWithoutTax * $iPQty;
                $fTotalPriceProductTaxIncl += $fProductPriceWithoutTax * $iPQty;
            }
        }

        $fMaxTaxRate = $this->getMaxTaxRateFromProducts($iOrderId);
        // add orders totals
        foreach ($aData['Totals'] as &$aTotal) {
            switch ($aTotal['Type']) {
                case 'Shipping': {
                    $fShippingTaxIncl += (float) $aTotal['Value'];
                    $fTotalTaxIncl += $fShippingTaxIncl;
                    $aTotal['Tax'] = $fMaxTaxRate;
                    $fShippingPriceWithoutTax = $this->oPrice->calcPercentages($fShippingTaxIncl, null, $aTotal['Tax']);
                    $iNumberOfFractionalPart = MLSHOPWARE_VERSION >= 5 ? 4 : 2;
                    $fShippingTaxExcl += round($fShippingPriceWithoutTax, $iNumberOfFractionalPart);
                    $fTotalTaxExcl += round($fShippingPriceWithoutTax, $iNumberOfFractionalPart);
                    break;
                }
                case 'Payment': {
                    if (intval($aTotal['Value']) != 0) {
                        $sPaymentMethod = $aTotal['Type'].'_'.(isset($aTotal['Code']) ? $aTotal['Code'] : '');
                        if (isset($aTotal['Code']) && is_numeric($aTotal['Code'])) {
                            $oPayment = $oShopwareModel->getRepository('Shopware\Models\Payment\Payment')->find($aTotal['Code']);
                            if (is_object($oPayment)) {
                                $sPaymentMethod = $oPayment->getDescription();
                            }
                        }
                        $fTotalTaxIncl += (float)$aTotal['Value'];
                        $fTotalPriceProductTaxExcl += (float)$aTotal['Value'];
                        $aTotal['Tax'] = $fMaxTaxRate;
                        $fProductPriceWithoutTax = $this->checkPayment($iOrderId, '', array(
                            'ItemTitle' => $sPaymentMethod,
                            'SKU' => '',
                            'Price' => $aTotal['Value'],
                            'Tax' => $aTotal['Tax'],
                            'Data' => isset($aTotal['Data']) ? $aTotal['Data'] : array(),
                            'Quantity' => 1,
                            'Modus' => 4,
                        ), $aData['Order']['Status']);
                        $fTotalTaxExcl += $fProductPriceWithoutTax;
                        $fTotalPriceProductTaxIncl += $fProductPriceWithoutTax;
                    }
                    break;
                }
                default: {
                    if (intval($aTotal['Value']) != 0) {
                        $fTotalTaxIncl += (float)$aTotal['Value'];
                        $fTotalPriceProductTaxExcl += (float)$aTotal['Value'];
                        $fProductPriceWithoutTax = $this->addProductToOrder($iOrderId, $sOrderNumber, array(
                            'ItemTitle' => (isset($aTotal['Code']) && $aTotal['Code'] != '') ? $aTotal['Code'] : $aTotal['Type'],
                            'SKU' => isset($aTotal['SKU']) ? $aTotal['SKU'] : '',
                            'Price' => $aTotal['Value'],
                            'Tax' => array_key_exists('Tax', $aTotal) ? $aTotal['Tax'] : $fMaxTaxRate,
                            'Data' => isset($aTotal['Data']) ? $aTotal['Data'] : array(),
                            'Quantity' => 1,
                            'Modus' => 4,
                        ), $aData['Order']['Status']);
                        $fTotalTaxExcl += $fProductPriceWithoutTax;
                        $fTotalPriceProductTaxIncl += $fProductPriceWithoutTax;
                    }
                    break;
                }
            }
        }

        //update order data
        $this->executeSql("
            UPDATE ".$oShopwareModel->getClassMetadata('Shopware\Models\Order\Order')->getTableName()."
               SET invoice_amount = ?,
                   invoice_amount_net = ?,
                   invoice_shipping = ?,
                   invoice_shipping_net = ?
             WHERE id = ?
        ", array(
            $fTotalTaxIncl,
            $fTotalTaxExcl,
            floatval($fShippingTaxIncl),
            floatval($fShippingTaxExcl),
            $iOrderId,
        ));
    }

    /**
     * update existed order
     * @return array
     * @throws Exception
     */
    public function updateOrder() {
        $oShopwareModel = Shopware()->Models();
        $this->selectShop();
        $aData = $this->aNewData;
        $fTotalTaxExcl = $fTotalTaxIncl = $fShipingTaxExcl = $fShipingTaxIncl = $fTotal_products = $fTotal_products_wt = 0.00;

        //update payment method
        $this->updatePaymentMethod();
        //update shipping method
        $this->updateShippingMethod();
        //update payment status 
        $this->updatePaymentStatus();
        //update order statuses 
        $this->updateOrderStatus();

        $aAddresses = $aData['AddressSets'];
        if (empty($aAddresses['Main'])) {
            throw new Exception("Main address is empty");
        }
        $sOrderNumber = $this->oCurrentOrder->getNumber();
        $aPayment = $this->getTotal('Payment');
        $iCustomerPaymentID = $this->getPaymentMethod($aPayment['Code'], true);
        $iCustomerPaymentID = empty($iCustomerPaymentID) ? $this->getPaymentMethod($aPayment['Code'], false) : $iCustomerPaymentID;
        $this->addCustomerToOrder($aAddresses, $iCustomerPaymentID);
        //update address data
        foreach (array('Shipping', 'Billing') as $sAddressType) {
            $this->{"update" . $sAddressType . "Address"}();
        }

        $iOrderId = $this->oCurrentOrder->getId();
        $this->executeSql('DELETE od, oda from `s_order_details` od inner join `s_order_details_attributes` oda on oda.detailID = od.id WHERE od.orderID = '.$iOrderId);

        // products and totals data will be added in this function
        $this->addProductsAndTotals($iOrderId, $sOrderNumber, $aData, $oShopwareModel);

        $this->oOrder->set('current_orders_id', $iOrderId); //important
        $aData['ShopwareOrderNumber'] = $sOrderNumber;
        return $aData;
    }

    /**
     * create a new order by magnalister order data
     * @return array
     * @throws Exception
     */
    public function createOrder() {
        $oShopwareModel = Shopware()->Models();
        $this->selectShop();
        $aData = $this->aNewData;
        $fTotalTaxExcl = $fTotalTaxIncl = $fShipingTaxExcl = $fShipingTaxIncl = $fTotal_products = $fTotal_products_wt = 0.00;
        $aAddresses = $aData['AddressSets'];

        if (empty($aAddresses['Main'])) {// add new order when Main address is filled
            throw new Exception('main address is empty');
        }

        if (count($aData['Products']) <= 0) {// add new order when order has any product
            throw new Exception('product is empty');
        }

        $oShopwareOrder = Shopware()->Modules()->Order();
        $sOrderNumber = $oShopwareOrder->sGetOrderNumber();
        $aPayment = $this->getTotal('Payment');
        $iCustomerPaymentID = $this->getPaymentMethod($aPayment['Code'], true);
        $iCustomerPaymentID = empty($iCustomerPaymentID) ? $this->getPaymentMethod($aPayment['Code'], false) : $iCustomerPaymentID;
        $iCustomerNumber = $this->addCustomerToOrder($aData['AddressSets'], $iCustomerPaymentID);//importand I pass main data object to set password of customer

        $iDispatchId = $this->getDispatch();
        $iCleared = $this->getPaymentStatus();
        $fNet = 0;
        $iTransactionID = $this->getTransactionId();
        
        /**
         * @todo evaluate ustid if neccessary (eg.: Shopware_Controllers_Frontend_Checkout::getUserData())
         * @var int $iTaxfree
         */
        $iTaxfree = $this->getCountry($aAddresses['Shipping'])->getTaxFree() ? 1 : 0;
        $iTemporaryID = uniqid(mt_rand(), true);
        $iShopId = Shopware()->Shop()->getId();
        $oCurrency = $oShopwareModel->getRepository('\Shopware\Models\Shop\Currency')->findOneBy(array('currency' => $aData['Order']['Currency']));
        if (!is_object($oCurrency)) {
            $sMessage = MLI18n::gi()->get('Shopware_Orderimport_CurrencyCodeDontExistsError', array(
                'mpOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                'ISO' => $aData['Order']['Currency'])
            );
            MLErrorLog::gi()->addError(-1, ' ', $sMessage, array('MOrderID' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId')));
            throw new Exception($sMessage);
        }
        //show  in order detail
        $sInternalComment = isset($aData['MPSpecific']['InternalComment']) ? $aData['MPSpecific']['InternalComment'] : '';
        //show in order detail and invoice pdf 
        $sCustomerComment = '';
        $aShowInformationInInvoice = MLModul::gi()->getConfig('order.information');
        if ($aShowInformationInInvoice !== null && current($aShowInformationInInvoice)) {
            $sCustomerComment .= isset($aData['MPSpecific']['InternalComment']) ? $aData['MPSpecific']['InternalComment'] : '';
        }

        /* @var $oCurrency \Shopware\Models\Shop\Currency */
        $iPaymentID = $this->getPaymentMethod($aPayment['Code']);
        $blOrderResult = $this->executeSql("
            INSERT INTO " . $oShopwareModel->getClassMetadata('Shopware\Models\Order\Order')->getTableName() . "
                    SET ordernumber = ?, userID = ?, invoice_amount = ?, invoice_amount_net = ?, invoice_shipping = ?,
                        invoice_shipping_net = ?, ordertime = ?, status = ?, cleared = ?, paymentID = ?,
                        transactionID = ?, customercomment = ?, internalcomment = ?, net = ?, taxfree = ?,
                        partnerID = ?, temporaryID = ?, referer = ?, language = ?, dispatchID = ?,
                        currency = ?, currencyFactor = ?, subshopID = ?, remote_addr = ?
        ", array(
            $sOrderNumber, $this->iCustomerId, $fTotalTaxIncl,
            $fTotalTaxExcl, floatval($fShipingTaxIncl), floatval($fShipingTaxExcl),
            $aData['Order']['DatePurchased'], $aData['Order']['Status'], $iCleared,
            $iPaymentID, $iTransactionID, $sCustomerComment, $sInternalComment, $fNet, $iTaxfree, '',
            $iTemporaryID, '', $iShopId, $iDispatchId, $oCurrency->getCurrency(),
            $oCurrency->getFactor(), Shopware()->Shop()->getId(), ((string) $_SERVER['REMOTE_ADDR'])
        ));
        if (!$blOrderResult) {
            throw new Exception('there is a problem to insert order data');
        }
        $iOrderId = $this->getShopwareDb()->lastInsertId();
        $sAttributeSql = "
            INSERT INTO s_order_attributes (orderID, attribute1, attribute2, attribute3, attribute4, attribute5, attribute6)
                 VALUES ($iOrderId ,'','','','','','')
       ";

        /*
          modus 0 = default article
          modus 1 = premium articles
          modus 2 = voucher
          modus 3 = customergroup discount
          modus 4 = payment surcharge / discount
          modus 10 = bundle discount
          modus 12 = trusted shops article
         */ 
        $this->executeSql($sAttributeSql);

        // products and totals data will be added in this function
        $this->addProductsAndTotals($iOrderId, $sOrderNumber, $aData, $oShopwareModel);

        $this->oOrder->set('orders_id', $iOrderId);
        $this->oOrder->set('current_orders_id', $iOrderId); //important
        $aData['ShopwareOrderNumber'] = $sOrderNumber;

        $aShippingAddress = $this->prepareAddress($aAddresses['Shipping'], "$iCustomerNumber");        
        $oDhlHelper = MLHelper::gi('model_order_dhl');
        /* @var $oDhlHelper ML_Shopware_Helper_Model_Order_Dhl  */
        $blExistingProduct = $oDhlHelper->checkExistingArticle($iOrderId);
        try {//some shopware hook or event can break this process
            if ($blExistingProduct) {
                $oShopwareOrder->sSaveShippingAddress($aShippingAddress, $iOrderId);
            } else {
                throw new Exception('not found detail can be problematic for dhl plugin');
            }
        } catch (Exception $oEx) {
            if (!$oDhlHelper->checkShippingAddress($iOrderId)) {//add shipping address normal
                $oDhlHelper->sSaveShippingAddress($aShippingAddress, $iOrderId);
            }
        }

        $aBillingAddress = $this->prepareAddress($aAddresses['Billing'], "$iCustomerNumber");
        try {//some shopware hook or event can break this process
            if ($blExistingProduct) {
                $oShopwareOrder->sSaveBillingAddress($aBillingAddress, $iOrderId);
            } else {
                throw new Exception('not found detail can be problematic for dhl plugin');
            }
        } catch (Exception $oEx) {
            if (!$oDhlHelper->checkBillingAddress($iOrderId)) {//add billing address normaly
                $oDhlHelper->sSaveBillingAddress($aBillingAddress, $iOrderId);
            }
        }
        return $aData;
    }

    /**
     * tries to fetch the maximal tax rate from order products table (s_order_details)
     * @param $iOrderId
     * @return float
     */
    protected function getMaxTaxRateFromProducts($iOrderId){
        $oOrder = Shopware()->Models()->getRepository('\Shopware\Models\Order\Order')->find($iOrderId);
        $oDetails = $oOrder->getDetails();
        $fTotalTax = 0.00;
        foreach ($oDetails as $oDetail) {
            /* @var $oDetail Shopware\Models\Order\Detail */
            $fTotalTax = max($fTotalTax, (float)$oDetail->getTaxRate());
        }
        return (float)$fTotalTax;
    }
    
    /**
     * get specific total of Order data by total Type
     * @param string $sName
     * @return array
     */
    public function getTotal($sName) {
        $aTotals = $this->aNewData['Totals'];
        foreach ($aTotals as $aTotal) {
            if ($aTotal['Type'] == $sName) {
                return $aTotal;
            }
        }
        return array();
    }

    /**
     * if no payment status is set it return 17 as open status
     */
    protected function getPaymentStatus() {
        if (!isset($this->aNewData['Order']['PaymentStatus']) || empty($this->aNewData['Order']['PaymentStatus'])) {
            return 17;
        } else {
            return $this->aNewData['Order']['PaymentStatus'];
        }
    }

    /**
     * try to find  matched shipping method in shopware otherwise it create new shipping method(Dispath)
     * @return int
     */
    protected function getDispatch() {
        $aTotalShipping = $this->getTotal('Shipping');
        if (isset($aTotalShipping['Code'])) {
            try {
                if (is_numeric($aTotalShipping['Code'])) {
                    $oDispatch = Shopware()->Models()->getRepository('Shopware\Models\Dispatch\Dispatch')->find($aTotalShipping['Code']);
                    if (!is_object($oDispatch)) {
                        $oDispatch = Shopware()->Models()->getRepository('Shopware\Models\Dispatch\Dispatch')->findOneBy(array('name' => $aTotalShipping['Code']));
                    }
                } else {
                    $oDispatch = Shopware()->Models()->getRepository('Shopware\Models\Dispatch\Dispatch')->findOneBy(array('name' => $aTotalShipping['Code']));
                }
                if (!is_object($oDispatch)) {
                    $oDispatch = new Shopware\Models\Dispatch\Dispatch();
                    $oDispatch->setType(0);
                    $oDispatch->setName($aTotalShipping['Code']);
                    $oDispatch->setDescription('');
                    $oDispatch->setComment($aTotalShipping['Code']);
                    $oDispatch->setActive(0);
                    $oDispatch->setPosition(20);
                    $oDispatch->setCalculation(1);
                    $oDispatch->setStatusLink('');
                    $oDispatch->setSurchargeCalculation(0);
                    $oDispatch->setTaxCalculation(0);
                    $oDispatch->setBindLastStock(0);
                    $oDispatch->setBindShippingFree(0);
                    Shopware()->Models()->persist($oDispatch);
                    Shopware()->Models()->flush($oDispatch);
                }

                $oShippingCosts = Shopware()->Models()->getRepository('Shopware\Models\Dispatch\ShippingCost')->findBy(array('dispatchId' => $oDispatch->getId()));
                if (!$oShippingCosts) {
                    $oShippingCosts = new Shopware\Models\Dispatch\ShippingCost();
                    $oShippingCosts->setFrom('0');
                    $oShippingCosts->setValue(1);
                    $oShippingCosts->setFactor(0);
                    $oShippingCosts->setDispatch($oDispatch);

                    Shopware()->Models()->persist($oShippingCosts);
                    Shopware()->Models()->flush($oShippingCosts);
                }
                return $oDispatch->getId();
            } catch (Exception $oExc) {
                // shipping-code-dispatcher not found, use default in following-code
                MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'),  array(
                    'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                    'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                    'Exception' => 'problem to get dispatch or create it : ' . $oExc->getMessage(),
                    'ExcpetionTrace' => $oExc->getTraceAsString(),
                ));
            }
        }
        //choose first and active dispatch as default dispatch to use in order import             
        $oDispatch = Shopware()->Models()->getRepository('Shopware\Models\Dispatch\Dispatch');
        /* @var $oDispatch Shopware\Models\Dispatch\Repository */
        $aDefaultDispatch = $oDispatch->getDispatchesQueryBuilder()
            ->select(array('id' => 'dispatches.id'))->setMaxResults(1)->orderBy('dispatches.position')->where('dispatches.position <> 0')->getQuery()->getOneOrNullResult();
        return $aDefaultDispatch['id'];
    }

    /**
     * try to find matched payment method in shopware otherwise it will create new payment method
     * @param string $sMethodName
     * @param boolean $blActive
     * @return int
     */
    public function getPaymentMethod($sMethodName, $blActive = false) {
        try {
            Shopware()->Models()->clear();
            $oShopwareModel = Shopware()->Models();
            if (!$blActive && isset($sMethodName) && !empty($sMethodName)) {
                if (is_numeric($sMethodName)) {
                    $oPayment = $oShopwareModel->getRepository('Shopware\Models\Payment\Payment')->find($sMethodName);
                } else {
                    $oPayment = $oShopwareModel->getRepository('Shopware\Models\Payment\Payment')->findOneBy(array('name' => $sMethodName));
                }
                if (!isset($oPayment)) {
                    $oPayment = new \Shopware\Models\Payment\Payment();
                    $oPayment->fromArray(array(
                        "active" => false, "additionalDescription" => $sMethodName,
                        "attribute" => array(), "class" => '',
                        "countries" => array(), "debitPercent" => "0",
                        "description" => $sMethodName, "embedIFrame" => "",
                        "esdActive" => false, "hide" => 0,
                        "hideProspect" => false, "iconCls" => "",
                        "id" => 0, "leaf" => false,
                        "name" => $sMethodName, "parentId" => 0,
                        "pluginId" => 0, "position" => 0,
                        "shops" => array(), "source" => 1,
                        "surcharge" => "", "surchargeString" => "",
                        "table" => '', "template" => $sMethodName,
                        "text" => ""
                    ));

                    $oShopwareModel->persist($oPayment);
                    $oShopwareModel->flush($oPayment);
                }
                if (!$blActive || $oPayment->getActive()) {
                    return $oPayment->getId();
                }
            }
        } catch (Exception $oExc) {
            MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                'Exception' => 'problem to get payment or create it : ' . $oExc->getMessage()
            ));
        }
        //choose first position and active payment as default payment to use in order import 
        $oPayment = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        /* @var $dispatch Shopware\Models\Payment\Repository */
        $aDefaultPayment = $oPayment->getPaymentsQueryBuilder()
            ->select(array('id' => 'p.id'))->setMaxResults(1)->orderBy('p.position')->where('p.active = 1')->getQuery()->getOneOrNullResult();
        return $aDefaultPayment['id'];
    }
    
    /**
     * @param array $aAddress $aAddress['CountryCode'] is only in use
     * @return \Shopware\Models\Country\Country
     * @throws Exception
     */
    protected function getCountry($aAddress){
        $iCountryCode = trim($aAddress['CountryCode']);
        $sMlOrderId = MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId');
        if (!empty($iCountryCode)) {
            $oCountry = Shopware()->Models()->getRepository('\Shopware\Models\Country\Country')->findOneBy(array('iso' => $iCountryCode));
            if (!is_object($oCountry)) {                    
                $message =  MLI18n::gi()->get('Shopware_Orderimport_CountryCodeDontExistsError', array('mpOrderId' => $sMlOrderId, 'ISO'=>$iCountryCode));
                MLErrorLog::gi()->addError(-1, ' ', $message, array('MOrderID' => $sMlOrderId));
                throw new Exception($message);
            }
        } else {
            $message =  MLI18n::gi()->get('Shopware_Orderimport_CountryCodeIsEmptyError', array('mpOrderId' => $sMlOrderId));
            MLErrorLog::gi()->addError(-1, ' ', $message, array('MOrderID' => $sMlOrderId));
            throw new Exception($message);
        }
        return $oCountry;
    }
    
    /**
     * 
     * @param array $aAddress $aAddress['Suburb'] and $aAddress['CountryCode'] are only in use
     * @return \Shopware\Models\Country\State
     */
    protected function getState($aAddress){
        try{
            $oCountry = $this->getCountry($aAddress);
            if (array_key_exists('Suburb', $aAddress) && !empty($aAddress['Suburb'])) {
                foreach ($oCountry->getStates() as $oState){
                    /* @var $oState \Shopware\Models\Country\State */
                    if ($oState->getName() == $aAddress['Suburb']) {
                        return $oState;
                    }
                }
            }
        }  catch (Exception $oExc){
        }
        return null;
    }
    
    /**
     * generic function to manage address data (billing and shipping )
     * @param array $aAddress
     * @param string $sCustomerNumber
     * @return type
     * @throws Exception
     */
    protected function prepareAddress($aAddress, $sCustomerNumber) {
        $blSNExist = $this->isStreetNumberExist();
        $oCountry = $this->getCountry($aAddress);
        $oState = $this->getState($aAddress);
        $iCountryId = $oCountry->getId();
        $sCity = trim($aAddress["City"]);
        if($oState !== null){
            $iStateId = $oState->getId();
        }else{
            $iStateId = 0;
            if(!empty($aAddress["Suburb"])){
                $sCity .= " - ".trim($aAddress["Suburb"]);
            }
        }

        return array(
            "userID" => $this->getCustomerId(),
            "company" => (empty($aAddress["Company"])) ? '' : $aAddress["Company"],
            "department" => '',
            "salutation" => $aAddress["Gender"] == 'f' ? 'ms' : 'mr',
            "firstname" => $aAddress["Firstname"],
            "lastname" => $aAddress["Lastname"],
            "street" => empty($aAddress["Street"]) || !$blSNExist ? (isset($aAddress["StreetAddress"]) ? $aAddress["StreetAddress"] : '--') : $aAddress["Street"],
            "streetnumber" => $aAddress["Housenumber"],
            "magna_origstreet" => empty($aAddress["Street"]) ? (isset($aAddress["StreetAddress"]) ? $aAddress["StreetAddress"] : '') : $aAddress["Street"],
            "zipcode" => $aAddress["Postcode"],
            "city" => $sCity,
            "countryID" => $iCountryId,
            "magna_origcountrycode" => trim($aAddress['CountryCode']),
            "customernumber" => $sCustomerNumber,
            "stateID" => $iStateId,
            "phone" => $aAddress['Phone'],
            "fax" => '',
            "ustid" => '',
            "text1" => '',
            "text2" => '',
            "text3" => '',
            "text4" => '',
            "text5" => '',
            "text6" => '',
        );
    }

    /**
     * Save user billing address
     */
    public function SaveCustomerBillingAddress($aAddress, $blCustomerExists = false) {
        $blSNExist = $this->isStreetNumberExist();
        $sSqlCommand = "  `s_user_billingaddress`  SET ";
        foreach (
        array(
            'customernumber', 'company', 'department', 'salutation', 'firstname', 'lastname',
            'street', 'streetnumber', 'zipcode', 'city', 'phone', 'fax', 'countryID',
            'stateID', 'ustid'
        ) as $sKey) {
            if ($sKey == 'streetnumber' && !$blSNExist) {//in shopware 5 we don't have this field
                continue;
            }
            $sSqlCommand .= " $sKey = {$this->getShopwareDB()->quote($aAddress[$sKey])} ,";
        }
        $sSql = $blCustomerExists ? '   UPDATE ' . substr($sSqlCommand, 0, -1) . " WHERE userID =  {$this->getShopwareDB()->quote($aAddress['userID'])} " : 'INSERT INTO' . $sSqlCommand . " userID = {$this->getShopwareDB()->quote($aAddress['userID'])} ";

        $result = $this->executeSql($sSql);
        if ($blCustomerExists === false) {
            $billingID = $this->getShopwareDB()->lastInsertId();
            $this->executeSql("INSERT INTO s_user_billingaddress_attributes (billingID, text1, text2, text3, text4, text5, text6) VALUES (?,?,?,?,?,?,?)", array(
                $billingID,
                $aAddress["text1"], $aAddress["text2"], $aAddress["text3"], $aAddress["text4"], $aAddress["text5"], $aAddress["text6"]
            ));
        }

        return $result;
    }

    /**
     * save order shipping address
     */
    public function SaveCustomerShippingAddress($aAddress, $blCustomerExists = false) {
        $blSNExist = $this->isStreetNumberExist();
        $sSqlCommand = "  `s_user_shippingaddress`  SET ";
        foreach (array(
            'company', 'department',
            'salutation', 'firstname',
            'lastname', 'street',
            'streetnumber', 'zipcode',
            'city', 'countryID',
            'stateID'
        ) as $sKey) {
            if ($sKey == 'streetnumber' && !$blSNExist) {//in shopware 5 we don't have this field
                continue;
            }
            $sSqlCommand .= " $sKey = {$this->getShopwareDB()->quote($aAddress[$sKey])} ,";
        }
        $sSqlCommand = $blCustomerExists ? '   UPDATE ' . substr($sSqlCommand, 0, -1) . ' WHERE userID = ' .$this->getShopwareDB()->quote($aAddress['userID']) : 'INSERT INTO' . $sSqlCommand . ' userID = ' .$this->getShopwareDB()->quote($aAddress['userID']) ;

        $result = $this->executeSql($sSqlCommand);

        //new attribute table
        $shippingId = $this->getShopwareDB()->lastInsertId();
        if ($blCustomerExists === false) {
            $this->executeSql("INSERT INTO s_user_shippingaddress_attributes (shippingID, text1, text2, text3, text4, text5, text6) VALUES (?,?,?,?,?,?,?)", array(
                $shippingId, $aAddress["text1"], $aAddress["text2"], $aAddress["text3"], $aAddress["text4"], $aAddress["text5"], $aAddress["text6"]
            ));
        }
        return $result;
    }

    /**
     * update billing address
     * @return \ML_Shopware_Helper_Model_ShopOrder
     */
    public function updateBillingAddress() {
        $sCutomerNumber = '';
        if ($this->oCurrentOrder->getBilling() !== null) {
            $sCutomerNumber = $this->oCurrentOrder->getBilling()->getNumber();
        }
        $iOrderId = $this->oCurrentOrder->getId();
        $blSNExist = $this->isStreetNumberExist();
        $aAddress = $this->prepareAddress($this->aNewData['AddressSets']['Billing'], $sCutomerNumber);
        $sSql = "
            UPDATE s_order_billingaddress
               SET userID = ?, orderID = ?, customernumber = ?, company = ?, department = ?,
                   salutation = ?, firstname = ?, lastname = ?, street = ?, ".
                   ($blSNExist?"streetnumber = ?,":"").
                   " zipcode = ?, city = ?, phone = ?, fax = ?, countryID = ?,  stateID = ? , ustid = ?
             WHERE orderID = ".$iOrderId;
        
        $aValues = array();
        $aValues[] = $aAddress['userID'];
        $aValues[] = $iOrderId;
        $aValues[] = $aAddress['customernumber'];
        $aValues[] = $aAddress['company'];
        $aValues[] = $aAddress['department'];
        $aValues[] = $aAddress['salutation'];
        $aValues[] = $aAddress['firstname'];
        $aValues[] = $aAddress['lastname'];
        $aValues[] = $aAddress['street'];
        if ($blSNExist) {
            $aValues[] = $aAddress['streetnumber'];
        }
        $aValues[] = $aAddress['zipcode'];
        $aValues[] = $aAddress['city'];
        $aValues[] = $aAddress['phone'];
        $aValues[] = $aAddress['fax'];
        $aValues[] = $aAddress['countryID'];
        $aValues[] = $aAddress['stateID'];
        $aValues[] = $aAddress['ustid'];
        $this->executeSql($sSql, $aValues);
        return $this;
    }

    /**
     * update shipping address
     * @return \ML_Shopware_Helper_Model_ShopOrder
     */
    public function updateShippingAddress() {
        $blSNExist = $this->isStreetNumberExist();
        $aAddress = $this->prepareAddress($this->aNewData['AddressSets']['Shipping'], '');
        $iOrderId = $this->oCurrentOrder->getId();
        $sSql = "
            UPDATE s_order_shippingaddress
            SET userID = ?,  orderID = ?,  company = ?,  department = ?,  salutation = ?, firstname = ?,  lastname = ?, street = ?, ".
                ($blSNExist?"streetnumber = ?,":"").
                "zipcode = ?, city = ?, countryID = ?, stateID = ?  WHERE orderID = " . $iOrderId;
        $aValues = array();
        $aValues[] = $aAddress['userID'];
        $aValues[] = $iOrderId;
        $aValues[] = $aAddress['company'];
        $aValues[] = $aAddress['department'];
        $aValues[] = $aAddress['salutation'];
        $aValues[] = $aAddress['firstname'];
        $aValues[] = $aAddress['lastname'];
        $aValues[] = $aAddress['street'];
        if($blSNExist){
            $aValues[] = $aAddress['streetnumber'];            
        }
        $aValues[] = $aAddress['zipcode'];
        $aValues[] = $aAddress['city'];
        $aValues[] = $aAddress['countryID'];
        $aValues[] = $aAddress['stateID'];
        $this->executeSql($sSql, $aValues);
        
        $oDhlHelper = MLHelper::gi('model_order_dhl');
        $oDhlHelper->fillDhlAttributes($iOrderId, array(
            'firstName' => $aAddress['firstname'],
            'lastName' => $aAddress['lastname'],
            'city' => $aAddress['city'],
            'zip' => $aAddress['zipcode'],
            'country' => $aAddress['magna_origcountrycode'],
            'street' => $aAddress['magna_origstreet'],
            'streetNumber' => $aAddress['streetnumber']
        ));
        return $this;
    }

    /**
     * if we have payment in order we add it as order detail
     * @param int $iOrderId
     * @param string $sOrderNumber
     * @param array $aProduct
     * @param string $sOrderStatus
     * @return float
     */
    public function checkPayment($iOrderId, $sOrderNumber, $aProduct, $sOrderStatus) {
        if (is_object($this->oCurrentOrder)) {
            $oDetails = $this->oCurrentOrder->getDetails();
            /* @var $oDetail Shopware\Models\Order\Detail */
            foreach ($oDetails as $oDetail) {
                $sName = $oDetail->getArticleName();
                if (trim($sName) == trim($aProduct['ItemTitle'])) {
                    return $this->updateProduct($oDetail->getId(), $aProduct);
                }
            }
        }
        return $this->addProductToOrder($iOrderId, $sOrderNumber, $aProduct, $sOrderStatus);
    }


    /**
     * if same product should be added to order, this function just update current product
     * and return net price of product
     * @param int $iDetailId
     * @param array $aProduct
     * @return float 
     */
    protected function updateProduct($iDetailId, $aProduct) {
        //price
        $fGros = (float)$aProduct['Price'];
        $fTaxPercent = (float)$aProduct['Tax'];
        $oTax = Shopware()->Models()->getRepository('\Shopware\Models\Tax\Tax')->findOneBy(array('tax' => $fTaxPercent));
        $sSql = "
           UPDATE `s_order_details`
              SET
                  price = ?,
                  quantity = ?,
                  taxID = ?,
                  tax_rate = ?
            WHERE id = ?
        ";
        $this->executeSql($sSql, array(
            $fGros,
            $aProduct['Quantity'],
            is_object($oTax) ? $oTax->getId() : null,
            $fTaxPercent,
            $iDetailId
        ));
        return round($this->oPrice->calcPercentages($fGros, null, $fTaxPercent), 5);
    }

    /**
     * add product to order detail and return net price of product
     * @param int $iOrderId
     * @param string $sOrderNumber
     * @param array $aProduct
     * @param string $sOrderStatus
     * @return float
     */
    protected function addProductToOrder($iOrderId, $sOrderNumber, $aProduct, $sOrderStatus, $iCurrentQty = 0) {   
        $oProduct = MLProduct::factory();
        if (   isset($aProduct['SKU'])
            && $oProduct->getByMarketplaceSKU($aProduct['SKU'])->exists()
        ) {
            $sArticleNumber = $oProduct->getProductField('number');
            $iArticleId = (int)$oProduct->getId();
            $blIsEsdarticle = is_object($oProduct->getProductField('esd','object')) ? 1 : 0;
            $fTaxPercent = $oProduct->getTax($this->aNewData['AddressSets']['Shipping']);
        } else {
            $sArticleNumber = $aProduct['SKU'];
            $iArticleId = 0;
            $blIsEsdarticle = 0;
            $fDefaultProductTax = MLModul::gi()->getConfig('mwst.fallback');
            // fallback
            if ($fDefaultProductTax === null) {
                $fDefaultProductTax = MLModul::gi()->getConfig('mwstfallback'); // some modules have this, other that
            }
            $fTaxPercent = (($aProduct['Tax'] === false) ? $fDefaultProductTax : $aProduct['Tax']);
//            $aProduct['Modus'] = 12;
        }
        //price
        $fGros = (float)$aProduct['Price'];
        $fNet = round($this->oPrice->calcPercentages($fGros, null, (float)$fTaxPercent), 5);

        // $oTax could be null
        $oTax = Shopware()->Models()->getRepository('\Shopware\Models\Tax\Tax')->findOneBy(array('tax' => $fTaxPercent));

        $sName = $this->getShopwareDB()->quote($aProduct['ItemTitle']);
        $sSql = "
            INSERT INTO `s_order_details`
                    SET orderID = $iOrderId,
                        ordernumber = '$sOrderNumber',
                        articleID = $iArticleId,
                        articleordernumber = '$sArticleNumber',
                        price = {$fGros},
                        quantity = {$aProduct['Quantity']},
                        name = {$sName},
                        status = {$sOrderStatus} ,
                        releasedate = '0000-00-00',
                        modus = {$aProduct['Modus']},
                        esdarticle = {$blIsEsdarticle},
                        taxID = ".(is_object($oTax) ? $oTax->getId() : 'null').",
                        tax_rate = $fTaxPercent
        ";
        // Add new entry to the table
        if ($this->executeSql($sSql)) {
            $iOrderDetailsId = $this->getShopwareDB()->lastInsertId();
            $sAttributeSql = "
                INSERT INTO s_order_details_attributes (detailID, attribute1, attribute2, attribute3, attribute4, attribute5, attribute6)
                     VALUES ($iOrderDetailsId ,'','','','','','')
            ";
            $this->executeSql($sAttributeSql);
            if ($blIsEsdarticle) {
                $aEsdProductData = array(
                    "quantity" => $aProduct['Quantity'],
                    "articleID" => $iArticleId,
                    "ordernumber" => $sArticleNumber,
                    "articlename" => $sName
                );
                /**
                * merged orders - keep imported product in Order_ESD
                */
                $blImportedBefore = false;
                foreach (isset($this->aCurrentData['Products']) ? $this->aCurrentData['Products'] : array() as $aOldProduct) {
                    if ($aProduct['SKU'] == $aOldProduct['SKU']) {
                        $blImportedBefore = true;
                    }
                }
                if (!$blImportedBefore) {
                    $oShopwareOrderModule = Shopware()->Modules()->Order();
                    $oCustomer = $this->getCustomer($this->aNewData['AddressSets']['Main']['EMail']);
                    if(is_object($oCustomer)){
                        $oShopwareOrderModule->sUserData = array("additional"=>array(
                            "user" => array(
                                "id" => $oCustomer->getId(),
                                "email" => $oCustomer->getEmail(),
                            )
                        ));
                        Shopware()->Modules()->Order()->sManageEsdOrder($aEsdProductData, $iOrderId, $iOrderDetailsId);
                        $oShopwareOrderModule->sUserData = null;
                    }
                }
            }
        }
        if (   isset($aProduct['SKU'])
            && $oProduct->getByMarketplaceSKU($aProduct['SKU'])->exists()
            && isset($aProduct['StockSync'])
            && $aProduct['StockSync']
        ) {
            $oProduct->setStock($oProduct->getStock() - $iCurrentQty);
        }
        return $fNet;
    }

    /**
     * @param string $sEmail
     * @return Shopware\Models\Customer\Customer
     */
    protected function getCustomer($sEmail) {
        return Shopware()->Models()->getRepository('\Shopware\Models\Customer\Customer')->findOneBy(array('email' => $sEmail));
    }

    /**
     * set $this->CustomerId by found customer , and then return CustomerNumber
     *  -CustomerNumber is different from customer id-
     * @param array $aAddresses
     * @param int $iPaymentID
     * @return int
     * @throws Exception
     */
    protected function addCustomerToOrder(&$aAddresses, $iPaymentID) {
        $iCustomerNumber = '';
        //$oCustomer = $this->getCustomer($aAddresses['Main']['EMail']);
        $oCustomer = $this->getCustomer($aAddresses['Main']['EMailIdent']);
        $blCustomerExists = ( $oCustomer != null);
        // Add customer if customer isn't existed
        if (!$blCustomerExists) {
            $sPassword = "";
            for ($i = 0; $i < 10; $i++) {
                $randnum = mt_rand(0, 35);
                if ($randnum < 10) {
                    $sPassword .= $randnum;
                } else {
                    $sPassword .= chr($randnum + 87);
                }
            }
            $sEncoderName = 'md5'; //Shopware()->PasswordEncoder()->getDefaultPasswordEncoderName();
            $aAddresses['Main']['Password'] = $sPassword; //important to send password in Promotion Mail
            $sEncodedPassword = md5($sPassword);
            
            $sConfigCustomerGroup = MLModul::gi()->getConfig('CustomerGroup');
            if($sConfigCustomerGroup === null){
                $sConfigCustomerGroup = MLModul::gi()->getConfig('customergroup');
            }
            if($sConfigCustomerGroup == '-'){
                $oCustomerGroup = null;
            }else{
                $oCustomerGroup = Shopware()->Models()->getRepository('\Shopware\Models\Customer\Group')->find($sConfigCustomerGroup);
            }
            if ($oCustomerGroup !== null) {
                $sCustomerGroup = $oCustomerGroup->getKey();
            } else {
                $sCustomerGroup = 'EK';
            }
            $aFields = array_merge(Shopware()->Models()->getClassMetadata('Shopware\Models\Article\Article')->columnNames, Shopware()->Models()->getClassMetadata('Shopware\Models\Customer\Customer')->columnNames);
            $sCustomerSql = "
                INSERT INTO s_user
                   SET password = " . $this->getShopwareDB()->quote($sEncodedPassword) . ",
                       active = 1 ,
                       customergroup = " . $this->getShopwareDB()->quote($sCustomerGroup) . ",
                       firstlogin = CURRENT_DATE(),
                       lastlogin = NOW(),
                       validation = ''," .
                       (in_array('encoder', $aFields) ? "encoder = '".$sEncoderName."'," : '') .
                       "email = " . $this->getShopwareDB()->quote(trim($aAddresses['Main']['EMail'])) . ",
                       language = " . Shopware()->Shop()->getId() . ",
                       referer = '',
                       accountmode = ".($sConfigCustomerGroup == '-' ? '1' : '0').",
                       newsletter = 0,
                       paymentID = $iPaymentID,
                       paymentpreset = 0,
                       subshopID = " . Shopware()->Shop()->getId();
                       /* accountmode should be 0, otherwise user cannot login with this account */
            if ($this->executeSql($sCustomerSql)) {
                $this->iCustomerId = $this->getShopwareDB()->lastInsertId();
            } else {
                throw new Exception('error in adding user');
            }
        } else {
            try {
                $iCustomerNumber = is_object($oCustomer->getBilling())?$oCustomer->getBilling()->getNumber():null;
            } catch (Exception $oExc) {}
            $sCustomerSql = "
                UPDATE s_user
                   SET email = ".$this->getShopwareDB()->quote(trim($aAddresses['Main']['EMail']))."
                 WHERE id = ".$oCustomer->getId()."
            ";
            if (!$this->executeSql($sCustomerSql)) {
                throw new Exception('error in adding user');
            }
            $this->iCustomerId = $oCustomer->getId();
        }
        if ($iCustomerNumber === '' && Shopware()->Config()->get('sSHOPWAREMANAGEDCUSTOMERNUMBERS')) {
            $iCustomerNumber = $this->getShopwareDb()->fetchOne("SELECT `number`+1 FROM `s_order_number` WHERE name = 'user'");
            $iCountUsedBefore = (int)$this->getShopwareDb()->fetchOne("SELECT count(*) FROM s_user_billingaddress where customernumber = '".$iCustomerNumber."'");
            if ($iCountUsedBefore > 0) {
                $iCustomerNumberShipping = $this->getShopwareDb()->fetchOne('SELECT MAX( (CAST(customernumber as unsigned)) ) +1 FROM s_user_billingaddress');
                $iCustomerNumber = max(array((int)$iCustomerNumber, (int)$iCustomerNumberShipping));
            }
            $this->executeSql("
                UPDATE `s_order_number`
                   SET `number` = '".$iCustomerNumber."'
                 WHERE `name` = 'user'
            ");
        }
        $this->SaveCustomerShippingAddress($this->prepareAddress($aAddresses['Shipping'], $iCustomerNumber), $blCustomerExists);
        $this->SaveCustomerBillingAddress($this->prepareAddress($aAddresses['Billing'], $iCustomerNumber), $blCustomerExists);
        return $iCustomerNumber;
    }
    
    /**
     * try to find customer id related to current order 
     * @return integer
     * @throws Exception if customer not found
     */
    protected function getCustomerId () {
        if ($this->iCustomerId === null) {// try to get it from existing Order
            if ($this->oCurrentOrder !== null) {
                $this->iCustomerId = $this->oCurrentOrder->getCustomer()->getId();
            }
        }
        if ($this->iCustomerId === null) {
            throw new Exception('Customer not found');
        }
        return $this->iCustomerId;
    }
    
    /**
     * shopware 5 doesn't have streetnumber field in address tables 
     * @var bool
     */
    protected $blIsStreetNumberExist = null;
    
    /**
     * shopware 5 doesn't have streetnumber field in address tables
     * @return bool
     */
    protected function isStreetNumberExist(){
        if ($this->blIsStreetNumberExist === null) {
            $this->blIsStreetNumberExist = MLDatabase::getDbInstance()->columnExistsInTable('streetnumber', 's_order_billingaddress');
        }
        return $this->blIsStreetNumberExist;
    }
}

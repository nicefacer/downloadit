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

class ML_Shopware_Helper_Model_Order_Dhl {
    
    
    /**
     * @var Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    protected $oShopwareDB = null;
    
    /**
     * shopware 5 doesn't have streetnumber field in address tables 
     * @var bool
     */
    protected $blIsStreetNumberExist = null;    
        
    /**
     * @var boolean  if the order has an article that is not found in shop , it will be false
     */
    protected $blFoundArticle = null;
        
    public function __construct() {        
        $this->blFoundArticle = null;
    }
    /**
     * shopware 5 doesn't have streetnumber field in address tables 
     * @return bool
     */
    protected function isStreetNumberExist(){
        if($this->blIsStreetNumberExist === null){
            $this->blIsStreetNumberExist = MLDatabase::getDbInstance()->columnExistsInTable('streetnumber', 's_order_billingaddress');            
        }
        return $this->blIsStreetNumberExist;
    }
    
    /**
     * 
     * @return Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    protected function getShopwareDb(){
        if($this->oShopwareDB === null){
            $this->oShopwareDB = Shopware()->Db();
        }
        return $this->oShopwareDB;
    }


    /**
     * Sql method for extended logging
     * @param string $sQuery
     * @return \ML_Shopware_Helper_Model_ShopOrder
     * @throws Exception rethrow Exception
     */
    protected function executeSql($sQuery, $aArray = array()) {
        try {
//            MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'),  array(
//                'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
//                'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
//                'Query' =>  array($sQuery => $aArray)
//            ));
            $this->getShopwareDb()->query($sQuery, $aArray);
            return true;
        } catch (Exception $oEx) {
            MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'),  array(
                'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                'Exception' => $oEx->getMessage(),
                'Query' =>  array($sQuery => $aArray),
            ));
            throw $oEx;
        }
    }
    
    /**
     * Save order billing address
     * @access public
     */
    public function sSaveBillingAddress($aAddress, $id) {
        $blSNExist = $this->isStreetNumberExist();
        
        $aValues = array();
        $aValues[] = $aAddress['userID'];
        $aValues[] =             $id;
        $aValues[] = $aAddress['customernumber'];
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
        $aValues[] = $aAddress['phone'];
        $aValues[] = $aAddress['fax'];
        $aValues[] = $aAddress['countryID'];
        $aValues[] = $aAddress['stateID'];
        $aValues[] = $aAddress["ustid"];
        $result = $this->executeSql("INSERT INTO s_order_billingaddress SET userID = ?, orderID = ?, customernumber = ?, company = ?, department = ?, salutation = ?, firstname = ?, lastname = ?, street = ?, ".
                ($blSNExist?"streetnumber = ?,":"").
                " zipcode = ?, city = ?, phone = ?, fax = ?, countryID = ?, stateID = ?, ustid = ? ", $aValues);
        //new attribute tables
        $billingID = $this->getShopwareDb()->lastInsertId();
        $this->executeSql("INSERT INTO s_order_billingaddress_attributes (billingID, text1, text2, text3, text4, text5, text6) VALUES (?,?,?,?,?,?,?)", array(
            $billingID,
            $aAddress["text1"], $aAddress["text2"], $aAddress["text3"], $aAddress["text4"], $aAddress["text5"], $aAddress["text6"]
        ));
        return $result;
    }

    /**
     * save order shipping address
     * @access public
     */
    public function sSaveShippingAddress($aAddress, $id) {
        $blSNExist = $this->isStreetNumberExist();
        
        $aValues = array();
        $aValues[] = $aAddress['userID'];
        $aValues[] =             $id;
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
        $result = $this->executeSql("
        INSERT INTO s_order_shippingaddress
        SET userID = ?,  orderID = ?, company = ?, department = ?, salutation = ?, firstname = ?,  lastname = ?, street = ?,
            ".
                ($blSNExist?"streetnumber = ?,":"").
                " zipcode = ?, city = ?, countryID = ?, stateID= ?", $aValues);

        //new attribute table
        $shippingId = $this->getShopwareDb()->lastInsertId();
        $this->executeSql(
                "INSERT INTO s_order_shippingaddress_attributes (shippingID, text1, text2, text3, text4, text5, text6) VALUES (?,?,?,?,?,?,?)", array(
            $shippingId,
            $aAddress["text1"],
            $aAddress["text2"],
            $aAddress["text3"],
            $aAddress["text4"],
            $aAddress["text5"],
            $aAddress["text6"]
        ));
        $this->fillDhlAttributes($id, array(
            'firstName' => $aAddress['firstname'],
            'lastName' => $aAddress['lastname'],
            'city' => $aAddress['city'],
            'zip' => $aAddress['zipcode'],
            'country' => $aAddress['magna_origcountrycode'],
            'street' => $aAddress['magna_origstreet'],
            'streetNumber' => $aAddress['streetnumber']
        ));
        return $result;
    }
    
    public function fillDhlAttributes($iOderId, $aAddress){
        try{//there can be some Exception that make a problem to rolling back 
            if(class_exists('Shopware\SwagDhl\Structs\Address') && class_exists('Shopware\SwagDhl\Structs\OrderInfo')){
                $oOrder = Shopware()->Models()->getRepository('\Shopware\Models\Order\Order')->find($iOderId);
                if (is_object($oOrder)) {
                    foreach (array('firstName', 'lastName', 'street', 'streetNumber', 'city', 'zip', 'country') as $sKey) {
                        $aAddress[$sKey] = array_key_exists($sKey, $aAddress) ? $aAddress[$sKey] : '';
                    }
                    $oCountry = Shopware()->Models()->getRepository('\Shopware\Models\Country\Country')->findOneBy(array('iso' => $aAddress['country']));
                    if ($oCountry !== null) {
                        $aAddress['country'] = $oCountry->getName();
                    }
                    $oAddress = new Shopware\SwagDhl\Structs\Address($aAddress);
                    $oOrderAttribute = $oOrder->getAttribute();
                    if (in_array($oOrderAttribute->getSwagDhlOrderInfo(), array('', null))) {
                        $oOrderInfo = new Shopware\SwagDhl\Structs\OrderInfo();
                        $iWeight = 0;
                        
                        if(class_exists('Shopware\SwagDhl\Components\WeightCalculationService') && $this->checkExistingArticle($oOrder->getId())) {
                            try {
                                $oDhlWeightCalculation = new Shopware\SwagDhl\Components\WeightCalculationService(Shopware()->Models(),0);
                                $iWeight = $oDhlWeightCalculation->calculateWeight($oOrder);
                            }  catch (Exception $oExc){}
                        }
                        $oOrderInfo->weight = $iWeight;
                        $oOrderInfo->isBulkfreight = 0;
                        $oOrderInfo->identifier = null;
                        $oOrderAttribute->setSwagDhlOrderInfo(serialize($oOrderInfo));
                    }
                    $oOrderAttribute->setSwagDhlAddress(serialize($oAddress));
                    Shopware()->Models()->persist($oOrderAttribute);
                    Shopware()->Models()->flush($oOrderAttribute);
                }
            }
        } catch (Exception $oEx) {
            MLMessage::gi()->addError($oEx);
            MLLog::gi()->add(MLSetting::gi()->get('sCurrentOrderImportLogFileName'), array(
                'MOrderId' => MLSetting::gi()->get('sCurrentOrderImportMarketplaceOrderId'),
                'PHP' => get_class($this).'::'.__METHOD__.'('.__LINE__.')',
                'ShopOrderID' => $iOderId,
                'Exception' => 'There is a problem in filling dhl specific field: '.$oEx->getMessage()."\n".$oEx->getTraceAsString()
            ));
        }
        return $this;
    }

        
    /**
     * check if shipping address exist or not
     * @param int $iOrderId
     * @return boolean
     */
    public function checkBillingAddress($iOrderId) {
        $aResult = $this->getShopwareDb()->fetchAll('select `id` from `s_order_billingaddress` WHERE orderID = ?', array($iOrderId));
        return (count($aResult) > 0);
    }

    /**
     * check if shipping address exist or not
     * @param type $iOrderId
     * @return boolean
     */
    public function checkShippingAddress($iOrderId) {
        $aResult = $this->getShopwareDb()->fetchAll('select `id` from `s_order_shippingaddress` WHERE orderID = ?', array($iOrderId));
        return (count($aResult) > 0);
    }
    
    public function fillMissingDhlData () {
        $oMlSql = MLDatabase::getDbInstance();
        if ($oMlSql->columnExistsInTable('swag_dhl_address', 's_order_attributes') && $oMlSql->columnExistsInTable('swag_dhl_order_info', 's_order_attributes')) {
            $oQuery = MLDatabase::factorySelectClass()
                ->select('OrderId, mo.orderData')
                ->from('magnalister_orders', 'mo')
                ->join(array('s_order_attributes', 'soa', 'soa.OrderId = mo.current_orders_id'), ML_Database_Model_Query_Select::JOIN_TYPE_INNER)
                ->where(
                    array(
                        'or' => array(
                            "swag_dhl_address = ''",
                            "swag_dhl_address is null",
                            "swag_dhl_order_info = ''",
                            "swag_dhl_order_info is null",
                        )
                    )
                )
            ;
            if ($oQuery->getCount() > 0) {
                foreach ($oQuery->getResult() as $aRow) {
                    $aOrderData = json_decode($aRow['orderData'], true);
                    $aShippingAddress = 
                        (isset($aOrderData['AddressSets']) && isset($aOrderData['AddressSets']['Shipping']))
                        ? $aOrderData['AddressSets']['Shipping'] 
                        : array()
                    ;
                    $aAddress = array();
                    foreach (array(
                        'firstName'     => 'Firstname',
                        'lastName'      => 'Lastname',
                        'city'          => 'City',
                        'zip'           => 'Postcode',
                        'country'       => 'CountryCode',
                    ) as $sDhl => $mMagna) {
                        $aAddress[$sDhl] = array_key_exists($mMagna, $aShippingAddress) ? $aShippingAddress[$mMagna] : '';
                    }
                    if (
                        array_key_exists('Street', $aShippingAddress) && !empty($aShippingAddress['Street'])
                        && array_key_exists('Housenumber', $aShippingAddress) && !empty($aShippingAddress['Housenumber'])
                    ) {
                        $aAddress['street'] = $aShippingAddress['Street'];
                        $aAddress['streetNumber'] = $aShippingAddress['Housenumber'];
                    } elseif (
                        array_key_exists('StreetAddress', $aShippingAddress) && !empty($aShippingAddress['StreetAddress'])
                    ) {
                        $aAddress['street'] = $aShippingAddress['StreetAddress'];
                        $aAddress['streetNumber'] = '';
                    } else {
                        $aAddress['street'] = '';
                        $aAddress['streetNumber'] = '';
                    }
                    $this->fillDhlAttributes($aRow['OrderId'], $aAddress);
                }
            }
        }
    }

    
    
    /**
     * usefull function in updating order detail to update order 
     * we can find if the product exist already in current order or not 
     * @param int $iOrderId
     * @return boolean
     */
    public function checkExistingArticle($iOrderId) {
        $oOrder = Shopware()->Models()->getRepository('\Shopware\Models\Order\Order')->find($iOrderId);
        if(is_object($oOrder)){
        /* @var $oOrder \Shopware\Models\Order\Order  */
            foreach ($oOrder->getDetails() as $oDetail) {
                if ($oDetail->getMode() != 0) {
                    continue;
                }
                $oArticleDetail = Shopware()->Models()->getRepository('Shopware\Models\Article\Detail')->findOneBy(array('number' => $oDetail->getArticleNumber()));
                if (!is_object($oArticleDetail)) {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }
}

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
class ML_Ebay_Model_Modul extends ML_Modul_Model_Modul_Abstract {
    /**
     *
     * @var array $aPrice list of ML_Shop_Model_Price_Interface
     */
    protected $aPrice = array(
        'fixed' => null,
        'chinese' => null,
        'buyitnow' => null,
    );
    
    /**
     * better cache it, for exceptions in ebay-api
     * @var string side id
     */
    protected $sEbaySiteId = null;

    public function __construct() {
        parent::__construct();    
        $oEbayConfigUpdate = MLDatabase::factory('config')->set('mpid', 0 )->set('mkey', 'ebay_update_version_'.$this->getMarketPlaceId());
        $sEbayVersion = $oEbayConfigUpdate->get('value');       
        if ($sEbayVersion == null || version_compare($sEbayVersion, MLSetting::gi()->sClientBuild, '<')) {
            $this->trigerAfterUpdate();
            $oEbayConfigUpdate->set('value', MLSetting::gi()->sClientBuild)
                    ->save()
            ;
        }
    }
    
    public function getConfig($sName = null) {
        $mParent = parent::getConfig($sName);
        if ($sName === null) {
            $mParent['productfield.brand'] = 
                array_key_exists('productfield.brand', $mParent) 
                ? $mParent['productfield.brand'] 
                : $this->getConfig('manufacturer')
            ;
        } elseif ($sName == 'productfield.brand' && $mParent === null) {
            $mParent = $this->getConfig('manufacturer');
        }
        if(parent::getConfig('importonlypaid')){
            if($sName == 'orderstatus.paid' ){
                return $this->getConfig('orderstatus.open');
            }  else if($sName =='paymentstatus.paid' ){
                return $this->getConfig('orderimport.paymentstatus');
            }
        }
        return $mParent;
    }

    public function getMarketPlaceName($blIntern = true) {
        return $blIntern ? 'ebay' : MLI18n::gi()->get('sModuleNameEbay');
    }

    public function hasStore() {
        try {
            $aStore = MagnaConnector::gi()->submitRequestCached(array('ACTION' => 'HasStore'), 30 * 60);
            $blHasStore = $aStore['DATA']['Answer'] == 'True';
            return $blHasStore;
        } catch (Exception $oEx) { //no store
            return false;
        }
    }

    public function getEBayAccountSettings() {
        try {
            $aSettings = MagnaConnector::gi()->submitRequestCached(array(
                'ACTION' => 'GeteBayAccountSettings',
            ), 30 * 60);
            return $aSettings['DATA'] ;
        } catch (MagnaException $oEx) {
            return false;
        }
    }
    
    protected function getShippingServiceDetails() {
        try {
            $aShipping = MagnaConnector::gi()->submitRequestCached(array(
                'ACTION' => 'GetShippingServiceDetails',
                'DATA' => array('Site' => MLModul::gi()->getConfig('site')
            ),), 30 * 60);
            if ($aShipping['STATUS'] == 'SUCCESS') {
                $aLocalService = array();
                $aIntService = array();
                foreach ($aShipping['DATA']['ShippingServices'] as $sService => $aService) {
                    if ($aService['InternationalService'] == 0) {
                        $aLocalService[$sService] = $aService['Description'];
                    } else {
                        $aIntService[$sService] = $aService['Description'];
                    }
                }
                $aLocations = $aShipping['DATA']['ShippingLocations'];
                return array('local' => $aLocalService, 'international' => $aIntService, 'locations' => $aLocations);
            } else {
                return array('local' => array(), 'international' => array(), 'locations' => array());
            }

        } catch (MagnaException $oEx) {
            return array('local' => array(), 'international' => array(), 'locations' => array());
        }
    }
    
    public function getLocalShippingServices() {
        $aShipping = $this->getShippingServiceDetails();
        return isset($aShipping['local']) ? $aShipping['local'] : array();
    }

    public function getInternationalShippingServices() {
        $aShipping = $this->getShippingServiceDetails();
        return isset($aShipping['international']) ? $aShipping['international'] : array();
    }

    public function getInternationalShippingLocations() {
        $aShipping = $this->getShippingServiceDetails();
        return isset($aShipping['locations']) ? $aShipping['locations'] : array();
    }

    /**
     * @param string $sService only amount value of selected service
     * @return array|string
     */
        public function getShippingDiscountProfiles($sService = null) {
        $aOut = array();
        try {
            $aResponse = MagnaConnector::gi()->submitRequestCached(array(
                'ACTION' => 'GetShippingDiscountProfiles'
            ), 30 * 60);
            if ($aResponse['STATUS'] == 'SUCCESS') {
                if (array_key_exists('Profiles', $aResponse['DATA'])) {
                    foreach ($aResponse['DATA']['Profiles'] as $key => $profile) {
                        $aOut[$key] = array('name' => $profile['ProfileName'], 'amount' => $profile['EachAdditionalAmount']);
                    }
                }
                //$aOut=$aResponse['DATA'];
            }
        } catch (MagnaException $e) {}

        if ($sService === null) {
            return $aOut;
        } else {
            return isset($aOut[$sService]) ? $aOut[$sService]['amount'] : 0;
        }
    }
    
    public function getShippingPromotionalDiscount() {
        try {
            $aResponse = MagnaConnector::gi()->submitRequestCached(array(
                'ACTION' => 'GetShippingDiscountProfiles'
            ), 30 * 60);
            if ($aResponse['STATUS'] == 'SUCCESS') {
                if (array_key_exists('PromotionalShippingDiscount', $aResponse['DATA'])) {
                    return $aResponse['DATA']['PromotionalShippingDiscount'];
                }
            }
        } catch (MagnaException $e) {}
        return array();
    }

    public function getListingDurations($sListingType) {
        try {
            $aDurations = MagnaConnector::gi()->submitRequestCached(array(
                'ACTION' => 'GetListingDurations',
                'DATA' => array(
                    'ListingType' => $sListingType
                )
            ), 30 * 60);
        } catch (MagnaException $e) {
            //echo print_m($e->getErrorArray(), 'Error');
            $aDurations = array(
                'DATA' => array(
                    'ListingDurations' => array('no' => $e->getMessage())
                )
            );
        }
        $aOut = array();
        foreach ($aDurations['DATA']['ListingDurations'] as $sDuration) {
            $sDefine = 'ML_EBAY_LABEL_LISTINGDURATION_'.strtoupper($sDuration);
            $aOut[$sDuration] = (defined($sDefine) ? constant($sDefine) : $sDuration);
        }
        return $aOut;
    }

    public function getPaymentOptions() {
        try {
            $aPayment = MagnaConnector::gi()->submitRequestCached(array(
                'ACTION' => 'GetPaymentOptions',
                'DATA' => array('Site' => MLModul::gi()->getConfig('site')),
            ), 30 * 60);
            if ($aPayment['STATUS'] == 'SUCCESS' && isset($aPayment['DATA']['PaymentOptions']) && is_array($aPayment['DATA']['PaymentOptions'])) {
                return $aPayment['DATA']['PaymentOptions'];
            } else {
                return array();
            }

        } catch (MagnaException $e) {
            return array();
        }
    }

    public function getConditionValues() {
        $oI18n = MLI18n::gi();
        return array(
            '1000' => $oI18n->get('ML_EBAY_CONDITION_NEW'),
            '1500' => $oI18n->get('ML_EBAY_CONDITION_NEW_OTHER'),
            '1750' => $oI18n->get('ML_EBAY_CONDITION_NEW_WITH_DEFECTS'),
            '2000' => $oI18n->get('ML_EBAY_CONDITION_MANUF_REFURBISHED'),
            '2500' => $oI18n->get('ML_EBAY_CONDITION_SELLER_REFURBISHED'),
            '3000' => $oI18n->get('ML_EBAY_CONDITION_USED'),
            '4000' => $oI18n->get('ML_EBAY_CONDITION_VERY_GOOD'),
            '5000' => $oI18n->get('ML_EBAY_CONDITION_GOOD'),
            '6000' => $oI18n->get('ML_EBAY_CONDITION_ACCEPTABLE'),
            '7000' => $oI18n->get('ML_EBAY_CONDITION_FOR_PARTS_OR_NOT_WORKING')
        );
    }

    public function getHitcounterValues() {
        $oI18n = MLI18n::gi();
        return array(
            'NoHitCounter' => $oI18n->get('ML_EBAY_NO_HITCOUNTER'),
            'BasicStyle' => $oI18n->get('ML_EBAY_BASIC_HITCOUNTER'),
            'RetroStyle' => $oI18n->get('ML_EBAY_RETRO_HITCOUNTER'),
            'HiddenStyle' => $oI18n->get('ML_EBAY_HIDDEN_HITCOUNTER'),
        );
    }

    public function getListingTypeValues() {
        $oI18n = MLI18n::gi();
        $aOut = array();
        if ($this->hasStore()) {
            $aOut['StoresFixedPrice'] = $oI18n->get('ML_EBAY_LISTINGTYPE_STORESFIXEDPRICE');
        }
        $aOut['FixedPriceItem'] = $oI18n->get('ML_EBAY_LISTINGTYPE_FIXEDPRICEITEM');
        $aOut['Chinese'] = $oI18n->get('ML_EBAY_LISTINGTYPE_CHINESE');
        return $aOut;
    }

    /**
     * configures price-object
     * @return ML_Shop_Model_Price_Interface
     */
    public function getPriceObject($sType = null) {
        $sType = strtolower($sType);
        if (in_array($sType, array('storesfixedprice', 'fixedpriceitem'))) {
            $sType = 'fixed';
        } elseif ($sType == 'chinese') {
            $sType = 'chinese';
        } else { //buynow
            $sType = 'buyitnow';
        }
        if ($this->aPrice[$sType] === null) {
            $sKind = $this->getConfig($sType.'.price.addkind');
            $fFactor = (float)$this->getConfig($sType.'.price.factor');
            $iSignal = $this->getConfig($sType.'.price.signal');
            $iSignal = $iSignal === '' ? null : (int)$iSignal;
            $blSpecial = (boolean)$this->getConfig(($sType == 'buyitnow' ? 'chinese' : $sType).'.price.usespecialoffer');
            $sGroup = $this->getConfig(($sType == 'buyitnow' ? 'chinese' : $sType).'.price.group');
            $this->aPrice[$sType] = MLPrice::factory()->setPriceConfig($sKind, $fFactor, $iSignal, $sGroup, $blSpecial);
        }
        return $this->aPrice[$sType];
    }

    public function getStockConfig($sType) {
        $sType = strtolower($sType);
        if (in_array($sType, array('storesfixedprice', 'fixedpriceitem'))) {
            return array(
                'type' => $this->getConfig('fixed.quantity.type'),
                'value' => $this->getConfig('fixed.quantity.value'),
                'max' => $this->getConfig('maxquantity')
            );
        } else {
            return array(
                'type' => 'stock',
                'value' => null,
                'max' => 1
            );
        }
    }

    public function getEbaySiteId() {
        if ($this->sEbaySiteId === null) {
            try {
                $aResponse = MagnaConnector::gi()->submitRequestCached(array(
                    'ACTION' => 'GeteBayOfficialTime'
                ), 30 * 60);
                $sEbaySite = $aResponse['DATA']['SiteID'];
            } catch (MagnaException $e) {
                $e->setCriticalStatus(false);
                $sEbaySite = 77;
            }
            $this->sEbaySiteId = $sEbaySite;
        }
        return $this->sEbaySiteId;
    }

    protected function geteBayReturnPolicyDetails() {
        global $_MagnaSession;
        #echo print_m($_MagnaSession,'$_MagnaSession');
        $mpID = (int)MLRequest::gi()->get('mp');
        $site = MLModul::gi()->getConfig('site');
        if (!isset($site) || empty($site)) {
            $site = '999'; //  999 um keine falsche Gleichheit bei nicht gesetzten Werten zu bekommen
        }
        if (@isset($_MagnaSession[$mpID]['eBayReturnPolicyDetails']['Site']) &&
            ($_MagnaSession[$mpID]['eBayReturnPolicyDetails']['Site'] == $site)
        ) {
            return $_MagnaSession[$mpID]['eBayReturnPolicyDetails'];

        } else {
            try {
                $returnPolicyDetails = MagnaConnector::gi()->submitRequest(array(
                    'ACTION' => 'GetReturnPolicyDetails',
                    'DATA' => array('Site' => $site),
                ));
            } catch (MagnaException $e) {
                $returnPolicyDetails = array(
                    'DATA' => false
                );
            }
            if (!is_array($returnPolicyDetails) || @empty($returnPolicyDetails['DATA'])) {
                return false;
            }
            arrayEntitiesFixHTMLUTF8($returnPolicyDetails['DATA']['ReturnPolicyDetails']);
            $_MagnaSession[$mpID]['eBayReturnPolicyDetails'] = $returnPolicyDetails['DATA']['ReturnPolicyDetails'];
            return $returnPolicyDetails['DATA']['ReturnPolicyDetails'];
        }
    }

    public function geteBaySingleReturnPolicyDetail($detailName) {
        global $_MagnaSession;
        $mpID = $_MagnaSession['mpID'];
        if ((!isset($_MagnaSession[$mpID]['eBayReturnPolicyDetails'])) || (!is_array($_MagnaSession[$mpID]['eBayReturnPolicyDetails']))) {
            $returnPolicyDetails = $this->geteBayReturnPolicyDetails();
        } else {
            $returnPolicyDetails = $_MagnaSession[$mpID]['eBayReturnPolicyDetails'];
        }
        if (!isset($returnPolicyDetails[$detailName])) {
            return array('' => '-');
        }
        return $returnPolicyDetails[$detailName];
    }
    
    /**
     * @return array('configKeyName'=>array('api'=>'apiKeyName', 'value'=>'currentSantizedValue'))
     */
    protected function getConfigApiKeysTranslation() {
        $sDate = $this->getConfig('preimport.start');
        //magento tip to find empty date
        $sDate = (preg_replace('#[ 0:-]#', '', $sDate) ==='') ? date('Y-m-d') : $sDate;
        $sDate = date('Y-m-d', strtotime($sDate));
        $sSync = $this->getConfig('stocksync.tomarketplace');
        return array(
            'site'=>array('api' => 'Access.Site', 'value' => ($this->getConfig('site'))),            
            'inventory.import' => array('api' => 'Inventory.Import', 'value' => ($this->getConfig('inventory.import'))),
            'import' => array('api' => 'Orders.Import', 'value' => ($this->getConfig('import'))),
            'preimport.start' => array('api' => 'Orders.Import.TS', 'value' => $sDate),
            'importonlypaid' => array('api' => 'Orders.ImportOnlyPaid', 'value' => ($this->getConfig('importonlypaid') == '1' ? 'true':'false')),
            'syncproperties' => array('api' => 'Inventory.ListingDetailsSync', 'value' => ((bool)$this->getConfig('syncproperties')?'true':'false')),
            'syncrelisting' => array('api' => 'Inventory.AutoRelist', 'value' => ((bool)$this->getConfig('syncrelisting')?'true':'false')),
            'synczerostock' => array('api' => 'Inventory.ZeroStockSynchro', 'value' => ((bool)$this->getConfig('synczerostock')?'true':'false')),
            'stocksync.tomarketplace' => array('api' => 'Callback.SyncInventory', 'value' => isset($sSync) ? $sSync : 'no'),
        );
    }
    
    
    public function getCarrier() {
        try {
            $aResponse = MagnaConnector::gi()->submitRequestCached(array(
                'ACTION' => 'GetCarriers'
            ), 30 * 60);
            return $aResponse['DATA'];             
        } catch (MagnaException $e) {
            return array();
        }
    }
    
    public function isAuthed($blResetCache = false) {
        if (parent::isAuthed($blResetCache)) {
            if ($this->tokenAvailable()) {
                $expires = $this->getConfig('token.expires');
                if (is_datetime($expires) && ($expires < date('Y-m-d H:i:s'))) {
                    MLMessage::gi()->addNotice(MLI18n::gi()->ML_EBAY_TEXT_TOKEN_INVALID);
                    return false;
                } else {
                    return true;
                }
            } else {
                MLMessage::gi()->addError(MLI18n::gi()->ML_EBAY_TEXT_TOKEN_NOT_AVAILABLE_YET);
                return false;
            }
        }else{
            return false;
        }
    }

    public function tokenAvailable($blResetCache = false) {
        $sCacheKey = strtoupper(__class__).'__'.$this->getMarketPlaceId().'_ebaytoken';
        $oCache = MLCache::gi();
        if ($blResetCache) {
            $oCache->delete($sCacheKey);
        }
        if (!$oCache->exists($sCacheKey) || !((bool)$oCache->get($sCacheKey)) ) {
            $blToken = false;
            try {
                $result = MagnaConnector::gi()->submitRequest(array(
                    'ACTION' => 'CheckIfTokenAvailable'
                ));
                if ('true' == $result['DATA']['TokenAvailable']) {
                    $this->setConfig('token', '__saved__');
                    $this->setConfig('token.expires', $result['DATA']['TokenExpirationTime']);
                    $blToken = true;
                }
            } catch (MagnaException $e) {}
            $oCache->set($sCacheKey, $blToken, 60*15);
        }
        return (bool)$oCache->get($sCacheKey);
    }

    protected function trigerAfterUpdate() {
       
        $oUpdate = MLDatabase::factorySelectClass();
        //remove ebay. prifix
        $iCount = (int)$oUpdate
                ->update('magnalister_config', array(
                            'mkey' => array('func' => "lower(Replace(mkey , 'ebay.' ,'' ))")
                        )     
                )
                ->where("mpid = ".$this->getMarketPlaceId()." AND mkey LIKE 'ebay.%'")
                ->doUpdate();
        
        //convert array to bool in some config key
        foreach (array(
            'checkin.status',
            'useprefilledinfo',
            'fixed.price.usespecialoffer',
            'fixed.exchangerate',
            'usevariations',
            'chinese.price.usespecialoffer',
            'update.orderstatus',
            'update.paymentstatus'
                ) 
                as $sKey
                ){
            $oConfig = MLDatabase::factory('config')->set('mpid',$this->getMarketPlaceId())->set('mkey', $sKey);
            $mValue = $oConfig->get('value');
            if(is_array($mValue)){
                $oConfig->set('value', (int)current($mValue))->save();
                $iCount ++;
            }
        }

        //change exchangerate confgi key
        $oUpdate->init();
        $iCount += (int)$oUpdate
                ->update('magnalister_config', array(
                            'mkey' => 'exchangerate_update'
                        )
                )
                ->where("mpid = ".$this->getMarketPlaceId()." AND mkey = 'fixed.exchangerate'")
                ->doUpdate();
        
        if($iCount > 0){
            $this->aConfig = null;
        }
    }
    
    public function isConfigured() {
        $bReturn = parent::isConfigured();
        $sCurrency = $this->getConfig('currency');
        $aFields = MLRequest::gi()->data('field');
        if(!MLHttp::gi()->isAjax() && $aFields !== null && isset($aFields['currency']) ){ // saving new site in configuration
            $sCurrency = $aFields['currency'];
        }
        if (!empty($sCurrency) && !in_array($sCurrency, array_keys(MLCurrency::gi()->getList()))) {
            MLMessage::gi()->addWarn(sprintf(MLI18n::gi()->ML_GENERIC_ERROR_CURRENCY_NOT_IN_SHOP , $sCurrency));
            return false;
        }
        
        return $bReturn;
    }

}

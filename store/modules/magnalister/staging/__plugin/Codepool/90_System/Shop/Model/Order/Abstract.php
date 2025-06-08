<?php
MLFilesystem::gi()->loadClass('Database_Model_Table_Order');

/**
 * Implements some generic methods that can be shared between various shopsystems for the
 * Order Model.
 */
abstract class ML_Shop_Model_Order_Abstract extends ML_Database_Model_Table_Order {
    /**
     * @var ML_Modul_Model_Modul_Abstract $oModul
     */
    protected $oModul = null;
    protected $oShop = null;
    protected $oProduct = null;
    protected $oPrice = null;
    
    /**
     * Initializes the order model and loads all needed resources.
     * 
     * @param bool $blForce
     *    Forces something
     * @return self
     */
    public function init($blForce = false) {
        parent::init($blForce);

        $this->oShop = MLShop::gi();
        $this->oProduct = MLProduct::factory();
        $this->oPrice = MLPrice::factory();
        return $this;
    }
    
    /**
     * Loads the order module and returns it (registry like behaviour).
     * @deprecated was it only use for orderlogo?
     * @return ML_Modul_Model_Modul_Abstract
     */
    public function getModul() {
        if ($this->oModul === null) {
            try {
                $this->oModul = MLModul::gi();
            } catch (Exception $oEx) {
            
            }
        }
        return $this->oModul;
    }
    
    /**
     * Creates order and manipulates $aData 
     * eg. $aData['AddressSets']['Main']['Password'] if possible
     *
     * @see /Doku/orderexport.json
     * @param array $aData
     * @return array
     */
    abstract public function shopOrderByMagnaOrderData($aData);
    
    /** 
     * will be triggered afer $this->shopOrderByMagnaOrderData and $this->save(with orderdata)
     * @return \ML_Shop_Model_Order_Abstract
     */
    public function triggerAfterShopOrderByMagnaOrderData() {
        return $this;
    }
    
    /**
     * Gets the translation of the order status from this order.
     *
     * @return string
     */
    abstract public function getShopOrderStatusName();
    
    /**
     * @return string
     *    A Timestamp with the format YYYY-mm-dd
     */
    abstract public function getShippingDate();
    
    /**
     * Get the carrier for this order.
     * If there is no carrier information available for this order 
     * this method will return the setting orderstatus.carrier.default.
     *
     * @return string
     *    The shipping carrier
     */
    abstract public function getShippingCarrier();
    
    /**
     * Gets the tracking code for this order.
     * If there is no tracking code available the setting 
     * orderstatus.carrier.additional will be
     * returned (which does not make any sense.
     * An empty string should be returned instead.)
     *
     * @return string
     */
    abstract public function getShippingTrackingCode();
    
    /**
     * Returns a link to the order detail page if possible.
     *
     * @return string
     */
    abstract public function getEditLink();
    
    /**
     * Gets list of ML_Shop_Model_Order_Abstract for current marketplace which are
     * not synchronized (shop.status != magnalister.status)
     * @return array
     *     array(shop.orders_id, ..)
     */
    public static function getOutOfSyncOrdersArray ($iOffset = 0 ,$blCount = false) {
        throw new Exception ("Method '".__METHOD__."' is not implemented.");
    }
    
    /**
     * Gets the "last modified" timestamp of this order.
     *
     * @return string
     *    Timestamp with the format YYYY-mm-dd h:i:s
     */
    abstract public function getShopOrderLastChangedDate();
    
    /**
     * Gets the order status from this order.
     *
     * @return string be careful about return value , if the status is id , you should convert it to string , otherwise there could be some problem in coparision with config data
     */
    abstract public function getShopOrderStatus();
    
    /**
     * Gets an order model instance by the marketplace specific order id.
     *
     * @param string $sId
     * @return ML_Shop_Model_Order_Abstract
     */
    public function getByMagnaOrderId($sId) {
        $oSelect = MLDatabase::factorySelectClass();
        $aData = $oSelect->from($this->sTableName)->where(array('data','like',"%\"$sId\"%"))->getResult() ;
        if ( !empty($aData) ) {
            $aData = array_shift($aData);
            $this->blLoaded = true ;
            foreach ( $aData as $sKey => $sValue ) {
                   $this->__set($sKey , $sValue) ;
                   $this->aOrginData[strtolower($sKey)] = $sValue ;
            }
        }else{ //prevent table_abstract exception to show "keys are not filled" if no order found
            $this->init(true)->set('special', $sId)->aKeys = array('special');
        }
        return $this;
    }
    
    /**
     * Returns the logo (path) of the platform from this order.
     *
     * @return string
     */
    public function getLogo() {
	if ($this->get('platform') !== null) {
            if ($this->get('logo') !== null) {
                $sLogo = $this->get('logo');
            } else {
                $sOrderLogoClass = 'ML_' . ucfirst($this->get('platform')) . '_Model_OrderLogo';
                if (class_exists($sOrderLogoClass, false)) {
                    $oOrderLogo = new $sOrderLogoClass;
                    $sLogo = $oOrderLogo->getLogo($this);
                    $this->set('logo', $sLogo)->save();
                } else {
                    return null;
                }
            }
            return MLHttp::gi()->getResourceUrl('images/logos/' . $sLogo, true);
        } else {
            return null;
        }
    }
    
    /**
     * send speicfic field in order Acknowledge
     * @param type $aOrderParameters
     * @param type $aOrder
     */
    abstract public function setSpecificAcknowledgeField(&$aOrderParameters,$aOrder);
    
    public function getTitle() {
        return '<div style="color: #000000;font: bold 14px sans-serif;">
            <span style="color:#dc043d;" >m</span>agnalister Details
            <img style="vertical-align: middle;" src="'.$this->getLogo().'">
            </div>';
	;
    }

}

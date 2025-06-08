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

class ML_Bepado_Helper_Model_Service_Product {

    /** @var ML_Database_Model_Table_Selection $oSelection     */
    protected $oSelection = null;
    protected $aSelectionData = array();

    /** @var ML_Bepado_Model_Table_Bepado_Prepare $oPrepare     */
    protected $oPrepare = null;

    /** @var ML_Shop_Model_Product_Abstract $oProduct     */
    protected $oProduct = null;
    protected $oVariant = null;
    protected $aData = null;

    public function __call($sName, $mValue) {
        return $sName . '()';
    }

    public function __construct() {
        $this->oPrepare = MLDatabase::factory('bepado_prepare');
        $this->oSelection = MLDatabase::factory('selection');
    }

    public function setProduct(ML_Shop_Model_Product_Abstract $oProduct) {
        $this->oProduct = $oProduct;
        $this->sPrepareType = '';
        $this->aData = null;
        return $this;
    }
    
    public function setVariant(ML_Shop_Model_Product_Abstract $oProduct){
        $this->oVariant=$oProduct;
        return $this;
    }
    
    public function resetData () {
        $this->aData = null;
        return $this;
    }
    
    public function getData() {
        if ($this->aData === null) {
            $this->oPrepare->init()->set('products_id',$this->oVariant->get('id'));
            $aData = array();
            foreach (
                array(
                    'SKU',
                    'EAN',
                    'ItemUrl',
                    'Title',
                    'Description',
                    'Manufacturer',
                    'Tax',
                    'Price',
                    'PurchasePrice',
                    'Currency',
                    'ShippingServiceOptions',
                    'ShippingTime',
                    'Quantity',
                    'Images',
                    'MarketplaceCategories',
                    'Weight',
                    'BasePrice'
                ) as $sField) {
                if (method_exists($this, 'get' . $sField)) {
                    $mValue = $this->{'get' . $sField}();
                    if (is_array($mValue)) {
                        foreach ($mValue as $sKey => $mCurrentValue) {
                            if (empty($mCurrentValue)) {
                                unset ($mValue[$sKey]);
                            }
                        }
                        $mValue = empty($mValue) ? null : $mValue;
                    }
                    if ($mValue !== null) {
                        $aData[$sField] = $mValue;
                    }
                } else {
                    MLMessage::gi()->addWarn("function  ML_Bepado_Helper_Model_Service_Product::get" . $sField . "() doesn't exist");
                }
            }
            $this->aData = $aData;
        }
        return $this->aData;
    }

    protected function getSKU() {
        return $this->oVariant->getMarketPlaceSku();
    }
    
    protected function getEan(){
        return $this->oVariant->getModulField('general.ean', true);
    }
    
    protected function getTitle(){
        return $this->oVariant->getName();
    }
    
    protected function getDescription(){
        return $this->oVariant->getDescription();
    }
    
    protected function getManufacturer () {
        return $this->oVariant->getModulField('manufacturer');
    }
    
    protected function getTax () {
        return $this->oVariant->getTax();
    }
    

    
    protected function getShippingTime() {
        return $this->oPrepare->get('shippingtime');
    }
    
    protected function getShippingServiceOptions() {
        $aShippingServiceOptions = array();
        foreach (array('ShippingCountry', 'ShippingCost', 'ShippingService') as $sPrepareField) {
            $aValues = $this->oPrepare->get($sPrepareField);
            $aValues = is_array($aValues) ? $aValues : array();
            foreach ($aValues as $iPrepared => $sPrepared) {
                $aShippingServiceOptions[$iPrepared][$sPrepareField] = $sPrepared;
            }
        }
        return $aShippingServiceOptions;
    }
    
    protected function getImages() {
        $aOut = array();
        foreach ($this->oVariant->getImages() as $sImage ) {
            try{
                $aImage = MLImage::gi()->resizeImage($sImage, 'products', 500, 500);
                $aOut[]['URL'] = $aImage['url'];
            } catch(Exception $ex) {
                // Happens if image doesn't exist.
            }
        }
        return $aOut;
    }
    
    protected function getCurrency () {
        return MLCurrency::gi()->getDefaultIso();
    }
    
    protected function getQuantity() {
        $iQty = $this->oVariant->getSuggestedMarketplaceStock(
            MLModul::gi()->getConfig('quantity.type'), 
            MLModul::gi()->getConfig('quantity.value')
        );
        return $iQty < 0 ? 0 : $iQty;
    }
    
    protected function getMarketplaceCategories() {
        return $this->oPrepare->get('category');
    }
    
    protected function getWeight() {
        return $this->oVariant->getWeight();
    }
    
    protected function getPrice() {
        return $this->oVariant->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject('b2c'));
    }
    
    protected function getPurchasePrice() {
        return $this->oVariant->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject('b2b'), false);
    }
    
    protected function getItemUrl() {
        return $this->oVariant->getFrontendLink();
    }
    
    protected function getBasePrice() {
        return $this->oVariant->getBasePrice();
    }
    
}

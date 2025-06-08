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

class ML_Tradoria_Helper_Model_Service_Product {

    /** @var ML_Database_Model_Table_Selection $oSelection */
    protected $oSelection = null;
    protected $aSelectionData = array();

    /** @var ML_Tradoria_Model_Table_Tradoria_Prepare $oPrepare  */
    protected $oPrepare = null;

    /** @var ML_Shop_Model_Product_Abstract $oProduct  */
    protected $oProduct = null;
    protected $oVariant = null;
    protected $aData = null;

    public function __call($sName, $mValue) {
        return $sName . '()';
    }

    public function __construct() {
        $this->oPrepare = MLDatabase::factory('tradoria_prepare');
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

            $aFields = array(
                'SKU',
                'ItemTitle',
                'Description',
                'ItemTax',
                'Images',
                'Quantity',
                'Price',
                'Manufacturer',
                'ManufacturerPartNumber',
                'BasePrice',
                'ShippingService',
                'MarketplaceCategory',
                'ShippingGroup',
                'ShippingTime',
                'ListingDuration',
                'ProductType',
                'ReturnPolicy',
                'EAN'
            );

            if (MLModul::gi()->getConfig('catmatch.mpshopcats') === '1') {
                $aFields = array_merge($aFields, array(
                    'ShopCategory',
                    'ShopCategoryStructure',
                ));
            }

            foreach ($aFields as $sField) {
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
                    MLMessage::gi()->addWarn("function  ML_Tradoria_Helper_Model_Service_Product::get" . $sField . "() doesn't exist");
                }
            }
            if (empty($aData['BasePrice'])) {
                unset($aData['BasePrice']);
            }
            $this->aData = $aData;
        }
        return $this->aData;
    }

    protected function getSKU() {
        return $this->oVariant->getMarketPlaceSku();
    }

    protected function getItemTitle() {
        $iLangId = MLModul::gi()->getConfig('lang');
        $this->oVariant->setLang($iLangId);

        return $this->oVariant->getName();
    }

    protected function getDescription() {
        return $this->oVariant->getDescription();
    }

    protected function getItemTax() {
        $taxClassId = $this->oVariant->getTaxClassId();
        $taxMatchConfig = MLModul::gi()->getConfig('checkin.taxmatching');
        return $taxMatchConfig[$taxClassId];
    }

    
    protected function getImageSize(){        
        $sSize = MLModul::gi()->getConfig('imagesize');
        $iSize = $sSize == null ? 500 : (int)$sSize;
        return $iSize;
    }
    
    protected function getImages() {
        $aOut = array();
        $iSize = $this->getImageSize();
        foreach ($this->oVariant->getImages() as $sImage ) {
            try {
                $aImage = MLImage::gi()->resizeImage($sImage, 'products', $iSize, $iSize);
                $aOut[] = $aImage['url'];
            } catch(Exception $ex) {
                // Happens if image doesn't exist.
            }
        }
        return $aOut;
    }

    protected function getQuantity() {
        $iQty = $this->oVariant->getSuggestedMarketplaceStock(
            MLModul::gi()->getConfig('quantity.type'),
            MLModul::gi()->getConfig('quantity.value')
        );
        return $iQty < 0 ? 0 : $iQty;
    }

    protected function getPrice() {
        if (isset($this->aSelectionData['price'])) {
            return $this->aSelectionData['price'];
        } else {
            return $this->oVariant->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject());
        }
    }

    protected function getBasePrice() {
        return $this->oVariant->getBasePrice();
    }

    protected function getShippingService() {
        return $this->oPrepare->get('ShippingService');
    }

    protected function getMarketplaceCategory() {
        return $this->oPrepare->get('PrimaryCategory');
    }

    protected function getShopCategory() {
        return $this->oVariant->getCategoryIds(false);
    }

    protected function getShopCategoryStructure() {
        return $this->oVariant->getCategoryStructure(false);
    }

    /**
     * @todo use new custom field to each shop(magento , shopware, prestashop) to return each product leadtimetoship
     */
    protected function getShippingTime() {
        return MLModul::gi()->getConfig('checkin.leadtimetoship');
    }

    protected function getShippingGroup() {
        return MLModul::gi()->getConfig('checkin.shippinggroup');
    }

    protected function getListingDuration() {
        return $this->oPrepare->get('ListingDuration');
    }

    protected function getProductType() {
        return $this->oPrepare->get('ProductType');
    }

    protected function getReturnPolicy() {
        return $this->oPrepare->get('ReturnPolicy');
    }

    protected function getManufacturer() {
        return $this->oVariant->getManufacturer();
    }

    public function getManufacturerPartNumber() {
        return $this->oVariant->getManufacturerPartNumber();
    }

    protected function getEan() {
        return $this->oVariant->getEan();
    }

}

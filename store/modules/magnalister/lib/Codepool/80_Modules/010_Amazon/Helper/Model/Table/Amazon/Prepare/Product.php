<?php
/**
 * @todo use form modul
 */
class ML_Amazon_Helper_Model_Table_Amazon_Prepare_Product {
    protected $oPrepare = null;
    protected $aModulConfig = array();
    protected $oProduct = null;

    public function __construct() {
        $this->oPrepare = MLDatabase::factory('amazon_prepare');
        $this->aModulConfig = MLModul::gi()->getConfig();
    }

    public function apply(ML_Shop_Model_Product_Abstract $oProduct, $aData = array()) {
        $this->oProduct = $oProduct;
        $iPID = $oProduct->get('id');
        $iPID ? $this->init('apply') : $this->oPrepare->set('preparetype', 'apply');
        $sManufacturer = $iPID ? $this->oProduct->getParent()->getModulField('general.manufacturer', true) : '';
        if (empty($sManufacturer)) {
            $sManufacturer = $this->aModulConfig['prepare.manufacturerfallback'];
        }
        if (isset($this->aModulConfig['checkin.skuasmfrpartno']) && $this->aModulConfig['checkin.skuasmfrpartno']) {
            $sManufacturerPartNumber = $this->oProduct->getSku();
        } else {
            $sManufacturerPartNumber = $iPID ? $this->oProduct->getModulField('general.manufacturerpartnumber', true) : '';
        }

        $sDescription = $iPID ? $this->oProduct->getParent()->getDescription() : '';
        $sDescription = $this->amazonSanitizeDescription($sDescription);
        $sEan = $iPID ? $this->oProduct->getModulField('general.ean', true) : '';
        $aBasePrice = $iPID ? $this->oProduct->getBasePrice() : array();
        $aImages = array();
        if ($iPID) {
            foreach ($this->oProduct->getParent()->getImages() as $sImage) {
                $aImages[$sImage] = true;
            }
        }
        $this->oPrepare
            ->set('aidenttype', 'EAN')
            ->set('aidentid', $sEan)
            ->set('maincategory', '')
            ->set('topmaincategory', '')
            ->set('topproducttype', '')
            ->set('topbrowsenode1', '')
            ->set('topbrowsenode2', '')
            ->set('ConditionType', $this->aModulConfig['itemcondition'])
            ->set('ConditionNote', '')
            ->set('ApplyData',
                array(
                    'ProductType' => '',
                    'BrowseNodes' => array(),
                    'ItemTitle' => $iPID ? $this->oProduct->getParent()->getName() : '',
                    'Manufacturer' => $sManufacturer,
                    'Brand' => $sManufacturer,
                    'ManufacturerPartNumber' => $sManufacturerPartNumber,
                    'EAN' => $sEan,
                    'Images' => $aImages,
                    'BulletPoints' => $this->stringToArray($iPID ? $this->oProduct->getParent()->getMetaDescription() : '', 5, 500),
                    'Description' => $sDescription,
                    'Keywords' => $this->stringToArray($iPID ? $this->oProduct->getParent()->getMetaKeywords() : '', 5, 1000),
                    'Attributes' => array(),
                    'BasePrice' => $aBasePrice
                )
            );

        if (isset($aData['ShippingTime'])) {
            if ($aData['ShippingTime'] != 'X') {
                $this->oPrepare
                    ->set('leadtimetoship', is_numeric($aData['ShippingTime']) ? $aData['ShippingTime'] : 0) //deprecated
                    ->set('shippingtime', is_numeric($aData['ShippingTime']) ? $aData['ShippingTime'] : 0);
            }
            unset($aData['ShippingTime']);
        }

        if (isset($aData['ApplyData']['EAN'])) {
            $this->oPrepare->set('aidentid', $aData['ApplyData']['EAN']);
        }

        // all values will set to prepare table (may unset some)
        $this->setData($aData);
        return $this;
    }

    public function auto(ML_Shop_Model_Product_Abstract $oProduct, $aData = array()) {
        $this->oProduct = $oProduct;
        $this->init('auto')->matching();
        $sEan = $this->oProduct->getModulField('general.ean', true);
        $this->oPrepare
            ->set('aidenttype', 'EAN')
            ->set('aidentid', $sEan === null ? '' : $sEan);
        $this->setData($aData);
        return $this;
    }

    public function manual(ML_Shop_Model_Product_Abstract $oProduct, $aData = array()) {
        $this->oProduct = $oProduct;
        $this->init('manual')->matching();
        $this->oPrepare
            ->set('aidenttype', 'ASIN')
            ->set('aidentid', '')//get from $aData
        ;
        $this->setData($aData);
        return $this;
    }

    public function getTableModel() {
        return $this->oPrepare;
    }

    protected function matching() {
        $this->oPrepare
            ->set('price', $this->oProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject(), true, false))
            ->set('conditiontype', $this->aModulConfig['itemcondition'])
            ->set('conditionnote', '')
            ->set('lowestprice', 0.0)
            ->set('shipping', $this->aModulConfig['internationalshipping'])
            ->set('LeadtimeToShip', $this->aModulConfig['leadtimetoship'])
            ->set('ShippingTime', $this->aModulConfig['leadtimetoship']);
    }

    protected function init($sPrepareType) {
        $this->oPrepare->init(true)->set('productsid', $this->oProduct->get('id'))->load();
        $this->oPrepare->set('preparetype', $sPrepareType);
        return $this;
    }

    protected function setData($aData) {
        foreach ($aData as $sKey => $mValue) {
            if ($sKey == 'ApplyData') {
                $aApply = $this->oPrepare->get('applyData');
                foreach ($mValue as $sDataKey => $mDataValue) {
                    $aApply[$sDataKey] = $mDataValue;
                }
                $this->oPrepare->set('applyData', $aApply);
            } else {
                $this->oPrepare->set($sKey, $mValue);
            }
        }
        return $this;
    }

    protected function amazonSanitizeDescription($sDescription) {
        $sDescription = str_replace(array('&nbsp;', html_entity_decode('&nbsp;', ENT_COMPAT, 'UTF-8')), ' ', $sDescription);
        $sDescription = sanitizeProductDescription(
            $sDescription,
            '',
            '_keep_all_'
        );
        $sDescription = str_replace(array('<br />', '<br/>'), '<br>', $sDescription);
        $sDescription = preg_replace('/(\s*<br[^>]*>\s*)*$/', '', $sDescription);
        $sDescription = preg_replace('/\s\s+/', ' ', $sDescription);
        $sDescription = mb_substr($sDescription, 0, 2000, 'UTF-8');
        return $sDescription;
    }

    protected function stringToArray($sString, $iCount, $iMaxChars) {
        $aArray = explode(',', $sString);
        array_walk($aArray, array($this, 'trim'));
        $aOut = array_slice($aArray, 0, $iCount);
        foreach ($aOut as $sKey => $sBullet) {
            $aOut[$sKey] = trim($sBullet);
            if (empty($aOut[$sKey])) {
                continue;
            }
            $aOut[$sKey] = substr($sBullet, 0, strpos(wordwrap($sBullet, $iMaxChars, "\n", true)."\n", "\n"));
        }
        return array_pad($aOut, $iCount, '');
    }

    protected function trim(&$v, $k) {
        $v = trim($v);
    }
}

<?php

class ML_Magento_Model_Product extends ML_Shop_Model_Product_Abstract {

    /**
     *
     * @var Mage_Catalog_Model_Product $oProduct
     */
    protected $oProduct = null;

    /**
     * only by child products
     * @var Mage_Catalog_Model_Product $oCartItem
     */
    protected $oCartItem = null;

    /**
     * array of Mage_Catalog_Model_Product
     * @var array $aMagentoProducts
     */
    protected static $aMagentoProducts = array();
    
    /**
     * @var array of skus of already searched articels
     */
    protected static $aCreateModelProductByMarketplaceSkuSearched = array();
    
    
    /**
     * image-cache
     * @var null default
     * @var array loaded images for not calculating multiple times
     */
    protected $aImages = null;
    
    public function __construct() {
        parent::__construct();
    }

    /**
     * 
     * @param ML_Shop_Model_Product_Abstract $oModel
     * @param Mage_Catalog_Model_Product $oProduct
     */
    protected static function addStaticProduct($oModel, $oProduct) {
        $oProduct->load($oProduct->getId());
        $sKey = MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.keytype')->get('value') == 'pID' ? 'productsid' : 'productssku';
        self::$aMagentoProducts[$oModel->get($sKey)] = $oProduct;
    }

    protected static function getStaticProduct($oModel) {
        $aKey = MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.keytype')->get('value') == 'pID' ? array('productsid', 'id') : array('productssku', 'sku')
        ;
        try {
            $iLang = MLModul::gi()->getConfig('lang');
        } catch (Exception $oEx) {
            $iLang = 0;
        }
        if (!array_key_exists($oModel->get($aKey[0]), self::$aMagentoProducts)) {
            self::$aMagentoProducts[$oModel->get($aKey[0])] = Mage::helper('catalog/product')->getProduct(
                $oModel->get($aKey[0]), Mage::app()->getStore($iLang)->getId(), $aKey[1]
            );
        }
        return self::$aMagentoProducts[$oModel->get($aKey[0])];
    }

    public function getVariantCount() {
        $this->load();
        $oVariantCalculator = MLHelper::gi('model_product_variants')->setProduct($this->oProduct);
        $iCount = $oVariantCalculator->getVariantCount();
        foreach ($oVariantCalculator->getMessages() as $sMessage) {
            MLMessage::gi()->addObjectMessage($this, $sMessage);
        }
        return $iCount;
    }

    protected function loadShopVariants() {
        //calc variants
        $oVariantCalculator = MLHelper::gi('model_product_variants')->setProduct($this->oProduct);
        $aVariants = $oVariantCalculator->getVariants();
        // set data
        $this
            ->set('data', array('messages' => $oVariantCalculator->getMessages()))
            ->save()
        ;
        foreach ($oVariantCalculator->getMessages() as $sMessage) {
            MLMessage::gi()->addObjectMessage($this, $sMessage);
        }
        $iSkuProb = 0;
        foreach ($aVariants as $aVariant) {
            $oVariant = MLProduct::factory()->loadByShopProduct(
                $this->oProduct, $this->get('id'), array_merge_recursive(array('shop' => array('qty' => 1)), $aVariant)
            );
            if ($oVariant->allKeysExists()) {
                $this->addVariant($oVariant);
            } else {
                ++$iSkuProb;
            }
        }
        if ($iSkuProb > 0) {
            MLMessage::gi()->addObjectMessage(
                $this, MLI18n::gi()->get(
                    'Productlist_ProductMessage_sNoDistinctSku', array('count' => $iSkuProb)
                )
            );
        }
    }

    public function loadShopProduct() {
        if ($this->oProduct === null) {//not loaded
            $this->oProduct = false; //not null
            if ($this->get('parentid') == 0) {
                $oProduct = $this;
            } else {
                $oProduct = $this->getParent();
                $oProduct->getVariants();//preload variants
            }
            $oShopProduct = self::getStaticProduct($oProduct);
            if ($oShopProduct->getId() === null) {
                $this->delete();
                throw new Exception('product dont exists in shop');
            }
            $aData = $this->get('shopdata');
            $this->oProduct = $oShopProduct;
            $this->prepareProductForMarketPlace();
            if ($this->get('parentid') != 0) {//is variant
                $sKey = MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.keytype')->get('value');
                if (!isset($aData['product']) || !is_object($mData['product'])) {
                    $aData['product'] = self::getStaticProduct($this);
                    if ($aData['product']->getSku() === null) {//options sku fix
                        $aData['product'] = $this->oProduct;
                    }
                }
                $this->loadByShopProduct($oShopProduct, $this->get('parentid'), $aData);
            }
        }
        return $this;
    }

    public function loadByShopProduct($mProduct, $iParentId = 0, $mData = null) {
        //need for pricecalc
        $this->oProduct = current(
            $mProduct->getTypeInstance()->prepareForCartAdvanced(
                new Varien_Object($mData['shop']), clone $mProduct, Mage_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_LITE
            )
        ); //needed for price
        $this->prepareProductForMarketPlace();
        //set filter
        $sKey = MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.keytype')->get('value');
        $this->aKeys = array($sKey == 'pID' ? 'marketplaceidentid' : 'marketplaceidentsku');
        $this->set('parentid', $iParentId)->aKeys[] = 'parentid';
        if ($iParentId == 0) {
            // set data
            $sSku = $this->oProduct->getSku();
            $sSku = $sSku === null ? '' : $sSku;
            $this
                    ->set('marketplaceidentid', $this->oProduct->getId())
                    ->set('marketplaceidentsku', $sSku)
                    ->set("productsid", $this->oProduct->getId())
                    ->set("productssku", $sSku)
                    ->set('shopdata', array())
                    ->save()
                ->aKeys = array('id')
            ;
            self::addStaticProduct($this, $this->oProduct);
        } else {
            $sAddId = '';
            $aNameDatas = array();
            if (isset($mData['shop']['super_attribute'])) {
                $aNameDatas['s'] = $mData['shop']['super_attribute'];
            }
            $blSkuProblem = false;
            $aOptionSkus = '';
            if (isset($mData['shop']['options'])) {
                if (MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.keytype')->get('value') != 'pID') {
                    foreach ($mData['shop']['options'] as $sKey => $sValue) {
                        $sOptionSku = $this->oProduct->getOptionById($sKey)->getValueById($sValue)->getSku();
                        if ($sOptionSku == '') {
                            return $this;
                        } else {
                            $aOptionSkus[] = $sOptionSku;
                        }
                    }
                }
                $aNameDatas['o'] = $mData['shop']['options'];
            }
            foreach ($aNameDatas as $sType => $aNameData) {
                foreach ($aNameData as $iKey => $sData) {
                    $sAddId.=$iKey . $sType . $sData . ', ';
                }
            }
            $sAddId = $sAddId != '' ? '(' . substr($sAddId, 0, -2) . ')' : '';
            $this->oCartItem = $mData['product'];

            //$oCart=Mage::getSingleton('checkout/cart')->getQuote();

            unset($mData['product']);
            $sSku = $this->oCartItem->getSku();
            $sSku = $sSku === null ? '' : $sSku;
            if (!empty($aOptionSkus)) {
                $sSku.='-' . implode('-', $aOptionSkus);
            }
            $this
                ->set('marketplaceidentid', $this->oProduct->getId() . $sAddId)
                ->set('marketplaceidentsku', $sSku)
                ->set('productsid', $this->oCartItem->getId())
                ->set('productssku', $sSku)
                ->set('shopdata', $mData)
                ->set('data', array('messages' => $this->getMessages()))
            ;
            self::addStaticProduct($this, $this->oCartItem);
            $this->load();
            if ($this->exists()) {
                $this->aKeys = array('id');
                $this->save();
            } else {
                $this->save()->aKeys = array('id');
            }
        }
        return $this;
    }

    public function getImageUrl($iX = 40, $iY = 40) {
        $this->load();
        $aImages = $this->getImages();
        return (empty($aImages)) ? '' : MLImage::gi()->resizeImage(current($aImages), 'products', $iX, $iY, true);
    }

    public function getName() {
        $this->load();
        $sAdd = '';
        if ($this->oCartItem !== null) {
            $aShopData = $this->get('shopdata');
            $aNameDatas = array();
            if (isset($aShopData['magna']['super_attribute'])) {
                $aNameDatas[] = $aShopData['magna']['super_attribute'];
            }
            if (isset($aShopData['magna']['options'])) {
                $aNameDatas[] = $aShopData['magna']['options'];
            }
            foreach ($aNameDatas as $aNameData) {
                foreach ($aNameData as $aData) {
                    $sAdd.=$aData['label'] . ': ' . $aData['title'] . ', ';
                }
            }
            if ($sAdd != '') {
                $sAdd = ' (' . substr($sAdd, 0, -2) . ')';
            }
        }
        return $this->getMagentoProduct()->name . $sAdd;
    }

    public function getEditLink() {
        $this->load();
        $aShopData = $this->get('shopdata');
        $aParams = array('store' => $this->getMagentoProduct()->getStoreId(),);
        if (!isset($aShopData['shop']['super_attribute'])) {
            $aParams['id'] = $this->getMagentoProduct()->getId();
        } else {
            // commented code links to master-variant
            //$aParams['required'] = implode(',', $aShopData['shop']['super_attribute']);
            //$aParams['product'] = $this->getMagentoProduct()->getId();
            $aParams['id'] = $this->oCartItem->getId();
        }
        if (!MLSetting::gi()->get('blDebug')) {
            $aParams['popup'] = 1;
        }
        return Mage::getModel('adminhtml/url')->getUrl(
                'adminhtml/catalog_product/edit', $aParams
        );
    }
    
    public function getFrontendLink() {
        if ($this->get('parentid') == 0) {
            $oProduct = $this->getMagentoProduct();
        } else {
            $oProduct = $this->getParent()->getMagentoProduct();
        }
        return $oProduct->getUrlInStore();
    }

    public function getShopPrice($blGros = true, $blFormated = false) {
        $this->load();
        $mReturn = $this->getPrice($blGros, $blFormated/* ,false */);
        return $mReturn;
    }

    /**
     * @return int
     */
    public function getStock() {
        $this->load();        
        if (
            $this->getMagentoProduct()->getStockItem()->getManageStock() // stock management
            &&
            !$this->getMagentoProduct()->getStockItem()->getIsInStock() // is not in stock
        ) { // master have no stock
            return 0;
        } elseif (
            $this->getMagentoProduct()->getEntityId() == $this->getRealProduct()->getEntityId()
            && $this->getMagentoProduct()->getTypeId() != 'simple'
        ) { // master, configurable
            $iStock = 999;
            /*  // exact calc, we dont need
                foreach ($this->getVariants() as $oVariant) {
                    $iVariantStock = $oVariant->getStock();
                    $iStock += $iVariantStock >= 0 ? $iVariantStock : 0;
                } 
             */
            return $iStock;
        } else { // variant
            if (!$this->getRealProduct()->getStockItem()->getManageStock()) {
                return 999;
            } elseif (!$this->getRealProduct()->getStockItem()->getIsInStock()) {
                return 0;
            } else {
                return (int) $this->getRealProduct()->getStockItem()->getQty();
            }
        }
    }

    public function setStock($iStock) {
        $this->load();
        $this->getRealProduct()->getStockItem()->setQty($iStock)->save();
        return $this;
    }

    public function getSuggestedMarketplaceStock($sType, $fValue, $iMax = null) {
        if(
            MLModul::gi()->getConfig('inventar.productstatus') == 'true'
            && !$this->isActive()
        ) {
            return 0;
        }
        if ($sType == 'lump') {
            $iStock = $fValue;
        } else {
            $iStock = $this->getStock();
            if ($sType == 'stocksub') {
                $iStock = $iStock - $fValue;
            }
        }
        if ($this->oCartItem !== null && $sType != 'lump') {//check for custom options, will divided for current product
            $aCartItemInfo = $this->get('shopdata');
            if (isset($aCartItemInfo['shop']['options'])) {
                $iCountProducts = 0;
                foreach ($this->getParent()->loadShopProduct()->getVariants() as $oVariant) {
                    if ($oVariant->getRealProduct()->getId() == $this->getRealProduct()->getId()) {
                        ++$iCountProducts;
                    }
                }
                $iStock = $iStock / $iCountProducts;
            }
        }
        if ($sType != 'lump' && !empty($iMax)) {
            $iStock = min((int) $iStock, $iMax);
        } else {
            $iStock = (int) $iStock;
        }
        return $iStock > 0 ? $iStock : 0;
    }

    public function getSuggestedMarketplacePrice(ML_Shop_Model_Price_Interface $oPrice, $blGros = true, $blFormated = false) {
        $this->load();
        $aConf = $oPrice->getPriceConfig();
        $fTax = $aConf['tax'];
        $sPriceKind = $aConf['kind'];
        $fPriceFactor = (float) $aConf['factor'];
        $iPriceSignal = $aConf['signal'];
        $blSpecialPrice = $aConf['special'];
        if (!$blSpecialPrice) {
            $this->getMagentoProduct()->setSpecialPrice(0);
        }
        $sCustomerGroup = $aConf['group'];
        $this->getMagentoProduct()->setCustomerGroupId($sCustomerGroup);
        $mReturn = $this->getPrice($blGros, $blFormated, /* $blSpecial, */ $sPriceKind, $fPriceFactor, $iPriceSignal, $fTax);
        return $mReturn;
    }

    protected function prepareProductForMarketPlace() {
        try {
            $aConf = MLModul::gi()->getConfig();
            if ($this->getMagentoProduct()->getStoreId() != $aConf['lang']) {
                $this->getMagentoProduct()->setStoreId($aConf['lang'])->load($this->getMagentoProduct()->getId());
            }
            Mage::app()->getStore($aConf['lang']);
            $oStore = $this->getMagentoProduct()->getStore();
            if ($oStore->getCurrentCurrency()->getCode() != $aConf['currency']) {
                $oCurrency = Mage::getModel('directory/currency')->load($aConf['currency']);
                $oStore->setCurrentCurrency($oCurrency);
            }
        } catch (Exception $oEx) {
            
        }
    }

    /**
     * 
     * @param Mage_Catalog_Model_Product $oProduct
     * @param type $blGros
     * @param type $blFormated
     * @return type
     */
    protected function getPrice($blGros, $blFormated, $sPriceKind = '', $fPriceFactor = 0.0, $iPriceSignal = null, $fTax = null) {
        $aData = $this->get('shopdata');
        $oProduct = $this->getMagentoProduct();
        $oProduct->setFinalPrice(null);
        /* @var $oTax Mage_Tax_Helper_Data */
        $oTax = Mage::helper('tax');
        /* @var $oCurrency Mage_Core_Helper_Data */
        $oCurrency = Mage::helper('core');
        $oPrice = MLPrice::factory();
        // 1. calc brutprice
        $fBrutPrice = $oCurrency->currencyByStore($oTax->getPrice($oProduct, $oProduct->getFinalPrice(), true), $oProduct->getStore(), false, false);
        $fPercent = $oProduct->getTaxPercent();
        if ($fPercent === null) {
            $fPercent = 0;
        }
        if($fTax !== null) {
            $fNetOriginalPrice = $oPrice->calcPercentages($fBrutPrice, null, $fPercent);
            $fBrutPrice = $oPrice->calcPercentages(null, $fNetOriginalPrice, $fTax);
            $fPercent = $fTax;
        }
        // 2. add modulprice to brut
        if ($sPriceKind == 'percent') {
            $fBrutPrice = $oPrice->calcPercentages(null, $fBrutPrice, $fPriceFactor);
        } elseif ($sPriceKind == 'addition') {
            $fBrutPrice = $fBrutPrice + $fPriceFactor;
        }
        if ($iPriceSignal !== null) {
            $fBrutPrice = ((int) $fBrutPrice) + ($iPriceSignal / 100);
        }
        // 3. calc netprice from modulprice
        $fNetPrice = $oPrice->calcPercentages($fBrutPrice, null, $fPercent);
        // 4. define out price (unformated price of current shop)
        $fUsePrice = $blGros ? $fBrutPrice : $fNetPrice;
        if ($blFormated) {//recalc currency and format
            $fOutPrice = $fUsePrice / ($oProduct->getStore()->getCurrentCurrencyRate());
            return $oCurrency->currencyByStore($fOutPrice, $oProduct->getStore(), true);
        } else {
            return $fUsePrice;
        }
    }
    
    /**
     * returns tax-value for product. if $aAdressData is set, it try to locate tax for address
     */
    public function getTax( $aAddressData = null ) {
        $oProduct = $this->getMagentoProduct();
        if ($aAddressData === null) {
            $oAddress = null;
        } else {
            $oAddress = new Varien_Object();
            $oAddress 
                ->setCountryId($aAddressData['CountryCode'])
                ->setPostcode($aAddressData['Postcode'])//some postcode
            ;
            if (array_key_exists('Suburb', $aAddressData) && !empty($aAddressData['Suburb'])) {
                $oAddress->setRegionId(Mage::getModel('directory/region')
                    ->loadByCode($aAddressData['Suburb'], $aAddressData['CountryCode'])
                    ->getId()
                );
            }
        }
        $oTaxCalc = Mage::getSingleton('tax/calculation')->getRateRequest($oAddress, null, null, $oProduct->getStore());
        $fPercent = Mage::getSingleton('tax/calculation')->getRate($oTaxCalc->setProductClassId($oProduct->getData('tax_class_id')));
        return $fPercent;
    }

    public function getTaxClassId() {
        $oProduct = $this->getMagentoProduct();
        return $oProduct['tax_class_id'];
    }

    /**
     * @param type $sFieldName
     * @param type $blGeneral
     * @return null
     */
    public function getModulField($sFieldName, $blGeneral = false) {
        $this->load();
        if ($blGeneral) {
            $sValue = MLDatabase::factory('config')->set('mpid', 0)->set('mkey', $sFieldName)->get('value');
        } else {
            $sValue = MLModul::gi()->getConfig($sFieldName);
        }
        if ($sValue) {
            return $this->__get($sValue);
        } else {
            return null;
        }
    }

    protected function getMagentoProduct() {
        if ($this->oProduct === null) {
            $this->load();
        }
        return $this->oProduct;
    }

    /**
     * get Mage_Catalog_Model_Product depending on cartitem
     * @return Mage_Catalog_Model_Product
     */
    protected function getRealProduct() {
        /* @var $oCurrent Mage_Catalog_Model_Product */
        if ($this->oCartItem instanceof Mage_Catalog_Model_Product) {
            $oCurrent = $this->oCartItem;
        } else {
            $sKey = MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.keytype')->get('value') == 'pID' ? 'productsid' : 'productssku';
            if (array_key_exists($this->get($sKey), self::$aMagentoProducts)) {
                $this->oCartItem = self::$aMagentoProducts[$this->get($sKey)];
                $oCurrent = $this->oCartItem;
            } else {
                MLMessage::gi()->addDebug('Magento Variant not found', $this->data(false));
                $oCurrent = $this->getMagentoProduct();
            }
        }
        $oCurrent->toArray(); //load
        return $oCurrent;
    }

    /**
     * @param string $sName
     * @return mixed
     */
    public function __get($sName) {
        $this->load();
        $oCurrent = $this->getRealProduct();
        if (
            $this->oCartItem !== null &&
            $sName == MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.ean')->get('value')
        ) {//ean - dont use with custom options
            $aInfo = $this->get('shopdata');
            if (
                isset($aInfo['shop']['options']) &&
                count($aInfo['shop']['options']) != 0
            ) {
                return null;
            }
        }
        $mValue = $oCurrent->getResource()->getAttribute($sName)->getFrontend()->getValue($oCurrent);
        if ($mValue === null) {
            $mValue = $oCurrent->getResource()->getAttribute($sName)->getDefaultValue();
        }
        return $mValue;
    }

    /**
     * walks products image-gallery, skips disabled images and set base-image as first item
     * @return array /path/to/image, ..
     */
    public function getImages() {
        $aOut = array();
        $sBaseImageName = $this->getRealProduct()->getImage();
        if ($sBaseImageName != 'no_selection') {
            $oMediaConfig = Mage::getModel('catalog/product_media_config');
            $aOut[] = $oMediaConfig->getMediaPath($sBaseImageName);
        }
        $oImages = $this->getRealProduct()->getMediaGalleryImages();
        if ($oImages !== null) {
            foreach ($oImages as $oImage) {
                if ($oImage->getFile() != $sBaseImageName && !$oImage->getDisabled()) {
                    $aOut[] = $oImage->getPath();
                }
            }
        }
        return $aOut;
    }
    
    public function getAllImages() {
        if($this->aImages === null){
            $this->aImages = $this->getImages();
            foreach($this->getVariants() as $oVariant){
                $this->aImages = array_merge($this->aImages, $oVariant->getImages());
            }
        }
        return $this->aImages;
    }

    public function getDescription() {
        return $this->getRealProduct()->getDescription();
    }

    public function getShortDescription() {
        return $this->getRealProduct()->getShortDescription();
    }

    public function getMetaDescription() {
        return $this->getRealProduct()->getMetaDescription();
    }

    public function getMetaKeywords() {
        return $this->getRealProduct()->getMetaKeyword();
    }

    public function getCategoryPath() {
        $sPath = '';
        /* @var $oProduct Mage_Catalog_Model_Product */
        $oProduct = $this->getMagentoProduct();
        $iCatId = current($oProduct->getCategoryIds());
        /* @var $oCat Mage_Catalog_Model_Category */
        $oCat = Mage::getModel('catalog/category')->load($iCatId);
        $iBaseCatId = $oProduct->getStore()->getRootCategoryId();
        while (!in_array($oCat->getId(), array(0, $iBaseCatId))) {
            $sPath = $oCat->getName() . ($sPath == '' ? '' : ' > ' . $sPath);
            $oCat = $oCat->getParentCategory();
        }
        return $sPath;
    }

    public function getCategoryIds($blIncludeRootCats = true) {
        $aCatIds = $this->getMagentoProduct()->getCategoryIds();
        if (!$blIncludeRootCats) {
            $aRootCatIds = $this->getRootCategoriesIds();
            foreach ($aCatIds as $iCat => $iCatId) {
                if (in_array($iCatId, $aRootCatIds)) {
                    unset ($aCatIds[$iCat]);
                }
            }
        }
        return $aCatIds;
    }
    
    protected function getRootCategoriesIds () {
        $aOut = array();
        $oProduct = $this->getMagentoProduct();	
        $oBaseCat = Mage::getModel('catalog/category')->load($oProduct->getStore()->getRootCategoryId());
        while (is_object($oBaseCat) && $oBaseCat->getId() !== null) {
            $aOut[] = $oBaseCat->getId();
            $oBaseCat = $oBaseCat->getParentCategory();
        }
        return $aOut;
    }
   
    public function getCategoryStructure($blIncludeRootCats = true) {
        $aCategories = array();
        $aRootCatIds = $aExistedCatId = $blIncludeRootCats ? array() : $this->getRootCategoriesIds();
        $oProduct = $this->getMagentoProduct();
        foreach ($oProduct->getCategoryIds() as $iCatId) {
            $oCat = Mage::getModel('catalog/category')->load($iCatId);
            do {                             
                if(in_array($oCat->getId() , $aExistedCatId)){
                    break;
                }
                $aCurrentCat = array(
                    'ID' => $oCat->getId(),
                    'Name' => $oCat->getName(),
                    'Description' => $oCat->getDescription(),
                    'Status' => true,
                );
                $aExistedCatId[] = $oCat->getId();
                $oCat = $oCat->getParentCategory();
                if(is_object($oCat) && $oCat->getId() !== null && !in_array($oCat->getId(), $aRootCatIds)){                    
                    $aCurrentCat['ParentID'] = $oCat->getId();
                }
                $aCategories[] = $aCurrentCat;
            } while (is_object($oCat) && $oCat->getId() !== null);
        }
        return $aCategories;
    }

    public function getVariatonData() {        
        return $this->getVariatonDataOptinalField(array('name','value'));
    }
    
    public function getVariatonDataOptinalField($aFields = array()) {
        $aOut = array();
        $aShopData = $this->get('shopdata');
        foreach (array('super_attribute', 'options') as $sType) {
            if (isset($aShopData['magna'][$sType])) {
                foreach ($aShopData['magna'][$sType] as $aInfo) {
                    $aData = array();
                    if(in_array('code',$aFields)){//an identifier for group of attributes , that used in Meinpaket at the moment
                        $aData['code'] = strtolower($aInfo['code']);                                
                    }
                    if(in_array('valueid',$aFields)){//an identifier for value of attributes , that used in Meinpaket at the moment
                        $aData['valueid']=  isset($aInfo["value_index"])?$aInfo["value_index"] : $aInfo["option_id"];  //shold be check if this field always exist in magetno products                              
                    }                     
                    if(in_array('name',$aFields)){
                        $aData['name']=  $aInfo['label'];                                
                    }
                    if(in_array('value',$aFields)){
                        $aData['value']=  $aInfo['title'];                                
                    }
                    $aOut[] = $aData;
                }
            }
        }        
        return $aOut;
    }

    public function isActive() {
        $this->load();
        return 
            $this->getRealProduct()->getStatus() == 1 && (
                $this->get('parentid') == 0 
                ? true 
                : $this->getParent()->isActive()
        );
    }

    public function createModelProductByMarketplaceSku($sSku) {
        $aFilterValues = array();
        if (MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.keytype')->get('value') == 'pID') {
            $sFilterAttribute = 'entity_id';
            $aFilterValues[] = (int) $sSku;
        } else {
            $sFilterAttribute = 'sku';
            // we load/create several products, which depends on sku
            // perhaps we found correct product and it can be loaded
            // if not however - its now in products-table
            for ($i = strlen($sSku); $i > 0; --$i) {
                $sSearchSku = substr($sSku, 0, $i);
                if (!in_array($sSearchSku, self::$aCreateModelProductByMarketplaceSkuSearched)) {
                    self::$aCreateModelProductByMarketplaceSkuSearched[] = $sSearchSku;
                    $aFilterValues[] = $sSearchSku;
                }
            }
        }
        if (empty($aFilterValues)) { // all values was searched in current request
            return $this;
        }
        $oCollection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToFilter($sFilterAttribute, $aFilterValues)
            ->addAttributeToFilter('type_id', array('simple', 'configurable', 'virtual'))
        ;
        // marketplace language
        try {
            $aConfig = MLModul::gi()->getConfig();
            $iStoreId = $aConfig['lang'];
            $oCollection->setStore($iStoreId);
        } catch (Exception $oEx) {//no-modul   
        }

        $oSelect = $oCollection->getSelectSql();
        $oCache = MLCache::gi();

        // filter products with multiple options (only allow dropdown or radio)
        if (!$oCache->exists(strtoupper(get_class($this)) . '__magento_filter_options')) {
            $aOptions = array();
            /* @var $oOptions Mage_Catalog_Model_Resource_Product_Option_Collection */
            $oOptions = Mage::getModel('catalog/product_option')->getCollection();
            $oOptionsSelect = $oOptions->getSelect();
            $oOptionsSelect->where("type not in('drop_down', 'radio')");
            foreach ($oOptions as $oOption) {
                $aOptions[] = $oOption->getProductId();
            }
            $oCache->set(strtoupper(get_class($this)) . '__magento_filter_options', $aOptions, 60 * 60 * 2);
        } else {
            $aOptions = $oCache->get(strtoupper(get_class($this)) . '__magento_filter_options');
        }
        $oSelect->where("e.entity_id not in('" . implode("', '", array_unique($aOptions)) . "')");

        // get master, if products are in catalog_super_link (are parts of configurable)
        if (!$oCache->exists(strtoupper(get_class($this)) . '__magento_filter_super')) {
            $aSuper = array();
            foreach (MLDatabase::getDbInstance()->fetchArray("select product_id, parent_id from " . Mage::getSingleton('core/resource')->getTableName('catalog_product_super_link')) as $aRow) {
                $aSuper[$aRow['product_id']] = $aRow['parent_id'];
            }
            $oCache->set(strtoupper(get_class($this)) . '__magento_filter_super', $aSuper, 60 * 60 * 2);
        } else {
            $aSuper = $oCache->get(strtoupper(get_class($this)) . '__magento_filter_super');
        }
        foreach ($oCollection->load() as $oProduct) {
            $oMyTable = MLProduct::factory();
            if (isset($aSuper[$oProduct->getId()])) {//product is not master
                $oMaster = Mage::getModel('catalog/product')->load($aSuper[$oProduct->getId()]);
                $oMyTable->loadByShopProduct($oMaster);
            } else {
                $oMyTable->loadByShopProduct($oProduct);
            }
            try {
                $oMyTable->getVariants(); // also get the variants
            } catch (Exception $oEx) {
                
            }
        }
        return $this;
    }

    public function getWeight() {
        $sUnit = MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.weightunit')->get('value');
        $fWeight = (float)$this->getRealProduct()->getWeight();
        if($fWeight > 0 ){
            return array(
                "Unit" => $sUnit === null ? "KG" : $sUnit,
                "Value"=>  $fWeight, 
            );
        }else{
            return array();
        }
    }
    
    /**
     * @todo add magento base price
     */
    public function getBasePriceString($fPrice, $blLong = true) {
        return '';
    }
    public function getBasePrice() {
        /**
         * @todo add magento base price
         */
        return array();
    }

    public function setLang($iLang) {
        if ($this->getMagentoProduct()->getStoreId() != $iLang) {
            $this->getMagentoProduct()->setStoreId($iLang)->load($this->getMagentoProduct()->getId());
            if (array_key_exists($this->get('productssku'), self::$aMagentoProducts)) {
                self::$aMagentoProducts[$this->get('productssku')]->setStoreId($iLang)->load(self::$aMagentoProducts[$this->get('productssku')]->getId());
            }
            $this->oCartItem = null;
        }
    }

    public function getAttributeValue($mAttributeCode) {
        return $this->$mAttributeCode;
    }

    public function getManufacturer() {
        return $this->getModulField('manufacturer');
    }

    public function getManufacturerPartNumber() {
        return $this->getModulField('manufacturerpartnumber');
    }
    
    public function getEAN() {
        return $this->getModulField('ean');
    }

}

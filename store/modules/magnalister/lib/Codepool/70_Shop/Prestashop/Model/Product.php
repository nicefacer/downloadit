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
class ML_Prestashop_Model_Product extends ML_Shop_Model_Product_Abstract  {

    const ID_FIELD_NAME = 'p.`id_product`';
    const SKU_FIELD_NAME = 'p.`reference`';

    /**
     *  @var ProductCore $oProduct
     */
    protected $oProduct = null;

    protected $iVariantCount = null;

    public function getVariantCount() {
        if ($this->iVariantCount === null) {
            $this->load();
            $sSql = 'SELECT count(*)  FROM `'._DB_PREFIX_.'product_attribute` pa '.Shop::addSqlAssociation('product_attribute', 'pa').'
                                    WHERE pa.`id_product` = '.(int)$this->oProduct->id.'';
			if(count(Shop::getContextListShopID()) > 1){
				$sSql = str_replace('.id_shop IN ('.implode(', ', Shop::getContextListShopID()).')', '.id_shop IN ('.Context::getContext()->shop->id.')', $sSql);
			}
            $this->iVariantCount = Db::getInstance()->getValue($sSql);
            if ($this->iVariantCount == 0) {
                $this->iVariantCount = 1;
            }
        }
        return $this->iVariantCount;
    }

    protected function loadShopVariants() {
        if ($this->oProduct !== null) {
            $iVariationCount = $this->getVariantCount();
            if ($iVariationCount > MLSetting::gi()->get('iMaxVariantCount')) {
                $this
                        ->set('data', array('messages' => array(MLI18n::gi()->get('Productlist_ProductMessage_sErrorToManyVariants', array('variantCount' => $iVariationCount, 'maxVariantCount' => MLSetting::gi()->get('iMaxVariantCount'))))))
                        ->save()
                ;
                MLMessage::gi()->addObjectMessage($this, MLI18n::gi()->get('Productlist_ProductMessage_sErrorToManyVariants', array('variantCount' => $iVariationCount, 'maxVariantCount' => MLSetting::gi()->get('iMaxVariantCount'))));
            } else {
                $aAttributes = $this->oProduct->getAttributeCombinations(Context::getContext()->language->id);
                $aVariants = array();
                foreach ($aAttributes as $aAttribute) {
                    $aVariants[$aAttribute['id_product_attribute']]['id_product_attribute'] = $aAttribute['id_product_attribute'];
                    $aVariants[$aAttribute['id_product_attribute']]['info'][] = array('name' => $aAttribute['group_name'], 'value' => $aAttribute['attribute_name']);
                }
                unset($aAttributes);
                if (count($aVariants) <= 0) {
                    $aVariants[] = array("id_product" => $this->oProduct->id);
                }
                foreach ($aVariants as &$aVariant) {
                    $this->addVariant(
                            MLProduct::factory()->loadByShopProduct($this->oProduct, $this->get('id'), $aVariant)
                    );
                }
            }
        }
        return $this;
    }

    public function loadShopProduct() {
        if ($this->oProduct === null) {//not loaded
            $this->oProduct = false; //not null
            if ($this->get('parentid') == 0) {
                $oProduct = $this;
            } else {
                $oProduct = $this->getParent();
            }
            $sKey = MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.keytype')->get('value');
            if ($sKey == 'pID') {
                $oShopProduct = new Product($oProduct->get('productsid'), true);
            } else {
                $oShopProduct = MLHelper::gi('model_product')->getProductByReference($oProduct->get('productssku'),true);
            }
            if (empty($oShopProduct->id) && $this->get('id') != 0) { // $this->get('id')!= 0 because of OldLib/php/modules/amazon/application/applicationviews.php line 556
                $iId = $this->get('id');
                $this->delete();
                MLMessage::gi()->addDebug('Parent :: One of selected product was deleted from shop now it is deleted from magnalister list too, please refresh the page : '.$iId);
                throw new Exception;
            }
            $aData = $this->get('shopdata');
            $this->oProduct = $oShopProduct;
            $this->prepareProductForMarketPlace();
            if ($this->get('parentid') != 0) {//is variant
                $this->loadByShopProduct($oShopProduct, $this->get('parentid'), $aData);
            }
        }

        return $this;
    }

    public function loadByShopProduct($mProduct, $iParentId = 0, $mData = null) {
        $this->oProduct = $mProduct;
        $this->prepareProductForMarketPlace();
        if($mProduct instanceof Combination){
             throw new Exception('bad parameter');
        }
        /* for product who have no reference number ,random refrence is inserted becaue it will made problem when product key is Article number */
        $sKey = MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.keytype')->get('value');
        $this->aKeys = array($sKey == 'pID' ? 'marketplaceidentid' : 'marketplaceidentsku');
        $this->set('parentid', $iParentId)->aKeys[] = 'parentid';
        $sMessage = array();
        if ($iParentId == 0) {
            $this->addSku($this->oProduct);
            $this
                ->set('marketplaceidentid', $this->oProduct->id)
                ->set('marketplaceidentsku', $this->oProduct->reference)
                ->set('productsid', $this->oProduct->id)
                ->set('productssku', $this->oProduct->reference)
                ->set('shopdata', array())
                ->set('data', array('messages' => $sMessage))
                ->save()
                ->aKeys = array('id')
            ;
        } else {
            if (isset($mData['id_product_attribute'])) {
                $oVariation = new Combination($mData['id_product_attribute']);
            } else if (isset($mData['id_product'])) {
                $oVariation = new Product($mData['id_product'], true);
            } else {
                throw new Exception("not id set to create new variation", "13131313");
            }
            if (empty($oVariation->id)) {
                $this->delete();
                MLMessage::gi()->addDebug('Child ::One of selected product was deleted from shop now it is deleted from magnalister list too, please refresh the page');
                throw new Exception;
            }
            $this->addSku($oVariation);            
            $this
                    ->set('marketplaceidentid', $oVariation->id . (isset($mData['id_product'])?'':'_' . $this->oProduct->id))
                    ->set('marketplaceidentsku', $oVariation->reference)
                    ->set("productsid", $oVariation->id)
                    ->set("productssku", $oVariation->reference)
                    ->set('shopdata', $mData)
                    ->set('data', array('messages' => $sMessage))
            ;
            if ($this->exists()) {
                $this->aKeys = array('id');
                $this->save();
            } else {
                $this->save()->aKeys = array('id');
            }
        }
        return $this;
    }

    protected function getRealProduct() {
        $this->load();
        $mData = $this->get('shopdata');
        if (isset($mData['id_product_attribute'])) {
            $oPV = new Combination($mData['id_product_attribute']);
        } else {
            $oPV = new Product($this->oProduct->id, true);
        }
        return $oPV;
    }

    public function __get($sName) {
        $this->load();
		$sValue = 0;
        $oPV = $this->getRealProduct();
        if (isset($oPV->$sName)) {
            $sValue = $oPV->$sName;
            unset($oPV);
        }elseif($oPV instanceof Combination && (strpos($sName, 'manu')!== false)){
            $sValue = $this->getParent()->$sName;
        }
		if(is_array($sValue)){
			if(isset($sValue[Context::getContext()->language->id])){
				return $sValue[Context::getContext()->language->id];
			}else{
				return current($sValue);
			}
		}else{
			return $sValue ;
		}
		
    }

    public function getImages() {
        $this->load();
        $aOut = array();
        $oPV = $this->getRealProduct();
        $aImages = array();
        if ($oPV instanceof Product) {
            foreach($this->getLoaddedProduct()->getImages(Context::getContext()->language->id) as $aImg){
               $aImages[] = $aImg['id_image'];
            }
        } else {
            $aImages = $this->getLoaddedProduct()->_getAttributeImageAssociations($oPV->id);
        }
        foreach ($aImages as $iImg) {
            $oImage = new Image($iImg);
            $aOut[] = _PS_PROD_IMG_DIR_.$oImage->getExistingImgPath().'.'.$oImage->image_format;
        }
        return $aOut;
    }
    
    public function getImageUrl($iX = 40, $iY = 40) {
        $this->load();
        $iProductCoverId= null;
        $oPV = $this->getRealProduct();
        $aImages = array();
        if ($oPV instanceof Combination) {
            $aImages = $this->getLoaddedProduct()->_getAttributeImageAssociations($oPV->id);
            $iProductCoverId = array_shift($aImages);
        }
        if ($iProductCoverId == null) {
            $iProductCoverId = Product::getCover($this->getLoaddedProduct()->id);
        }
        if ($iProductCoverId > 0) {
            if(is_array($iProductCoverId)){
                $iProductCoverId = array_shift($iProductCoverId);
            }
            $oimage = new Image($iProductCoverId);
            try {
                return MLImage::gi()->resizeImage(_PS_IMG_DIR_ . 'p/' . $oimage->getExistingImgPath() . '.jpg', 'products', $iX, $iY, true);
            } catch (Exception $oEx) {
            }
        }
        return '';
    }

    public function getName() {
        $this->load();
        $sPostFix = '';
        if (($iAttributeId = $this->getAttributeId()) !== null) {
            $sPostFix .= Product::getProductName($this->getId(), $iAttributeId);
        }
        $sProductName = $this->getLoaddedProduct()->name[Context::getContext()->language->id];
        return (strpos($sPostFix, $sProductName) !== false) ? $sPostFix : $sProductName . $sPostFix;
    }

    public function getEditLink() {
        if(defined('_PS_VERSION_') && version_compare(_PS_VERSION_, '1.7.0.0', '>=')){
            return Context::getContext()->link->getAdminLink('AdminProducts', true, array('id_product'=>  $this->getLoaddedProduct()->id));
        } else {
            return 'index.php?controller=AdminProducts&updateproduct&id_product=' . $this->getId() .
                    '&token=' . Tools::getAdminToken('AdminProducts' . (int) Tab::getIdFromClassName('AdminProducts') . (int) Context::getContext()->employee->id);
        }
    }
    
    public function getFrontendLink() {
        $oLink = new Link();
        return $oLink->getProductLink($this->getLoaddedProduct());
    }

    public function getShopPrice($blGros = true, $blFormated = false) {
        $this->load();
        Context::getContext()->cookie->specialpriceisactive = false;
        return $this->getPrice($blGros, $blFormated);
    }

    public function getSuggestedMarketplaceStock($sType, $fValue, $iMax = null){
        if(
            MLModul::gi()->getConfig('inventar.productstatus') == 'true'
            && !$this->isActive()
        ) {
            return 0;
        }
        if ($sType == 'lump') {
            $iStock =  (int)$fValue;
        } else {
            $iStock = $this->getStock();
            if ($sType == 'stocksub') {
                $iStock = $iStock - $fValue;
            }

            if (!empty($iMax)) {
                $iStock = min( (int) $iStock,$iMax);
            }
        }

        return $iStock > 0 ? $iStock : 0;
    }

    protected function prepareProductForMarketPlace() {  
        $aConf = null;
        try {
            $aConf = MLModul::gi()->getConfig();
        } catch(Exception $oExc) {
            MLMessage::gi()->addDebug($oExc);
        }      
        if($aConf !== null){
            $context = Context::getContext();
            /* @var  $context  ContextCore */
            if(array_key_exists('orderimport.shop', $aConf)){
                Shop::setContext(Shop::CONTEXT_SHOP,$aConf['orderimport.shop']); 
                $context->shop = new Shop($aConf['orderimport.shop']);
                $this->oProduct = new Product($this->oProduct->id, true, null, $context->shop->id);
            }else{
                Shop::setContext(Shop::CONTEXT_SHOP,$context->shop->id);
            }
            $context->currency = isset($aConf['currency']) ? new Currency(Currency::getIdByIsoCode($aConf['currency'])) : $context->currency;
            $context->language = new Language($aConf['lang']);                    
        }
    }

    public function getSuggestedMarketplacePrice(ML_Shop_Model_Price_Interface $oPrice, $blGros = true, $blFormated = false) {
        $this->load();
        $context = Context::getContext();
        $aConf = $oPrice->getPriceConfig();
        $fTax = $aConf['tax'];
        $sPriceKind = $aConf['kind'];
        $fPriceFactor = (float) $aConf['factor'];
        $iPriceSignal = $aConf['signal'];
        $blSpecialPrice = $aConf['special'];
        /* active or diactive special price */
        $context->cookie->specialpriceisactive = $blSpecialPrice;
        $sCustomerGroup = $aConf['group'];
        $context->customer = new Customer();
        $context->customer->id_default_group = isset($sCustomerGroup) ? $sCustomerGroup : _PS_DEFAULT_CUSTOMER_GROUP_;

        $mReturn = $this->getPrice($blGros, $blFormated /* , $blSpecial */, $sPriceKind, $fPriceFactor, $iPriceSignal, $fTax);
        /* roll back special price to its current shop state */
        return $mReturn;
    }

    protected function getPrice($blGros, $blFormated, $sPriceKind = '', $fPriceFactor = 0.0, $iPriceSignal = null, $fTax = null) {
        $fPercent = (float) ($this->getLoaddedProduct() instanceof Product ? $this->getLoaddedProduct()->getTaxesRate() : $this->getLoaddedProduct()->getTaxesRate());
        $context = Context::getContext();
        $iGroupId = $context->cookie->specialpriceisactive && is_object($context->customer)  ? $context->customer->id_default_group : 0;
       
        $fPrice = Product::priceCalculation(
                $context->shop->id , (int) $this->oProduct->id, $this->getAttributeId(), 
                Configuration::get('PS_COUNTRY_DEFAULT',null ,null ,$context->shop->id), 
                0/*$id_state*/ ,  0/*$zipcode*/ , $context->currency->id /*$id_currency*/ ,
                $iGroupId/*$id_group*/  ,  1/*$quantity*/  , ($fTax === null) /*use_tax*/  , 2 /*$decimals*/  , 
                false/*$only_reduc*/ , $context->cookie->specialpriceisactive/*$use_reduc*/ , true/*$with_ecotax*/ , 
                $specific_price  ,  true  ,null , null ,null,1
            )   ;
        if($fPrice === null){
            $fPrice = 0;
        }
        $oPrice = MLPrice::factory();
        if($fTax !== null) {
            $fPrice = $oPrice->calcPercentages(null, $fPrice, $fTax);
        }
        if ($sPriceKind == 'percent') {
            $fPrice = $oPrice->calcPercentages(null, $fPrice, $fPriceFactor);
        } elseif ($sPriceKind == 'addition') {
            $fPrice = $fPrice + $fPriceFactor;
        }
        if ($iPriceSignal !== null) {
            $fPrice = ((int) $fPrice) + ($iPriceSignal / 100);
        }
        // 3. calc netprice from modulprice
        $fNetPrice = $oPrice->calcPercentages($fPrice, null, $fPercent);
        // 4. define out price (unformated price of current shop)
        $fUsePrice = $blGros ? $fPrice : $fNetPrice;
        if ($blFormated) {
            return "<span class='price'>".Tools::displayPrice($fUsePrice, Context::getContext()->currency->id)."</span>";
        }
        return $fPrice;
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
		if(strpos($sValue,'product_feature_') === 0){
			$sValue = str_replace('product_feature_', '', $sValue);
			return MLHelper::gi('model_product')->getProductFeatureValue($this->getId() ,$sValue, Context::getContext()->language->id);
		}else{
			return $this->__get($sValue);
		}
    }

    public function getDescription() {
        return $this->getLoaddedProduct()->description[Context::getContext()->language->id];
    }

    public function getShortDescription() {
        return $this->getLoaddedProduct()->description_short[Context::getContext()->language->id];
    }

    public function getMetaDescription() {
        return $this->getLoaddedProduct()->meta_description[Context::getContext()->language->id];
    }

    public function getMetaKeywords() {
        return $this->getLoaddedProduct()->meta_keywords[Context::getContext()->language->id];
    }

    /**
     * 
     * @param type $blIncludeRootCats
     * @return array
     */
    public function getCategoryIds($blIncludeRootCats = true) {
        $aCategories = array();
        foreach (Product::getProductCategoriesFull($this->getLoaddedProduct()->id) as $iCategoryId => $aCategoryData ){
            if( $blIncludeRootCats || !in_array($iCategoryId, $this->getRootCategoriesIds())){
                $aCategories[] = $iCategoryId;
            }
        }
        return $aCategories;
    }

    public function getCategoryPath() {
        $oCategory = new Category(Context::getContext()->shop->getCategory());
        $sCategories = '';
        foreach(Product::getProductCategoriesFull($this->getLoaddedProduct()->id) as $iCategoryId => $aCategoryData) {
            if(method_exists('Tools', 'getPath')){
                $sCategories .= $oCategory->name[Context::getContext()->language->id] . ' > ' . Tools::getPath($iCategoryId).'<br>';
            } else {
                $oProductCategory = new Category($iCategoryId);
                foreach ($oProductCategory->getAllParents(Context::getContext()->language->id)->orderBy('id_category') as $category) {
                    if($category->name !== 'Root') {
                        $sCategories .= $category->name . ' > ';
                    }
                }
                $sCategories .= '<br>';
            }
        }
        return $sCategories;
    }

    /**
     * 
     * @param type $blIncludeRootCats
     * @return array
     */
    public function getCategoryStructure($blIncludeRootCats = true) {
        $aCategories = array();
        $aRootCatIds = $aExistedCatId = $blIncludeRootCats ? array() : $this->getRootCategoriesIds();
        foreach(Product::getProductCategoriesFull($this->getLoaddedProduct()->id) as $iCategoryId => $aCategoryData) {
            do {
                if (in_array($iCategoryId , $aExistedCatId)) {
                    break;
                }
                $oCategory = new Category($iCategoryId);
                $aCategory = array(
                    'ID' => $oCategory->id_category,
                    'Name' => $oCategory->name[Context::getContext()->language->id],
                    'Description' => $oCategory->description[Context::getContext()->language->id],
                    'Status' => true,
                );
                $aExistedCatId[] = $oCategory->id_category;
                $iCategoryId = $oCategory->id_parent;
                if ($iCategoryId != 0 && !in_array($iCategoryId, $aRootCatIds)) {
                    $aCategory['ParentID'] = $iCategoryId;
                }
                $aCategories[] = $aCategory;
            } while($iCategoryId != 0);
        }

        return $aCategories;
    }
    
    protected function getRootCategoriesIds () {
        $aTopCategoryIds = array(1);//Root category
        
        /* get top category of each shop in Prestashop */
        foreach (Category::getRootCategories(null, false) as $aCategory){
            $aTopCategoryIds[] = $aCategory['id_category'];
        }
        return $aTopCategoryIds;
    }
    
    public function getStock() {
        $this->load();
        return StockAvailable::getQuantityAvailableByProduct($this->getLoaddedProduct()->id, $this->getAttributeId(), Context::getContext()->shop->id);
    }

    protected function getLoaddedProduct() {
        if ($this->oProduct === null) {
            $this->load();
        }
        return $this->oProduct;
     }

    /**
     * returns tax-value for product. if $aAdressData is set, it try to locate tax for address
     */
    public function getTax( $aAddressData = null ) {
        if ($aAddressData === null) {
            return $this->getLoaddedProduct()->tax_rate ;
        } else {
            $oAddress = Address::initialize();
            $iCountryId = Country::getByIso($aAddressData['CountryCode']);
            $oAddress->id_country = $iCountryId;
            if (array_key_exists('Suburb', $aAddressData) && !empty($aAddressData['Suburb'])) {
                $oAddress->id_state = State::getIdByIso($aAddressData['Suburb'], $iCountryId);
            }
            $oAddress->postcode = $aAddressData['Postcode'];
            return $this->getLoaddedProduct()->getTaxesRate($oAddress);
        }
    }

    public function getVariatonData() {        
        return $this->getVariatonDataOptinalField(array('name','value'));
    }
    
    public function getVariatonDataOptinalField($aFields = array()) {
        $aOut = array();
        $oCombination = $this->getRealProduct();
        /* @var  $oCombination CombinationCore */
        $aAtributes = $this->getLoaddedProduct()->getAttributeCombinationsById($oCombination->id, Context::getContext()->language->id);
        foreach($aAtributes  as $aAtribute) {
            $aData = array();
            if (in_array('code',$aFields)) {//an identifier for group of attributes , that used in Meinpaket at the moment
                $aData['code']=  $aAtribute['id_attribute_group'];                                
            }
            if (in_array('valueid',$aFields)) {//an identifier for group of attributes , that used in Meinpaket at the moment
                $aData['valueid']=  $aAtribute['id_attribute'];                                
            } 
            if (in_array('name',$aFields)) {
                $aData['name']=  $aAtribute['group_name'];                                
            }
            if (in_array('value',$aFields)) {
                $aData['value']=  $aAtribute['attribute_name'];                                
            }
            $aOut[] = $aData;
        }
        return $aOut;
    }

    public function isActive() {
        $this->load();
        return $this->getLoaddedProduct()->active == 1;
    }

    public function createModelProductByMarketplaceSku($sSku) {
        $oMyTable = MLProduct::factory();
        $oShopProduct = null;
        if (MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.keytype')->get('value') == 'pID') {
            if (strpos($sSku, '_') !== false) {
                $aIds = explode("_", $sSku);
                if (is_int($aIds[0]) && is_int($aIds[1])) {
                    $oProduct = new Product($aIds[1],true);
                    $oCombination = new Combination($aIds[0]);
                    if (!empty($oProduct->id) && !empty($oCombination->id)) {
                        $oShopProduct=$oProduct;
                    }
                }
            } else {
                if (is_int($sSku)) {
                    $oProduct = new Product($sSku,true);
                    if (!empty($oProduct->id)) {
                        $oShopProduct = $oProduct;
                    }
                }
            }
        } else {
            $oProduct = MLHelper::gi('model_product')->getProductByReference($sSku);
            if ($oProduct !== null) {
                if($oProduct instanceof Combination){
                    $oShopProduct = new Product($oProduct->id_product,true);
                }else{
                    $oShopProduct = $oProduct;
                }
            }
        }
        if($oShopProduct !== null){
            $oMyTable->loadByShopProduct($oShopProduct);
            $oMyTable->getVariants();
        }
        return $this;
    }

    public function setStock($iStock) {
        $this->load();
        StockAvailable::setQuantity($this->getId(), $this->getAttributeId(0), (int)$iStock);
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                if (StockAvailable::dependsOnStock($this->getId())) {// if the available quantities depends on the physical stock
                    StockAvailable::synchronize($this->getId(),  Context::getContext()->shop->id );// synchronizes
            }
        }
        return $this;
    }

    protected function addSku($oProduct) {
        if (empty($oProduct->reference)) {
            $iLoopLimit = 1000;
            do {
                $sNewSku = $oProduct->id . '_' . rand(0, getrandmax());
                $oProduct->reference = $sNewSku;
                $aProduct = MLHelper::gi('model_product')->getProductByReference($sNewSku);
                $iLoopLimit--;
            } while ($aProduct != null && $iLoopLimit > 0);
            if (isset($oProduct) && isset($oProduct->id)) {
                $oProduct->update();
            }
        }
        return $this;
    }

    public function getAttributeId($mReturn = null) {
        $this->load();
        $mData = $this->get('shopdata');
        if (isset($mData['id_product_attribute'])) {
            return $mData['id_product_attribute'];
        } else {
            return $mReturn;
        }
    }

    public function getId() {
        return $this->getLoaddedProduct()->id;
    }

    public function getBasePriceString($fPrice, $blLong = true) {
        $aBasePrice = $this->getBasePrice();
        if(!empty($aBasePrice)){
            $sBasePrice = Tools::displayPrice(($this->getLoaddedProduct()->unit_price), Context::getContext()->currency->id);
            $sBaseWeight = $this->getLoaddedProduct()->unity;
            return "{$sBasePrice} / {$sBaseWeight} ";
        }else{
            return '';
        }
    }

    public function getBasePrice() {
        $sUnit = (string) $this->getLoaddedProduct()->unity;
        $sUnit = trim($sUnit);
        if (empty($sUnit) || $this->getLoaddedProduct()->unit_price_ratio <= 0) {
            return array();
        } else {
            return array(
                "Unit" => $sUnit,
                "Value" => $this->getLoaddedProduct()->unit_price_ratio,
            );
        }
    }

    public function getWeight(){
        $fWeight = (float)($this->getLoaddedProduct()->weight);
        $sUnit = Configuration::get('PS_WEIGHT_UNIT');
        if($fWeight > 0){
            return array(
                "Unit" => $sUnit,
                "Value"=>  $fWeight,
            );
        }else{
            return array();
        }
    }

    public function setLang( $iLang ){
         if (Context::getContext()->language->id != $iLang ) {
             Context::getContext()->language = new Language($iLang);
         }
        return $this;
    }

    public function getTaxClassId() {
        $oProduct = $this->getLoaddedProduct();
        return $oProduct->id_tax_rules_group;
    }

    public function getAttributeValue($mAttributeCode) {
        $oPV = $this->getRealProduct();
        $attributes = $oPV->getAttributesName(_LANG_ID_);

        $aAttributeCode = explode('_', $mAttributeCode);

        if ($aAttributeCode[0] === 'a') {
            foreach ($attributes as $attribute) {
                $result = MLDatabase::factorySelectClass()
                    ->select('id_attribute_group')
                    ->from(_DB_PREFIX_ . 'attribute')
                    ->where("id_attribute = {$attribute['id_attribute']}")
                    ->getResult();

                if ($result[0]['id_attribute_group'] === $aAttributeCode[1]) {
                    return $attribute['name'];
                }
            }
        } else if ($aAttributeCode[0] === 'f') {
            $ifeatureValue = MLDatabase::factorySelectClass()
                ->select('l.value')
                ->from(_DB_PREFIX_ . 'feature_product', 'p')
                ->join(array(_DB_PREFIX_ . 'feature_value_lang', 'l', 'p.id_feature_value = l.id_feature_value', ML_Database_Model_Query_Select::JOIN_TYPE_LEFT))
                ->where("l.id_lang = " . _LANG_ID_ . " and p.id_feature = $aAttributeCode[1] and p.id_product = {$this->getLoaddedProduct()->id}")
                ->getResult();

            return $ifeatureValue[0]['value'];
        }

        return null;
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
        
    protected function deleteVariants($aIds = array()) {
        if(Shop::isFeatureActive() && 
                is_object($this->oProduct) // it can be product was deleted before, now we couldn't load variants by not existed product, so we delete normaly all variation
                ){
            $aIds = array();              
            $aBackupVariants = $this->aVariants;
            $this->aVariants = array();
            $iBackupShopId = Shop::getContextShopID();

            Shop::setContext(Shop::CONTEXT_ALL);
            $this->loadShopVariants();            
            foreach ($this->aVariants as $oVariant) {
                $aIds[] = $oVariant->get('id');
            }
        }
        parent::deleteVariants($aIds);           
            
        if(Shop::isFeatureActive() && 
                is_object($this->oProduct) // it can be product was deleted before, now we couldn't load variants by not existed product, so we delete normaly all variation
                ){
            $this->aVariants = $aBackupVariants ;
            Shop::setContext(Shop::CONTEXT_SHOP,$iBackupShopId);
        }
    }
    
    public function getShippingCostByZone($iZoneId, $fPrice, $iCarrierId) {
        $aAllProductCarrier = $this->getLoaddedProduct()->getCarriers();
        if(empty($aAllProductCarrier)){
            $oCarrier = new Carrier($iCarrierId);
        } else {            
            $aCarrier = current($this->getLoaddedProduct()->getCarriers());
            $oCarrier = new Carrier($aCarrier['id_carrier']);
        }
        
        if(empty($oCarrier->id)) {
            return false;
        }
        
        $fShippingCostByWeight = Carrier::checkDeliveryPriceByWeight($oCarrier->id, $this->getLoaddedProduct()->weight, (int) $iZoneId);
        $fShippingCostByPrice = Carrier::checkDeliveryPriceByPrice($oCarrier->id, $fPrice, (int) $iZoneId, (int) Context::getContext()->currency->id);
        
        // Get only carriers that have a range compatible with cart
        if (($oCarrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT && !$fShippingCostByWeight) || ($oCarrier->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE && !$fShippingCostByPrice)) {
            return false;
        }

        if ($oCarrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT) {
            $shipping = $oCarrier->getDeliveryPriceByWeight($this->getLoaddedProduct()->weight, (int) $iZoneId);
        } else {
            $shipping = $oCarrier->getDeliveryPriceByPrice($fPrice, (int) $iZoneId, (int) Context::getContext()->currency->id);
        }
        MLMessage::gi()->addInfo(var_dump_pre($shipping,true));
        return $shipping;
    }

}

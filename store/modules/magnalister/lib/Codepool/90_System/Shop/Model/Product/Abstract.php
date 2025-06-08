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

MLFilesystem::gi()->loadClass('Database_Model_Table_Product');

/**
 * An abstract model class that represents a product.
 * Each shop driver has to implement the abstract methods.
 */
abstract class ML_Shop_Model_Product_Abstract extends ML_Database_Model_Table_Product {
    
    protected $isSkuChanged = false;
    protected static $aParent = array();
    
    /**
     * @var bool $blMessage
     *    Indicates if messages have to be shown in case the product could not be loaded.
     */
    protected $blMessage = true;
    
    /**
     * @var array $aVariants
     *     A vector of ML_Shop_Model_Product_Abstract that represent this products variations.
     */
    protected $aVariants = null;
    
    /**
     * Loads a product based on its SKU.
     *
     * @param string $sSku
     * @param boolean $blMaster
     *    Indicates whether the product is a master item.
     *
     * @return ML_Shop_Model_Product_Abstract
     */
    public function getByMarketplaceSKU($sSku, $blMaster = false) {
        if (empty($sSku)) {
            return $this->set('id', 0);
        } else {
            $sIdent = (MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.keytype')->get('value') == 'pID')
                ? 'marketplaceidentid'
                : 'marketplaceidentsku';
            $sMasterCondition = ($blMaster ? '=' : '<>');
            $sSql = sprintf("select `id` from " . $this->sTableName . " where `%s`='%s' and `parentid`%s0", $sIdent, MLDatabase::getDbInstance()->escape($sSku), $sMasterCondition);
            $aID = MLDatabase::getDbInstance()->fetchArray($sSql);
            if (isset($aID[0]['id'])) {
                $iID = $aID[0]['id'];
            } else {
                try {
                    $this->createModelProductByMarketplaceSku($sSku);
                    $iID = MLDatabase::getDbInstance()->fetchOne($sSql);
                }
                catch (Exception $oExc) {
                    MLMessage::gi()->addDebug($oExc->getMessage());
                }
            }
            $this->init(true);//we should run init , whether product is found or not , may be the object is filled by older search
            if ($iID) {
                $this->set('id', $iID);
                if ($sSku != $this->get($sIdent)) {
                    $this->init(true)->set('productssku', $sSku);
                }
            } else {
                $this->set('productssku', $sSku);
            }
            
            if($this->exists() && $this->isSkuChanged){
                /*    It is possible value of Sku in magnalister_product was old and can be updated after loading this product , 
                      if this variable is true , we try to load product and if sku is changed we try to search sku again
                */
                $this->init(true);
                return $this->getByMarketplaceSKU($sSku, $blMaster);
            }
            return $this;
        }
    }
    
    /**
     * checks data if message-key exists
     * @return array of string
     */
    protected function getMessages() {
        return array();
        $aShopData = $this->get('data');
        return isset($aShopData['messages']) ? $aShopData['messages'] : array();
    }
    
    /**
     * Adds a variation item to this item.
     *
     * @param ML_Shop_Model_Product_Abstract $oVariant
     *
     * @return void
     */
    protected function addVariant($oVariant) {
        $this->aVariants[] = $oVariant;
    }
    
    /**
     * Gets all loaded variations for this product
     * @return array
     *    A vector of ML_Shop_Model_Product_Abstract
     */
    public function getVariants() {
        $this->load();
        if ($this->get('id') != 0) {
            $aIds = array();
            if ($this->aVariants === null) {
                $this->aVariants = array();
                $this->loadShopVariants();
            }
            foreach ($this->aVariants as $oVariant) {
                $aIds[] = $oVariant->get('id');
            }
            if (count($aIds) > 0) {
                $this->deleteVariants($aIds);
            }
        }
        return $this->aVariants;
    }
    
    /**
     * Deletes all variations of this item from the magnalister_products table.
     * @param array $aIds ids of current variation that will be excluded from delete
     */
    protected function deleteVariants($aIds = array()) {
        $sSql = "select id from magnalister_products where parentid='" . $this->get('id') . "' " . (count($aIds) > 0 ? "and id not in ('" . implode("' ,'", $aIds) . "')" : '');
        foreach (MLDatabase::getDbInstance()->fetchArray($sSql) as $aRow) { //delete not existing ids (copy to backup)
            MLProduct::factory()->set('id', $aRow['id'])->delete();
        }
    }
    
    /**
     * Get the products SKU.
     *
     * @return string
     */
    public function getSku() {
        return $this->get("productssku");
    }
    
    /**
     * Get the marketplace sku based on the global setting of the key type identifier "general.keytype".
     *
     * @return string
     */
    public function getMarketPlaceSku() {
        if (MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.keytype')->get('value') == 'pID') {
            return $this->get('marketplaceidentid');
        } else {
            return $this->get('marketplaceidentsku');
        }
    }
    
    /**
     * Loads shop-specific product(-data).
     * Uses self::getMarketPlaceSku() to identify the product.
     * 
     * Should check, if shop-specific product(-data) already is loaded
     *
     * @throws Exception product don't exists in shop
     * @return $this
     */
    abstract protected function loadShopProduct();
    
    /**
     * Loads all shop-variants of this product.
     */
    abstract protected function loadShopVariants();
    
    /**
     * Checks if the product exists in magnalister_products and shop system product table.
     *
     * @return bool
     */
    public function exists() {
        $this->blMessage = false;
        $oReturn = parent::exists();
        $this->blMessage = true;
        return $oReturn;
    }
    
    /**
     * Checks if the product exists just in magnalister_products .
     * when we don't need to check existing in shop system product table , this function can be faster than  normal exists() function
     *
     * @return bool
     */
    public function existsMlProduct() {
        $this->blMessage = false;
        try{
            $blReturn = parent::load()->blLoaded;
        }  catch (Exception $oExc){            
            $blReturn = $this->blLoaded = false;
        }
        $this->blMessage = true;
        return $blReturn;
    }
    
    /**
     * This method handles loading the product.
     * @return self
     */
    public function load() {
        try {
            parent::load();
            try {
                $this->loadShopProduct();
            }
            catch (Exception $oEx) {
                $this->blLoaded = false;
                MLMessage::gi()->addWarn($oEx);
            }
        }
        catch (Exception $oEx) {
            $this->blLoaded = false;
            if ($this->blMessage) {
                MLMessage::gi()->addDebug($oEx);
            }
        }
        //add to messagestack
        foreach ($this->getMessages() as $sMessage) {
            MLMessage::gi()->addObjectMessage($this, $sMessage);
        }
        return $this;
    }
    
    /**
     * Returns the parent of this product if it is a variant.
     *
     + @return ML_Shop_Model_Product_Abstract
     */
    public function getParent() {
        if (!array_key_exists($this->get('parentid'), self::$aParent)) {
            self::$aParent[$this->get('parentid')] = MLProduct::factory()->set('id', $this->get('parentid'));
        }
        return self::$aParent[$this->get('parentid')];
    }
    
    /**
     * Deletes all variation and the master from the magnalister products table
     * return mixed
     */
    public function delete() {
        $aIdsDelete = array();
        if (($this->get('parentid') == 0) && ($this->get('id') != 0)) {
            foreach (MLDatabase::getDbInstance()->fetchArray("select id from " . $this->getTableName() . " where parentid = " . $this->get('id')) as $aId) {
                $aIdsDelete[] = $aId['id'];
            }
            if (count($aIdsDelete) > 0) {
                $this->deleteVariants();
                
                //delete variation from selection
                $oDeletedSelection = MLDatabase::factory('selection')->getList();
                $oDeletedSelection->getQueryObject()->where('pid in (' . implode(',', $aIdsDelete) . ')');
                $oDeletedSelection->delete();
            }
        }
        return parent::delete();
    }
    
    /**
     * Gets data elements of the magnalister products table.
     *
     * @param string $sName
     * @return ?string
     */
    public function get($sName) {
        $sName = strtolower($sName);
        if (!isset($this->aData[$sName])) {
            parent::load();
        }
        return array_key_exists($sName, $this->aData) ? MLHelper::getEncoderInstance()->decode($this->aData[$sName]) : null;
    }
    
    /**
     * Return array of placeholders that should be replaced in Marketplace description of the item.
     * This method can be overwritten to add more placeholders to the list.
     * @return array
     */
    public function getReplaceProperty() {
        return array(
            '#TITLE#' => $this->getName(),
            '#ARTNR#' => $this->getMarketPlaceSku(),
            '#PID#' => $this->get('marketplaceidentid'),
            '#SHORTDESCRIPTION#' => $this->getShortDescription(),
            '#DESCRIPTION#' => $this->getDescription()
        );
    }
    
    protected function isChanged() {
        $sKey = MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.keytype')->get('value');
        $sSkuFieldName = $sKey == 'pID' ? 'marketplaceidentid' : 'marketplaceidentsku';
        if($this->aData[$sSkuFieldName] != $this->aOrginData[$sSkuFieldName]){
            $this->isSkuChanged = true;
        }
        return parent::isChanged();
    }
    
    /**
     * merge images of master article and images of variation of this product, and return all of them together
     * @return array
     */
    public function getAllImages() {
        return $this->getImages();
    }


    /**
     * Gets the price of current shop without special offers.
     * @param bool $blGros
     * @param bool $blFormated
     * @return mixed
     *     A string if $blFormated == true
     *     A float if $blFormated == false
     */
    abstract public function getShopPrice($blGros = true, $blFormated = false);
    
    /**
     * Gets the price depending on the marketplace config.
     * @param bool $blBrut 
     * @param bool $blFormated
     * @return mixed
     *     A string if $blFormated == true
     *     A float if $blFormated == false
     */
    abstract public function getSuggestedMarketplacePrice(ML_Shop_Model_Price_Interface $oPrice, $blGros = true, $blFormated = false);
    
    /**
     * Get an element of this item. Used for matchings defined in modulconfig.
     * @return ?mixed
     *    value of product-attribute or null if the value does not exist.
     */
    abstract public function getModulField($sFieldName, $blGeneral = false);
    
    /**
     * Gets the title of the product.
     * @return string
     */
    abstract public function getName();
    
    /**
     * Returns the url of the main item image in the requested resolution.
     * If the url does not yet exist an image will be generated.
     *
     * @param int $iX
     * @param int $iY
     *
     * @return string
     */
    abstract public function getImageUrl($iX = 40, $iY = 40);
    
    /**
     * Returns the link to edit the product in the shop.
     *
     * @return string
     */
    abstract public function getEditLink();
    
    /**
     * Returns the link to frontend of the product in the shop.
     *
     * @return string
     */
    abstract public function getFrontendLink();
    
    /**
     * load data with shop-product-info (main-product)
     * if not exist, create entree in db
     * also creates variants of main-product 
     * @use self::addVariant()
     *
     * @param mixed $mProduct depends by shop
     * @param integer $iParentId $this->get("parentid");
     * @param mixed $mData Shopspecific
     *
     * @return mixed
     */
    abstract public function loadByShopProduct($mProduct, $iParentId = 0, $mData = null);
    
    /**
     * Gets all images for the current item.
     * @return array
     *    /file/path/to/image/
     */
    abstract public function getImages();
    
    /**
     * Gets the description of the item.
     * @return string
     */
    abstract public function getDescription();
    
    /**
     * Gets the short description of the item.
     * @return string
     */
    abstract public function getShortDescription();
    
    /**
     * Gets the meta description of the item.
     * @return string
     */
    abstract public function getMetaDescription();
    
    /**
     * Gets the meta keywords of the item.
     * @return string
     */
    abstract public function getMetaKeywords();
    
    /**
     * Gets the quantity of the item.
     * @return int
     */
    abstract public function getStock();
    
    /**
     * Changes the quantity of the item.
     * @param int $iStock new stock
     * @return ML_Shop_Model_Product_Abstract
     */
    abstract public function setStock($iStock);
    
    /**
     * Get the quantity of the product based on the module configuation.
     * Also cares about options-child-articles.
     *
     * @param string $sType
     * @param float $fValue
     *
     * @return int
     */
    abstract public function getSuggestedMarketplaceStock($sType, $fValue, $iMax = null);
    
    /**
     * get distinct data of variant
     * eg:
     * array(
     *  array(
     *      'name'=>'color'
     *      'value'=>'red'
     *  ),...
     * );
     * if empty, variant == master
     * @return array
     */
    abstract public function getVariatonData();
    
    /**
     * Gets the tax percentage of the item.
     * @param null $aAddressData get tax for home country
     * @param array $aAddressData get tax for $aAddressData
     * @return float
     */
    abstract public function getTax( $aAddressData = null );

    /**
     * Gets tax class id for current product.
     * 
     * @return int Tax Class Id.
     */
    abstract public function getTaxClassId();
   
    /**
     * Gets the item status
     * @return bool
     *    true: product is active
     *    false: product is inactive and can't be bought.
     */
    abstract public function isActive();
    
    /**
     * try to find in shop given sku and create table entrees of masters/variants
     * after the searched product maybe exists in table - if not, just product-table have more entries
     * @param string $sSku depend by general.keytype
     * @uses self::loadByShopProduct() as new instance
     * @return \ML_Magento_Model_Product
     */
    abstract public function createModelProductByMarketplaceSku($sSku);
    
    /**
     * Get the category oath of the current item.
     * @return mixed
     */
    abstract public function getCategoryPath();
    
    /**
     * Gets the base price of the current item.
     * @return array
     *     array('Unit'=>(string),'Value'=>(float))
     */
    abstract public function getBasePrice();
    
    /**
     * gets formatted baseprice string
     * @param float $fPrice price to format
     * @param bool $blLong use lang or short unit-names eg. kg <=> kilogram
     * @return string 
     */
    abstract public function getBasePriceString($fPrice, $blLong = true);
     
    /**
     * Returns the number of variant items if this item is a master item.
     * @return int
     */
    abstract public function getVariantCount();

         
    /**
     * Returns the EAN
     * @return string
     */
    abstract public function getEAN();
            
    /**
     * Returns the manufacturer
     * @return string
     */
    abstract public function getManufacturer();

    /**
     * Returns the manufacturer part number
     * @return string
     */
    abstract function getManufacturerPartNumber();
    
    /**
     * @param $blIncludeRootCats
     * @return array
     */
    abstract public function getCategoryStructure($blIncludeRootCats = true);
    
    /**
     * @return array
     */
    abstract public function getCategoryIds($blIncludeRootCats = true);
    
    /**
     * @return array empty array or array( "Unit"=><Unit of weight>, "Value"=> <amount of weight>)
     */
    abstract public function getWeight();

}

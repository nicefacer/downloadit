<?php

class ML_ZzzzDummy_Model_Product extends ML_Shop_Model_Product_Abstract {
    
    protected $oShopProduct = null;

    public function getVariantCount() {
        return count($this->loadShopVariants()->aVariants);
    }
    
    public function loadShopVariants() {
        if (empty($this->aVariants)) {
            $oShopProductList = MLDatabase::factory('ZzzzDummyShopProduct')->set('parentid', $this->oShopProduct->get('id'))->getList()->getList();
            foreach ($oShopProductList as $oShopProduct) {
                $oMlProduct = MLProduct::factory();
                /* @var $oProduct ML_Shop_Model_Product_Abstract */
                $oMlProduct->loadByShopProduct($oShopProduct, $this->oShopProduct->get('id'));
                $this->aVariants[$oMlProduct->get('id')] = $oMlProduct;
            }
        }
        return $this;
    }

    public function loadShopProduct() {
        if ($this->oShopProduct === null) {
            $sField = MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.keytype')->get('value') == 'pID' ? 'id' : 'sku';
            $this->oShopProduct = MLDatabase::factory('ZzzzDummyShopProduct');
            $aKeys = $this->oShopProduct->getKeys();
            $this->oShopProduct->setKeys(array($sField))->set($sField, $this->get('products'.$sField))->load()->setKeys($aKeys);
        }
        return $this;
    }

    public function loadByShopProduct($mProduct, $iParentId = 0, $mData = null) {
        $iParentId = empty($iParentId) ? 0 : MLProduct::factory()->setKeys(array('productsid'))->set('productsid', $iParentId)->get('id');
        $aKeys = $this->getKeys();
        $sField = MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.keytype')->get('value') == 'pID' ? 'id' : 'sku';
        $this->setKeys(array('products'.$sField));
        $this->set('products'.$sField, $mProduct->get($sField))->set('parentid', $iParentId);
        if (!$this->exists()) {
            foreach (array(
                'productsid' => 'id',
                'productssku' => 'sku',
                'marketplaceidentid' => 'id',
                'marketplaceIdentsku' => 'sku'
            ) as $sMlField => $sShopField) {
                $this->set($sMlField , $mProduct->get($sShopField));
            }
            $this->save()->setKeys($aKeys);
        }
        return $this;
    }
    
    public function getName() {
        return $this->oShopProduct->get('name');
    }    
    
    public function getEditLink() {
        $aKeys = array('id', 'parentid');
        $aData = $this->oShopProduct->data();
        $aSet = array();
        $aWhere = array();
        foreach ($aData as $sKey => $mValue) {
            if (in_array($sKey, $aKeys)) {
                $aWhere[] = "    `".$sKey."` ".(
                    $aData[$sKey] === null 
                    ? 'is null' 
                    : " = '".MLHelper::getEncoderInstance()->encode($aData[$sKey])."'"
                );
            } else {
                $aSet[] = "    `".$sKey."` = `".$sKey."`";
            }
        }
        return MLHttp::gi()->getUrl(array(
            'controller' => 'main_tools_sql', 
            'SQL' => urlencode("/*\nUPDATE\n    `".$this->oShopProduct->getTableName()."`\nSET\n".implode(",\n", $aSet)."\nWHERE\n".implode(" AND\n",$aWhere)."\n;\n*/")
        ));
    }
    
    public function getFrontendLink() {$aKeys = array('id', 'parentid');
        $aData = $this->oShopProduct->data();
        $aWhere = array();
        foreach ($aData as $sKey => $mValue) {
            if (in_array($sKey, $aKeys)) {
                $aWhere[] = "    `".$sKey."` ".(
                    $aData[$sKey] === null 
                    ? 'is null' 
                    : " = '".MLHelper::getEncoderInstance()->encode($aData[$sKey])."'"
                );
            }
        }
        return MLHttp::gi()->getUrl(array(
            'controller' => 'main_tools_sql', 
            'SQL' => urlencode("SELECT\n    *\nFROM\n    `".$this->oShopProduct->getTableName()."`\nWHERE\n".implode(" AND\n",$aWhere)."\n;")
        ));
    }
    
    public function getStock() {
        return $this->oShopProduct->get('stock');
    }

    public function setStock($iStock) {
        $this->oShopProduct->set('stock', $iStock);
        return $this;
    }
    
    public function getTax( $aAddressData = null ) {
        return $this->oShopProduct->get('tax');
    }
    
    public function getTaxClassId() {
        return $this->getTax();
    }

    public function getDescription() {
        return $this->oShopProduct->get('description');
    }

    public function getShortDescription() {
        return $this->oShopProduct->get('shortdescription');
    }

    public function getMetaDescription() {
        return $this->oShopProduct->get('metadescription');
    }

    public function getMetaKeywords() {
        return $this->oShopProduct->get('metakeywords');
    } 
    
    public function isActive() {
        return $this->oShopProduct->get('active');
    }
    
    public function getWeight() {
        return $this->oShopProduct->get('weight');
    }

    public function getManufacturer() {
        return $this->oShopProduct->get('manufacturer');
    }

    public function getManufacturerPartNumber() {
        return $this->oShopProduct->get('manufacturerpartnumber');
    }

    public function getImages() {
        return $this->oShopProduct->get('images');
    }
    
    public function getImageUrl($iX = 40, $iY = 40) {
        $aImages = $this->getImages();
        $sImage = array_shift($aImages);
        return (empty($sImage) ? '' : MLImage::gi()->resizeImage($sImage, 'product', $iX, $iY, true));
    }   
    
    
    /**
     * @todo: use parameters
     */
    public function getShopPrice($blGros = true, $blFormated = false) {
        return $this->oShopProduct->get('price');
    }     
    
    /**
     * @todo
     */
    public function getSuggestedMarketplaceStock($sType, $fValue, $iMax = null) {
        $iStock = 10;
        return $iStock > 0 ? $iStock : 0;
    }

    /**
     * @todo
     */
    public function getSuggestedMarketplacePrice(ML_Shop_Model_Price_Interface $oPrice, $blGros = true, $blFormated = false) {
        $mReturn = 10.99;
        return $mReturn;
    }
    
    /**
     * @todo
     */
    public function getModulField($sFieldName, $blGeneral = false) {
        return null;
    }
    
    /**
     * @todo
     */
    public function getCategoryPath() {
        return __METHOD__;
    }
    
    /**
     * @todo
     */
    public function getCategoryIds($blIncludeRootCats = true) {
        return array(3, 5, 7);
    }
   
    /**
     * @todo
     */
    public function getCategoryStructure($blIncludeRootCats = true) {
        $aCategories = array (
            array(
                'ID' => '3',
                'Name' => 'Category 3',
                'Description' => 'Category 3 Description',
                'Status' => true,
            )
        );
        return $aCategories;
    }

    /**
     * @todo
     */
    public function getVariatonData() {
        return $this->getVariatonDataOptinalField(array('name','value'));
    }
    
    /**
     * @todo
     */
    public function getVariatonDataOptinalField($aFields = array()) {
        $aOut = array(
            array (
                'name' => 'foo',
                'bar' => 'asdasdasd',
            )
        );
        return $aOut;
    }

    /**
     * @todo
     */
    public function createModelProductByMarketplaceSku($sSku) {
//        $oShopTable->zzzzDummyProduct($sSku);
        return $this;
    }

    /**
     * @todo
     */
    public function getBasePriceString($fPrice, $blLong = true) {
        return '';
    }

    /**
     * @todo
     */    
    public function getBasePrice() {
        /**
         * @todo add magento base price
         */
        return array();
    }

    public function setLang($iLang) {
        return $this;
    }

    public function getEAN() {        
        return $this->oShopProduct->get('ean');
    }

}

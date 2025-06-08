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
class ML_Magento_Helper_Model_ProductList_List{
    
    protected $oI18n=null;
    
    /**
     *
     * @var Mage_Catalog_Model_Resource_Product_Collection $oCollection
     */
    protected $oCollection=null;
    
    /**
     *
     * @var Varien_Db_Select $oSelect
     */
    protected $oSelect=null;
    
    protected $aFields=array();
    
    protected $aHeader=array();
    
    protected $aList=array();
    
    protected $oLoadedProduct=null;
    protected $oProduct=null;
    
    protected $aAttributes=null;
    
    protected $aMixedData=array();
    
    public function __construct() {
        $this->oI18n = MLI18n::gi();
        $aAttributes = array();
        $oAttributeCollection = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load(false)
        ;
        foreach ($oAttributeCollection as $oAttribute) {
            $aAttributes[$oAttribute->attribute_code] = $oAttribute;
        }
        $this->aAttributes = $aAttributes;
    }
    
    public function clear() {
        $oRef = new ReflectionClass($this);
        foreach ($oRef->getDefaultProperties() as $sKey => $mValue) {
            $this->$sKey = $mValue;
        }
        $this->__construct();
        return $this;
    }
    
    public function setCollection($oCollection) {
        $this->oCollection = $oCollection;
        $this->oSelect = $oCollection->getSelectSql();
        return $this;
    }
    
    public function getList() {
        $this->oCollection->load();
        foreach ($this->oCollection as $oProduct) {
            $this->oProduct = $oProduct;
            /* @var $oMLProduct ML_Shop_Model_Product */
            $oMLProduct = MLProduct::factory();
            $oMLProduct->loadByShopProduct($oProduct);
            $this->oLoadedProduct = $oMLProduct;
            foreach ($this->aFields as $sField) {
                $mReturn =
                    method_exists($this, $sField)
                    ? $this->{$sField}()
                    : $this->magentoAttribute($sField)
                ;
                if ($mReturn !== null) {
                    $this->aMixedData[$oMLProduct->get("id")][$sField] = $mReturn;
                }
            }
            $this->aList[$oProduct->getId()] = $oMLProduct;
        }
        return $this->aList;
    }
    
    public function getMixedData($oProduct, $sKey) {
        $this->oLoadedProduct = $oProduct;
        return $this->magentoAttribute($sKey);
    }

    public function getHeader() {
        return $this->aHeader;
    }
    
    public function priceShop($sTypeVariant = null) {
        if ($this->oLoadedProduct === null) {
            if (!in_array(__function__, $this->aFields)) {
                $this->aFields[] = __function__;
                $this->aHeader[__function__] = array(
                    'title' => $this->oI18n->get('Productlist_Header_sPriceShop'),
                    'order' => 'price',
                    'type' => 'priceShop',
                    'type_variant' => ($sTypeVariant ===null) ? 'priceShop' : $sTypeVariant,
                );
            }
            return $this;
        }
    }
    
    public function priceMarketplace($sTypeVariant = null) {
        if ($this->oLoadedProduct === null) {
            if (!in_array(__function__, $this->aFields)) {
                $this->aFields[] = __function__;
                $this->aHeader[__function__] = array(
                    'title' => sprintf($this->oI18n->get('Productlist_Header_sPriceMarketplace'), MLModul::gi()->getMarketPlaceName(false)),
                    'type' => 'priceMarketplace',
                    'type_variant' => ($sTypeVariant ===null) ?'priceMarketplace' : $sTypeVariant,
                );
            }
            return $this;
        }
    }
    
    public function image($sTypeVariant = null) {
        if ($this->oLoadedProduct === null) {
            if (!in_array(__function__, $this->aFields)) {
                $this->aFields[] = __function__;
                $this->aHeader[__function__] = array(
                    'title' => $this->oI18n->get('Productlist_Header_sImage'),
                    'type' => 'image',
                    'type_variant' => $sTypeVariant === null ? 'image' : $sTypeVariant,
                );
            }
            return $this;
        }
    }
    
    public function magentoAttribute($sCode, $blUse = true, $sTitle = null, $sTypeVariant = null) {
        if ($this->oLoadedProduct === null) {
            if (!in_array($sCode, $this->aFields)) {
                if ($blUse && array_key_exists($sCode, $this->aAttributes)) {
                    $this->aFields[] = '_'.$sCode;
                    $this->aHeader['_'.$sCode] = array(
                        'title' => ($sTitle === null) ? $this->aAttributes[$sCode]->frontendLabel : $sTitle,
                        'order' => $sCode,
                        'type' => 'simpleText',
                        'type_variant' => ($sTypeVariant === null) ? 'simpleText' : $sTypeVariant,
                    );
                }
            }
            return $this;
        }
        else{
            $sCode = substr($sCode, 1);
            return $this->oLoadedProduct->$sCode;
        }
    }
    
    public function product($sTypeVariant = null) {
        if ($this->oLoadedProduct === null) {
            if (!in_array(__function__, $this->aFields)) {
                $this->aFields[] = __function__;
                $this->aHeader[__function__] = array(
                    'title' => $this->oI18n->get('Productlist_Header_sProduct'),
                    'order' => 'name',
                    'type' => 'product',
                    'type_variant' => ($sTypeVariant === null) ? 'product' : $sTypeVariant,
                );
            }
            return $this;
        }
    }

    public function preparedStatus($sTypeVariant = null) {
        if ($this->oLoadedProduct === null) {
            $this->aFields[] = __function__;
            $this->aHeader[__function__] = array(
                'title' => $this->oI18n->get('Productlist_Header_sPreparedStatus'),
                'type' => 'preparedStatus',
                'type_variant' => ($sTypeVariant === null) ? 'preparedStatus' : $sTypeVariant,
            );
            return $this;
        }
    }    
    
    public function preparedType($sTypeVariant=null) {
        if ($this->oLoadedProduct === null) {
            $sTitle = $this->oI18n->get(ucfirst(MLModul::gi()->getMarketPlaceName()).'_Productlist_Header_sPreparedType');
            if ($sTitle == ucfirst(MLModul::gi()->getMarketPlaceName()).'_Productlist_Header_sPreparedType') {
                $sTitle = $this->oI18n->get('Productlist_Header_sPreparedType');
            }
            $this->aFields[] = __function__;
            $this->aHeader[__function__] = array(
                'title' => $sTitle,
                'type' => 'preparedType',
                'type_variant' => ($sTypeVariant === null) ? 'preparedType' : $sTypeVariant,
            );
            return $this;
        }
    }
    public function addMLField($aHead) {
        if ($this->oLoadedProduct === null) {
            $this->aHeader[] = $aHead;
            return $this;
        }
    }
}

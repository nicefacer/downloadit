<?php
class ML_Magento_Helper_Model_Product_Variants {
    protected $oProduct = null;
    protected $aMessages = array();
    protected $aProductsConfig=null;
    
    protected function multiplyOptions($oChild, $aShop = array(), $aMagna = null, $aRecursiveInfo = array()) {
        $aOptions = isset($aShop['options'])?$aShop['options']:null;
        $aSuper   = isset($aShop['super']  )?$aShop['super']  :null;
        if ($aOptions === null) {
            $aOut = array();
            $aVariantsConfig = array();
            /* @var $oOption Mage_Catalog_Model_Product_Option */
            foreach ($this->oProduct->getOptions() as $oOption) {
                if (!$oOption->getIsRequire()) {
                    if (MLSetting::gi()->get('MagentoUseNotRequiredOptions')) {//not required add empty value
                        $aVariantsConfig[$oOption->getId()][] = array(
                            'sku' => 'keine auswahl',
                            'name' => 'keine auswahl',
                            'price' => 0,
                            'price_type' => 'fixed',
                            'default_title' => '',
                            'store_title' => '',
                            'default_price' => '',
                            'default_price_type' => '',
                            'store_price' => '',
                            'store_price_type' => '',
                            'option_type_id' => '',
                            'option_id' => '',
                            'sort_order' => '',
                            'title' => '',
                            'label' => ''
                        );
                    } else {//dont use empty values
                        continue;
                    }
                }
                foreach ($oOption->getValues() as $oValue) {
                    $aValue = $oValue->getData();
                    $aValue['label'] = $oOption->default_title;
                    $aVariantsConfig[$oOption->getId()][] = $aValue;
                }
            }
            if (count($aVariantsConfig) == 1) {
                $aVariants = array();
                foreach (current($aVariantsConfig) as $sKey => $aVariantConfig) {
                    foreach ($aVariantConfig as $sName => $sCurrentVal) {
                        $aVariants[$sKey][$sName][] = $sCurrentVal;
                    }
                }
            } else {
                $aVariants = $this->multiplyOptions($oChild, array('options'=>$aVariantsConfig));
            }
            $aSkus = array();
            foreach ($aVariants as $aValue) {
                $aQuote=array();
                if($aSuper!=null){
                    $aQuote['shop']['super_attribute']=$aSuper;
                    $aQuote['magna']['super_attribute']=$aMagna['magna'];
                }
                $aMagnaOptions=array();
                foreach (array_keys($aValue['option_id']) as $iTypeIdKey) {
                    $aQuote['shop']['options'][$aValue['option_id'][$iTypeIdKey]] = $aValue['option_type_id'][$iTypeIdKey];
                    foreach(array_keys($aValue) as $sOption){
                        $aMagnaOptions[$aValue['option_id'][$iTypeIdKey]][$sOption]=$aValue[$sOption][$iTypeIdKey];
                        
                    }
                }
                $aQuote['magna']['options'] = $aMagnaOptions;
                if (!in_array(json_encode($aQuote), $aSkus)) {

                    $aSkus[] = json_encode($aQuote);
                    $aOut[] = $aQuote;
                    $this->aProductsConfig[]=array_merge(array('product'=>$oChild),$aQuote);
                }
            }
            return $aOut;
        } else {
            $aOut = array();
            reset($aOptions);
            $aNext = $aOptions;
            $aGroup = current($aOptions);
            $sKey = key($aOptions);
            if (count($aNext)) {
                unset($aNext[$sKey]);
                foreach ($aGroup as $aValues) {
                    $aRec = $this->multiplyOptions($oChild, array('options'=>$aNext), null,array_merge_recursive($aRecursiveInfo, $aValues));
                    if (!isset($aRec['sku'])) {
                        foreach ($aRec as $aRecValue) {
                            $aOut[] = $aRecValue;
                        }
                    } else {
                        $aOut[] = $aRec;
                    }
                }
            } else {
                return $aRecursiveInfo;
            }
            return $aOut;
        }
    }


    public function getMessages() {
        return $this->aMessages;
    }

    protected function addMessage($sMessage) {
        if (!in_array($sMessage, $this->aMessages)) {
            $this->aMessages[] = $sMessage;
        }
        return $this;
    }
    public function setProduct($oProduct) {
        if (
                !is_object($this->oProduct) 
                || $oProduct->getId() != $this->oProduct->getId()
        ) {
            $this->aMessages = array();
            $this->aProductsConfig = array();
            $this->oProduct = $oProduct;
        }
        return $this;
    }
    public function getVariants() {
        if (
            is_object($this->oProduct) 
            && empty($this->aMessages) 
            && empty($this->aProductsConfig)
        ) {
            $this->getVariantCount();
            if (empty($this->aMessages)) {
                try {
                    $iStoreId =  (int) MLModul::gi()->getConfig('lang');
                    $this->oProduct->setStoreId($iStoreId)->load($this->oProduct->getId());
                } catch (ML_Filesystem_Exception $oEx) {//no modul
                }
                if (in_array($this->oProduct->getTypeId(), array('simple', 'virtual'))) {
                    $blSelf = true;
                    foreach ($this->oProduct->getOptions() as $oChild) {
                        if ($oChild->getIsRequire()) {
                            $blSelf = false;
                            break;
                        }
                    }
                    if ($blSelf) {
                        $this->aProductsConfig[] = array('product'=>$this->oProduct);
                    }else{
                        $this->multiplyOptions($this->oProduct);
                    }
                } elseif ($this->oProduct->getTypeId() == 'configurable') {
                    /* @var $oModel Mage_Catalog_Model_Product_Type_Configurable */
                    $oModel = Mage::getModel('catalog/product_type_configurable');
                    // equal to $aChilds = $this->oProduct->getTypeInstance()->getUsedProducts() but load for defined shop
                    $oCollection =
                        $this->oProduct->getTypeInstance()->getUsedProductCollection()
                        ->addAttributeToSelect('*')
                        ->addFilterByRequiredOptions()
                    ;
                    try {
                        $iStoreId = (int) MLModul::gi()->getConfig('lang');
                        $oCollection->setStore($iStoreId)
                            ->joinField(
                                'store_id', 
                                Mage::getSingleton('core/resource')->getTableName('catalog_category_product_index'), 
                                'store_id', 
                                'product_id=entity_id', 
                                '{{table}}.store_id = '.$iStoreId, 
                                'left'
                            )
                        ;
                    } catch (ML_Filesystem_Exception $oEx) {//no modul
                    }
                    $oCollection->getSelect()
                        ->distinct(true)
                    ;
                    $aChilds=array();
                    foreach ($oCollection->load() as $oItem) {
                        if (isset($iStoreId)) {
				$oItem->setStoreId($iStoreId);
			}
                        $aChilds[]=$oItem;
                    }
                    if (count($aChilds) != 0) {
                        try{//initialize magento shop for loading attributes in defined language
                            MLShop::gi()->initMagentoStore((int) MLModul::gi()->getConfig('lang'));
                        }catch(Exception $oEx){//no modul
                        }
                        $_attributes = $this->oProduct->getTypeInstance(true)->getConfigurableAttributes($this->oProduct);
                        $aSuperConf = array();
                        foreach ($_attributes as $_attribute) {
                            $aData = $_attribute->toArray();
                            //in some magentos label is null
                            $aLabels = $aData['product_attribute']->getStoreLabels();
                            try {
				$sLabel = isset($aLabels[(int) MLModul::gi()->getConfig('lang')]) ? $aLabels[(int) MLModul::gi()->getConfig('lang')] : current($aLabels);
			    } catch (Exception $oEx) {
				$sLabel = current($aLabels);
			    }
                            if (empty($sLabel)) {
                                $sLabel = $aData['product_attribute']->getStoreLabel();
                            }
                            if (empty($sLabel)) {
                                $sLabel = $aData['label'];
                            }
                            $aPrices = array();
                            foreach ($aData['prices'] as $aPrice){
                                $aPrice['title'] = $aPrice['label'];
                                $aPrice['label'] = $sLabel;
                                $aPrices[$aPrice['value_index']] = $aPrice;
                            }
                            $aSuperConf[]=array(
                                'id' => $_attribute->getAttributeId(),
                                'code' => $aData['product_attribute']->getAttributeCode(),
                                'labels' => $aPrices
                            );
                         }
                        /* @var $oChild ML_Magento_Model_Shop_Product */
                        foreach ($aChilds as $oChild) {
                            $aParentIds = $oModel->getParentIdsByChild($oChild->getId());
                            if (count($aParentIds) > 1) {
                                $this->addMessage('subarticle have multiple parents (ids: '.implode(', ', $aParentIds).').');
                            }
                            $aSuper = array();
                            $aMagnaSuper = array();
                            foreach ($aSuperConf as $aValue) {
                                $aSuper[$aValue['id']] = $oChild->{$aValue['code']};
                                $aMagnaSuper[$aValue['id']] = $aValue['labels'][$oChild->{$aValue['code']}];
                                $aMagnaSuper[$aValue['id']]['code'] = $aValue['code'];
                            }
                            $aMultiply = $this->multiplyOptions(
                                $oChild, 
                                array('super'=>$aSuper), 
                                array('shop'=>$aSuper, 'magna'=>$aMagnaSuper)
                            );
                            if (count($aMultiply) == 0) {
                                $this->aProductsConfig[] = array(
                                    'product' => $oChild,
                                    'shop' => array('super_attribute'=>$aSuper),
                                    'magna' => array('super_attribute'=>$aMagnaSuper)
                                );
                            }
                        }
                    }
                }
            }
        }
        return $this->aProductsConfig;
    }
    public function getVariantCount() {
        try {
            $iStoreId = (int) MLModul::gi()->getConfig('lang');
            $this->oProduct->setStoreId($iStoreId)->load($this->oProduct->getId());
        } catch (ML_Filesystem_Exception $oEx) {//no modul
        }
        $iOptions=1;
        foreach ($this->oProduct->getOptions() as $oChild) {
            if (
                $oChild->getIsRequire() 
                || MLSetting::gi()->get('MagentoUseNotRequiredOptions')
            ) {
                $iOptions = $iOptions*(
                    count($oChild->getValues())
                    +(MLSetting::gi()->get('MagentoUseNotRequiredOptions')?1:0)
                );
            }
        }
        if (in_array($this->oProduct->getTypeId(), array('simple', 'virtual'))) {
            $iOut=$iOptions;
        } elseif ($this->oProduct->getTypeId() == 'configurable') {
            // equal to $aChilds = $this->oProduct->getTypeInstance()->getUsedProducts() but load for defined shop
            $oCollection = $this
                ->oProduct->getTypeInstance()->getUsedProductCollection()
                ->addFilterByRequiredOptions()
            ;
            try {
                $aConfig = MLModul::gi()->getConfig();
                $iStoreId = (int)$aConfig['lang'];
                $oCollection        
                    ->setStore($iStoreId)
                    ->joinField(
                        'store_id', 
                        Mage::getSingleton('core/resource')->getTableName('catalog_category_product_index'), 
                        'store_id', 
                        'product_id=entity_id', 
                        '{{table}}.store_id = '.$iStoreId, 
                        'left'
                    )
                ; 
            }catch (ML_Filesystem_Exception $oEx) {//no modul
            }
            $oCollection->getSelect()->distinct(true);
            $iChilds = $oCollection->count();
            $iOut = $iOptions*$iChilds;
        } else {
            $this->addMessage(
                MLI18n::gi()->get(
                    'Productlist_ProductMessage_sErrorProductTypeNotSupported', 
                    array('productType' => $this->oProduct->getTypeId())
                )
            );
            $iOut= 0;
        }
        if ($iOut==0) {
            $this->addMessage(
                MLI18n::gi()->get('Productlist_ProductMessage_sErrorNoVariants')
            );
        } elseif ($iOut>MLSetting::gi()->get('iMaxVariantCount')) {
            $this->addMessage(
                MLI18n::gi()->get(
                    'Productlist_ProductMessage_sErrorToManyVariants', 
                    array('variantCount'=>$iOut,'maxVariantCount'=>MLSetting::gi()->get('iMaxVariantCount'))
                )
            );
        }
        return $iOut;
    }
}
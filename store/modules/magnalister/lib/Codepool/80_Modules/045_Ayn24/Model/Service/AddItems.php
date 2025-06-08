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
class ML_Ayn24_Model_Service_AddItems extends ML_Modul_Model_Service_AddItems_Abstract {

    protected function uploadItems() {
        return $this;
    }

    protected function getProductArray() {
        /* @var $oHelper ML_Ayn24_Helper_Model_Service_Product */
        $oHelper = MLHelper::gi('Model_Service_Product');
        $aMasterProducts = array();
        foreach ($this->oList->getList() as $oProduct) {
            /* @var $oProduct ML_Shop_Model_Product_Abstract */
            $oHelper->setProduct($oProduct);
            $aVariants = $this->oList->getVariants($oProduct);

            $oMasterProduct = $oHelper->setVariant($oProduct)->getData(true);
            $oMasterProduct['Variations'] = array();

            if (count($aVariants) > 1) {
                foreach ($aVariants as $oVariant) {
                    /* @var $oVariant ML_Shop_Model_Product_Abstract */
                    if ($this->oList->isSelected($oVariant)) {
                        $oHelper->resetData();

                        $oVariantProduct = $oHelper->setVariant($oVariant)->getData();
                        $oMasterProduct['ShippingDetails'] = $oVariantProduct['ShippingDetails'];
                        $oMasterProduct['Variations'][] = $oVariantProduct;
                    }
                }

                $this->processVariations($oMasterProduct);
            } else {
                $oMaster = current($aVariants);
                if ($this->oList->isSelected($oMaster)) {
                    $oHelper->resetData();
                    $oMasterProduct = array_merge($oMasterProduct, $oHelper->setVariant($oMaster)->getData());
                }
            }

            // Implemented here because master item is not stored in prepare table.
            $oMasterProduct['MarketplaceCategory'] = MLDatabase::factory('ayn24_prepare')
                ->set('products_id', reset($aVariants)->get('id'))
                ->get('PrimaryCategory');

            $oMasterProduct['MarketplaceCategory'] = str_replace('_', '.', $oMasterProduct['MarketplaceCategory']);

            $aMasterProducts[] = $oMasterProduct;
        }

        return $aMasterProducts;
    }

    protected function processVariations(&$aProduct) {
        foreach ($aProduct['Variations'] as &$aVariant) {
            unset($aVariant['ShippingDetails']);//only fo master
            $oPreparedItem = MLDatabase::factory('ayn24_prepare')
                ->set('products_id', $aVariant['VariationId']);

            $aVarConfigParts = explode(':', $oPreparedItem->get('VariationConfiguration'));

            if (count($aVarConfigParts) !== 2) {
                continue;
            }

            $aNewVarConfig = array(
                'Identifier' => isset($aVarConfigParts[0]) ? $aVarConfigParts[0] : '',
                'CustomIdentifier' => isset($aVarConfigParts[1]) ? $aVarConfigParts[1] : ''
            );

            $varConfig = $this->loadVariationMatching($aNewVarConfig);
            if (empty($varConfig)) {
                MLErrorLog::gi()->addError(null, null, MLI18n::gi()->get('ayn24_error_checkin_variation_config_empty'), $aNewVarConfig);
                continue;
            }

            $textHelper = MLHelper::gi('text');

            $newVarConfigDecoded = array(
                'MpIdentifier' => $textHelper->decodeText($aNewVarConfig['Identifier']),
                'CustomIdentifier' => $textHelper->decodeText($aNewVarConfig['CustomIdentifier'])
            );

            $aProduct['MPVariationConfiguration'] = $newVarConfigDecoded;

            $aVariant['MarketplaceSku'] = $aVariant['SKU'];
            foreach ($aVariant['Variation'] as &$vSet) {
                if (!isset($varConfig[$vSet['code']])) {
                    $sMessage = MLI18n::gi()->get(
                        'ayn24_error_checkin_variation_config_missing_nameid',
                        array (
                            'Attribute' => $vSet['name'],
                            'SKU' =>  $aVariant['SKU'],
                            'MpIdentifier' => fixHTMLUTF8Entities($newVarConfigDecoded['MpIdentifier']),
                        )
                    );
                    MLMessage::gi()->addWarn($sMessage, '', false);
                    MLErrorLog::gi()->addApiError(array('ERRORMESSAGE'=>$sMessage));
                    throw new Exception;
                }
                $matching = $varConfig[$vSet['code']];
                
                $vSet['MPName'] = $matching['MPName'];
                $vSet['Name'] = $vSet['name'];
                $vSet['Value'] = $vSet['value'];
                $vSet['ValueId'] = $vSet['valueid'];
                if(array_key_exists($vSet['ValueId'] , $matching['Values'])){
                    $vSet['MPValue'] = $matching['Values'][$vSet['ValueId']];                    
                }
                unset($vSet['code'],$vSet['valueid'],$vSet['name'],$vSet['value']);
            }

            $aVariant['SKU'] = (getDBConfigValue('general.keytype', '0') == 'artNr') ? $aVariant['MarketplaceSku'] : $aVariant['MarketplaceId'];

            // @todo: Check reduced price
            // if the reduced price is available here it has been enabled in the module configuration and should be used.
            if (isset($aVariant['PriceReduced'])) {
                $aVariant['Price'] = $aVariant['PriceReduced'];
            }
        }

        if (empty($aProduct['Variations'])) {
            MLErrorLog::gi()->addError(
                $aProduct['VariationId'], $aProduct['SKU'], MLI18n::gi()->get('ayn24_error_checkin_variation_config_cannot_calc_variations'), $aProduct);
            return false;
        }

        return true;
    }

    protected function loadVariationMatching($variationConfiguration) {
        $varConfig = MLDatabase::factory('ayn24_variantmatching')
            ->set('Identifier', $variationConfiguration['Identifier'])
            ->set('CustomIdentifier', $variationConfiguration['CustomIdentifier'])
            ->get('ShopVariation');

        if (empty($varConfig)) {
            return false;
        }

        $textHelper = MLHelper::gi('text');
        $identifier = $textHelper->decodeText($variationConfiguration['Identifier']);

        $apiVarConfig = ML::gi()->instance('controller_ayn24_prepare_variations')->getMPVariationAttributes($identifier);

        $keys = array_keys($apiVarConfig);
        foreach ($keys as $apiKey) {
            $key = $textHelper->encodeText($apiKey);
            $mpname = $apiKey;
            $newkey = $varConfig[$key]['Code'];
            $varConfig[$key]['MPName'] = $mpname;
            $varConfig[$newkey] = $varConfig[$key];
            unset($varConfig[$key]);

            if ($varConfig[$newkey]['Kind'] == 'FreeText') {
                // some free text attributes have different values in different languages.
                $storeId = MLModul::gi()->getConfig('lang');
                $varConfig[$newkey]['Values'] = MLFormHelper::getShopInstance()->getAttributeOptions($newkey, $storeId);
            }
        }

        arrayEntitiesToUTF8($varConfig);

        return $varConfig;
    }
	
    protected function additionalErrorManagement($aResponse) {        
        if (isset($aResponse["CHECKINERRORS"]) && !empty($aResponse['CHECKINERRORS'])) {
            foreach ($aResponse['CHECKINERRORS'] as $aError) {
                MLMessage::gi()->addWarn($aError['ErrorMessage'], '', false);
                $aMessage = array();
                if(isset($aError['ErrorMessage'])){
                    $aMessage['ERRORMESSAGE'] = $aError['ErrorMessage'];
                }
                if(isset($aError['AdditionalData'])){
                    $aMessage['ERRORDATA'] = $aError['AdditionalData'];
                }
                MLErrorLog::gi()->addApiError($aMessage);
            }
        }
    }

	/**
     * Ayn24 can add item with quantity <= 0
     * @return boolean
     */
    protected function checkQuantity() {
        return true;
    }
}

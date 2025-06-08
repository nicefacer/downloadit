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
abstract class ML_Modul_Model_Service_AddItems_Abstract extends ML_Modul_Model_Service_Abstract {
    protected $oList = null;
    protected $blPurge = false;
    protected $sAction = 'AddItems';
    protected $sGetItemsFeeAction = 'GetArticlesFee';
    protected $aError = array();
    protected $aData = null;

    public function execute() {
        $this->addItems();
        $this->uploadItems();
        return $this;
    }

    public function setProductList(ML_Productlist_Model_ProductList_Abstract $oList) {
        $this->oList = $oList;
        return $this;
    }

    public function setPurge($blPurge) {
        $this->blPurge = $blPurge;
        return $this;
    }

    protected function uploadItems() {
        $aRequest = array(
            'ACTION' => 'UploadItems',
            'SUBSYSTEM' => $this->oModul->getMarketPlaceName(),
            'MARKETPLACEID' => $this->oModul->getMarketPlaceId(),
        );
        try {
            if (!MLSetting::gi()->get('blDryAddItems')) {
				$aResponse = MagnaConnector::gi()->submitRequest($aRequest);
                if (isset($aResponse['ERRORS'])) {
                    foreach ($aResponse['ERRORS'] as $aError) {
                        $sMessage = (isset($aError['SUBSYSTEM']) ? $aError['SUBSYSTEM'].' : ' : '').(isset($aError['ERRORDATA']) && isset($aError['ERRORDATA']['SKU']) ? ' Item SKU ( '.$aError['ERRORDATA']['SKU'].' ) ' : '').$aError['ERRORMESSAGE'];
                        MLMessage::gi()->addWarn($sMessage, '', false);
			MLErrorLog::gi()->addApiError($aError);
                    }
                }
                $this->additionalErrorManagement($aResponse);
            }
        } catch (MagnaException $oEx) {
            $this->handleException($oEx);
        } catch (Exception $oEx) {
            MLMessage::gi()->addDebug($oEx);
        }
        MLMessage::gi()->addDebug('Api Response: upload', '<textarea>'.json_indent(json_encode((isset($aResponse) && !empty($aResponse)) ? $aResponse : $aRequest)).'</textarea>');
        if (isset($oEx)) {
            throw $oEx;
        } else {
            return $this;
        }
    }

    /**
     * remove master or variants that their quantity <= 0
     * @return boolean
     */
    protected function checkQuantity() {
        foreach ($this->aData as $sKey => $aItem) {
            if (isset($aItem['Quantity']) && ((int) $aItem['Quantity']) <= 0) {
                $sMessage = MLI18n::gi()->get('sAddItemProductWithZeroQuantity');
                MLMessage::gi()->addWarn($sMessage, '', false);

                $oProduct = MLProduct::factory()->getByMarketplaceSKU($aItem['SKU'], true);
                if(!$oProduct->existsMlProduct()){//it is possible that we send a variation as master product(if variation is not supported) 
                    $oProduct = MLProduct::factory()->getByMarketplaceSKU($aItem['SKU']);  
                }                  
                $iProductId = $oProduct->get('id');
                MLErrorLog::gi()->addError($iProductId, $aItem['SKU'], $sMessage, array('SKU' => $aItem['SKU']));
                $this->aError[] = $sMessage;
                unset($this->aData[$sKey]);
            } elseif (!empty($aItem['Variations'])) {
                foreach ($aItem['Variations'] as $sVKey => $aVItem) {
                    if (isset($aVItem['Quantity']) && ((int) $aVItem['Quantity']) <= 0) {
                        unset($this->aData[$sKey]['Variations'][$sVKey]);
                    }
                }
                if (empty($aItem['Variations'])) {
                    $sMessage = MLI18n::gi()->get('sAddItemProductWithZeroQuantity');
                    MLMessage::gi()->addWarn($sMessage, '', false);

                    $oProduct = MLProduct::factory()->getByMarketplaceSKU($aItem['SKU']);
                    $iProductId = $oProduct->get('id');
                    MLErrorLog::gi()->addError($iProductId, $aItem['SKU'], $sMessage, array('SKU' => $aItem['SKU']));
                    $this->aError[] = $sMessage;
                    unset($this->aData[$sKey]);
                } else {
                    // reset index keys of array
                    $this->aData[$sKey]['Variations'] = array_values($this->aData[$sKey]['Variations']);
                }

            }

        }
        return !empty($this->aData);
    }

    protected function hookAddItem($iMagnalisterProductsId, &$aAddItemData) {
        /* {Hook} "additem": Enables you to extend or modify the product data that will be submitted to the marketplace.
            Variables that can be used: 
            <ul>
                <li>$iMagnalisterProductsId (int): Id of the product in the database table `magnalister_product`.</li>
                <li>$aProductData (array): Data row of `magnalister_product` for the corresponding $iMagnalisterProductsId. The field "productsid" is the product id from the shop.</li>
                <li>$iMarketplaceId (int): Id of marketplace</li>
                <li>$sMarketplaceName (string): Name of marketplace</li>
                <li>&$aAddItemData (array): Data for the AddItems request.</li>
            </ul>
        */
        if (($sHook = MLFilesystem::gi()->findhook('additem', 1)) !== false) {
            $aProductData = MLProduct::factory()->set('id', $iMagnalisterProductsId)->data();
            $iMarketplaceId = MLModul::gi()->getMarketPlaceId();
            $sMarketplaceName = MLModul::gi()->getMarketPlaceName();
            require $sHook;
        }
    }

    protected function addItems() {
        if (count($this->oList->getMasterIds(true)) > 0) {
            foreach ($this->getProductArray() as $iProductId => $aAddItemData) {
                $this->hookAddItem($iProductId, $aAddItemData);
                $this->aData[] = $aAddItemData;
            }
            if ($this->checkQuantity()) {
                $aRequest = array(
                    'ACTION' => $this->sAction,
                    'SUBSYSTEM' => $this->oModul->getMarketPlaceName(),
                    'MODE' => ($this->blPurge ? 'PURGE' : 'ADD'),
                    'MARKETPLACEID' => $this->oModul->getMarketPlaceId(),
                    'DATA' => array_values($this->aData)//use array_values() covert indexes to auto index , other indexes can make malform error specialy in Rakuten Additem 
                );
                try {
                    if (!MLSetting::gi()->get('blDryAddItems')) {
                        $aResponse = MagnaConnector::gi()->submitRequest($aRequest);
                        if (isset($aResponse['ERRORS'])) {
                            foreach ($aResponse['ERRORS'] as $aError) {
                                $sMessage = (isset($aError['SUBSYSTEM']) ? $aError['SUBSYSTEM'].' : ' : '').(isset($aError['ERRORDATA']) && isset($aError['ERRORDATA']['SKU']) ? ' Item SKU ( '.$aError['ERRORDATA']['SKU'].' ) ' : '').$aError['ERRORMESSAGE'];
                                MLMessage::gi()->addWarn($sMessage, '', false);
								MLErrorLog::gi()->addApiError($aError);
                            }
                        }                        
                        $this->additionalErrorManagement($aResponse);
                    }
                } catch (MagnaException $oEx) {
                    $oEx->setCriticalStatus(false);
					$this->handleException($oEx);
                } catch (Exception $oEx) {
                    MLMessage::gi()->addDebug($oEx);
                }
                MLMessage::gi()->addDebug('Api Response: addItems', '<textarea>'.json_indent(json_encode((isset($aResponse) && !empty($aResponse)) ? $aResponse : $aRequest)).'</textarea>');
            }
            $aStatistic = $this->oList->getStatistic();
            if ($aStatistic['iCountPerPage'] != $aStatistic['iCountTotal']) {
                throw new Exception('list not finished');
            }
        }
        return $this;
    }

    /**
     * Gets fee for items selected to be uploaded.
     *
     * @return array
     */
    public function getItemsFee() {
        $aRequest = array(
            'ACTION' => $this->sGetItemsFeeAction,
            'SUBSYSTEM' => $this->oModul->getMarketPlaceName(),
            'MARKETPLACEID' => $this->oModul->getMarketPlaceId(),
            'DATA' => $this->getItemsFeeData()
        );

        return MagnaConnector::gi()->submitRequest($aRequest);
    }

    /**
     * Gets data parameter of GetItemsFee request.
     *
     * @throws MagnaException
     */
    protected function getItemsFeeData() {
        throw new MagnaException('Method getItemsFeeData is not implemented in AddItems service. It must be implemented in order to get fee for items.');
    }

	protected function handleException($oEx) {
		MLMessage::gi()->addError($oEx, '', false);
		$this->aError[] = $oEx->getMessage();
	}

    /**
     * @return array of product data depend on marketplace
     */
    abstract protected function getProductArray();

    public function haveError() {
        return count($this->aError) > 0;
    }

    public function getErrors() {
        return array_unique($this->aError);
    }
    
    protected function additionalErrorManagement($aResponse){
        //just used in Allyouneed and Rakuten to get special error message
    }
}

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
MLFilesystem::gi()->loadClass('Form_Controller_Widget_Form_PrepareAbstract');

class ML_Cdiscount_Controller_Cdiscount_Prepare_Apply_Form extends ML_Form_Controller_Widget_Form_PrepareAbstract {

	public function construct() {
		parent::construct();
        $this->oPrepareHelper->bIsSinglePrepare = $this->oSelectList->getCountTotal() === '1';
	}
	
    protected $aParameters = array('controller');

    public function render() {
        $this->getFormWidget();
        return $this;
    }

	public function getRequestField($sName = null, $blOptional = false) {
		if (count($this->aRequestFields) == 0) {
			$this->aRequestFields = $this->getRequest($this->sFieldPrefix);
			$this->aRequestFields = is_array($this->aRequestFields)?$this->aRequestFields:array();
		}

		return parent::getRequestField($sName, $blOptional);
	}

    protected function getSelectionNameValue() {
        return 'apply';
    }

	protected function triggerBeforeFinalizePrepareAction() {
        $this->oPrepareList->set('preparetype', 'apply');
		if (!empty($this->oPrepareHelper->aErrors)) {
			foreach ($this->oPrepareHelper->aErrors as $error) {
				MLMessage::gi()->addError(MLI18n::gi()->get($error));
			}
			
			$this->oPrepareList->set('verified', 'ERROR');
			return false;
		}
		
        $this->oPrepareList->set('verified', 'OK');
        return true;
    }
    
    protected function categoriesField(&$aField) {
        $aField['subfields']['primary']['values'] = array('' => '..') + ML::gi()->instance('controller_cdiscount_config_prepare')->getField('primarycategory', 'values');

        foreach ($aField['subfields'] as &$aSubField) {
            //adding current cat, if not in top cat
            if (!array_key_exists($aSubField['value'], $aSubField['values'])) {
                $oCat = MLDatabase::factory('cdiscount_categories' . $aSubField['cattype']);
                $oCat->init(true)->set('categoryid', $aSubField['value'] ? $aSubField['value'] : 0);
                $sCat = '';
                foreach ($oCat->getCategoryPath() as $oParentCat) {
                    $sCat = $oParentCat->get('categoryname') . ' &gt; ' . $sCat;
                }

                $aSubField['values'][$aSubField['value']] = substr($sCat, 0, -6);
            }
        }
    }
    
    protected function itemConditionField(&$aField) {
		$aField['values'] = $this->callApi(array('ACTION' => 'GetOfferCondition'), 60);
	}

    protected function shippingTimeField(&$aField) {
		$aField['values'] = $this->callApi(array('ACTION' => 'GetDeliveryTimes'), 60);
	}
    
    protected function itemCountryField(&$aField) {
		$aField['values'] = $this->callApi(array('ACTION' => 'GetDeliveryCountries'), 60);
	}
    
    protected function callAjaxGetCategoryAttributes() {
        $sCategoryID = MLRequest::gi()->get('categoryid');
        $sItemID = MLRequest::gi()->get('itemid');
        try {
            if (empty($sCategoryID)) {
                return null;
            }

            $aCatAttributes = MagnaConnector::gi()->submitRequest(array('ACTION' => 'GetCategoryDetails', 'DATA' => array('CategoryID' => $sCategoryID)));
        } catch (MagnaException $me) {
            $aCatAttributes = array (
                'DATA' => null
            );
        }

        if (isset($aCatAttributes['DATA']['attributes'])) {
            MLSetting::gi()->add(
                'aAjax', array(
                    'Data' => $this->renderCatAttributes($aCatAttributes['DATA'], $sItemID)
                )
            );
        }
    }
    
    protected function callApi($aRequest, $iLifeTime){
        try { 
            $aResponse = MagnaConnector::gi()->submitRequestCached($aRequest, $iLifeTime);
            if ($aResponse['STATUS'] == 'SUCCESS' && isset($aResponse['DATA']) && is_array($aResponse['DATA'])) {
                return $aResponse['DATA'];
            } else {
                return array();
            }
		} catch (MagnaException $e) {
            return array();
		}
    }
    
    private function renderCatAttributes($category, $itemID) {
        $html = '';
		if (empty($category['attributes'])) {
			$html .= '<th>' . MLI18n::gi()->get('cdiscount_category_no_attributes') . '</th>';
		} else {
			$values = $this->getValuesFromPrepare($category['id_category'], $itemID);
			$oddEven = false;
			foreach ($category['attributes'] as $attribute) {
				$data = isset($values[$attribute['name']]) ? $values[$attribute['name']] : '';
				$class = ($oddEven = !$oddEven) ? 'odd' : 'even';
				if ($attribute['is_multiple_allowed']) {
					if (empty($data)) {
						$data = array( 0 => '');
					}
					
					$lastValue = end($data);
					$lastKey = key($data);

					foreach ($data as $key => $value) {
						$disabled = $lastKey === $key ? '' : 'disabled';
						$minusButton = $key === 0 ? '' : '<input id="' . $attribute['name'] . '_' . $key . '" type="button" value="-" class="mlbtn fullfont minus"/>';
						$html .= '<tr class="' . $class . '">
									<th id="' . $attribute['name'] . '_' . $key . '_th">' . fixHTMLUTF8Entities($attribute['title'], ENT_COMPAT, 'UTF-8') . '</th>
									<td class="input">
										<input type="text" class="fullwidth" name="ml[field][catAttributes][' . $attribute['name'] . '][values][]" id="' . $attribute['name'] . '_' . $key . '" value="' . $value . '">
									</td>
									<td style="width: 100px">
										<input id="' . $attribute['name'] . '_' . $key . '" type="button" value="+" class="mlbtn fullfont plus" ' . $disabled . '/>
										' . $minusButton . '
									</td>
									<input id="' . $attribute['name'] . '_' . $key . '" type="hidden" name="ml[field][catAttributes][' . $attribute['name'] . '][required]" value="' . $attribute['mandatory'] . '"/>
								 </tr>
								';
					}
				} else {
					$html .= '<tr class="' . $class . '">
								<th id="' . $attribute['name'] . '_th">' . fixHTMLUTF8Entities($attribute['title'], ENT_COMPAT, 'UTF-8') . '</th>
								<td class="input">
									<input type="text" class="fullwidth" name="ml[field][catAttributes][' . $attribute['name'] . '][values]" id="' . $attribute['name'] . '" value="' . $data . '">
								</td>
								<input id="' . $attribute['name'] . '" type="hidden" name="ml[field][catAttributes][' . $attribute['name'] . '][required]" value="' . $attribute['mandatory'] . '"/>
							 </tr>
							';
				}
			}			
		}
		
		return $html;
	}
    
    private function getValuesFromPrepare($catID, $itemID) {
		$result = false;
		if (isset($itemID)) {
			$result = MLDatabase::getDbInstance()->fetchOne('
				SELECT CatAttributes 
				FROM ' . TABLE_MAGNA_CDISCOUNT_PREPARE . '
				WHERE products_id = "' . $itemID . '" 
					AND PrimaryCategory = "' . $catID . '"
			');
		}
		
		if ($result !== false) {
			return json_decode($result, true);
		} else {
			$result = MLDatabase::getDbInstance()->fetchOne('
				SELECT CatAttributes 
				FROM ' . TABLE_MAGNA_CDISCOUNT_PREPARE . '
				WHERE PrimaryCategory = "' . $catID . '"
			');
			
			if ($result !== false) {
				return json_decode($result, true);
			}
		}
		
		return null;
	}
    
}

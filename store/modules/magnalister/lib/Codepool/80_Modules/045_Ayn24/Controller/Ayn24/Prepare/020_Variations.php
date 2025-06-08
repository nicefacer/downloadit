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
MLFilesystem::gi()->loadClass('Form_Controller_Widget_Form_ConfigAbstract');

class ML_Ayn24_Controller_Ayn24_Prepare_Variations extends ML_Form_Controller_Widget_Form_ConfigAbstract {

    protected $aParameters = array('controller');
    private $shopAttributes;

    public static function getTabTitle() {
        return MLI18n::gi()->get('ayn24_prepare_variations_title');
    }

    public static function getTabActive() {
        return MLModul::gi()->isAuthed();
    }

    public function deleteAction($blExecute = true) {
        if ($blExecute) {
            $aMatching = $this->getRequestField();
            $sCustomIdentifier = isset($aMatching['customidentifier']) ? $aMatching['customidentifier'] : '';
            $this->getVariationDb()->deleteCustomVariation($this->encodeText($sCustomIdentifier));
//            unset($aMatching['customidentifier']);
//            unset($aMatching['attributename']);
//            unset($aMatching['variationgroups']);
        }
    }

    public function saveAction($blExecute = true) {
        if ($blExecute) {
            $aMatching = $this->getRequestField();
            $sIdentifier = $aMatching['variationgroups.value'];
            $sCustomIdentifier = isset($aMatching['customidentifier']) ? $aMatching['customidentifier'] : '';
            if (isset($aMatching['attributename'])) {
                $sIdentifier = $aMatching['attributename'];
                if ($sIdentifier === 'none') {
                    MLMessage::gi()->addError(MLI18n::gi()->get('ayn24_prepare_match_variations_attribute_missing'));
                    return;
                }

                if ($sCustomIdentifier == '') {
                    MLMessage::gi()->addError(MLI18n::gi()->get('ayn24_prepare_match_variations_custom_ident_missing'));
                    return;
                }
            }

            if (isset($aMatching['variationgroups'])) {
                $sCustomIdentifier = $this->encodeText($sCustomIdentifier, false);
                $aMatching = $aMatching['variationgroups'][$sIdentifier];
                $oVariantMatching = $this->getVariationDb();
                $oVariantMatching->deleteCustomVariation($sCustomIdentifier);

                if ($sIdentifier == 'new') {
                    $sIdentifier = $aMatching['variationgroups.code'];
                    unset($aMatching['variationgroups.code']);
                }

                foreach ($aMatching as $key => $value) {
                    if ($value['Code'] == '') {
                        MLMessage::gi()->addError(MLI18n::gi()->get('ayn24_prepare_match_variations_not_all_matched'));
                        return;
                    }

                    $aMatching[$this->encodeText($key)] = $value;
                    unset($aMatching[$key]);
                }

                $oVariantMatching->set('Identifier', $this->encodeText($sIdentifier, false))
                    ->set('CustomIdentifier', $sCustomIdentifier)
                    ->set('ShopVariation', json_encode($aMatching))
                    ->save();
                MLMessage::gi()->addSuccess(MLI18n::gi()->get('ayn24_prepare_match_variations_saved'));
            } else {
                MLMessage::gi()->addError(MLI18n::gi()->get('ayn24_prepare_match_variations_no_selection'));
            }
        }
    }

    public function getRequestValue(&$aField) {
        parent::getRequestValue($aField);
        $sName = $aField['realname'];
        if ($sName === 'variationgroups.value') {
            return;
        }

        if (MLHttp::gi()->isAjax()) {
            $aRequestTriggerField = MLRequest::gi()->get('ajaxData');
            if ($aRequestTriggerField['method'] === 'variationmatching') {
               unset($aField['value']);
               return;
            }
        }
        
        if (!isset($aField['value'])) {
            $mValue = null;
            $aRequestFields = $this->getRequestField();
            $aNames = explode('.', $aField['realname']);
            if (count($aNames) > 1 && isset($aRequestFields[$aNames[0]])) {
                // parent real name is in format "variationgroups.qnvjagzvcm1hda____.rm9ybwf0.code"
                // and name in request is "[variationgroups][Buchformat][Format][Code]"
                $sName = $sKey = $aNames[0];
                $aTmp = $aRequestFields[$aNames[0]];
                for ($i = 1; $i < count($aNames); $i++) {
                    if (is_array($aTmp)) {
                        foreach ($aTmp as $key => $value) {
                            if ($this->encodeText($key) == $aNames[$i]) {
                                $sName .= '.' . $key;
                                $sKey = $key;
                                $aTmp = $value;
                                break;
                            } else if (strtolower($key) === 'code') {
                                break;
                            }
                        }
                    } else {
                        break;
                    }
                }

                if ($sKey && $sKey != $aNames[0] && !is_array($value)) {
                    $mValue = array($sKey => $value, 'name' => $sName);
                }
            }

            if ($mValue != null) {
                $aField['value'] = reset($mValue);
                $aField['valuearr'] = $mValue;
            }
        }
    }

    public function getValue(&$aField) {
        parent::getValue($aField);
        $sName = $aField['realname'];
        
        // when top variation groups drop down is changed, its value is updated in getRequestValue
        // otherwise, it should remain empty. 
        // without second condition this function will be executed recursevly because of the second line below.
        if (!isset($aField['value']) && $sName !== 'variationgroups.value') {
            // check whether we're getting value for standard group or for custom variation mathing group
            $sCustomGroupName = $this->getField('variationgroups.value', 'value');
            $aCustomIdentifier = explode(':', $sCustomGroupName);

            if (count($aCustomIdentifier) == 2 && ($sName === 'attributename' || $sName === 'customidentifier'))
            {
                $aField['value'] = $this->decodeText($aCustomIdentifier[$sName === 'attributename' ? 0 : 1]);
                return;
            }

            $aNames = explode('.', $sName);
            if (count($aNames) == 4 && strtolower($aNames[3]) === 'code') {
                // real name is in format "variationgroups.qnvjagzvcm1hda____.rm9ybwf0.code"
                $sCustomIdentifier = count($aCustomIdentifier) == 2 ? $aCustomIdentifier[1] : '';
                $aValue = $this->getVariationDb()
                    ->set('Identifier', $aNames[1])
                    ->set('CustomIdentifier', $sCustomIdentifier)
                    ->get('ShopVariation');
                if ($aValue) {
                    foreach ($aValue as $sKey => $aMatch) {
                        if ($sKey === $aNames[2]) {
                            $aField['value'] = $aMatch['Code'];
                            break;
                        }
                    }
                }
            }
        }
    }
	
	public function getMPVariationAttributes($sVariationValue) {
        $aValues = $this->getFromApi('GetVariantConfigurationDefinition', array('Code' => $sVariationValue));
        $result = array();
        if ($aValues) {
            foreach ($aValues['Attributes'] as $key => $value) {
                $result[$key] = $value['AttributeName'];
            }
        }

        return $result;
    }

    protected function encodeText($sText, $blLower = true) {
        return MLHelper::gi('text')->encodeText($sText, $blLower);
    }
    
    protected function decodeText($sText) {
        return MLHelper::gi('text')->decodeText($sText);
    }

    protected function getAttributeValues($sIdentifier, $sCustomIdentifier, $sAttributeCode) {
        $aValue = $this->getVariationDb()
            ->set('Identifier', $this->encodeText($sIdentifier), false)
            ->set('CustomIdentifier', $this->encodeText($sCustomIdentifier, false))
            ->get('ShopVariation');
        if ($aValue) {
            foreach ($aValue as $sKey => $aMatch) {
                if ($sKey === $this->encodeText($sAttributeCode)) {
                    return $aMatch['Values'];
                }
            }
        }

        return array();
    }

    protected function variationMatchingField(&$aField) {
        $aField['ajax'] = array(
            'selector' => '#' . $this->getField('variationgroups.value', 'id'),
            'trigger' => 'change',
            'field' => array(
                'type' => 'switch',
            ),
        );
    }

    protected function variationGroups_ValueField(&$aField) {
        $aValues = $this->getMPVariationGroups(true);
        $aField['values']['none'] = MLI18n::gi()->get('ML_AMAZON_LABEL_APPLY_PLEASE_SELECT');
        $aField['values'][MLI18n::gi()->get('ayn24_prepare_variations_groups')] = $aValues;
        $customVars = $this->getCustomVariations();
        if (count($customVars) > 0) {
            $aField['values'][MLI18n::gi()->get('ayn24_prepare_variations_groups_custom')] = $customVars;
        }

        $aField['values']['new'] = MLI18n::gi()->get('ayn24_prepare_variations_groups_new');
    }

    protected function deleteActionField(&$aField) {
        $sGroupIdentifier = $this->getField('variationgroups.value', 'value');
        if (strpos($sGroupIdentifier, ':') !== false) {
            $aField['type'] = 'submit';
            $aField['value'] = 'delete';
        } else {
            $aField['type'] = '';
        }
    }

    protected function attributeNameField(&$aField) {
        $aField['type'] = 'select';
        $aField['values'] = array_merge(
            array('none' => MLI18n::gi()->get('ML_AMAZON_LABEL_APPLY_PLEASE_SELECT')), $this->getMPVariationGroups(false));
    }

    protected function attributeNameAjaxField(&$aField) {
        $aField['type'] = 'ajax';
        $aField['cascading'] = true;
        $aField['breakbefore'] = true;
        $aField['ajax'] = array(
            'selector' => '#' . $this->getField('attributename', 'id'),
            'trigger' => 'change',
            'field' => array(
                'type' => 'variations',
            ),
        );
    }

    protected function getMPVariationGroups($blFinal) {
        $aValues = $this->getFromApi('GetAvailableVariantConfigurations');
        $result = array();
        if (count($aValues) > 0) {
            foreach ($aValues as $key => $value) {
                if ($value['IsFinal'] === $blFinal) {
                    $result[$key] = $value['Name'];
                }
            }
        }

        return $result;
    }

    protected function getShopAttributes() {
        if ($this->shopAttributes == null) {
            $this->shopAttributes = MLFormHelper::getShopInstance()->getAttributeListWithOptions();
        }

        return $this->shopAttributes;
    }

    protected function getShopAttributeValues($sAttributeCode) {
        $attributes = MLFormHelper::getShopInstance()->getAttributeOptions($sAttributeCode);
        $aResult = array();
        foreach ($attributes as $key => $value) {
            $aResult[$key] = array(
                'i18n' => $value,
            );
        }

        return $aResult;
    }

    protected function getMPAttributeValues($sVariationValue, $sAttributeCode) {
        $aValues = $this->getFromApi('GetVariantConfigurationDefinition', array('Code' => $sVariationValue));
        if (isset($aValues['Attributes'][$sAttributeCode])) {
            return $aValues['Attributes'][$sAttributeCode]['AllowedValues'];
        }

        return array();
    }

    private function getFromApi($actionName, $aData = array()) {
        try {
            $aResponse = MagnaConnector::gi()->submitRequestCached(array('ACTION' => $actionName, 'DATA' => $aData));
            if ($aResponse['STATUS'] == 'SUCCESS' && isset($aResponse['DATA']) && is_array($aResponse['DATA'])) {
                return $aResponse['DATA'];
            }
        } catch (MagnaException $e) {
            
        }

        return array();
    }
    
    /**
     * @return ML_Ayn24_Model_Table_Ayn24_VariantMatching
     */
    private function getVariationDb() {
        return MLDatabase::factory('ayn24_variantmatching');
    }
    
    private function getCustomVariations() {
        $aResult = $this->getVariationDb()->getCustomVariations();
        foreach ($aResult as $sKey => $sValue) {
            $aResult[$sKey] = $this->decodeText($sValue);
        }
        
        return $aResult;
    }
}

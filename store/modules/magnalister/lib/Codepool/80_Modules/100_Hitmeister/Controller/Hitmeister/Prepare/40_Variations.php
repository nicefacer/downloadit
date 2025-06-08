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

class ML_Hitmeister_Controller_Hitmeister_Prepare_Variations extends ML_Form_Controller_Widget_Form_ConfigAbstract {

    protected $aParameters = array('controller');
    private $shopAttributes;
    private $numberOfMaxAdditionalAttributes = 0;

    public static function getTabTitle() {
        return MLI18n::gi()->get('hitmeister_prepare_variations_title');
    }

    public static function getTabActive() {
        return MLModul::gi()->isAuthed();
    }

    public function deleteAction($blExecute = true) {
        if ($blExecute) {
            $aMatching = $this->getRequestField();
            $sCustomIdentifier = isset($aMatching['customidentifier']) ? $aMatching['customidentifier'] : '';
            $this->getVariationDb()->deleteCustomVariation($sCustomIdentifier);
//            unset($aMatching['customidentifier']);
//            unset($aMatching['attributename']);
//            unset($aMatching['variationgroups']);
        }
    }

    public function saveAction($blExecute = true) {
        if ($blExecute) {
            $aActions = $this->getRequest($this->sActionPrefix);
            $aMatching = $this->getRequestField();
            $sIdentifier = $aMatching['variationgroups.value'];
            $sCustomIdentifier = isset($aMatching['customidentifier']) ? $aMatching['customidentifier'] : '';
            if (isset($aMatching['attributename'])) {
                $sIdentifier = $aMatching['attributename'];
                if ($sIdentifier === 'none') {
                    MLMessage::gi()->addError(MLI18n::gi()->get('hitmeister_prepare_match_variations_attribute_missing'));
                    return;
                }

                if ($sCustomIdentifier == '') {
                    MLMessage::gi()->addError(MLI18n::gi()->get('hitmeister_prepare_match_variations_custom_ident_missing'));
                    return;
                }
            }

            if (isset($aMatching['variationgroups'])) {
                $aMatching = $aMatching['variationgroups'][$sIdentifier];
                $oVariantMatching = $this->getVariationDb();
                $oVariantMatching->deleteVariation($sIdentifier);

                if ($sIdentifier == 'new') {
                    $sIdentifier = $aMatching['variationgroups.code'];
                    unset($aMatching['variationgroups.code']);
                }

                $aErrors = array();
                foreach ($aMatching as $key => &$value) {
                    if ($value['Code'] == '' || !isset($value['Values']) || empty($value['Values'])) {
                        if ($value['Required']) {
                            $aErrors[] = $key . MLI18n::gi()->get('hitmeister_prepare_variations_error_text');
                        }

                        unset($aMatching[$key]);
                        continue;
                    }

                    if (!is_array($value['Values'])) {
                        continue;
                    }

                    $sInfo = MLI18n::gi()->get('hitmeister_prepare_variations_manualy_matched');
                    $sFreeText = $value['Values']['FreeText'];
                    unset($value['Values']['FreeText']);

                    if ($value['Values']['0']['Shop']['Key'] === 'noselection' || $value['Values']['0']['Marketplace']['Key'] === 'noselection') {
                        unset($value['Values']['0']);
                        if (empty($value['Values']) && $value['Required']) {
                            $aErrors[] = $key . MLI18n::gi()->get('hitmeister_prepare_variations_error_text');
                        }

                        continue;
                    }

                    if ($value['Values']['0']['Marketplace']['Key'] === 'reset') {
                        unset($aMatching[$key]);
                        continue;
                    }

                    if ($value['Values']['0']['Marketplace']['Key'] === 'manual') {
                        $sInfo = MLI18n::gi()->get('hitmeister_prepare_variations_free_text_add');
                        if (empty($sFreeText) || !isset($sFreeText)) {
                            $aErrors[] = $key . MLI18n::gi()->get('hitmeister_prepare_variations_error_free_text');
                            unset($value['Values']['0']);
                            continue;
                        }

                        $value['Values']['0']['Marketplace']['Value'] = $sFreeText;
                    }

                    if ($value['Values']['0']['Marketplace']['Key'] === 'auto') {
                        $this->autoMatch($sIdentifier, $key, $value);
                        continue;
                    }

                    $this->checkNewMatchedCombination($value['Values']);
                    if ($value['Values']['0']['Shop']['Key'] === 'all') {
                        $newValue = array();
                        $i = 0;
                        foreach($this->getShopAttributeValues($value['Code']) as $keyAttribute => $valueAttribute) {
                            $newValue[$i]['Shop']['Key'] = $keyAttribute;
                            $newValue[$i]['Shop']['Value'] = $valueAttribute;
                            $newValue[$i]['Marketplace']['Key'] = $value['Values']['0']['Marketplace']['Key'];
                            $newValue[$i]['Marketplace']['Value'] = $value['Values']['0']['Marketplace']['Value'] . $sInfo;
                            $i++;
                        }

                        $value['Values'] = $newValue;
                    } else {
                        $value['Values']['0']['Marketplace']['Value'] .= $sInfo;
                    }
                }

                $oVariantMatching->set('Identifier', $sIdentifier)
                    ->set('CustomIdentifier', $sCustomIdentifier)
                    ->set('ShopVariation', json_encode($aMatching))
                    ->save();

                if ($aActions['saveaction'] === '1') {
                    if (empty($aErrors)) {
                        MLMessage::gi()->addSuccess(MLI18n::gi()->get('hitmeister_prepare_match_variations_saved'));
                    } else {
                        foreach($aErrors as $sError) {
                            MLMessage::gi()->addError($sError);
                        }
                    }
                }
            } else {
                MLMessage::gi()->addError(MLI18n::gi()->get('hitmeister_prepare_match_variations_no_selection'));
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
                            if ($key == $aNames[$i]) {
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

            if (count($aCustomIdentifier) == 2 && ($sName === 'attributename' || $sName === 'customidentifier')){
                $aField['value'] = $aCustomIdentifier[$sName === 'attributename' ? 0 : 1];
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
        $aValues = $this->getFromApi('GetCategoryDetails', array('CategoryID' => $sVariationValue));
        $result = array();
        if ($aValues) {
            foreach ($aValues['attributes'] as $value) {
                $result[$value['name']] = array(
                    'value' => $value['title'],
                    'required' => $value['mandatory'],
                );
            }
        }

        $aResultFromDB = $this->getAttributesFromDB($sVariationValue, '');
        $iFromDb = 0;
        if ($aResultFromDB) {
            $array = $this->arrayFilterKey($aResultFromDB, function($key) {
                return strpos($key, 'additional_attribute_') === 0;
            });

            $iFromDb = count($array);
        }

        if ($iFromDb < $this->numberOfMaxAdditionalAttributes) {
            for ($i = 0; $i <= $iFromDb; $i++) {
                $result['additional_attribute_' . $i] = array(
                    'value' => MLI18n::gi()->get('hitmeister_prepare_variations_additional_attribute_label'),
                    'required' => false,
                );
            }
        } else {
            for ($i = 0; $i < $iFromDb; $i++) {
                $result['additional_attribute_' . $i] = array(
                    'value' => MLI18n::gi()->get('hitmeister_prepare_variations_additional_attribute_label'),
                    'required' => false,
                );
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

    protected function getAttributeValues($sIdentifier, $sCustomIdentifier, $sAttributeCode = '', $bFreeText = false) {
        $aValue = $this->getVariationDb()
            ->set('Identifier', $sIdentifier, false)
            ->set('CustomIdentifier', $sCustomIdentifier)
            ->get('ShopVariation');
        if ($aValue) {
            if (!empty($sAttributeCode)) {
                foreach ($aValue as $sKey => $aMatch) {
                    if ($sKey === $sAttributeCode) {
                        return $aMatch['Values'];
                    }
                }
            } else {
                return $aValue;
            }
        }

        if ($bFreeText) {
            return '';
        }

        return array();
    }

    protected function variationGroupsField(&$aField) {
        $aField['subfields']['variationgroups.value']['values'] = array('' => '..') + ML::gi()->instance('controller_hitmeister_config_prepare')->getField('primarycategory', 'values');

        foreach ($aField['subfields'] as &$aSubField) {
            //adding current cat, if not in top cat
            if (!array_key_exists($aSubField['value'], $aSubField['values'])) {
                $oCat = MLDatabase::factory('hitmeister_categories' . $aSubField['cattype']);
                $oCat->init(true)->set('categoryid', $aSubField['value'] ? $aSubField['value'] : 0);
                $sCat = '';
                foreach ($oCat->getCategoryPath() as $oParentCat) {
                    $sCat = $oParentCat->get('categoryname') . ' &gt; ' . $sCat;
                }

                $aSubField['values'][$aSubField['value']] = substr($sCat, 0, -6);
            }
        }
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
            $this->shopAttributes = MLFormHelper::getShopInstance()->getPrefixedAttributeList();
        }

        return $this->shopAttributes;
    }

    protected function getShopAttributeValues($sAttributeCode) {
        return MLFormHelper::getShopInstance()->getPrefixedAttributeOptions($sAttributeCode);
    }

    protected function getMPAttributeValues($sCategoryId, $sMAttributeCode, $sAttributeCode = false) {
        if ($sAttributeCode) {
            $aValues = $this->getShopAttributeValues($sAttributeCode);
        } else {
            $aValues = array();
        }

        return array(
            'values' => $aValues,
            'from_mp' => false
        );
    }

    protected function getAttributesFromDB($sIdentifier, $sCustomIdentifier) {
        $aValue = $this->getVariationDb()
            ->set('Identifier', $sIdentifier, false)
            ->set('CustomIdentifier', $sCustomIdentifier)
            ->get('ShopVariation');

        if ($aValue) {
            return $aValue;
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
     * @return ML_Hitmeister_Model_Table_Hitmeister_VariantMatching
     */
    private function getVariationDb() {
        return MLDatabase::factory('hitmeister_variantmatching');
    }

    private function getCustomVariations() {
        $aResult = $this->getVariationDb()->getCustomVariations();
        foreach ($aResult as $sKey => $sValue) {
            $aResult[$sKey] = $sValue;
        }

        return $aResult;
    }

    private function autoMatch($categoryId, $sMPAttributeCode, &$aAttributes) {
        $aMPAttributeValues = $this->getMPAttributeValues($categoryId, $sMPAttributeCode, $aAttributes['Code']);
        $sInfo = MLI18n::gi()->get('hitmeister_prepare_variations_auto_matched');
        $blFound = false;
        if ($aAttributes['Values']['0']['Shop']['Key'] === 'all') {
            $newValue = array();
            $i = 0;
            foreach($this->getShopAttributeValues($aAttributes['Code']) as $keyAttribute => $valueAttribute) {
                foreach ($aMPAttributeValues['values'] as $key => $value) {
                    if (strcasecmp($valueAttribute, $value) == 0) {
                        $newValue[$i]['Shop']['Key'] = $keyAttribute;
                        $newValue[$i]['Shop']['Value'] = $valueAttribute;
                        $newValue[$i]['Marketplace']['Key'] = $key;
                        $newValue[$i]['Marketplace']['Value'] = $value . $sInfo;
                        $blFound = true;
                        $i++;
                        break;
                    }
                }
            }

            $aAttributes['Values'] = $newValue;
        } else {
            foreach ($aMPAttributeValues['values'] as $key => $value) {
                if (strcasecmp($aAttributes['Values']['0']['Shop']['Value'] , $value) == 0) {
                    $aAttributes['Values']['0']['Marketplace']['Key'] = $key;
                    $aAttributes['Values']['0']['Marketplace']['Value'] = $value . $sInfo;
                    $blFound = true;
                    break;
                }
            }
        }

        if (!$blFound) {
            unset($aAttributes['Values']['0']);
        }

        $this->checkNewMatchedCombination($aAttributes['Values']);
    }

    private function checkNewMatchedCombination(&$aAttibutes) {
        foreach ($aAttibutes as $key => $value) {
            if ($key === 0) {
                continue;
            }

            if (isset($aAttibutes['0']) && $value['Shop']['Key'] === $aAttibutes['0']['Shop']['Key']) {
                unset($aAttibutes[$key]);
                break;
            }
        }
    }

    private function arrayFilterKey($input, $callback) {
        if (!is_array($input) ) {
            trigger_error( 'array_filter_key() expects parameter 1 to be array, ' . gettype( $input ) . ' given', E_USER_WARNING );
            return null;
        }

        if (empty($input)) {
            return $input;
        }

        $filteredKeys = array_filter( array_keys( $input ), $callback );
        if (empty($filteredKeys)) {
            return array();
        }

        $input = array_intersect_key(array_flip($filteredKeys), $input);

        return $input;
    }
}

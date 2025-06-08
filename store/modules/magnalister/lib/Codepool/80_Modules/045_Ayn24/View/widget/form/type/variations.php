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
/** @var ML_Ayn24_Controller_Ayn24_Prepare_Variations $this */
class_exists('ML', false) or die();
$mParentValue = $this->getField('variationgroups.value', 'value');
if (is_array($mParentValue)) {
    reset($mParentValue);
    $mParentValue = key($mParentValue);
}

$sCustomAttribute = $this->getField('attributename', 'value');
if ($sCustomAttribute !== null) {
    $mParentValue = $sCustomAttribute;
}

if (strpos($mParentValue, ':') !== false) {
    $mParentValue = explode(':', $mParentValue);
    $mParentValue = $this->decodeText($mParentValue[0]);
}

$i18n = $this->getFormArray('aI18n');
if (!empty($mParentValue) && $mParentValue !== 'none' && $mParentValue !== 'new') {
    $aShopAttributes = $this->getShopAttributes();
    $aAyn24Attributes = $this->getMPVariationAttributes($mParentValue);
    $aFieldset = array(
        'id' => $this->getIdent() . '_fieldset_' . $this->encodeText($mParentValue),
        'legend' => array(
            'i18n' => $i18n['legend']['variationmatching'],
            'template' => 'two-columns',
        ),
        'row' => array(
            'template' => 'default',
        ),
    );
    foreach ($aAyn24Attributes as $key => $value) {
        $sBaseName = "field[variationgroups][$mParentValue][$key]";
        $sName = $sBaseName . '[Code]';
        $sId = 'variationgroups.' . $this->encodeText($mParentValue) . '.' . $this->encodeText($key) . '.code';
        $aSelectField = $this->getField($sId);
        $aSelectField['type'] = 'select';
        $aSelectField['name'] = $sName;
        $aSelectField['values'] = $aShopAttributes;
        $aSelectField['i18n'] = $i18n['field']['webshopattribute'];

        $aAjaxField = $this->getField($sId . '_ajax');
        $aAjaxField['type'] = 'ajax';
        $aAjaxField['cascading'] = true;
        $aAjaxField['breakbefore'] = true;
        $aAjaxField['padding-right'] = 0;
        $aAjaxField['i18n']['label'] = '';
        if (isset($aSelectField['value']) && $aSelectField['value'] != null) {
            // value field on ajax is used to initialize cascading ajax fields in attributematch.php 
            // when variation group is selected
            $aAjaxField['value'] = array(
                $key => $aSelectField['value'],
                'name' => 'variationgroups.' . $mParentValue . '.' . $key,
            );
        }

        $aAjaxField['ajax'] = array(
            'selector' => '#' . $aSelectField['id'],
            'trigger' => 'change',
            'field' => array(
                'id' => $sId . '_ajax_field',
                'type' => 'attributematch',
            ),
        );

        $aSubfield = $this->getField($sId . '_sub');
        $aSubfield['type'] = 'subFieldsContainer';
        $aSubfield['i18n']['label'] = $value;
        $aSubfield['subfields'] = array(
            'select' => $aSelectField,
            'ajax' => $aAjaxField,
            'hidden' => array(
                'type' => 'hidden',
                'name' =>  $sBaseName . '[Kind]',
                'id' => $sId . '_kind',
                'value' => count($this->getMPAttributeValues($mParentValue, $key)) > 0 ? 'Matching' : 'FreeText',
            ),
        );

        $aFieldset['fields'][] = $aSubfield;
    }
    ?>
    <table class="attributesTable">
        <?php $this->includeView('widget_form_fieldset', array('aFieldset' => $aFieldset)); ?>
    </table>
    <?php
}

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

$aParent = $this->getField(substr($aField['realname'], 0, -5));
$aParentValue = isset($aParent['valuearr']) ? $aParent['valuearr'] : null;
if ($aParentValue == null) {
    // if parent's value is a string it is set from database. 
    // in that case, field's value has all the information needed here.
    $aParentValue = $aField['value'];
}

if (is_array($aParentValue) && count($aParentValue) === 2 && reset($aParentValue) != '') {
    $aName = explode('.', $aParentValue['name']);
    $sName = 'field[' . implode('][', $aName) . '][Values]';
    $sAttributeCode = reset($aParentValue);
    $sMPAttributeCode = key($aParentValue);
    $sVariationValue = $aName[1];
    $aShopAttributes = $this->getShopAttributeValues($sAttributeCode);
    $aMPAttributes = $this->getMPAttributeValues($sVariationValue, $sMPAttributeCode);
    $i18n = $this->getFormArray('aI18n');

    $sCustomGroupName = $this->getField('variationgroups.value', 'value');
    $aCustomIdentifier = explode(':', $sCustomGroupName);
    $sCustomIdentifier = count($aCustomIdentifier) == 2 ? $this->decodeText($aCustomIdentifier[1]) : '';
    $aNewField = array(
        'type' => 'matching',
        'name' => $sName,
        'i18n' => $i18n['field']['attributematching'],
        'addonempty' => true,
        'automatch' => true,
        'valuessrc' => $aShopAttributes,
        'valuesdst' => $aMPAttributes,
        'value' => $this->getAttributeValues($sVariationValue, $sCustomIdentifier, $sMPAttributeCode)
    );
    $this->includeType($aNewField);
} else {
    // without this line the whole row is removed which removes needed controls
    echo ' ';
}

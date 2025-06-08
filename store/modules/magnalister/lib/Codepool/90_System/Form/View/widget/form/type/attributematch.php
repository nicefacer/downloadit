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
/** @var ML_Hitmeister_Controller_Hitmeister_Prepare_Variations $this */
class_exists('ML', false) or die();

$aParent = $this->getField(substr($aField['realname'], 0, -5));
$aParentValue = isset($aParent['valuearr']) ? $aParent['valuearr'] : null;

//Getting type of tab (is it variation tab or apply form)
$sParentId = ' ' . $aParent['id'];
$ini = strpos($sParentId, 'hitmeister_prepare_');
if ($ini == 0) return '';
$ini += strlen('hitmeister_prepare_');
$len = strpos($sParentId, '_field', $ini) - $ini;
$tabType = substr($sParentId, $ini, $len);
if ($tabType === 'variations') {
    $sId = 'hitmeister_prepare_variations';
} else {
    $sId = 'hitmeister_prepare_apply_form';
}


if ($aParentValue == null) {
    // if parent's value is a string it is set from database. 
    // in that case, field's value has all the information needed here.
    $aParentValue = isset($aField['value']) ? $aField['value'] : null;
}

if (is_array($aParentValue) && count($aParentValue) === 2 && reset($aParentValue) != '') {
    $aName = explode('.', $aParentValue['name']);
    $sName = 'field[' . implode('][', $aName) . '][Values]';
    $sAttributeCode = reset($aParentValue);
    $sMPAttributeCode = key($aParentValue);
    $sVariationValue = $aName[1];
    $aShopAttributes = $this->getShopAttributeValues($sAttributeCode);
    $aMPAttributes = $this->getMPAttributeValues($sVariationValue, $sMPAttributeCode, $sAttributeCode);
    $i18n = $this->getFormArray('aI18n');

    $sCustomGroupName = $this->getField('variationgroups.value', 'value');
    $aCustomIdentifier = explode(':', $sCustomGroupName);
    $sCustomIdentifier = count($aCustomIdentifier) == 2 ? $aCustomIdentifier[1] : '';
    $aMatchedAttributes = $this->getAttributeValues($sVariationValue, $sCustomIdentifier, $sMPAttributeCode);
    if ($sAttributeCode === 'freetext') {
        $aNewField = array(
            'type' => 'string',
            'name' => $sName,
            'value' => $this->getAttributeValues($sVariationValue, $sCustomIdentifier, $sMPAttributeCode, true)
        );
    } else if ($sAttributeCode === 'category') {
        $oCat = MLDatabase::factory('hitmeister_categoriesmarketplace');
        $iCatId = $this->getAttributeValues($sVariationValue, $sCustomIdentifier, $sMPAttributeCode, true);
        $oCat->init(true)->set('categoryid', $iCatId);
        $sCat = '';
        foreach ($oCat->getCategoryPath() as $oParentCat) {
            $sCat = $oParentCat->get('categoryname') . ' &gt; ' . $sCat;
        }

        $sCat = substr($sCat, 0, -6);

        $aNewField = array(
            'name' => 'field[categories]',
            'type' => 'categoryselect',
            'subfields' => array(
                'primary' => array(
                    'name' => $sName,
                    'type' => 'optional',
                    'cattype' => 'marketplace',
                    'realname' => $sName,
                    'hint' => array(
                        'template' => 'text'
                    ),
                    'i18n' => array(
                        'label' => '1. Marktplatz-Kategorie:'
                    ),
                    'id' => $sId . '_field_variationgroups_value_additional',
                    'value' => $iCatId,
                    'optional' => array(
                        'active' => true,
                        'active' => true,
                        'field' => array(
                            'type' => 'categoryselect'
                        ),
                        'defaultvalue' => true
                    ),
                    'values' => array('' => '..') + ML::gi()->instance('controller_hitmeister_config_prepare')->getField('primarycategory', 'values') + array($iCatId => $sCat)
                )
            ),
            'realname' => 'categories',
            'id' =>  $sId . '_field_categories_additional',
            'hint' => array(
                'template' => 'text'
            ),
            'i18n' => array(
                'label' => 'Hitmeister Kategorien'
            )
        );

        ?>
        <script type="text/javascript">//<![CDATA[
            (function($) {
                function escapeSelector(s){
                    return s.replace( /(:|\.|\[|\])/g, "\\$1" );
                }
                $(document).on('click', '.ml-js-category-btn', function() {
                    var element = $(this);
                    var eModal = $(element.attr("data-ml-catselector"));
                    if (typeof eModal.val() == 'undefined') {
                        eModal = $('#modal-<?php echo $sId ?>_field_variationgroups_value');
                    }

                    var eSelect = element.closest("tr").find("select");
                    eModal.jDialog({
                        width : '75%',
                        buttons: {
                            "Abbrechen" : function() {
                                $( this ).dialog( "close" );
                            },
                            "OK" : function() {
                                var eRadio = eModal.find("input[type=radio]:checked");
                                if (eSelect.find("option[value="+escapeSelector(eRadio.val())+"]").length == 0) {
                                    eSelect.append('<option value="'+eRadio.val()+'">'+eRadio.attr("title")+'</option>');
                                }
                                eSelect.val(eRadio.val()).change();
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                    eModal.parents('.ui-dialog').find('.ui-dialog-titlebar').append(eModal.find('.ml-js-ui-dialog-titlebar-additional').addClass('ml-ui-dialog-titlebar-additional'));
                });
            })(jqml);
            //]]></script>
        <?php
    } else if (empty($aShopAttributes)) {
        $aNewField = array(
            'type' => 'hidden',
            'id' =>  $sId . '_field_hidden',
            'name' => $sName,
            'value' => 'true'
        );
    } else {
        $aNewField = array(
            'type' => 'matchingselect',
            'name' => $sName,
            'i18n' => $i18n['field']['attributematching'],
            'addonempty' => true,
            'automatch' => true,
            'valuessrc' => $aShopAttributes,
            'valuesdst' => $aMPAttributes,
            'values' => $aMatchedAttributes
        );
    }

    $this->includeType($aNewField);
} else {
    // without this line the whole row is removed which removes needed controls
    echo ' ';
}

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
MLI18n::gi()->ayn24_prepare_prepare = 'Neue Produkte erstellen';
MLI18n::gi()->ayn24_prepare_form_category = 'Ayn24 Kategorien is mandatory.';
MLI18n::gi()->ayn24_prepare_variations_title = 'Varianten-Matching';
MLI18n::gi()->ayn24_prepare_variations_groups = 'Ayn24 Gruppen';
MLI18n::gi()->ayn24_prepare_variations_groups_custom = 'Eigene Gruppen';
MLI18n::gi()->ayn24_prepare_variations_groups_new = 'Eigene Gruppe anlegen';
MLI18n::gi()->ayn24_prepare_match_variations_no_selection = 'Bitte w&auml;hlen Sie eine Variantengruppe aus.';
MLI18n::gi()->ayn24_prepare_match_variations_custom_ident_missing = 'Bitte w&auml;hlen Sie Bezeichner.';
MLI18n::gi()->ayn24_prepare_match_variations_attribute_missing = 'Bitte w&auml;hlen Sie Attributsnamen.';
MLI18n::gi()->ayn24_prepare_match_variations_not_all_matched = 'Bitte weisen Sie allen Ayn24 Attributen ein Shop-Attribut zu.';
MLI18n::gi()->ayn24_prepare_match_variations_saved = 'Erfolgreich gespeichert.';
MLI18n::gi()->ayn24_prepare_match_variations_delete = 'Wollen Sie die eigene Gruppe wirklich l&ouml;schen? Alle zugeh&ouml;rigen Variantenmatchings werden dann ebenfalls gel&ouml;scht.';
MLI18n::gi()->ayn24_error_checkin_variation_config_empty = 'Variationen sind nicht konfiguriert.';
MLI18n::gi()->ayn24_error_checkin_variation_config_cannot_calc_variations = 'Es konnten keine Variationen errechnet werden.';
MLI18n::gi()->ayn24_error_checkin_variation_config_missing_nameid = 'Es konnte keine Zuordnung f&uuml;r das Shop Attribut "{#Attribute#}" bei der gew&auml;hlten Ayn24 Variantengruppe "{#MpIdentifier#}" f&uuml;r den Varianten Artikel mit der SKU "{#SKU#}" gefunden werden.';

MLI18n::gi()->add('ayn24_prepare_prepare_form', array(
    'legend' => array(
        'categories' => 'Kategorie Matching',
        'variations' => 'Variantenkonfiguration',
        'shipping' => 'Versandoptionen',
    ),
    'field' => array(
        'categories' => array(
            'label' => 'Kategorie Matching',
        ),
        'primarycategory' => array(
            'label' => 'Ayn24 Kategorie:',
        ),
        'variationconfiguration' => array(
            'label' => 'Variantenkonfiguration',
            'withoutvariations' => 'Keine Varianten &uuml;bertragen',
        ),
        'shippingcost' => array(
            'label' => 'Versandkosten',
        ),
        'shippingtype' => array(
            'label' => 'Versandtyp',
        ),
    ),
), false);

MLI18n::gi()->add('ayn24_prepare_variations', array(
    'legend' => array(
        'variations' => 'Variantengruppe von Ayn24 ausw&auml;hlen',
        'attributes' => 'Attributsnamen von Ayn24 ausw&auml;hlen',
        'variationmatching' => array('Ayn24 Attribut', 'Mein Web-Shop Attribut'),
        'action' => '{#i18n:form_action_default_legend#}',
    ),
    'field' => array(
        'variationgroups.value' => array(
            'label' => 'Variantengruppe',
        ),
        'deleteaction' => array(
            'label' => '{#i18n:ML_BUTTON_LABEL_DELETE#}',
        ),
        'groupschanged' => array(
            'label' => '',
        ),
        'attributename' => array(
            'label' => 'Attributsnamen',
        ),
        'attributenameajax' => array(
            'label' => '',
        ),
        'customidentifier' => array(
            'label' => 'Bezeichner',
        ),
        'webshopattribute' => array(
            'label' => 'Web-Shop Attribut',
        ),
        'saveaction' => array(
            'label' => '{#i18n:ML_BUTTON_LABEL_SAVE_DATA#}',
        ),
        'attributematching' => array(
            'matching' => array(
                'titlesrc' => 'Shop-Wert',
                'titledst' => 'Ayn24-Wert',
            ),
        ),
    ),
), false);

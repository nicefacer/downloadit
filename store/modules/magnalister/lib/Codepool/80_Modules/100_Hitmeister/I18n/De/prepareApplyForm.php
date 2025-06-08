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
 * (c) 2010 - 2015 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

MLI18n::gi()->add('hitmeister_prepare_apply_form',array(
    'legend' => array(
        'details' => 'Produktdetails',
        'categories' => 'Kategorie',
        'variationmatching' => array('Hitmeister Attribut', 'Mein Web-Shop Attribut'),
        'unit' => 'Unit attributes',
    ),
    'field' => array(
        'variationgroups' => array(
            'label' => 'Hitmeister Kategorien',
            'hint' => '<b>Hinweis:</b> Die mit <span class="bull">&bull;</span> markierten Felder sind Pflichtfelder und m&uuml;ssen ausgef&uuml;llt werden.',
        ),
        'variationgroups.value' => array(
            'label' => '1. Marktplatz-Kategorie:',
        ),
        'webshopattribute' => array(
            'label' => 'Web-Shop Attribut',
        ),
        'attributematching' => array(
            'matching' => array(
                'titlesrc' => 'Shop-Wert',
                'titledst' => 'Hitmeister-Wert',
            ),
        ),
        'title' => array(
            'label' => 'Titel',
        ),
        'subtitle' => array(
            'label' => 'Untertitel',
        ),
        'description' => array(
            'label' => 'Beschreibung',
        ),
        'images' => array(
            'label' => 'Produktbilder',
        ),
        'price' => array(
            'label' => 'Preis',
        ),
        'itemcondition' => array(
            'label' => 'Zustand',
        ),
        'shippingtime' => array(
            'label' => 'Lieferzeit',
        ),
        'itemcountry' => array(
            'label' => 'Artikel wird versandt aus',
        ),
        'comment' => array(
            'label' => 'Hinweise zu Ihrem Artikel',
        ),
    ),
),false);

MLI18n::gi()->add('hitmeister_prepare_variations', array(
    'legend' => array(
        'variations' => 'Variantengruppe von Hitmeister ausw&auml;hlen',
        'attributes' => 'Attributsnamen von Hitmeister ausw&auml;hlen',
        'variationmatching' => array('Hitmeister Attribut', 'Mein Web-Shop Attribut'),
        'action' => '{#i18n:form_action_default_legend#}',
    ),
    'field' => array(
        'variationgroups' => array(
            'label' => 'Variantengruppe',
            'hint' => '<b>Hinweis:</b> Die mit <span class="bull">&bull;</span> markierten Felder sind Pflichtfelder und m&uuml;ssen ausgef&uuml;llt werden.',
        ),
        'variationgroups.value' => array(
            'label' => '1. Marktplatz-Kategorie:',
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
                'titledst' => 'Hitmeister-Wert',
            ),
        ),
    ),
), false);

MLI18n::gi()->hitmeister_prepareform_max_length_part1 = 'Max length of';
MLI18n::gi()->hitmeister_prepareform_max_length_part2 = 'attribute is';
MLI18n::gi()->hitmeister_prepareform_category = 'Category attribute is mandatory.';
MLI18n::gi()->hitmeister_prepareform_title = 'Bitte geben Sie einen Titel an.';
MLI18n::gi()->hitmeister_prepareform_description = 'Bitte geben Sie eine Artikelbeschreibung an.';
MLI18n::gi()->hitmeister_prepareform_category_attribute = ' (Kategorie Attribute) ist erforderlich und kann nicht leer sein.';
MLI18n::gi()->hitmeister_category_no_attributes= 'Es sind keine Attribute f&uuml;r diese Kategorie vorhanden.';
MLI18n::gi()->hitmeister_prepare_variations_title = 'Varianten Matching';
MLI18n::gi()->hitmeister_prepare_variations_groups = 'Hitmeister Gruppen';
MLI18n::gi()->hitmeister_prepare_variations_groups_custom = 'Eigene Gruppen';
MLI18n::gi()->hitmeister_prepare_variations_groups_new = 'Eigene Gruppe anlegen';
MLI18n::gi()->hitmeister_prepare_match_variations_no_selection = 'Bitte w&auml;hlen Sie eine Variantengruppe aus.';
MLI18n::gi()->hitmeister_prepare_match_variations_custom_ident_missing = 'Bitte w&auml;hlen Sie Bezeichner.';
MLI18n::gi()->hitmeister_prepare_match_variations_attribute_missing = 'Bitte w&auml;hlen Sie Attributsnamen.';
MLI18n::gi()->hitmeister_prepare_match_variations_not_all_matched = 'Bitte weisen Sie allen Hitmeister Attributen ein Shop-Attribut zu.';
MLI18n::gi()->hitmeister_prepare_match_variations_saved = 'Erfolgreich gespeichert.';
MLI18n::gi()->hitmeister_prepare_match_variations_delete = 'Wollen Sie die eigene Gruppe wirklich l&ouml;schen? Alle zugeh&ouml;rigen Variantenmatchings werden dann ebenfalls gel&ouml;scht.';
MLI18n::gi()->hitmeister_error_checkin_variation_config_empty = 'Variationen sind nicht konfiguriert.';
MLI18n::gi()->hitmeister_error_checkin_variation_config_cannot_calc_variations = 'Es konnten keine Variationen errechnet werden.';
MLI18n::gi()->hitmeister_error_checkin_variation_config_missing_nameid = 'Es konnte keine Zuordnung f&uuml;r das Shop Attribut "{#Attribute#}" bei der gew&auml;hlten Ayn24 Variantengruppe "{#MpIdentifier#}" f&uuml;r den Varianten Artikel mit der SKU "{#SKU#}" gefunden werden.';
MLI18n::gi()->hitmeister_prepare_variations_free_text = 'Free text';
MLI18n::gi()->hitmeister_prepare_variations_additional_category = 'Additional category';
MLI18n::gi()->hitmeister_prepare_variations_error_text = ' attribute is mandatory.';
MLI18n::gi()->hitmeister_prepare_variations_error_missing_value = ' attribute is mandatory, and your product does not have value for chosen matched attribute from shop.';
MLI18n::gi()->hitmeister_prepare_variations_error_free_text = ': Free text field can not be empty.';
MLI18n::gi()->hitmeister_prepare_variations_matching_table = 'Gemachte';
MLI18n::gi()->hitmeister_prepare_variations_manualy_matched = ' - (Manually matched)';
MLI18n::gi()->hitmeister_prepare_variations_auto_matched = ' - (Auto matched)';
MLI18n::gi()->hitmeister_prepare_variations_free_text_add = ' - (Free text)';
MLI18n::gi()->hitmeister_prepare_variations_reset_info = 'Wollen Sie das Matching wirklich aufheben?';
MLI18n::gi()->hitmeister_prepare_variations_change_attribute_info = 'Bevor Sie Attribute &auml;ndern k&ouml;nnen, heben Sie bitte alle Matchings zuvor auf.';
MLI18n::gi()->hitmeister_prepare_variations_additional_attribute_label = 'Eigene Attribute';

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

MLI18n::gi()->add('fyndiq_prepare_form', array(
    'legend' => array(
        'details' => 'Produktdetails',
        'delivery' => 'Versand',
        'categories' => 'Kategorie',
    ),
    'field' => array(
        'categories' => array(
            'label' => 'Fyndiq Kategorien',
        ),
        'primarycategory' => array(
            'label' => '1. Marktplatz-Kategorie:',
        ),
        'catattributes' => array(
            'label' => 'Category attributes',
        ),
        'itemtitle' => array(
            'label' => 'Titel',
            'hint' => 'Titel max. 64 Zeichen',
        ),
        'description' => array(
            'label' => 'Beschreibung',
            'hint'  => 'Maximal 4096 Zeichen. Einige HTML-Tags und deren Attribute sind erlaubt. Diese Z&auml;hlen zu den 4096 Zeichen dazu.',
        ),
        'brand' => array(
            'label' => 'Brand',
        ),
        'images' => array(
            'label' => 'Produktbilder',
        ),
        'comparasionunit' => array(
            'label' => 'Comparasion unit',
        ),
        'location' => array(
            'label' => 'Location',
        ),
        'price' => array(
            'label' => 'Preis',
        ),
        'shippingtime' => array(
            'label' => 'Lieferzeit',
        ),
        'shippingcost' => array(
            'label' => 'Versandkosten (EUR)',
        ),
    ),
), false);

MLI18n::gi()->fyndiq_prepareform_max_length_part1 = 'Max length of';
MLI18n::gi()->fyndiq_prepareform_max_length_part2 = 'attribute is';
MLI18n::gi()->fyndiq_prepare_form_category = 'Fyndiq Kategorien is mandatory.';
MLI18n::gi()->fyndiq_prepare_form_itemtitle = 'Title attribute is mandatory, and must be between 5 and 64 characters';
MLI18n::gi()->fyndiq_prepare_form_description = 'Description attribute is mandatory, and must be between 10 and 4096 characters';
MLI18n::gi()->fyndiq_prepareform_category_attribute = ' category attribute is mandatory.';
MLI18n::gi()->fyndiq_category_no_attributes = 'There are no attributes for this category.';
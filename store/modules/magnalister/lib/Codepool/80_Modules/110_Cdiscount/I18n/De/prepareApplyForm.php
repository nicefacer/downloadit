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

MLI18n::gi()->add('cdiscount_prepare_apply_form',array(
    'legend' => array(
        'details' => 'Produktdetails',
        'categories' => 'Kategorie',
        'unit' => 'Unit attributes',
    ),
    'field' => array(
        'categories' => array(
            'label' => 'Cdiscount Kategorien',
        ),
        'primarycategory' => array(
            'label' => '1. Marktplatz-Kategorie:',
        ),
        'catattributes' => array(
            'label' => 'Category attributes',
        ),
        'title' => array(
            'label' => 'Titel',
            'hint' => 'Titel max. 132 Zeichen',
        ),
        'subtitle' => array(
            'label' => 'Untertitel',
        ),
        'description' => array(
            'label' => 'Beschreibung',
            'hint'  => 'Maximal 420 Zeichen.',
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
        'shipping_time' => array(
            'label' => 'Shipping time',
        ),
        'shippingtimemin' => array(
            'label' => 'Min (in days)',
        ),
        'shippingtimemax' => array(
            'label' => 'Max (in days)',
        ),
        'fee' => array(
            'label' => 'Shipping fee (€)',
        ),
        'shippingfeestandard' => array(
            'label' => 'Shipping fee (€)',
        ),
        'shippingfeeextrastandard' => array(
            'label' => 'Additional shipping fee (€)',
        ),
        'shippingfeetracked' => array(
            'label' => 'Shipping fee (€)',
        ),
        'shippingfeeextratracked' => array(
            'label' => 'Additional shipping fee (€)',
        ),
        'shippingfeeregistered' => array(
            'label' => 'Shipping fee (€)',
        ),
        'shippingfeeextraregistered' => array(
            'label' => 'Additional shipping fee (€)',
        ),
        'addfee' => array(
            'label' => 'Additional shipping fee (€)',
        ),
        'shipping_time_standard' => array(
            'label' => 'Standard',
        ),
        'shipping_time_tracked' => array(
            'label' => 'Tracked',
        ),
        'shipping_time_registered' => array(
            'label' => 'Registered',
        ),
        'comment' => array(
            'label' => 'Hinweise zu Ihrem Artikel',
            'hint'  => 'Maximal 200 Zeichen.',
        ),
    ),
)
,false);

MLI18n::gi()->cdiscount_prepareform_max_length_part1 = 'Max length of';
MLI18n::gi()->cdiscount_prepareform_max_length_part2 = 'attribute is';
MLI18n::gi()->cdiscount_prepareform_category = 'Category attribute is mandatory.';
MLI18n::gi()->cdiscount_prepareform_title = 'Title attribute is mandatory.';
MLI18n::gi()->cdiscount_prepareform_description = 'Description attribute is mandatory.';
MLI18n::gi()->cdiscount_prepareform_category_attribute = ' category attribute is mandatory.';
MLI18n::gi()->cdiscount_category_no_attributes= 'There are no attributes for this category.';
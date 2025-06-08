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
MLSetting::gi()->ayn24_prepare_prepare_form = array(
    'categories' => array(
        'fields' => array(
            array(
                'name' => 'categories',
                'type' => 'categoryselect',
                'subfields' => array(
                    'primary' => array('name' => 'primarycategory', 'type' => 'categoryselect', 'cattype' => 'marketplace'),
                ),
            ),
        ),
    ),
    'variations' => array(
        'fields' => array(
            array(
                'name' => 'variationconfiguration',
                'type' => 'select',
            ),
        ),
    ),
    'shipping' => array(
        'fields' => array(
            array(
                'name' => 'shippingcost',
                'type' => 'string',
            ),
            array(
                'name' => 'shippingtype',
                'type' => 'select',
            ),
        ),
    ),
);

MLSetting::gi()->ayn24_prepare_variations = array(
    'variations' => array(
        'fields' => array(
            array(
                'name' => 'variationgroups.value',
                'type' => 'select',
            ),
        ),
    ),
    'attributes' => array(
        'fieldss' => array(
            array(
                'name' => 'customidentifier',
                'type' => 'string',
            ),
            array(
                'name' => 'attributenametitle',
                'type' => 'subFieldsContainer',
                'subfields' => array(
                    'select' => array('name' => 'attributename', 'type' => 'select'),
                    'ajax' => array('name' => 'attributenameajax', 'type' => 'ajax'),
                ),
            ),
        ),
    ),
    'variationmatching' => array(
        'type' => 'ajaxfieldset',
        'legend' => array(
            'template' => 'two-columns',
        ),
        'field' => array (
            'name' => 'variationmatching',
            'type' => 'ajax',
        ),
    ),
    'action' => array(
        'legend' => array(
            'classes' => array(
                'mlhidden',
            ),
        ),
        'row' => array(
            'template' => 'action-row-row-row',
        ),
        'fields' => array(
            array(
                'name' => 'saveaction',
                'value' => 'save',
                'type' => 'submit', 
                'position' => 'right',
            ),
        ),
    ),
);

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
MLSetting::gi()->hitmeister_prepare_apply_form = array(
    'categories' => array(
		'fields' => array(
            array(
                'name' => 'variationgroups',
                'type' => 'categoryselect',
                'subfields' => array(
					'variationgroups.value' => array('name' => 'variationgroups.value', 'type' => 'categoryselect', 'cattype' => 'marketplace'),
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
	'details' => array(
		'fields' => array(
			array(
				'name' => 'title',
				'type' => 'string',
			),
			array(
				'name' => 'subtitle',
				'type' => 'string',
			),
			array(
				'name' => 'description',
				'type' => 'wysiwyg',
			),
			array(
				'name' => 'images',
				'type' => 'imagemultipleselect',
			),
		),
	),
    'unit' => array(
		'fields' => array(
			array(
				'name' => 'itemcondition',
				'type' => 'select',
			),
			array(
				'name' => 'price',
				'type' => 'price',
                'currency' => 'EUR',
                'enabled' => false,
			),
			array(
				'name' => 'shippingtime',
				'type' => 'select',
			),
			array(
				'name' => 'itemcountry',
				'type' => 'select',
			),
			array(
				'name' => 'comment',
				'type' => 'text',
			),
		),
	),
);

MLSetting::gi()->hitmeister_prepare_variations = array(
	'variations' => array(
		'fields' => array(
			array(
				'name' => 'variationgroups',
				'type' => 'categoryselect',
				'subfields' => array(
					'variationgroups.value' => array('name' => 'variationgroups.value', 'type' => 'categoryselect', 'cattype' => 'marketplace'),
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

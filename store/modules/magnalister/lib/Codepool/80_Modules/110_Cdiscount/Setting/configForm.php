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

MLSetting::gi()->add('cdiscount_config_account', array(
    'tabident' => array(
        'legend' => array(
            'classes' => array('mlhidden'),
        ),
        'fields' => array(
            array(
                'name' => 'tabident',
                'type' => 'string',
            ),
        ),
    ),
    'account' => array(
        'fields' => array(
			array(
                'name' => 'mpusername',
                'type' => 'string',
            ),
            array(
                'name' => 'mppassword',
                'type' => 'password',
                'savevalue' => '__saved__'
            ),
        ),
    ),
), false);


MLSetting::gi()->add('cdiscount_config_prepare', array(
    'prepare' => array(
        'fields' => array(
            array(
                'name' => 'prepare.status',
                'type' => 'bool',
            ),
            array(
                'name' => 'lang',
                'type' => 'select',
            ),
             array(
                'name' => 'imagepath',
                'type' => 'string',
                'expert' => true,
            ),
            array(
                'name' => 'itemcondition',
                'type' => 'select',
            ),
            array(
                'name' => 'shipping_time',
                'type' => 'subFieldsContainer',
                'subfields' => array(
                    'mintime' => array('name' => 'shippingtimemin', 'type' => 'string'),
                    'maxtime' => array('name' => 'shippingtimemax', 'type' => 'string'),
                )
            ),
            array(
                'name' => 'shipping_time_standard',
                'type' => 'subFieldsContainer',
                'subfields' => array(
                    'fee' => array('name' => 'shippingfeestandard', 'type' => 'string'),
                    'addfee' => array('name' => 'shippingfeeextrastandard', 'type' => 'string')
                ),
            ),
            array(
                'name' => 'shipping_time_tracked',
                'type' => 'subFieldsContainer',
                'subfields' => array(
                    'fee' => array('name' => 'shippingfeetracked', 'type' => 'string'),
                    'addfee' => array('name' => 'shippingfeeextratracked', 'type' => 'string')
                ),
            ),
            array(
                'name' => 'shipping_time_registered',
                'type' => 'subFieldsContainer',
                'subfields' => array(
                    'fee' => array('name' => 'shippingfeeregistered', 'type' => 'string'),
                    'addfee' => array('name' => 'shippingfeeextraregistered', 'type' => 'string')
                ),
            ),
        ),
        array(
            'name' => 'itemsperpage',
            'type' => 'string',
        ),
    ),
    'upload' => array(
        'fields' => array(
            array(
                'name' => 'checkin.status',
                'type' => 'bool',
            ),
			array(
                'name' => 'checkin.quantity',
                'type' => 'selectwithtextoption',
                'subfields' => array(
                    'select' => array(
                        'name' => 'quantity.type',
                        'default' => 'stock'
                    ),
                    'string' => array('name' => 'quantity.value'),
                )
            ),
        ),
    ),
), false);

MLSetting::gi()->add('cdiscount_config_price', array(
    'price' => array(
        'fields' => array(
            array(
                'name' => 'usevariations',
                'type' => 'bool',
                'default' => true
            ),
            array(
                'name' => 'price',
                'type' => 'subFieldsContainer',
                'subfields' => array(
                    'addkind' => array('name' => 'price.addkind', 'type' => 'select'),
                    'factor' => array('name' => 'price.factor', 'type' => 'string'),
                    'signal' => array('name' => 'price.signal', 'type' => 'string')
                )
            ),
            array(
                'name' => 'priceoptions',
                'type' => 'subFieldsContainer',
                'subfields' => array(
                    'group' => array('name' => 'price.group', 'type' => 'select'),
                    'usespecialoffer' => array('name' => 'price.usespecialoffer', 'type' => 'bool'),
                ),
            ),
            array(
                'name' => 'exchangerate_update',
                'type' => 'bool',
            ),
        )
    )
), false);

MLSetting::gi()->add('cdiscount_config_sync', array(
    'sync' => array(
        'fields' => array(
            array(
                'name' => 'stocksync.tomarketplace',
                'type' => 'addon_select',
                'addonsku' => 'FastSyncInventory',
            ),
            array(
                'name' => 'stocksync.frommarketplace',
                'type' => 'select',
            ),
            array(
                'name' => 'inventorysync.price',
                'type' => 'select',
            ),
        )
    )
), false);

MLSetting::gi()->add('cdiscount_config_orderimport', array(
    'importactive' => array(
        'fields' => array(
            array(
                'name' => 'importactive',
                'type' => 'subFieldsContainer',
                'subfields' => array(
                    'import' => array('name' => 'import', 'type' => 'radio', ),
                    'preimport.start' => array('name' => 'preimport.start', 'type' => 'datepicker'),
                ),
            ),
			array(
                'name' => 'orderstatus.open',
                'type' => 'select',
            ),
            array(
                'name' => 'customergroup',
                'type' => 'select',
            ),
            array(
                'name' => 'orderimport.shop',
                'type' => 'select',
            ),
        ),
    ),
    'mwst' => array(
        'fields' => array(
            array(
                'name' => 'mwst.fallback',
                'type' => 'string',
            ),
        ),
    ),
	'orderstatus' => array(
        'fields' => array(
            array(
                'name' => 'orderstatus.sync',
                'type' => 'select',
            ),
            array(
                'name' => 'orderstatus.shipped',
                'type' => 'select'
            ),
            array(
                'name' => 'orderstatus.cancelled',
                'type' => 'select'
            ),
        ),
    )
), false);

MLSetting::gi()->add('cdiscount_config_emailtemplate', array(
    'mail' => array(
        'fields' => array(
            array(
                'name' => 'mail.send',
                'type' => 'radio',
                'default' => false,
            ),
            array(
                'name' => 'mail.originator.name',
                'type' => 'string',
                'default' => '{#i18n:cdiscount_config_account_emailtemplate_sender#}',
            ),
            array(
                'name' => 'mail.originator.adress',
                'type' => 'string',
                'default' => '{#i18n:cdiscount_config_account_emailtemplate_sender_email#}',
            ),
            array(
                'name' => 'mail.subject',
                'type' => 'string',
                'default' => '{#i18n:cdiscount_config_account_emailtemplate_subject#}',
            ),
            array(
                'name' => 'mail.content',
                'type' => 'configMailContentContainer',
                'default' => '{#i18n:cdiscount_config_account_emailtemplate_content#}',
            ),
            array(
                'name' => 'mail.copy',
                'type' => 'radio',
                'default' => true,
            ),
        ),
    ),
), false);
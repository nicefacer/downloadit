<?php

MLSetting::gi()->add('bepado_config_account', array(    
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
            array(
                'name' => 'shopid',
                'type' => 'string',
            ),
            array(
                'name' => 'apikey',
                'type' => 'password',
                'savevalue' => '__saved__'
            ),
            array(
                'name' => 'ftpusername',
                'type' => 'string'
            ),
            array(
                'name' => 'ftppassword',
                'type' => 'password',
                'savevalue' => '__saved__'
            ),
        ),
    )
), false);


MLSetting::gi()->add('bepado_config_prepare', array(
    'prepare' => array(
        'fields' => array(
            array(
                'name' => 'prepare.status',
                'type' => 'bool',
            ),
        ),
    ),
    'upload' => array(
        'fields' => array(
            array(
                'name' => 'checkin.status',
                'type' => 'bool',
            ),
            array(
                'name' => 'lang',
                'type' => 'select',
            ),
            array(
                'name' => 'quantity',
                'type' => 'selectwithtextoption',
                'subfields' => array(
                    'select' => array('name' => 'quantity.type', 'type' => 'select'),
                    'string' => array('name' => 'quantity.value', 'type' => 'string')
                )
            ),
        )
    ),
    'shipping' => array(
        'fields' => array(
            array(
                'name' => 'shippingtime',
                'type' => 'select'
            ),
            array(
                'name' => 'shippingcontainer',
                'type' => 'duplicate',
                'duplicate' => array(
                    'field' => array('type' => 'subFieldsContainer')
                ),
                'subfields' => array(
                    array('name' => 'shippingcountry', 'type' => 'select'),
                    array('name' => 'shippingservice', 'type' => 'string'),
                    array('name' => 'shippingcost', 'type' => 'string'),
                )
            ),
        )
    ),
), false);

MLSetting::gi()->add('bepado_config_price', array(
    'price' => array(
        'fields' => array(
            array(
                'name' => 'price',
                'type' => 'subFieldsContainer',
                'subfields' => array(
                    'addkind' => array('name' => 'b2c.price.addkind', 'type' => 'select'),
                    'factor' => array('name' => 'b2c.price.factor', 'type' => 'string'),
                    'signal' => array('name' => 'b2c.price.signal', 'type' => 'string')
                )
            ),
            array(
                'name' => 'priceoptions',
                'type' => 'subFieldsContainer',
                'subfields' => array(
                    'group' => array('name' => 'b2c.price.group', 'type' => 'select'),
                    'usespecialoffer' => array('name' => 'b2c.price.usespecialoffer', 'type' => 'bool'),
                ),
            ),
            array(
                'name' => 'b2b.price',
                'type' => 'subFieldsContainer',
                'subfields' => array(
                    'addkind' => array('name' => 'b2b.price.addkind', 'type' => 'select'),
                    'factor' => array('name' => 'b2b.price.factor', 'type' => 'string'),
                    'signal' => array('name' => 'b2b.price.signal', 'type' => 'string'),
                    'active' => array('name' => 'b2b.price.active', 'type' => 'bool')
                )
            ),
            array(
                'name' => 'b2b.priceoptions',
                'type' => 'subFieldsContainer',
                'subfields' => array(
                    'group' => array('name' => 'b2b.price.group', 'type' => 'select'),
                    'usespecialoffer' => array('name' => 'b2b.price.usespecialoffer', 'type' => 'bool'),
                ),
            ),
            array(
                'name' => 'exchangerate_update',
                'type' => 'bool',
            ),
        )
    ),
), false);

MLSetting::gi()->add('bepado_config_sync', array(
    'sync' => array(
        'fields' => array(
            array(
                'name' => 'stocksync.tomarketplace',              
                'type' => 'select',/*
                'type' => 'addon_select',
                'addonsku' => 'FastSyncInventory',*/
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

MLSetting::gi()->add('bepado_config_orderimport', array(
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
                'name' => 'customergroup',
                'type' => 'select',
            ),
            array(
                'name' => 'orderimport.shop',
                'type' => 'select',
            ),
            array(
                'name' => 'orderstatus.open',
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
            /*//{search: 1427198983}
            array(
                'name' => 'mwst.shipping',
                'type' => 'string',
            ),
            //*/
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
                'name' => 'orderstatus.canceled',
                'type' => 'select'
            ),
        ),
    )
), false);

MLSetting::gi()->add('bepado_config_emailtemplate', array(
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
                'default' => '{#i18n:bepado_config_account_emailtemplate_sender#}',
            ),
            array(
                'name' => 'mail.originator.adress',
                'type' => 'string',
                'default' => '{#i18n:bepado_config_account_emailtemplate_sender_email#}',
            ),
            array(
                'name' => 'mail.subject',
                'type' => 'string',
                'default' => '{#i18n:bepado_config_account_emailtemplate_subject#}',
            ),
            array(
                'name' => 'mail.content',
                'type' => 'configMailContentContainer',
                'default' => '{#i18n:bepado_config_account_emailtemplate_content#}',
            ),
            array(
                'name' => 'mail.copy',
                'type' => 'radio',
                'default' => true,
            ),
        ),
    ),
), false);
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

MLSetting::gi()->add('meinpaket_config_account', array(
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
                'name' => 'username',
                'type' => 'string',
            ),
            array(
                'name' => 'password',
                'type' => 'password',
                'savevalue' => '__saved__'
            ),
        ),
    ),
), false);

MLSetting::gi()->add('meinpaket_config_prepare', array(
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
                'name' => 'catmatch.mpshopcats',
                'type' => 'bool',
                'default' => true,
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
                'name' => 'shippingcostfixed',
                'type' => 'bool',
            ),
            array(
                'name' => 'shippingtype',
                'type' => 'select',
            ),
        ),
    ),
    'checkin' => array(
        'fields' => array(
            array(
                'name' => 'checkin.status',
                'type' => 'bool',
            ),
            array(
                'name' => 'checkin.quantity',
                'type' => 'selectwithtextoption',
                'subfields' => array(
                    'select' => array('name' => 'quantity.type', 'default' => 'lump'),
                    'string' => array('name' => 'quantity.value'),
                )
            ),
            array (
                'name' => 'checkin.skipean',
                'type' => 'bool',
                'default' => true,
            ),
            array(
                'name' => 'checkin.leadtimetoship',
                'type' => 'select',
            ),
            array (
                'name' => 'checkin.taxmatching',
                'type' => 'matching',
            ),
            array(
                'name' => 'checkin.manufacturerfallback',
                'type' => 'string',
            ),
            array(
                'name' => 'checkin.shortdesc',
                'type' => 'select',
                'expert' => true,
            ),
            array(
                'name' => 'checkin.longdesc',
                'type' => 'select',
                'expert' => true,
            ),
            array(
                'name' => 'imagesize',
                'type' => 'select',
            ),
        ),
    )
), false);

MLSetting::gi()->add('meinpaket_config_price', array(
    'price' => array(
        'fields' => array(
            array(
                'name' => 'price',
                'type' => 'subFieldsContainer',
                'subfields' => array(
                    'addkind' => array('name' => 'price.addkind', 'type' => 'select', 'default' => 'percent'),
                    'factor' => array('name' => 'price.factor', 'type' => 'string', 'default' => '0'),
                    'signal' => array('name' => 'price.signal', 'type' => 'string'),
                )
            ),
            array(
                'name' => 'priceoptions',
                'type' => 'subFieldsContainer',
                'subfields' => array(
                    'group' => array('name' => 'price.group', 'type' => 'select', 'default' => '2'),
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

MLSetting::gi()->add('meinpaket_config_orderimport', array(
    'importactive' => array(
        'fields' => array(
            'importactive'=>array(
                'name' => 'importactive',
                'type' => 'subFieldsContainer',
                'subfields' => array(
                    'import' => array('name' => 'import', 'type' => 'radio', 'default' => true ),
                    'preimport.start' => array('name' => 'preimport.start', 'type' => 'datepicker', 'default' => date('d.m.Y')),
                ),
            ),
            'customergroup'=>array(
                'name' => 'customergroup',
                'type' => 'select',
                'default' => 2,
            ),
            'orderimport.shop' => array(
                'name' => 'orderimport.shop',
                'type' => 'select',
            ),
            'orderstatus.open'=>array(
                'name' => 'orderstatus.open',
                'type' => 'select',
                'default' => 'processing',
            ),
            'orderimport.shippingmethod'=>array(
                'name' => 'orderimport.shippingmethod',
                'type' => 'string',
                'default' => 'dhlmeinpaket',
                'expert' => true,
            ),
            'orderimport.paymentmethod'=>array(
                'name' => 'orderimport.paymentmethod',
                'type' => 'string',
                'default' => 'meinpaket',
                'expert' => true,
            ),
            /*array(
                'name' => 'defaultshipping',
                'type' => 'selectwithtextoption',
                'expert' => true,
                'subfields' => array(
                    'select' => array('name' => 'orderimport.shippingmethod', 'default' => 'textfield'),
                    'string' => array('name' => 'orderimport.shippingmethod.name', 'default' => 'dhlmeinpaket'),
                ),
            ),
            array(
                'name' => 'defaultpayment',
                'type' => 'selectwithtextoption',
                'expert' => true,
                'subfields' => array(
                    'select' => array('name' => 'orderimport.paymentmethod', 'default' => 'textfield'),
                    'string' => array('name' => 'orderimport.paymentmethod.name', 'default' => 'meinpaket'),
                ),
            ),*/
        ),
    ),
    'mwst' => array(
        'fields' => array(
            array(
                'name' => 'mwst.fallback',
                'type' => 'string',
                'default' => 19,
            ),
            /*//{search: 1427198983}
            array(
                'name' => 'mwst.shipping',
                'type' => 'string',
                'default' => 19,
            ),
            //*/
        ),
    ),
    'orderstatus' => array(
        'fields' => array(
            array(
                'name' => 'orderstatus.sync',
                'type' => 'select',
                'default' => 'no',
            ),
            array(
                'name' => 'orderstatus.shipped',
                'type' => 'select'
            ),
            array(
                'name' => 'orderstatus.canceled.customerrequest',
                'type' => 'select'
            ),
            array(
                'name' => 'orderstatus.canceled.outofstock',
                'type' => 'select'
            ),
            array(
                'name' => 'orderstatus.canceled.damagedgoods',
                'type' => 'select'
            ),
            array(
                'name' => 'orderstatus.canceled.dealerrequest',
                'type' => 'select'
            ),
        ),
    )
), false);

MLSetting::gi()->add('meinpaket_config_sync', array(
    'sync' => array(
        'fields' => array(
            array(
                'name' => 'stocksync.tomarketplace',              
                'type' => 'select',/*
                'type' => 'addon_select',
                'addonsku' => 'FastSyncInventory',*/
                'default' => 'auto',
            ),
            array(
                'name' => 'stocksync.frommarketplace',
                'type' => 'select',
                'default' => 'rel',
            ),
            array(
                'name' => 'inventorysync.price',
                'type' => 'select',
                'default' => 'auto',
            ),
        ),
    ),
), false);

MLSetting::gi()->add('meinpaket_config_emailtemplate', array(
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
                'default' => '{#i18n:meinpaket_config_account_emailtemplate_sender#}',
            ),
            array(
                'name' => 'mail.originator.adress',
                'type' => 'string',
                'default' => '{#i18n:meinpaket_config_account_emailtemplate_sender_email#}',
            ),
            array(
                'name' => 'mail.subject',
                'type' => 'string',
                'default' => '{#i18n:meinpaket_config_account_emailtemplate_subject#}',
            ),
            array(
                'name' => 'mail.content',
                'type' => 'configMailContentContainer',
                'default' => '{#i18n:meinpaket_config_account_emailtemplate_content#}',
            ),
            array(
                'name' => 'mail.copy',
                'type' => 'radio',
                'default' => true,
            ),
        ),
    ),
), false);
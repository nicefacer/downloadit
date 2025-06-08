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

MLSetting::gi()->add('ebay_config_account', array(    
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
            array(
                'name' => 'token',
                'type' => 'ebay_token',
                'icludeauth' => true
            ),
            array(
                'name' => 'site',
                'type' => 'select'
            ),
            array(
                'name' => 'currency',
                'type' => 'ajax'
            ),
        ),
    )
), false);


MLSetting::gi()->add('ebay_config_prepare', array(
    'location'=> array(
        'fields' => array(
            array(
                'name' => 'postalcode',
                'type' => 'string',
            ),
            array(
                'name' => 'location',
                'type' => 'string',
            ),
            array(
                'name' => 'country',
                'type' => 'select',
            ),
        )
    ),
    'prepare' => array(
        'fields' => array(
            array(
                'name' => 'prepare.status',
                'type' => 'bool',
            ),
            array(
                'name' => 'mwst',
                'type' => 'string',
            ),
            array(
                'name' => 'conditionid',
                'type' => 'select',
            ),
            array(
                'name' => 'lang',
                'type' => 'select',
            ),
            array(
                'name' => 'dispatchtimemax',
                'type' => 'select',
                'default' => '3',
            ),
            array(
                'name' => 'topten',
                'type' => 'topten',
                'expert' => true,
            ),
        ),
    ),
    'pictures' => array(
        'fields' => array(
            array(
                'name' => 'imagesize',
                'type' => 'select',
            ),
            array(
                'name' => 'gallerytype',
                'type' => 'select',
                'default' => 'Gallery',
            ),
            array(
                'name' => 'picturepack',
                'type' => 'addon_bool',
                'addonsku' => 'EbayPicturePack',
            ),
            array(
                'name' => 'variationdimensionforpictures',
            ),
        ),
    ),
    'payment' => array(
        'fields' => array(
             array(
                'name' => 'paymentmethods',
                'type' => 'multipleselect',
            ),
            'paypaladdress' => array(
                'name' => 'paypal.address',
                'type' => 'string',
            ),
            'paymentinstructions' => array(
                'name' => 'paymentinstructions',
                'type' => 'text',
            ),
        ),
    ),
    'shipping' => array(
        'fields' => array(
            array(
                'name' => 'shippinglocalcontainer',
                'type' => 'ebay_shippingcontainer'
            ),
            array(
                'name' => 'shippinginternationalcontainer',
                'type' => 'optional',
                'optional' => array(
                    'editable' => true,
                    'name' => 'shippinginternational',
                    'field' => array(
                        'type' => 'ebay_shippingcontainer'
                    )
                )
            ),
        ),
    ),
    'returnpolicy' => array(
        'fields' => array(
            array(
                'name' => 'returnpolicy.returnsaccepted',
                'type' => 'select',
            ),
            'returnswithin' => array(
                'name' => 'returnpolicy.returnswithin',
                'type' => 'select',
            ),
            array(
                'name' => 'returnpolicy.shippingcostpaidby',
                'type' => 'select',
            ),
            array(
                'name' => 'returnpolicy.description',
                'type' => 'text',
            ),
        ),
    ),
    'misc' => array(
        'fields' => array(  
            array(
                'name' => 'usevariations',
                'type' => 'bool',
                'default' => true
            ),
            'usePrefilledInfo' => array(
                'name' => 'useprefilledinfo',
                'type' => 'bool',
                'expert' => true,
            ),
            'privatelisting' => array(
                'name' => 'privatelisting',
                'type' => 'bool',
            ),
            'hitcounter' => array(
                'name' => 'hitcounter',
                'type' => 'select',
            ),
        ),
    ),
    'upload' => array(
        'fields' => array(
            array(
                'name' => 'productfield.brand',
                'type' => 'select',
            ),
        )
    ),
), false);

MLSetting::gi()->add('ebay_config_price', array(
        'fixedprice' => array(
            'fields' => array(
                array(
                    'name' => 'fixed.quantity',
                    'type' => 'selectwithtextoption',
                    'subfields' => array(
                        'select' => array('name' => 'fixed.quantity.type', 'type' => 'select'),
                        'string' => array('name' => 'fixed.quantity.value', 'type' => 'string')
                    )
                ),
                 array(
                    'name' => 'maxquantity',
                    'type' => 'string',
                ),
                array(
                    'name' => 'fixed.price',
                    'type' => 'subFieldsContainer',
                    'subfields' => array(
                        'addkind' => array('name' => 'fixed.price.addkind', 'type' => 'select'),
                        'factor' => array('name' => 'fixed.price.factor', 'type' => 'string'),
                        'signal' => array('name' => 'fixed.price.signal', 'type' => 'string')
                    )
                ),
                array(
                    'name' => 'fixed.priceoptions',
                    'type' => 'subFieldsContainer',
                    'subfields' => array(
                        'group' => array('name' => 'fixed.price.group', 'type' => 'select'),
                        'usespecialoffer' => array('name' => 'fixed.price.usespecialoffer', 'type' => 'bool'),
                    ),
                ),
               array(
                    'name' => 'fixed.duration',
                    'type' => 'select',
                ),
               array(
                    'name' => 'ebayplus',
                    'type' => 'bool',
                ),
            ),
        ),
        'chineseprice' => array(
            'fields' => array(  
                array(
                    'name' => 'chinese.quantity',
                    'type' => 'information',
                ),   
                array(
                    'name' => 'chinese.price',
                    'type' => 'subFieldsContainer',
                    'subfields' => array(
                        'addkind' => array('name' => 'chinese.price.addkind', 'type' => 'select'),
                        'factor' => array('name' => 'chinese.price.factor', 'type' => 'string'),
                        'signal' => array('name' => 'chinese.price.signal', 'type' => 'string')
                    )
                ),
                array(
                    'name' => 'chinese.buyitnow.price',
                    'type' => 'subFieldsContainer',
                    'subfields' => array(
                        'addkind' => array('name' => 'chinese.buyitnow.price.addkind', 'type' => 'select'),
                        'factor' => array('name' => 'chinese.buyitnow.price.factor', 'type' => 'string'),
                        'signal' => array('name' => 'chinese.buyitnow.price.signal', 'type' => 'string'),
                        'use' => array('name' => 'buyitnowprice', 'type' => 'bool'),
                    )
                ),
                array(
                    'name' => 'chinese.priceoptions',
                    'type' => 'subFieldsContainer',
                    'subfields' => array(
                        'group' => array('name' => 'chinese.price.group', 'type' => 'select'),
                        'usespecialoffer' => array('name' => 'chinese.price.usespecialoffer', 'type' => 'bool'),
                    ),
                ),
                array(
                    'name' => 'chinese.duration',
                    'type' => 'select',
                ),
            )
        ),

        'price' => array(
            'fields' => array(
                array(
                    'name' => 'bestofferenabled',
                    'type' => 'bool',
                ),
                array(
                    'name' => 'exchangerate_update',
                    'type' => 'bool',
                ),
            )           
        )
), false);


MLSetting::gi()->add('ebay_config_sync', array(
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
            array(
                'name' => 'inventory.import',
                'type' => 'radio',
                'default' => 'true',
                'expert' => 'true',
            ),
            array(
                'name' => 'synczerostock',
                'type' => 'addon_bool',
                'addonsku' => 'EbayZeroStockAndRelisting',
            ),
            array(
                'name' => 'syncrelisting',
                'type' => 'addon_bool',
                'addonsku' => 'EbayZeroStockAndRelisting',
            ),
            array(
                'name' => 'syncproperties',
                'type' => 'addon_bool',
                'addonsku' => 'EbayProductIdentifierSync',
            ),
        )
    ),
    'syncchinese' => array(
        'fields' => array(
            array(
                'name' => 'chinese.stocksync.tomarketplace',
                'type' => 'select',
            ),
            array(
                'name' => 'chinese.stocksync.frommarketplace',
                'type' => 'select',
            ),
            array(
                'name' => 'chinese.inventorysync.price',
                'type' => 'select',
            ),
        )
    )
), false);

MLSetting::gi()->add('ebay_config_orderimport', array(
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
            'orderstatus.open' => array(
                'name' => 'orderstatus.open',
                'type' => 'select',
            ),
            'importonlypaid' => array(
                'name' => 'importonlypaid',
                'type' => 'ebay_importonlypaid',
                'importonlypaid' => array('disablefields' => array('orderstatus.closed', 'updateable.orderstatus', 'update.orderstatus')),
            ),
            array(
                'name' => 'orderstatus.closed',
                'type' => 'multipleselect'
            ),
            array(
                'name' => 'orderimport.shop',
                'type' => 'select',
            ),
            'orderimport.shippingmethod' => array(
                'name' => 'orderimport.shippingmethod',
                'type' => 'selectwithtextoption',
                'subfields' => array(
                    'select' => array('name' => 'orderimport.shippingmethod', 'type' => 'select'),
                    'string' => array('name' => 'orderimport.shippingmethod.name', 'type' => 'string','default' => 'ebay',)
                ),
                'expert' => true,
            ),  
            'orderimport.paymentmethod' => array(
                'name' => 'orderimport.paymentmethod',
                'type' => 'selectwithtextoption',
                'subfields' => array(
                    'select' => array('name' => 'orderimport.paymentmethod', 'type' => 'select'),
                    'string' => array('name' => 'orderimport.paymentmethod.name', 'type' => 'string','default' => 'ebay',)
                ),
                'expert' => true,
            ),
        ),
    ),
    'mwst' => array(
        'fields' => array(
            array(
                'name' => 'mwstfallback',
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
    'orderupdate' => array(
        'fields' => array(
            array(
                'name' => 'updateableorderstatus',
                'type' => 'subFieldsContainer',
                'subfields' => array(
                    'updateableorderstatus' => array('name' => 'updateable.orderstatus', 'type' => 'multipleselect'),
                    'updateorderstatus' => array('name' => 'update.orderstatus', 'type' => 'bool','default'=>true),
                )
            ),
            'orderstatus.paid' => array(
                'name' => 'orderstatus.paid',
                'type' => 'select',
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
                'name' => 'orderstatus.carrier.default',
                'type' => 'select'
            ),
            array(
                'name' => 'orderstatus.cancelled',
                'type' => 'select'
            ),
        ),
    )
), false);


MLSetting::gi()->add('ebay_config_emailtemplate', array(
    'mail' => array(
        'fields' => array(
            array(
                'name' => 'mail.send',
                'type' => 'radio',
                'default' => 'false',
            ),
            array(
                'name' => 'mail.originator.name',
                'type' => 'string',
                'default' => '{#i18n:ebay_config_account_emailtemplate_sender#}',
            ),
            array(
                'name' => 'mail.originator.adress',
                'type' => 'string',
                'default' => '{#i18n:ebay_config_account_emailtemplate_sender_email#}',
            ),
            array(
                'name' => 'mail.subject',
                'type' => 'string',
                'default' => '{#i18n:ebay_config_account_emailtemplate_subject#}',
            ),
//            array(
//                'name' => 'mail.content',
//                'type' => 'wysiwyg',
//                'default' => '{#i18n:ebay_config_emailtemplate_content#}',
//                'resetdefault' => '{#i18n:ebay_config_emailtemplate_content#}',
//            ),
            array(
                'name' => 'mail.content',
                'type' => 'configMailContentContainer',
                'default' => '{#i18n:ebay_config_emailtemplate_content#}',
                'resetdefault' => '{#i18n:ebay_config_emailtemplate_content#}',
            ),
            array(
                'name' => 'mail.copy',
                'type' => 'radio',
                'default' => 'true',
            ),
        ),
    ),
), false);


MLSetting::gi()->add('ebay_config_producttemplate', array(
    'product' => array(
        'fields' => array(
             array(
                'name' => 'template.name',
                'type' => 'string',
                'default' => '#TITLE#',
            ),
             array(
                'name' => 'template.content',
                'default' => '{#i18n:ebay_config_producttemplate_content#}',
                'resetdefault' => '{#i18n:ebay_config_producttemplate_content#}',
                'type' => 'wysiwyg',
            ),
        ),
    ),
), false);

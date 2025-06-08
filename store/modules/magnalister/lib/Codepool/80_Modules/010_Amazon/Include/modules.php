<?php
    MLSetting::gi()->add('aModules', array(
    'amazon' => array(
        'title' => '{#i18n:sModuleNameAmazon#}',
        'logo' => 'amazon',
        'displayAlways' => true,
        'requiredConfigKeys' => array(
            'username',
            'password',
            'mwstoken',
            'lang',
            'internationalshipping',
            'mwstfallback',
            /*//{search: 1427198983}
            'mwst.shipping',
            //*/
            'quantity.type',
            'leadtimetoship',
            'price.addkind',
            'import',
            'orderstatus.open',
            'orderstatus.fba',
            'orderstatus.sync',
            'orderstatus.shipped',
            'orderstatus.carrier.default',
            'orderstatus.cancelled',
            'stocksync.tomarketplace',
            'stocksync.frommarketplace',
            'mail.send',
            'orderimport.shop',
            //'customergroup', /* gibt es nicht in osCommerce */
        ),
        'authKeys' => array(
            'username' => 'USERNAME', 
            'password' => 'PASSWORD',
            'mwstoken' => 'MWSToken',
            'merchantid' => 'MERCHANTID',
            'marketplaceid' => 'MARKETPLACE', 
            'site' => 'SITE', 
        ),
        'settings' => array(
            'defaultpage' => 'prepare',
            'subsystem' => 'Amazon',
            'currency' => '__depends__',
            'hasOrderImport' => true,
        ),
        'type' => 'marketplace',
    ),
));
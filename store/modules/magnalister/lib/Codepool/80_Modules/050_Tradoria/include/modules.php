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
MLSetting::gi()->add('aModules', array(
    'tradoria' => array(
        'title' => '{#i18n:sModuleNameTradoria#}',
        'logo' => 'rakuten',
        'type' => 'marketplace',
        'displayAlways' => false,
        'requiredConfigKeys' => array(
            'apikey',
            'mpusername',
            'mppassword',
            'lang',
            'quantity.type',
            'price.addkind',
            'price.factor',
            'stocksync.tomarketplace',
            'stocksync.frommarketplace',
            'inventorysync.price',
            'import',
            'preimport.start',
            'customergroup',
            'orderimport.shop',
            'orderstatus.open',
            'mwst.fallback',
            'orderstatus.sync',
            'orderstatus.shipped',
            'orderstatus.canceled',
        ),
        'authKeys' => array(
            'apikey' => 'KEY', 
            'mpusername' => 'MPUSERNAME', 
            'mppassword' => 'MPPASSWORD',
        ),
        'settings' => array(
            'defaultpage' => 'checkin',
            'subsystem' => 'Rakuten',
            'currency' => '__depends__',
            'hasOrderImport' => true,
        ),
    ),
));

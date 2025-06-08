<?php // 
$aForm = array(
    'general' => array(
        'fields' => array(
            'pass'=>array(
                'key' => 'general.passphrase',
                'type' => 'text',
                'verify' => 'notempty'
            ),
        ),
    ),
    'sku' => array(
        'fields' => array(
            'sku'=>array(
                'key' => 'general.keytype',
                'type' => 'radio',
                'inputCellStyle' => 'line-height: 1.5em;',
                'separator' => '<br/>',
                'default' => 'artNr'
            )
        )
    ),
    'stats' => array(
        'fields' => array(
            'back' => array(
                'key' => 'general.stats.backwards',
                'type' => 'selection',
                'default' => '5',
            ),
        ),
    ),
    'ftp' => array(
        'fields' => array(
            'host' => array(
                'key' => 'general.ftp.host',
                'type' => 'text',
                'cssClasses' => array('autoWidth'),
                'morefields' => array(
                    'port'=>array(
                        'key' => 'general.ftp.port',
                        'type' => 'text',
                        'verify' => 'int',
                        'default' => 21,
                        'cssClasses' => array('autoWidth'),
                    )
                )
            ),
            'login'=>array(
                'key' => 'general.ftp.username',
                'type' => 'text',
                'verify' => 'notempty'
            ),
            'pswd'=>array(
                'key' => 'general.ftp.password',
                'type' => 'password',
                'settings' => array(
                    'save' => true
                ),
            ),
        ),
    ),
    'orderimport' => array(
        'fields' => array(
            'timetable' => array(
                'key' => 'general.callback.importorders',
                'type' => 'checkbox',
                'inputCellStyle' => 'line-height: 1.5em;',
                'cssClasses' => array('smalltext'),
                'separator' => '<br/>',
                'separatormodulo' => 12,
                'default' => array(
                    '0' => true, '1' => true, '2' => true, '3' => true,
                    '4' => true, '5' => true, '6' => true, '7' => true,
                    '8' => true, '9' => true, '10' => true, '11' => true,
                    '12' => true, '13' => true, '14' => true, '15' => true,
                    '16' => true, '17' => true, '18' => true, '19' => true,
                    '20' => true, '21' => true, '22' => true, '23' => true,
                ),
            ),
            'orderinformation' => array(
                'type' => 'checkbox',
                'key' => 'general.order.information',
                'default' => array(
                    'val' => false
                )
            ),
        ),
    ),
    'cronTimeTable' => array(
        'fields' => array(
            /*
             *  this part is commented , because till now no customer in v3 need it and shopware has own configuration
            'cid' => array(
                'type' => 'selection',
                'expertsetting' => true,
                'key' => 'customers_cid.assignment',
                'default' => 'none',
            ),
             */
            'editor' => array(
                'key' => 'general.editor',
                'type' => 'radio',
                'expertsetting' => true,
                'inputCellStyle' => 'line-height: 1.5em;',
                'separator' => '<br/>',
                'default' => 'tinyMCE',
            ),
            'stocksyncbyorder' => array(
                'key' => 'general.trigger.checkoutprocess.inventoryupdate',
                'type' => 'checkbox',
                'expertsetting' => true,
                'default' => array(
                    'val' => true,
                ),
            ),
        ),
    ),
    'articleStatusInventory' => array(
        'fields' => array(
            'statusIsZero' => array(
                'key' => 'general.inventar.productstatus',
                'type' => 'radio',
                'default' => 'false',
            )
        )
    ),
    'productfields' => array(
            'fields' => array(
                'manufacturer' => array(
                    'key' => 'general.manufacturer',
                    'type' => 'selection',
                    'values'=>'Shop::getManufacturer',
//                    'expertsetting' => true,
                ),
                'mfnpartno' => array(
                    'key' => 'general.manufacturerpartnumber',
                    'type' => 'selection',
                    'values'=>'Shop::getManufacturerPartNumber',
//                    'expertsetting' => true,
                ),
                'EAN' => array(
                    'key' => 'general.ean',
                    'type' => 'selection',
                    'values'=>'Shop::getEan',
//                    'expertsetting' => true,
                ),
            ),
        ),
);
//if (!MLSetting::gi()->get('blSaveMode')) {
//    unset($aForm['ftp']);
//}
MLSetting::gi()->set('aGeneralForm', $aForm);
<?php
MLSetting::gi()->add('aButtons', array(
    array(
        'title' => 'ML_LABEL_IMPORT_ORDERS',
        'icon' => 'cart',
        'link' => array('do' => 'ImportOrders'),
        'type' => 'cron',
        'enabled' => true,
    ),
    array(
        'title' => 'ML_LABEL_SYNC_ORDERSTATUS',
        'icon' => 'upload',
        'link' => array('do' => 'SyncOrderStatus'),
        'type' => 'cron',
        'enabled' => true,
    ),
    array(
        'title' => 'ML_LABEL_SYNC_INVENTORY',
        'icon' => 'sync',
        'link' => array('do' => 'SyncInventory'),
        'type' => 'cron',
        'enabled' => true,
    ),
    array(
        'title' => 'ML_LABEL_UPDATE',
        'icon' => 'update',
        'link' => array('do' => 'update', 'method' => 'update'),
        'enabled' => true,
    ),
));
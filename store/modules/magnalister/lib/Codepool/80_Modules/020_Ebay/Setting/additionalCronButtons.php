<?php

$aButtons = MLSetting::gi()->get('aButtons');
$aMyButtons = array();
foreach ($aButtons as $aButton) {
    $aMyButtons[] = $aButton;
    if ($aButton['link'] == array('do' => 'SyncInventory')) {
        $aMyButtons[] = array(
            'title' => 'sEbayProductIdentifierSyncButton',
            'icon' => 'sync',
            'link' => array('do' => 'SyncProductIdentifiers'),
            'type' => 'cron',
            'id'=> 'cron_sync_product_identifiers',
            'enabled' => MLShop::gi()->addonBooked('EbayProductIdentifierSync'),
            'disablemessage' => MLI18n::gi()->get('sEbaySyncButtonDisableIfno'),
            'mpFilter' => 'ebay' // only ebay
        );
    }
}
MLSetting::gi()->set('aButtons', $aMyButtons, true);
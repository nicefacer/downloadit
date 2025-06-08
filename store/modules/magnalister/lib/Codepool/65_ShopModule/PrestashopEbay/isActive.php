<?php
global $magnaConfig;
return (
        isset($magnaConfig['maranon']['Marketplaces'][MLRequest::gi()->data('mp')]) && $magnaConfig['maranon']['Marketplaces'][MLRequest::gi()->data('mp')]=='ebay' //ebay module activation
        &&
        defined('_PS_VERSION_') //prestashop shop activation
)?true:false;
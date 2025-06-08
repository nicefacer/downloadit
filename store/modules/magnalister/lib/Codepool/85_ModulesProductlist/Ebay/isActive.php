<?php
global $magnaConfig;
return (
    (
        isset($magnaConfig['maranon']['Marketplaces'][MLRequest::gi()->data('mp')])
        &&
        $magnaConfig['maranon']['Marketplaces'][MLRequest::gi()->data('mp')]=='ebay'
    )
    || MLRequest::gi()->data('mp')=='ebay'
)?true:false; 
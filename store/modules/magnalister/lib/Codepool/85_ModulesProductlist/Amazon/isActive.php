<?php
global $magnaConfig;
return (
    (
        isset($magnaConfig['maranon']['Marketplaces'][MLRequest::gi()->data('mp')])
        &&
        $magnaConfig['maranon']['Marketplaces'][MLRequest::gi()->data('mp')]=='amazon'
    )
    || MLRequest::gi()->data('mp')=='amazon'
)?true:false; 
<?php

global $magnaConfig;
return (
    isset($magnaConfig['maranon']['Marketplaces'][MLRequest::gi()->data('mp')]) && $magnaConfig['maranon']['Marketplaces'][MLRequest::gi()->data('mp')] == 'check24' //check24 module activation
    &&
    (class_exists('Enlight_Application', false) && Enlight_Application::Instance()->App() === 'Shopware') //showpare shop activation
    );

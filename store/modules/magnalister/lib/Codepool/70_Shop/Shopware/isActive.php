<?php
    return ((class_exists('Enlight_Application',false) &&  Enlight_Application::Instance()->App() == 'Shopware') ? true : false);

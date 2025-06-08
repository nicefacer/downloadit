<?php

if(Module::isEnabled('revsliderprestashop') == 1 &&
    Module::isInstalled('revsliderprestashop') == 1 &&
    file_exists(_PS_MODULE_DIR_.'revsliderprestashop/revprestashoploader.php')){            
    SmartShortCode::add_shortcode('rev_slider',array('RevsliderPrestashop','rev_slider_shortcode'));
}
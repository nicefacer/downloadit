<?php

SmartShortCode::add_shortcode('sds_manufacturers','sds_manufacturers_cb');
    function sds_manufacturers_cb($atts = array(), $content){
        extract(SmartShortCode::shortcode_atts(
            array(
                'speed'=>'600',
                'maxslide'=>'6'
            ),$atts
        ));
        $context = Context::getContext(); 
        $manufacturers = Manufacturer::getManufacturers(false,$context->language->id, true);
        $context->smarty->assign(
            array(
                'manufacturers' => $manufacturers,
                'speed' => $speed,
                'maxslide' => $maxslide
            )
        );
        $output = $context->smarty->fetch(_PS_MODULE_DIR_.'smartshortcode/smartmanufacturer.tpl');

        return $output;
        
    }
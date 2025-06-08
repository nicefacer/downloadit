<?php
function sds_button_cb($atts, $content = null, $tag, $hook_name) {
        extract(SmartShortCode::shortcode_atts(array(
            'class' => '',
            'href' => '',
            'target' => '_self',
        ), $atts));
        return '<a target="'.$target.'" class="'.$class.'" href="'.$href.'">'.SmartShortCode::do_shortcode($content,$hook_name).'</a>';
        
    }
    SmartShortCode::add_shortcode('button', 'sds_button_cb');
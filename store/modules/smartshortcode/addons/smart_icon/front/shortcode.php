<?php
    function smartshortcode_smart_icon($atts, $content = null, $tag, $hook_name) {
        extract(SmartShortCode::shortcode_atts(array(
                    'name' => '',
                    'class'=>'',
                    'size'=>'',
                    'style'=>''
                        ), $atts));
        return '<i class="'.$name.' '.$class.' '.$size.'" style='.$style.' >' . SmartShortCode::do_shortcode($content,$hook_name) . '
    </i>';
    }

    SmartShortCode::add_shortcode('smart_icon', 'smartshortcode_smart_icon');
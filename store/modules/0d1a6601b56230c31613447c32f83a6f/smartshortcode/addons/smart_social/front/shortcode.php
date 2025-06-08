<?php
function smartshortcode_smart_social($atts, $content = null, $tag, $hook_name) {
        extract(SmartShortCode::shortcode_atts(array(
                    'name' => '',
                    'link' => '',
                    'title' => '',
                    'class'=>'',
                    'size'=>'',                
        ), $atts));
        $html = $before = $after = '';
        if(!empty($link)){
            $before = "<a class='sds_social' href='{$link}'>";
            $after = "{$title}</a>";
        }
        $html .= '<i class="'.$name.' '.$class.' '.$size.'" ></i>';
        return $before.$html.$after;
    }

    SmartShortCode::add_shortcode('smart_social', 'smartshortcode_smart_social');

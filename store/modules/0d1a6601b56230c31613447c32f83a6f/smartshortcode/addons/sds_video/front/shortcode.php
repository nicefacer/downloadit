<?php

    function sds_video_cb($atts = array(),$content = null, $tag, $hook_name){
        extract(SmartShortCode::shortcode_atts(array(
            'src' => '',
            'width' => '',
            'height' => '',        
        ), $atts));
        
        return "<iframe src='{$src}' width='{$width}' height='{$height}'></iframe>";
        
    }

    SmartShortCode::add_shortcode('sds_video', 'sds_video_cb');

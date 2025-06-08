<?php
  function sds_alert_box($atts, $content = null, $tag, $hook_name) {
        extract(SmartShortCode::shortcode_atts(array(
            'class' => ''
        ), $atts));
        return '<div class="alert_box fix"><div class="'.$class.'"><p>'.SmartShortCode::do_shortcode($content,$hook_name).'</p><button class="close" type="button" data-dismiss="alert"><i class="icon-remove"></i></button></div></div>';
        
    }
    SmartShortCode::add_shortcode('alert_box', 'sds_alert_box');
<?php
    
 function sds_grid_cb($atts = array(),$content = null, $tag, $hook_name){
        extract(SmartShortCode::shortcode_atts(array(
            'class' => 'col-lg-1 col-md-1 col-sm-1 col-xs-1',
            
        ), $atts));
        
        return "<div class='{$class}'>".SmartShortCode::do_shortcode($content,$hook_name)."</div>";
        
    }
    SmartShortCode::add_shortcode('grid', 'sds_grid_cb');
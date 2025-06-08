<?php
    function testimonial_cb($atts, $content = null, $tag, $hook_name) {
        extract(SmartShortCode::shortcode_atts(array(
            'class' => '',
            'author' => '',
            'style' => '0',
            'image_src' => '',
        ), $atts));
        
        
        switch($style):

            case 0:

            case 1:
                return '<div class="'.$class.'">'.SmartShortCode::do_shortcode($content,$hook_name).'<br><br><span>'.$author.'</span></div>';
                break;

            case 2:
                $markup = '<div class="'.$class.'">';
                
                if(!empty($image_src))
                      $markup .= '<img class="img-responsive" alt="" src="'.$image_src.'" />';
                
                $markup .= '<div class="testimonial_3_body_text">'.SmartShortCode::do_shortcode($content,$hook_name).'<br><br><span>'.$author.'</span></div></div>';
                
                return $markup;
                break;

            case 3:
                
                $markup = '<div class="'.$class.'"><div class="popover top"><div class="arrow"></div><div class="popover-content">'.SmartShortCode::do_shortcode($content,$hook_name).'</div>
    </div>';
                if(!empty($image_src))
                    $markup .= '<img alt="client" src="'.$image_src.'" />';
                
                $markup .= '<br><br><span>'.$author.'</span></div>';
                
                return $markup;            
                break;


        endswitch;
                        
        
        
    }
    SmartShortCode::add_shortcode('testimonial', 'testimonial_cb');
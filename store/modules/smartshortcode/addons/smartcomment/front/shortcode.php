<?php

function sds_smartcomment_cb($atts = array(),$content=null,$tag,$hook_name){
    if(!class_exists('Blogcomment')) return false;
    extract(SmartShortCode::shortcode_atts(array(          
        'limit' => '5',                      
      ), $atts));
    $context = Context::getContext();
    $comments = SmartShortCode::getLatestComments($context->language->id,$limit);
    $context->smarty->assign(
        array(
            'latesComments' => $comments,            
        )
    );
    $template_file_name = getTemplatePath('smartbloglatestcomments.tpl');
    $html = $context->smarty->fetch($template_file_name);
    return $html;
}  
SmartShortCode::add_shortcode('smartcomment', 'sds_smartcomment_cb');
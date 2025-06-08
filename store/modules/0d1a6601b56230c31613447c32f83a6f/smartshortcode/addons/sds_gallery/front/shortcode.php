<?php

    function sds_gallery_cb($atts = array(),$content = null, $tag, $hook_name){
    extract(SmartShortCode::shortcode_atts(array(
        'src' => '',
        'width' => '',
        'height' => '',
        'title' => '',
        'relation' => '',
    ), $atts));
    $wrs = $wre = $html = '';
    if($relation == 'false'){
        $wrs .= "<a class='sds_image_gallery_single' href='{$src}'>";
        $wre .= '</a>';
    }else{
        $wrs .= "<a href='{$src}'>";
        $wre .= '</a>';
    }
    $html .= "<img src='{$src}' ";
    if(!empty($width) && is_numeric($width))
        $html .= "width='{$width}' ";
    if(!empty($height) && is_numeric($height))
        $html .= "height='{$height}' ";
    $html .= "alt='{$title}' />";
    
    if(!empty($title)){
        $html .= "<span class='gallery_title'>{$title}</span>";
    }
    
    return $wrs.$html.$wre;
    
}

SmartShortCode::add_shortcode('sds_gallery', 'sds_gallery_cb');
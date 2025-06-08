<?php

function sds_smartblog_cb($atts = array(),$content = null, $tag, $hook_name)
{
    if(!class_exists('SmartBlogPost')) return false;
    extract(SmartShortCode::shortcode_atts(array(
      'blog_cat' => '',
      'per_page' => '12',
      'pcats' => '',
      'products' => '',
      'title' => '',            
    ), $atts));
    $result = array();
    $context = Context::getContext();
    $pcatsarr = explode(',',$pcats);
    $productsarr = explode(',',$products);
    $html = '';
    if(!empty($pcats) && $context->controller->php_self == 'category' && !in_array(Tools::getvalue('id_category'),$pcatsarr))
            return false;        
    elseif(!empty($products) && $context->controller->php_self == 'product' && !in_array(Tools::getvalue('id_product'),$productsarr))
            return false;

    if(!empty($blog_cat) && is_numeric($blog_cat)){
        $posts = BlogPostCategory::getToltalByCategory($context->language->id,$blog_cat,0,$per_page);
    }else{
        $posts = SmartBlogPost::getAllPost($context->language->id,0,$per_page);     
    }
    //start manupulate data for tpl
    $i = 0;
    if(!empty($posts))
        foreach($posts as $post){
            $result[$i]['id'] = $post['id_post'];
            $result[$i]['id_smart_blog_post'] = $post['id_post'];
            $result[$i]['id_post'] = $post['id_post'];
            if(!empty($post['is_featured'])){
                $result[$i]['is_featured'] = $post['is_featured'];
            }else{
                $result[$i]['is_featured'] = '';
            }
            if(!empty($post['cat_name'])){
                $result[$i]['cat_name'] = $post['cat_name'];
            }else{
                $result[$i]['cat_name'] = '';
            }
            $result[$i]['viewed'] = $post['viewed'];
            $result[$i]['title'] = $post['meta_title'];
            $result[$i]['meta_title'] = $post['meta_title'];
            $result[$i]['meta_description'] = $post['meta_description'];
            $result[$i]['short_description'] = $post['short_description'];
            $result[$i]['content'] = $post['content'];
            $result[$i]['meta_keyword'] = $post['meta_keyword'];
            $result[$i]['id_category'] = $post['id_category']; 
            $result[$i]['link_rewrite'] = $post['link_rewrite'];
            $result[$i]['cat_link_rewrite'] = $post['cat_link_rewrite'];
            $result[$i]['lastname'] = $post['lastname'];
            $result[$i]['firstname'] = $post['firstname'];
            $result[$i]['post_img'] = $post['post_img'];
            $result[$i]['created'] = $post['created'];
            $result[$i]['date_added'] = $post['created'];
            $i++;
        }
        //end manupulate data for tpl
    if($hook_name != 'displayLeftColumn' && $hook_name != 'displayRightColumn' && $hook_name != 'displayFooter'){
        
        $context->smarty->assign(
            array(
                'view_data' => $result,                                          
                'sds_title' => $title
            )
        );
        $template_file_name = getTemplatePath('smartblog_latest_news.tpl');
        $html = $context->smarty->fetch($template_file_name);
    }else{
        $context->smarty->assign(
            array(
                'posts' => $result,                                          
                'sds_title' => $title
            )
        );
        $template_file_name = getTemplatePath('smartblogrecentposts.tpl');
        $html = $context->smarty->fetch($template_file_name);
    }
    return $html;
}
SmartShortCode::add_shortcode('smartblog', 'sds_smartblog_cb');
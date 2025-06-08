<?php

 function shortcode_category_products ($atts, $content = null, $tag, $hook_name) {
        
      extract(SmartShortCode::shortcode_atts(array(
          'id_category' => '1',
          'per_page' => '12',
          'orderby' => 'position',
          'order' => 'DESC',
        ), $atts));
        
      $context = Context::getContext(); 
        $ssc = new SmartShortCode();
      $out_put = '';
        $cache_products = SmartShortCode::getProductsByCategoryID($id_category,$per_page, $context->language->id, null, false, $orderby ,$order);          
        if(is_array($cache_products) && !empty($cache_products)){
                if($hook_name != 'displayLeftColumn' && $hook_name != 'displayRightColumn' && $hook_name != 'displayFooter'){
                    $context->smarty->assign(
                    array(
                         'new_products' => $cache_products,
                         'sds_hook' => $hook_name,                            
                         'sds_title' => $ssc->l('New products')
                       )
                  );
          $template_file_name = getTemplatePath('blocknewproducts.tpl');
          $out_put = $context->smarty->fetch($template_file_name);
                }else{
                    $thecats = Category::getCategoryInformations(array($id_category));
                    $sds_title = "";
                    if(isset($thecats[(int)$id_category])){
                        $sds_title .= $thecats[(int)$id_category]['name'].' ';
                    }
                    $sds_title .= $ssc->l('products');
                    $context->smarty->assign(
                        array(
                            'productsViewedObj' => $cache_products,
                            'sds_hook' => $hook_name,                            
                            'sds_title' => $sds_title
                        )
                    );
                    $template_file_name = getTemplatePath('blockviewed.tpl');
          $out_put = $context->smarty->fetch($template_file_name);
                }
        }else{
            $out_put = $ssc->l('No products at this Category.');
        }
          return  $out_put ;
    }
    SmartShortCode::add_shortcode('category_products', 'shortcode_category_products');

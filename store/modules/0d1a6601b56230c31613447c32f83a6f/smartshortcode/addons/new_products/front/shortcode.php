<?php
    function shortcode_new_products ($atts, $content = null, $tag, $hook_name) {
      extract(SmartShortCode::shortcode_atts(array(
          'per_page' => '12',
          'orderby' => 'position',
          'order' => 'DESC',      
          ), $atts));
      $context = Context::getContext();
        $ssc = new SmartShortCode();   
        if (!Configuration::get('NEW_PRODUCTS_NBR'))
                    return;
               
        $cache_products = Product::getNewProducts((int) Context::getContext()->language->id, 0, $per_page, false, $orderby, $order);
         if(!empty($cache_products)){
               if($hook_name != 'displayLeftColumn' && $hook_name != 'displayRightColumn' && $hook_name != 'displayFooter'){
                 $context->smarty->assign(
                     array(
                         'new_products' => $cache_products,
                         'sds_hook' => $hook_name,                            
                         'sds_title' => $ssc->l('New products')
                     )
                 );
                $template_file_name = getTemplatePath('blocknewproducts.tpl') ; 
        $out_put = $context->smarty->fetch($template_file_name);
                 return   $out_put;
               }else{
                   $context->smarty->assign(
                     array(
                         'productsViewedObj' => $cache_products,
                         'sds_hook' => $hook_name,                            
                         'sds_title' => $ssc->l('New products')
                     )
                 );
        $template_file_name = getTemplatePath('blockviewed.tpl'); 
        $out_put = $context->smarty->fetch($template_file_name);
        return   $out_put;
               }
        }else{
            $out_put = $ssc->l('No new product is available at this time');
        }
        return $out_put ;
    }

//   function getTemplatePath($template='',$module_name ='smartshortcode'){


//   if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/'.$module_name.'/'.$template))
//    return _PS_THEME_DIR_.'modules/'.$module_name.'/'.$template;
//   elseif (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/'.$module_name.'/views/templates/front/'.$template))
//    return _PS_THEME_DIR_.'modules/'.$module_name.'/views/templates/front/'.$template;
//   elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.$module_name.'/views/templates/front/'.$template))
//    return _PS_MODULE_DIR_.$module_name.'/views/templates/front/'.$template;

//   return false;

// }
    SmartShortCode::add_shortcode('new_products', 'shortcode_new_products');
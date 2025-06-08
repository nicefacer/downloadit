<?php
 function shortcode_specials_products ($atts, $content = null, $tag, $hook_name) {
      extract(SmartShortCode::shortcode_atts(array(
          'per_page' => '12',
          'orderby' => 'position',
          'order' => 'DESC',
        ), $atts));
       $out_put = '';
       $cache_products = '';
      $context = Context::getContext();

      if (!($cache_products = Product::getPricesDrop((int) Context::getContext()->language->id, 0, $per_page ,  false, $orderby, $order)))
            return;

           $ssc = new SmartShortCode();
        if(!empty($cache_products)){
         
        $link = new Link(); 
                
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
                   return $out_put;
                }else{
                    $context->smarty->assign(
                        array(
                            'specials' => $cache_products,
                            'sds_hook' => $hook_name
                        )
                    );
                    $template_file_name = getTemplatePath('blockspecials.tpl'); 
          $out_put = $context->smarty->fetch($template_file_name);
                }
        }else{
            $out_put = $ssc->l('No specials products at this time.');
        }
             
        return  $out_put;

    }

    SmartShortCode::add_shortcode('specials_products', 'shortcode_specials_products');
<?php

 function shortcode_bestsellers_products ($atts, $content = null, $tag, $hook_name) {
      extract(SmartShortCode::shortcode_atts(array(
          'per_page' => '12',
          'orderby' => null,
          'order' => 'DESC',
          ), $atts));
                
        $ssc = new SmartShortCode();
        $context = Context::getContext();

        $cache_products = ProductSaleCore::getBestSales((int) Context::getContext()->language->id, 0, $per_page, $orderby, $order);

        if(!empty($cache_products)){
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
                $context->smarty->assign(
                     array(
                         'productsViewedObj' => $cache_products,
                         'sds_hook' => $hook_name,                            
                         'sds_title' => $ssc->l('Bestseller products')
                     )
                 );
                $template_file_name = getTemplatePath('blockviewed.tpl');
                $out_put = $context->smarty->fetch($template_file_name);
            } 
        }else{
            $out_put = $ssc->l('No BestSellers products at this time');
        }
             
        return  $out_put;

    }

    SmartShortCode::add_shortcode('bestsellers_products', 'shortcode_bestsellers_products');

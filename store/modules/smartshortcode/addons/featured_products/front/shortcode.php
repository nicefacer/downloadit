<?php

function shortcode_featured_products ($atts, $content = null, $tag, $hook_name) {
      extract(SmartShortCode::shortcode_atts(array(
          'per_page' => '12',
          'orderby' => 'position',
          'order' => 'DESC',
          'random' => 'false',
        ), $atts));
        
      $context = Context::getContext();

        $category = new Category($context->shop->getCategory(), (int)$context->language->id);

        $cache_products = $category->getProducts((int)$context->language->id, 1, $per_page, $orderby,$order, false, true, (bool)$random, $per_page);


        $ssc = new SmartShortCode();
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
                            'sds_title' => $ssc->l('Featured products')
                        )
                    );
                    $template_file_name = getTemplatePath('blockviewed.tpl');
                  $out_put = $context->smarty->fetch($template_file_name);
               }
        }else{
            $out_put = $ssc->l('No featured products at this time');
        }
        
        return $out_put ;

    }

    SmartShortCode::add_shortcode('featured_products', 'shortcode_featured_products');
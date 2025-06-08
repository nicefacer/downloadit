<?php
$out_put_file =  '    
        
    <li class="ajax_block_product col-xs-12 col-sm-4 col-md-3  first-item-of-tablet-line first-item-of-mobile-line">
        <div class="product-container" itemscope="" itemtype="http://schema.org/Product">
            <div class="left-block">
                <div class="product-image-container">
                    <a class="product_img_link" href="'.$product["link"].'" title="'.$product["name"].'" itemprop="url">
                    <img class="replace-2x img-responsive" src="'.$image_url .'" alt="'.$product["link"].'" 
                    title="'.$product["link"].'" width="250" height="250" itemprop="image">
                        </a>
                    <a class="quick-view" href="'.$product["link"].'"  rel="'.$product["link"].'">
                    <span>Quick view</span>
                    </a>
                <div class="content_price" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
                    <span itemprop="price" class="price product-price">
                         '.Tools::displayPrice($product['price_tax_exc']).'</span>
                        <meta itemprop="priceCurrency" content="1">
                </div>
                    <span class="new-box">
                        <span class="new-label">New</span>
                        </span>
                </div>
                </div>
                <div class="right-block">
                    <h5 itemprop="name">
                    <a class="product-name" href="'.$product["link"].'" title="'.$product["link"].'" itemprop="url">
                         root'.$product["name"].'
                    </a>
                    </h5>
                     

                <div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer" class="content_price">
                    <span itemprop="price" class="price product-price">
                        '.Tools::displayPrice($product["price_tax_exc"] ).'</span>
                        <meta itemprop="priceCurrency" content="1">
                        </div>
                    <div class="button-container">
                    <a class="button ajax_add_to_cart_button sds-btn sds-btn-default" 
                    href="http://localhost/ps-module/smart-short-code/1.6.0.6/cart?add=1&amp;id_product=4&amp;token=bcbc7d0659ba015f695d68094b62aa67"
                    rel="nofollow" title="Add to cart" data-id-product="4">
                    <span>Add to cart</span>
                    </a>
                                                        
                    <a itemprop="url" class="button lnk_view sds-btn sds-btn-default" href="'.$product["link"].'" title="View">
                    <span>More</span>
                    </a>
                    </div>
                    <div class="product-flags">
                     </div>
                    <span itemprop="offers" itemscope="" itemtype="http://schema.org/Offer" class="availability">
                        <span class="available-now">
                        <link itemprop="availability" href="http://schema.org/InStock">'.$product['available_now'].'</span>
                        </span>
                 </div>
            </div><!-- .product-container> -->
        </li>';
		
?>
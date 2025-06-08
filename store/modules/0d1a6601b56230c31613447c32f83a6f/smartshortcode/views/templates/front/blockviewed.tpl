{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Block Viewed products -->

{if $sds_hook == 'displayFooter'}
    
<div class="block footer-block col-xs-12 col-md-3">
	<h4>{$sds_title}</h4>
	<div class="block_content products-block">
            {if $productsViewedObj}
		<ul>
			{foreach from=$productsViewedObj item=viewedProduct name=myLoop}
				<li class="clearfix{if $smarty.foreach.myLoop.last} last_item{elseif $smarty.foreach.myLoop.first} first_item{else} item{/if}">
					<a
					class="products-block-image" 
					href="{$link->getProductLink($viewedProduct.id_product)|escape:'html':'UTF-8'}" 
					title="{l s='More about %s' mod='blockviewed' sprintf=[$viewedProduct.name|escape:'html':'UTF-8']}" >
						<img 
						src="{if isset($viewedProduct.id_image) && $viewedProduct.id_image}{$link->getImageLink($viewedProduct.link_rewrite, $viewedProduct.id_image, 'small_default')}{else}{$img_prod_dir}{$lang_iso}-default-medium_default.jpg{/if}" 
						alt="{$viewedProduct.legend|escape:'html':'UTF-8'}" />
					</a>
					<div class="product-content">
						<h5>
							<a class="product-name" 
							href="{$link->getProductLink($viewedProduct.id_product)|escape:'html':'UTF-8'}" 
							title="{l s='More about %s' mod='blockviewed' sprintf=[$viewedProduct.name|escape:'html':'UTF-8']}">
								{$viewedProduct.name|truncate:25:'...'|escape:'html':'UTF-8'}
							</a>
						</h5>
						<p class="product-description">{$viewedProduct.description_short|strip_tags:'UTF-8'|truncate:40}</p>
                                                <div class="price-box">
                                                    {if !$PS_CATALOG_MODE}
                                                        <span class="price special-price">
                                                            {if !$priceDisplay}
                                                                {displayWtPrice p=$viewedProduct.price}
                                                            {else}
                                                                {displayWtPrice p=$viewedProduct.price_tax_exc}
                                                            {/if}
                                                        </span>
                                                         {if $viewedProduct.specific_prices}
                                                            {assign var='specific_prices' value=$viewedProduct.specific_prices}
                                                            {if $specific_prices.reduction_type == 'percentage' && ($specific_prices.from == $specific_prices.to OR ($smarty.now|date_format:'%Y-%m-%d %H:%M:%S' <= $specific_prices.to && $smarty.now|date_format:'%Y-%m-%d %H:%M:%S' >= $specific_prices.from))}
                                                                <span class="price-percent-reduction">-{$specific_prices.reduction*100|floatval}%</span>
                                                            {/if}
                                                        {/if}
                                                         
                                                    {/if}
                                                </div>
					</div>
				</li>
			{/foreach}
		</ul>
            {/if}
	</div>
</div>
{elseif $sds_hook == 'displayLeftColumn' || $sds_hook == 'displayRightColumn'}    
<div class="block">
	<p class="title_block">{$sds_title}</p>
	<div class="block_content products-block">
            
            {if $productsViewedObj}
		<ul>
			{foreach from=$productsViewedObj item=viewedProduct name=myLoop}
                            
				<li class="clearfix{if $smarty.foreach.myLoop.last} last_item{elseif $smarty.foreach.myLoop.first} first_item{else} item{/if}">
					<a
					class="products-block-image" 
					href="{$link->getProductLink($viewedProduct.id_product)|escape:'html':'UTF-8'}" 
					title="{l s='More about %s' mod='blockviewed' sprintf=[$viewedProduct.name|escape:'html':'UTF-8']}" >
						<img 
						src="{if isset($viewedProduct.id_image) && $viewedProduct.id_image}{$link->getImageLink($viewedProduct.link_rewrite, $viewedProduct.id_image, 'small_default')}{else}{$img_prod_dir}{$lang_iso}-default-medium_default.jpg{/if}" 
						alt="{$viewedProduct.legend|escape:'html':'UTF-8'}" />
					</a>
					<div class="product-content">
						<h5>
							<a class="product-name" 
							href="{$link->getProductLink($viewedProduct.id_product)|escape:'html':'UTF-8'}" 
							title="{l s='More about %s' mod='blockviewed' sprintf=[$viewedProduct.name|escape:'html':'UTF-8']}">
								{$viewedProduct.name|truncate:25:'...'|escape:'html':'UTF-8'}
							</a>
						</h5>
						<p class="product-description">{$viewedProduct.description_short|strip_tags:'UTF-8'|truncate:40}</p>
                                                <div class="price-box">
                                                    {if !$PS_CATALOG_MODE}
                                                        <span class="price special-price">
                                                            {if !$priceDisplay}
                                                                {displayWtPrice p=$viewedProduct.price}
                                                            {else}
                                                                {displayWtPrice p=$viewedProduct.price_tax_exc}
                                                            {/if}
                                                        </span>
                                                         {if $viewedProduct.specific_prices}
                                                            {assign var='specific_prices' value=$viewedProduct.specific_prices}
                                                            {if $specific_prices.reduction_type == 'percentage' && ($specific_prices.from == $specific_prices.to OR ($smarty.now|date_format:'%Y-%m-%d %H:%M:%S' <= $specific_prices.to && $smarty.now|date_format:'%Y-%m-%d %H:%M:%S' >= $specific_prices.from))}
                                                                <span class="price-percent-reduction">-{$specific_prices.reduction*100|floatval}%</span>
                                                            {/if}
                                                        {/if}
                                                         
                                                    {/if}
                                                </div>
					</div>
				</li>
			{/foreach}
		</ul>
            {/if}
	</div>
</div>
{else}
    
    {if $productsViewedObj}
        
        {assign var='lastitempc' value=''}
        {assign var='lastitemtablate' value=''}
        {assign var='lastitemmobile' value=''}
        {assign var='firstitempc' value='first-in-line'}
        {assign var='firstitemtablate' value='first-item-of-tablet-line'}
        {assign var='firstitemmobile' value='first-item-of-mobile-line'}
        {assign var='ij' value=1}
        {assign var='sdsstatic_token' value=Tools::getToken(false)}
        

        <ul class="smart_shortcode product_list grid row blocknewproducts tab-pane active">
            {foreach from=$productsViewedObj item=viewedProduct name=myLoop}
                
                {if ($ij%4)==0}
                    {$lastitempc='last-in-line'}
                {/if}
                {if ($ij%3)==0}
                    {$lastitemtablate='last-item-of-tablet-line'}
                {/if}
                {if ($ij%2)==0}
                    {$lastitemmobile='last-item-of-mobile-line'}
                {/if}
                {if ($ij%5)==0}
                    {$firstitempc='first-in-line'}
                {/if}
                {if ($ij%4)==0}
                    {$firstitemtablate='first-item-of-tablet-line'}
                {/if}
                {if ($ij%3)==0}
                    {$firstitemmobile='first-item-of-mobile-line'}
                {/if}

                <li class="ajax_block_product col-xs-12 col-sm-4 col-md-3 {$lastitempc} {$lastitemtablate} {$lastitemmobile} {$firstitempc} {$firstitemtablate} {$firstitemmobile}">
                    <div class="product-container" itemscope="" itemtype="http://schema.org/Product">
                        <div class="left-block">
                            <div class="product-image-container">
                                <a class="product_img_link" href="{$link->getProductLink($viewedProduct.id_product)|escape:'html':'UTF-8'}" title="{l s='More about %s' mod='smartshortcode' sprintf=[$viewedProduct.name|escape:'html':'UTF-8']}" itemprop="url">
                                    <img class="replace-2x img-responsive" src="{if isset($viewedProduct.id_image) && $viewedProduct.id_image}{$link->getImageLink($viewedProduct.link_rewrite, $viewedProduct.id_image, 'home_default')}{else}{$img_prod_dir}{$lang_iso}-default-medium_default.jpg{/if}" alt="{$viewedProduct.legend|escape:'html':'UTF-8'}" width="250" height="250" itemprop="image">
                                </a>
                                <a class="quick-view" href="{$link->getProductLink($viewedProduct.id_product)|escape:'html':'UTF-8'}"  rel="{$link->getProductLink($viewedProduct.id_product)|escape:'html':'UTF-8'}">
                                    <span>{l s='Quick view' mod='smartshortcode'}</span>
                                </a>
                                <div class="content_price" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
                                    <span itemprop="price" class="price product-price">
                                    {if !$PS_CATALOG_MODE}                                        
                                        {if !$priceDisplay}
                                            {displayWtPrice p=$viewedProduct.price}
                                        {else}
                                            {displayWtPrice p=$viewedProduct.price_tax_exc}
                                        {/if}                                        
                                    {/if}
                                    </span>
                                    <meta itemprop="priceCurrency" content="1">
                                </div>
                                {if $viewedProduct.new}
                                <span class="new-box">
                                    <span class="new-label">{l s='NEW' mod='smartshortcode'}</span>
                                </span>
                                {/if}
                            </div>
                        </div>
                        <div class="right-block">
                            <h5 itemprop="name">
                                <a class="product-name" href="{$link->getProductLink($viewedProduct.id_product)|escape:'html':'UTF-8'}" title="{$viewedProduct.name|escape:'html':'UTF-8'}" itemprop="url">
                                     {$viewedProduct.name|escape:'html':'UTF-8'}
                                </a>
                            </h5>
                            <div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer" class="content_price">
                                <span itemprop="price" class="price product-price">
                                  {if !$PS_CATALOG_MODE}                                        
                                          {if !$priceDisplay}
                                              {displayWtPrice p=$viewedProduct.price}
                                          {else}
                                              {displayWtPrice p=$viewedProduct.price_tax_exc}
                                          {/if}                                        
                                      {/if}
                                </span>
                                <meta itemprop="priceCurrency" content="1">
                            </div>
                            <div class="button-container">
                                <a class="button ajax_add_to_cart_button sds-btn sds-btn-default" href="{$link->getPageLink("cart")}?add=1&amp;id_product={$viewedProduct.id_product}&amp;token={$sdsstatic_token}" rel="nofollow" title="{l s='Add to cart' mod='smartshortcode'}" data-id-product="{$viewedProduct.id_product}">
                                    <span>{l s='Add to cart' mod='smartshortcode'}</span>
                                </a>

                                <a itemprop="url" class="button lnk_view sds-btn sds-btn-default" href="{$link->getProductLink($viewedProduct.id_product)|escape:'html':'UTF-8'}" title="{l s='View' mod='smartshortcode'}">
                                    <span>{l s='More' mod='smartshortcode'}</span>
                                </a>
                            </div>
                            <div class="product-flags">
                            </div>
                        </div>
                    </div>
                </li> 

                {$ij=$ij+1}
                {$lastitempc=''}
                {$lastitemtablate=''}
                {$lastitemmobile=''}
                {$firstitempc=''}
                {$firstitemtablate=''}
                {$firstitemmobile=''}               
            {/foreach}
        </ul>
    {/if}
{/if}
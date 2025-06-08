{if configuration::get('smart_shortcode_tab_style') == 'tab'}
	{if $sds_results != ''}
		{foreach from=$sds_results item=sds_result}
			<li><a class="idTabHrefShort" href="#idsmartproducttab-{$sds_result.id_smart_product_tab}">{$sds_result.title}</a></li>
		{/foreach}
	{/if}
{else}
	{if $sds_results != ''}
		{foreach from=$sds_results item=sds_result}
			<section class="page-product-box">
				<h3 class="page-product-heading">{$sds_result.title}</h3>
				<div class="rte">{$sds_result.content}</div>
			</section>
		{/foreach}
	{/if}
{/if}
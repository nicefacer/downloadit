{if configuration::get('smart_shortcode_tab_style') == 'tab'}
	{foreach from=$sds_results item=sds_result}
	   <div id="idsmartproducttab-{$sds_result.id_smart_product_tab}">
	        {$sds_result.content}
	   </div>
	{/foreach}
{/if}
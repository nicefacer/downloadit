{if isset($new_products) && $new_products}
	{include file="$tpl_dir./product-list.tpl" products=$new_products class='blocknewproducts' id='smartblocknewproducts'}
{/if}
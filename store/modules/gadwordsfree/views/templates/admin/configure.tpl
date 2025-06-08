{*
* 2007-2015 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}


{literal}
<script type="text/javascript">
	$('document').ready(function(){
		$('#test-code-btn').click(function() {
			var htmlString = $('#gadword-test').html();
			$('#gadwords-code').text(htmlString);
			$('#gadwords-title').show();
		});
	});
</script>
{/literal}
<div style="width:100%;height:120px;">

<div style="width:33.3%;text-align:left;float:left;height:100px">

<ins data-revive-zoneid="7" data-revive-id="27f1a68d9b3c239bbbd38cc09b79d453"></ins>

<script async src="//dh42.com/openx/www/delivery/asyncjs.php"></script>

</div>



<div style="width:33.3%;text-align:center;float:left;height:100px">

<ins data-revive-zoneid="8" data-revive-id="27f1a68d9b3c239bbbd38cc09b79d453"></ins>

<script async src="//dh42.com/openx/www/delivery/asyncjs.php"></script>

</div>



<div style="width:33.3%;text-align:center;float:right;height:100px">

<ins data-revive-zoneid="9" data-revive-id="27f1a68d9b3c239bbbd38cc09b79d453"></ins>

<script async src="//dh42.com/openx/www/delivery/asyncjs.php"></script>

</div>

</div>
<div class="panel">
	<h3><i class="icon icon-tags"></i> {l s='Test Your Code' mod='gadwords'}</h3>
	<div class="row">
		<label class="control-label col-lg-3">
		   Test your code
		</label>
		<div class="col-lg-4">
			<button id="test-code-btn" class="btn btn-large btn-primary">Click Here</button>
		</div>
	</div>

	<div id="gadword-test" style="display:none">
		{literal}
		<script type="text/javascript">
			var google_conversion_id = {/literal}{$GADWORDS_CONVERSION_TRACKING_ID|intval}{literal};
			var google_conversion_language = "{/literal}{if !empty($LANG)}{$LANG|escape:'htmlall':'UTF-8'}{else}en{/if}{literal}";
			var google_conversion_format = "3";
			var google_conversion_color = "ffffff";
			var google_conversion_label = "{/literal}{$GADWORDS_CONVERSION_TRACKING_LABEL|escape:'htmlall':'UTF-8'}{literal}";
			var google_conversion_currency = "{/literal}{$CURRENCY|escape:'htmlall':'UTF-8'}{literal}";
			var google_conversion_value = {/literal}{if !empty($TOTAL_ORDER)}{$TOTAL_ORDER|floatval}{else}1.000000{/if}{literal};
			var google_remarketing_only = false;
		</script>
		{/literal}

		{literal}
		<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
		</script>
		<noscript>
			<div style="display:inline;">
				<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/{/literal}{$GADWORDS_CONVERSION_TRACKING_ID|intval}{literal}/?value={/literal}{if !empty($TOTAL_ORDER)}{$TOTAL_ORDER|floatval}{else}1.000000{/if}{if !empty($CURRENCY)}&amp;currency_code={$CURRENCY|escape:'htmlall':'UTF-8'}{/if}{literal}&amp;label={/literal}{$GADWORDS_CONVERSION_TRACKING_LABEL|escape:'htmlall':'UTF-8'}{literal}&amp;guid=ON&amp;script=0"/>
			</div>
		</noscript>
		{/literal}
	</div>
	<div class="row">
		<h2 id="gadwords-title" style="display: none;">{l s='Google Javascript code' mod='gadwordsfree'} :</h2>
		<div id="gadwords-code" class="col col-md-6"></div>
	</div>
</div>

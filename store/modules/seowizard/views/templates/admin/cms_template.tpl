{foreach $cms_url_data as $url}
<url>
	<loc><![CDATA[{$url}]]></loc> {*variable contains HTML content, Can not escape this*}
	<changefreq>{$sitemap_data['ks_frequency']|escape:'htmlall':'UTF-8'}</changefreq>
	<priority>{$sitemap_data['ks_priority']|escape:'htmlall':'UTF-8'}</priority>
</url>
{/foreach}
{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2016 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Order Lookup Result Page
*}
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
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Front tpl file
*}
{if $type == 'facebook'}
    <meta property="og:type" content="{$page|escape:'htmlall':'UTF-8'}">{*variable contaions html or url escape not required*}
    <meta property="og:title" content="{$title|escape:'htmlall':'UTF-8'}">{*variable contaions html or url escape not required*}
    <meta property="og:description" content="{$description|escape:'htmlall':'UTF-8'}">{*variable contaions html or url escape not required*}
    <meta property="og:image" content="{$image|escape:'htmlall':'UTF-8'}">{*variable contaions html or url escape not required*}
    {if $page != 'product'}
        <meta property="og:url" content="{$url|escape:'htmlall':'UTF-8'}">
    {/if}
{else}
    <meta name="twitter:title" content="{$title|escape:'htmlall':'UTF-8'}">{*variable contaions html or url escape not required*}
    <meta name="twitter:description" content="{$description|escape:'htmlall':'UTF-8'}">{*variable contaions html or url escape not required*}
    <meta name="twitter:image" content="{$image|escape:'htmlall':'UTF-8'}">{*variable contaions html or url escape not required*}
    {if $page != 'product'}
        <meta name="twitter:url" content="{$url|escape:'htmlall':'UTF-8'}">
    {/if}
{/if}

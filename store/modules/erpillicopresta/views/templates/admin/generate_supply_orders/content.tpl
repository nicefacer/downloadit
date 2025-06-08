{*
* 2007-2013 PrestaShop
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
*  @author    Illicopresta SA <contact@illicopresta.com>
*  @copyright 2007-2015 Illicopresta
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}


<input type="hidden" id="translate_select_least_one_order" value="{l s='Please select at least one order' mod='erpillicopresta'}" />

{include file=$template_path|cat:'common/erp_sidebar.tpl' erp_feature=$erp_feature}

{if isset($content)}
<input type="hidden" id="unselected_orders_list" name="unselected_orders_list" value="{if ( isset($smarty.post.unselected_orders_list))} {$smarty.post.unselected_orders_list} {/if}"/>
{$content}
{/if}
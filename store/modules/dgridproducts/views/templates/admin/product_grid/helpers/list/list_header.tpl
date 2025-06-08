{*
* 2007-2016 PrestaShop
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
* @author    Goryachev Dmitry    <dariusakafest@gmail.com>
* @copyright 2007-2016 Goryachev Dmitry
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/list/list_header.tpl"}

{block name="override_header"}
    <script>
		if (typeof tabs_manager == 'undefined')
			var tabs_manager = {};
        {assign var='priceDisplayPrecision' value=$smarty.const._PS_PRICE_DISPLAY_PRECISION_|intval}
        var currencySign = "{$currencySign|escape:'quotes':'UTF-8'}";
        var currencyRate = "{$currencyRate|escape:'quotes':'UTF-8'}";
        var currencyFormat = "{$currencyFormat|escape:'quotes':'UTF-8'}";
        var currencyBlank = "{$currencyBlank|escape:'quotes':'UTF-8'}";
        var priceDisplayPrecision = "{$priceDisplayPrecision|escape:'quotes':'UTF-8'}";
        var id_default_lang = {$default_lang->id|intval};
        var ad = '{$ad|addslashes|escape:'quotes':'UTF-8'}';
    </script>
    <script>
        l = {};
        l['ean13'] = "{l s='Ean13 wrong' mod='dgridproducts'}";
        l['upc'] = "{l s='Upc wrong' mod='dgridproducts'}";
        l['type_error'] = "{l s='Write wrong' mod='dgridproducts'}";
    </script>
{/block}

{block name=leadin}
    {if isset($category_tree)}
        <div class="bloc-leadin">
            <div class="panel">
                <div class="tree-panel-heading-controls filter_tree_category clearfix">
                    <div id="tree_toolbar" {if !isset($smarty.get.id_category)}style="display: none;"{/if}>
                        <div class="tree-actions pull-right">
                            <a class="collapse_all btn btn-default button" href="#" style="display: none;">
                                <i class="icon-collapse-alt"></i>
                                {l s='Collapse all' mod='dgridproducts'}
                            </a>
                            <a class="expand_all btn btn-default button" href="#">
                                <i class="icon-expand-alt"></i>
                                {l s='Expand all' mod='dgridproducts'}
                            </a>
                        </div>
                    </div>
                    <label class="tree-panel-label-title">
                        <input {if isset($smarty.get.id_category)}checked{/if} id="filter_category_tree" type="checkbox" class="filter-by-category" name="filter-by-category">
                        <i class="icon-tags"></i>
                        {l s='Filter by category' mod='dgridproducts'}
                    </label>
                </div>
                <div id="container_category_tree" {if !isset($smarty.get.id_category)}style="display: none;"{/if}>
                    {$category_tree|escape:'mail':'UTF-8'}
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function(){
                $('#filter_category_tree').click(function() {
                    if ($(this).is(':checked')) {
                        $('#container_category_tree').show();
                        $('#tree_toolbar').show();
                    } else {
                        $('#container_category_tree').hide();
                        $('#tree_toolbar').hide();
                        location.href = 'index.php?controller={$smarty.get.controller|escape:'quotes':'UTF-8'}&token={$smarty.get.token|escape:'quotes':'UTF-8'}&reset_filter_category=1';
                    }
                });
                var filter_tree_cat = null;
                filter_tree_cat = new TreeCustom('.block_category_tree', '#tree_toolbar');
                filter_tree_cat.afterChange = function ()
                {
                   var id = $(".block_category_tree").find(":input[type=radio]:checked").val()
                    if (location.href.indexOf('id_category') == -1)
                        location.href = 'index.php?controller={$smarty.get.controller|escape:'quotes':'UTF-8'}&token={$smarty.get.token|escape:'quotes':'UTF-8'}&id_category=' + id;
                    else
                        location.href = location.href.replace(
                                /&id_category=[0-9]*/, "")+"&id_category="
                        + id
                };
                filter_tree_cat.init();
                window.filter_tree_cat = filter_tree_cat;
            });
        </script>
    {/if}
{/block}
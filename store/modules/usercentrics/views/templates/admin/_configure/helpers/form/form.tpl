{**
 * usercentrics
 *
 * @author    silbersaiten <info@silbersaiten.de>
 * @copyright 2021 silbersaiten
 * @license   See joined file licence.txt
 * @category  Module
 * @support   silbersaiten <support@silbersaiten.de>
 * @version   1.0.3
 * @link      http://www.silbersaiten.de
*}
{extends file='helpers/form/form.tpl'}
{block name="input"}
    {if $input['type'] == 'uc_technologies'}
        {if isset($input.desc_before) && !empty($input.desc_before)}
            <p class="help-block">
                {if is_array($input.desc_before)}
                    {foreach $input.desc_before as $p}
                        {if is_array($p)}
                            <span id="{$p.id|escape:'html':'UTF-8'}">{$p.text|escape:'html':'UTF-8'}</span><br />
                        {else}
                            {$p|escape:'html':'UTF-8'}<br />
                        {/if}
                    {/foreach}
                {else}
                    {$input.desc_before|escape:'html':'UTF-8'}
                {/if}
            </p>
        {/if}
        <div class="uc_template_header">
            {if isset($input.show_crawler_button) && $input.show_crawler_button == true}<button class="btn btn-default">{$input.crawler_button_label|escape:'html':'UTF-8'}</button>{/if}
        </div>
        <div class="col-md-6 uc-template-categories">
            <p class="uc-template-categories-header">
                <button type="button" class="btn btn-default" id="open_uc_newcategory_form"
                        disabled="disabled"
                >
                    {$input.open_uc_newcategory_form_button_label|escape:'html':'UTF-8'}
                </button>
                <div class="row business_coming_container">
                    <p class="business_coming_msg badge badge-info">Business coming soon</p>
                </div>
                <div class="panel uc_newcategory_form" style="display:none;">
                    <div class="panel-heading">
                        {$input.newcategory_form_label|escape:'html':'UTF-8'}
                    </div>
                    <div class="uc_newcategory_form_messages" style="display: none;"></div>
                        <input type="hidden" name="uc_languages" value="{foreach $uc_languages as $language}{$language.id_lang|escape:'html':'UTF-8'},{/foreach}">
                        <label>{$input.category_name_label|escape:'html':'UTF-8'}</label>
                        {foreach $uc_languages as $language}
                            {if $uc_languages|count > 1}
                            <div class="form-group translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" style="{if $language.id_lang != $default_uc_language}display: none;{/if}">
                                <div class="col-lg-9">
                            {/if}
                           <input type="text" name="catname_{$language.id_lang}" id="catname_{$language.id_lang}" value="{if !empty($fields_value['catname'][$language.id_lang])}{$fields_value['catname'][$language.id_lang]|escape:'html':'UTF-8'}{/if}">
                            {if $uc_languages|count > 1}
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                        {$language.name|escape:'html':'UTF-8'}
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        {foreach from=$uc_languages item=language}
                                            <li>
                                                <a href="javascript:hideOtherLanguage('{$language.id_lang|escape:'html':'UTF-8'}');" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a>
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                                </div>
                            {/if}
                        {/foreach}
                        <label>{$input.category_description_label|escape:'html':'UTF-8'}</label>
                        {foreach $uc_languages as $language}
                            {if $uc_languages|count > 1}
                                <div class="form-group translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" style="{if $language.id_lang != $default_uc_language}display: none;{/if}">
                                <div class="col-lg-9">
                            {/if}
                            <textarea name="catdescription_{$language.id_lang|escape:'html':'UTF-8'}" id="catdescription_{$language.id_lang|escape:'html':'UTF-8'}">{if !empty($fields_value['catdescription'][$language.id_lang])}{$fields_value['catdescription'][$language.id_lang]|escape:'html':'UTF-8'}{/if}</textarea>
                            {if $uc_languages|count > 1}
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                        {$language.name|escape:'html':'UTF-8'}
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        {foreach from=$uc_languages item=language}
                                            <li>
                                                <a href="javascript:hideOtherLanguage('{$language.id_lang|escape:'html':'UTF-8'}');" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a>
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                                </div>
                            {/if}
                        {/foreach}

                    <label>{$input.category_disable_autotrans_label|escape:'html':'UTF-8'}</label>
                    {foreach $uc_languages as $language}
                        {if $uc_languages|count > 1}
                            <div class="form-group translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" style="{if $language.id_lang != $default_uc_language}display: none;{/if}">
                            <div class="col-lg-9">
                        {/if}
                        <input type="checkbox" class="catdisable_autotrans" name="catdisable_autotrans_{$language.id_lang|escape:'html':'UTF-8'}" value="true"{if $language.id_lang != 'en'} checked="checked"{/if}>
                        {if $uc_languages|count > 1}
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                    {$language.name|escape:'html':'UTF-8'}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$uc_languages item=language}
                                        <li>
                                            <a href="javascript:hideOtherLanguage('{$language.id_lang|escape:'html':'UTF-8'}');" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                            </div>
                        {/if}
                    {/foreach}
                    <p class="alert alert-info">{$input.category_disable_autotrans_help|escape:'html':'UTF-8'}</p>

                    <label>{$input.category_isessential_label|escape:'html':'UTF-8'}</label>
                    <div class="form-group"><div class="col-lg-12"><input type="checkbox" name="catisessential" value="true"></div>
                        <p class="help-block">{$input.category_is_essential_label|escape:'html':'UTF-8'}</p>
                    </div>

                    <div class="panel-footer">
                        <button type="button" class="btn btn-default" id="close_uc_newcategory_form">{$input.close_uc_newcategory_form_button_label|escape:'html':'UTF-8'}</button>
                        <button class="btn btn-primary" id="addCategory">{$input.addcategory_button_label|escape:'html':'UTF-8'}</button>
                    </div>
                <div class="messages" style="display: none;"></div>
            </div>
            {foreach $input.categories as $category}
            <div class="uc-template-category" data-slug="{$category.categorySlug|escape:'html':'UTF-8'}">
                <div class="row">
                    <div class="col-md-10">
                        {foreach $uc_languages as $language}
                        <label class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" style="{if $language.id_lang != $default_uc_language}display: none;{/if}">{$category.label[$language.id_lang]|escape:'html':'UTF-8'}</label>
                        {/foreach} <span class="quantity_consent_templates badge badge-info">{count($category.consentTemplates)}</span>
                        {foreach $uc_languages as $language}
                        <p class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" style="{if $language.id_lang != $default_uc_language}display: none;{/if}">{$category.description[$language.id_lang]|escape:'html':'UTF-8'}</p>
                        {/foreach}
                        <p class="isEssential help-block">{if $category.isEssential == 1}{$input.category_is_essential_label|escape:'html':'UTF-8'}{/if}</p>
                    </div>
                    <div class="col-md-2">
                        <a class="deleteCategory btn" data-slug="{$category.categorySlug|escape:'html':'UTF-8'}" {if count($category.consentTemplates) >0} disabled="disabled"{/if}>{if !$input.material_design_icons}<i class="icon-trash"></i>{else}<i class="material-icons">delete</i>{/if}</a>
                    </div>
                </div>
            </div>
            {/foreach}
            <div class="uc-template-category-empty hide">
                <div class="row">
                    <div class="col-md-10">
                        {foreach $uc_languages as $language}
                            <label class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}"></label>
                        {/foreach} <span class="quantity_consent_templates badge badge-info"></span>
                        {foreach $uc_languages as $language}
                            <p class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}"></p>
                        {/foreach}
                    </div>
                    <div class="col-md-2">
                        <a class="deleteCategory btn">{if !$input.material_design_icons}<i class="icon-trash"></i>{else}<i class="material-icons">delete</i>{/if}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 uc-template-templates">
            <div class="uc-template-templates-header hide">
                <input type="text" name="technology" id="technology">
                <button type="button" class="btn btn-default" id="addVendor">{$input.addtechnology_button_label|escape:'html':'UTF-8'}</button>
                <input type="hidden" id="uc_template_id">
                <input type="hidden" id="uc_category_slug">
                <input type="hidden" id="uc_template_name">
                <input type="hidden" id="uc_template_version">
                <input type="hidden" id="uc_template_image">
                <div class="messages" style="display: none;"></div>
            </div>
            {foreach $input.categories as $category}
                {foreach $category.consentTemplates as $template}
                    <div class="row uc-constent-template hide" data-category-slug="{$category.categorySlug|escape:'html':'UTF-8'}" data-id="{$template.templateId|escape:'html':'UTF-8'}">
                        <div class="col-md-10">
                            <img src="https://img.usercentrics.eu/dps/{$template.templateId|escape:'html':'UTF-8'}.svg"><label>{$template.template.dataProcessor|escape:'html':'UTF-8'} ({$template.version|escape:'html':'UTF-8'})</label>
                            <p>{$template.description|escape:'html':'UTF-8'}</p>
                        </div>
                        <div class="col-md-2">
                            {*{if !$category.isEssential}*}
                                <a class="deleteVendor btn" data-category-slug="{$category.categorySlug|escape:'html':'UTF-8'}" data-id="{$template.templateId|escape:'html':'UTF-8'}">{if !$input.material_design_icons}<i class="icon-trash"></i>{else}<i class="material-icons">delete</i>{/if}</a>
                            {*{/if}*}
                        </div>
                    </div>
                {/foreach}
            {/foreach}
            <div class="row uc-constent-template-empty hide">
                <div class="uc-constent-template-info col-md-10">
                    <img src="">
                    <label></label>
                    <p></p>
                </div>
                <div class="uc-constent-template-action col-md-2">
                    <a class="deleteVendor btn">{if !$input.material_design_icons}<i class="icon-trash"></i>{else}<i class="material-icons">delete</i>{/if}</a>
                </div>
            </div>
        </div>
        {if isset($input.desc_after_templates_category) && !empty($input.desc_after_templates_category)}
	        <div class="col-md-12">
	        	<p class="help-block">{$input.desc_after_templates_category}</p>
	        </div>
        {/if}
    {elseif $input['type'] == 'uc_lv'}
        {foreach $input.values as $value}
            <div class="radio {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}">
                {strip}
                    <label>
                        <div class="row">
                            <img src="{$value.image_url|escape:'html':'UTF-8'}" style="width: 150px;">
                        </div>
                        <div class="row">
                            <input type="radio"	name="{$input.name|escape:'html':'UTF-8'}" id="{$value.id|escape:'html':'UTF-8'}" value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}/>
                            {$value.label|escape:'html':'UTF-8'}
                        </div>
                    </label>
                {/strip}
            </div>
            {if isset($value.p) && $value.p}<p class="help-block">{$value.p|escape:'html':'UTF-8'}</p>{/if}
        {/foreach}
    {elseif $input['type'] == 'uc_logo'}
        <div class="input-group">
            <input type="text"
                   id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                   name="{$input.name|escape:'html':'UTF-8'}"
                   class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"
                   value="{if isset($input.string_format) && $input.string_format}{$fields_value[$input.name]|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}"
                    {if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
                    {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                    {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                    {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                    {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                    {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                    {if isset($input.required) && $input.required} required="required" {/if}
                    {if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if} />
            <button class="btn" type="button" onClick="javascript: $('#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}').val($(this).data('url'))" data-url="{$input.button.url|escape:'html':'UTF-8'}">{$input.button.label|escape:'html':'UTF-8'}</button>
        </div>
    {elseif $input['type'] == 'uc_privacy_button'}
        {foreach $input.values as $value}
            <div class="radio {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}">
                {strip}
                    <label>
                        <div class="row">
                            <img src="{$value.image_url|escape:'html':'UTF-8'}" style="width: 100px;">
                        </div>
                        <div class="row">
                            <input type="radio"	name="{$input.name|escape:'html':'UTF-8'}" id="{$value.id|escape:'html':'UTF-8'}" value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}/>
                            {$value.label|escape:'html':'UTF-8'}
                        </div>
                    </label>
                {/strip}
            </div>
            {if isset($value.p) && $value.p}<p class="help-block">{$value.p|escape:'html':'UTF-8'}</p>{/if}
        {/foreach}
    {elseif $input['type'] == 'uc_privacy_button_visible'}
        {foreach $input.values as $value}
            <div class="radio {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}">
                {strip}
                    <label>
                        <div class="row">
                            <img src="{$value.image_url|escape:'html':'UTF-8'}" style="width: 150px;">
                        </div>
                        <div class="row">
                            <input type="radio"	name="{$input.name|escape:'html':'UTF-8'}" id="{$value.id|escape:'html':'UTF-8'}" value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}/>
                            {$value.label|escape:'html':'UTF-8'}
                        </div>
                    </label>
                {/strip}
            </div>

        {/foreach}
        {foreach $input.values as $value}
            {if isset($value.p) && $value.p}<p class="help-block" for="{$value.id|escape:'html':'UTF-8'}">{$value.p|escape:'html':'UTF-8'}</p>{/if}
            {if isset($value.p2) && $value.p2}<p class="help-block" for="{$value.id|escape:'html':'UTF-8'}">{$value.p2|escape:'html':'UTF-8'}</p>{/if}
        {/foreach}
    {elseif $input.type == 'uc_textarea'}
        {assign var=use_textarea_autosize value=true}
        {if isset($input.lang) AND $input.lang}
            {foreach $uc_languages as $language}
                {if $uc_languages|count > 1}
                    <div class="form-group translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}"{if $language.id_lang != $default_uc_language} style="display:none;"{/if}>
                    <div class="col-lg-9">
                {/if}
            {if isset($input.maxchar) && $input.maxchar}
                <div class="input-group">
                <span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon">
															<span class="text-count-down">{$input.maxchar|intval}</span>
														</span>
            {/if}
                <textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}" id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_{$language.id_lang|escape:'html':'UTF-8'}" class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}"{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}>{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}</textarea>
            {if isset($input.maxchar) && $input.maxchar}
                </div>
            {/if}
                {if $uc_languages|count > 1}
                    </div>
                    <div class="col-lg-2">
                        <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                            {$language.name}
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            {foreach from=$uc_languages item=language}
                                <li>
                                    <a href="javascript:hideOtherLanguage('{$language.id_lang|escape:'html':'UTF-8'}');" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                    </div>
                {/if}
            {/foreach}
            {if isset($input.maxchar) && $input.maxchar}
                <script type="text/javascript">
                    $(document).ready(function(){
                        {foreach from=$uc_languages item=language}
                        countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}_counter"));
                        {/foreach}
                    });
                </script>
            {/if}
        {else}
            {if isset($input.maxchar) && $input.maxchar}
                <span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon">
												<span class="text-count-down">{$input.maxchar|intval}</span>
											</span>
            {/if}
            <textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name|escape:'html':'UTF-8'}" id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}" {if isset($input.cols)}cols="{$input.cols|escape:'html':'UTF-8'}"{/if} {if isset($input.rows)}rows="{$input.rows|escape:'html':'UTF-8'}"{/if} class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}"{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}>{$fields_value[$input.name]|escape:'html':'UTF-8'}</textarea>
            {if isset($input.maxchar) && $input.maxchar}
                <script type="text/javascript">
                    $(document).ready(function(){
                        countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter"));
                    });
                </script>
            {/if}
        {/if}
    {elseif $input.type == 'uc_select'}
        {foreach $uc_languages as $language}
            {if $uc_languages|count > 1}
            <div class="form-group translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}"{if $language.id_lang != $default_uc_language} style="display:none;"{/if}>
                <div class="col-lg-9">
            {/if}
            {if isset($input.options[$language.id_lang].query) && !$input.options[$language.id_lang].query && isset($input.empty_message)}
                {$input.empty_message|escape:'html':'UTF-8'}
                {$input.required = false}
                {$input.desc = null}
            {else}
                <select name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}"
                        class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if} fixed-width-xl"
                        id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}"
                        {if isset($input.multiple) && $input.multiple} multiple="multiple"{/if}
                        {if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
                        {if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if}
                        {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}>
                    {if isset($input.options.default)}
                        <option value="{$input.options.default.value|escape:'html':'UTF-8'}">{$input.options.default.label|escape:'html':'UTF-8'}</option>
                    {/if}
                    {if isset($input.options.optiongroup)}
                        {foreach $input.options[$language.id_lang].optiongroup.query AS $optiongroup}
                            <optgroup label="{$optiongroup[$input.options[$language.id_lang].optiongroup.label]}">
                                {foreach $optiongroup[$input.options[$language.id_lang].options.query] as $option}
                                    <option value="{$option[$input.options[$language.id_lang].options.id]|escape:'html':'UTF-8'}"
                                            {if isset($input.multiple)}
                                                {foreach $fields_value[$input.name][$language.id_lang] as $field_value}
                                                    {if $field_value == $option[$input.options[$language.id_lang].options.id]}selected="selected"{/if}
                                                {/foreach}
                                            {else}
                                                {if $fields_value[$input.name][$language.id_lang] == $option[$input.options.options.id]}selected="selected"{/if}
                                            {/if}
                                    >{$option[$input.options[$language.id_lang].options.name]|escape:'html':'UTF-8'}</option>
                                {/foreach}
                            </optgroup>
                        {/foreach}
                    {else}
                        {foreach $input.options[$language.id_lang].query AS $option}
                            {if is_object($option)}
                                <option value="{$option->$input.options[$language.id_lang].id|escape:'html':'UTF-8'}"
                                        {if isset($input.multiple)}
                                            {foreach $fields_value[$input.name][$language.id_lang] as $field_value}
                                                {if $field_value == $option->$input.options[$language.id_lang].id}
                                                    selected="selected"
                                                {/if}
                                            {/foreach}
                                        {else}
                                            {if $fields_value[$input.name][$language.id_lang] == $option->$input.options[$language.id_lang].id}
                                                selected="selected"
                                            {/if}
                                        {/if}
                                >{$option->$input.options[$language.id_lang].name|escape:'html':'UTF-8'}</option>
                            {elseif $option == "-"}
                                <option value="">-</option>
                            {else}
                                <option value="{$option[$input.options[$language.id_lang].id]|escape:'html':'UTF-8'}"
                                        {if isset($input.multiple)}
                                            {foreach $fields_value[$input.name][$language.id_lang] as $field_value}
                                                {if $field_value == $option[$input.options[$language.id_lang].id]}
                                                    selected="selected"
                                                {/if}
                                            {/foreach}
                                        {else}
                                            {if $fields_value[$input.name][$language.id_lang] == $option[$input.options[$language.id_lang].id]}
                                                selected="selected"
                                            {/if}
                                        {/if}
                                >{$option[$input.options[$language.id_lang].name]|escape:'html':'UTF-8'}</option>
                            {/if}
                        {/foreach}
                    {/if}
                </select>
            {/if}
            {if $uc_languages|count > 1}
                </div>
                <div class="col-lg-2">
                    <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                        {$language.name|escape:'html':'UTF-8'}
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        {foreach from=$uc_languages item=language}
                            <li>
                                <a href="javascript:hideOtherLanguage('{$language.id_lang|escape:'html':'UTF-8'}');" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a>
                            </li>
                        {/foreach}
                    </ul>
                </div>
            </div>
            {/if}
        {/foreach}
    {elseif $input.type == 'uc_languagesAvailable'}
        {if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
            {$input.empty_message|escape:'html':'UTF-8'}
            {$input.required = false}
            {$input.desc = null}
        {else}
            <select name="{$input.name|escape:'html':'UTF-8'}"
                    class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if} fixed-width-xl"
                    id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                    {if isset($input.multiple) && $input.multiple} multiple="multiple"{/if}
                    {if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
                    {if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if}
                    {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                    {if isset($input.data) && $input.data}{foreach $input.data AS $data} data-{$data.name|escape:'html':'UTF-8'}="{$data.value|escape:'html':'UTF-8'}"{/foreach}{/if}
                    >
                {if isset($input.options.default)}
                    <option value="{$input.options.default.value|escape:'html':'UTF-8'}">{$input.options.default.label|escape:'html':'UTF-8'}</option>
                {/if}
                    {foreach $input.options.query AS $option}
                        {if is_object($option)}
                            <option value="{$option->$input.options.id|escape:'html':'UTF-8'}"
                                    {if $option->$input.options.id == 'en'}  locked="locked"{/if}
                                    {if isset($input.multiple)}
                                        {foreach $fields_value[$input.name] as $field_value}
                                            {if $field_value == $option->$input.options.id}
                                                selected="selected"
                                            {/if}
                                        {/foreach}
                                    {else}
                                        {if $fields_value[$input.name] == $option->$input.options.id}
                                            selected="selected"
                                        {/if}
                                    {/if}
                            >{$option->$input.options.name|escape:'html':'UTF-8'}</option>
                        {elseif $option == "-"}
                            <option value="">-</option>
                        {else}
                            <option value="{$option[$input.options.id]|escape:'html':'UTF-8'}"
                                    {if $option[$input.options.id] == 'en'}  locked="locked"{/if}
                                    {if isset($input.multiple)}
                                        {foreach $fields_value[$input.name] as $field_value}
                                            {if $field_value == $option[$input.options.id]}
                                                selected="selected"
                                            {/if}
                                        {/foreach}
                                    {else}
                                        {if $fields_value[$input.name] == $option[$input.options.id]}
                                            selected="selected"
                                        {/if}
                                    {/if}
                            >{$option[$input.options.name]|escape:'html':'UTF-8'}</option>

                        {/if}
                    {/foreach}
            </select>
        {/if}
    {elseif $input.type == 'uc_switch'}
        <span class="switch prestashop-switch fixed-width-lg {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}">
            {foreach $input.values as $value}
                <input type="radio" name="{$input.name}"{if $value.value == 1} id="{$input.name}_on"{else} id="{$input.name}_off"{/if} value="{$value.value}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if (isset($input.disabled) && $input.disabled) or (isset($value.disabled) && $value.disabled)} disabled="disabled"{/if}/>
            {strip}
                <label {if $value.value == 1} for="{$input.name}_on"{else} for="{$input.name}_off"{/if}>
                {if $value.value == 1}
                    {l s='Yes' d='Admin.Global'}
                {else}
                    {l s='No' d='Admin.Global'}
                {/if}
            </label>
            {/strip}
            {/foreach}
            <a class="slide-button btn"></a>
        </span>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
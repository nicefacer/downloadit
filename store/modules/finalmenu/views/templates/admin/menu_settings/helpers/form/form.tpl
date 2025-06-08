{extends file="helpers/form/form.tpl"}
{block name="footer"}
    {if  isset($tab_object)}
        <div class="select-wrapper" id="advance-menu" {if  $fields_value['type'] == 1}style="display: none"{/if}>
        <hr>
        <h2>{l s='Advanced view' mod='finalmenu'}</h2>
        <div class="container">
        <div class="row">
        <div id="tab-control">
            <h4>{l s="Tab wrapper options" mod="finalmenu"}</h4>
            <div class="options-wrapper">
                <div class="form-group">
                    <label for="tab_wrapper_width" class="control-label col-lg-2 ">
                        {l s='Tab wrapper width' mod='finalmenu'}
                    </label>
                    <select id="tab_wrapper_width" name="tab_wrapper_width" class="col-lg-2" style="margin-left: 5px">
                        {if isset($tab_wrapper_width) && $tab_wrapper_width}
                            <option value="1" {if $tab_wrapper_width == 1}selected{/if}>{l s='1 column'  mod='finalmenu'}</option>
                            <option value="2" {if $tab_wrapper_width == 2}selected{/if}>{l s='2 columns'  mod='finalmenu'}</option>
                            <option value="3" {if $tab_wrapper_width == 3}selected{/if}>{l s='3 columns'  mod='finalmenu'}</option>
                            <option value="4" {if $tab_wrapper_width == 4}selected{/if}>{l s='4 columns'  mod='finalmenu'}</option>
                            <option value="5" {if $tab_wrapper_width == 5}selected{/if}>{l s='5 columns'  mod='finalmenu'}</option>
                            <option value="6" {if $tab_wrapper_width == 6}selected{/if}>{l s='6 columns'  mod='finalmenu'}</option>
                            <option value="7" {if $tab_wrapper_width == 7}selected{/if}>{l s='7 columns'  mod='finalmenu'}</option>
                            <option value="8" {if $tab_wrapper_width == 8}selected{/if}>{l s='8 columns'  mod='finalmenu'}</option>
                            <option value="9" {if $tab_wrapper_width == 9}selected{/if}>{l s='9 columns'  mod='finalmenu'}</option>
                            <option value="10" {if $tab_wrapper_width == 10}selected{/if}>{l s='10 columns'  mod='finalmenu'}</option>
                            <option value="11" {if $tab_wrapper_width == 11}selected{/if}>{l s='11 columns'  mod='finalmenu'}</option>
                            <option value="12" {if $tab_wrapper_width == 12}selected{/if}>{l s='12 columns'  mod='finalmenu'}</option>
                        {else}
                            <option value="1">{l s='1 column'  mod='finalmenu'}</option>
                            <option value="2">{l s='2 columns'  mod='finalmenu'}</option>
                            <option value="3">{l s='3 columns'  mod='finalmenu'}</option>
                            <option value="4">{l s='4 columns'  mod='finalmenu'}</option>
                            <option value="5">{l s='5 columns'  mod='finalmenu'}</option>
                            <option value="6">{l s='6 columns'  mod='finalmenu'}</option>
                            <option value="7">{l s='7 columns'  mod='finalmenu'}</option>
                            <option value="8">{l s='8 columns'  mod='finalmenu'}</option>
                            <option value="9">{l s='9 columns'  mod='finalmenu'}</option>
                            <option value="10">{l s='10 columns'  mod='finalmenu'}</option>
                            <option value="11">{l s='11 columns'  mod='finalmenu'}</option>
                            <option value="12" selected="selected">{l s='12 columns'  mod='finalmenu'}</option>
                        {/if}
                    </select>
                </div>
                <div class="form-group">
                    <label for="tab_wrapper_bg_color" class="control-label col-lg-2">
                        {l s='Tab wrapper background color' mod='finalmenu'}
                    </label>
                    <div class="form-group">
                        <div class="col-lg-2" style="margin-left: 5px">
                            <div class="row">
                                <div class="input-group">
                                    <input type="text" data-hex="true" class="color mColorPickerInput mColorPicker" name="tab_wrapper_bg_color" value="{if isset($tab_wrapper_bg_color) && $tab_wrapper_bg_color}{$tab_wrapper_bg_color}{else}#ededed{/if}" id="tab_wrapper_color" style="color: black; background-color: {if isset($tab_wrapper_bg_color) && $tab_wrapper_bg_color}{$tab_wrapper_bg_color}{else}#ededed{/if};"><span style="cursor:pointer;" id="icp_tab_wrapper_color" class="mColorPickerTrigger input-group-addon" data-mcolorpicker="true"><img src="../img/admin/color.png" style="border:0;margin:0 0 0 3px" align="absmiddle"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tab_blocks_border_color" class="control-label col-lg-2">
                        {l s='Blocks border color' mod='finalmenu'}
                    </label>
                    <div class="form-group">
                        <div class="col-lg-2" style="margin-left: 5px">
                            <div class="row">
                                <div class="input-group">
                                    <input type="text" data-hex="true" class="color mColorPickerInput mColorPicker" name="tab_blocks_border_color" value="{if isset($tab_blocks_border_color) && $tab_blocks_border_color}{$tab_blocks_border_color}{else}#dddddd{/if}" id="blocks_border_color" style="color: black; background-color: {if isset($tab_blocks_border_color) && $tab_blocks_border_color}{$tab_blocks_border_color}{else}#dddddd{/if};"><span style="cursor:pointer;" id="icp_blocks_border_color" class="mColorPickerTrigger input-group-addon" data-mcolorpicker="true"><img src="../img/admin/color.png" style="border:0;margin:0 0 0 3px" align="absmiddle"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-2 ">
                        {l s='Background image link' mod='finalmenu'}
                    </label>
                    {foreach $languages as $language}
                        {if $languages|count > 1}
                            <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none;"{/if}>
                            <div class="col-lg-5">
                        {/if}
                        <p style="overflow: hidden">
                            <input id="tab_background_link_{$language.id_lang}" type="text" name="tab_background_link_{$language.id_lang}" value="{if isset($tab_background_link[{$language.id_lang}]) && $tab_background_link[{$language.id_lang}]}{$tab_background_link[{$language.id_lang}]|escape:'html':'UTF-8'}{/if}" class="col-lg-2">
                        </p>
                        <a href="filemanager/dialog.php?type=1&field_id=tab_background_link_{$language.id_lang}" class="btn btn-default iframe-upload"  data-input-name="tab_background_link_{$language.id_lang}" type="button" style="float: right">{l s='Choose image' mod='finalmenu'}</a>
                        {if $languages|count > 1}
                            </div>
                            <div class="col-lg-1">
                                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                    {$language.iso_code}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$languages item=language}
                                        <li>
                                            <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                            </div>
                        {/if}
                    {/foreach}
                </div>
                <div class="form-group">
                    <label for="tab_bg_img_repeat" class="control-label col-lg-2 ">
                        {l s='Tab background image repeat' mod='finalmenu'}
                    </label>
                    <select id="tab_bg_img_repeat" name="tab_bg_img_repeat" class="col-lg-2" style="margin-left: 5px">
                        {if isset($tab_bg_img_repeat) && $tab_bg_img_repeat}
                            <option value="repeat" {if $tab_bg_img_repeat == 'repeat'}selected{/if}>{l s='repeat both'  mod='finalmenu'}</option>
                            <option value="no-repeat" {if $tab_bg_img_repeat == 'no-repeat'}selected{/if}>{l s='no repeat'  mod='finalmenu'}</option>
                            <option value="repeat-x" {if $tab_bg_img_repeat == 'repeat-x'}selected{/if}>{l s='repeat-x'  mod='finalmenu'}</option>
                            <option value="repeat-y" {if $tab_bg_img_repeat == 'repeat-y'}selected{/if}>{l s='repeat-y'  mod='finalmenu'}</option>
                        {else}
                            <option value="repeat" selected="selected">{l s='repeat both'  mod='finalmenu'}</option>
                            <option value="no-repeat">{l s='no repeat'  mod='finalmenu'}</option>
                            <option value="repeat-x">{l s='repeat-x'  mod='finalmenu'}</option>
                            <option value="repeat-y">{l s='repeat-y'  mod='finalmenu'}</option>
                        {/if}
                    </select>
                </div>
                <div class="form-group">
                    <label for="tab_bg_img_position" class="control-label col-lg-2 ">
                        {l s='Tab background image position' mod='finalmenu'}
                    </label>
                    <select id="tab_bg_img_position" name="tab_bg_img_position" class="col-lg-2" style="margin-left: 5px">
                        {if isset($tab_bg_img_position) && $tab_bg_img_position}
                            <option value="top-left" {if $tab_bg_img_position == 'top-left'}selected{/if}>{l s='top left'  mod='finalmenu'}</option>
                            <option value="top-right" {if $tab_bg_img_position == 'top-right'}selected{/if}>{l s='top right'  mod='finalmenu'}</option>
                            <option value="bottom-left" {if $tab_bg_img_position == 'bottom-left'}selected{/if}>{l s='bottom left'  mod='finalmenu'}</option>
                            <option value="bottom-right" {if $tab_bg_img_position == 'bottom-right'}selected{/if}>{l s='bottom right'  mod='finalmenu'}</option>
                        {else}
                            <option value="top-left" selected="selected">{l s='top left'  mod='finalmenu'}</option>
                            <option value="top-right">{l s='top right'  mod='finalmenu'}</option>
                            <option value="bottom-left">{l s='bottom left'  mod='finalmenu'}</option>
                            <option value="bottom-right">{l s='bottom right'  mod='finalmenu'}</option>
                        {/if}
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-2">
                        {l s='Tab wrapper padding' mod='finalmenu'}
                    </label>
                    <div class="form-group">
                        <div class="input-group" style="max-width: 100px; float: left; margin-right: 10px">
                            <div class="input-group-addon">{l s="left:" mod="finalmenu"}</div>
                            <input type="text" data-hex="true" name="tab_bg_img_pdng_left" style="width: 60px"
                                   value="{if isset($tab_bg_img_pdng_left) && $tab_bg_img_pdng_left}{$tab_bg_img_pdng_left}{else}0{/if}" id="tab_bg_img_pdng_left">
                            <div class="input-group-addon">px</div>
                        </div>
                        <div class="input-group col-lg-1" style="max-width: 100px; float: left; margin-right: 10px">
                            <div class="input-group-addon">{l s="right:" mod="finalmenu"}</div>
                            <input type="text" data-hex="true" name="tab_bg_img_pdng_right" style="width: 60px"
                                   value="{if isset($tab_bg_img_pdng_right) && $tab_bg_img_pdng_right}{$tab_bg_img_pdng_right}{else}0{/if}" id="tab_bg_img_pdng_right">
                            <div class="input-group-addon">px</div>
                        </div>
                        <div class="input-group col-lg-1" style="max-width: 100px; float: left; margin-right: 10px">
                            <div class="input-group-addon">{l s="top:" mod="finalmenu"}</div>
                            <input type="text" data-hex="true" name="tab_bg_img_pdng_top" style="width: 60px"
                                   value="{if isset($tab_bg_img_pdng_top) && $tab_bg_img_pdng_top}{$tab_bg_img_pdng_top}{else}0{/if}" id="tab_bg_img_pdng_top">
                            <div class="input-group-addon">px</div>
                        </div>
                        <div class="input-group col-lg-1" style="max-width: 100px; float: left; margin-right: 10px">
                            <div class="input-group-addon">{l s="bottom:" mod="finalmenu"}</div>
                            <input type="text" data-hex="true" name="tab_bg_img_pdng_bottom" style="width: 60px"
                                   value="{if isset($tab_bg_img_pdng_bottom) && $tab_bg_img_pdng_bottom}{$tab_bg_img_pdng_bottom}{else}0{/if}" id="tab_bg_img_pdng_bottom">
                            <div class="input-group-addon">px</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12" id="topbar-available-blocks">
            <h4>{l s="Drag and drop to create custom tab view" mod="finalmenu"}</h4>
            <div class="draggable-item">
                <span class="btn btn-default"><i class="icon-fullscreen"></i>{l s="CMS pages" mod="finalmenu"}</span>
                <input type="hidden" class="type" value="cms-pages">
            </div>
            <div class="draggable-item">
                <span class="btn btn-default"><i class="icon-fullscreen"></i>{l s="Suppliers" mod="finalmenu"}</span>
                <input type="hidden" class="type" value="suppliers">
            </div>
            <div class="draggable-item">
                <span class="btn btn-default"><i class="icon-fullscreen"></i>{l s="Manufacturers" mod="finalmenu"}</span>
                <input type="hidden" class="type" value="manufacturers">
            </div>
            <div class="draggable-item">
                <span class="btn btn-default"><i class="icon-fullscreen"></i>{l s="Categories" mod="finalmenu"}</span>
                <input type="hidden" class="type" value="categories">
            </div>
            <div class="draggable-item">
                <span class="btn btn-default"><i class="icon-fullscreen"></i>{l s="Product" mod="finalmenu"}</span>
                <input type="hidden" class="type" value="products">
            </div>
            <div class="draggable-item">
                <span class="btn btn-default"><i class="icon-fullscreen"></i>{l s="Search field" mod="finalmenu"}</span>
                <input type="hidden" class="type" value="search-field">
            </div>
            <div class="draggable-item">
                <span class="btn btn-default"><i class="icon-fullscreen"></i>{l s="Custom image" mod="finalmenu"}</span>
                <input type="hidden" class="type" value="custom-image">
            </div>
            <div class="draggable-item">
                <span class="btn btn-default"><i class="icon-fullscreen"></i>{l s="Custom HTML" mod="finalmenu"}</span>
                <input type="hidden" class="type" value="custom-html">
            </div>
            <div class="draggable-item">
                <span class="btn btn-default"><i class="icon-fullscreen"></i>{l s="CMS page" mod="finalmenu"}</span>
                <input type="hidden" class="type" value="cms-page">
            </div>
            <div class="draggable-item">
                <span class="btn btn-default"><i class="icon-fullscreen"></i>{l s="Custom link" mod="finalmenu"}</span>
                <input type="hidden" class="type" value="custom-link">
            </div>
        </div>

        {***************************************************************************************************
            TAB BLOCKS
        ****************************************************************************************************}

        <div class="col-lg-{if isset($tab_wrapper_width) && $tab_wrapper_width}{$tab_wrapper_width}{else}12{/if}" id="tab-view">
            {if isset($blocks) && $blocks}
                {foreach $blocks as $block}
                    <div class="tab-wrapper
                    col-lg-{$block.nmb_of_columns} tab-disable" id="{$block.name}" style="display: block; 
                    
                    {if $block.separator == "separator-none"}
                    {elseif $block.separator == "separator-left"}
                        border-left: 2px solid black;
                    {elseif $block.separator == "separator-right"}
                        border-right: 2px solid black;
                    {elseif $block.separator == "separator-bottom"}
                        border-bottom: 2px solid black;
                    {elseif $block.separator == "separator-top"}
                        border-top: 2px solid black;
                    {elseif $block.separator == "separator-complet"}
                        border: 2px solid black;
                    {elseif $block.separator == "separator-top-bottom"}
                        border-top: 2px solid black;
                        border-bottom: 2px solid black;
                    {elseif $block.separator == "separator-left-right"}
                        border-left: 2px solid black;
                        border-right: 2px solid black;
                    {/if}

                    float: {$block.float}; padding-left: {$block.padding_left}px; padding-right: {$block.padding_right}px; padding-bottom: {$block.padding_bottom}px; padding-top: {$block.padding_top}px;">
                        <div class="inner-tab-wrapper" style="{if $block.name == 'products'}height: {($block.nmb_of_rows * 200)}px{elseif $block.name == 'custom-link'}height: 50px{/if}">
                            <span class="tab-wrapper-title">{$block.title}</span>
                            <input type="hidden" class="type" value="{$block.type}">
                            <div class="draggable-tab-header">
                                <i class="icon-pencil"></i>
                                <i class="icon-times"></i>
                            </div>
                            <input type="hidden" name="active_blocks[]" id="block-ID" value="{$block.name}">
                        </div>
                    </div>
                {/foreach}
            {/if}
        </div>

        {****************************************************************************************************}
        {*LAYOUT EDIT OPTIONS*}
        {*****************************************************************************************************}

        <div class="settings-wrapper col-lg-12">
        <div id="block-layout" class="option-block">
            <h4>{l s='Block layout' mod='finalmenu'}</h4>
            <hr>
            <div class="block-layout">
                <div class="col-lg-2 block-width">
                    <label>{l s='Choose block width' mod='finalmenu'}</label>
                    <select id="block-width">
                        <option value="1">{l s='1 column' mod='finalmenu'}</option>
                        <option value="2">{l s='2 columns' mod='finalmenu'}</option>
                        <option value="3">{l s='3 columns' mod='finalmenu'}</option>
                        <option value="4">{l s='4 columns' mod='finalmenu'}</option>
                        <option value="5">{l s='5 columns' mod='finalmenu'}</option>
                        <option value="6">{l s='6 columns' mod='finalmenu'}</option>
                        <option value="7">{l s='7 columns' mod='finalmenu'}</option>
                        <option value="8">{l s='8 columns' mod='finalmenu'}</option>
                        <option value="9">{l s='9 columns' mod='finalmenu'}</option>
                        <option value="10">{l s='10 columns' mod='finalmenu'}</option>
                        <option value="11">{l s='11 columns' mod='finalmenu'}</option>
                        <option value="12">{l s='12 columns' mod='finalmenu'}</option>
                    </select>
                </div>
                <div class="col-lg-2 block-separator">
                    <label>{l s='Choose block separators' mod='finalmenu'}</label>
                    <select id="block-separator">
                        <option value="separator-none">{l s='none' mod='finalmenu'}</option>
                        <option value="separator-left">{l s='left' mod='finalmenu'}</option>
                        <option value="separator-right">{l s='right' mod='finalmenu'}</option>
                        <option value="separator-top">{l s='top' mod='finalmenu'}</option>
                        <option value="separator-bottom">{l s='bottom' mod='finalmenu'}</option>
                        <option value="separator-left-right">{l s='left + right' mod='finalmenu'}</option>
                        <option value="separator-top-bottom">{l s='top + bottom' mod='finalmenu'}</option>
                        <option value="separator-complet">{l s='complet' mod='finalmenu'}</option>
                    </select>
                </div>
                <div class="col-lg-2 block-float">
                    <label>{l s='Choose block float' mod='finalmenu'}</label>
                    <select id="block-float">
                        <option value="none">{l s='none' mod='finalmenu'}</option>
                        <option value="left">{l s='left' mod='finalmenu'}</option>
                        <option value="right">{l s='right' mod='finalmenu'}</option>
                    </select>
                </div>
            </div>
            <div class="alert alert-info">{l s='Each block has set auto height to fit inserted content. You can manipulate with block height by using custom padding.' mod='finalmenu'}</div>
             <div class="custom-padding col-lg-6">
                 <div class="form-group">
                     <label for="block-top-padding" class="control-label col-lg-6 ">{l s='Block top padding' mod='finalmenu'}</label>
                     <div class="col-lg-6 ">
                         <div class="input-group col-lg-2">
                             <input type="text"  id="block-top-padding" value="0" class="col-lg-2">
                             <span class="input-group-addon"> px </span>
                         </div>
                     </div>
                 </div>
                 <div class="form-group">
                     <label for="block-bottom-padding" class="control-label col-lg-6 ">{l s='Block bottom padding' mod='finalmenu'}</label>
                     <div class="col-lg-6 ">
                         <div class="input-group col-lg-2">
                             <input type="text"  id="block-bottom-padding" value="0" class="col-lg-2">
                             <span class="input-group-addon"> px </span>
                         </div>
                     </div>
                 </div>
                 <div class="form-group">
                     <label for="block-left-padding" class="control-label col-lg-6 ">{l s='Block left padding' mod='finalmenu'}</label>
                     <div class="col-lg-6 ">
                         <div class="input-group col-lg-2">
                             <input type="text"  id="block-left-padding" value="0" class="col-lg-2">
                             <span class="input-group-addon"> px </span>
                         </div>
                     </div>
                 </div>
                 <div class="form-group">
                     <label for="block-right-padding" class="control-label col-lg-6 ">{l s='Block right padding' mod='finalmenu'}</label>
                     <div class="col-lg-6 ">
                         <div class="input-group col-lg-2">
                             <input type="text"  id="block-right-padding" value="0" class="col-lg-2">
                             <span class="input-group-addon"> px </span>
                         </div>
                     </div>
                 </div>
            </div>
            <img src="{$base_url}modules/finalmenu/img/padding.jpg" alt="{l s='How padding works' mod='finalmenu'}" title="{l s='How padding works' mod='finalmenu'}" style="float: left">
        </div>

        {****************************************************************************************************}
        {*CMS EDIT OPTIONS*}
        {*****************************************************************************************************}

        <div id="cms-pages" class="option-block">
            <h4>{l s='Block settings' mod='finalmenu'}</h4>
            <hr>
            <label class="control-label col-lg-2">
                {l s='Choose CMS category' mod='finalmenu'}
            </label>
            <select size="5" class="select-box" style="width: 300px; height: 160px">
                {$adv_view_cms_options}
            </select>
            <div class="col-lg-12" style="margin: 10px 0px">
                <div class="btn btn-default pull-right advmenu-update-block">
                    <i class="process-icon-save"></i>{l s='update' mod='finalmenu'}
                </div>
            </div>
        </div>

        {****************************************************************************************************}
        {*CATEGORIES EDIT OPTIONS*}
        {*****************************************************************************************************}

        <div id="categories" class="option-block">
            <h4>{l s='Block settings' mod='finalmenu'}</h4>
            <hr>
            <label class="control-label col-lg-2">
                {l s='Choose categories view' mod='finalmenu'}
            </label>
            <select class="category-view-select" style="width: 300px; clear: right;" name="category-view-select">
                <option value="list" id="list" selected="selected">{l s='List view' mod='finalmenu'}</option>
                <option value="grip" id="grip">{l s='Grip view' mod='finalmenu'}</option>
            </select>

            <div class="category-grip-view" class="category-view" style="display: none">
                <div class="alert alert-warning">{l s='If you want to change content (Image Url, Category short description) already inserted items you must press update button after you have finished changes in your item, otherwise your changes will be deleted.' mod='finalmenu'}</div>
                <div class="col-lg-3 block-float">
                    <label>{l s='Number of columns for one item' mod='finalmenu'}</label>
                    <select id="item-number-of-columns">
                        <option value="1">{l s='1' mod='finalmenu'}</option>
                        <option value="2">{l s='2' mod='finalmenu'}</option>
                        <option value="3">{l s='3' mod='finalmenu'}</option>
                        <option value="4">{l s='4' mod='finalmenu'}</option>
                        <option value="5">{l s='5' mod='finalmenu'}</option>
                        <option value="6">{l s='6' mod='finalmenu'}</option>
                        <option value="7">{l s='7' mod='finalmenu'}</option>
                        <option value="8">{l s='8' mod='finalmenu'}</option>
                        <option value="9">{l s='9' mod='finalmenu'}</option>
                        <option value="10">{l s='10' mod='finalmenu'}</option>
                        <option value="11">{l s='11' mod='finalmenu'}</option>
                        <option value="12">{l s='12' mod='finalmenu'}</option>
                    </select>
                </div>
            </div>        
            <div class="category-select-wrapper">
                <div id="category-simple-view" class="category-view">
                    <select size="5" class="select-box" style="width: 300px; height: 160px">
                        {$adv_view_cat_options}
                    </select>
                    <div id="add-category" class="edit-button btn btn-default" style="display: none">
                        {l s='add selected' mod='finalmenu'}
                    </div>
                </div>
                <div class="category-grip-view" class="category-view" style="display: none">
                    <select size="5" class="selected-grip" style="width: 300px; height: 160px">
                    </select>
                    <div id="remove-category" class="edit-button btn btn-default">
                        {l s='remove selected' mod='finalmenu'}
                    </div>
                </div>
            </div>

            <div id="category-info" style="display: none">
                <div class="form-group">
                    <label for="image-url" class="control-label col-lg-2 ">
                        {l s='Image Url' mod='finalmenu'}
                    </label>
                    {foreach $languages as $language}
                        {if $languages|count > 1}
                            <div class="translatable-field lang-{$language.id_lang}"  {if $language.id_lang != $defaultFormLanguage}style="display:none;"{/if}>
                        {/if}
                        <div class="col-lg-9">
                            <p style="overflow: hidden">
                                <input type="text" id="category-small-image-{$language.id_lang}" name="category-small-image-{$language.id_lang}" value="" class="col-lg-2">
                                <p class="help-block">{l s="You can either enter the image's absolute link, or upload the image file. Image will be resize to 64x64px and his size should be at least 128x128px." mod="finalmenu"}</p>
                            </p>
                            <a href="filemanager/dialog.php?type=1&field_id=category-small-image-{$language.id_lang}" class="btn btn-default iframe-upload"  data-input-name="category-small-image-{$language.id_lang}" type="button" style="float: right">{l s='Choose image' mod='finalmenu'}</a>
                        </div>
                        {if $languages|count > 1}
                            <div class="col-lg-1">
                                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                    {$language.iso_code}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$languages item=language}
                                        <li>
                                            <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                            </div>
                        {/if}
                    {/foreach}
                </div>
                <div class="form-group">
                    <label class="control-label short-desc col-lg-2 ">
                        {l s='Category short description' mod='finalmenu'}
                    </label>
                    {foreach $languages as $language}
                        {if $languages|count > 1}
                            <div class="translatable-field lang-{$language.id_lang}"  {if $language.id_lang != $defaultFormLanguage}style="display:none;"{/if}>
                            <div class="col-lg-9">
                        {/if}
                            <textarea id="category-short-desc-{$language.id_lang}" class="col-lg-9"></textarea>
                        {if $languages|count > 1}
                            </div>
                            <div class="col-lg-1">
                                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                    {$language.iso_code}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$languages item=language}
                                        <li>
                                            <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                            </div>
                        {/if}
                    {/foreach}
                </div>
            </div>

            <div class="col-lg-12" style="margin: 10px 0px">
                <div class="btn btn-default pull-right advmenu-update-block">
                    <i class="process-icon-save"></i>{l s='update' mod='finalmenu'}
                </div>
            </div>
        </div>

        {****************************************************************************************************}
        {*MANUFACTURES EDIT OPTIONS*}
        {*****************************************************************************************************}

        <div id="manufacturers" class="option-block">
            <h4>{l s='Block settings' mod='finalmenu'}</h4>
            <hr>
            <div class="view-grip">
                <label class="col-lg-12"><span class="col-lg-2"></span>{l s='Manufacturers view grid' mod='finalmenu'}</label>
                <div class="form-group">
                    <label for="carriage-width" class="control-label col-lg-2 ">
                        {l s='Number of manufacturers per row' mod='finalmenu'}
                    </label>
                    <div class="col-lg-9 ">
                        <select name="carriage-width" class=" fixed-width-xl">
                            <option value="1" selected="selected">{l s=1 mod='finalmenu'}</option>
                            <option value="2">{l s='2' mod='finalmenu'}</option>
                            <option value="3">{l s='3' mod='finalmenu'}</option>
                            <option value="4">{l s='4' mod='finalmenu'}</option>
                            <option value="5">{l s='5' mod='finalmenu'}</option>
                            <option value="6">{l s='6' mod='finalmenu'}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="carriage-height" class="control-label col-lg-2 ">
                        {l s='Number of rows' mod='finalmenu'}
                    </label>
                    <div class="col-lg-9 ">
                        <select name="carriage-height" class=" fixed-width-xl">
                            <option value="1" selected="selected">{l s='1' mod='finalmenu'}</option>
                            <option value="2">{l s='2' mod='finalmenu'}</option>
                            <option value="3">{l s='3' mod='finalmenu'}</option>
                            <option value="4">{l s='4' mod='finalmenu'}</option>
                            <option value="5">{l s='5' mod='finalmenu'}</option>
                            <option value="6">{l s='6' mod='finalmenu'}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-2" for="simple_product">
                    {l s='Choose block view type' mod='finalmenu'}
                </label>
                <div class="col-lg-9">
                    <div class="radio">
                        <label for="multi" class="control-label">
                            <input type="radio" class="multi" name="manufacturers-view" value="multi">
                            {l s='Manufacturers as list' mod='finalmenu'}
                        </label>
                    </div>
                    <div class="radio">
                        <label for="carousel" class="control-label">
                            <input type="radio" class="carousel" name="manufacturers-view" value="carousel">
                            <span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='This option will enable you to display manufacturers in scrollable carousel' mod='finalmenu'}">
                                 {l s='Manufacturers with image (carousel support)' mod='finalmenu'}
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <label class="control-label col-lg-2">
                {l s='Choose manufacturers' mod='finalmenu'}
            </label>
            <select multiple="multiple" class="carriage-select select-box" style="width: 300px; height: 160px">
                {$adv_view_man_options}
            </select>
            <div class="col-lg-12" style="margin: 10px 0px">
                <div class="btn btn-default pull-right advmenu-update-block">
                    <i class="process-icon-save"></i>{l s='update' mod='finalmenu'}
                </div>
            </div>
        </div>

        {****************************************************************************************************}
        {*SUPPLIERS EDIT OPTIONS*}
        {*****************************************************************************************************}

        <div id="suppliers" class="option-block">
            <h4>{l s='Block settings' mod='finalmenu'}</h4>
            <hr>

            <div class="view-grip">
                <label class="col-lg-12"><span class="col-lg-2"></span>{l s='Suppliers view grid' mod='finalmenu'}</label>
                <div class="form-group">
                    <label for="carriage-width" class="control-label col-lg-2 ">
                        {l s='Number of suppliers per row' mod='finalmenu'}
                    </label>
                    <div class="col-lg-9 ">
                        <select name="carriage-width" class=" fixed-width-xl">
                            <option value="1" selected="selected">{l s=1 mod='finalmenu'}</option>
                            <option value="2">{l s='2' mod='finalmenu'}</option>
                            <option value="3">{l s='3' mod='finalmenu'}</option>
                            <option value="4">{l s='4' mod='finalmenu'}</option>
                            <option value="5">{l s='5' mod='finalmenu'}</option>
                            <option value="6">{l s='6' mod='finalmenu'}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="carriage-height" class="control-label col-lg-2 ">
                        {l s='Number of rows' mod='finalmenu'}
                    </label>
                    <div class="col-lg-9 ">
                        <select name="carriage-height" class=" fixed-width-xl">
                            <option value="1" selected="selected">{l s='1' mod='finalmenu'}</option>
                            <option value="2">{l s='2' mod='finalmenu'}</option>
                            <option value="3">{l s='3' mod='finalmenu'}</option>
                            <option value="4">{l s='4' mod='finalmenu'}</option>
                            <option value="5">{l s='5' mod='finalmenu'}</option>
                            <option value="6">{l s='6' mod='finalmenu'}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-2" for="suppliers-view-type">
                    {l s='Choose block view type' mod='finalmenu'}
                </label>
                <div class="col-lg-9">
                    <div class="radio">
                        <label for="multi" class="control-label">
                            <input type="radio" name="suppliers-view" class="multi" checked="checked" value="multi">
                            {l s='Suppliers as list' mod='finalmenu'}
                        </label>
                    </div>
                    <div class="radio">
                        <label for="carousel" class="control-label">
                            <input type="radio" name="suppliers-view" class="carousel" value="carousel">
                            <span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='This option will enable you to display suppliers in scrollable carousel' mod='finalmenu'}">
                                 {l s='Suppliers with image (carousel support)' mod='finalmenu'}
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <label class="control-label col-lg-2">
                {l s='Choose suppliers' mod='finalmenu'}
            </label>
            <select  name='strings' id="strings" multiple style="width:300px;height: 150px" class="carriage-select select-box">
                {$adv_view_sup_options}
            </select>
            <div class="col-lg-12" style="margin: 10px 0px">
                <div class="btn btn-default pull-right advmenu-update-block">
                    <i class="process-icon-save"></i>{l s='update' mod='finalmenu'}
                </div>
            </div>
        </div>

        {****************************************************************************************************}
        {*PRODUCT EDIT OPTIONS*}
        {*****************************************************************************************************}

        <div id="products" class="option-block">
            <h4>{l s='Block settings' mod='finalmenu'}</h4>
            <hr>

            <label class="col-lg-12"><span class="col-lg-2"></span>{l s='Products view grid' mod='finalmenu'}</label>
            <div class="form-group">
                <label for="product-width" class="control-label col-lg-2 ">
                    {l s='Number of products per row' mod='finalmenu'}
                </label>
                <div class="col-lg-9 ">
                    <select name="product-width" class=" fixed-width-xl">
                        <option value="1" selected="selected">{l s=1 mod='finalmenu'}</option>
                        <option value="2">{l s='2' mod='finalmenu'}</option>
                        <option value="3">{l s='3' mod='finalmenu'}</option>
                        <option value="4">{l s='4' mod='finalmenu'}</option>
                        <option value="5">{l s='5' mod='finalmenu'}</option>
                        <option value="6">{l s='6' mod='finalmenu'}</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="product-height" class="control-label col-lg-2 ">
                    {l s='Number of rows' mod='finalmenu'}
                </label>
                <div class="col-lg-9 ">
                    <select name="product-height" class=" fixed-width-xl">
                        <option value="1" selected="selected">{l s='1' mod='finalmenu'}</option>
                        <option value="2">{l s='2' mod='finalmenu'}</option>
                        <option value="3">{l s='3' mod='finalmenu'}</option>
                        <option value="4">{l s='4' mod='finalmenu'}</option>
                        <option value="5">{l s='5' mod='finalmenu'}</option>
                        <option value="6">{l s='6' mod='finalmenu'}</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-2" for="product-autocomplete">
                    <span class="label-tooltip" data-toggle="tooltip"
                          title="{l s='Start by typing the first letters of the product\'s name, then select the product from the drop-down list. ' mod='finalmenu'}{l s='Do not forget to save the product afterwards!' mod='finalmenu'}">
                          {l s='Choose your products' mod='finalmenu'}
                    </span>
                </label>
                <div class="col-lg-4">
                    <input type="hidden" name="products-IDs" id="products-IDs" value=""/>
                    <input type="hidden" name="products-names" id="products-names" value=""/>

                    <div id="ajax_choose_product">
                        <div class="input-group">
                            <input type="text" id="product-autocomplete" name="product-autocomplete"/>
                            <span class="input-group-addon"><i class="icon-search"></i></span>
                        </div>
                    </div>

                    <div id="selected-products">
                    </div>
                </div>
            </div>

            <div class="col-lg-12" style="margin: 10px 0px">
                <div class="btn btn-default pull-right advmenu-update-block">
                    <i class="process-icon-save"></i>{l s='update' mod='finalmenu'}
                </div>
            </div>
        </div>

        {****************************************************************************************************}
        {*SEARCH EDIT OPTIONS*}
        {*****************************************************************************************************}     

        <div id="search-field" class="option-block">
            <h4>{l s='Block settings' mod='finalmenu'}</h4>
            <hr>

            <div class="form-group">
                <label for="search-position" class="control-label col-lg-2 ">
                    {l s='Choose searchfield position' mod='finalmenu'}
                </label>
                <div class="col-lg-9 ">
                    <select name="search-position" class=" fixed-width-xl">
                        <option id="center" value="center">{l s='center' mod='finalmenu'}</option>
                        <option id="left" value="left">{l s='left' mod='finalmenu'}</option>
                        <option id="right" value="right">{l s='right' mod='finalmenu'}</option>
                    </select>
                </div>
            </div>

            <div class="col-lg-12" style="margin: 10px 0px">
                <div class="btn btn-default pull-right advmenu-update-block">
                    <i class="process-icon-save"></i>{l s='update' mod='finalmenu'}
                </div>
            </div>
        </div>

        {****************************************************************************************************}
        {*IMAGE EDIT OPTIONS*}
        {*****************************************************************************************************}

        <div id="custom-image" class="option-block">
            <h4>{l s='Block settings' mod='finalmenu'}</h4>
            <hr>
            <div class="form-group">
                <label for="image-url" class="control-label col-lg-2 ">
                    {l s='Image Url' mod='finalmenu'}
                </label>
                {foreach $languages as $language}
                    {if $languages|count > 1}
                        <div class="translatable-field lang-{$language.id_lang}"  {if $language.id_lang != $defaultFormLanguage}style="display:none;"{/if}>
                    {/if}
                    <div class="col-lg-9">
                        <p style="overflow: hidden">
                            <input type="text" id="image-url-{$language.id_lang}" name="image-url-{$language.id_lang}" class="col-lg-2">
                            <p  class="help-block">{l s="You can either enter the image's absolute link, or upload the image file." mod="finalmenu"}</p>
                        </p>
                        <a href="filemanager/dialog.php?type=1&field_id=image-url-{$language.id_lang}" class="btn btn-default iframe-upload"  data-input-name="image-url-{$language.id_lang}" type="button" style="float: right">{l s='Choose image' mod='finalmenu'}</a>
                    </div>
                    {if $languages|count > 1}
                        <div class="col-lg-1">
                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                {$language.iso_code}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=language}
                                    <li>
                                        <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                        </div>
                    {/if}
                {/foreach}
            </div>
            <div class="form-group">
                <label for="image-link" class="control-label col-lg-2 ">
                    {l s='Image Link' mod='finalmenu'}
                </label>
                {foreach $languages as $language}
                    {if $languages|count > 1}
                        <div class="translatable-field lang-{$language.id_lang}"  {if $language.id_lang != $defaultFormLanguage}style="display:none;"{/if}>
                    {/if}
                    <div class="col-lg-9">
                        <p style="overflow: hidden">
                            <input type="text" id="image-link-{$language.id_lang}" name="image-link-{$language.id_lang}" class="col-lg-2">
                            <p class="help-block">{l s="Can be omitted." mod="finalmenu"}</p>
                        </p>
                    </div>
                    {if $languages|count > 1}
                        <div class="col-lg-1">
                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                {$language.iso_code}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=language}
                                    <li>
                                        <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                        </div>
                    {/if}
                {/foreach}
            </div>
            <div class="form-group">
                <label for="image-desc" class="control-label col-lg-2 ">
                    {l s='Image description' mod='finalmenu'}
                </label>
                {foreach $languages as $language}
                    {if $languages|count > 1}
                        <div class="translatable-field lang-{$language.id_lang}"  {if $language.id_lang != $defaultFormLanguage}style="display:none;"{/if}>
                    {/if}
                    <div class="col-lg-9">
                        <p style="overflow: hidden">
                            <input type="text" id="image-desc-{$language.id_lang}" name="image-desc-{$language.id_lang}" class="col-lg-2">
                        <p class="help-block">{l s="Please enter a short but meaningful description." mod="finalmenu"}</p>
                        </p>
                    </div>
                    {if $languages|count > 1}
                        <div class="col-lg-1">
                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                {$language.iso_code}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=language}
                                    <li>
                                        <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                        </div>
                    {/if}
                {/foreach}
            </div>
            <div class="col-lg-12" style="margin: 10px 0px">
                <div class="btn btn-default pull-right advmenu-update-block">
                    <i class="process-icon-save"></i>{l s='update' mod='finalmenu'}
                </div>
            </div>
        </div>

        {****************************************************************************************************}
        {*CUSTOM HTML EDIT OPTIONS*}
        {*****************************************************************************************************}

        <div id="custom-html" class="option-block">
            <h4>{l s='Block settings' mod='finalmenu'}</h4>
            <hr>
            <div class="form-group">
                <label for="advmenu-custom-code" class="control-label col-lg-2 ">
                    {l s='Enter custom code' mod='finalmenu'}
                </label>
                {foreach $languages as $language}
                    {if $languages|count > 1}
                        <div class="form-group translatable-field lang-{$language.id_lang}"  {if $language.id_lang != $defaultFormLanguage}style="display:none;"{/if}>
                        <div class="col-lg-8">
                    {/if}
                    <textarea name="html-{$language.id_lang}" ></textarea>

                    {if $languages|count > 1}
                        </div>
                        <div class="col-lg-1">
                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                {$language.iso_code}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=language}
                                    <li>
                                        <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                        </div>
                    {/if}
                {/foreach}
            </div>
            <div class="col-lg-12" style="margin: 10px 0px">
                <div class="btn btn-default pull-right advmenu-update-block">
                    <i class="process-icon-save"></i>{l s='update' mod='finalmenu'}
                </div>
            </div>
        </div>

        {****************************************************************************************************}
        {*CUSTOM LINK EDIT OPTIONS*}
        {*****************************************************************************************************}

        <div id="custom-link" class="option-block">
            <h4>{l s='Block settings' mod='finalmenu'}</h4>
            <hr>
            <div class="form-group">
                <label for="custom-link-name" class="control-label col-lg-2 ">
                    {l s='Link name' mod='finalmenu'}
                </label>
                {foreach $languages as $language}
                    {if $languages|count > 1}
                        <div class="translatable-field lang-{$language.id_lang}"  {if $language.id_lang != $defaultFormLanguage}style="display:none;"{/if}>
                    {/if}
                    <div class="col-lg-9">
                        <p style="overflow: hidden">
                            <input type="text" id="custom-link-name-{$language.id_lang}" name="custom-link-name-{$language.id_lang}" value="" class="col-lg-2">
                        </p>
                    </div>
                    {if $languages|count > 1}
                        <div class="col-lg-1">
                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                {$language.iso_code}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=language}
                                    <li>
                                        <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                        </div>
                    {/if}
                {/foreach}
            </div>
            <div class="form-group">
                <label for="custom-link-url" class="control-label col-lg-2 ">
                    {l s='Link url' mod='finalmenu'}
                </label>
                {foreach $languages as $language}
                    {if $languages|count > 1}
                        <div class="translatable-field lang-{$language.id_lang}"  {if $language.id_lang != $defaultFormLanguage}style="display:none;"{/if}>
                    {/if}
                    <div class="col-lg-9">
                        <p style="overflow: hidden">
                            <input type="text" id="custom-link-url-{$language.id_lang}" name="custom-link-url-{$language.id_lang}" value="" class="col-lg-2">
                        </p>
                    </div>
                    {if $languages|count > 1}
                        <div class="col-lg-1">
                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                {$language.iso_code}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=language}
                                    <li>
                                        <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                        </div>
                    {/if}
                {/foreach}
            </div>
            <div class="form-group">
                <label for="custom_link_new_window" class="control-label col-lg-3 ">
                    {l s='New window' mod='finalmenu'}
                </label>
                <div class="col-lg-9 ">
                    <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" value="1" name="custom_link_new_window" id="custom_link_new_window_on" checked="checked">
                    <label for="custom_link_new_window_on">{l s='Yes' mod='finalmenu'}</label>
                    <input type="radio" name="custom_link_new_window" id="custom_link_new_window_off" value="0" >
                    <label for="custom_link_new_window_off">{l s='No' mod='finalmenu'}</label>
                    <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>

            <div class="col-lg-12" style="margin: 10px 0px">
                <div class="btn btn-default pull-right advmenu-update-block">
                    <i class="process-icon-save"></i>{l s='update' mod='finalmenu'}
                </div>
            </div>
        </div>

        {****************************************************************************************************}
        {*CMS page EDIT OPTIONS*}
        {****************************************************************************************************}   

        <div id="cms-page" class="option-block">
            <h4>{l s='Block settings' mod='finalmenu'}</h4>
            <hr>
            <label class="control-label col-lg-2">
                {l s='Available pages' mod='finalmenu'}
            </label>
            <select name="cms-page-select" id="cms-page-select" class="select-box" style="width: 300px;">
                {$adv_view_available_cms_pages}
            </select>
            <div class="col-lg-12" style="margin: 10px 0px">
                <div class="btn btn-default pull-right advmenu-update-block">
                    <i class="process-icon-save"></i>{l s='update' mod='finalmenu'}
                </div>
            </div>
        </div>

        </div>
        </div> <!-- row end -->
        </div>

        <div id="hidden-inputs">
            <input type="hidden" name="blocks-id-count" value="{if isset($blocks) && $blocks}{$blocks|@count}{else}0{/if}">
            <input type="hidden" name="active-block-ID" id="active-block-ID" value="">
            <input type="hidden" name="active-block-type" id="active-block-type" value="">
            <input type="hidden" name="advance_tab_object" id="advance_tab_object" value='{if isset($tab_object) && $tab_object}{$tab_object}{/if}'>
        </div>

        </div>
        <div class="select-wrapper" id="simple-menu" {if $fields_value["type"] != 1}style="display: none"{/if}>
            <hr>
            <h2>{l s='Simple view' mod='finalmenu'}</h2>
            <div class="form-group">
                <label class="col-lg-3 control-label">{l s='Deph limit' mod='finalmenu'}</label>
                <div class="col-lg-3">
                    <p style="overflow: hidden"> <input type="text" id="category_limit" name="category_limit" value="{if isset($category_limit) && $category_limit}{$category_limit|escape:'html':'UTF-8'}{/if}" class="col-lg-2"> </p>
                    <p class="help-block">{l s='Set own level depth. Leave blank or insert 0 for all child. This option is only available for categories and CMS categories.' mod='finalmenu'}</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label">{l s='Select tab view' mod='finalmenu'}</label>
                <div class="col-lg-9">
                    <select name="simple_menu_select" id="simple_menu_select" style="width: 300px; height: 160px;" size="5">
                        {$smp_view_available_options}
                    </select>
                </div>
            </div>
            <div class="link-select-show-box">
                <div class="form-group">
                    <label for="link_title" class="control-label col-lg-3">
                        {l s='Link title' mod='finalmenu'}
                    </label>
                    {foreach $languages as $language}
                        {if $languages|count > 1}
                            <div class="translatable-field lang-{$language.id_lang}"  {if $language.id_lang != $defaultFormLanguage}style="display:none;"{/if}>
                        {/if}
                        <div class="col-lg-7">
                            <p style="overflow: hidden"> <input type="text" id="link_title_{$language.id_lang}" name="link_title_{$language.id_lang}" value="{if isset($fields_value["link_title"][{$language.id_lang}]) && $fields_value["link_title"][{$language.id_lang}]}{$fields_value["link_title"][{$language.id_lang}]|escape:'html':'UTF-8'}{/if}" class="col-lg-2"> </p>
                        </div>
                        {if $languages|count > 1}
                            <div class="col-lg-1">
                                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                    {$language.iso_code}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$languages item=language}
                                        <li><a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a></li>
                                    {/foreach}
                                </ul>
                            </div>
                            </div>
                        {/if}
                    {/foreach}
                </div>
                <div class="form-group">
                    <label for="link_url" class="control-label col-lg-3">
                        {l s='Link url' mod='finalmenu'}
                    </label>
                    {foreach $languages as $language}
                        {if $languages|count > 1}
                            <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none;"{/if}>
                        {/if}
                        <div class="col-lg-7">
                            <p style="overflow: hidden"><input type="text" id="link_url_{$language.id_lang}" name="link_url_{$language.id_lang}" value="{if isset($fields_value["link_url"]) && $fields_value["link_url"]}{$fields_value["link_url"][{$language.id_lang}]|escape:'html':'UTF-8'}{/if}" class="col-lg-2" > </p>
                        </div>
                        {if $languages|count > 1}
                            <div class="col-lg-1">
                                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                    {$language.iso_code}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$languages item=language}
                                        <li>
                                            <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                            </div>
                        {/if}
                    {/foreach}
                </div>
                <div class="form-group">
                    <label for="tab_link_new_window" class="control-label col-lg-3 ">
                        {l s='New window' mod='finalmenu'}
                    </label>
                    <div class="col-lg-9 ">
                        <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="tab_link_new_window" id="tab_link_new_window_on" value="1" {if isset($tab_link_new_window) && $tab_link_new_window && $tab_link_new_window == 1}checked="checked"{/if}>
                        <label for="tab_link_new_window_on">{l s='Yes' mod='finalmenu'}</label>
                        <input type="radio" name="tab_link_new_window" id="tab_link_new_window_off" value="0" {if (isset($tab_link_new_window) && $tab_link_new_window && $tab_link_new_window == 0) || !isset($tab_link_new_window)}checked="checked"{/if}>
                        <label for="tab_link_new_window_off">{l s='No' mod='finalmenu'}</label>
                        <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>
            </div>

            <div class="product-select-show-box">
               <div class="form-group">
                   <label class="col-lg-3 control-label">
                       {l s='Insert product ID' mod='finalmenu'}
                   </label>
                   <div class="col-lg-7">
                       <p style="overflow: hidden">
                           <input type="text" id="product_ID" name="product_ID" value="{if isset($product_ID) && $product_ID}{$product_ID|escape:'html':'UTF-8'}{/if}">
                       </p>
                   </div>
               </div>
            </div>
            <input type="hidden" value="{if isset($simple_menu_select) && $simple_menu_select}{$simple_menu_select}{/if}" name="simple-menu-selected-option">
        </div>
        <script type="text/javascript">
            var settingsUpdated = "{l s='Update successful. Your settings are ready for save!' mod='finalmenu'}";
        </script>
    {/if}
    {$smarty.block.parent}
{/block}

{block name="input"}
{if $input.type == 'icon_picker'}
        <div class="input-group col-lg-2">
            <input type="text" name="tab_icon" id="tab_icon" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" class="col-lg-2">
            <span class="input-group-addon icon-select" ><b>{l s='icon' mod='finalmenu'}</b></span>
        </div>
            <div class="icons-selector">
            <i class="icon-glass"></i>
            <i class="icon-music"></i>
            <i class="icon-search"></i>
            <i class="icon-envelope-alt"></i>
            <i class="icon-heart"></i>
            <i class="icon-star"></i>
            <i class="icon-star-empty"></i>
            <i class="icon-user"></i>
            <i class="icon-film"></i>
            <i class="icon-th-large"></i>
            <i class="icon-th"></i>
            <i class="icon-th-list"></i>
            <i class="icon-ok"></i>
            <i class="icon-remove"></i>
            <i class="icon-zoom-in"></i>
            <i class="icon-zoom-out"></i>
            <i class="icon-power-off"></i>
            <i class="icon-off"></i>
            <i class="icon-signal"></i>
            <i class="icon-gear"></i>
            <i class="icon-cog"></i>
            <i class="icon-trash"></i>
            <i class="icon-home"></i>
            <i class="icon-file-alt"></i>
            <i class="icon-time"></i>
            <i class="icon-road"></i>
            <i class="icon-download-alt"></i>
            <i class="icon-download"></i>
            <i class="icon-upload"></i>
            <i class="icon-inbox"></i>
            <i class="icon-play-circle"></i>
            <i class="icon-rotate-right"></i>
            <i class="icon-repeat"></i>
            <i class="icon-refresh"></i>
            <i class="icon-list-alt"></i>
            <i class="icon-lock"></i>
            <i class="icon-flag"></i>
            <i class="icon-headphones"></i>
            <i class="icon-volume-off"></i>
            <i class="icon-volume-down"></i>
            <i class="icon-volume-up"></i>
            <i class="icon-qrcode"></i>
            <i class="icon-barcode"></i>
            <i class="icon-tag"></i>
            <i class="icon-tags"></i>
            <i class="icon-book"></i>
            <i class="icon-bookmark"></i>
            <i class="icon-print"></i>
            <i class="icon-camera"></i>
            <i class="icon-font"></i>
            <i class="icon-bold"></i>
            <i class="icon-italic"></i>
            <i class="icon-text-height"></i>
            <i class="icon-text-width"></i>
            <i class="icon-align-left"></i>
            <i class="icon-align-center"></i>
            <i class="icon-align-right"></i>
            <i class="icon-align-justify"></i>
            <i class="icon-list"></i>
            <i class="icon-indent-left"></i>
            <i class="icon-indent-right"></i>
            <i class="icon-facetime-video"></i>
            <i class="icon-picture"></i>
            <i class="icon-pencil"></i>
            <i class="icon-map-marker"></i>
            <i class="icon-adjust"></i>
            <i class="icon-tint"></i>
            <i class="icon-edit"></i>
            <i class="icon-share"></i>
            <i class="icon-check"></i>
            <i class="icon-move"></i>
            <i class="icon-step-backward"></i>
            <i class="icon-fast-backward"></i>
            <i class="icon-backward"></i>
            <i class="icon-play"></i>
            <i class="icon-pause"></i>
            <i class="icon-stop"></i>
            <i class="icon-forward"></i>
            <i class="icon-fast-forward"></i>
            <i class="icon-step-forward"></i>
            <i class="icon-eject"></i>
            <i class="icon-chevron-left"></i>
            <i class="icon-chevron-right"></i>
            <i class="icon-plus-sign"></i>
            <i class="icon-minus-sign"></i>
            <i class="icon-remove-sign"></i>
            <i class="icon-ok-sign"></i>
            <i class="icon-question-sign"></i>
            <i class="icon-info-sign"></i>
            <i class="icon-screenshot"></i>
            <i class="icon-remove-circle"></i>
            <i class="icon-ok-circle"></i>
            <i class="icon-ban-circle"></i>
            <i class="icon-arrow-left"></i>
            <i class="icon-arrow-right"></i>
            <i class="icon-arrow-up"></i>
            <i class="icon-arrow-down"></i>
            <i class="icon-mail-forward"></i>
            <i class="icon-share-alt"></i>
            <i class="icon-resize-full"></i>
            <i class="icon-resize-small"></i>
            <i class="icon-plus"></i>
            <i class="icon-minus"></i>
            <i class="icon-asterisk"></i>
            <i class="icon-exclamation-sign"></i>
            <i class="icon-gift"></i>
            <i class="icon-leaf"></i>
            <i class="icon-fire"></i>
            <i class="icon-eye-open"></i>
            <i class="icon-eye-close"></i>
            <i class="icon-warning-sign"></i>
            <i class="icon-plane"></i>
            <i class="icon-calendar"></i>
            <i class="icon-random"></i>
            <i class="icon-comment"></i>
            <i class="icon-magnet"></i>
            <i class="icon-chevron-up"></i>
            <i class="icon-chevron-down"></i>
            <i class="icon-retweet"></i>
            <i class="icon-shopping-cart"></i>
            <i class="icon-folder-close"></i>
            <i class="icon-folder-open"></i>
            <i class="icon-resize-vertical"></i>
            <i class="icon-resize-horizontal"></i>
            <i class="icon-bar-chart"></i>
            <i class="icon-twitter-sign"></i>
            <i class="icon-facebook-sign"></i>
            <i class="icon-camera-retro"></i>
            <i class="icon-key"></i>
            <i class="icon-gears"></i>
            <i class="icon-cogs"></i>
            <i class="icon-comments"></i>
            <i class="icon-thumbs-up-alt"></i>
            <i class="icon-thumbs-down-alt"></i>
            <i class="icon-star-half"></i>
            <i class="icon-heart-empty"></i>
            <i class="icon-signout"></i>
            <i class="icon-linkedin-sign"></i>
            <i class="icon-pushpin"></i>
            <i class="icon-external-link"></i>
            <i class="icon-signin"></i>
            <i class="icon-trophy"></i>
            <i class="icon-github-sign"></i>
            <i class="icon-upload-alt"></i>
            <i class="icon-lemon"></i>
            <i class="icon-phone"></i>
            <i class="icon-unchecked"></i>
            <i class="icon-check-empty"></i>
            <i class="icon-bookmark-empty"></i>
            <i class="icon-phone-sign"></i>
            <i class="icon-twitter"></i>
            <i class="icon-facebook"></i>
            <i class="icon-github"></i>
            <i class="icon-unlock"></i>
            <i class="icon-credit-card"></i>
            <i class="icon-rss"></i>
            <i class="icon-hdd"></i>
            <i class="icon-bullhorn"></i>
            <i class="icon-bell"></i>
            <i class="icon-certificate"></i>
            <i class="icon-hand-right"></i>
            <i class="icon-hand-left"></i>
            <i class="icon-hand-up"></i>
            <i class="icon-hand-down"></i>
            <i class="icon-circle-arrow-left"></i>
            <i class="icon-circle-arrow-right"></i>
            <i class="icon-circle-arrow-up"></i>
            <i class="icon-circle-arrow-down"></i>
            <i class="icon-globe"></i>
            <i class="icon-wrench"></i>
            <i class="icon-tasks"></i>
            <i class="icon-filter"></i>
            <i class="icon-briefcase"></i>
            <i class="icon-fullscreen"></i>
            <i class="icon-group"></i>
            <i class="icon-link"></i>
            <i class="icon-cloud"></i>
            <i class="icon-beaker"></i>
            <i class="icon-cut"></i>
            <i class="icon-copy"></i>
            <i class="icon-paperclip"></i>
            <i class="icon-paper-clip"></i>
            <i class="icon-save"></i>
            <i class="icon-sign-blank"></i>
            <i class="icon-reorder"></i>
            <i class="icon-list-ul"></i>
            <i class="icon-list-ol"></i>
            <i class="icon-strikethrough"></i>
            <i class="icon-underline"></i>
            <i class="icon-table"></i>
            <i class="icon-magic"></i>
            <i class="icon-truck"></i>
            <i class="icon-pinterest"></i>
            <i class="icon-pinterest-sign"></i>
            <i class="icon-google-plus-sign"></i>
            <i class="icon-google-plus"></i>
            <i class="icon-money"></i>
            <i class="icon-caret-down"></i>
            <i class="icon-caret-up"></i>
            <i class="icon-caret-left"></i>
            <i class="icon-caret-right"></i>
            <i class="icon-columns"></i>
            <i class="icon-sort"></i>
            <i class="icon-sort-down"></i>
            <i class="icon-sort-up"></i>
            <i class="icon-envelope"></i>
            <i class="icon-linkedin"></i>
            <i class="icon-rotate-left"></i>
            <i class="icon-undo"></i>
            <i class="icon-legal"></i>
            <i class="icon-dashboard"></i>
            <i class="icon-comment-alt"></i>
            <i class="icon-comments-alt"></i>
            <i class="icon-bolt"></i>
            <i class="icon-sitemap"></i>
            <i class="icon-umbrella"></i>
            <i class="icon-paste"></i>
            <i class="icon-lightbulb"></i>
            <i class="icon-exchange"></i>
            <i class="icon-cloud-download"></i>
            <i class="icon-cloud-upload"></i>
            <i class="icon-user-md"></i>
            <i class="icon-stethoscope"></i>
            <i class="icon-suitcase"></i>
            <i class="icon-bell-alt"></i>
            <i class="icon-coffee"></i>
            <i class="icon-food"></i>
            <i class="icon-file-text-alt"></i>
            <i class="icon-building"></i>
            <i class="icon-hospital"></i>
            <i class="icon-ambulance"></i>
            <i class="icon-medkit"></i>
            <i class="icon-fighter-jet"></i>
            <i class="icon-beer"></i>
            <i class="icon-h-sign"></i>
            <i class="icon-plus-sign-alt"></i>
            <i class="icon-double-angle-left"></i>
            <i class="icon-double-angle-right"></i>
            <i class="icon-double-angle-up"></i>
            <i class="icon-double-angle-down"></i>
            <i class="icon-angle-left"></i>
            <i class="icon-angle-right"></i>
            <i class="icon-angle-up"></i>
            <i class="icon-angle-down"></i>
            <i class="icon-desktop"></i>
            <i class="icon-laptop"></i>
            <i class="icon-tablet"></i>
            <i class="icon-mobile-phone"></i>
            <i class="icon-circle-blank"></i>
            <i class="icon-quote-left"></i>
            <i class="icon-quote-right"></i>
            <i class="icon-spinner"></i>
            <i class="icon-circle"></i>
            <i class="icon-mail-reply"></i>
            <i class="icon-reply"></i>
            <i class="icon-github-alt"></i>
            <i class="icon-folder-close-alt"></i>
            <i class="icon-folder-open-alt"></i>
            <i class="icon-expand-alt"></i>
            <i class="icon-collapse-alt"></i>
            <i class="icon-smile"></i>
            <i class="icon-frown"></i>
            <i class="icon-meh"></i>
            <i class="icon-gamepad"></i>
            <i class="icon-keyboard"></i>
            <i class="icon-flag-alt"></i>
            <i class="icon-flag-checkered"></i>
            <i class="icon-terminal"></i>
            <i class="icon-code"></i>
            <i class="icon-reply-all"></i>
            <i class="icon-mail-reply-all"></i>
            <i class="icon-star-half-full"></i>
            <i class="icon-star-half-empty"></i>
            <i class="icon-location-arrow"></i>
            <i class="icon-crop"></i>
            <i class="icon-code-fork"></i>
            <i class="icon-unlink"></i>
            <i class="icon-question"></i>
            <i class="icon-info"></i>
            <i class="icon-exclamation"></i>
            <i class="icon-superscript"></i>
            <i class="icon-subscript"></i>
            <i class="icon-eraser"></i>
            <i class="icon-puzzle-piece"></i>
            <i class="icon-microphone"></i>
            <i class="icon-microphone-off"></i>
            <i class="icon-shield"></i>
            <i class="icon-calendar-empty"></i>
            <i class="icon-fire-extinguisher"></i>
            <i class="icon-rocket"></i>
            <i class="icon-maxcdn"></i>
            <i class="icon-chevron-sign-left"></i>
            <i class="icon-chevron-sign-right"></i>
            <i class="icon-chevron-sign-up"></i>
            <i class="icon-chevron-sign-down"></i>
            <i class="icon-html5"></i>
            <i class="icon-css3"></i>
            <i class="icon-anchor"></i>
            <i class="icon-unlock-alt"></i>
            <i class="icon-bullseye"></i>
            <i class="icon-ellipsis-horizontal"></i>
            <i class="icon-ellipsis-vertical"></i>
            <i class="icon-rss-sign"></i>
            <i class="icon-play-sign"></i>
            <i class="icon-ticket"></i>
            <i class="icon-minus-sign-alt"></i>
            <i class="icon-check-minus"></i>
            <i class="icon-level-up"></i>
            <i class="icon-level-down"></i>
            <i class="icon-check-sign"></i>
            <i class="icon-edit-sign"></i>
            <i class="icon-external-link-sign"></i>
            <i class="icon-share-sign"></i>
            <i class="icon-compass"></i>
            <i class="icon-collapse"></i>
            <i class="icon-collapse-top"></i>
            <i class="icon-expand"></i>
            <i class="icon-euro"></i>
            <i class="icon-eur"></i>
            <i class="icon-gbp"></i>
            <i class="icon-dollar"></i>
            <i class="icon-usd"></i>
            <i class="icon-rupee"></i>
            <i class="icon-inr"></i>
            <i class="icon-yen"></i>
            <i class="icon-jpy"></i>
            <i class="icon-renminbi"></i>
            <i class="icon-cny"></i>
            <i class="icon-won"></i>
            <i class="icon-krw"></i>
            <i class="icon-bitcoin"></i>
            <i class="icon-btc"></i>
            <i class="icon-file"></i>
            <i class="icon-file-text"></i>
            <i class="icon-sort-by-alphabet"></i>
            <i class="icon-sort-by-alphabet-alt"></i>
            <i class="icon-sort-by-attributes"></i>
            <i class="icon-sort-by-attributes-alt"></i>
            <i class="icon-sort-by-order"></i>
            <i class="icon-sort-by-order-alt"></i>
            <i class="icon-thumbs-up"></i>
            <i class="icon-thumbs-down"></i>
            <i class="icon-youtube-sign"></i>
            <i class="icon-youtube"></i>
            <i class="icon-xing"></i>
            <i class="icon-xing-sign"></i>
            <i class="icon-youtube-play"></i>
            <i class="icon-dropbox"></i>
            <i class="icon-stackexchange"></i>
            <i class="icon-instagram"></i>
            <i class="icon-flickr"></i>
            <i class="icon-adn"></i>
            <i class="icon-bitbucket"></i>
            <i class="icon-bitbucket-sign"></i>
            <i class="icon-tumblr"></i>
            <i class="icon-tumblr-sign"></i>
            <i class="icon-long-arrow-down"></i>
            <i class="icon-long-arrow-up"></i>
            <i class="icon-long-arrow-left"></i>
            <i class="icon-long-arrow-right"></i>
            <i class="icon-apple"></i>
            <i class="icon-windows"></i>
            <i class="icon-android"></i>
            <i class="icon-linux"></i>
            <i class="icon-dribbble"></i>
            <i class="icon-skype"></i>
            <i class="icon-foursquare"></i>
            <i class="icon-trello"></i>
            <i class="icon-female"></i>
            <i class="icon-male"></i>
            <i class="icon-gittip"></i>
            <i class="icon-sun"></i>
            <i class="icon-moon"></i>
            <i class="icon-archive"></i>
            <i class="icon-bug"></i>
            <i class="icon-vk"></i>
            <i class="icon-weibo"></i>
            <i class="icon-renren"></i>
            </div>
{elseif $input.type == 'layout_picker'}
    <div class="layout-picker">
        <div class="layout{if $fields_value[$input.name] == 'layout-1'} selected-menu-layout{/if}" id="layout-1"></div>
        <div class="layout{if $fields_value[$input.name] == 'layout-2'} selected-menu-layout{/if}" id="layout-2"></div>
        <div class="layout{if $fields_value[$input.name] == 'layout-3'} selected-menu-layout{/if}" id="layout-3"></div>
        <div class="layout{if $fields_value[$input.name] == 'layout-4'} selected-menu-layout{/if}" id="layout-4"></div>
        <input type="hidden" name="menu_layout_holder" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}">
    </div>
{elseif $input.type == 'mobile_select_box'}
    <select name="{$input.name|escape:'html':'utf-8'}" style="width: 300px; height: 160px;" size="5">
        {$mobile_menu_available_options}
    </select>
{elseif $input.type == 'image_upload'}
        {if isset($input.lang) AND $input.lang}
            {if $languages|count > 1}
                <div class="form-group">
            {/if}
            {foreach $languages as $language}
                {assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
                {if $languages|count > 1}
                    <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                    <div class="col-lg-9">
                {/if}
                {if $input.type == 'tags'}
                {literal}
                    <script type="text/javascript">
                        $().ready(function () {
                            var input_id = '{/literal}{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}{literal}';
                            $('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag' js=1}{literal}'});
                            $({/literal}'#{$table}{literal}_form').submit( function() {
                                $(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
                            });
                        });
                    </script>
                {/literal}
                {/if}
            {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                <div class="input-group {if isset($input.class)}{$input.class}{/if}">
            {/if}
                {if isset($input.maxchar)}
                    <span id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter" class="input-group-addon">
                                                    <span class="text-count-down">{$input.maxchar}</span>
                                                </span>
                {/if}
                {if isset($input.prefix)}
                    <span class="input-group-addon">
                      {$input.prefix}
                    </span>
                {/if}
                <input type="text"
                       id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"
                       name="{$input.name}_{$language.id_lang}"
                       class="{if $input.type == 'tags'}tagify {/if}{if isset($input.class)}{$input.class}{/if}"
                       value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                       onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"
                        {if isset($input.size)} size="{$input.size}"{/if}
                        {if isset($input.maxchar)} data-maxchar="{$input.maxchar}"{/if}
                        {if isset($input.maxlength)} maxlength="{$input.maxlength}"{/if}
                        {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                        {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                        {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                        {if isset($input.required) && $input.required} required="required" {/if}
                        {if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder}"{/if} />
                <a href="filemanager/dialog.php?type=1&field_id={$input.name}_{$language.id_lang}" class="btn btn-default iframe-upload"  data-input-name="{$input.name}_{$language.id_lang}" type="button" style="float: right">{$input.buttonLabel|escape:'html':'UTF-8'}</a>
                {if isset($input.suffix)}
                    <span class="input-group-addon">
                          {$input.suffix}
                    </span>
                {/if}
            {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                </div>
            {/if}
                {if $languages|count > 1}
                    </div>
                    <div class="col-lg-2">
                        <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                            {$language.iso_code}
                            <i class="icon-caret-down"></i>
                        </button>
                        <ul class="dropdown-menu">
                            {foreach from=$languages item=language}
                                <li><a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a></li>
                            {/foreach}
                        </ul>
                    </div>
                    </div>
                {/if}
            {/foreach}
            {if isset($input.maxchar)}
                <script type="text/javascript">
                    function countDown($source, $target) {
                        var max = $source.attr("data-maxchar");
                        $target.html(max-$source.val().length);

                        $source.keyup(function(){
                            $target.html(max-$source.val().length);
                        });
                    }

                    $(document).ready(function(){
                        {foreach from=$languages item=language}
                        countDown($("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"), $("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter"));
                        {/foreach}
                    });
                </script>
            {/if}
            {if $languages|count > 1}
                </div>
            {/if}
        {else}
            {if $input.type == 'tags'}
            {literal}
                <script type="text/javascript">
                    $().ready(function () {
                        var input_id = '{/literal}{if isset($input.id)}{$input.id}{else}{$input.name}{/if}{literal}';
                        $('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag'}{literal}'});
                        $({/literal}'#{$table}{literal}_form').submit( function() {
                            $(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
                        });
                    });
                </script>
                {/literal}
            {/if}
            {assign var='value_text' value=$fields_value[$input.name]}
            {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                <div class="input-group {if isset($input.class)}{$input.class}{/if}">
            {/if}
            {if isset($input.maxchar)}
                <span id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter" class="input-group-addon"><span class="text-count-down">{$input.maxchar}</span></span>
            {/if}
            {if isset($input.prefix)}
                <span class="input-group-addon">
                                          {$input.prefix}
                                        </span>
            {/if}
            <input type="text"
                   name="{$input.name}"
                   id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
                   value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                   class="{if $input.type == 'tags'}tagify {/if}{if isset($input.class)}{$input.class}{/if}"
            {if isset($input.size)} size="{$input.size}"{/if}
            {if isset($input.maxchar)} data-maxchar="{$input.maxchar}"{/if}
            {if isset($input.maxlength)} maxlength="{$input.maxlength}"{/if}
            {if isset($input.class)} class="{$input.class}"{/if}
            {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
            {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
            {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
            {if isset($input.required) && $input.required } required="required" {/if}
            {if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder}"{/if}
        />
            <a href="filemanager/dialog.php?type=1&field_id={$input.name}" class="btn btn-default iframe-upload"  data-input-name="{$input.name}" type="button" style="float: right">{$input.buttonLabel|escape:'html':'UTF-8'}</a>
            {if isset($input.suffix)}
            <span class="input-group-addon">
                {$input.suffix}
            </span>
            {/if}

            {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
            </div>
            {/if}
            {if isset($input.maxchar)}
            <script type="text/javascript">
            function countDown($source, $target) {
            var max = $source.attr("data-maxchar");
            $target.html(max-$source.val().length);

            $source.keyup(function(){
            $target.html(max-$source.val().length);
            });
            }
            $(document).ready(function(){
            countDown($("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"), $("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter"));
            });
            </script>
            {/if}
        {/if}
 {else}
    {$smarty.block.parent}
{/if}
{/block}








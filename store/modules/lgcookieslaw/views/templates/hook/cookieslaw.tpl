{*
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 *}
{if $lgcookieslaw_position == 3}
<div id="lgcookieslaw_banner" class="lgcookieslaw_banner  lgcookieslaw_message_floating">
    <div class="container">
        <div class="lgcookieslaw_message">{if version_compare($smarty.const._PS_VERSION_,'1.7.0','>=')}{{$cookie_message nofilter}|strip_tags:"<p>"}{* HTML CONTENT *}{else}{stripslashes($cookie_message|escape:'quotes':'UTF-8')}{/if}
            <a id="lgcookieslaw_info" {if isset($cms_target) && $cms_target} target="_blank" {/if} href="{$cms_link|escape:'quotes':'UTF-8'}" >
                {stripslashes($button2|escape:'quotes':'UTF-8')}
            </a>            
            {if $lgcookieslaw_setting_button != 1}
            <a onclick="customizeCookies()">
                {l s='customize cookies' mod='lgcookieslaw'}
            </a>
            {/if}
        </div>
        <div class="lgcookieslaw_button_container">
            {if $lgcookieslaw_setting_button == 1}
            <a class="lgcookieslaw_customize_cookies" onclick="customizeCookies()">
                {l s='customize cookies' mod='lgcookieslaw'}
            </a>
            {/if}
            <button id="lgcookieslaw_accept" class="lgcookieslaw_btn{if $lgcookieslaw_setting_button != 1} lgcookieslaw_btn_accept_big{/if}" onclick="closeinfo(true, true)">{stripslashes($button1|escape:'quotes':'UTF-8')}</button>
        </div>
    </div>
</div>
{else}
<div id="lgcookieslaw_banner" class="lgcookieslaw_banner">
    <div class="container">
        <div class="lgcookieslaw_message">{if version_compare($smarty.const._PS_VERSION_,'1.7.0','>=')}{{$cookie_message nofilter}|strip_tags:"<p>"}{* HTML CONTENT *}{else}{stripslashes($cookie_message|escape:'quotes':'UTF-8')}{/if}
            <a id="lgcookieslaw_info" {if isset($cms_target) && $cms_target} target="_blank" {/if} href="{$cms_link|escape:'quotes':'UTF-8'}" >
                {stripslashes($button2|escape:'quotes':'UTF-8')}
            </a>            
            <a class="lgcookieslaw_customize_cookies" onclick="customizeCookies()">
                {l s='customize cookies' mod='lgcookieslaw'}
            </a>
        </div>
        <div class="lgcookieslaw_button_container">
            <button id="lgcookieslaw_accept" class="lgcookieslaw_btn lgcookieslaw_btn_accept" onclick="closeinfo(true, true)">{stripslashes($button1|escape:'quotes':'UTF-8')}</button>
        </div>
    </div>
</div>
{/if}
<div style="display: none;" id="lgcookieslaw-modal">
    <div class="lgcookieslaw-modal-body">
        <h2>{l s='Cookies configuration' mod='lgcookieslaw'}</h2>
        <div class="lgcookieslaw-section">
            <div class="lgcookieslaw-section-name">
                {l s='Customization' mod='lgcookieslaw'}
            </div>
            <div class="lgcookieslaw-section-checkbox">
                <label class="lgcookieslaw_switch">
                    <div class="lgcookieslaw_slider_option_left">{l s='No' mod='lgcookieslaw'}</div>
                    <input type="checkbox" id="lgcookieslaw-cutomization-enabled" {if $third_paries}checked="checked"{/if}>
                    <span class="lgcookieslaw_slider{if $third_paries} lgcookieslaw_slider_checked{/if}"></span>
                    <div class="lgcookieslaw_slider_option_right">{l s='Yes' mod='lgcookieslaw'}</div>
                </label>
            </div>
            <div class="lgcookieslaw-section-description">
                {$cookie_additional nofilter}{* HTML CONTENT *}
            </div>
        </div>
        <div class="lgcookieslaw-section">
            <div class="lgcookieslaw-section-name">
                {l s='Functional (required)' mod='lgcookieslaw'}
            </div>
            <div class="lgcookieslaw-section-checkbox">
                <label class="lgcookieslaw_switch">
                    <div class="lgcookieslaw_slider_option_left">{l s='No' mod='lgcookieslaw'}</div>
                    <input type="checkbox" checked="checked" disabled="disabled">
                    <span class="lgcookieslaw_slider lgcookieslaw_slider_checked"></span>
                    <div class="lgcookieslaw_slider_option_right">{l s='Yes' mod='lgcookieslaw'}</div>
                </label>
            </div>
            <div class="lgcookieslaw-section-description">
                {$cookie_required nofilter}{* HTML CONTENT *}
            </div>
        </div>
    </div>
    <div class="lgcookieslaw-modal-footer">
        <div class="lgcookieslaw-modal-footer-left">
            <button class="btn" id="lgcookieslaw-close"> > {l s='Cancel' mod='lgcookieslaw'}</button>
        </div>
        <div class="lgcookieslaw-modal-footer-right">
            <button class="btn" id="lgcookieslaw-save" onclick="closeinfo(true)">{l s='Accept and continue' mod='lgcookieslaw'}</button>
        </div>
    </div>
</div>
<div class="lgcookieslaw_overlay"></div>

{*
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 *}
{literal}
.lgcookieslaw_banner {
    background-color: rgba({/literal}{$bgcolor|escape:'html':'UTF-8'}{literal});
    border-color: {/literal}{$bgcolor|escape:'html':'UTF-8'}{literal};
    border-left: 1px solid {/literal}{$bgcolor|escape:'html':'UTF-8'}{literal};
    border-right: 1px solid {/literal}{$bgcolor|escape:'html':'UTF-8'}{literal};
    color: {/literal}{$fontcolor|escape:'html':'UTF-8'}{literal} !important;
    -webkit-box-shadow: 0px 1px 5px 0px {/literal}{$shadowcolor|escape:'html':'UTF-8'}{literal};
    -moz-box-shadow:    0px 1px 5px 0px {/literal}{$shadowcolor|escape:'html':'UTF-8'}{literal};
    box-shadow:         0px 1px 5px 0px {/literal}{$shadowcolor|escape:'html':'UTF-8'}{literal};
    {/literal}
    {$position|escape:'htmlall':'UTF-8'};
    {literal}
}
#lgcookieslaw_banner .lgcookieslaw_message a {
    color: {/literal}{$fontcolor|escape:'html':'UTF-8'}{literal} !important;
    border-bottom: 1px solid {/literal}{$fontcolor|escape:'html':'UTF-8'}{literal};
}
.lgcookieslaw-modal-body {
    border-top: 4px solid {/literal}{$btn1_bgcolor|escape:'html':'UTF-8'}{literal};
}
.lgcookieslaw_banner .lgcookieslaw_btn {
    border-color: {/literal}{$btn1_bgcolor|escape:'html':'UTF-8'}{literal} !important;
    background: {/literal}{$btn1_bgcolor|escape:'html':'UTF-8'}{literal} !important;
    color: {/literal}{$btn1_fontcolor|escape:'html':'UTF-8'}{literal} !important;
}
.lgcookieslaw_banner a:hover.lgcookieslaw_btn {
    border-color: {/literal}{$btn2_bgcolor|escape:'html':'UTF-8'}{literal};
    background: {/literal}{$btn2_bgcolor|escape:'html':'UTF-8'}{literal};
    color: {/literal}{$btn2_fontcolor|escape:'html':'UTF-8'}{literal} !important;
}
/* New module*/

.lgcookieslaw-section-description ul li:before {
    color: {/literal}{$btn1_bgcolor|escape:'html':'UTF-8'}{literal}!important;
}
input:checked + .lgcookieslaw_slider, .lgcookieslaw_slider_checked {
    background-color: {/literal}{$btn1_bgcolor|escape:'html':'UTF-8'}{literal}!important;
}
input:focus + .lgcookieslaw_slider, .lgcookieslaw_slider_checked {
    box-shadow: 0 0 1px {/literal}{$btn1_bgcolor|escape:'html':'UTF-8'}{literal}!important;
}
#lgcookieslaw-save {
    background: {/literal}{$btn1_bgcolor|escape:'html':'UTF-8'}{literal}!important;
}
.lgcookieslaw_message_floating {
    border: 1px solid {/literal}{$btn1_bgcolor|escape:'html':'UTF-8'}{literal};
}
.lgcookieslaw_message_floating .lgcookieslaw_customize_cookies {
    border: 1px solid {/literal}{$fontcolor|escape:'html':'UTF-8'}{literal};
}
{/literal}

{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Admin Velovalidation tpl file
*}
<script>
   velovalidation.setErrorLanguage({
            empty_field: "{l s='Field cannot be empty.' mod='seowizard'}",
            maxchar_field: "{l s='Field cannot be greater than # characters.' mod='seowizard'}",
            minchar_field: "{l s='Field cannot be less than # character(s).' mod='seowizard'}",
            script: "{l s='Script tags are not allowed.' mod='seowizard'}",
            style: "{l s='Style tags are not allowed.' mod='seowizard'}",
            iframe: "{l s='Iframe tags are not allowed.' mod='seowizard'}",
            html_tags: "{l s='Field should not contain HTML tags.' mod='seowizard'}",
            invalid_url: "{l s='Invalid URL format.' mod='seowizard'}",
            empty_url: "{l s='Please enter URL.' mod='seowizard'}",
            max_url: "{l s='URL cannot be greater than #d characters.' mod='seowizard'}",
        });
</script>
<style>

    #kbgc_image_create_block form{
        display: inline-block;
        width: 49%;
    }
    #configuration_form_1 .form-wrapper{
        height: 333px;
    }

</style>
<script>
    var module_path = "{$module_path}";{*variable contains HTML content, Can not escape this*}
</script>
<div class="velsof_overlay"></div>
<div class="row" id="kbgc_image_create_block">
    <div class="col-sm-12">
        <div class="panel">
            <h3>
                <i class="icon-image"></i>&nbsp;
                {l s='Sitemap Settings' mod='seowizard'}
            </h3>
            <div class="image_form_container">

                {foreach $sitemaps as $shop_id=>$sitemap}
                    {foreach $sitemap as $lang=>$site}
                        <div id="link_{$shop_id|escape:'quotes':'UTF-8'}_{$lang|escape:'quotes':'UTF-8'}" style='font-weight: bold;'> {l s='Sitemap' mod='seowizard'}: <a href='{$site}' target='_blank'>{$site}</a></div>{*variable contains HTML content, Can not escape this*}
                        {/foreach}
                    {/foreach}


                {$form_product} {* Variable contains HTML content, escape not required *}
                {$form_category} {* Variable contains HTML content, escape not required *}
                {$form_cms} {* Variable contains HTML content, escape not required *}
                {$form_manufacturer} {* Variable contains HTML content, escape not required *}
            </div>
        </div>        
    </div>


</div>

<div id="kb_buy_link" style="text-align: center; padding: 25px; height: 140px; background: #9c9c9c24;">
    <div><span style="font-size:18px;">{l s='You are using the Free version of the module. Click here to buy the Paid version which is having the advanced features.' mod='seowizard'}</span>
        <br>
        <br>
        <a target="_blank" href="https://www.knowband.com/prestashop-seo-wizard"><span style="margin-left:30%;max-width:40% !important;font-size:18px;" class="btn btn-block btn-success action-btn">{l s='Buy Now' mod='seowizard'}</span></a><div>
        </div>

    </div>
</div>


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
* Admin tpl file
*}


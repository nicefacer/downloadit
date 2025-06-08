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
<div class="clearfix"></div>
<br>
<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <div class='panel-heading'>
                {l s='Cron Instructions' mod='seowizard'}
            </div>
            <div class='row'>
                <p>
                    {l s='Add the cron to your store via control panel/putty to update the SEO optimization. Please find the instructions to setup crons below -' mod='seowizard'}
                </p>
                <br>
                <p>
                    <b>{l s='URLs to Add to Cron via Control Panel' mod='seowizard'}</b>
                </p>
                <p>1. <b>{l s='Products Optimization' mod='seowizard'}</b> - {$sync_products_optimization_url|escape:'htmlall':'UTF-8'}</p>
                <p>2. <b>{l s='Product Meta tags' mod='seowizard'}</b> - {$sync_product_meta_tags_url|escape:'htmlall':'UTF-8'}</p>
                <p>3. <b>{l s='Generate Site Map' mod='seowizard'}</b> - {$sync_generate_sitemap_url|escape:'htmlall':'UTF-8'}</p>
                <br>
                <p>
                    <b>{l s='Cron setup via SSH' mod='seowizard'}</b>
                </p>
                <p>1. <b>{l s='Products Optimization' mod='seowizard'}</b> - 0 1 * * * curl -O /dev/null  {$sync_products_optimization_url|escape:'htmlall':'UTF-8'}</p>
                <p>2. <b>{l s='Product Meta tags' mod='seowizard'}</b> - 0 1 * * * curl -O /dev/null {$sync_product_meta_tags_url|escape:'htmlall':'UTF-8'}</p>
                <p>3. <b>{l s='Generate Site Map' mod='seowizard'}</b> - 0 1 * * * curl -O /dev/null  {$sync_generate_sitemap_url|escape:'htmlall':'UTF-8'}</p>
            </div>
        </div>
    </div>
</div>
<div class="modal"></div>

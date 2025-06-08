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
* Admin Synchronization tpl file
*}


<script>

    var kbCurrentToken = "{$kb_current_token|escape:'htmlall':'UTF-8'}";
    var opt_msg = "{l s='Optimization completed.' mod='seowizard'}";
    var opt_fail_msg = "{l s='Optimization failed.' mod='seowizard'}";


    var seo_product_list_err_msg = "{l s='Please select products which you want to restore.' mod='seowizard'}";
    var seo_category_list_err_msg = "{l s='Please select categories which you want to restore.' mod='seowizard'}";
    var seo_manufacturer_list_err_msg = "{l s='Please select manufacturers which you want to restore.' mod='seowizard'}";
    var seo_cms_list_err_msg = "{l s='Please select cms pages which you want to restore.' mod='seowizard'}";
</script>
<div class="bootstrap optimization">

</div>

<div class="row">
    <div class="col-lg-6">
        <div class="panel">                      
            <div class='panel-heading'>
                {l s='Optimize Products' mod='seowizard'}
            </div>
            <div class='row'>
                <div class="profileTabs col-lg-12">
                    <div class="form-group">
                        <label class="control-label col-lg-6">
                            <span class="label-tooltip">
                                {l s='Products Optimization' mod='seowizard'}
                            </span>
                        </label>
                        <div class="col-lg-6">
                            <input type="hidden" name="prod_opt_url" id="prod_opt_url" value="{$prod_opt_url|escape:'htmlall':'UTF-8'}"/>
                            <a href="javascript://" id="prod_opt" class="btn btn-info" onclick="product_optimization()" role="button">{l s='Product Optimization' mod='seowizard'}</a>
                            <span class="prod_opt_loader"><img src="{$spinner_img}" /></span> {*variable contains HTML content, Can not escape this*}
                            <div class="clearfix"></div>
                            <p class='help-block'>{l s='Optimize all products' mod='seowizard'}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 free-disabled">
        <div class="panel">                      
            <div class='panel-heading'>
                {l s='Optimize Categories' mod='seowizard'}
            </div>
            <div class='row'>
                <div class="profileTabs col-lg-12">
                    <div class="form-group">
                        <label class="control-label col-lg-6">
                            <span class="label-tooltip">
                                {l s='Categories Optimization' mod='seowizard'}
                            </span>
                        </label>
                        <div class="col-lg-6">
                            <a href="javascript://" id="opt_categories" class="btn btn-info" onclick="return false;" role="button">{l s='Optimize Categories' mod='seowizard'}</a>
                            <div class="clearfix"></div>
                            <p class='help-block'>{l s='Optimize all categories' mod='seowizard'}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="clearfix"></div>

<div class="row free-disabled">
    <div class="col-lg-6">
        <div class="panel">                      
            <div class='panel-heading'>
                {l s='Optimize CMS' mod='seowizard'}
            </div>
            <div class='row'>
                <div class="profileTabs col-lg-12">
                    <div class="form-group">
                        <label class="control-label col-lg-6">
                            <span class="label-tooltip">
                                {l s='CMS Optimization' mod='seowizard'}
                            </span>
                        </label>
                        <div class="col-lg-6">

                            <a href="javascript://" id="opt_cms" class="btn btn-info" onclick="return false;" role="button">{l s='Optimize CMS pages' mod='seowizard'}</a>

                            <div class="clearfix"></div>
                            <p class='help-block'>{l s='Optimize all CMS pages' mod='seowizard'}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="panel">                      
            <div class='panel-heading'>
                {l s='Optimize Manufacturers' mod='seowizard'}
            </div>
            <div class='row'>
                <div class="profileTabs col-lg-12">
                    <div class="form-group">
                        <label class="control-label col-lg-6">
                            <span class="label-tooltip">
                                {l s='Manufacturers Optimization' mod='seowizard'}
                            </span>
                        </label>
                        <div class="col-lg-6">

                            <a href="javascript://" id="opt_manufacturer" class="btn btn-info" onclick="return false;" role="button">{l s='Optimize manufacturers pages' mod='seowizard'}</a>

                            <div class="clearfix"></div>
                            <p class='help-block'>{l s='Optimize all manufacturers pages' mod='seowizard'}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="clearfix"></div>
<div class="row">
    <div class="col-lg-6 ">
        <div class="panel">                      
            <div class='panel-heading'>
                {l s='Update Product Meta Tags' mod='seowizard'}
            </div>
            <div class='row'>
                <div class="profileTabs col-lg-12">
                    <div class="form-group">
                        <label class="control-label col-lg-6">
                            <span class="label-tooltip">
                                {l s='Product Meta tags' mod='seowizard'}
                            </span>
                        </label>
                        <div class="col-lg-6">
                            <input type="hidden" name="met_pro_url" id="pro_meta_url" value="{$pro_met_url|escape:'htmlall':'UTF-8'}"/>
                            <a href="javascript://" id="pro_meta" class="btn btn-info" onclick="productmeta()" role="button">{l s='Update Product Meta tags' mod='seowizard'}</a>
                            <span class="meta_pro_loader"><img src="{$spinner_img}" /></span> {*variable contains HTML content, Can not escape this*}
                            <div class="clearfix"></div>
                            <p class='help-block'>{l s='Update all Products Meta tags' mod='seowizard'}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 free-disabled">
        <div class="panel">                      
            <div class='panel-heading'>
                {l s='Update Category Meta Tags' mod='seowizard'}
            </div>
            <div class='row'>
                <div class="profileTabs col-lg-12">
                    <div class="form-group">
                        <label class="control-label col-lg-6">
                            <span class="label-tooltip">
                                {l s='Category Meta tags' mod='seowizard'}
                            </span>
                        </label>
                        <div class="col-lg-6">
                            <a href="javascript://" id="cat_meta" class="btn btn-info" onclick="return false;" role="button">{l s='Update Categories Meta tags' mod='seowizard'}</a>
                            <div class="clearfix"></div>
                            <p class='help-block'>{l s='Update all Categories Meta tags' mod='seowizard'}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="clearfix"></div>

<div class="row">
    <div class="col-lg-6 free-disabled">
        <div class="panel">                      
            <div class='panel-heading'>
                {l s='Update Manufacturers Meta Tags' mod='seowizard'}
            </div>
            <div class='row'>
                <div class="profileTabs col-lg-12">
                    <div class="form-group">
                        <label class="control-label col-lg-6">
                            <span class="label-tooltip">
                                {l s='Manufacturers Meta tags' mod='seowizard'}
                            </span>
                        </label>
                        <div class="col-lg-6">
                            <a href="javascript://" id="man_meta" class="btn btn-info" onclick="return false;" role="button">{l s='Update Manufacturers Meta tags' mod='seowizard'}</a>
                            <div class="clearfix"></div>
                            <p class='help-block'>{l s='Update all Munufacturers Meta tags' mod='seowizard'}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="clearfix"></div>

<div class="row">
    <div class="col-lg-12" id="restore_initial_data">
        <div class="panel">
            <div class='panel-heading'>
                {l s='Restore initial data' mod='seowizard'}
            </div>
            <form id="restore_initial_setting" method="post" action="index.php?controller=AdminKbCron&token={$kb_current_token|escape:'htmlall':'UTF-8'}" role="search">
                <div class="form-wrapper">
                    <div class='row'>
                        <div class="profileTabs col-lg-12">

                            <div class="form-group" style="display: block;">
                                <label class="control-label col-lg-3">
                                    {l s='Group' mod='seowizard'}
                                </label>
                                <div class="col-lg-9">
                                    <div class="input-group">
                                        <select name="kb_seo[group_id]" class=" fixed-width-xl" id="kb_seo[group_id]">
                                            <option value="products">{l s='Products' mod='seowizard'}</option>
                                            <option value="categories">{l s='Categories' mod='seowizard'}</option>
                                            <option value="manufacturers">{l s='Manufacturers' mod='seowizard'}</option>
                                            <option value="cms">{l s='CMS Pages' mod='seowizard'}</option>
                                        </select>
                                    </div>
                                    <p class="help-block">
                                        {l s='Select the Group which you want to restore' mod='seowizard'}
                                    </p>
                                </div>

                            </div>

                            <div class="form-group" style="display: block;">
                                <label class="control-label col-lg-3 ">
                                    {l s='All / Selected' mod='seowizard'}
                                </label>
                                <div class="col-lg-9">
                                    <div class="input-group">
                                        <select name="kb_seo[selected]" class=" fixed-width-xl" id="kb_seo[selected]">
                                            <option value="0">{l s='All' mod='seowizard'}</option>
                                            <option value="1">{l s='Selected' mod='seowizard'}</option>
                                        </select>
                                    </div>
                                    <p class="help-block">
                                        {l s='Restore selected or all' mod='seowizard'}
                                    </p>
                                </div>

                            </div>

                            <div class="form-group" style="display: block;">
                                <label class="control-label col-lg-3 required">
                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="Select the products">
                                        {l s='Choose Products' mod='seowizard'}
                                    </span>
                                </label>
                                <div class="col-lg-9">
                                    <div class="input-group">
                                        <input type="text" name="kb_seo[products]" id="kb_seo[products]" value="" class="ac_input" required="required" autocomplete="off">
                                        <span class="input-group-addon">
                                            <i class="icon-search"></i>
                                        </span>
                                    </div>
                                    <div id="kb_excluded_product_holder">
                                    </div>
                                    <p class="help-block">
                                        {l s='Select the products which you want restore' mod='seowizard'}
                                    </p>
                                </div>
                            </div>
                            <div class="form-group hide">
                                <input type="hidden" name="kb_seo[excluded_products_hidden]" id="kb_seo[excluded_products_hidden]" value="">
                            </div>

                            <div class="form-group" style="display: block;">
                                <label class="control-label col-lg-3 required">
                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Select the categories' mod='seowizard'}">
                                        {l s='Choose Categories' mod='seowizard'}
                                    </span>
                                </label>
                                <div class="col-lg-9">
                                    <div class="input-group">
                                        <input type="text" name="kb_seo[categories]" id="kb_seo[categories]" value="" class="ac_input" required="required" autocomplete="off">
                                        <span class="input-group-addon">
                                            <i class="icon-search"></i>
                                        </span>
                                    </div>
                                    <div id="kb_excluded_category_holder">
                                    </div>
                                    <p class="help-block">
                                        {l s='Select the categories which you want to restore' mod='seowizard'}
                                    </p>
                                </div>
                            </div>
                            <div class="form-group hide">
                                <input type="hidden" name="kb_seo[excluded_categories_hidden]" id="kb_seo[excluded_categories_hidden]" value="">
                            </div>


                            <div class="form-group" style="display: block;">
                                <label class="control-label col-lg-3 required">
                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Select the cms' mod='seowizard'}">
                                        {l s='Choose CMS' mod='seowizard'}
                                    </span>
                                </label>
                                <div class="col-lg-9">
                                    <div class="input-group">
                                        <input type="text" name="kb_seo[cms]" id="kb_seo[cms]" value="" class="ac_input" required="required" autocomplete="off">
                                        <span class="input-group-addon">
                                            <i class="icon-search"></i>
                                        </span>
                                    </div>
                                    <div id="kb_excluded_cms_holder">
                                    </div>
                                    <p class="help-block">
                                        {l s='Select the cms which you want to restore' mod='seowizard'}
                                    </p>
                                </div>
                            </div>
                            <div class="form-group hide">
                                <input type="hidden" name="kb_seo[excluded_cms_hidden]" id="kb_seo[excluded_cms_hidden]" value="">
                            </div>

                            <div class="form-group" style="display: block;">
                                <label class="control-label col-lg-3 required">
                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Select the manufacturers' mod='seowizard'}">
                                        {l s='Choose Manufacturers' mod='seowizard'}
                                    </span>
                                </label>
                                <div class="col-lg-9">
                                    <div class="input-group">
                                        <input type="text" name="kb_seo[manufacturers]" id="kb_seo[manufacturers]" value="" class="ac_input" required="required" autocomplete="off">
                                        <span class="input-group-addon">
                                            <i class="icon-search"></i>
                                        </span>
                                    </div>
                                    <div id="kb_excluded_manufacturer_holder">
                                    </div>
                                    <p class="help-block">
                                        {l s='Select the manufacturers which you want to restore' mod='seowizard'}
                                    </p>
                                </div>
                            </div>
                            <div class="form-group hide">
                                <input type="hidden" name="kb_seo[excluded_manufacturers_hidden]" id="kb_seo[excluded_manufacturers_hidden]" value="">
                            </div>

                        </div>

                    </div>
                </div>
                <div class="panel-footer">
                    <button type="button" id="submit_seo_prod_wizard" class="btn btn-default btn btn-default pull-right" name="seowizard[submit_seo_prod_wizard]" onclick="restoreDataValidation();"><i class="process-icon-save"></i> {l s='Restore' mod='seowizard'}</button>
                </div>
            </form>

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
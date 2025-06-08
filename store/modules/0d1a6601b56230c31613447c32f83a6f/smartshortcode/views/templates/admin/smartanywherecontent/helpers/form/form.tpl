{extends file="helpers/form/form.tpl"}
{block name="label"}
    {if $input.type == 'vc_content_type'}
        {if isset($input.vc_is_edit) && !empty($input.vc_is_edit)}
<script type="text/javascript">
    $(document).ready(function(){
            {if $input.display_type_values != '1'}
                display_type_unchecked(0);
                {if $input.prd_page_values == 1}
                    prd_page_values_checked(0);
                {else}
                    prd_page_values_unchecked(0);
                {/if}
                {if $input.cat_page_values == '1'}
                    cat_page_values_checked(0);
                {else}
                    cat_page_values_unchecked(0);
                {/if}
                {if $input.cms_page_values == '1'}
                    cms_page_values_checked(0);
                {else}
                    cms_page_values_unchecked(0);
                {/if}
            {else}
                display_type_checked(0);
            {/if}
    });
</script> 
        {else}
<script type="text/javascript">
    $(document).ready(function(){
        vccontentallfieldhide();
    });
</script> 
        {/if}

<input type="hidden" value="{$input.id_category}" name="id_category" id="id_category">
<input type="hidden" value="{$input.id_product}" name="id_product" id="id_product">

<input type="hidden" value="{$input.prd_specify_values}" name="prd_specify" id="prd_specify_id_text">
<input type="hidden" value="{$input.cat_specify_values}" name="cat_specify" id="cat_specify_id_text">
<input type="hidden" value="{$input.cms_specify_values}" name="cms_specify" id="cms_specify_id_text">
        <script type="text/javascript">
            $(document).ready(function(){
                $("input:radio[name=display_type]").on('click',function(){
                    var active_val = $('input:radio[name=display_type]:checked').val();
                    if(active_val == 1){
                        display_type_checked(500);
                    }else{
                        display_type_unchecked(500);
                    }
                });
                $("input:radio[name=prd_page]").on('click',function(){
                    var prd_page_val = $('input:radio[name=prd_page]:checked').val();
                    if(prd_page_val == 1){
                        prd_page_values_checked(500);
                    }else{
                        prd_page_values_unchecked(500);
                    }
                });
                $("input:radio[name=cat_page]").on('click',function(){
                    var cat_page_val = $('input:radio[name=cat_page]:checked').val();
                    if(cat_page_val == 1){
                        cat_page_values_checked(500);
                    }else{
                        cat_page_values_unchecked(500);
                    }
                });
                $("input:radio[name=cms_page]").on('click',function(){
                    var cms_page_val = $('input:radio[name=cms_page]:checked').val();
                    if(cms_page_val == 1){
                        cms_page_values_checked(500);
                    }else{
                        cms_page_values_unchecked(500);
                    }
                });
            });
function vccontentallfieldhide(){
    $("#prd_page_on").parent().parent().parent().hide();
    $("#cat_page_on").parent().parent().parent().hide();
    $("#cms_page_on").parent().parent().parent().hide();
    $("#prd_specify_id").parent().parent().hide();
    $("#cat_specify_id").parent().parent().hide();
    $("#cms_specify_id").parent().parent().hide();
}
function display_type_checked(vcspeed){
    $("#prd_page_on").parent().parent().parent().hide(vcspeed);
    $("#cat_page_on").parent().parent().parent().hide(vcspeed);
    $("#cms_page_on").parent().parent().parent().hide(vcspeed);
    $("#prd_specify_id").parent().parent().hide(vcspeed);
    $("#cat_specify_id").parent().parent().hide(vcspeed);
    $("#cms_specify_id").parent().parent().hide(vcspeed);
}
function display_type_unchecked(vcspeed){
    $("#prd_page_on").parent().parent().parent().show(vcspeed);
    $("#cat_page_on").parent().parent().parent().show(vcspeed);
    $("#cms_page_on").parent().parent().parent().show(vcspeed);
    var prd_page_val = $('input:radio[name=prd_page]:checked').val();
    if(prd_page_val == 1){
        prd_page_values_checked(500);
    }else{
        prd_page_values_unchecked(500);
    }
    var cat_page_val = $('input:radio[name=cat_page]:checked').val();
    if(cat_page_val == 1){
        cat_page_values_checked(500);
    }else{
        cat_page_values_unchecked(500);
    }
    var cms_page_val = $('input:radio[name=cms_page]:checked').val();
    if(cms_page_val == 1){
        cms_page_values_checked(500);
    }else{
        cms_page_values_unchecked(500);
    }
}
function prd_page_values_checked(vcspeed){
    $("#prd_specify_id").parent().parent().hide(vcspeed);
}
function prd_page_values_unchecked(vcspeed){
    $("#prd_specify_id").parent().parent().show(vcspeed);
}
function cat_page_values_checked(vcspeed){
    $("#cat_specify_id").parent().parent().hide(vcspeed);
}
function cat_page_values_unchecked(vcspeed){
    $("#cat_specify_id").parent().parent().show(vcspeed);
}
function cms_page_values_checked(vcspeed){
    $("#cms_specify_id").parent().parent().hide(vcspeed);
}
function cms_page_values_unchecked(vcspeed){
    $("#cms_specify_id").parent().parent().show(vcspeed);
}
        // start multiple
            //<![CDATA
function prd_listchange() {
    var obj = $(this);
    var str = obj.val().join(',');
    obj.closest('form').find('#prd_specify_id_text').val(str);
}
function cat_listchange() {
    var obj = $(this);
    var str = obj.val().join(',');
    obj.closest('form').find('#cat_specify_id_text').val(str);
}
function cms_listchange() {
    var obj = $(this);
    var str = obj.val().join(',');
    obj.closest('form').find('#cms_specify_id_text').val(str);
}

function prd_textchange() {
    var obj = $(this);
    var list = obj.closest('form').find('#prd_specify_id');
    var values = obj.val().split(',');
    var len = values.length;
    
    list.find('option').prop('selected', false);
    for (var i = 0; i < len; i++)
        list.find('option[value="' + $.trim(values[i]) + '"]').prop('selected', true);
}
function cat_textchange() {
    var obj = $(this);
    var list = obj.closest('form').find('#cat_specify_id');
    var values = obj.val().split(',');
    var len = values.length;
    
    list.find('option').prop('selected', false);
    for (var i = 0; i < len; i++)
        list.find('option[value="' + $.trim(values[i]) + '"]').prop('selected', true);
}
function cms_textchange() {
    var obj = $(this);
    var list = obj.closest('form').find('#cms_specify_id');
    var values = obj.val().split(',');
    var len = values.length;
    
    list.find('option').prop('selected', false);
    for (var i = 0; i < len; i++)
        list.find('option[value="' + $.trim(values[i]) + '"]').prop('selected', true);
}
$(document).ready(function(){
    $('form[id="smart_contentanywhere_form"] input[id="prd_specify_id_text"]').each(function(){
        $(this).change(prd_textchange).change();
    });
    $('form[id="smart_contentanywhere_form"] input[id="cat_specify_id_text"]').each(function(){
        $(this).change(cat_textchange).change();
    });
    $('form[id="smart_contentanywhere_form"] input[id="cms_specify_id_text"]').each(function(){
        $(this).change(cms_textchange).change();
    });


    $('form[id="smart_contentanywhere_form"] select[id="prd_specify_id"]').each(function(){
        $(this).change(prd_listchange);
    });
    $('form[id="smart_contentanywhere_form"] select[id="cat_specify_id"]').each(function(){
        $(this).change(cat_listchange);
    });
    $('form[id="smart_contentanywhere_form"] select[id="cms_specify_id"]').each(function(){
        $(this).change(cms_listchange);
    });
});
//]]>
        // end multiple
        </script>
<style>
.bootstrap select[multiple], .bootstrap select[size] {
    height: 250px;
}
.bootstrap .prd_specify_class.fixed-width-xl,.bootstrap .cat_specify_class.fixed-width-xl,.bootstrap .cms_specify_class.fixed-width-xl {
    width: 450px !important;
}
</style>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

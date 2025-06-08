/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
 * Description
 *
 * Gamification wheel for offering discount coupons.
 */

var prod_lang = '';
var cms_lang = '';
var manu_lang = '';
var cat_lang = '';

function seovalidation() {
//    configuration_form
    $('#submit_seo_expert').attr('disabled', 'disabled');
    $('#configuration_form').submit();
}

function metaTagValidation() {
//    configuration_form
    $('#save_meta_data').attr('disabled', 'disabled');
    $('#knowband_meta_form').submit();
}


function updatetags() {
//    configuration_form
    $.ajax({
        url: module_path,
        type: 'post',
        data: 'ajax=true&method=generateproductsitemap',
        dataType: 'json',
        beforeSend: function () {
            $('#submit_seo_prod_expert').attr('disabled', 'disabled');
            $('.velsof_overlay').show();
        },
        success: function (json) {
            $('.image_form_container').prepend("<div id='link_" + shopid + "' style='font-weight: bold;'> Sitemap: <a href='" + json['success'] + "' target='_blank'>" + json['success'] + "</a></div>");
        },
        complete: function () {
            $('#submit_seo_prod_expert').removeAttr('disabled');
        }
    });
}

$(document).on('change', "[name^='seowizard[sitemap_prod_lang]']", function (e) {
    prod_lang = $(this).val();
});

$(document).on('change', "[name^='seowizard[sitemap_cat_lang]']", function (e) {
    cat_lang = $(this).val();
});

$(document).on('change', "[name^='seowizard[sitemap_cms_lang]']", function (e) {
    cms_lang = $(this).val();
});

$(document).on('change', "[name^='seowizard[sitemap_man_lang]']", function (e) {
    manu_lang = $(this).val();
});

function seositemapprodvalidation() {
    var shopid = $("[name^='seowizard[sitemap_prod_shop]']").val();
    var lang_id = '';
    if (prod_lang == '') {
        lang_id = $("[name^='seowizard[sitemap_prod_shop]']").val();
    } else {
        lang_id = prod_lang;
    }
    $('#link_' + shopid + '_' + lang_id).remove();
    $.ajax({
        url: module_path,
        type: 'post',
        data: 'ajax=true&method=generateproductsitemap&data=' + $('#configuration_form').serialize(),
        dataType: 'json',
        beforeSend: function () {
            $('#submit_seo_prod_expert').attr('disabled', 'disabled');
            $('.velsof_overlay').show();
        },
        success: function (json) {
            $('.image_form_container').prepend("<div id='link_" + shopid + "_" + lang_id + "' style='font-weight: bold;'> Sitemap: <a href='" + json['success'] + "' target='_blank'>" + json['success'] + "</a></div>");
        },
        complete: function () {
            $('#submit_seo_prod_expert').removeAttr('disabled');
            $('.velsof_overlay').hide();
        }
    });
}

function interlinkingValidation(ele) {
//    configuration_form
    var error = false;
    $(".error_message").remove();

    $("#seo_keyword").removeClass('error_field');
    $("#seo_keyword_url").removeClass('error_field');
    $("#seo_keyword_title").removeClass('error_field');
    var keyword_man_err = velovalidation.checkMandatory($("#seo_keyword"));
    if (keyword_man_err != true)
    {
        error = true;
        $("#seo_keyword").addClass('error_field');
        $("#seo_keyword").after('<span class="error_message">' + keyword_man_err + '</span>');
    }

    var keyword_url_man_err = velovalidation.checkMandatory($("#seo_keyword_url"));
    if (keyword_url_man_err != true)
    {
        error = true;
        $("#seo_keyword_url").addClass('error_field');
        $("#seo_keyword_url").after('<span class="error_message">' + keyword_url_man_err + '</span>');
    } else {
        var keyword_url_err = velovalidation.checkUrl($("#seo_keyword_url"));
        if (keyword_url_err != true) {
            error = true;
            $("#seo_keyword_url").addClass('error_field');
            $("#seo_keyword_url").after('<span class="error_message">' + keyword_url_err + '</span>');
        }
    }

    var keyword_url_title_man_err = velovalidation.checkMandatory($("#seo_keyword_title"));
    if (keyword_url_title_man_err != true)
    {
        error = true;
        $("#seo_keyword_title").addClass('error_field');
        $("#seo_keyword_title").after('<span class="error_message">' + keyword_url_title_man_err + '</span>');
    }
    if (!error) {
        $(ele).attr('disabled', 'disabled');
        $('#knowband_interlinking_form').submit();
    }
}
//'select[name=productsorting[category]]'
$(document).ready(function () {

    $(document).on('click', '.generate_sitemap_button', function () {
        var sitemap_id = $(this).attr('id');
        var shop_id = $(this).attr('data-shop');
        $('#link_' + shop_id).remove();
        $.ajax({
            url: module_path,
            type: 'post',
            data: 'ajax=true&method=generatesitemap&sitemap_id=' + sitemap_id,
            dataType: 'json',
            beforeSend: function () {
                $('#' + sitemap_id).attr('disabled', 'disabled');
                $('#loader_' + sitemap_id).show();
                $('.velsof_overlay').show();
                $('body').addClass('knowband_no_scroll');
                $('#' + sitemap_id).closest('td').css('position', 'absolute');
            },
            success: function (json) {
                $('#form-product_sorting').remove();
                $('#configuration_form').after(json);
                $('.velsof_overlay').hide();
                $('body').removeClass('knowband_no_scroll');
                if (json['success']) {
                    $('#' + sitemap_id).closest('table').after("<div id='link_" + shop_id + "' style='font-weight: bold;'> Sitemap: <a href='" + json['success'] + "' target='_blank'>" + json['success'] + "</a></div>");
                }
            },
            complete: function () {
                $('#' + sitemap_id).closest('td').css('position', '');
                $('#' + sitemap_id).removeAttr('disabled');
                $('#loader_' + sitemap_id).hide();
            }
        });
    });

    if ($('#group_id').val() == 'products') {
        $('#desc_id').closest('.form-group').show();
    } else {
        $('#desc_id').closest('.form-group').hide();
    }

    $(document).on('change', '#group_id', function () {
        if ($(this).val() == 'products') {
            $('#desc_id').closest('.form-group').show();
        } else {
            $('#desc_id').closest('.form-group').hide();
        }
    });
    if ($('#seo_knit_id').length) {
        var knitval = $('#seo_knit_id').val();
        if (knitval != 'undefined' && knitval != '' && getUrlParameter('duplicate_id') == 'undefined') {
            $('#group_id').attr('disabled', 'disabled');
        }
    }

    if ($('#seo_knme_id').length) {
        var knmeval = $('#seo_knme_id').val();
        if (knmeval != 'undefined' && knmeval != '') {
            $('#group_id').attr('disabled', 'disabled');
            $('#seo_tag_type').attr('disabled', 'disabled');
        }
    }

//    $('input[name="kb_seo[products]"]').closest('.input-group').after($('#kb_excluded_product_holder'));
    $('input[name="kb_seo[products]"]').parent().after($('#kb_excluded_product_holder'));
    if (typeof kbCurrentToken != 'undefined') {
        $('input[name="kb_seo[products]"]').autocomplete(
            'ajax-tab.php', {
                minChars: 2,
                max: 50,
                delay: 100,
                width: 500,
                selectFirst: false,
                scroll: false,
                dataType: 'json',
                cacheLength: 0,
                formatItem: function (data, i, max, value, term) {
                    return value;
                },
                parse: function (data) {
                    var mytab = new Array();
                    for (var i = 0; i < data.length; i++) {
                        var ref_str = '';
                        if (typeof data[i].reference != 'undefined' && data[i].reference != '') {
                            ref_str = ' (' + data[i].reference + ')';
                        }
                        mytab[mytab.length] = {data: data[i], value: data[i].name + ref_str};
                    }
                    return mytab;
                },
                extraParams: {
                    controller: 'AdminKbCron',
                    excludeIds: function () {
                        var selected_pro = $('input[name="kb_seo[excluded_products_hidden]"]').val();
                        return selected_pro.replace(/\-/g, ',');
                    },
                    token: kbCurrentToken,
                    ajax: true,
                    method: 'searchAutoCompleteKbProduct'
                }
            }
        ).result(function (event, data, formatted) {
            addProductToExcludeOrGiftCard(data, 'exclude');
        });
    }
    $(document).on('click', '.delExcludedProduct', function () {
        var delProductId = $(this).attr('name');
        deleteSelectedProduct('exclude', delProductId);
        $(this).parent().remove();
    });



    $('input[name="kb_seo[categories]"]').parent().after($('#kb_excluded_category_holder'));
    if (typeof kbCurrentToken != 'undefined') {
        $('input[name="kb_seo[categories]"]').autocomplete(
            'ajax-tab.php', {
                minChars: 2,
                max: 50,
                delay: 100,
                width: 500,
                selectFirst: false,
                scroll: false,
                dataType: 'json',
                cacheLength: 0,
                formatItem: function (data, i, max, value, term) {
                    return value;
                },
                parse: function (data) {
                    var mytab = new Array();
                    for (var i = 0; i < data.length; i++) {
                        var ref_str = '';
                        if (typeof data[i].reference != 'undefined' && data[i].reference != '') {
                            ref_str = ' (' + data[i].reference + ')';
                        }
                        mytab[mytab.length] = {data: data[i], value: data[i].name + ref_str};
                    }
                    return mytab;
                },
                extraParams: {
                    controller: 'AdminKbCron',
                    excludeIds: function () {
                        var selected_pro = $('input[name="kb_seo[excluded_categories_hidden]"]').val();
                        return selected_pro.replace(/\-/g, ',');
                    },
                    token: kbCurrentToken,
                    ajax: true,
                    method: 'searchAutoCompleteKbCategory'
                }
            }
        ).result(function (event, data, formatted) {
            addCategoryToExclude(data, 'exclude');
        });
    }
    $(document).on('click', '.delExcludedCategory', function () {
        var delCategoryId = $(this).attr('name');
        deleteSelectedCategory('exclude', delCategoryId);
        $(this).parent().remove();
    });


    $('input[name="kb_seo[manufacturers]"]').parent().after($('#kb_excluded_manufacturer_holder'));
    if (typeof kbCurrentToken != 'undefined') {
        $('input[name="kb_seo[manufacturers]"]').autocomplete(
            'ajax-tab.php', {
                minChars: 2,
                max: 50,
                delay: 100,
                width: 500,
                selectFirst: false,
                scroll: false,
                dataType: 'json',
                cacheLength: 0,
                formatItem: function (data, i, max, value, term) {
                    return value;
                },
                parse: function (data) {
                    var mytab = new Array();
                    for (var i = 0; i < data.length; i++) {
                        var ref_str = '';
                        if (typeof data[i].reference != 'undefined' && data[i].reference != '') {
                            ref_str = ' (' + data[i].reference + ')';
                        }
                        mytab[mytab.length] = {data: data[i], value: data[i].name + ref_str};
                    }
                    return mytab;
                },
                extraParams: {
                    controller: 'AdminKbCron',
                    excludeIds: function () {
                        var selected_pro = $('input[name="kb_seo[excluded_manufacturers_hidden]"]').val();
                        return selected_pro.replace(/\-/g, ',');
                    },
                    token: kbCurrentToken,
                    ajax: true,
                    method: 'searchAutoCompleteKbManufacturer'
                }
            }
        ).result(function (event, data, formatted) {
            addManufaturerToExclude(data, 'exclude');
        });
    }
    $(document).on('click', '.delExcludedManufacturer', function () {
        var delManufaturerId = $(this).attr('name');
        deleteSelectedManufaturer('exclude', delManufaturerId);
        $(this).parent().remove();
    });



    $('input[name="kb_seo[cms]"]').parent().after($('#kb_excluded_cms_holder'));
    if (typeof kbCurrentToken != 'undefined') {
        $('input[name="kb_seo[cms]"]').autocomplete(
            'ajax-tab.php', {
                minChars: 2,
                max: 50,
                delay: 100,
                width: 500,
                selectFirst: false,
                scroll: false,
                dataType: 'json',
                cacheLength: 0,
                formatItem: function (data, i, max, value, term) {
                    return value;
                },
                parse: function (data) {
                    var mytab = new Array();
                    for (var i = 0; i < data.length; i++) {
                        var ref_str = '';
                        if (typeof data[i].reference != 'undefined' && data[i].reference != '') {
                            ref_str = ' (' + data[i].reference + ')';
                        }
                        mytab[mytab.length] = {data: data[i], value: data[i].meta_title + ref_str};
                    }
                    return mytab;
                },
                extraParams: {
                    controller: 'AdminKbCron',
                    excludeIds: function () {
                        var selected_pro = $('input[name="kb_seo[excluded_manufacturers_hidden]"]').val();
                        return selected_pro.replace(/\-/g, ',');
                    },
                    token: kbCurrentToken,
                    ajax: true,
                    method: 'searchAutoCompleteKbCMS'
                }
            }
        ).result(function (event, data, formatted) {
            addCMSToExclude(data, 'exclude');
        });
    }
    $(document).on('click', '.delExcludedCMS', function () {
        var delCMSId = $(this).attr('name');
        deleteSelectedCMS('exclude', delCMSId);
        $(this).parent().remove();
    });
    hideFilters();
    $('select[name="kb_seo[group_id]"]').on('change', function () {
        hideFilters();
    });
    $('select[name="kb_seo[selected]"]').on('change', function () {
        hideFilters();
    });

    if ($('select[name="seo_tag_type"').val() != 'Normal') {
        $('input[name="seo_meta_tag_keyword"]').closest('.form-group').hide();
    }

    $('select[name="seo_tag_type"').on('change', function () {
        if ($('select[name="seo_tag_type"').val() != 'Normal') {
            $('input[name="seo_meta_tag_keyword"]').closest('.form-group').hide();
        } else {
            $('input[name="seo_meta_tag_keyword"]').closest('.form-group').show();
        }
    });

});

function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
}
;

function change_tab(a, b)
{
    $('.list-group-item').each(function () {
        $(this).removeClass('active');
    });
    $(a).addClass('active');
    switch (b) {
        case 1:
//            $("[id^='fieldset'] h3").html(general_settings);
            $('#configuration_form').show();
            $("[name^='seowizard[seo_expert_enable]']").closest('.form-group ').show();
            $("[name^='seowizard[sitemap_type]']").closest('.form-group ').show();
            $("[name^='seowizard[sitemap_shop]']").closest('.form-group ').show();
            $("[name^='seowizard[sitemap_priority]']").closest('.form-group ').show();
            $("[name^='seowizard[sitemap_frequency]']").closest('.form-group ').show();
            $("[name^='seowizard[img_enable]']").closest('.form-group ').show();
            $('#form-sitemap_table').hide();
            $("[name^='seowizard[inter_description_field]']").closest('.form-group ').hide();
            break;
        case 2:
            $('#form-sitemap_table').show();
            $('#configuration_form').hide();
            $("[name^='seowizard[seo_expert_enable]']").closest('.form-group ').hide();
            $("[name^='seowizard[sitemap_type]']").closest('.form-group ').hide();
            $("[name^='seowizard[sitemap_shop]']").closest('.form-group ').hide();
            $("[name^='seowizard[sitemap_priority]']").closest('.form-group ').hide();
            $("[name^='seowizard[sitemap_frequency]']").closest('.form-group ').hide();
            $("[name^='seowizard[img_enable]']").closest('.form-group ').hide();
            $("[name^='seowizard[inter_description_field]']").closest('.form-group ').hide();
            break;

        case 3:
            $('#configuration_form').show();
            $("[name^='seowizard[inter_description_field]']").closest('.form-group ').show();
            $('#form-sitemap_table').hide();
            $("[name^='seowizard[seo_expert_enable]']").closest('.form-group ').hide();
            $("[name^='seowizard[sitemap_type]']").closest('.form-group ').hide();
            $("[name^='seowizard[sitemap_shop]']").closest('.form-group ').hide();
            $("[name^='seowizard[sitemap_priority]']").closest('.form-group ').hide();
            $("[name^='seowizard[sitemap_frequency]']").closest('.form-group ').hide();
            $("[name^='seowizard[img_enable]']").closest('.form-group ').hide();
            break;

        case 4:
//            $('#form-sitemap_table').hide();
            $('#form-sitemap_table').hide();
            $('#configuration_form').hide();
            $("[name^='seowizard[seo_expert_enable]']").closest('.form-group ').hide();
            $("[name^='seowizard[sitemap_type]']").closest('.form-group ').hide();
            $("[name^='seowizard[sitemap_shop]']").closest('.form-group ').hide();
            $("[name^='seowizard[sitemap_priority]']").closest('.form-group ').hide();
            $("[name^='seowizard[sitemap_frequency]']").closest('.form-group ').hide();
            $("[name^='seowizard[img_enable]']").closest('.form-group ').hide();
            $("[name^='seowizard[inter_description_field]']").closest('.form-group ').hide();
            break;
        default:
    }
}

function product_optimization()
{
    $("#prod_opt").attr('disabled', true);
    $(".prod_opt_loader").show();
    $.ajax({
        type: 'GET',
        url: $("#prod_opt_url").val(),
        data: {},
        success: function (data) {
            if (data == 'Success')
            {
                $(".prod_opt_loader").hide();
                $("#prod_opt").attr('disabled', false);
                show_notification('success');
            } else {
                $(".prod_opt_loader").hide();
                $("#prod_opt").attr('disabled', false);
                show_notification('failed');
            }
        }
    });
}

function productmeta()
{
    $("#pro_meta").attr('disabled', true);
    $(".meta_pro_loader").show();
    $.ajax({
        type: 'GET',
        url: $("#pro_meta_url").val(),
        data: {},
        success: function (data) {
            if (data == 'Success')
            {
                $(".meta_pro_loader").hide();
                $("#pro_meta").attr('disabled', false);
                show_notification('success');
            } else {
                $(".meta_pro_loader").hide();
                $("#pro_meta").attr('disabled', false);
                show_notification('failed');
            }
        }
    });
}

function show_notification(type)
{
    if (type == 'success')
    {
        $(".optimization").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">x</button>' + opt_msg + '</div>');
    } else {
        $(".optimization").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">x</button>' + opt_fail_msg + '</div>');
    }
}

function addProductToExcludeOrGiftCard(data, action)
{
    if (data == null)
        return false;
    var productId = data.id_product;
    var productName = data.name;
    if (action == 'exclude') {
        var $divAccessories = $('#kb_excluded_product_holder');
    } else {
        var $divAccessories = $('#kb_gift_product_holder');
    }
    /* delete product from select + add product line to the div, input_name, input_ids elements */
    var ref_str = '';
    if (typeof data.reference != 'undefined' && data.reference != '') {
        ref_str = ' (' + data.reference + ')';
    }
    if (action == 'exclude') {
        var delButtonClass = 'delExcludedProduct';
    } else {
        var delButtonClass = 'delGiftProduct';
        $divAccessories.html('');
    }

    $divAccessories.html($divAccessories.html() + '<div class="form-control-static"><button type="button" class="' + delButtonClass + ' btn btn-default" name="' + productId + '"><i class="icon-remove text-danger"></i></button>&nbsp;' + productName + ref_str + '</div>');

    if (action == 'exclude') {
        $('input[name="kb_seo[products]"]').val('');
        $('input[name="kb_seo[products]"]').setOptions({
            extraParams: {
                controller: 'AdminKbCron',
                excludeIds: function () {
                    var selected_pro = $('input[name="kb_seo[excluded_products_hidden]"]').val();
                    return selected_pro.replace(/\-/g, ',');
                },
                token: kbCurrentToken,
                ajax: true,
                method: 'searchAutoCompleteKbProduct'
            }
        });
    }
    if (action == 'exclude') {
        var current_excluded_pro = $('input[name="kb_seo[excluded_products_hidden]"]').val();
        if (current_excluded_pro != '') {
            $('input[name="kb_seo[excluded_products_hidden]"]').val(current_excluded_pro + ',' + productId);
        } else {
            $('input[name="kb_seo[excluded_products_hidden]"]').val(productId);
        }
    }

}

function deleteSelectedProduct(type, pId)
{
    var pId = typeof pId !== 'undefined' ? pId : 0;
    if (type == 'exclude') {
        $('input[name="kb_seo[excluded_products_hidden]"]').val(
            removeIdFromCommaString(
                $('input[name="kb_seo[excluded_products_hidden]"]').val(),
                pId,
                ','
                )
            );
        $('input[name="kb_seo[products]"]').val('');
        $('input[name="kb_seo[products]"]').setOptions({
            extraParams: {
                controller: 'AdminKbCron',
                excludeIds: function () {
                    var selected_pro = $('input[name="kb_seo[excluded_products_hidden]"]').val();
                    return selected_pro.replace(/\-/g, ',');
                },
                token: kbCurrentToken,
                ajax: true,
                method: 'searchAutoCompleteKbProduct'
            }
        });
    }
}

function removeIdFromCommaString(list, value, separator) {
    separator = separator || ",";
    var values = list.split(separator);
    for (var i = 0; i < values.length; i++) {
        if (values[i] == value) {
            values.splice(i, 1);
            return values.join(separator);
        }
    }
    return list;
}


function restoreDataValidation() {
    var group_id = $('select[name="kb_seo[group_id]"]').val();
    var selected_or_all_id = $('select[name="kb_seo[selected]"]').val();

    var error = false;
    $('.error_message').remove();
    if (group_id == 'products') {
        if (selected_or_all_id == '1') {
            if ($('.delExcludedProduct').length == 0) {
                error = true;
                $('input[name="kb_seo[products]"]').closest('.input-group').after('<p class="error_message">' + seo_product_list_err_msg + '</p>');
            }
        }
    } else if (group_id == 'categories') {
        if (selected_or_all_id == '1') {
            if ($('.delExcludedCategory').length == 0) {
                error = true;
                $('input[name="kb_seo[categories]"]').closest('.input-group').after('<p class="error_message">' + seo_category_list_err_msg + '</p>');
            }
        }
    } else if (group_id == 'manufacturers') {
        if (selected_or_all_id == '1') {
            if ($('.delExcludedManufacturer').length == 0) {
                error = true;
                $('input[name="kb_seo[manufacturers]"]').closest('.input-group').after('<p class="error_message">' + seo_manufacturer_list_err_msg + '</p>');
            }
        }
    } else if (group_id == 'cms') {
        if (selected_or_all_id == '1') {
            if ($('.delExcludedCMS').length == 0) {
                error = true;
                $('input[name="kb_seo[cms]"]').closest('.input-group').after('<p class="error_message">' + seo_cms_list_err_msg + '</p>');
            }
        }
    }

    if (!error) {
        $('#restore_initial_setting').submit();
    }

}

function hideFilters() {
    var group_id = $('select[name="kb_seo[group_id]"]').val();
    var selected_or_all_id = $('select[name="kb_seo[selected]"]').val();
    $('input[name="kb_seo[products]"]').closest('.form-group').hide();
    $('input[name="kb_seo[categories]"]').closest('.form-group').hide();
    $('input[name="kb_seo[cms]"]').closest('.form-group').hide();
    $('input[name="kb_seo[manufacturers]"]').closest('.form-group').hide();

    if (group_id == 'products') {
        if (selected_or_all_id == '1') {
            $('input[name="kb_seo[products]"]').closest('.form-group').show();
        }
    }
}

$(function () {
    if ($('#configuration_form').length == 1) {
        $('#configuration_form_1,#configuration_form_2,#configuration_form_3').addClass('free-disabled');
    }

    jQuery('select[name="kb_seo[group_id]"] option').each(function () {
        if (jQuery(this).val() != '') {
            if (jQuery.inArray(jQuery(this).val(), ['products']) === -1) {
                jQuery(this).attr('disabled', 'disabled');
            }
        }
    });
    jQuery('select[name="group_id"] option').each(function () {
        if (jQuery(this).val() != '') {
            if (jQuery.inArray(jQuery(this).val(), ['products']) === -1) {
                jQuery(this).attr('disabled', 'disabled');
            }
        }
    });
    jQuery('select[name="seo_tag_type"] option').each(function () {
        if (jQuery(this).val() != '') {
            if (jQuery.inArray(jQuery(this).val(), ['Normal']) === -1) {
                jQuery(this).attr('disabled', 'disabled');
            }
        }
    });
    jQuery('select[name="desc_id"] option').each(function () {
        if (jQuery(this).val() != '') {
            if (jQuery.inArray(jQuery(this).val(), ['long']) === -1) {
                jQuery(this).attr('disabled', 'disabled');
            }
        }
    });

});


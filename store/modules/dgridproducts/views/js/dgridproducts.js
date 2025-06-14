/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Goryachev Dmitry    <dariusakafest@gmail.com>
 * @copyright 2007-2016 Goryachev Dmitry
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var current_id_product = null;

function scrollTolastEditProduct()
{
    if (current_id_product != null)
    {
        var elem = $('[data-id="'+current_id_product+'"].viewCombinations');
        elem.closest('tr').addClass('active_tr');
        setTimeout(function () {
            $('.active_tr').removeClass('active_tr');
        }, 1000);
        $('body').stop(true, true).animate({
            scrollTop: (elem.eq(0).offset().top - 150) + 'px'
        });
        current_id_product = null;
    }
}

$.fn.alignCenterScreen = function() {
    this.css("position", "fixed");
    this.css("top", ($(window).height() - this.height()) / 2 + "px");
    this.css("left", ($(window).width() - this.width()) / 2 + "px");
    return this
};
$.fn.disableSelection = function() {
    return this
        .attr('unselectable', 'on')
        .css('user-select', 'none')
        .on('selectstart', false);
};

$('.edit_field').live('dblclick', function (e) {
    e.preventDefault();
    if (!validateActiveField())
        return false;
    if (!$(this).is('.edit_category'))
    {
        $(this).closest('td').find('.form_edit_field').show();
        $(this).hide();
    }
    else
    {
        var self = this;
        current_id_product = $(self).data('id');
        $(self).addClass('loading');
        $.ajax({
            url: ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'get_categories',
                id_product: $(self).data('id')
            },
            success: function (r)
            {
                $(self).removeClass('loading');
                $('.box_categories .content_form').html(r.content);
                $('.box_categories').show();
                $('.box_categories_form').setCenterPositionAbsoluteBlockGP();
            }
        });
    }
});


$(document).ready(function () {
    $('.form_edit_field textarea[data-event-save=1]').live('save_field', function () {
        var self = this;
        if (!$(self).closest('.form_edit_field').is(':visible'))
            return false;
        $.ajax({
            url: ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'save_field',
                table: $(self).data('field-table'),
                id: $(self).data('field-id'),
                name: $(self).data('field-name'),
                lang: $(self).data('field-lang'),
                id_lang: $(self).data('field-id-lang'),
                value: $(self).val(),
                criterion: $(self).data('criterion'),
                shop: ($(self).data('shop') ? 1 : 0)
            }
        });
        var value = null;

        if ($(self).data('field-lang'))
            value = $(self).closest('td').find('.lang_' + id_default_lang).find('textarea').val();
        else
            value = $(self).closest('td').find('.form_edit_field').find('textarea').val();

        if ($(self).is('.type_price'))
         value = formatCurrency(parseFloat(value), currencyFormat, currencySign, currencyBlank);
        if ($(self).is('.type_integer'))
         value = parseInt(value);
        $(self).closest('td').find('.edit_field').show().text(value);

        if ($(self).data('field-name') == 'price_final' && ($(self).data('criterion') == 'id_product' || $(self).data('criterion') == 'id_product_attribute'))
        {
            var rate = parseInt($(self).data('rate'));
            if (isNaN(rate))
                rate = 0;
            var price = parseFloat($(self).val());
            var price_excl_tax = price / (100 + rate) * 100;
            $('[data-field-id='+parseInt($(self).data('field-id'))+'][data-field-name=price][data-criterion="'+$(self).data('criterion')+'"]').val(round(price_excl_tax, 6));
            price_excl_tax = round(price_excl_tax, 2).toString();
            price_excl_tax = formatCurrency(parseFloat(price_excl_tax), currencyFormat, currencySign, currencyBlank);
            $('[data-field-id='+parseInt($(self).data('field-id'))+'][data-field-name=price][data-criterion="'+$(self).data('criterion')+'"]').closest('td').find('.edit_field').text(price_excl_tax);
        }
        if ($(self).data('field-name') == 'price' && ($(self).data('criterion') == 'id_product' || $(self).data('criterion') == 'id_product_attribute'))
        {
            var rate = parseInt($(self).data('rate'));
            if (isNaN(rate))
                rate = 0;
            var price = parseFloat($(self).val());
            var price_excl_tax = price +  (price * rate / 100);
            $('[data-field-id='+parseInt($(self).data('field-id'))+'][data-field-name=price_final][data-criterion="'+$(self).data('criterion')+'"]').val(round(price_excl_tax, 6));
            price_excl_tax = round(price_excl_tax, 2).toString();
            price_excl_tax = formatCurrency(parseFloat(price_excl_tax), currencyFormat, currencySign, currencyBlank);
            $('[data-field-id='+parseInt($(self).data('field-id'))+'][data-field-name=price_final][data-criterion="'+$(self).data('criterion')+'"]').closest('td').find('.edit_field').text(price_excl_tax);
        }
    });

    $('.save_bool').live({
        click: function (e)
        {
            e.preventDefault();
            var self = this;
            if (parseInt($(self).val('value')))
                return false;
            $.ajax({
                url: ajax_url,
                type: 'POST',
                data: {
                    ajax: true,
                    action: 'save_field',
                    table: $(self).data('field-table'),
                    id: $(self).data('field-id'),
                    name: $(self).data('field-name'),
                    value: 1,
                    criterion: $(self).data('criterion'),
                    shop: ($(self).data('shop') ? 1 : 0)
                }
            });
            $('[data-criterion='+$(self).data('criterion')+'][data-field-name='+$(self).data('field-name')+']')
                .find('img')
                .each(function () {
                    $(this).attr('src', $(this).attr('src').replace('enabled.gif', 'disabled.gif'));
                    $(this).attr('alt', $(this).attr('alt').replace('enabled.gif', 'disabled.gif'));
                    $(this).attr('title', $(this).attr('title').replace('enabled.gif', 'disabled.gif'));
                    $(this).parent().data('value', 0);
                    $(this).parent().attr('data-value', 0);
                });
            $(self).find('img').attr('src', $(self).find('img').attr('src').replace('disabled.gif', 'enabled.gif'));
            $(self).find('img').attr('alt', $(self).find('img').attr('alt').replace('disabled.gif', 'enabled.gif'));
            $(self).find('img').attr('title', $(self).find('img').attr('title').replace('disabled.gif', 'enabled.gif'));
            $(self).find('img').parent().data('value', 1);
            $(self).find('img').parent().attr('data-value', 1);
        }
    });
    $('.form_edit_field.v15 .btn_lang button').click(function () {
         if (!$(this).is('.active'))
         {
             $(this).parent().find('.dropdown-menu').show();
            $(this).addClass('active');
         }
         else
         {
             $(this).parent().find('.dropdown-menu').hide();
             $(this).removeClass('active');
         }
    });
    $('.form_edit_field.v15 .btn_lang li a').click(function () {
        $(this).closest('.btn_lang').find('button').trigger('click');
    });
    $('.edit_field').disableSelection();
});

$('.edit_field').live('click', function () {
    if (!validateActiveField())
        return false;
    if ($(this).closest('td').is('.active_field'))
        return false;
    $('.edit_field').closest('td.active_field').find('textarea[data-event-save=1]').trigger('save_field');
    $('.edit_field').closest('td.active_field').find('.form_edit_field').hide();

    $('.edit_field').closest('td').removeClass('active_field');
    $(this).closest('td').addClass('active_field');
});
$('.type_price').live('change', function () {
    $(this).val($(this).val().replace(/,/g, '.'));
});

$('.box_categories_stage, .cancel_change_category').live('click', function () {
    $(this).closest('.box_categories').hide();
    scrollTolastEditProduct();
});

$('.viewCombinations').live({
    click: function(e)
    {
        e.preventDefault();
        $('.cancelCreateCombination').trigger('click');
        var self = this;
        current_id_product = $(self).data('id');
        $.ajax({
            url: ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'get_combinations',
                id_product: $(self).data('id')
            },
            success: function (r)
            {
                $('.stage_combinations, .form_combinations').fadeIn(500);
                initFormCombination(r, $(self).data('id'));
            }
        });
    }
});

function initFormCombination(response, id_product)
{
    $('.form_combinations .content_form').html(response.content);
    $('.form_combinations').setCenterPositionAbsoluteBlockGP();
    $('.form_combinations').find('[name=id_product]').val(id_product);
    $('.form_combinations').find('[name=product_price]').val(response.product_price);
    $('.form_combinations').find('[name=product_rate]').val(response.product_rate);
    $('.form_combinations').find('.pa_final_price').text(formatCurrency(response.product_price, currencyFormat, currencySign, currencyBlank));
    var product_images = '<ul class="list-inline"></ul>';
    if (typeof response.images != 'undefined')
    {
        for (var i in response.images)
            product_images = $(product_images).append('<li><input type="checkbox" name="id_image_attr[]" value="'+response.images[i]['id_image']+'" id="id_image_attr_'+response.images[i]['id_image']+'">' +
                '<label>'+response.images[i]['tmp_image']+'</label>' +
                '</li>');
    }
    $('.product_images').html(product_images);
}

$('.add_combination').live('click', function (e) {
    e.preventDefault();
    var form_combination = $('.form_create_combination');
    if (form_combination.is(':hidden'))
        form_combination.stop(true, true).slideDown(300);
    form_combination.find('.pa_final_price').text();
    $('.ajax_form_edit_attributes').html('');
});

$('#attribute_price_impact').live('change', function () {
    viewFinalPriceCombinationFromImpactPrice();
});

$('#attribute_price').live('blur keyup', function () {
    viewFinalPriceCombinationFromImpactPrice();
});

function calcFinalPriceCombinationFromImpactPrice()
{
    var form_combination = $('.form_create_combination');
    var price_impact = parseInt(form_combination.find('#attribute_price_impact').val());
    var price = parseFloat(form_combination.find('#attribute_price').val());
    var product_price = parseFloat(form_combination.find('[name=product_price]').val());
    var product_rate = parseFloat(form_combination.find('[name=product_rate]').val());

    var final_price = product_price;
    if (!final_price)
        return final_price;

    if (isNaN(price))
        price = 0;

    if (product_rate)
        price += (price / 100) * product_rate;

    final_price = product_price + (price_impact* price);
    return final_price;
}

function viewFinalPriceCombinationFromImpactPrice()
{
    var form_combination = $('.form_create_combination');
    var final_price = calcFinalPriceCombinationFromImpactPrice();
    form_combination.find('.pa_final_price').text(formatCurrency(final_price, currencyFormat, currencySign, currencyBlank));
}



$('.stage_combinations, .close_form_combinations').live({
    click: function (e) {
        e.preventDefault();
        $('.stage_combinations, .form_combinations').fadeOut(500);
        $('.ajax_form_edit_attributes').html('');
        scrollTolastEditProduct();
    }
});

/*edit images*/
$('.edit_image').live('click', function (e) {
    e.preventDefault();
    var self = this;
    $.ajax({
        url: ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
            ajax: true,
            action: 'get_images',
            id_product: $(self).data('id'),
            id_product_attribute: ($(self).data('pa') ? $(self).data('pa') : 0)
        },
        success: function (r) {
            $('.stage_images, .form_images').fadeIn(500);
            $('.form_images .content_form').html(r.content);
            $('.form_images').setCenterPositionAbsoluteBlockGP();
        }
    });
});
$('.stage_images, .close_form_images').live({
    click: function (e) {
        e.preventDefault();
        $('.stage_images, .form_images').fadeOut(500);
    }
});
$('.delete_image').live('click', function (e) {
    e.preventDefault();
    var self = this;
    $.ajax({
        url: ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
            ajax: true,
            action: 'delete_image',
            id_image: $(self).data('image')
        },
        success: function (r)
        {
            if (!r.hasError)
                $('.dgp_image_'+ $(self).data('image')).remove();
            else
                alert(r.message);
            if (!$('.dgp_images').find('[class^=dgp_image_]').length)
                $('.none_images').show();
        }
    });
});
$('.set_cover').live('click', function (e) {
    e.preventDefault();
    $('.form_images').addClass('loading');
    var self = this;
    if (parseInt($(self).data('value')))
        return false;
    $.ajax({
        url: ajax_url,
        type: 'POST',
        data: {
           ajax: true,
           action: 'set_cover',
           id_image: $(self).data('image')
        },
        success: function ()
        {
            $('.form_images').removeClass('loading');
            $('.set_cover')
                .find('img')
                .each(function () {
                    $(this).attr('src', $(this).attr('src').replace('enabled.gif', 'disabled.gif'));
                    $(this).attr('alt', $(this).attr('alt').replace('enabled.gif', 'disabled.gif'));
                    $(this).attr('title', $(this).attr('title').replace('enabled.gif', 'disabled.gif'));
                    $(this).parent().data('value', 0);
                    $(this).parent().attr('data-value', 0);
                });
            $(self).find('img').attr('src', $(self).find('img').attr('src').replace('disabled.gif', 'enabled.gif'));
            $(self).find('img').attr('alt', $(self).find('img').attr('alt').replace('disabled.gif', 'enabled.gif'));
            $(self).find('img').attr('title', $(self).find('img').attr('title').replace('disabled.gif', 'enabled.gif'));
            $(self).find('img').parent().data('value', 1);
            $(self).find('img').parent().attr('data-value', 1);
        }
    });
});

var reader = new FileReader();
var base64image = null;
reader.onloadend = function (e)
{
    $('.form_images').addClass('loading');
    base64image = e.target.result;
    if (base64image.indexOf('image/jpeg;') === -1)
    {
        alert(not_available_type);
        $('.form_images').removeClass('loading');
        return false;
    }
    $.ajax({
        url: ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
            ajax: true,
            action: 'upload_image',
            image: base64image,
            id_product: $('.dgp_product_image').val(),
            id_product_attribute: ($('.dgp_product_attribute_image').length ? $('.dgp_product_attribute_image').val() : 0)
        },
        success: function (r) {
            $('.form_images').removeClass('loading');
            if (!r.hasError)
            {
                $('.dgp_images').append(r.content);
                var input = $('.add_image_input').clone();
                $('.add_image_input').replaceWith(input);
            }
            else
                alert(r.message);
            if ($('.dgp_images').find('[class^=dgp_image_]').length)
                $('.none_images').hide();
        }
    });
};
$('.add_image_input').live('change', function () {
    console.log($(this).prop('files'));
    var files = $(this).prop('files');
    var blob = files[0];
    reader.readAsDataURL(blob.slice(0, blob.size, blob.type));
});
$('.in_combination').live('change', function () {
    var self = this;
    $.ajax({
        url: ajax_url,
        type: 'POST',
        dataType: 'POST',
        data: {
            ajax: true,
            action: 'set_combination_image',
            change: ($(self).is(':checked') ? 1 : -1),
            id_image: $(self).data('image'),
            id_product_attribute: $(self).data('pa')
        }
    });
});
/*edit images*/

/*edit features*/
$('.viewFeatures').live('click', function (e) {
    e.preventDefault();
    var id_product = $(this).data('id');

    current_id_product = id_product;

    $.ajax({
        url: ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
            ajax: true,
            action: 'get_features',
            id_product: id_product
        },
        success: function (r)
        {
            if (!r.hasError)
            {
                $('.form_features .content_form').html(r.content);
                $('.stage_features, .form_features').stop(true, true).fadeIn(500);
                $('.form_features').setCenterPositionAbsoluteBlockGP();
            }
        }
    });
});
$('.stage_features, .close_form_features').live('click', function (e) {
    e.preventDefault();
    $('.stage_features, .form_features').stop(true, true).fadeOut(500);
    scrollTolastEditProduct();
});
$('.saveFeatures').live('click', function () {
    $.ajax({
        url: ajax_url,
        type: 'POST',
        dataType: 'json',
        data: $('#product-features :input').serialize() + '&ajax=true&action=save_features',
        success: function (r)
        {
            if (r.hasError)
                alert(r.error);
            else
            {
                $('.stage_features, .form_features').stop(true, true).fadeOut(500);
                scrollTolastEditProduct();
            }
        }
    });
});
/*edit features*/


/*edit metatags*/
$('.viewMetaTags').live('click', function (e) {
    e.preventDefault();
    var id_product = $(this).data('id');
    current_id_product = id_product;
    $.ajax({
        url: ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
          ajax: true,
          action: 'get_meta_tags',
          id_product: id_product
        },
        success: function (r)
        {
            if (!r.hasError)
            {
                $('.form_seo .content_form').html(r.content);
                $('.stage_seo, .form_seo').stop(true, true).fadeIn(500);
                $('.form_seo').setCenterPositionAbsoluteBlockGP();
            }
        }
    });
});
$('.stage_seo, .close_form_seo').live('click', function (e) {
    e.preventDefault();
    $('.stage_seo, .form_seo').stop(true, true).fadeOut(500);
    scrollTolastEditProduct();
});
$('.saveSeo').live('click', function () {
    $('.form_seo_error').remove();
    $.ajax({
        url: ajax_url,
        type: 'POST',
        dataType: 'json',
        data: $('#product-seo :input').serialize() + '&ajax=true&action=save_seo',
        success: function (r)
        {
            if (r.hasError)
            {
                $('.form_seo .content_form #product-seo').prepend('<div class="form_seo_error alert alert-danger error">'+ r.errors.join('<br>')+'</div>');
            }
            else
            {
                $('.stage_seo, .form_seo').stop(true, true).fadeOut(500);
                scrollTolastEditProduct();
            }
        }
    });
});
/*edit metatags*/


/*edit more*/
$('.stage_popup_form, .close_form_popup').live('click', function (e) {
    e.preventDefault();
    $('.stage_popup_form, .form_popup').stop(true, true).fadeOut(500);
    scrollTolastEditProduct();
});
$('.viewAdditionalSettingProduct').live('click', function (e) {
    e.preventDefault();
    var id_product = $(this).data('id');
    current_id_product = id_product;
    $.ajax({
        url: ajax_url,
        type: 'POST',
        dataType: 'json',
        data:
        {
            ajax: true,
            action: 'get_more',
            id_product: id_product
        },
        success: function (r) {
            if (!r.hasError)
            {
                $('.form_popup').html(r.content);
                $('.stage_popup_form, .form_popup').stop(true, true).fadeIn(500);
                $('.form_popup').setCenterPositionAbsoluteBlockGP();
            }
        }
    });
});
$('.saveAdditionalSettingProduct').live('click', function () {
    tinyMCE.triggerSave();
    for (var i in languages)
       $(this).find('#'+window.input_id + languages[i]['id_lang']).val($('#'+window.input_id + languages[i]['id_lang']).tagify('serialize'));
    var data = $('.form_popup:not([name="carriers[]"]) :input').serialize();

    $('.form_popup').find('[name="carriers[]"] option').each(function () {
        data += '&carriers[]=' + $(this).attr('value');
    });

    $.ajax({
        url: ajax_url,
        type: 'POST',
        dataType: 'json',
        data: data + '&ajax=true&action=save_more',
        success: function (r)
        {
            if (r.hasError)
            {
                $('.form_popup .content_form h3').after('<div class="form_popup_error alert alert-danger error">'+ r.errors.join('<br>')+'</div>');
            }
            else
            {
                $('.stage_popup_form, .form_popup').stop(true, true).fadeOut(500);
                scrollTolastEditProduct();
            }
        }
    });
});
/*edit more*/

/*form specific price*/
$('.viewSpecificPrices').live('click', function (e) {
    e.preventDefault();
    var id_product = $(this).data('id');
    current_id_product = id_product;
    $.ajax({
        url: ajax_url,
        type: 'POST',
        dataType: 'json',
        data:
        {
            ajax: true,
            action: 'get_specific_price',
            id_product: id_product
        },
        success: function (r) {
            if (!r.hasError)
            {
                $('.form_popup').html(r.content);
                $('.stage_popup_form, .form_popup').stop(true, true).fadeIn(500);
                $('.form_popup').setCenterPositionAbsoluteBlockGP();
            }
        }
    });
});
$('#form_specific_price').live('submit', function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    $.ajax({
        url: ajax_url,
        type: 'POST',
        dataType: 'json',
        data: data + '&ajax=true&action=add_specific_price',
        success: function (r)
        {
            if (r.hasError)
            {
                $('.form_popup .content_form h3').after('<div class="form_popup_error alert alert-danger error">'+ r.errors.join('<br>')+'</div>');
            }
            else
            {
                $('.stage_popup_form, .form_popup').stop(true, true).fadeOut(500);
                scrollTolastEditProduct();
            }
        }
    });
});
/*form specific price*/


/*form advanced stock management*/
$('.viewAdvancedStockManagement').live('click', function (e) {
    e.preventDefault();
    var id_product = $(this).data('id');
    var id_product_attribute = ($(this).data('id-pa') ? $(this).data('id-pa') : 0);
    initFormAdvancedStockManagement(id_product, id_product_attribute);
});

function initFormAdvancedStockManagement(id_product, id_product_attribute)
{
    $.ajax({
        url: ajax_url,
        type: 'POST',
        dataType: 'json',
        data:
        {
            ajax: true,
            action: 'advanced_stock_management',
            id_product: id_product,
            id_product_attribute: id_product_attribute
        },
        success: function (r) {
            if (!r.hasError)
            {
                $('.form_popup').html(r.content);
                $('.stage_popup_form, .form_popup').stop(true, true).fadeIn(500);
                $('.form_popup').setCenterPositionAbsoluteBlockGP();
            }
        }
    });
}


$('.applyQuantityAdvancedStock').live('click', function (e) {
    var self = $(this);
    e.preventDefault();
    var data = $(this).closest('tr').find(':input').serialize();
    $.ajax({
        url: document.location.href.replace(document.location.hash, ''),
        type: 'POST',
        dataType: 'json',
        data: data + '&ajax=true&action=save_advanced_stock_management',
        success: function (r)
        {
            var id_product = self.data('id-product');
            var id_product_attribute = self.data('id-product-attribute');
            initFormAdvancedStockManagement(id_product, id_product_attribute);
        }
    });
});

/*form advanced stock management*/

$(window).resize(function () {
    $('.box_categories_form').each(function () {
        $(this).setCenterPositionAbsoluteBlockGP();
    });
    $('.form_combinations').setCenterPositionAbsoluteBlockGP();
    $('.form_images').setCenterPositionAbsoluteBlockGP();
    $('.form_features').setCenterPositionAbsoluteBlockGP();
    $('.form_seo').setCenterPositionAbsoluteBlockGP();
    $('.form_popup').setCenterPositionAbsoluteBlockGP();
});

if (typeof $.fn.setCenterPositionAbsoluteBlockGP == 'undefined')
    $.fn.setCenterPositionAbsoluteBlockGP = function ()
    {
        var offsetElemTop = 20;
        var scrollTop = $(document).scrollTop();
        var elemWidth = $(this).width();
        var windowWidth = $(window).width();
        $(this).css({
            top: ($(this).height() > $(window).height() ? scrollTop + offsetElemTop : scrollTop + (($(window).height()-$(this).height())/2)),
            left: ((windowWidth-elemWidth)/2)
        });
    };

//$('[name^=categoryBox], [name=id_category_default]').live('change', function () {
//    $(this).closest('.box_categories_form').find('.save_form').show();
//});
$('.save_btn').live('click', function () {
    var self = this;
    $(self).attr('disabled', true);
    var data = $(this).closest('.box_categories_form').find('[name^=categoryBox], [name=id_category_default]').serialize();
    data += '&id_product=' + $(this).data('id_product');
    data += '&ajax=true';
    data += '&action=save_categories';
    $.ajax({
        url: ajax_url,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function (r)
        {
            if (!r.hasError)
            {
                $(self).removeAttr('disabled');
                //$(self).closest('.save_form').hide();
                var category = $('#id_category_default_' + $(self).data('id_product')+ ' option:selected').text();
                $('.edit_category[data-id='+$(self).data('id_product')+']').text(category);
                $('.box_categories').hide();
                scrollTolastEditProduct();
            }
            else
            {
                $(self).removeAttr('disabled');
                alert(r.error);
            }
        }
    });
});

$(window).scroll(function () {
    var save_btn = $('.save_btn');
    var save_btn_height = save_btn.height();

    var box_categories_form = $('.box_categories_form');
    var box_categories_form_top = box_categories_form.offset().top;
    var box_categories_form_height = box_categories_form.height() + 15;

    var scroll_top = $(this).scrollTop();

    var save_form_top = 0;

    var offset_save_btn = scroll_top - box_categories_form_top;
    if (offset_save_btn > 0)
    {
        if((offset_save_btn + save_btn_height) < box_categories_form_height)
            save_form_top = offset_save_btn;
        else
            save_form_top = box_categories_form_height - save_btn_height;
    }
    else
        save_form_top = 0;

    $('.box_categories_form .save_form').css({
        top: save_form_top + 'px'
    });
});


$('._error').live('click', function () {
    $(this).removeClass('_error');
});

$('.list-action-enable').live('click', function (e) {
    e.preventDefault();
    var self = this;
    var id_product = $(this).attr('href').replace(/.*id\_product\=([0-9]+).*/, '$1');
    $.ajax({
        url: ajax_url,
        type: 'POST',
        data: {
            id_product: id_product,
            ajax: true,
            action: 'set_active'
        },
        success: function () {
            if ($(self).is('.action-enabled'))
            {
                $(self).removeClass('action-enabled').addClass('action-disabled');
                $(self).find('.icon-remove').removeClass('hidden');
                $(self).find('.icon-check').addClass('hidden');
            }
            else
            {
                $(self).removeClass('action-disabled').addClass('action-enabled');
                $(self).find('.icon-check').removeClass('hidden');
                $(self).find('.icon-remove').addClass('hidden');
            }
        }
    });
});

$('.legend_image').live('blur', function () {
   var data = $(this).closest('td').find(':input').serialize();
    $.ajax({
        url: ajax_url,
        type: 'POST',
        dataType: 'json',
        data: data + '&ajax=true&action=save_legend_image&id_image=' + $(this).data('image')
    });
});


$('[data-switcher-translate]').live('change', function () {
    var translate = $(this).closest('td').find('[data-translate]');
    translate.find('[data-lang]').hide();
    translate.find('[data-lang='+$(this).val()+']').show();
});

function changeLang(obj, id_lang)
{
    $(obj).parent().parent().parent().find('button').html($(obj).data('lang-iso') + '<span class="caret"></span>');
    $(obj).closest('.form_edit_field').find('> div[class^=lang_]').hide();
    $(obj).closest('.form_edit_field').find('.lang_'+id_lang).show();
}

function round(a,b) {
    b=b||0;
    return parseFloat(a.toFixed(b));
}

function validateActiveField()
{
    if (!$('.active_field').length)
        return true;
    if ($('.active_field :input').length && !$('.active_field :input:visible').length)
        return true;
    var field = $('.active_field [data-event-save="1"]');
    field.removeClass('_error');
    var validate = field.data('validate');
    if (typeof ValidateType[validate] != 'undefined')
    {
        if (!ValidateType[validate](field.val()))
        {
            field.addClass('_error');
            alert((typeof l[validate] != 'undefined' ? l[validate] : l['type_error']));
            return false;
        }
    }
    return true;
}

ValidateType = {
    ean13: function (value)
    {
        if (value.search(/^[0-9]{0,13}$/) !== -1)
            return true;
        return false;
    },
    upc: function (value)
    {
        if (value.search(/^[0-9]{0,12}$/) !== -1)
            return true;
        return false;
    }
};

$(function () {
    $('[name="attribute_group"]').live('change', function () {
        $('[data-group]').hide();
        $('[data-group="'+$(this).val()+'"]').show();
    });

    $('.add_attr').live('click', function () {
        var form_combination = $(this).closest('.form_cc');
        if (form_combination.find('[groupid="'+form_combination.find('[name=attribute_group]').val()+'"]').length)
        {
            alert(exists_attr);
            return false;
        }
        form_combination.find('[id=product_att_list]').append('<option value="'+form_combination.find('[data-group="'+form_combination.find('[name=attribute_group]').val()+'"] [name=attribute]').val()+'" groupid="'+form_combination.find('[name=attribute_group]').val()+'">'+form_combination.find('[name=attribute_group] option:selected').text()+'&nbsp;&nbsp; : '+form_combination.find('[data-group="'+form_combination.find('[name=attribute_group]').val()+'"] [name=attribute] option:selected').text()+'</option>');
    });
    $('.delete_attr').live('click', function () {
        var form_combination = $(this).closest('.form_cc');
        form_combination.find('[id=product_att_list] option:selected').remove();
    });
    $('[name="attribute_group"]').trigger('change');

    $('#form-configuration .delete').live('click', function (e) {
        e.preventDefault();
        var that = this;
        $.ajax({
            url: $(this).attr('href') + '&ajax=true&action=delete_combination',
            dataType: 'json',
            success: function (r)
            {
                if (!r.hasError)
                    $(that).closest('tr').remove();
            }
        });
    });

    $('.cancelEditAttributes').live('click', function (e) {
        e.preventDefault();
        $('.ajax_form_edit_attributes').html('');
    });

    $('.saveAttributes').live('click', function (e) {
        e.preventDefault();
        $('.ajax_form_edit_attributes').find('.error').remove();
        var data = {
            ajax: true,
            action: 'save_edit_attributes',
            attribute_combination_list: [],
            id_product_attribute: parseInt($('.form_edit_attributes').find('[name=id_product_attribute]').val())
        };
        $('.form_edit_attributes [id=product_att_list] option').each(function () {
            data['attribute_combination_list'].push($(this).attr('value'));
        });

        $.ajax({
            url: ajax_url,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (r)
            {
                if (r.hasError)
                {
                    $('.ajax_form_edit_attributes').prepend('<div class="alert alert-danger error"><ul><li>'+ r.errors.join('</li><li>')+'</li></ul></div>');
                }
                else
                {
                    $('.ajax_form_edit_attributes').html('');
                    $('.edit_attributes[data-id='+data['id_product_attribute']+']').text(r.attributes_name);
                }
            }
        });
    });
});

$('.cancelCreateCombination').live('click', function () {
   $('.form_create_combination').stop(true, true).slideUp(300);
    $('#form_create_combination [name=attribute_group]').trigger('change');
   $('#product_att_list option').remove();
});

$('.createCombination').live('click', function ()
{
    $('#form_create_combination .error').remove();
    var data = {};
    data['ajax'] = true;
    data['action'] = 'add_combination';
    data['id_product'] = $('[name=id_product]').val();
    data['attribute_reference'] = $('[name=attribute_reference]').val();
    data['attribute_ean13'] = $('[name=attribute_ean13]').val();
    data['attribute_upc'] = $('[name=attribute_upc]').val();
    data['attribute_wholesale_price'] = $('[name=attribute_wholesale_price]').val();
    data['attribute_price'] = $('[name=attribute_price]').val();
    data['attribute_price_impact'] = $('[name=attribute_price_impact]').val();
    data['attribute_weight_impact'] = $('[name=attribute_weight_impact]').val();
    data['attribute_weight'] = $('[name=attribute_weight]').val();
    data['attribute_unit_impact'] = $('[name=attribute_unit_impact]').val();
    data['attribute_minimal_quantity'] = $('[name=attribute_minimal_quantity]').val();
    data['available_date_attribute'] = $('[name=available_date_attribute]').val();
    data['attribute_unity'] = $('[name=attribute_unity]').val();
    data['attribute_combination_list'] = [];
    $('#form_create_combination [id=product_att_list] option').each(function () {
        data['attribute_combination_list'].push($(this).attr('value'));
    });
    data['id_image_attr'] = [];
    $('[name="id_image_attr[]"]:checked').each(function () {
        data['id_image_attr'].push($(this).attr('value'));
    });
    $.ajax({
        url: ajax_url,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function (r)
        {
            if (r.hasError)
            {
                $('#form_create_combination').prepend('<div class="alert alert-danger error">'+r.errors.join('<br>')+'</div>');
                $('body').animate({
                    scrollTop: $('#form_create_combination .error').offset().top - 30
                });
            }
            else
            {
                //$('.cancelCreateCombination').trigger('click');
                initFormCombination(r, $('[name=id_product]').val());
                alert(combination_create_success);
            }
        }
    });
});

$('.edit_attributes').live('click', function (e) {
    e.preventDefault();
    var id = parseInt($(this).data('id'));

    $.ajax({
        url: ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
            ajax: true,
            action: 'edit_attributes',
            id_product_attribute: id
        },
        success: function (r) {
            $('.ajax_form_edit_attributes').html(r.content);
            $('body').stop(true, true).animate({
                scrollTo: ($('.ajax_form_edit_attributes').offset().top + 30) + 'px'
            }, 300);
        }
    });
});

$(function () {
   $('[name="sp_id_product_attribute"], [name="leave_bprice"], [name="sp_reduction_type"]').live('change', function () {
       calcSPFinalPrice();
   });
    $('[name="sp_reduction"], [name="sp_price"]').live('keyup', function () {
        calcSPFinalPrice();
    });
    function calcSPFinalPrice()
    {
        var id_product_attribute = $('[name="sp_id_product_attribute"]').val();
        var leave_price = $('[name="leave_bprice"]').is(':checked');
        var price = parseFloat($('[name="sp_price"]').val());
        var reduction = parseFloat($('[name="sp_reduction"]').val());
        var reduction_type = $('[name="sp_reduction_type"]').val();
        var final_price = 0;
        if (id_product_attribute == 0)
            final_price = product_price;
        else
        {
            if (typeof combination_prices[id_product_attribute] != 'undefined')
                final_price = combination_prices[id_product_attribute];
            else
                final_price = product_price;
        }
        if (!leave_price)
            final_price = price;

        if (reduction_type == 'percentage')
            final_price -= (final_price/100*reduction);
        else
            final_price -= reduction;
        $('.sp_final_price').text(formatCurrency(final_price, currencyFormat, currencySign, currencyBlank));
    }
});

function updateUnitPriceWithTax(elem)
{
    var price = parseFloat($(elem).val());
    var price_with_tax = price + (price / 100 * tax_rate);
    $('#unit_price_with_tax').text(roundNumber(price_with_tax, 6));
}

function roundNumber(number, precision)
{
    precision = Math.pow(10, precision);
    return parseInt(number * precision) / precision;
}

function updateUnitySecond(elem)
{
    $('#unity_second').text($(elem).val());
}
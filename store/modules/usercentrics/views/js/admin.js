/**
 * usercentrics
 *
 * @author    silbersaiten <info@silbersaiten.de>
 * @copyright 2020 silbersaiten
 * @license   See joined file licence.txt
 * @category  Module
 * @support   silbersaiten <support@silbersaiten.de>
 * @version   1.0.0
 * @link      http://www.silbersaiten.de
 */

var usercentrics_configure = {
    init: function () {
        $('input[type="radio"]').each(function() {
            if ($(this).is(':checked')) {
                $('p.help-block[for="' + $(this).attr('id') + '"]').slideDown(400);
                $('.show_for_' + $(this).attr('id')).closest('.form-group').slideDown(400);
                //$(this).closest('.form-group').find('.help-block').not('[for]').slideUp(400);
            } else {
                $('p.help-block[for="' + $(this).attr('id') + '"]').slideUp(400);
                $('.show_for_' + $(this).attr('id')).closest('.form-group').slideUp(400);
                //$(this).closest('.form-group').find('.help-block').not('[for]').slideDown(400);
            }
        });

        $('input[type="radio"]').on('click', function() {
            $('input[name="'+$(this).attr('name')+'"]').each(function() {
                if ($(this).is(':checked')) {
                    $('p.help-block[for="' + $(this).attr('id') + '"]').slideDown(400);
                    $('.show_for_' + $(this).attr('id')).closest('.form-group').slideDown(400);
                    //$(this).closest('.form-group').find('.help-block').not('[for]').slideUp(400);
                } else {
                    $('p.help-block[for="' + $(this).attr('id') + '"]').slideUp(400);
                    $('.show_for_' + $(this).attr('id')).closest('.form-group').slideUp(400);
                    //$(this).closest('.form-group').find('.help-block').not('[for]').slideDown(400);
                }
            });
        })

        $('.uc-template-category').on('click', function() {
            $('.uc-template-category').removeClass('active');
            $(this).addClass('active');
            $('.uc-template-templates').find('.uc-constent-template').addClass('hide');
            $('.uc-template-templates').find('.uc-constent-template[data-category-slug="'+$(this).data('slug')+'"]').removeClass('hide');
            $('#uc_category_slug').val($(this).data('slug'));
            $('.uc-template-templates-header').removeClass('hide');
        });

        $('#addVendor').on('click', function(e) {
            e.preventDefault();
            var category_slug = $('#uc_category_slug').val();
            var template_id = $('#uc_template_id').val();
            var template_name = $('#uc_template_name').val();
            var template_version = $('#uc_template_version').val();
            var template_image = $('#uc_template_image').val();
            $.ajax({
                url: 'index.php?controller=AdminModules&configure=usercentrics&module_name=usercentrics&ajax=1&action=addVendor&token='+token,
                data: 'category_slug=' +category_slug+ '&template_id='+template_id,
                dataType: 'json',
                cache: false,
                method: 'POST',
                success: function(json) {
                    if (json.success) {
                        var template = $('.uc-template-templates .uc-constent-template-empty').clone(true, true);
                        template.appendTo('.uc-template-templates');

                        template.removeClass('uc-constent-template-empty')
                        template.addClass('uc-constent-template')

                        template.attr('data-category-slug', category_slug)
                        template.attr('data-id', template_id);
                        template.find('label').html(template_name + ' (' + template_version + ')');
                        template.find('img').attr('src', template_image);
                        template.find('a.deleteVendor').attr('data-id', template_id);
                        template.find('a.deleteVendor').attr('data-category-slug', category_slug);
                        template.removeClass('hide');

                        var cur_quan = 1 + parseInt($('.uc-template-category[data-slug="'+category_slug+'"]').find('span.quantity_consent_templates').text());
                        $('.uc-template-category[data-slug="'+category_slug+'"]').find('span.quantity_consent_templates').text(cur_quan);
                        if (cur_quan > 0) {
                            $('.uc-template-category[data-slug="'+category_slug+'"]').find('a.deleteCategory').attr('disabled', 'disabled');
                        }

                        $('.uc-template-templates-header .messages').html(json.message).removeClass('alert-danger').addClass('alert').addClass('alert-success');
                        $('.uc-template-templates-header .messages').show();
                        $('.uc-template-templates-header .messages').fadeOut(5000);
                    } else {
                        $('.uc-template-templates-header .messages').html(json.message).removeClass('alert-success').addClass('alert').addClass('alert-danger');
                        $('.uc-template-templates-header .messages').show();
                        $('.uc-template-templates-header .messages').fadeOut(5000);
                    }
                }
            });
        });

        $('.deleteVendor').on('click', function(e) {
            e.preventDefault();

            var template_id = $(this).data('id');
            var category_slug = $(this).data('category-slug');

            $.ajax({
                url: 'index.php?controller=AdminModules&configure=usercentrics&module_name=usercentrics&ajax=1&action=deleteVendor&token='+token,
                data: 'template_id='+template_id,
                dataType: 'json',
                cache: false,
                method: 'POST',
                success: function(json) {
                    if (json.success) {
                        var cur_quan = parseInt($('.uc-template-category[data-slug="'+category_slug+'"]').find('span.quantity_consent_templates').text()) - 1;
                        cur_quan = (cur_quan < 0)?0:cur_quan;
                        $('.uc-template-category[data-slug="'+category_slug+'"]').find('span.quantity_consent_templates').text(cur_quan);
                        if (cur_quan == 0) {
                            $('.uc-template-category[data-slug="'+category_slug+'"]').find('a.deleteCategory').removeAttr('disabled');
                        }

                        $('.uc-constent-template[data-id="'+template_id+'"]').remove();
                        $('.uc-template-templates-header .messages').html(json.message).removeClass('alert-danger').addClass('alert').addClass('alert-success');
                        $('.uc-template-templates-header .messages').show();
                        $('.uc-template-templates-header .messages').fadeOut(5000);
                    } else {
                        $('.uc-template-templates-header .messages').html(json.message).removeClass('alert-success').addClass('alert').addClass('alert-danger');
                        $('.uc-template-templates-header .messages').show();
                        $('.uc-template-templates-header .messages').fadeOut(5000);
                    }
                }
            });
        });

        $('.deleteCategory').on('click', function(e) {
            e.preventDefault();

            var category_slug = $(this).data('slug');

            $.ajax({
                url: 'index.php?controller=AdminModules&configure=usercentrics&module_name=usercentrics&ajax=1&action=deleteCategory&token='+token,
                data: 'category_slug='+category_slug,
                dataType: 'json',
                cache: false,
                method: 'POST',
                success: function(json) {
                    if (json.success) {
                        $('.uc-template-category[data-slug="'+category_slug+'"]').remove();
                        $('.uc-template-categories-header .messages').html(json.message).removeClass('alert-danger').addClass('alert').addClass('alert-success');
                        $('.uc-template-categories-header .messages').show();
                        $('.uc-template-categories-header .messages').fadeOut(5000);
                    } else {
                        $('.uc-template-categories-header .messages').html(json.message).removeClass('alert-success').addClass('alert').addClass('alert-danger');
                        $('.uc-template-categories-header .messages').show();
                        $('.uc-template-categories-header .messages').fadeOut(5000);
                    }
                }
            });
        });

        $('#addCategory').on('click', function(e) {
            e.preventDefault();

            var data = {};
            $('.uc_newcategory_form input[type="text"], .uc_newcategory_form input[type="checkbox"], .uc_newcategory_form textarea').each(
                function(index, item) {
                    if ($(this).attr('type') == 'checkbox') {
                        if ($(this).is(':checked')) {
                            data[$(this).attr('name')] = $(this).val();
                        } else {
                            data[$(this).attr('name')] = 'false';
                        }
                    } else {
                        data[$(this).attr('name')] = $(this).val();
                    }
                }
            );

            $.ajax({
                url: 'index.php?controller=AdminModules&configure=usercentrics&module_name=usercentrics&ajax=1&action=addCategory&token='+token,
                data: data,
                dataType: 'json',
                cache: false,
                method: 'POST',
                success: function(json) {
                    if (json.success) {
                        console.log(json.slug);
                        var slug = json.slug;
                        var uc_languages = $('.uc_newcategory_form input[name="uc_languages"]').val().split(',');
                        var template = $('.uc-template-categories .uc-template-category-empty').clone(true, true);
                        template.appendTo('.uc-template-categories');
                        template.removeClass('uc-template-category-empty');
                        template.addClass('uc-template-category');

                        $.each(uc_languages, function(index, value){
                            if (value != '') {
                                template.find('label.translatable-field.lang-' + value).text(data['catname_' + value]);
                                template.find('p.translatable-field.lang-' + value).text(data['catdescription_' + value]);
                                template.find('span.quantity_consent_templates').text('0');
                            }
                        });
                        template.attr('data-slug', slug);
                        template.find('a.deleteCategory').attr('data-slug', slug);
                        template.removeClass('hide');

                        $('.uc_newcategory_form_messages').html(json.message).removeClass('alert-danger').addClass('alert').addClass('alert-success');
                        $('.uc_newcategory_form_messages').show();
                        $('.uc_newcategory_form_messages').fadeOut(5000);
                    } else {
                        $('.uc_newcategory_form_messages').html(json.message).removeClass('alert-success').addClass('alert').addClass('alert-danger');
                        $('.uc_newcategory_form_messages').show();
                        $('.uc_newcategory_form_messages').fadeOut(5000);
                    }
                }
            });
        });



        $('#open_uc_newcategory_form').on('click', function(e) {
            $('.uc_newcategory_form').show();
            $('#open_uc_newcategory_form').hide();
        });
        $('#close_uc_newcategory_form').on('click', function(e) {
            $('.uc_newcategory_form').hide();
            $('#open_uc_newcategory_form').show();
        });

        $('input[type="checkbox"].catdisable_autotrans').on('click', function(e) {
            // if current language is enabled, then other disabled
           if (!$(this).is(':checked')) {
               $('.catdisable_autotrans').not($(this)).prop( "checked", true );
           }
        });


        $('#languagesAvailable').select2(
            {
                width: '100%',
                maximumSelectionSize: $('#languagesAvailable').data('maximum-selection'),
                maximumSelectionLength: $('#languagesAvailable').data('maximum-selection'),
            }
        );

        $('#technology').select2({
            debug: true,
            placeholder: '',
            minimumInputLength: 2,
            width: '50%',
            dropdownCssClass: "bootstrap",
            id: function(e) { return e.templateId; },
            ajax: {
                url: "index.php?controller=AdminModules&configure=usercentrics&module_name=usercentrics&ajax=1&action=getConsentTemplates",
                dataType: 'json',
                quietMillis: 250,
                data: function (term) {
                    return {
                        q: term,
                        token: token
                    };
                },
                results: function (data) {
                    var excludeIds = new Array();//getSelectedIds();
                    var returnIds = new Array();
                    if (data) {
                        for (var i = data.length - 1; i >= 0; i--) {
                            var is_in = 0;
                            for (var j = 0; j < excludeIds.length; j ++) {
                                if (data[i].id == excludeIds[j][0] && (typeof data[i].id_product_attribute == 'undefined' || data[i].id_product_attribute == excludeIds[j][1]))
                                    is_in = 1;
                            }
                            if (!is_in)
                                returnIds.push(data[i]);
                        }
                        return {
                            results: returnIds
                        }
                    } else {
                        return {
                            results: []
                        }
                    }
                }
            },
            formatResult: usercentrics_configure.templateFormatResult,
            formatSelection: usercentrics_configure.templateFormatSelection,
        }).on("change", function(e) {
            console.log(e);
        }).on("select2:selecting", function(e) {
            $('#uc_template_id').val(e.val);
            $('#uc_template_id').val(e.val);
            $('#uc_template_name').val(e.object.name);
            $('#uc_template_version').val(e.object.version);
            if (typeof e.object.image != 'undefined') {
                $('#uc_template_image').val(e.object.image);
            } else {
                $('#uc_template_image').val('');
            }
        }).on("select2-selecting", function(e) {
            console.log(e.val);
            console.log(e.object);
            $('#uc_template_id').val(e.val);
            $('#uc_template_name').val(e.object.name);
            $('#uc_template_version').val(e.object.version);
            console.log(e.object.image);
            if (typeof e.object.image != 'undefined') {
                $('#uc_template_image').val(e.object.image);
            } else {
                $('#uc_template_image').val('');
            }
        });

        this.setBusinessComingMessageToDisabledFields();
    },
    templateFormatResult: function (item) {
        itemTemplate = "<div class='media'>";
        itemTemplate += "<div class='pull-left'>";
        itemTemplate += "<img class='media-object' width='25' src='" + item.image + "' alt=''>";
        itemTemplate += "</div>";
        itemTemplate += "<div class='media-body'>";
        itemTemplate += "<h6 class='media-heading'>" + item.name + "</h6>";
        itemTemplate += "</div>";
        itemTemplate += "</div>";
        return itemTemplate;
    },
    templateFormatSelection: function (item) {
        return item.name;
    },
    /** For disabling */
    setBusinessComingMessageToDisabledFields: function(){
        var forError = $('.business_coming');
        forError.each(function(){
            $(this).closest('.form-group').find('label.control-label').append('<p class="business_coming_msg badge badge-info">Business coming soon</p>');
        });
    }
}

$(function(){
    usercentrics_configure.init();
});

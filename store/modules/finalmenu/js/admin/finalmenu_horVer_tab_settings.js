$(document).ready(function() {


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // TAB CONTROL - MISCELLANEOUS JS
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $('.tab-view-select').change(function() {
        var selectedView = $(this).val(),
            selected;
        $('.select-wrapper').hide();

        if(selectedView == 0)
            selected = 'advance-menu';
        else if (selectedView == 1)
            selected = 'simple-menu';

        $('#' + selected).show();
    });

    $('.iframe-upload').fancybox({
        'width': 900,
        'height': 600,
        'type': 'iframe',
        'autoScale': false,
        'autoDimensions': false,
        'fitToView': false,
        'autoSize': false,
        onUpdate: function() {
            $('.fancybox-iframe').contents().find('a.link').data('field_id', $(this.element).data("input-name"));
            $('.fancybox-iframe').contents().find('a.link').attr('data-field_id', $(this.element).data("input-name"));
        },
        afterShow: function() {
            $('.fancybox-iframe').contents().find('a.link').data('field_id', $(this.element).data("input-name"));
            $('.fancybox-iframe').contents().find('a.link').attr('data-field_id', $(this.element).data("input-name"));
        }
    });

    // ICON PICKER
    $('html').click(function() {
        $('.icons-selector').hide();
    });

    $('.icon-select').click(function(e) {
        e.stopPropagation();
        $('.icons-selector').show();
    });

    $('.icons-selector i').click(function() {
        var selectedClass = $(this).attr('class');
        $('#tab_icon').val(selectedClass);
    });

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // VIEW OBJECT CREATION
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // OBJECT SKELETON
    var tabObject = {
        // simple tab options
        "simple_menu_select": "",
        "category_limit": undefined,
        "product_ID": undefined,
        "link_url": {},
        "link_title": {},
        "tab_link_new_window": 0,
        // advance tab options
        "tab_wrapper_width": 12,
        "tab_wrapper_bg_color": "#ededed",
        "tab_blocks_border_color": "#dddddd",
        "tab_background_link": {},
        "tab_bg_img_repeat": "repeat",
        "tab_bg_img_position": "top-left",
        "tab_bg_img_pdng_left": 0,
        "tab_bg_img_pdng_right": 0,
        "tab_bg_img_pdng_top": 0,
        "tab_bg_img_pdng_bottom": 0,
        "blocks_count": parseInt($('#hidden-inputs input[name="blocks-id-count"]').val()),
        "blocks": {}
    };

    // Check whether object is presented, if so then load it.
    var hiddenObject = $('#advance_tab_object').val();
    if (hiddenObject.length !== 0) {
        var oldTabObject = JSON.parse(hiddenObject);
        $.extend(tabObject, oldTabObject);

        // object is presented, load basic setup
        if(tabObject.hasOwnProperty('selected_view') && tabObject['selected_view'] == 1) {
            $('#simple_menu_select').val(tabObject['simple_menu_select']);
            $('#category_limit').val(tabObject['category_limit']);
            $('#product_ID').val(tabObject['product_ID']);
            $('input[name=tab_link_new_window]').val(tabObject['tab_link_new_window']);

            if (typeof languages != "undefined") {
                languages.forEach(function(langObject) {
                    $('#link_url_' + langObject.id_lang).val(tabObject['link_url'][langObject.id_lang]);
                    $('#link_title_' + langObject.id_lang).val(tabObject['link_title'][langObject.id_lang]);
                });
            } else {
                $('#link_url_' + id_language).val(tabObject['link_url'][id_language]);
                $('#link_title_' + id_language).val(tabObject['link_title'][id_language]);
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ADVANCE VIEW - OBJECT CONTROL
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // ACTIONS
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $(".draggable-item").draggable({
        cursor: "move",
        connectToSortable: "#tab-view",
        cursorAt: {
            top: 20,
            left: 20
        },
        helper: function(event) {
            return $("<div> <div id='tab-helper' class='tab-wrapper'><div class='inner-tab-wrapper'><div class='draggable-tab-header'><i class='icon-pencil'></i><i class='icon-times'></i></div</div> </div> </div>");
        }
    });

    $('#tab-view').sortable({
        placeholder: 'tab-placeholder',
        connectWith: "#sidebar-tab-options",
        receive: function(event, ui) {
            tabObject['blocks_count']++;
            var newItem = $('#tab-view .draggable-item'),
                type = newItem.children('input[class="type"]').val(),
                blockID = type + '_' + tabObject['blocks_count'],
                title;

            var itemContent = newItem.html();
            newItem.html("");
            newItem.append('<div class="inner-tab-wrapper"></div>');
            newItem.children('.inner-tab-wrapper').append(itemContent);

            newItem.find('span').removeClass().addClass('tab-wrapper-title');
            switch (type) {
                case 'cms-pages':
                    title = 'CMS pages';
                    break;
                case 'categories':
                    title = 'Categories';
                    break;
                case 'suppliers':
                    title = 'Suppliers';
                    break;
                case 'manufacturers':
                    title = 'Manufacturers';
                    break;
                case 'products':
                    title = 'Product';
                    break;
                case 'search-field':
                    title = 'Search field';
                    break;
                case 'custom-image':
                    title = 'Custom image';
                    break;
                case 'custom-html':
                    title = 'Custom html';
                    break;
                case 'cms-page':
                    title = 'CMS page';
                    break;
                case 'custom-link':
                    title = 'Custom link';
                    break;
            }

            var newBlock = {
                "name": blockID,
                "title": title,
                "type": type
            };

            appendFields(newBlock, type);
            tabObject['blocks'][blockID] = newBlock;

            newItem.removeClass().addClass('tab-wrapper tab-disable col-lg-2').attr('id', blockID);
            newItem.children('.inner-tab-wrapper').append("<div class='draggable-tab-header'><i class='icon-pencil'></i><i class='icon-times'></i></div><input type='hidden' id='block-ID' value='" + blockID + "'>");
        },
        start: function(event, ui) {
            var itemClass = ui.item.attr('class');
            if (itemClass == 'draggable-item ui-draggable')
                itemClass = 'tab-wrapper col-lg-2';

            $('.tab-placeholder').addClass(itemClass);
        }
    });

    // TAB WRAPPER WIDTH CHANGE LISTENER
    $('#tab_wrapper_width').change(function() {
        var selectedValue = $(this).val();
        $('#tab-view').removeClass().addClass('col-lg-' + selectedValue + ' ui-sortable');
    });

    $('.category-view-select').change(function() {
        var selectedView = $(this).val();

        if (selectedView == 'grip') {
            $('.category-grip-view').show();
            $('#add-category').show();
            $('#category-info').show();
        } else {
            $('.category-grip-view').hide();
            $('#add-category').hide();
            $('#category-info').hide();
        }
    });

    $('#advance-menu #tab-view').delegate('> div', 'mouseenter', function() {
        $(this).removeClass('tab-disable').addClass('tab-active');
    }).delegate('> div', 'mouseleave', function() {
        $(this).removeClass('tab-active').addClass('tab-disable');
    });

    $('input[name="manufacturers-view"], input[name="suppliers-view"]').change(function() {
        var value = $(this).val();

        if (value == 'multi')
            $(this).parents('.option-block').find('.view-grip').slideUp();
        else
            $(this).parents('.option-block').find('.view-grip').slideDown();
    });

    // CATEGORY GRIP VIEW EVENTS
    // ADD CATEGORY
    $('#add-category').on("click", function() {
        var selectBox = $('#categories .select-box'),
            selectedOption = $('option:selected', selectBox);
        var selectedVal = selectedOption.attr('id'),
            selectedText = selectedOption.text(),
            blockID = $('input[name=active-block-ID]').val();

        if(!selectedOption.prop('disabled')) {

            selectedOption.prop('disabled', true);
            var links = {};
            var desc = {};

            if (typeof languages != "undefined") {
                languages.forEach(function(langObject) {
                    links[langObject.id_lang] = $('#category-small-image-' + langObject.id_lang).val();
                    desc[langObject.id_lang] = $('#category-short-desc-' + langObject.id_lang).val();
                });
            } else {
                links[id_language] = $('#category-small-image-' + id_language).val();
                desc[id_language] = $('#category-short-desc-' + id_language).val();
            }

            tabObject['blocks'][blockID]['grip_view'][selectedVal] = {
                "name": selectedVal,
                "image_link": links,
                "category_desc": desc
            };
            $('#category-info input[type=text]').val('');
            $('#category-info textarea').val('');
            $('#categories .selected-grip').append('<option val="' + selectedVal + '" id="' + selectedVal + '">' + selectedText + '</option>');
        }
    });

    // REMOVE CATEGORY
    $('#remove-category').on("click", function() {
        var selectedVal = $('.selected-grip option:selected').attr('id'),
            blockID = $('input[name=active-block-ID]').val();

        $('.selected-grip option#' + selectedVal).remove();
        $('input.' + selectedVal + '.' + blockID).remove();
        delete tabObject.blocks[blockID]['grip_view'][selectedVal];

        $('#category-info input[type=text]').val('');
        $('#category-info textarea').val('');

        $('.select-box option#' + selectedVal).prop('disabled', false);
    });

    // view selected option
    $('#categories .selected-grip').on("click", function() {
        var selectedVal = $('option:selected', this).attr('id'),
            link, desc,
            blockID = $('input[name=active-block-ID]').val();

        if(selectedVal) {
            if (typeof languages != "undefined") {
                languages.forEach(function(langObject) {
                    $('#category-small-image-' + langObject.id_lang).val(tabObject['blocks'][blockID]['grip_view'][selectedVal]['image_link'][langObject.id_lang]);
                    $('#category-short-desc-' + langObject.id_lang).val(tabObject['blocks'][blockID]['grip_view'][selectedVal]['category_desc'][langObject.id_lang]);
                });
            } else {
                $('#category-small-image-' + id_language).val(tabObject['blocks'][blockID]['grip_view'][selectedVal]['image_link'][id_language]);
                $('#category-short-desc-' + id_language).val(tabObject['blocks'][blockID]['grip_view'][selectedVal]['category_desc'][id_language]);
            }
        }
    });

    $('#advance-menu #tab-view').delegate('.draggable-tab-header i', 'click', function() {
        var itemClass = $(this).attr('class'),
            parent = $(this).closest('.tab-wrapper'),
            blockID = parent.find('input[id="block-ID"]').val(),
            type = parent.find('input[class="type"]').val();

        if (itemClass == 'icon-times') {
            parent.remove();
            delete tabObject['blocks'][blockID];
            $('div.option-block').slideUp();
            $('input[name="active-block-ID"]').val('');
        } else if (itemClass == 'icon-pencil') {
            $('div.option-block').slideUp();
            $('input[name="active-block-ID"]').val(blockID);
            $('input[name="active-block-type"]').val(type);
            insertOptions(blockID, type);
            $('div#block-layout').slideDown();
            $('div#' + type).slideDown();
        }
    });

    $('div.advmenu-update-block').on("click", function(e) {
        e.preventDefault();
        var width = $('#block-layout #block-width').val();
        var blockID = $('input[name=active-block-ID]').val();
        var blockType = $('input[name=active-block-type]').val();

        $('#tab-view div#' + blockID).removeClass().addClass('tab-wrapper tab-disable col-lg-' + width);

        saveSettings(blockType, blockID);
        showSuccessMessage(settingsUpdated);
    });

    // PRODUCT AUTOCOMPLETE - from prestashop core
    $('#product-autocomplete').autocomplete('ajax_products_list.php', {
        minChars: 1,
        autoFill: true,
        max: 20,
        matchContains: true,
        mustMatch: true,
        scroll: false,
        cacheLength: 0,
        extraParams: {
            excludeIds: getProductsIds()
        },
        formatItem: function(item) {
            return item[0] + ' - ' + item[1];
        }
    }).result(function(event, data, formatted) {
        addProduct(event, data, formatted);
    });

    $('#selected-products').delegate('.del-product', 'click', function() {
        delProduct($(this).attr('name'));
    });
    

    // FUNCTIONS 
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // PRODUCT AUTOCOMPLETE - from prestashop core
   ///////////////////////////////////////////////////////////////////////////////////////////
    
    function addProduct(event, data, formatted) {
        if (data === null)
            return false;

        var productId = data[1];
        var productName = data[0];

        var $selectedProducts = $('#selected-products');
        var $productsIDs = $('#products-IDs');
        var $productsNames = $('#products-names');

        /* delete product from select + add product line to the div, input_name, input_ids elements */
        $selectedProducts.html($selectedProducts.html() + '<div class="form-control-static"><button type="button" class="del-product btn btn-default" name="' + productId + '"><i class="icon-remove text-danger"></i></button>&nbsp;' + productName + '</div>');
        $productsNames.val($productsNames.val() + productName + '¤');
        $productsIDs.val($productsIDs.val() + productId + '-');

        $('#product-autocomplete').val('');
        $('#product-autocomplete').setOptions({
            extraParams: {
                excludeIds: getProductsIds()
            }
        });
    }

    function delProduct(id) {
        var div = getE('selected-products');
        var input = getE('products-IDs');
        var name = getE('products-names');

        // Cut hidden fields in array
        var inputCut = input.value.split('-');
        var nameCut = name.value.split('¤');

        if (inputCut.length != nameCut.length)
            return jAlert('Bad size');

        // Reset all hidden fields
        input.value = '';
        name.value = '';
        div.innerHTML = '';
        for (var i in inputCut) {
            // If empty, error, next
            if (!inputCut[i] || !nameCut[i])
                continue;

            // Add to hidden fields no selected products OR add to select field selected product
            if (inputCut[i] != id) {
                input.value += inputCut[i] + '-';
                name.value += nameCut[i] + '¤';
                div.innerHTML += '<div class="form-control-static"><button type="button" class="del-product btn btn-default" name="' + inputCut[i] + '"><i class="icon-remove text-danger"></i></button>&nbsp;' + nameCut[i] + '</div>';
            }
        }

        $('#product-autocomplete').setOptions({
            extraParams: {
                excludeIds: getProductsIds()
            }
        });
    }

    function getProductsIds() {
        if ($('#products-IDs').val() === undefined || $('#products-IDs').val() === '')
            return '-1';
        return $('#products-IDs').val().replace(/\-/g, ',');
    }

    // APPEND ACTION
    ///////////////////////////////////////////////////////////////////////////////////////////
  
    function appendFields(newBlock, type) {
        switch (type) {
            case 'cms-pages':
                appendCMSpagesSettings(newBlock);
                break;
            case 'categories':
                appendCategoryBlockSettings(newBlock);
                break;
            case 'suppliers':
            case 'manufacturers':
                appendCarriageBlockSettings(newBlock);
                break;
            case 'products':
                appendPROBlockSettings(newBlock);
                break;
            case 'search-field':
                appendSERBlockSettings(newBlock);
                break;
            case 'custom-image':
                appendIMGBlockSettings(newBlock);
                break;
            case 'custom-html':
                appendHTMLBlockSettings(newBlock);
                break;
            case 'cms-page':
                appendCMSpageSettings(newBlock);
                break;
            case 'custom-link':
                appendLinkSettings(newBlock);
                break;
        }
        appendBlockLayout(newBlock);
    }

    function appendCMSpagesSettings(newBlock) {
        newBlock.selected = "";
    }

    function appendCategoryBlockSettings(newBlock) {
        newBlock.grip_view = {};
        newBlock.selected = "";
        newBlock.item_number_of_columns = "2";
        newBlock.selected_view = "list";
    }

    function appendCarriageBlockSettings(newBlock) {
        newBlock.block_view = "multi";
        newBlock.selected = "";
        newBlock.car_per_row = 1;
        newBlock.nmb_of_rows = 1;
    }

    function appendPROBlockSettings(newBlock) {
        newBlock.pro_per_row = 1;
        newBlock.nmb_of_rows = 1;
        newBlock.selected_products_IDs = "";
        newBlock.selected_products_names = "";
    }

    function appendSERBlockSettings(newBlock) {
        newBlock.position = 3;
    }

    function appendIMGBlockSettings(newBlock) {
        newBlock.image_link = {};
        newBlock.image_url = {};
        newBlock.image_desc = {};

        if (typeof languages != "undefined") {
            languages.forEach(function(langObject) {
                newBlock.image_link[langObject.id_lang] = "";
                newBlock.image_url[langObject.id_lang] = "";
                newBlock.image_desc[langObject.id_lang] = "";
            });
        } else {
            newBlock.image_link[id_language] = "";
            newBlock.image_url[id_language] = "";
            newBlock.image_desc[id_language] = "";
        }
    }

    function appendHTMLBlockSettings(newBlock) {
        newBlock.code = {};

        if (typeof languages != "undefined") {
            languages.forEach(function(langObject) {
                newBlock.code[langObject.id_lang] = "";
            });
        } else {
            newBlock.code[id_language] = "";
        }
    }

    function appendCMSpageSettings(newBlock) {
        newBlock.selected = "";
    }

    function appendBlockLayout(newBlock) {
        newBlock.nmb_of_columns = 1;
        newBlock.separator = "separator-none";
        newBlock.float = "left";
        newBlock.padding_top = 0;
        newBlock.padding_bottom = 0;
        newBlock.padding_left = 0;
        newBlock.padding_right = 0;
    }

    function appendLinkSettings(newBlock) {
        newBlock.custom_link_name = {};
        newBlock.custom_link_url = {};
        newBlock.custom_new_window = 0;

        if (typeof languages != "undefined") {
            languages.forEach(function(langObject) {
                newBlock.custom_link_name[langObject.id_lang] = "";
                newBlock.custom_link_url[langObject.id_lang] = "";
            });
        } else {
            newBlock.custom_link_name[langObject.id_lang] = "";
            newBlock.custom_link_url[langObject.id_lang] = "";
        }
    }

    // INSERT ACTION
    ///////////////////////////////////////////////////////////////////////////////////////////
    
    function insertOptions(blockID, type) {
        switch (type) {
            case 'cms-pages':
                insertOptionsCMSpages(blockID, type);
                break;
            case 'categories':
                insertOptionsCategory(blockID, type);
                break;
            case 'suppliers':
            case 'manufacturers':
                insertOptionsCarriage(blockID, type);
                break;
            case 'products':
                insertOptionsProduct(blockID, type);
                break;
            case 'search-field':
                insertOptionsSearch(blockID, type);
                break;
            case 'custom-image':
                insertOptionsImage(blockID, type);
                break;
            case 'custom-html':
                insertOptionsHtml(blockID, type);
                break;
            case 'cms-page':
                insertCMSpage(blockID, type);
                break;
            case 'custom-link':
                insertLink(blockID, type);
                break;
        }
    }

    function insertOptionsCMSpages(blockID, type) {
        $('div#' + type + ' select.select-box').val(tabObject['blocks'][blockID]['selected']);
        insertOptionsLayout(blockID);
    }

    function insertOptionsCategory(blockID, type) {
        var selectedView = tabObject['blocks'][blockID]['selected_view'];

        if (selectedView == 'grip') {
            var selectBox = $('#category-simple-view .select-box'),
                selectedOptionsSelect = $('.category-grip-view select.selected-grip');

            $('.category-grip-view').show();
            $('#add-category').show();
            $('#category-info').show();

            selectedOptionsSelect.empty();
            $('option', selectBox).prop('disabled', false);

            for (catg in tabObject['blocks'][blockID]['grip_view']) {
                selectBox.find('option#' + tabObject['blocks'][blockID]['grip_view'][catg]['name']).prop('disabled', true);
                var text = selectBox.find('option#' + tabObject['blocks'][blockID]['grip_view'][catg]['name']).text();
                selectedOptionsSelect.append('<option id="' + tabObject['blocks'][blockID]['grip_view'][catg]['name'] + '" value="' + tabObject['blocks'][blockID]['grip_view'][catg]['name'] + '">' + text + '</option>');
            }
        } else if (selectedView = 'list') {
            $('div#' + type + ' select.select-box').val(tabObject['blocks'][blockID]['selected']);
            $('div#' + type + ' select.select-box option').prop('disabled', false);
            $('.category-grip-view').hide();
            $('#add-category').hide();
            $('#category-info').hide();
        }
        $('div#' + type + ' select.category-view-select').val(selectedView);

        if(tabObject['blocks'][blockID]['item_number_of_columns']) {
            $('#item-number-of-columns').val(tabObject['blocks'][blockID]['item_number_of_columns']);
        } else {
            $('#item-number-of-columns').val("2");
        }

        insertOptionsLayout(blockID);
    }

    function insertOptionsCarriage(blockID, type) {
        var blockView = tabObject['blocks'][blockID]['block_view'],
            selected = tabObject['blocks'][blockID]['selected'],
            carPerRow = tabObject['blocks'][blockID]['car_per_row'],
            carNmbOfRows = tabObject['blocks'][blockID]['nmb_of_rows'];

        if (blockView == 'carousel')
            $("#" + type + " .view-grip").show();
        else
            $("#" + type + " .view-grip").hide();

        $("#" + type + " .select-box option").prop("selected", false);
        $.each(selected.split(","), function(i, e) {
            $("#" + type + " .select-box option[value='" + e + "']").prop("selected", true);
        });

        $('div#' + type + ' input.' + blockView).prop("checked", true);
        $('div#' + type + ' select[name=carriage-width]').val(carPerRow);
        $('div#' + type + ' select[name=carriage-height]').val(carNmbOfRows);
        insertOptionsLayout(blockID);
    }

    function insertOptionsProduct(blockID, type) {
        var columns = tabObject['blocks'][blockID]['pro_per_row'],
            rows = tabObject['blocks'][blockID]['nmb_of_rows'],
            IDs = tabObject['blocks'][blockID]['selected_products_IDs'],
            names = tabObject['blocks'][blockID]['selected_products_names'];

        $('div#' + type + ' select[name=product-width]').val(columns);
        $('div#' + type + ' select[name=product-height]').val(rows);
        $('div#' + type + ' input[name=products-IDs]').val(IDs);
        $('div#' + type + ' input[name=products-names]').val(names);

        var IDsArray = IDs.split('-');
        var namesArray = names.split('¤');
        $('#selected-products').empty();

        for (var i = 0; i < IDsArray.length; i++) {
            if (IDsArray[i].length === 0)
                break;
            $('#selected-products').append('<div class="form-control-static"><button type="button" class="del-product btn btn-default" name="' + IDsArray[i] + '"><i class="icon-remove text-danger"></i></button>&nbsp;' + namesArray[i] + '</div>');
        }

        insertOptionsLayout(blockID);
    }

    function insertOptionsSearch(blockID, type) {
        var position = tabObject['blocks'][blockID]['position'];
        $('#search-field select option#' + position).prop("selected", true);
        insertOptionsLayout(blockID);
    }

    function insertOptionsImage(blockID, type) {
        if (typeof languages != "undefined") {
            languages.forEach(function(langObject) {
                $('#custom-image input[name=image-link-' + langObject.id_lang + ']').val(tabObject['blocks'][blockID]['image_link'][langObject.id_lang]);
                $('#custom-image input[name=image-url-' + langObject.id_lang + ']').val(tabObject['blocks'][blockID]['image_url'][langObject.id_lang]);
                $('#custom-image input[name=image-desc-' + langObject.id_lang + ']').val(tabObject['blocks'][blockID]['image_desc'][langObject.id_lang]);
            });
        } else {
            $('#custom-image input[name=image-link-' + id_language + ']').val(tabObject['blocks'][blockID]['image_link'][id_language]);
            $('#custom-image input[name=image-url-' + id_language + ']').val(tabObject['blocks'][blockID]['image_url'][id_language]);
            $('#custom-image input[name=image-desc-' + id_language + ']').val(tabObject['blocks'][blockID]['image_desc'][id_language]);
        }
        insertOptionsLayout(blockID);
    }

    function insertLink(blockID, type) {
        if (typeof languages != "undefined") {
            languages.forEach(function(langObject) {
                $('#custom-link input[name=custom-link-name-' + langObject.id_lang + ']').val(tabObject['blocks'][blockID]['custom_link_name'][langObject.id_lang]);
                $('#custom-link input[name=custom-link-url-' + langObject.id_lang + ']').val(tabObject['blocks'][blockID]['custom_link_url'][langObject.id_lang]);
            });
        } else {
            $('#custom-link input[name=custom-link-name-' + id_language + ']').val(tabObject['blocks'][blockID]['custom_link_name'][id_language]);
            $('#custom-link input[name=custom-link-url-' + id_language + ']').val(tabObject['blocks'][blockID]['custom_link_url'][id_language]);
        }
        var new_window = tabObject['blocks'][blockID]['custom_new_window'];
        if (new_window == '' || new_window == 0) {
            $('#custom-link #custom_link_new_window_on').attr('checked', false);
            $('#custom-link #custom_link_new_window_off').attr('checked', true);
        } else {
            $('#custom-link #custom_link_new_window_on').attr('checked', true);
            $('#custom-link #custom_link_new_window_off').attr('checked', false);
        }
        insertOptionsLayout(blockID);
    }

    function insertOptionsHtml(blockID, type) {
        if (typeof languages != "undefined") {
            languages.forEach(function(langObject) {
                $('#custom-html textarea[name=html-' + langObject.id_lang + ']').val(html_entity_decode(tabObject['blocks'][blockID]['code'][langObject.id_lang], 'ENT_QUOTES'));
            });
        } else {
            $('#custom-html textarea[name=html-' + id_language + ']').val(html_entity_decode(tabObject['blocks'][blockID]['code'][id_language], 'ENT_QUOTES'));
        }
        insertOptionsLayout(blockID);
    }

    function insertCMSpage(blockID, type) {
        var selected = tabObject['blocks'][blockID]['selected'];
        $.each(selected.split(","), function(i, e) {
            $("#" + type + " .select-box option[value='" + e + "']").prop("selected", true);
        });
        insertOptionsLayout(blockID);
    }

    function insertOptionsLayout(blockID) {
        $('#block-layout #block-width').val(tabObject['blocks'][blockID]['nmb_of_columns']);
        $('#block-layout #block-separator').val(tabObject['blocks'][blockID]['separator']);
        $('#block-layout #block-float').val(tabObject['blocks'][blockID]['float']);
        $('#block-layout #block-top-padding').val(tabObject['blocks'][blockID]['padding_top']);
        $('#block-layout #block-bottom-padding').val(tabObject['blocks'][blockID]['padding_bottom']);
        $('#block-layout #block-left-padding').val(tabObject['blocks'][blockID]['padding_left']);
        $('#block-layout #block-right-padding').val(tabObject['blocks'][blockID]['padding_right']);
    }

    // UPDATE ACTION
    ///////////////////////////////////////////////////////////////////////////////////////////

    function saveSettings(type, blockID) {
        switch (type) {
            case 'cms-pages':
                updateCMSpages(blockID, type);
                break;
            case 'categories':
                updateCategory(blockID, type);
                break;
            case 'suppliers':
            case 'manufacturers':
                updateCarriage(blockID, type);
                break;
            case 'products':
                updateProduct(blockID, type);
                break;
            case 'search-field':
                updateSearch(blockID, type);
                break;
            case 'custom-image':
                updateImage(blockID, type);
                break;
            case 'custom-html':
                updateHtml(blockID, type);
                break;
            case 'cms-page':
                updateCMS(blockID, type);
                break;
            case 'custom-link':
                updateLink(blockID);
                break;
        }
    }

    function updateCMSpages(blockID, type) {
        tabObject['blocks'][blockID]['selected'] = $('div#' + type + ' select.select-box').val().toString();
        updateLayout(blockID);
    }

    function updateCategory(blockID, type) {
        var selectedView = $('div#' + type + ' select.category-view-select').val(),
            selectedValue = $('.selected-grip').find('option:selected').attr('id'),
            links = {},
            descs = {},
            listViewSelectedVal = $('div#' + type + ' select.select-box').val();

        if ((selectedValue != '' && typeof(selectedValue) != 'undefined') && selectedView == 'grip') {
            if (typeof languages != "undefined") {
                languages.forEach(function(langObject) {
                    links[langObject.id_lang] = $('#category-small-image-' + langObject.id_lang).val();
                    descs[langObject.id_lang] = $('#category-short-desc-' + langObject.id_lang).val();
                });
            } else {
                links[id_language] = $('#category-small-image-' + id_language).val();
                descs[id_language] = $('#category-short-desc-' + id_language).val();
            }

            tabObject['blocks'][blockID]['grip_view'][selectedValue] = {
                "name": selectedValue,
                "image_link": links,
                "category_desc": descs
            };
        } else if(listViewSelectedVal) {
            tabObject['blocks'][blockID]['selected'] = listViewSelectedVal.toString();
        }

        tabObject['blocks'][blockID]['item_number_of_columns'] = $('#item-number-of-columns').val();
        tabObject['blocks'][blockID]['selected_view'] = selectedView;
        updateLayout(blockID);
    }

    function updateCarriage(blockID, type) {
        tabObject['blocks'][blockID]['block_view'] = $('div#' + type + ' input[type=radio]:checked').val();
        tabObject['blocks'][blockID]['selected'] = $('div#' + type + ' select.carriage-select').val().toString();
        tabObject['blocks'][blockID]['car_per_row'] = $('div#' + type + ' select[name=carriage-width]').val();
        tabObject['blocks'][blockID]['nmb_of_rows'] = $('div#' + type + ' select[name=carriage-height]').val();
        updateLayout(blockID);
    }

    function updateProduct(blockID, type) {
        tabObject['blocks'][blockID]['pro_per_row'] = $('div#' + type + ' select[name=product-width]').val();
        tabObject['blocks'][blockID]['nmb_of_rows'] = $('div#' + type + ' select[name=product-height]').val();
        tabObject['blocks'][blockID]['selected_products_IDs'] = $('div#' + type + ' input[name=products-IDs]').val();
        tabObject['blocks'][blockID]['selected_products_names'] = $('div#' + type + ' input[name=products-names]').val();
        updateLayout(blockID);
    }

    function updateSearch(blockID, type) {
        tabObject['blocks'][blockID]['position'] = $('#search-field select[name=search-position]').val();
        updateLayout(blockID);
    }

    function updateImage(blockID, type) {
        if (typeof languages != "undefined") {
            languages.forEach(function(langObject) {
                tabObject['blocks'][blockID]['image_link'][langObject.id_lang] = $('#custom-image input[name=image-link-' + langObject.id_lang + ']').val();
                tabObject['blocks'][blockID]['image_url'][langObject.id_lang] = $('#custom-image input[name=image-url-' + langObject.id_lang + ']').val();
                tabObject['blocks'][blockID]['image_desc'][langObject.id_lang] = $('#custom-image input[name=image-desc-' + langObject.id_lang + ']').val();
            });
        } else {
            tabObject['blocks'][blockID]['image_link'][id_language] = $('#custom-image input[name=image-link-' + id_language + ']').val();
            tabObject['blocks'][blockID]['image_url'][id_language] = $('#custom-image input[name=image-url-' + id_language + ']').val();
            tabObject['blocks'][blockID]['image_desc'][id_language] = $('#custom-image input[name=image-desc-' + id_language + ']').val();
        }

        updateLayout(blockID);
    }

    function updateHtml(blockID, type) {
        if (typeof languages != "undefined") {
            languages.forEach(function(langObject) {
                var html = $('#custom-html textarea[name=html-' + langObject.id_lang + ']').val();
                tabObject['blocks'][blockID]['code'][langObject.id_lang] = htmlentities(html, 'ENT_QUOTES');
            });
        } else {
            var html = $('#custom-html textarea[name=html-' + id_language + ']').val();
            tabObject['blocks'][blockID]['code'][id_language] = htmlentities(html, 'ENT_QUOTES');
        }

        updateLayout(blockID);
    }

    function updateCMS(blockID) {
        var selected = $('#cms-page-select').val();
        tabObject['blocks'][blockID]['selected'] = selected;

        updateLayout(blockID);
    }

    function updateLink(blockID) {
        if (typeof languages != "undefined") {
            languages.forEach(function(langObject) {
                tabObject['blocks'][blockID]['custom_link_name'][langObject.id_lang] = $('#custom-link input[name=custom-link-name-' + langObject.id_lang + ']').val();
                tabObject['blocks'][blockID]['custom_link_url'][langObject.id_lang] = $('#custom-link input[name=custom-link-url-' + langObject.id_lang + ']').val();
            });
        } else {
            tabObject['blocks'][blockID]['custom_link_name'][id_language] = $('#custom-link input[name=custom-link-name-' + id_language + ']').val();
            tabObject['blocks'][blockID]['custom_link_url'][id_language] = $('#custom-link input[name=custom-link-url-' + id_language + ']').val();
        }
        tabObject['blocks'][blockID]['custom_new_window'] = $('#custom-link input[name=custom_link_new_window]:checked').val();
        updateLayout(blockID);
    }

    function updateLayout(blockID) {
        tabObject['blocks'][blockID]['nmb_of_columns'] = $('#block-layout #block-width').val();
        tabObject['blocks'][blockID]['separator'] = $('#block-layout #block-separator').val();

        tabObject['blocks'][blockID]['float'] = $('#block-layout #block-float').val();
        tabObject['blocks'][blockID]['padding_top'] = $('#block-layout #block-top-padding').val();
        tabObject['blocks'][blockID]['padding_bottom'] = $('#block-layout #block-bottom-padding').val();
        tabObject['blocks'][blockID]['padding_left'] = $('#block-layout #block-left-padding').val();
        tabObject['blocks'][blockID]['padding_right'] = $('#block-layout #block-right-padding').val();

        $('#' + blockID + '.tab-wrapper').css("float", tabObject['blocks'][blockID]['float']);
        $('#' + blockID + '.tab-wrapper').css("padding-left", tabObject['blocks'][blockID]['padding_left'] + "px");
        $('#' + blockID + '.tab-wrapper').css("padding-right", tabObject['blocks'][blockID]['padding_right'] + "px");
        $('#' + blockID + '.tab-wrapper').css("padding-bottom", tabObject['blocks'][blockID]['padding_bottom'] + "px");
        $('#' + blockID + '.tab-wrapper').css("padding-top", tabObject['blocks'][blockID]['padding_top'] + "px");

        $('#' + blockID + '.tab-wrapper').css("border", "none");
        switch(tabObject['blocks'][blockID]['separator']) {
            case "separator-none":
            break;
            case "separator-left":
            case "separator-right":
            case "separator-bottom":
            case "separator-top":
                var border = "border-" + tabObject['blocks'][blockID]['separator'].replace("separator-", "");
                $('#' + blockID + '.tab-wrapper').css(border, "2px solid black");
            break;
            case "separator-complet":
                $('#' + blockID + '.tab-wrapper').css("border", "2px solid black");
            break;
            case "separator-top-bottom":
                $('#' + blockID + '.tab-wrapper').css("border-top", "2px solid black");
                $('#' + blockID + '.tab-wrapper').css("border-bottom", "2px solid black");
            break;
            case "separator-left-right":
                $('#' + blockID + '.tab-wrapper').css("border-left", "2px solid black");
                $('#' + blockID + '.tab-wrapper').css("border-right", "2px solid black");
            break;
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // SIMPLE VIEW CONTROL
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( (tabObject['simple_menu_select'] !== null) && (tabObject['simple_menu_select'].length > 0) ) {
        var simpleMenuSelectedValue = tabObject['simple_menu_select'];
        $('select#simple_menu_select option[value=' + simpleMenuSelectedValue + ']').prop("selected", true);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // FORM SUBMIT
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    $('#desktop_menu_tabs_form, #vertical_menu_tabs_form').submit(function() {
        var selectedView = $('.tab-view-select').val();
        tabObject['selected_view'] = selectedView;
        if (selectedView == 1) {

            // simple menu is selected
            tabObject['simple_menu_select'] = $('#simple_menu_select').val();
            tabObject['category_limit'] = $('#category_limit').val();
            tabObject['product_ID'] = $('#product_ID').val();
            tabObject['tab_link_new_window'] = $('input[name=tab_link_new_window]').val();

            var URLs = {},
                Titles = {};

            if (typeof languages != "undefined") {
                languages.forEach(function(langObject) {
                    URLs[langObject.id_lang] = $('#link_url_' + langObject.id_lang).val();
                    Titles[langObject.id_lang] = $('#link_title_' + langObject.id_lang).val();
                });
            } else {
                URLs[id_language] = $('#link_url_' + id_language).val();
                Titles[id_language] = $('#link_title_' + id_language).val();
            }

            tabObject['link_title'] = Titles;
            tabObject['link_url'] = URLs;
        }

        // advance tab
        tabObject['tab_wrapper_width'] = $('#tab_wrapper_width').val();
        tabObject['tab_wrapper_bg_color'] = $('#tab_wrapper_color').val();
        tabObject['tab_blocks_border_color'] = $('#blocks_border_color').val();
        var Links = {};

        if (typeof languages != "undefined") {
            languages.forEach(function(langObject) {
                Links[langObject.id_lang] = $('#tab_background_link_' + langObject.id_lang).val();
            });
        } else
            Links[id_language] = $('#tab_background_link_' + id_language).val();

        tabObject['tab_background_link'] = Links;
        tabObject['tab_bg_img_repeat'] = $('#tab_bg_img_repeat').val();
        tabObject['tab_bg_img_position'] = $('#tab_bg_img_position').val();

        tabObject['tab_bg_img_pdng_left'] = $('#tab_bg_img_pdng_left').val();
        tabObject['tab_bg_img_pdng_right'] = $('#tab_bg_img_pdng_right').val();
        tabObject['tab_bg_img_pdng_top'] = $('#tab_bg_img_pdng_top').val();
        tabObject['tab_bg_img_pdng_bottom'] = $('#tab_bg_img_pdng_bottom').val();

        var tabID;
        $('#tab-view .tab-wrapper').each(function(index, val) {
            tabID = $(val).find('#block-ID').val();
            tabObject['blocks'][tabID]['order_index'] = index;
        });

        // convert tabObject into JSON
        $('#advance_tab_object').val(JSON.stringify(tabObject));
        return true;
    });

}); // end of ready function
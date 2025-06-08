/**
 * Finalmenu
 *
 * @author: Matej Berka
 * site: www.marpaweb.eu
 */

$(document).ready(function() {

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // GENERAL SETTINGS PAGE
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // SHARED 
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#tabs-menu a').on("click", function(e) {
        e.preventDefault();
        $('#tabs-menu a').removeClass("selected");
        $(this).addClass("selected");
        var id = $(this).attr('href');
        $('.tab-panel').hide();
        $(id).show();
    });

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // HORIZONTAL AND VERTICAL MENU CART
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    checkFont();
    $('input[name=google_fonts]').change(function() {
        checkFont();
    });

    function checkFont() {
        var value = $('input[name=google_fonts]:checked').val();
        if(value == 1)
            $('#desktop_menu_wrapper #menu_top_links_font_url').prop('disabled', false);
        else
            $('#desktop_menu_wrapper #menu_top_links_font_url').prop('disabled', true);
    }

    checkVerticalFont();
    $('input[name=google_fonts_vertical]').change(function() {
        checkVerticalFont();
    });

    function checkVerticalFont() {
        var value = $('input[name=google_fonts_vertical]:checked').val();
        if(value == 1)
            $('#vertical_menu_wrapper #menu_top_links_font_url').prop('disabled', false);
        else
            $('#vertical_menu_wrapper #menu_top_links_font_url').prop('disabled', true);
    }

    $('.layout-picker').delegate('div', 'click', function() {
        $(this).siblings().removeClass('selected-menu-layout');
        var selectedLayoutID = $(this).addClass('selected-menu-layout').attr('id');
        $('.layout-picker input[name=menu_layout_holder]').val(selectedLayoutID);
    });

    $('button[name=vertical_menu_settings_submit]').click(function(e) {
        e.preventDefault();
        document.getElementById("vertical_menu_tabs_form").submit();
    });


    $('button[name=desktop_menu_settings_submit]').click(function(e) {
        e.preventDefault();
        document.getElementById("desktop_menu_tabs_form").submit();
    });

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // MOBILE MENU CART
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    checkMobileFont();
    $('input[name=FINALm_google_fonts]').change(function() {
        checkMobileFont();
    });

    function checkMobileFont() {
        var value = $('input[name=FINALm_google_fonts]:checked').val();
        if (value == 1)
            $('#FINALm_links_font_url').prop('disabled', false);
        else
            $('#FINALm_links_font_url').prop('disabled', true);
    }

    $('#product-select').click(function() {
        $('.product-select-show-box').slideToggle();
        $('.link-select-show-box').slideUp();
    });

    $('#link-select').click(function() {
        $('.link-select-show-box').slideToggle();
        $('.product-select-show-box').slideUp();
    });

    $('button[name=mobile_menu_settings_submit]').click(function(e) {
        e.preventDefault();
        document.getElementById("mobile_menu_tabs_form").submit();
    });
});
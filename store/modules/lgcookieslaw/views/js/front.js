/**
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 */

function closeinfo(accept, all)
{
    closeBanners();
    if (typeof accept != 'undefined' && accept == true) {
        if (typeof all != 'undefined' && all == true) {
            var level = 1;
        } else {
            var level = document.getElementById("lgcookieslaw-cutomization-enabled").checked == true ? 1 : 2;
        }        
        setCookie(lgcookieslaw_cookie_name, level, lgcookieslaw_session_time);
    }

    if (typeof $.fancybox.open !== 'function') {
        $('#lgcookieslaw-modal').hide();
    }

    $.fancybox.close();
    if (lgcookieslaw_reload == true) {
        window.location.href = window.location.href;
    }
}

function showBanner()
{
    var banners = document.getElementsByClassName("lgcookieslaw_banner");
    if (banners) {
        for (var i = 0; i < banners.length; i++) {
            banners[i].style.display = 'table';
        }
    }
    if (lgcookieslaw_block) {
        $(".lgcookieslaw_overlay").css("display", "block");
    }
}

function closeBanners() {
    var banners = document.getElementsByClassName("lgcookieslaw_banner");
    if (banners) {
        for (var i = 0; i < banners.length; i++) {
            banners[i].style.display = 'none';
        }
    }
    if (lgcookieslaw_block) {
        $(".lgcookieslaw_overlay").css("display", "none");
    }
}

function checkLgCookie()
{
    var regex = new RegExp("^(.*;)?\\s*"+lgcookieslaw_cookie_name+"\\s*=\\s*[^;]+(.*)?$");
    return document.cookie.match(regex);
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";samesite=none;secure=true;path=/";
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

/*
var lgbtnclick = function(){
    var buttons = document.getElementsByClassName("lgcookieslaw_btn_accept");
    if (buttons != null) {
        for (var i = 0; i < buttons.length; i++) {
            buttons[i].addEventListener("click", function () {
                closeinfo(true);
                location.reload(true);
            });
        }
    }
};
*/

function customizeCookies() {
    closeBanners();
    $(".lgcookieslaw_overlay").css("display", "none");

    if (typeof $.fancybox.open === 'function') { 
        $.fancybox.open($("#lgcookieslaw-modal"), {
            autoSize : false,
            width:700,
            height:'auto',
            padding: 0,
            modal: true,
        });
    } else {
        $('#lgcookieslaw-modal').show();

        $('<a href="#lgcookieslaw-modal" />').fancybox({
            autoSize : false,
            width:700,
            height:'auto',
            padding: 0,
            modal: true,
        }).click();
    }
}

window.addEventListener('load',function() {

    if( checkLgCookie() ) {
        closeBanners();
    } else {
        showBanner();
        //lgbtnclick();
    }

    $('#lgcookieslaw-close').click(function() {
        if (typeof $.fancybox.open !== 'function') {
            $('#lgcookieslaw-modal').hide();
        }

        $.fancybox.close();
        showBanner();
    });

    $('.lgcookieslaw_slider').click(function(){
        if ($(this).parent().find('input[type=checkbox]').is(':disabled')) {
            return false;
        }

        if ($(this).hasClass('lgcookieslaw_slider_checked')) {
            $(this).removeClass('lgcookieslaw_slider_checked');
        } else {
            $(this).addClass('lgcookieslaw_slider_checked');
        }
    });
});

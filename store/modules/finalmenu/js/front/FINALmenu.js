/**
 * Finalmenu
 *
 * @author: Matej Berka
 * site: www.marpaweb.eu
 */
$(document).ready(function() {

    $('.top-link-wrapper .show-items-icon').click(function(e){
        var current = $(this).parents('.top-link-wrapper').siblings('.FINALmenu-tab-content');
        $('.FINALmenu-tab-content').not(current).hide();
        current.toggle();
    });

    $('.FINALmenu-tab-content li .show-items-icon').mouseover(function(){
        $(this).siblings('.hidden-categories').toggle();
    });

    $('#FINALmenu-mobile-nav li').click(function(e) {
        if($(e.target).is('a')){
            return;
        }
        var ul = $(' > ul', this);
        if(ul.is(':visible')) {
            ul.slideUp();
            $('> i', this).removeClass('icon-minus').addClass('icon-plus');
        } else {
            ul.slideDown();
            $('> i', this).removeClass('icon-plus').addClass('icon-minus');
        }
        e.stopPropagation();
    });

    $('.menu-place-holder').click(function(e) {
        $('#FINALmenu-mobile-nav').slideToggle();
    });
});

$(window).on({ 'touchstart' : function(e) {
        if(!($(e.target).is('.hidden-categories'))) {
            $('#FINALmenu-vertical .hidden-categories, #FINALmenu-desktop-nav .hidden-categories').hide();
        }
 } });

$(window).load(function() {
    var menuHeight = $("#FINALmenu-desktop-nav").height();
    var tabActive = false;
    $("#FINALmenu .FINALmenu-tab-content").css("top", menuHeight);

    if(!(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))) {
        // hover effects only for desktop users
        $('#FINALmenu-desktop-nav > li').mouseenter(function(e){
            var el = $(this);
            var timerIn = setTimeout(function() {
                var menuTatShow = $('.FINALmenu-tab-content', el);
                menuTatShow.css('display', 'block');
                menuTatShow.removeClass(menuAnimateOutHorizontal);
                menuTatShow.addClass(menuAnimateInHorizontal);

                setTimeout(function(){
                    tabActive = true;
                    for (var i = 0; i < menuSliders.length; i++) {
                        menuSliders[i].reloadSlider();
                    }
                }, 500);

                e.stopPropagation();
            }, 400);
            el.mouseleave(function(event) {
                clearTimeout(timerIn);
            });
        }).mouseleave(function(e) {
            var el = $(this);
            var timerOut = setTimeout(function() {
                var menuTatShow = $('.FINALmenu-tab-content', el);
                menuTatShow.removeClass(menuAnimateInHorizontal);
                menuTatShow.addClass(menuAnimateOutHorizontal);

                setTimeout(function(){
                    tabActive = false;
                    menuTatShow.css('display', 'none');
                }, 500);
                e.stopPropagation();
            }, 400);
            el.mouseenter(function(event) {
                clearTimeout(timerOut);
            });
        });

        var verticalTabActive = false;
        $('#FINALmenu-vertical-nav > li').mouseenter(function(e){
            var el = $(this);
            var timerIn = setTimeout(function() {
                var menuTatShow = $('.FINALmenu-tab-content', el);
                menuTatShow.css('display', 'block');
                menuTatShow.removeClass(menuAnimateOutVertical);
                menuTatShow.addClass(menuAnimateInVertical);
                setTimeout(function(){
                    verticalTabActive = true;
                    for (var i = 0; i < menuSliders.length; i++)
                        menuSliders[i].reloadSlider();
                }, 500);

                e.stopPropagation();
            }, 400);
            el.mouseleave(function(event) {
                clearTimeout(timerIn);
            });
        }).mouseleave(function(e) {
            var el = $(this);
            var timerOut = setTimeout(function() {
                var menuTatShow = $('.FINALmenu-tab-content', el);
                menuTatShow.removeClass(menuAnimateInVertical);
                menuTatShow.addClass(menuAnimateOutVertical);

                setTimeout(function(){
                    verticalTabActive = false;
                    menuTatShow.css('display', 'none');
                }, 500);
                e.stopPropagation();
            }, 400);
            el.mouseenter(function(event) {
                clearTimeout(timerOut);
            });
        });

        $('.FINALmenu-tab-content li').mouseenter(function(e){
            if(tabActive || verticalTabActive)
                $(' > .hidden-categories', this).fadeIn();
        }).mouseleave(function(e) {
            if(tabActive || verticalTabActive)
                $(' > .hidden-categories', this).fadeOut();
        });

        $('.image-view').mouseenter(function() {
            $(' .second-image', this).animate({
                opacity: '1'
            }, 500);
        }).mouseleave(function() {
            $('.second-image', this).animate({
                opacity: '0'
            }, 500);
        });
    }
});

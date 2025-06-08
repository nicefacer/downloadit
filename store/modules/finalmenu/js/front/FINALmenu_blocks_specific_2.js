$(document).ready(function () { // <![CDATA[
                window.menuSliders = new Array();
                window.menuAnimateInHorizontal = "zoomInUp";
                window.menuAnimateOutHorizontal = "zoomOutUp";
                var value = $("input[name=search_query]").val();
                $("input[name=search_query]").focusin(function () {
                    $(this).val("");
                });
                $("input[name=search_query]").change(function () {
                    value = $("input[name=search_query]").val();
                });
                $("input[name=search_query]").focusout(function () {
                    $(this).val(value);
                });
        
                var menu = $("#FINALmenu");
                var menu_position = menu.offset();
                var sticky = false;

                $(window).scroll(function () {
                    if ($( window ).width() > 768) {
                        var window_position = $(window).scrollTop();
                        if (window_position >= menu_position.top) {
                            if (!sticky) {
                                menu.addClass("sticky_menu");
                                  $("#FINALmenu.sticky_menu").animate({
                                    "top": "0px",
                                  }, 300);
                                sticky = true;
                                // $(".shopping_cart").clone().appendTo(menu);
                            }
                        } else {
                            if (sticky) {
                                // $("#FINALmenu.sticky_menu .shopping_cart").remove();
                                menu.removeClass("sticky_menu").removeAttr("style");
                                sticky = false;
                            }
                        }
                    }
                });// ]]>
                });
            window.menuAnimateInVertical = "zoomInUp";
            window.menuAnimateOutVertical = "zoomOutUp";
        
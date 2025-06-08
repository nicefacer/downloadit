/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
(function($) {
    $(document).ready(function() {
        var parentWindow;
        var parentWindowLoaded = function () {
            if (typeof parentWindow === "undefined" || parentWindow.closed) {
                if (typeof top.Shopware === "undefined") {
                    parentWindow = window.open(document.URL.substr(0,document.URL.indexOf('Magnalister')));
                } else {
                    parentWindow = top;
                }
            }
            return 
                typeof parentWindow.Shopware !== "undefined"
                && typeof parentWindow.Shopware.app !== "undefined"
                && typeof parentWindow.Shopware.app.Application !== "undefined"
            ;
        }
        $(".ml-plist-table , .ml-plist-old-fix").on('click', ".product-link a", function(e) {
            var iId = $(this).attr('href');
            var windowInterval = setInterval(
                function () {
                    if (parentWindowLoaded() !== false) {
                        parentWindow.Shopware.app.Application.addSubApplication({
                            name: 'Shopware.apps.Article',
                            action: 'detail',
                            params: {
                                articleId: iId
                            }
                        });
                        clearInterval(windowInterval);
                    }
                },
                500
            );
            return false;
        });
        $(".order-link a").on('click', function(e) {
            var iId = $(this).attr('href');
            var windowInterval = setInterval(
                function () {
                    if (parentWindowLoaded() !== false) {
                        parentWindow.Shopware.app.Application.addSubApplication({
                            name: 'Shopware.apps.Order',
                            params: {
                                orderId: iId
                            }
                        });
                        clearInterval(windowInterval);
                    }
                },
                500
            );
            return false;
        });
    });
})(jqml);

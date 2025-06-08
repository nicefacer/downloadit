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
    $(document).ready( function() {
        $(".magna").on("click", "[data-ml-modal]", function(event) {
            var element = $(this);
            var eModal = $(element.attr("data-ml-modal"));
            eModal.find('.ml-js-modalPushMessages').html('');
            if (eModal.hasClass('ml-modal-notcloseable') ) {
                eModal.jDialog({buttons : []});
                eModal.parents('.ui-dialog').find('.ui-dialog-titlebar-close').hide();
            } else {
                eModal.jDialog(eModal.data("ml-jdialog") || {});
            }
            eModal.parents('.ui-dialog').find('.ui-dialog-titlebar').append(eModal.find('.ml-js-ui-dialog-titlebar-additional').addClass('ml-ui-dialog-titlebar-additional'));
        });
    });
})(jqml);
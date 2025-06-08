(function($) {
    $(document).ready(function() {
        function fireAjaxRequest(eElement, ajaxAdditional) {
            $.blockUI(blockUILoading);
            var eForm = eElement.parentsUntil('form').parent(),
                aData = $(eForm).serializeArray(),
                aAjaxData = $.parseJSON(eElement.attr('data-ajax')),
                i;

            for (i in aAjaxData) {
                if (aAjaxData[i]['value'] === null) {
                    aAjaxData[i]['value'] = ajaxAdditional;
                }

                aData.push(aAjaxData[i]);
            }

            eElement.hide('slide', {direction: 'right'});
            $.ajax({
                url: eForm.attr("action"),
                type: eForm.attr("method"),
                data: aData,
                complete: function(jqXHR, textStatus) {
                    var eRow;
                    try {// need it for ebay-categories and attributes, cant do with global ajax, yet
                        var oJson = $.parseJSON(data);
                        var content = oJson.content;
                        eElement.html(content);
                    } catch (oExeception) {
                    }

                    eRow = eElement.parentsUntil('.js-field').parent();
                    if (eElement.text() !== '') {
                        eRow.show();
                    } else {
                        eRow.hide();
                    }

                    initAjaxForm(eElement, true);
                    $.unblockUI();
                    eElement.show('slide', {direction: 'right'});
                    $(".magnalisterForm select.optional").trigger("change");
                }
            });
        }

        function initAjaxForm(eElements, onlyChildren) {
            var els = eElements.find('.magnalisterAjaxForm');
            if (!onlyChildren) {
                els = els.andSelf();
            }

            els.each(function() {
                var eElement = $(this),
                    aAjaxController = $.parseJSON(eElement.attr('data-ajax-controller'));
                    
                if (aAjaxController !== null) {
                    if (eElement.find(aAjaxController.selector).length === 0) {
                        $(eElements).find(aAjaxController.selector).on(aAjaxController.trigger, function(event) {
                            fireAjaxRequest(eElement, event.ajaxAdditional);
                        });
                    } else {
                        $(eElement).on(aAjaxController.trigger, $('.magnalisterForm').find(aAjaxController.selector), function(event) {
                            fireAjaxRequest(eElement, event.ajaxAdditional);
                        });
                    }

                    if (eElement.attr('data-ajax-trigger') === 'true') {
                        // only trigger by first load
                        eElement.attr('data-ajax-trigger', 'false');
                        fireAjaxRequest(eElement);
                    }
                }
            });
        }

        initAjaxForm($('.magnalisterForm'));
    });
})(jqml);
(function($) {
    var settings;
    
    function getItemsFee(form) {        
        var dialogHtml =    '<div id="getitemsfeedialog" class="ml-modal dialog2" title="Information"></div>' + 
                            '<span id="getitemsfeedialogcontent" style="display: none">' + settings.i18n.process + '</span>';

        $('#getitemsfeedialog').remove();
        $('#getitemsfeedialogcontent').remove();
                            
        $('html').append(dialogHtml);
        var d = jqml('#getitemsfeedialogcontent').html();

        jqml('#getitemsfeedialog').html(d).jDialog({
            width: (d.length > 1000) ? '700px' : '500px',
            buttons: [
                {
                    id: 'getitemsfeedialog-ok',
                    text: settings.i18n.ok,
                    click: function() {
                        jqml(this).dialog('close');
                        settings.addItems(form);
                    }
                },
                {
                    id: 'getitemsfeedialog-abort',
                    text: settings.i18n.abort,
                    click: function() {
                        jqml(this).dialog('close');
                    }
                }
            ]
        });

        jqml('#getitemsfeedialog-ok').attr('disabled', true);
        jqml('#getitemsfeedialog-ok').css('visibility', 'hidden');
        jqml('#getitemsfeedialog-abort').attr('disabled', true);

        var url = jqml(form).attr('action').replace(/^https?:/, window.location.protocol);

        var dataArray = new Array();
        
        var formArray = jqml(form).serializeArray();
        for(var index = 0; index < formArray.length; index++) {
            if (formArray[index].name === 'ml[method]') {
                formArray[index].value = settings.method;
            }
            dataArray.push(formArray[index].name + '=' + formArray[index].value);
        }

        dataArray.push('ml[ajax]=true');

        var data = dataArray.join('&');

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(data) {
                var message = settings.message;

                if (data.Status === 'OK') {
                    message = message.replace('{1}', data.ItemsFee);
                    message = message.replace('{2}', settings.currency);

                    jqml('#getitemsfeedialog').html(message);
                    jqml('#getitemsfeedialog-ok').attr('disabled', false);
                    jqml('#getitemsfeedialog-ok').css('visibility', '');
                    jqml('#getitemsfeedialog-abort').attr('disabled', false);
                } else {
                    jqml('#getitemsfeedialog').html(data.Error);
                    jqml('#getitemsfeedialog-abort').attr('disabled', false);
                }
            }
        });
    }
    
    $.fn.itemsFee = function(options) {
        var defaults = {
            mode: 'on',
            addItems: function() {
                alert('Method addItems must be defined.');
            },
            message: null,
            currency: null,
            method: 'getItemsFee',
            i18n: {
                ok: 'Ok',
                abort: 'Abort'
            }
        };
        
        settings = $.extend({}, defaults, options);
        
        var form = this.closest('form');
        
        if (settings.mode === 'on') {
            getItemsFee(form);
        } else {            
            settings.addItems(form);
        }
    };
})(jqml);
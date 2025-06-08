(function($) {
    $(document).ready(function() {
        var zeichenLimit = 2000;
        function checkCharLimit(tArea) {
            if (tArea.val().length > zeichenLimit) {
                tArea.val(tArea.val().substr(0, zeichenLimit));
            }
            $('#charsLeft').html(zeichenLimit - tArea.val().length);
        }
        $(document).ready(function() {
            $('#item_note').keydown(function(event) {
                myConsole.log('event.which: ' + event.which);
                if (($(this).val().length >= zeichenLimit) &&
                        (event.which != 46) && // del
                        (event.which != 8) && // backspace
                        ((event.which < 37) || (event.which > 40)) // arrow-keys*/
                        ) {
                    myConsole.log('prevent');
                    event.preventDefault();
                }
                return true;
            }).keyup(function(event) {
                checkCharLimit($(this));
                return true;
            });

            checkCharLimit($('#item_note'));
        });
    });
})(jqml);
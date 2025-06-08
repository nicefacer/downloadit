(function($) {
    $(document).ready(function() {
        $('.magnalisterForm').on('dblclick', 'tr.headline', function(event) {
            if(document.selection && document.selection.empty) {
                document.selection.empty();
            } else if (window.getSelection) {
                var sel = window.getSelection();
                sel.removeAllRanges();
            }
            var eRow=$(this);  
            var eRows=eRow.nextUntil('tr.headline').not('tr.spacer');
            if(eRows.is(':hidden')){
                eRow.css('opacity',1);
                eRows.show();
            }else{
                eRow.css('opacity',.7);
                eRows.hide();
            }
        });
    });
})(jqml);
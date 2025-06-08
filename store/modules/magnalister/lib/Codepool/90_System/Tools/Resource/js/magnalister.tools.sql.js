(function($) {
    $(document).ready(function() {
        $('#magnaSql').keypress(function(event) {
            if (
                    (event.ctrlKey || event.metaKey)//Mac: CMD + Enter or Win: CTRL + Enter (only fx)
                    && 
                    event.which === 13
            ) {
                this.form.submit();
                return false;
            }
        });
        $('#magnaSql').focus();
        $("#preparedQuerys li.active").each(function() {
            $(this).parent().scrollTop($(this).offset().top - $(this).parent().offset().top);
        });
        $('#preparedQuerys li').bind('click dblclick', function(event) {
            $('#preparedQuerys li').removeClass('active');
            $(this).addClass('active');
            $('#magnaSql').text(decodeURIComponent($(this).attr('data-sql')).replace(/\+/g, ' '));
            if (event.type === 'dblclick') {
                $('#magnaSql')[0].form.submit();
            } else {
                $('#magnaSql').focus();
            }
        });
    });
})(jqml);
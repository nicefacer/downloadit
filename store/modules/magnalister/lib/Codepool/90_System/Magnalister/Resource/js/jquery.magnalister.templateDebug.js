(function($) {
    $('document').ready(function() {
        $('magnaview').on("mouseenter mouseleave",
            function(e) {
                e.stopPropagation();
                $('magnaview').removeClass('visible');
                if(e.type==='mouseenter'){
                    $(this).addClass('visible');
                }
            }
        );
    });
})(jqml);
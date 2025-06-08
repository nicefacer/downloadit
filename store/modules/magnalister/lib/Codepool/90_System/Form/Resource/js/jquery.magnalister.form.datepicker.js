(function($) {
    $(document).ready(function() {
        $.datepicker.setDefaults($.datepicker.regional['']);
        $(".datetimepicker input").datetimepicker(
            $.extend(
                $.datepicker.regional['de'],
                $.timepicker.regional['de']
            )
        );
        $(".datepicker input").datepicker(
            $.datepicker.regional['de']
        );
        $(".datepicker span, .datetimepicker span").click(function() {
            $(this).parent().find("input").val('');
        });
    });
})(jqml);
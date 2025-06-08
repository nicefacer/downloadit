(function($) {
    $(document).ready(function() {
        $('.magnalisterForm').on('click', '.duplicate>div>span button', function(event) {
            var sData = $(this).attr('data-ajax-additional');
            $(this).trigger({
                type: "duplicate",
                ajaxAdditional: sData
            });
        });
        $('.magnalisterForm').on('change', '.duplicate input[type="radio"].ml-js-form-duplicate-radiogroup', function(event) {
            var eContainer = $(this).parentsUntil('.duplicate').parent();
            var eRadios = eContainer.find('input[type="radio"].ml-js-form-duplicate-radiogroup');
            console.log(eRadios.length);
            var eRadio = eRadios.filter(':checked');
            if (eRadio.length === 1) {
                eContainer.find('input[type!="radio"].ml-js-form-duplicate-radiogroup').val('');
                eRadio.siblings('input[type!="radio"].ml-js-form-duplicate-radiogroup').val(eRadio.val());
            } else if (eRadios.length !== 0) {
                eRadios.removeAttr('checked');
                eRadios.first().prop('checked', true).trigger('change');
            }
        });
        $('.magnalisterForm .duplicate input[type="radio"].ml-js-form-duplicate-radiogroup').trigger('change');
    });
})(jqml);
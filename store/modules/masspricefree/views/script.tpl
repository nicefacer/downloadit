{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA Miłosz Myszczuk VATEU PL9730945634
* @copyright 2010-2023 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

<script>
$(function() {
    var $in = $('#masspricefree_value');
    $in.keyup(function() {
        $in.val($in.val().replace(/,/g,'.'));
    });
});
</script>
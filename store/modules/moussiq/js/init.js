/**
 * Moussiq PRO
 *
 * @category  Module
 * @author    silbersaiten <info@silbersaiten.de>
 * @support   silbersaiten <support@silbersaiten.de>
 * @copyright 2014 silbersaiten
 * @version   2.2.0
 * @link      http://www.silbersaiten.de
 * @license   See joined file licence.txt
 */
$(document).ready(function(){
    exporter.init();

    if ($("div").is("#fieldset_moussiqSettings_1"))
    	$('span#toggleMousiqueSettings').click(function(){$('div#fieldset_moussiqSettings_1').slideToggle('fast');});
    else  if ($("div").is("#fieldset_moussiqSettings"))
    	$('span#toggleMousiqueSettings').click(function(){$('div#fieldset_moussiqSettings').slideToggle('fast');});
    $('span#whatSCron').click(function(){$('p#wtfsCron').slideToggle('fast');});


    //old - remove
    $('span#toggleMousiqueSettings').click(function(){$('fieldset#mousiqueSettings').slideToggle('fast');});
});

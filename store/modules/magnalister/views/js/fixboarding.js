
$( document ).ready(function() {
    var prestashopmlintervalcounter = 0;
    if($('.magna').length) {
        var prestashopmlinterval = setInterval(function(){
            prestashopmlintervalcounter ++;
            if($('div.nobootstrap#content div.alert-onboarding').length) {
                $('div.nobootstrap#content div.alert-onboarding').wrapAll('<div class="bootstrap">');
                clearInterval(prestashopmlinterval);
            }
            if(prestashopmlintervalcounter === 60){//turn off interval after 60 try
                clearInterval(prestashopmlinterval);
            }
        },500);
    }
    
});
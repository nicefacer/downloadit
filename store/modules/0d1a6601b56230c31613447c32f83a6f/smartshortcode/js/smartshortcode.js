$(function(){
    
    $('.sds_image_gallery_single').magnificPopup({
            type: 'image',
            closeOnContentClick: true,
            mainClass: 'mfp-img-mobile',
            image: {
                    verticalFit: true
            }

    });
    
    $('.panel-group').each(function(){
       var elem = $(this);
       if(elem.children('.panel').first().find('.panel-collapse').hasClass('in'))
            elem.children('.panel').first().find('a.collapsed').removeClass('collapsed');
    });
});
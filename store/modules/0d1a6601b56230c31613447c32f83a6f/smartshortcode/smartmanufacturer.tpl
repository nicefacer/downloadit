<div class="manufacturer_slider">
    {$id = rand(000000,999999)}
  <ul id="bx_slider_{$id}">
        {foreach from=$manufacturers item=manufacturer name=manufacturers}
        <li>
            <a href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)}">
                <img src="{$img_manu_dir}{$manufacturer.id_manufacturer}-manufacturer_small.jpg" alt="{$manufacturer.name}" title="{$manufacturer.name}"  />
            </a>
        </li>
          {/foreach}
   </ul>
</div>
<script type="text/javascript">
        
    jQuery(function($) { 
        $(window).load(function() {             
            var elem = $('#bx_slider_{$id}');
            var wdi = 0;
            elem.children('li').each(function() { 
                if($(this).find('img').width() > wdi)
                    wdi = $(this).find('img').innerWidth()+10;            
             } );
            //var maxSlide = Math.floor(elem.parents('.manufacturer_slider').outerWidth(true)/wdi);
           
            if($.fn.bxSlider !== undefined) { 
                elem.bxSlider({                    
                    slideMargin : 10,
                    controls : true,
                    infiniteLoop : true,
                    responsive : true,
                    speed : parseInt({$speed}),
                    slideWidth : wdi,
                    minSlides : 1,
                    moveSlides : 2,
                   	maxSlides : parseInt({$maxslide}),
                    pager : false,                        
                    adaptiveHeight: true,
                    useCSS : false,
                    auto: true,
                    onSliderLoad: function () {
			            $('.manufacturer_slider .bx-controls-direction').hide();
			            $('.manufacturer_slider .bx-wrapper').hover(
			            function () { $('.manufacturer_slider .bx-controls-direction').fadeIn(300); },
			            function () { $('.manufacturer_slider .bx-controls-direction').fadeOut(300); }
			            );
		            }
                });
                //$('.manufacturer_slider .bx-wrapper').css('max-width','100% !important');
            }
        });            
    });
</script>
<?php
      function testimonial_slider_cb($atts = array(),$content = null, $tag, $hook_name){
        extract(SmartShortCode::shortcode_atts(array(
            'speed' => '500',
            'width' => '',
            'mode' => 'vertical',
            'responsive' => 'true',
            'auto' => 'true',        
            'loop' => 'true',        
            'controls' => 'true',
            'autoHover' => 'true',        
        ), $atts));
        
        $gallery_id = 'bx_slider_'.rand(00000,99999);
        
        $return = "<div class='testimonial_slider'><ul id='{$gallery_id}'>".SmartShortCode::do_shortcode($content,$hook_name)."</ul></div>";
        ob_start();
        ?>
        <script type="text/javascript">        
            jQuery(function($){            
                 $(window).load(function(){
                    var width = parseInt('<?php echo $width?>');
                    var speed = parseInt('<?php echo $speed?>');
                    
                    if($.fn.bxSlider !== undefined){
                        $('#<?php echo $gallery_id?>').bxSlider({
                            mode : '<?php echo $mode?>',                                        
                            autoHover : <?php echo $autoHover?>,
                            controls : <?php echo $controls?>,
                            infiniteLoop : <?php echo $loop?>,
                            responsive : <?php echo $responsive?>,
                            speed : speed,
                            <?php if($width != ''){?>
                            slideWidth : width,
                            <?php } ?>
                            pager : false,
                            auto : <?php echo $auto?>,
                            adaptiveHeight: true,
                            useCSS : false,
                            infiniteLoop: true,
                            slideMargin: 20,
                            onSliderLoad: function () {
                        $('#<?php echo $gallery_id?>').parents('.bx-wrapper').find('.bx-controls-direction').hide();
                        $('#<?php echo $gallery_id?>').parents('.bx-wrapper').hover(
                        function () { $('#<?php echo $gallery_id?>').parents('.bx-wrapper').find('.bx-controls-direction').fadeIn(300); },
                        function () { $('#<?php echo $gallery_id?>').parents('.bx-wrapper').find('.bx-controls-direction').fadeOut(300); }
                        );
                      }
                        });

                    }
                });
            });
        </script>
        <?php
        $return .= ob_get_clean();
        
        return $return;
        
    }
    SmartShortCode::add_shortcode('testimonial_slider', 'testimonial_slider_cb');
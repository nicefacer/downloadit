<?php
    function sds_slider_cb($atts = array(),$content = null, $tag, $hook_name){
        extract(SmartShortCode::shortcode_atts(array(
            'speed' => '500',
            'width' => '',
            'mode' => 'horizontal',
            'responsive' => 'true',
            'auto' => 'true',   
            'loop' => 'true',        
            'pager' => 'false',        
            'controls' => 'true',
            'autoHover' => 'true',       
        ), $atts));
        
        $gallery_id = 'bx_slider_'.rand(00000,99999);
        $width = (int) $width;
        $return = "<div id='{$gallery_id}' class='sds_gallery'><ul>".SmartShortCode::do_shortcode($content,$hook_name)."</ul></div>";
        ob_start();
        ?>
        <script type="text/javascript">
            
            jQuery(function($){
                $(window).load(function(){
                    var width = parseInt('<?php echo $width?>');
                    var speed = parseInt('<?php echo $speed?>');
                    if($.fn.bxSlider !== undefined){
                        $('#<?php echo $gallery_id?> > ul').bxSlider({
                            mode : '<?php echo $mode?>',                    
                            autoHover : <?php echo $autoHover?>,
                            controls : <?php echo $controls?>,
                            infiniteLoop : <?php echo $loop?>,
                            responsive : <?php echo $responsive?>,
                            speed : speed,
                            <?php if($width != ''){?>
                            slideWidth : width,
                            <?php } ?>
                            pager : <?php echo $pager?>,
                            auto : <?php echo $auto?>,
                            adaptiveHeight: true,
                            useCSS : false,
                        });

                    }
                    <?php if($width != ''){?>
                
                    $('#<?php echo $gallery_id?>').css({width:width+'px'});
                    <?php } ?>
                });
                
            });
        </script>
        <?php
        $return .= ob_get_clean();
        
        return $return;
        
    }
    SmartShortCode::add_shortcode('sds_slider', 'sds_slider_cb');
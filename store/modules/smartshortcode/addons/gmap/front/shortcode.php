<?php
       SmartShortCode::add_shortcode('gmap', 'sds_gmap_cb');

    function sds_gmap_cb($atts = array()) {

        extract(SmartShortCode::shortcode_atts(array(
            'height' => '440',
            'lat' => '23.7467330',
            'lng' => '90.4202590',
            'type' => 'ROADMAP',
            'zoom' => '17',
            'mapTypeControl' => 'true',
            'scrollwheel' => 'false'
            ), $atts));

        if (empty($lat) || empty($lng))
            return false;
        
        $map_id = "map_".rand(00000,99999);
        
        ob_start();
        ?>
        <div class="google-maps">
            <div id="<?php echo $map_id?>"></div>        
        </div>
        <script type="text/javascript">
            jQuery(function($){
                $(window).load(function(){
                    var mapelem = $("#<?php echo $map_id?>");
                    mapelem.css({width:'100%',height:'<?php echo $height?>px'});

                    var latlng = new google.maps.LatLng(parseFloat(<?php echo $lat?>),parseFloat(<?php echo $lng?>));

                    // Creating an object literal containing the properties we want to pass to the map  
                    var options = {  
                        zoom: <?php echo $zoom?>, // This number can be set to define the initial zoom level of the map
                        center: latlng,
                        mapTypeId: google.maps.MapTypeId.<?php echo $type?>, // This value can be set to define the map type ROADMAP/SATELLITE/HYBRID/TERRAIN
                        mapTypeControl: <?php echo $mapTypeControl ?>,
                        scrollwheel: <?php echo $scrollwheel ?>
                    };  
                    // Calling the constructor, thereby initializing the map  
                    var map = new google.maps.Map(document.getElementById("<?php echo $map_id?>"), options);
                }); 
           }); 
        </script>    
        <?php
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

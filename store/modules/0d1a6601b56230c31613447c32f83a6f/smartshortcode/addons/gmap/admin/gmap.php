<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_gallery_form" class="smartshortcode_form">
    <div class="form-group"><label>Google map</label></div>
    <div class="tabfields form-group">
        <label>Height:</label><input type="text" placeholder="Numeric value *" class="map_height form-control" />
    </div>
    <div class="tabfields form-group">
        <label>Latitude:</label><input type="text" placeholder="Numeric value *" class="map_lat form-control" />
    </div>
    <div class="tabfields form-group">
        <label>Longitude:</label><input type="text" placeholder="Numeric value *" class="map_lng form-control" />
    </div>    
    <div class="tabfields form-group">
        <label>Zoom:</label><input type="text" placeholder="Numeric value *" class="map_zoom form-control" />
    </div>    
    <div class="tabfields form-group">
        <label>Map Type:</label>
        
        <select class="map_type form-control">
            <option value="ROADMAP">ROADMAP</option>
            <option value="SATELLITE">SATELLITE</option>            
            <option value="HYBRID">HYBRID</option>
            <option value="TERRAIN">TERRAIN</option>
        </select>
    </div>    
    <div class="tabfields form-group">
        <label>Map Type Control:</label><input type="checkbox" value="1" checked="checked" class="mapTypeControl" />
    </div>
    <div class="tabfields form-group">
        <label>Scroll Wheel:</label><input type="checkbox" value="1" class="scrollwheel" />
    </div>
    
    <div>        
        <input type="submit" class="btn btn-primary" id="submit_tab_values" value="Insert Shortcode" /> 
    </div>
</form>
<script type="text/javascript">
$(function(){
    
   
    
    $(document.body).on('submit','#smartshortcode_gallery_form',function(){
        
        var map_height = $(this).find('.map_height').val();
        var map_lat = $(this).find('.map_lat').val();
        var map_lng = $(this).find('.map_lng').val();
        var map_zoom = $(this).find('.map_zoom').val();
        var map_type = $(this).find('.map_type > option:selected').val();
        var mapTypeControl = $(this).find('.mapTypeControl').is(':checked');
        var scrollwheel = $(this).find('.scrollwheel').is(':checked');
        
        var scode = '[gmap ';
        if(map_lat != '')
        scode += 'height="'+map_height+'" ';
        if(map_lat != '')
        scode += 'lat="'+map_lat+'" ';
        if(map_lng != '')
        scode += 'lng="'+map_lng+'" ';
        if(map_zoom != '')
        scode += 'zoom="'+map_zoom+'" ';
        scode += 'type="'+map_type+'" ';
        scode += 'mapTypeControl="'+mapTypeControl+'" ';
        scode += 'scrollwheel="'+scrollwheel+'"][/gmap]';
        
        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
        
    });

});
</script>
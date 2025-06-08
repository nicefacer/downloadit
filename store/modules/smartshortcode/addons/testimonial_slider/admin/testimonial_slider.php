<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_gallery_form" class="smartshortcode_form">
    <div class="form-group"><label>Testimonial Slider</label></div>
    <div class="tabfields form-group">
        <label>Slider Speed:</label><input type="text" placeholder="Numeric value (in miliseconds)" class="slider_speed form-control" />
    </div>
    <div class="tabfields form-group">
        <label>Slider Width:</label><input type="text" placeholder="Numeric value" class="slider_width form-control" />
    </div>    
    <div class="tabfields form-group">
        <label>Slider Mode:</label>
        
        <select class="slider_mode form-control">
            <option value="vertical">Vertical</option>
            <option value="horizontal">Horizontal</option>            
        </select>
    </div>    
    <div class="tabfields form-group">
        <label>Slider Auto Start:</label><input type="checkbox" value="1" checked="checked" class="slider_auto" />
    </div>
    <div class="tabfields form-group">
        <label>Slider Infinite Loop:</label><input type="checkbox" value="1" class="slider_loop" />
    </div>
    <div class="tabfields form-group">
        <label>Responsive Slider:</label><input type="checkbox" value="1" class="slider_responsive" />
    </div>
    <div class="tabfields form-group">
        <label>Pause on Hover:</label><input type="checkbox" value="1" class="slider_autoHover" />
    </div>
    <div class="tabfields form-group">
        <label>Slider Controls:</label><input type="checkbox" value="1" class="slider_controls" />
    </div>
    
    <div class="tabfields_wrap">
        <div class="tabfields form-group">
            <label>Author Name:</label><input type="text" placeholder="Author Name *" class="slide_title form-control" />
        </div>        
        <div class="tabfields form-group">
            <label>Testimonial:</label>
            <textarea rows="5" cols="15" placeholder="Plain Text" class="slide_content form-control"></textarea>
        </div>        
    </div>
    <div>
        <input type="button" class="btn submit" value="Add Slide" id="add_new" />
        <input type="submit" class="btn btn-primary" id="submit_tab_values" value="Insert Shortcode" /> 
    </div>
</form>
<script type="text/javascript">
$(function(){
    
    var defaultfield = $('.tabfields_wrap').html();
    
    $(document.body).on('click','#add_new',function(){
        var parent = $(this).parent();
        parent.before('<div class="tabfields_wrap">'+defaultfield+'</div>');
    });
    var sds_slide_walk = function(elem){
        var scode = '';
        var stat = true;
        elem.find('.tabfields_wrap').each(function(){
            var title = $(this).find('.slide_title').val();                   
            var content = $(this).find('.slide_content').val();
            
            scode += '[tslide ';
               
            if(title != '')
                scode += 'author="'+title+'"';
            else{
                alert("Author Name is required!");
                $(this).find('.slide_title').focus();
                return stat = false;
            }
            scode += ']'+content+'[/tslide]';
            
        });
        if(!stat)
            return stat;
        
        return [stat,scode];
    };
    
    $(document.body).on('submit','#smartshortcode_gallery_form',function(){
        
        var slider_speed = $(this).find('.slider_speed').val();
        var slider_width = $(this).find('.slider_width').val();
        var slider_mode = $(this).find('.slider_mode > option:selected').val();
        var slider_auto = $(this).find('.slider_auto').is(':checked');
        var slider_loop = $(this).find('.slider_loop').is(':checked');
        var slider_responsive = $(this).find('.slider_responsive').is(':checked');
        var slider_controls = $(this).find('.slider_controls').is(':checked');
        var slider_autoHover = $(this).find('.slider_autoHover').is(':checked');
        
        var scode = '[testimonial_slider ';
        if(slider_speed != '')
        scode += 'speed="'+slider_speed+'" ';
        if(slider_width != '')
        scode += 'width="'+slider_width+'" ';    
        scode += 'mode="'+slider_mode+'" ';
        scode += 'auto="'+slider_auto+'" ';
        scode += 'loop="'+slider_loop+'" ';
        scode += 'responsive="'+slider_responsive+'" ';
        scode += 'controls="'+slider_controls+'" ';
        scode += 'autoHover="'+slider_autoHover+'"]';
        
        var status = true;
        
        status = sds_slide_walk($(this));
        
        if(!status){
            return false;
        }
        scode += status[1];
        scode += '[/testimonial_slider]';
        
        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
        
    });

});
</script>
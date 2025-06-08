<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_gallery_form" class="smartshortcode_form">
    <div class="form-group"><label>Image Slider</label></div>
    <div class="tabfields form-group">
        <label>Slider Speed:</label><input type="text" placeholder="Numeric value (in miliseconds)" class="slider_speed form-control" />
    </div>
    <div class="tabfields form-group">
        <label>Slider Width:</label><input type="text" placeholder="Numeric value" class="slider_width form-control" />
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
        <label>Slider Auto Hover:</label><input type="checkbox" value="1" class="slider_autoHover" />
    </div>
    <div class="tabfields form-group">
        <label>Slider Controls:</label><input type="checkbox" checked="checked" value="1" class="slider_controls" />
    </div>
    <div class="tabfields form-group">
        <label>Slider Pager:</label><input type="checkbox" value="1" class="slider_pager" />
    </div>
    
    <div class="tabfields_wrap">
        <div class="tabfields form-group">
            <label>Slide title:</label><input type="text" placeholder="Title Here" class="slide_title form-control" />
        </div>
        <div class="tabfields form-group">
            <label>Image Src:</label><input type="text" id="slide_image_src_1" placeholder="Image src *" class="slide_image_src form-control" /><i class="icon-folder-open sds_filemanager"></i>
        </div>
        <div class="tabfields form-group">
            <label>Slide Content:</label>
            <textarea rows="5" cols="15" placeholder="Slide Content" class="slide_content form-control"></textarea>
        </div>        
    </div>
    <div>
        <input type="button" class="btn submit" value="Add Slide" id="add_new" />
        <input type="submit" class="btn btn-primary" id="submit_tab_values" value="Insert Shortcode" /> 
    </div>
</form>
<script type="text/javascript">
   
$(function(){
    var adminpath = parent.window.location.pathname;
    adminpath = adminpath.substring(0,adminpath.lastIndexOf('/') + 1);
    
    var defaultfield = $('.tabfields_wrap').html();
    
    
    $(document.body).on('click','.sds_filemanager',function(){
        
        var filemanagerurl = adminpath+'filemanager/dialog.php';
        var win = window.self;
        var id = $(this).prev().attr('id');
        
        parent.tinymce.activeEditor.windowManager.open({
             title: 'Image Manager',
             file: filemanagerurl+'?type=1',
             width: 860,  
             height: 570,
             resizable: true,
             maximizable: true,
             inline: 1
             }, {
             setUrl: function (url) {
                     var fieldElm = win.document.getElementById(id);                     
                     fieldElm.value = url;
                     
             }
         });
     });
    
    
    $(document.body).on('click','#add_new',function(){
        var parent = $(this).parent();
        parent.before('<div class="tabfields_wrap">'+defaultfield+'</div>');
        var c = $('.tabfields_wrap').length;
        parent.prev().find('.slide_image_src').attr('id','slide_image_src_'+c);
    });
    var sds_slide_walk = function(elem){
        var scode = '';
        var stat = true;
        elem.find('.tabfields_wrap').each(function(){
            var title = $(this).find('.slide_title').val();
            var href = $(this).find('.slide_image_src').val();        
            var content = $(this).find('.slide_content').val();
            
            if(href == ''){
                alert('Image src is required');
                $(this).find('.slide_image_src').parent().addClass('has-error');
                $(this).find('.slide_image_src').focus();
                
                return stat = false;
            }else{
                scode += '[bx_slide ';
                $(this).find('.slide_image_src').parent().removeClass('has-error');
                scode += 'src="'+href+'" ';
            }
                
            if(title != '')
                scode += 'title="'+title+'"';

            scode += ']'+content+'[/bx_slide]';
            
        });
        if(!stat)
            return stat;
        
        return [stat,scode];
    };
    
    $(document.body).on('submit','#smartshortcode_gallery_form',function(){
        
        var slider_speed = $(this).find('.slider_speed').val();
        var slider_width = $(this).find('.slider_width').val();
        var slider_auto = $(this).find('.slider_auto').is(':checked');
        var slider_loop = $(this).find('.slider_loop').is(':checked');
        var slider_responsive = $(this).find('.slider_responsive').is(':checked');
        var slider_controls = $(this).find('.slider_controls').is(':checked');
        var slider_pager = $(this).find('.slider_pager').is(':checked');
        var slider_autoHover = $(this).find('.slider_autoHover').is(':checked');
        
        var scode = '[sds_slider ';
        
        if(slider_speed != '')
        scode += 'speed="'+slider_speed+'" ';
        if(slider_width != '')
        scode += 'width="'+slider_width+'" '; 
        scode += 'auto="'+slider_auto+'" ';
        scode += 'loop="'+slider_loop+'" ';
        scode += 'pager="'+slider_pager+'" ';
        scode += 'responsive="'+slider_responsive+'" ';
        scode += 'controls="'+slider_controls+'" ';
        scode += 'autoHover="'+slider_autoHover+'"]';
        
        var status = true;
        
        status = sds_slide_walk($(this));
        
        if(!status){
            return false;
        }
        scode += status[1];
        scode += '[/sds_slider]';
        
        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
        
    });

});
</script>
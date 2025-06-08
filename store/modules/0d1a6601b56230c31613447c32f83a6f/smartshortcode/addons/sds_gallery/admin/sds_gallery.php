<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_image_form" class="smartshortcode_form">
    <div class="form-group"><label>Gallery</label></div>
    <div class="tabfields form-group">
        <label>Lightbox gallery:</label><input type="checkbox" value="<?php echo 'magnific-'.rand(00000000,99999999);?>" class="lightbox_gallery" />
    </div>
    <div class="tabfields form-group">
        <label>Image Thumb Width:</label><input type="text" value="64" placeholder="Numeric value" class="slide_image_w form-control" />
    </div>
    <div class="tabfields form-group">
        <label>Image Height:</label><input type="text" placeholder="Numeric value" class="slide_image_h form-control" />
    </div> 
    <div class="tabfields_wrap">
        <div class="tabfields form-group">
            <label>Image title:</label><input type="text" placeholder="Title Here" class="slide_title form-control" />
        </div>
        <div class="tabfields form-group">
            <label>Image Src:</label><input type="text" id="slide_image_src_1" placeholder="Image src *" class="slide_image_src form-control" /><i class="icon-folder-open sds_filemanager"></i>
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
    
    $(document.body).on('click','.sds_filemanager',function(evt){
        evt.preventDefault();        
        var filemanagerurl = adminpath+'filemanager/dialog.php';
        win = window.self;
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
         
         //parent.tinymce.activeEditor.windowManager.close(win);
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
            var w = $('.slide_image_w').val();        
            var h = $('.slide_image_h').val();        
            var lb = $('input.lightbox_gallery').is(':checked') ? true : false;
            if(href == ''){
                alert('Image src is required');
                $(this).find('.slide_image_src').parent().addClass('has-error');
                $(this).find('.slide_image_src').focus();
                
                return stat = false;
            }else{
                scode += '[sds_gallery ';
                $(this).find('.slide_image_src').parent().removeClass('has-error');
                scode += 'src="'+href+'"';
            }
                
            if(title != '')
                scode += ' title="'+title+'"';
            if(w != '')
                scode += ' width="'+w+'" ';
            if(h != '')
                scode += ' height="'+h+'" ';
            if(!lb)
                scode += ' relation="false"';
                
            scode += ']';
            
        });
        if(!stat)
            return stat;
        
        return [stat,scode];
    };
    
    $(document.body).on('submit','#smartshortcode_image_form',function(){
        
        var scode = '', scodeb = '', scodea = '';
        var lb = $('input.lightbox_gallery').is(':checked') ? $('input.lightbox_gallery').val() : false;
        var status = true;
        
        status = sds_slide_walk($(this));
        
        if(!status){
            return false;
        }
        if(lb){
            scodeb += '[sds_gallery_wrap id="'+lb+'"]';
            scodea += '[/sds_gallery_wrap]';
        }
        scode += status[1];
        scode = scodeb+scode+scodea;
        
        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
        
    });

});
</script>
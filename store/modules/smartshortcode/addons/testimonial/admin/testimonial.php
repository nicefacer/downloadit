<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_testimonial_form" class="smartshortcode_form">
    <div class="form-group"><label>Testimonial</label></div>
    <div class="tabfields_wrap">
        <div class="tabfields form-group">
            <label>Testimonial author:</label><input type="text" value="Anonymous" class="testimonial_author form-control" />
        </div>
        <div class="tabfields form-group">
            <label>Testimonial Text:</label>
            <textarea rows="5" cols="15" class="testimonial_content form-control">Testimonial text</textarea>
        </div>
        <div class="tabfields form-group">
            <label>Testimonial Style:</label>
            <select class="testimonial_style form-control">
                <option value="0">1</option>
                <option value="1">2</option>
                <option value="2">3</option>
                <option value="3">4</option>                
            </select>
        </div>
        <div class="tabfields form-group">
            <label>Additional Classes:</label><input type="text" class="testimonial_add_class form-control" />
        </div>       
    </div>
    <div>        
        <input type="submit" class="btn btn-primary" value="Insert Shortcode" /> 
    </div>
</form>
<script type="text/javascript">
$(function(){
    
    var adminpath = parent.window.location.pathname;
    adminpath = adminpath.substring(0,adminpath.lastIndexOf('/') + 1);
    
    var designclasses = ['shortcode_testimonial_1','shortcode_testimonial_2','shortcode_testimonial_3','shortcode_testimonial_4 bs-example-popover'];
    
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
    
    $(document.body).on('change','.testimonial_style',function(){
        
        var nhtml = '<div class="tabfields form-group"><label>Image src:</label><input type="text" id="testimonial_author_photo" placeholder="Put image src here." class="testimonial_author_photo form-control" /><i class="icon-folder-open sds_filemanager"></i></div>';
        var sval = parseInt($(this).children('option:selected').val());        
        if( sval > 1 && $(this).parents('#smartshortcode_testimonial_form').find('.testimonial_author_photo').length < 1){
            $(this).parent().after(nhtml);
        }else if(sval < 2){
            if($(this).parents('#smartshortcode_testimonial_form').find('.testimonial_author_photo').length > 0)
                $('.testimonial_author_photo').parent().remove();
        }       
    });
    
    $(document.body).on('submit','#smartshortcode_testimonial_form',function(){
                
        var content = $(this).find('.testimonial_content').val();                
        var author = $(this).find('.testimonial_author').val();                
        var style = $(this).find('.testimonial_style > option:selected').val();
        var extra = $(this).find('.testimonial_add_class').val();
        
        var scode = '[testimonial style="'+style+'" author="'+author+'" class="fix '+designclasses[parseInt(style)];
        
        if(extra != '')
            scode += ' '+extra;
        
        scode += '"';
        
        if($(this).find('.testimonial_author_photo').length > 0){
            scode += ' image_src="'+$(this).find('.testimonial_author_photo').val()+'"';
        }
        
        scode += ']'+content+'[/testimonial]';
        
        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
        
    });

});
</script>
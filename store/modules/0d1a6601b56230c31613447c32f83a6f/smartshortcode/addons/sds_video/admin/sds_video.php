<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_viframe_form" class="smartshortcode_form">
    <div class="form-group"><label>Video</label></div>
    <div class="tabfields_wrap">
        <div class="tabfields form-group"><label>Insert video src:</label>
            <textarea rows="5" cols="15" placeholder="http://" class="video_src form-control"></textarea>
        </div>
        <div class="tabfields form-group">
            <label>Iframe width:</label><input type="text" placeholder="Numbers only" class="iwidth form-control" />
        </div>
        <div class="tabfields form-group">
            <label>Iframe height:</label><input type="text" placeholder="Numbers only" class="iheight form-control" />
        </div>        
    </div>
    <div>        
        <input type="submit" class="btn btn-primary" value="Insert Shortcode" /> 
    </div>
</form>
<script type="text/javascript">
$(function(){
    
    $(document.body).on('submit','#smartshortcode_viframe_form',function(){
        
        var src = $('.video_src').val();
        var w = $('.iwidth').val();
        var h = $('.iheight').val();
        var scode = '[sds_video src="'+src+'" width="'+w+'" height="'+h+'"][/sds_video]';
        
        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
    });

    
});
</script>
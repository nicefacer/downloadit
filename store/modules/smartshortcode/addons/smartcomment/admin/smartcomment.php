<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_sp_form" class="smartshortcode_form">
    <div class="form-group"><label>Smart Blog Latest Comments</label></div>
    <div class="tabfields_wrap">
        <div class="tabfields form-group">
            <label>Per page:</label><input type="text" value="5" class="per_page form-control" />
        </div>        
    </div>
    <div>        
        <input type="submit" class="btn btn-primary" id="submit_tab_values" value="Insert Shortcode" /> 
    </div>
</form>
<script type="text/javascript">
$(function(){
    
    $(document.body).on('submit','#smartshortcode_sp_form',function(){
        
        var per_page = $(this).find('.per_page').val();
        
        var scode = '[smartcomment';
        
        if(per_page != '')
            scode += ' limit="'+per_page+'"';
       
        scode += '][/smartcomment]';
        
        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
        
    });

});
</script>
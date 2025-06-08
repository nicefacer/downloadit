<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_note_form" class="smartshortcode_form">
    <div class="form-group"><label>Notification</label></div>
    <div class="tabfields_wrap">
        <div class="tabfields form-group">
            <label>Notification Text:</label>
            <textarea rows="5" cols="15" class="note_content form-control">Notification text</textarea>
        </div>        
        <div class="tabfields form-group">
            <label>Notification Style:</label>
            <select class="note_style form-control">
                <option value="alert_box_default">Default</option>                
                <option value="alert_box_blue">Blue</option>                
                <option value="alert_box_red">Red</option>                
                <option value="alert_box_green">Green</option>                
                <option value="alert_box_yellow">Yellow</option>                
            </select>
        </div>
        <div class="tabfields form-group">
            <label>Additional Classes:</label><input type="text" value="fade in fix" class="note_add_class form-control" />
        </div>       
    </div>
    <div>        
        <input type="submit" class="btn btn-primary" id="submit_tab_values" value="Insert Shortcode" /> 
    </div>
</form>
<script type="text/javascript">
$(function(){
    
    $(document.body).on('submit','#smartshortcode_note_form',function(){
        
        var content = $(this).find('.note_content').val();                
        var style = $(this).find('.note_style > option:selected').val();        
        var extra = $(this).find('.note_add_class').val();
        
        var scode = '[alert_box class="alert_box_content ';
        
        scode += style;
        
        if(extra != '')
            scode += ' '+extra;
        
        scode += '"]'+content+'[/alert_box]';
        
        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
        
    });

});
</script>
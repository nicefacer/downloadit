<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_quote_form" class="smartshortcode_form">
    <div class="form-group"><label>Block Quote</label></div>
    <div class="tabfields_wrap">
        <div class="tabfields form-group">
            <label>Block quote author:</label><input type="text" value="Anonymous" class="quote_author form-control" />
        </div>
        <div class="tabfields form-group">
            <label>Block quote Text:</label>
            <textarea rows="5" cols="15" class="quote_content form-control">Block quote text</textarea>
        </div>        
        <div class="tabfields form-group">
            <label>Block quote Reverse:</label>
            <input type="checkbox" value="1" class="quote_reverse" />                
        </div>
        <div class="tabfields form-group">
            <label>Additional Classes:</label><input type="text" value="" class="quote_add_class form-control" />
        </div>       
    </div>
    <div>        
        <input type="submit" class="btn btn-primary" value="Insert Shortcode" /> 
    </div>
</form>
<script type="text/javascript">
$(function(){
    
    $(document.body).on('submit','#smartshortcode_quote_form',function(){
        
        var content = $(this).find('.quote_content').val();                
        var author = $(this).find('.quote_author').val();                
        var style = $(this).find('.quote_reverse').is(':checked') === true ? 'blockquote-reverse': '';        
        var extra = $(this).find('.quote_add_class').val();
        
        var scode = '[blockquote author="'+author+'" class="'+style;
        
        if(extra != '')
            scode += ' '+extra;
        
        scode += '"]'+content+'[/blockquote]';
        
        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
        
    });

});
</script>
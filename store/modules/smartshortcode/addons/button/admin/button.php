<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_button_form" class="smartshortcode_form">
    <div class="form-group"><label>Button</label></div>
    <div class="tabfields_wrap">
        <div class="tabfields form-group">
            <label>Button label:</label><input type="text" value="Button" class="button_title form-control" />
        </div>
        <div class="tabfields form-group">
            <label>Button Hyperlink:</label><input type="text" class="button_link form-control" />
        </div>
        <div class="tabfields form-group">
            <label>Button Target:</label>
            <select class="button_target form-control">
                <option value="_self">Self</option>
                <option value="_parent">Parent</option>
                <option value="_blank">Blank</option>
                <option value="_top">Top</option>                
            </select>
        </div>
        
        <div class="tabfields form-group">
            <label>Button Size:</label>
            <select class="button_size form-control">
                <option value="">Default</option>
                <option value="sds-btn-large">Large</option>
                <option value="sds-btn-small">Small</option>
            </select>
        </div>        
        <div class="tabfields form-group">
            <label>Button Style:</label>
            <select class="button_style form-control">
                <?php for($i=1;$i<11;$i++)
                echo "<option value='sds-btn-color-{$i}'>{$i}</option>";
                ?>                
            </select>
        </div>
        <div class="tabfields form-group">
            <label>Button Additional Class:</label><input type="text" class="button_add_class form-control" />
        </div>
    </div>
    <div>        
        <input type="submit" class="btn btn-primary" id="submit_tab_values" value="Insert Shortcode" /> 
    </div>
</form>
<script type="text/javascript">
$(function(){
    
    $(document.body).on('submit','#smartshortcode_button_form',function(){
        
        var title = $(this).find('.button_title').val();
        var href = $(this).find('.button_link').val();
        var size = $(this).find('.button_size > option:selected').val();
        var style = $(this).find('.button_style > option:selected').val();
        var target = $(this).find('.button_target > option:selected').val();
        var extra = $(this).find('.button_add_class').val();
        
        var scode = '[button target="'+target+'" href="'+href+'" class="sds-btn ';
        
        if(size != '')
            scode += size+' ';
        
        scode += style;
        
        if(extra != '')
            scode += ' '+extra;
        
        scode += '"]'+title+'[/button]';
        
        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
        
    });

});
</script>
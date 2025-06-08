<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_accordion_form" class="smartshortcode_form">
    <div class="form-group"><label>Spoiler</label></div>
    <div class="tabfields_wrap"><div class="tabfields form-group"><label>Spoiler1 title:</label><input type="text" value="Title" class="tab_title form-control" /></div><div class="tabfields form-group"><label>Spoiler1 Content:</label><textarea rows="5" cols="15" class="tab_content form-control">Content</textarea></div></div>
    <div>
        <input type="button" class="btn submit" value="Add Tab" id="add_new" />
        <input type="submit" class="btn btn-primary" id="submit_tab_values" value="Insert Shortcode" /> 
    </div>
</form>
<script type="text/javascript">
$(function(){
    $(document.body).on('click','#add_new',function(){
        var buttonp = $(this).parent();
        var counter = $('.tabfields_wrap').length;        
        counter++;        
        buttonp.before('<div class="tabfields_wrap"><div class="tabfields form-group"><label>Spoiler'+counter+' title:</label><input type="text" value="Title" class="tab_title form-control" /></div><div class="tabfields form-group"><label>Spoiler'+counter+' Content:</label><textarea rows="5" cols="15" class="tab_content form-control">Content</textarea></div></div>');
    });
    
    $(document.body).on('submit','#smartshortcode_accordion_form',function(){
        
        var tbc = 0;
        if($('.tabfields_wrap').length < 1){
            alert('No tabs added yet.');
            $("#add_new").trigger('click');
            return false;
        }
        
        var scode = '[spoiler]';
        
        $('.tabfields_wrap').each(function(){
            tbc++;
            var elem = $(this);
            var title = elem.find('.tab_title');
            if(title.val() == ''){
                alert('Please enter Tab'+tbc+' title.');
                title.trigger('focus');
                return false;
            }
            var content = elem.find('.tab_content');
            
            scode += '[atab accordion=false title="'+title.val()+'"]'+content.val()+'[/atab]';
            
        });
        
        scode += '[/spoiler]';
        
        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
    });

    
});
</script>
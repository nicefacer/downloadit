<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_tab_form" class="smartshortcode_form">
<!--    <div class="form-group">
        <label>Active tab</label>
        <input type="number" class="input-medium form-control" id="active_tab" />
    </div>-->
    <div class="form-group">
        <label>Orientation</label>
        <select class="input-medium form-control" id="tab_orientation">
            <option value="1" selected="selected">Horizontal</option>
            <option value="2">Vertical with left tab</option>
            <option value="3">Vertical with right tab</option>            
        </select>
    </div>
    <div class="tabfields_wrap">
        <div class="tabfields form-group">
            <label>Tab title:</label>
            <input type="text" value="Tab Title" class="tab_title form-control" />
        </div>
        <div class="tabfields form-group">
            <label>Tab Content:</label>
            <textarea rows="5" cols="15" class="tab_content form-control">Tab Content</textarea>
        </div>
    </div>
    <div>        
        <input type="button" class="btn submit" value="Add Tab" id="add_new" />
        <input type="submit" class="btn btn-primary" id="submit_tab_values" value="Insert Shortcode" /> 
    </div>
</form>
<script type="text/javascript">
$(function(){
    
    var tabhtml = $('.tabfields_wrap').html();
    $(document.body).on('click','#add_new',function(){
        var buttonp = $(this).parent();
        buttonp.before('<div class="tabfields_wrap">'+tabhtml+'</div>');
//        var counter = $('.tabfields_wrap').length;        
//        counter++;        
//        buttonp.before('<div class="tabfields_wrap"><div class="tabfields form-group"><label>Tab'+counter+' title:</label><input type="text" value="Tab Title" class="tab_title form-control" /></div><div class="tabfields form-group"><label>Tab'+counter+' Content:</label><textarea rows="5" cols="15" class="tab_content form-control">Tab Content</textarea></div></div>');
    });
    
    $(document.body).on('submit','#smartshortcode_tab_form',function(){
        
        var tbc = 0;
        var tt = true;
        if($('.tabfields_wrap').length < 1){
            alert('No tabs added yet.');
            $("#add_new").trigger('click');
            return false;
        }
        var orientation = $("#tab_orientation > option:selected").val();
//        var active = $('#active_tab').val();
        
        var scode = '[tabgroup class="shortcode_tab_'+orientation+'"';
//        if(active != '')
//            scode += ' active="'+active+'"';
        scode += ']';
        
        
        $('.tabfields_wrap').each(function(){
            tbc++;
            var elem = $(this);
            var title = elem.find('.tab_title');
            if(title.val() == ''){
                alert('Please enter Tab'+tbc+' title.');
                title.trigger('focus');
                return tt = false;
            }
            var content = elem.find('.tab_content');
            
            scode += '[tab title="'+title.val()+'"]'+content.val()+'[/tab]';
            
        });
        if(!tt) return false;
        
        
        scode += '[/tabgroup]';
        
        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
    });

    
});
</script>
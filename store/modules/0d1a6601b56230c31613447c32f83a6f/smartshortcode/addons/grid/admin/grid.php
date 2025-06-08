<?php
$grids = array(1,2,3,4,6,8,9,10,12);
?>
<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_grid_form" class="smartshortcode_form">
    <div class="form-group"><label>Columns</label></div>
    <div class="tabfields form-group">
        <label>Number of columns:</label>
        <select class="num_cols form-control">
            <option value='1'>one column</option>                
            <option value='2'>two columns</option>                
            <option value='3'>three columns</option>                
            <option value='4'>four columns</option>                
        </select>
    </div>
    <div class="row">
        <div class="tabfields_wrap col-sm-12">
            <div class="tabfields form-group">
                <label>Grid Content:</label>
                <textarea rows="5" cols="15" class="grid_content form-control">Grid Content</textarea>
            </div>
            <div class="tabfields form-group">
                <label>Large Grid:</label>
                <select class="large_grid_style form-control">
                    <?php                 
                    foreach($grids as $i)
                    echo "<option value='{$i}'>{$i}</option>";
                    ?>                
                </select>
            </div>
            <div class="tabfields form-group">
                <label>Medium Grid:</label>
                <select class="medium_grid_style form-control">
                    <?php                 
                    foreach($grids as $i)
                    echo "<option value='{$i}'>{$i}</option>";
                    ?>                
                </select>
            </div>
            <div class="tabfields form-group">
                <label>Small Grid:</label>
                <select class="small_grid_style form-control">
                    <?php                 
                    foreach($grids as $i)
                    echo "<option value='{$i}'>{$i}</option>";
                    ?>                
                </select>
            </div>
            <div class="tabfields form-group">
                <label>Extra Small Grid:</label>
                <select class="xsmall_grid_style form-control">
                    <?php                 
                    foreach($grids as $i)
                    echo "<option value='{$i}'>{$i}</option>";
                    ?>                
                </select>
            </div>
            <div class="tabfields form-group">
                <label>Additional Class:</label><input type="text" class="grid_add_class form-control" />
            </div>
        </div>
    </div>
    <div class="tabfields form-group">
        <label>Insert Grid Row:</label><input type="checkbox" class="grid_row" value="1" />
    </div>
    <div>
        <input type="submit" class="btn btn-primary" id="submit_tab_values" value="Insert Shortcode" /> 
    </div>
</form>
<script type="text/javascript">
$(function(){
    
    var def_col_setting = $('.tabfields_wrap').html();
    
    $(document.body).on('change','.num_cols',function(){
        var parent = $(this).parent();
        var loop = $(this).children('option:selected').val();
        var tabwrapclass = "";
        $('form.smartshortcode_form').find('.tabfields_wrap').remove();
        for(n = 0; n < parseInt(loop) ; n++){
            var smno, xsno;
            switch(parseInt(loop)){
                case 2:
                    tabwrapclass = "col-sm-6";
                    smno = 6;
                    xsno = 12;
                    break;
                case 3:
                    tabwrapclass = "col-sm-4";
                    smno = 4;
                    xsno = 12;
                    break;
                case 4:
                    tabwrapclass = "col-sm-3";
                    smno = 3;
                    xsno = 12;
                    break;
                default:
                    tabwrapclass = "col-sm-12";
                    smno = xsno = 12;
                    break;
            }
            
            parent.next().append('<div class="tabfields_wrap '+tabwrapclass+'">'+def_col_setting+'</div>');
            
            $('.tabfields_wrap select').each(function(){
                if($(this).hasClass('xsmall_grid_style')){
                    $(this).children('option[value="'+xsno+'"]').attr('selected',true);
                }else{
                    $(this).children('option[value="'+smno+'"]').attr('selected',true);
                }
            });
            
        }
    });
    
    $(document.body).on('submit','#smartshortcode_grid_form',function(){
        
        
        var scode = '';
        var $form = $(this);
        
        var hasgridrow = $form.find('.grid_row').is(':checked');
        
        if(hasgridrow){
            scode += '[gridrow]';
        }
        
        $form.find('.tabfields_wrap').each(function(){
            
            var content = $(this).find('.grid_content').val();           
            var xs = $(this).find('.xsmall_grid_style > option:selected').val();
            var sm = $(this).find('.small_grid_style > option:selected').val();
            var md = $(this).find('.medium_grid_style > option:selected').val();
            var lg = $(this).find('.large_grid_style > option:selected').val();
            var extra = $(this).find('.grid_add_class').val();

            scode += '[grid class="';

            if(lg != '')
                scode += 'col-lg-'+lg+' ';
            if(md != '')
                scode += 'col-md-'+md+' ';
            if(sm != '')
                scode += 'col-sm-'+sm+' ';
            if(xs != '')
                scode += 'col-xs-'+xs;
            if(extra != '')
                scode += ' '+extra;

            scode += '"]'+content+'[/grid]';
        
        });
        
        if(hasgridrow){
            scode += '[/gridrow]';
        }
        
        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
        
    });

});
</script>
<?php
if(Module::isEnabled('revsliderprestashop') == 1 &&
    Module::isInstalled('revsliderprestashop') == 1 &&
    file_exists(_PS_MODULE_DIR_.'revsliderprestashop/revprestashoploader.php')){            
$exists = "SHOW TABLES LIKE '"._DB_PREFIX_."revslider_sliders'";
$result = Db::getInstance()->ExecuteS($exists);
if(empty($result)) {echo ''; return;}

$sql = "SELECT title,alias FROM "._DB_PREFIX_."revslider_sliders";
$aliases = Db::getInstance()->ExecuteS($sql);
?>
<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_rev_form" class="smartshortcode_form">
    <div class="form-group"><label>Revolution slider</label></div>
    <div class="tabfields_wrap">              
        <div class="tabfields form-group">
            <label>Slider Alias:</label>
            <select class="alias form-control">                
                <option value="">Select</option>
                <?php if(!empty($aliases)){
                    foreach($aliases as $alias)
                        echo "<option value='{$alias['alias']}'>{$alias['title']}</option>";         
                 } ?>
            </select>
        </div>
          
    </div>
    <div>        
        <input type="submit" class="btn btn-primary" id="submit_tab_values" value="Insert Shortcode" /> 
    </div>
</form>
<script type="text/javascript">
$(function(){
    
    $(document.body).on('submit','#smartshortcode_rev_form',function(){
        
        
        var alias = $(this).find('.alias > option:selected').val();        
        if(alias == ''){
            $(this).find('.alias').focus();
            alert("Slider alias is required.");
            return false;
        }
        
        var scode = '[rev_slider ';        
        scode += alias;        
        scode += ']';
        
        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
        
    });

});
</script>

<?php
 }
?>
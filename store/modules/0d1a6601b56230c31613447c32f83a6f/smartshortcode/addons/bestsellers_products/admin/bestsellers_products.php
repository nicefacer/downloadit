<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_bs_form" class="smartshortcode_form">
    <div class="form-group"><label>Best sellers products</label></div>
    <div class="tabfields_wrap">
        <div class="tabfields form-group">
            <label>Per page:</label><input type="text" value="12" class="per_page form-control" />
        </div>
        
        <div class="tabfields form-group">
            <label>Order by:</label>
            <select class="order_by form-control">
                <option value="sales">Sales</option>                       
                <option value="price">Price</option>                       
                <option value="date_add">Published Date</option>                       
                <option value="name">Product Name</option>
                <option value="position">Position</option>                       
            </select>
        </div>
        
        <div class="tabfields form-group">
            <label>Order:</label>
            <select class="order form-control">                
                <option value="DESC">DESC</option>
                <option value="ASC">ASC</option>
            </select>
        </div>        
        
    </div>
    <div>        
        <input type="submit" class="btn btn-primary" id="submit_tab_values" value="Insert Shortcode" /> 
    </div>
</form>
<script type="text/javascript">
$(function(){
    
    $(document.body).on('submit','#smartshortcode_bs_form',function(){
        
        var per_page = $(this).find('.per_page').val();
        var random = $(this).find('.random').is(':checked')? 'true' : 'false';
        var orderby = $(this).find('.order_by > option:selected').val();
        var order = $(this).find('.order > option:selected').val();
        
        var scode = '[bestsellers_products orderby="'+orderby+'" order="'+order+'"';
        
        if(per_page != '')
            scode += ' per_page="'+per_page+'"';
       
        scode += '][/bestsellers_products]';
        
        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
        
    });

});
</script>
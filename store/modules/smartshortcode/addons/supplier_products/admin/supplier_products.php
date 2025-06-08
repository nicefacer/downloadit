<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_sp_form" class="smartshortcode_form">
    <div class="form-group"><label>Supplier products</label></div>
    <div class="tabfields_wrap">
        <div class="tabfields form-group">
            <label>Page:</label><input type="text" value="1" class="page form-control" />
        </div>
        <div class="tabfields form-group">
            <label>Supplier:</label>
            <select class="supplier form-control">
                <?php 
                $suppliers = Supplier::getSuppliers();
                if(!empty($suppliers)){   
                    foreach($suppliers as $supplier)
                    echo '<option value="'.$supplier['id_supplier'].'">'.$supplier['name'].'</option>';
                }
                ?>
            </select>
        </div>
                <div class="tabfields form-group">
            <label>Order by:</label>
            <select class="order_by form-control">
                <option value="id_product">Product id</option>                       
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
    
    $(document.body).on('submit','#smartshortcode_sp_form',function(){
        
        var page = $(this).find('.page').val();
        var per_page = $(this).find('.per_page').val();
        
        var orderby = $(this).find('.order_by > option:selected').val();
        var order = $(this).find('.order > option:selected').val();
        var supplier = $(this).find('.supplier > option:selected').val();
        if(supplier != undefined){
            var scode = '[supplier_products id_supplier="'+supplier+'" orderby="'+orderby+'" order="'+order+'"';

            if(per_page != '')
                scode += ' page="'+page+'"';
            if(per_page != '')
                scode += ' per_page="'+per_page+'"';

            scode += '][/supplier_products]';

            parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        }
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
        
    });

});
</script>
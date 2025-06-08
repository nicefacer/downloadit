<?php
class SDS_shortcode_category_walker{
    
    public $categories, $smartcat;
    public function __construct($cats){
        $this->smartcat = '';
        
        $this->categories = $cats;
        $this->generateCategoriesOption();
    }
    public function generatesubCategoriesOption($categories, $items_to_skip = null)
        {
                
                foreach ($categories as $key => $category)
                {   
                    $this->smartcat .= "<option value='{$category['id_category']}'>{$category['name']}</option>";

                    if (isset($category['children']))
                              $this->generatesubCategoriesOption($category['children']);
//                
                }
                return true;
        }
        public function generateCategoriesOption( $items_to_skip = null)
        {
                
                foreach ($this->categories as $key => $category)
                {
                    $this->smartcat .= "<option value='{$category['id_category']}'>{$category['name']}</option>";
                    
                        if (isset($category['children']))
                                  $this->generatesubCategoriesOption($category['children']);

                }

                return $this->smartcat;
        }
        
        public function getSelectdropdown(){
            return "<select class='category form-control'>{$this->smartcat}</select>";
        }

}

$cats = Category::getNestedCategories(null, (int)Context::getContext()->language->id, true);
$SDS_shortcode_category_walker = new SDS_shortcode_category_walker($cats);
$smartcatsdropdown = $SDS_shortcode_category_walker->getSelectdropdown();

?>
<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_cp_form" class="smartshortcode_form">
    <div class="form-group"><label>Category products</label></div>
    <div class="tabfields_wrap">
        <div class="tabfields form-group">
            <label>Per page:</label><input type="text" value="12" class="per_page form-control" />
        </div>
        
        <div class="tabfields form-group">
            <label>Category:</label>
            <?php echo $smartcatsdropdown;?>
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
    
    $(document.body).on('submit','#smartshortcode_cp_form',function(){
        
        var per_page = $(this).find('.per_page').val();        
        var category = $(this).find('.category > option:selected').val();
        var orderby = $(this).find('.order_by > option:selected').val();
        var order = $(this).find('.order > option:selected').val();
        
        var scode = '[category_products id_category="'+category+'" orderby="'+orderby+'" order="'+order+'"';
        
        if(per_page != '')
            scode += ' per_page="'+per_page+'"';
       
        scode += '][/category_products]';
        
        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
        
    });

});
</script>
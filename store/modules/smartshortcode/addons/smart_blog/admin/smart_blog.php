<?php

if(!file_exists(_PS_MODULE_DIR_.'smartblog/classes/BlogCategory.php')) return '';
class SDS_blog_walker{
    public static $context;
    public $categories, $pcategories, $products, $smartcat, $smartpcat, $smartpr;
    public function __construct($products,$pcats,$cats){
        $this->smartcat = '<option value="">Select</option>';
        $this->smartpr = '';
        $this->categories = $cats;
        $this->pcategories = $pcats;
        $this->products = $products;
        $this->generateCategoriesOption();
        $this->generateProductsOption();
        $this->generateProCategoriesOption();
    }
    public static function _init(){
        self::$context = Context::getContext();
    }
    public function generatesubCategoriesOption($categories)
    {
            foreach ($categories as $key => $category)
            {   
                $this->smartpcat .= "<option value='{$category['id_category']}'>{$category['name']}</option>";
                if (isset($category['children']))
                          $this->generatesubCategoriesOption($category['children']);
            }
            return true;
    }
    public function generateProCategoriesOption()
    {
            foreach ($this->pcategories as $key => $category)
            {
                $this->smartpcat .= "<option value='{$category['id_category']}'>{$category['name']}</option>";
                    if (isset($category['children']))
                              $this->generatesubCategoriesOption($category['children']);
            }
            return $this->smartpcat;
    }
    public function generateCategoriesOption()
    {
            foreach ($this->categories as $key => $category)
            {
                if(SDS_blog_walker::$context->shop->id == $category['id_shop'])                
                    $this->smartcat .= "<option value='{$category['id_smart_blog_category']}'>{$category['meta_title']}</option>";
            }
            return $this->smartcat;
    }
    public function generateProductsOption()
    {
            foreach ($this->products as $key => $p)
            {                
                if(SDS_blog_walker::$context->shop->id == $p['id_shop'])                
                    $this->smartpr .= "<option value='{$p['id_product']}'>{$p['name']}</option>";
            }
            return $this->smartpr;
    }
    public function getSelectdropdown(){
        return "<select class='category form-control'>{$this->smartcat}</select>";
    }
    public function getProCatSelectdropdown(){
        return "<select multiple='true' class='pcategory form-control'>{$this->smartpcat}</select>";
    }
    public function getProductsSelectdropdown(){
        return "<select multiple='true' class='product form-control'>{$this->smartpr}</select>";
    }
}
SDS_blog_walker::_init();
$cats = BlogCategory::getCategory(1, (int)SDS_blog_walker::$context->language->id);
$products = Product::getProducts((int)SDS_blog_walker::$context->language->id, 0, 0, 'date_upd', 'DESC');
$pcats = Category::getNestedCategories(null, (int)SDS_blog_walker::$context->language->id, true);
$SDS_shortcode_category_walker = new SDS_blog_walker($products,$pcats,$cats);
$smartcatsdropdown = $SDS_shortcode_category_walker->getSelectdropdown();
$smartproductsdropdown = $SDS_shortcode_category_walker->getProductsSelectdropdown();
$smartprocatsdropdown = $SDS_shortcode_category_walker->getProCatSelectdropdown();
?>
<a href="" class="backlink">Back to shortcode main.</a>
<form action="" id="smartshortcode_cp_form" class="smartshortcode_form">
    <div class="form-group"><label>Smart Blog</label></div>
    <div class="tabfields_wrap">
        <div class="tabfields form-group">
            <label>Heading Title:</label><input type="text" value="SmartBlog" class="title form-control" />
        </div>
        <div class="tabfields form-group">
            <label>Per page:</label><input type="text" value="12" class="per_page form-control" />
        </div>
        <div class="tabfields form-group">
            <label>Post Category:</label>
            <?php echo $smartcatsdropdown;?>
        </div>
        <div class="tabfields form-group">
            <label>Show only on specific Product:</label>
            <?php echo $smartproductsdropdown;?>
        </div>
        <div class="tabfields form-group">
            <label>Show only on specific product category:</label>
            <?php echo $smartprocatsdropdown;?>
        </div>
    </div>
    <div>        
        <input type="submit" class="btn btn-primary" id="submit_tab_values" value="Insert Shortcode" /> 
    </div>
</form>
<script type="text/javascript">
$(function(){
    $(document.body).on('submit','#smartshortcode_cp_form',function(){
        var title = $(this).find('.title').val();        
        var per_page = $(this).find('.per_page').val();        
        var category = $(this).find('.category > option:selected').val();
        var pcategory = '';
        var products = '';
        var c = 0;
        $(this).find('.pcategory > option:selected').each(function(){
            if(c++ > 0)
                pcategory += ',';
            pcategory += $(this).val();            
        });
        c = 0;
        $(this).find('.product > option:selected').each(function(){
            if(c++ > 0)
                products += ',';
            products += $(this).val();             
        });
        var scode = '[smartblog blog_cat="'+category+'" pcats="'+pcategory+'" products="'+products+'"';
        if(per_page != '')
            scode += ' per_page="'+per_page+'"';
        scode += ' title="'+title+'"][/smartblog]';
        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
        parent.tinyMCE.activeEditor.windowManager.close();
        return false;
    });
});
</script>
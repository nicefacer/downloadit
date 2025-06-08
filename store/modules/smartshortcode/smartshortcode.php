<?php

if (!defined('_PS_VERSION_'))
    exit;
require_once (dirname(__FILE__) . '/classes/smartanywherecontent.php');
require_once (dirname(__FILE__) . '/classes/SmartProductTabCreator.php');
class SmartShortCode extends Module {
    public $shortcodes = array();
    public static $smartshortcode, $static_shortcode_tags = array(),$fires = 0,$sds_current_hook = '';
    public function __construct()
    {
        $this->name = 'smartshortcode';
        $this->tab = 'front_office_features';
        $this->version = '2.3.0';
        $this->author = 'SmartDataSoft';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Smart Short Code');
        $this->description = $this->l('Smart Short Code System');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        if(file_exists(_PS_ROOT_DIR_."/js/admin/tinymce.inc.js")){
            $filename = _PS_ROOT_DIR_."/js/admin/tinymce.inc.js";
        }else{
            $filename = _PS_ROOT_DIR_."/js/tinymce.inc.js";
        }
        if(!is_writable($filename))
        {
            $warning = "Please change to this '"._PS_ROOT_DIR_."/js' folder permission (Write Permission Needed).";
            $this->adminDisplayWarning(html_entity_decode($warning));
        }
    }
    public function install()
    {
        $langs = Language::getLanguages();
        if(!parent::install() 
            || !$this->registerHook('displayHeader') 
            || !$this->registerHook('displayBackOfficeHeader') 
            || !$this->registerHook('displayBanner') 
            || !$this->registerHook('displayFooter') 
            || !$this->registerHook('displayFooterProduct') 
            || !$this->registerHook('displayHome') 
            || !$this->registerHook('displayHomeTab') 
            || !$this->registerHook('displayHomeTabContent') 
            || !$this->registerHook('displayLeftColumn') 
            || !$this->registerHook('displayLeftColumnProduct') 
            || !$this->registerHook('displayMaintenance') 
            || !$this->registerHook('displayMyAccountBlock') 
            || !$this->registerHook('displayMyAccountBlockfooter') 
            || !$this->registerHook('displayNav') 
            || !$this->registerHook('displayProductTab') 
            || !$this->registerHook('displayProductTabContent') 
            || !$this->registerHook('displayRightColumn') 
            || !$this->registerHook('displayRightColumnProduct') 
            || !$this->registerHook('displayTop') 
            || !$this->registerHook('displayTopColumn') 
            || !$this->registerHook('sdsShortcodeAdminLists') 
            || !$this->registerHook('sdsShortcodeAdminPages') 
            || !$this->registerHook('sdsShortcodeFront')       
            || !$this->moduleControllerRegistration() 
        )
            return false;
        /* Start Install Database*/
        $this->storfile();
        $sql = array();
        require_once(dirname(__FILE__) . '/sql/install.php');
        foreach ($sql as $sq) :
            if (!Db::getInstance()->Execute($sq))
                return false;
        endforeach;
        /* End Install Database*/
         $this->CreateShortCodeTabs();
        Configuration::updateGlobalValue('smart_load_font','module');
        Configuration::updateGlobalValue('smart_load_bootstrapcss','theme');
        Configuration::updateGlobalValue('smart_load_bootstrapjs','theme');
        Configuration::updateGlobalValue('smart_shortcode_tab_style','tab');
        return true;
    }
    public function uninstall()
    {
        if(!parent::uninstall())
            return false;
        $this->moduleControllerUnRegistration();
        $this->restorfile();
        //Start Uninstall Tab
        require_once(dirname(__FILE__) . '/sql/uninstall_tab.php');
        foreach ($idtabs as $tabid):
            if ($tabid){
                $tab = new Tab($tabid);
                $tab->delete();
            }
        endforeach;
        //End Uninstall Tab
        // Start Uninstall Blog Tables 
                $sql = array();
        require_once(dirname(__FILE__) . '/sql/uninstall.php');
        foreach ($sql as $s) :
            if (!Db::getInstance()->Execute($s))
                return false;
        endforeach;
           // End Uninstall Blog Tables
          return true;
    }
    public function moduleControllerRegistration()
    {
        $tab = new Tab();
        $tab->class_name = 'Smartshortcode_ajax';
        $tab->id_parent  = -1;
        $tab->module     = $this->name;
        $tab->name       = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = 'Smartshortcode_ajax';
        $tab->active     = 1;
        $tab->add();
        if(!$tab->id)
            return FALSE;
        Configuration::updateValue('SMARTSHORTCODE_CONTROLLER_TABS', json_encode(array($tab->id)));
        return true;
    }
    protected function moduleControllerUnRegistration()
    {
        $ids = json_decode(Configuration::get('SMARTSHORTCODE_CONTROLLER_TABS'), true);
        foreach ($ids AS $id)
        {
            $tab = new Tab($id);
            $tab->delete();
        }
        return true;        
    }
    public function l($str,$specific = false){
        return parent::l($str);
    }
    private function CreateShortCodeTabs()
    {
        $langs = Language::getLanguages();
        $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $smarttab = new Tab();
        $smarttab->class_name = "Adminsmartshortcode";
        $smarttab->module = "";
        $smarttab->id_parent = 0;
        foreach($langs as $l){
            $smarttab->name[$l['id_lang']] = $this->l('Shortcode');
        }
        $smarttab->save();
        $tab_id = $smarttab->id;
        require_once(dirname(__FILE__) . '/sql/install_tab.php');
        foreach ($tabvalue as $tab){
            $newtab = new Tab();
            $newtab->class_name = $tab['class_name'];
            $newtab->id_parent = $tab_id;
            $newtab->module = $tab['module'];
            foreach($langs as $l) {
                $newtab->name[$l['id_lang']] = $this->l($tab['name']);
            }
            $newtab->save();
        }
        return true;
    }
    public function executehookvalue($hook_name)
    {
        $hookresults = smartanywherecontent::GetHookVale($hook_name);
        if(!empty($hookresults))
        {
            $context = Context::getContext();
            foreach ($hookresults as $hr)
            {
                $id_category = $hr['id_category'];  
                $id_product = $hr['id_product'];   
                if($context->controller->php_self == 'category' && $id_category != 'none')
                {         
                    if(Tools::getvalue('id_category') == $id_category || $id_category == '1'){
                            $rs = smartanywherecontent::GetHookValeByCat($hook_name,$id_category);
                            $this->smarty->assign( array(
                                'result' => $rs
                            ));
                        return $this->display(__FILE__, 'views/templates/front/smartshortcontent.tpl');
                        }
                }
                elseif($context->controller->php_self == 'product' && $id_product != '-1')
                {
                    if(Tools::getvalue('id_product') == $id_product || $id_product == '0'){
                        $rs = smartanywherecontent::GetHookValeByPrd($hook_name,$id_product);                            
                        $this->smarty->assign( array(
                            'result'        => $rs
                        ));
                        return $this->display(__FILE__, 'views/templates/front/smartshortcontent.tpl');
                    }
                }
                elseif(($context->controller->php_self != 'product' && $context->controller->php_self != 'category'))
                {                        
                    $rs = smartanywherecontent::GetHookValeByNone($hook_name);
                        $this->smarty->assign( array(
                            'result'        => $rs
                        ));
                    return $this->display(__FILE__, 'views/templates/front/smartshortcontent.tpl');
                }
            }
        }
    }
    public static function getLatestComments($id_lang = null, $limit = 5){
        $result = array();
        if($id_lang == null){
                $id_lang = (int)Context::getContext()->language->id;
            }
            $sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'smart_blog_comment bc INNER JOIN 
            '._DB_PREFIX_.'smart_blog_post_shop bps ON bc.id_post=bps.id_smart_blog_post and bps.id_shop = '.(int) Context::getContext()->shop->id.'
            WHERE  bc.active= 1 ORDER BY bc.id_smart_blog_comment DESC limit 0, '.$limit);
             $i = 0;
        foreach($sql as $post){
            $result[$i]['id_smart_blog_comment'] = $post['id_smart_blog_comment'];
            $result[$i]['id_parent'] = $post['id_parent'];
            $result[$i]['id_customer'] = $post['id_customer'];
            $result[$i]['id_post'] = $post['id_post'];
            $result[$i]['name'] = $post['name'];
            $result[$i]['email'] = $post['email'];
            $result[$i]['website'] = $post['website']; 
            $result[$i]['active'] = $post['active']; 
            $result[$i]['created'] = $post['created']; 
            $result[$i]['content'] = $post['content'];
            $SmartBlogPost = new  SmartBlogPost();
            $result[$i]['slug'] = $SmartBlogPost->GetPostSlugById($post['id_post']);
            $i++;
        }
		return $result;
    }
    public function hooksdsShortcodeFront($params){
        $context = isset($this->context) ? $this->context : Context::getContext() ; 
        $dir = dirname(__FILE__).'/addons';       
        if (is_dir($dir) && (!isset($context->controller->controller_type) || $context->controller->controller_type == 'front')) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if($file != '.' && $file != '..'){
                        if(is_dir("{$dir}/{$file}/front")){
                            include "{$dir}/{$file}/front/shortcode.php";                         
                        }
                    }
                }
                closedir($dh);
            }
        }
    }
    public function hooksdsShortcodeAdminPages($params){
        $dir = dirname(__FILE__).'/addons';
        $file = Tools::getValue('smartShortcodeAction');
        if(file_exists("{$dir}/{$file}/admin/{$file}.php")){
            include "{$dir}/{$file}/admin/{$file}.php";        
        }
    }
    public function hooksdsShortcodeAdminLists($params){
        $dir = dirname(__FILE__).'/addons';
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if($file != '.' && $file != '..'){
                        if(is_dir("{$dir}/{$file}/admin")){
                            if(file_exists("{$dir}/{$file}/admin/link.php")){
                                include "{$dir}/{$file}/admin/link.php";
                                echo "\r\n";
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }
    }
    public function hookdisplayProductTab($params)
    {
        $results = SmartProductTabClass::getTabTitle(Tools::getValue('id_product'), (int)$this->context->language->id);
            $this->context->smarty->assign(array(
                'sds_results' => $results
                ));
       return $this->display(__FILE__, 'views/templates/front/smart_product_tab_creator.tpl');
    }
    public function hookdisplayProductTabContent($params)
    {
        $results = SmartProductTabClass::getTabTitle(Tools::getValue('id_product'), (int)$this->context->language->id);
        $this->context->smarty->assign(array(
            'sds_results' => $results
            ));
        return $this->display(__FILE__, 'views/templates/front/smartproducttab.tpl');
    }
    /* Please uncomment this bellow function for using any custom hook */
    // public function __call($function, $args)
    // {
    //     $hook = substr($function,0,4);
    //     if($hook == 'hook'){
    //         $hook_name = substr($function,4);
    //         return $this->contenthookvalue($hook_name);
    //     }else{
    //         return false;
    //     }
    // }
    public function hookDisplayHome($params)
    {
      // return  $this->executehookvalue('DisplayHome');
      return  $this->contenthookvalue('DisplayHome');
    }
    public function hookdisplayFooter($params)
    {
        // return  $this->executehookvalue('displayFooter');
        return  $this->contenthookvalue('displayFooter');
    }
    public function hookdisplayFooterProduct($params)
    {
        // return  $this->executehookvalue('displayFooterProduct');
        return  $this->contenthookvalue('displayFooterProduct');
    }
    public function hookdisplayHomeTab($params)
    {
        // return  $this->executehookvalue('displayHomeTab');
        return  $this->contenthookvalue('displayHomeTab');
    }
    public function hookdisplayHomeTabContent($params)
    {
        // return  $this->executehookvalue('displayHomeTabContent');
        return  $this->contenthookvalue('displayHomeTabContent');
    }
    public function hookdisplayLeftColumn($params)
    {            
        // return  $this->executehookvalue('displayLeftColumn');
        return  $this->contenthookvalue('displayLeftColumn');
    }
    public function hookdisplayLeftColumnProduct($params)
    {
        // return  $this->executehookvalue('displayLeftColumnProduct');
        return  $this->contenthookvalue('displayLeftColumnProduct');
    }
    public function hookdisplayMaintenance($params)
    {
        // return  $this->executehookvalue('displayMaintenance');
        return  $this->contenthookvalue('displayMaintenance');
    }
    public function hookdisplayMyAccountBlock($params)
    {
        // return  $this->executehookvalue('displayMyAccountBlock');
        return  $this->contenthookvalue('displayMyAccountBlock');
    }
    public function hookdisplayMyAccountBlockfooter($params)
    {
        // return  $this->executehookvalue('displayMyAccountBlockfooter');
        return  $this->contenthookvalue('displayMyAccountBlockfooter');
    }
    public function hookdisplayNav($params)
    {
        // return  $this->executehookvalue('displayNav');
        return  $this->contenthookvalue('displayNav');
    }
    public function hookdisplayBanner($params)
    {
        // return  $this->executehookvalue('displayBanner');
        return  $this->contenthookvalue('displayBanner');
    }
    public function hookdisplayRightColumn($params)
    {
        // return  $this->executehookvalue('displayRightColumn');
        return  $this->contenthookvalue('displayRightColumn');
    }
    public function hookdisplayRightColumnProduct($params)
    {
        // return  $this->executehookvalue('displayRightColumnProduct');
        return  $this->contenthookvalue('displayRightColumnProduct');
    }
    public function hookdisplayTop($params)
    {
        // return  $this->executehookvalue('displayTop');
        return  $this->contenthookvalue('displayTop');
    }
    public function hookdisplayTopColumn($params)
    {
        // return  $this->executehookvalue('displayTopColumn');
        return  $this->contenthookvalue('displayTopColumn');
    }
    public function generatesubCategoriesOption($categories, $items_to_skip = null)
    {
        $subcatvals = array();
        $spacer_size = '5';
         $this->element_index++;
        foreach ($categories as $key => $category)
        {
            $this->smartcat[$this->element_index]['id_category'] = $category['id_category'];
            $this->smartcat[$this->element_index]['name'] = str_repeat('&nbsp;', $spacer_size * (int)$category['level_depth']).$category['name'];
        

            if (isset($category['children']))
                  $this->generatesubCategoriesOption($category['children']);
         

        $this->element_index++;
        }
        return true;
    }
    public function generateCategoriesOption($categories, $items_to_skip = null)
    {
        $subcatvals = array();
        $spacer_size = '3';
        $this->smartcat[0]['id_category'] = 'none';
        $this->smartcat[0]['name'] = 'None';            
        $this->element_index = 1;
        foreach ($categories as $key => $category)
        {
                $this->smartcat[$this->element_index]['id_category'] = $category['id_category'];
                $this->smartcat[$this->element_index]['name'] = str_repeat('&nbsp;', $spacer_size * (int)$category['level_depth']).$category['name'];
            if (isset($category['children']))
                  $this->generatesubCategoriesOption($category['children']);
            $this->element_index++;
        }
        
        return $this->smartcat;
    }
    public function getSimpleProducts()
    {
        $context = Context::getContext();
        $id_lang = (int)Context::getContext()->language->id;
        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront')))
            $front = false;
        $sql = 'SELECT p.`id_product`, pl.`name`
                FROM `'._DB_PREFIX_.'product` p
                '.Shop::addSqlAssociation('product', 'p').'
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
                WHERE pl.`id_lang` = '.(int)$id_lang.'
                '.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
                ORDER BY pl.`name`';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
    public function getproduct()
    {
        $rs = array();
        $rslt[0]['id_product'] = '-1';
        $rslt[0]['name'] = 'None';
        $rslt[1]['id_product'] = 0;
        $rslt[1]['name'] = 'All';
        $sql = 'SELECT p.id_product,pl.name FROM '._DB_PREFIX_.'product p INNER JOIN 
        '._DB_PREFIX_.'product_lang pl ON p.id_product=pl.id_product INNER JOIN 
        '._DB_PREFIX_.'product_shop ps ON pl.id_product = ps.id_product AND ps.id_shop = '.(int) Context::getContext()->shop->id.'
        WHERE pl.id_lang='.(int)Context::getContext()->language->id.'
        AND p.active= 1 ORDER BY p.id_product DESC';
    $rs =  $this->getSimpleProducts();
    $i = 2;
    foreach($rs as $r){
        $rslt[$i]['id_product'] = $r['id_product'];
        $rslt[$i]['name'] = $r['name'];
        $i++;
     }
     return $rslt;
    }
    public function hookdisplayHeader($params) {
		if(Configuration::get('smart_load_font')=='module'){
			$this->context->controller->addCSS($this->_path . 'css/font-awesome.min.css');
		}
		if(Configuration::get('smart_load_bootstrapcss')=='module'){
			$this->context->controller->addCSS($this->_path . 'css/bootstrap/css/bootstrap.min.css');
		}
		if(Configuration::get('smart_load_bootstrapjs')=='module'){
			$this->context->controller->addJS($this->_path . 'css/bootstrap/js/bootstrap.min.js');
		}
        $this->context->controller->addCSS($this->_path . 'css/smartshortcode.css');
        $this->context->controller->addCSS($this->_path . 'css/magnific-popup.css');
        $this->context->controller->addJS('http'.((Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ? 's' : '').'://maps.google.com/maps/api/js?sensor=true');
        $this->context->controller->addJS($this->_path . 'js/jquery.magnific-popup.min.js');
        $this->context->controller->addJS($this->_path . 'js/smartshortcode.js');
        $this->context->controller->addJS($this->_path . 'js/vallenato.js');
            $this->context->controller->addCSS(_THEME_CSS_DIR_ . 'product_list.css');
        $this->context->controller->addCSS(($this->_path) . 'homefeatured.css', 'all');
        $this->context->controller->addJqueryPlugin(array('bxslider'));

       // $this->context->controller->addJS($this->_path . 'js/jquery.flexslider.js');
      	//$this->context->controller->addCSS(($this->_path) . 'css/flexslider.css', 'all');   
    }
    public static function render_product_ui($cache_products)
    {
        $context = Context::getContext(); 
        $context->smarty->assign(
        array(
                'products' => $cache_products,
                'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
                'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
            )
        );
        $output = $context->smarty->fetch(_PS_MODULE_DIR_.'smartshortcode/smartshortcode.tpl');
        return $output;
    }
    public function  hookdisplayBackOfficeHeader($params)
    {
        $admin_url = $_SERVER['REQUEST_URI'];
        $admin_url = substr($admin_url,0,strpos($admin_url,'index.php'));
        $admin_url = Tools::getHttpHost(true).$admin_url;
        $admin_url .= $this->context->link->getAdminLink('Smartshortcode_ajax',false);
        $admin_url .= '&secure_key='.Tools::encrypt('smartshortcode');
		$this->smarty->assign( array(
            'smartmodules_dir' => __PS_BASE_URI__,
            'ajax_url' => $admin_url
        ));
        return $this->display(__FILE__, 'views/templates/front/addjs.tpl');
    }
    public function openZip($file_to_open)
    {
		$target = _PS_ROOT_DIR_."/js/tiny_mce/plugins";  
		$zip = new ZipArchive();  //This is line 14
		$x = $zip->open($file_to_open);  
		if($x === true) {
			$zip->extractTo($target);  
			$zip->close();
		} 
	}   
	public static function getProductsByCategoryID($category_id,$limit=4, $id_lang = null, $id_shop = null, $child_count = false, $order_by = 'id_product', $order_way = "DESC")
	{
            $context = Context::getContext(); 
            $id_lang = is_null($id_lang) ? $context->language->id : $id_lang ;
            $id_shop = is_null($id_shop) ? $context->shop->id : $id_shop ;
            $id_supplier = (int)Tools::getValue('id_supplier');
            $active = true;
            $front = true;
            $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, MAX(product_attribute_shop.id_product_attribute) id_product_attribute, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, pl.`description`, pl.`description_short`, pl.`available_now`,
                                    pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, MAX(image_shop.`id_image`) id_image,
                                    il.`legend`, m.`name` AS manufacturer_name, cl.`name` AS category_default,
                                    DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
                                    INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
						DAY)) > 0 AS new, product_shop.price AS orderprice
				FROM `'._DB_PREFIX_.'category_product` cp
				LEFT JOIN `'._DB_PREFIX_.'product` p
					ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i
					ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE product_shop.`id_shop` = '.(int)$context->shop->id.'
					AND cp.`id_category` = '.(int)$category_id
					.($active ? ' AND product_shop.`active` = 1' : '')
					.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
					.($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '')
					.' GROUP BY product_shop.id_product';
            if (empty($order_by) || $order_by == 'position') $order_by = 'price';
            if (empty($order_way)) $order_way = 'DESC';
            if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add'  || $order_by == 'date_upd')
                    $order_by_prefix = 'p';
            else if ($order_by == 'name')
                    $order_by_prefix = 'pl';

            $sql .= " ORDER BY {$order_by_prefix}.{$order_by} {$order_way}";
            $sql .= ' LIMIT '.$limit.' '; 
           $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if(!$result)
            return array();    
		return Product::getProductsProperties($id_lang, $result);
	}
	public function restorfile()
	{
        // START COPY TINYMCE
        if(file_exists(_PS_ROOT_DIR_."/js/admin/tinymce.inc.js")){
            @unlink(_PS_ROOT_DIR_."/js/admin/tinymce.inc.js");
            @copy(dirname(__FILE__)."/backup/tinymce.inc.js",_PS_ROOT_DIR_."/js/admin/tinymce.inc.js");
        }else{
            @unlink(_PS_ROOT_DIR_."/js/tinymce.inc.js");
            @copy(dirname(__FILE__)."/backup/tinymce.inc.js",_PS_ROOT_DIR_."/js/tinymce.inc.js");
        }
        // END COPY TINYMCE
	}
    public function storfile()
    {
        // START COPY TINYMCE
        if(file_exists(_PS_ROOT_DIR_."/js/admin/tinymce.inc.js")){
             @copy(_PS_ROOT_DIR_."/js/admin/tinymce.inc.js", dirname(__FILE__)."/backup/tinymce.inc.js");
             @copy(dirname(__FILE__) . "/js/lib/ex/tinymce.inc.js",_PS_ROOT_DIR_."/js/admin/tinymce.inc.js");
        }else{
             @copy(_PS_ROOT_DIR_."/js/tinymce.inc.js", dirname(__FILE__)."/backup/tinymce.inc.js");
             @copy(dirname(__FILE__) . "/js/lib/ex/tinymce.inc.js",_PS_ROOT_DIR_."/js/tinymce.inc.js");
        }
        // END COPY TINYMCE
    }
    public static function getProductfortab($active = false,$id_lang=1)
    {
        $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $cache_id = 'smartproducttabcreator::getProduct_'.(bool)$active;
        if (!Cache::isStored($cache_id))
        {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT * FROM `'._DB_PREFIX_.'product` p INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON(p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)($id_lang).')');
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }
	/*End Module Admin Setting*/
    public static function  add_shortcode($tag,$func)
    {
            self::$static_shortcode_tags[$tag] = $func;
    }
    public static function do_shortcode($content,$hook_name='')
    {
            $shortcode_tags = self::$static_shortcode_tags;
            if (empty($shortcode_tags) || !is_array($shortcode_tags))
                    return $content;
            $pattern = self::get_shortcode_regex();
            self::$sds_current_hook = $hook_name;
            return preg_replace_callback( "/$pattern/s", array(new SmartShortCode,'do_shortcode_tag'), $content );
    }
    public static function do_shortcode_tag( $m ) {
            $shortcode_tags = self::$static_shortcode_tags;

            // allow [[foo]] syntax for escaping a tag
            if ( $m[1] == '[' && $m[6] == ']' ) {
                    return substr($m[0], 1, -1);
            }
          
            $tag = $m[2];
            $attr = self::shortcode_parse_atts( $m[3] );
            
            if ( isset( $m[5] ) ) {
                    // enclosing tag - extra parameter
                    return $m[1] . call_user_func( $shortcode_tags[$tag], $attr, $m[5], $tag, self::$sds_current_hook ) . $m[6];
            } else {
                    // self-closing tag
                    return $m[1] . call_user_func( $shortcode_tags[$tag], $attr, null,  $tag, self::$sds_current_hook ) . $m[6];
            }
    }
    public static function get_shortcode_regex() {
            
            $tagnames = array_keys(self::$static_shortcode_tags);
            $tagregexp = join( '|', array_map('preg_quote', $tagnames) );

            return
                      '\\['                              // Opening bracket
                    . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
                    . "($tagregexp)"                     // 2: Shortcode name
                    . '(?![\\w-])'                       // Not followed by word character or hyphen
                    . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
                    .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
                    .     '(?:'
                    .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
                    .         '[^\\]\\/]*'               // Not a closing bracket or forward slash
                    .     ')*?'
                    . ')'
                    . '(?:'
                    .     '(\\/)'                        // 4: Self closing tag ...
                    .     '\\]'                          // ... and closing bracket
                    . '|'
                    .     '\\]'                          // Closing bracket
                    .     '(?:'
                    .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
                    .             '[^\\[]*+'             // Not an opening bracket
                    .             '(?:'
                    .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
                    .                 '[^\\[]*+'         // Not an opening bracket
                    .             ')*+'
                    .         ')'
                    .         '\\[\\/\\2\\]'             // Closing shortcode tag
                    .     ')?'
                    . ')'
                    . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
    }
    public static function shortcode_parse_atts($text)
    {
            $atts = array();
            $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
            $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
            if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
                    foreach ($match as $m) {
                            if (!empty($m[1]))
                                    $atts[strtolower($m[1])] = stripcslashes($m[2]);
                            elseif (!empty($m[3]))
                                    $atts[strtolower($m[3])] = stripcslashes($m[4]);
                            elseif (!empty($m[5]))
                                    $atts[strtolower($m[5])] = stripcslashes($m[6]);
                            elseif (isset($m[7]) and strlen($m[7]))
                                    $atts[] = stripcslashes($m[7]);
                            elseif (isset($m[8]))
                                    $atts[] = stripcslashes($m[8]);
                    }
            } else {
                    $atts = ltrim($text);
            }
            return $atts;
    }    
    public static function shortcode_atts( $pairs, $atts, $shortcode = '' ) {
        $atts = (array)$atts;
        $out = array();
        foreach($pairs as $name => $default) {
                if ( array_key_exists($name, $atts) )
                        $out[$name] = $atts[$name];
                else
                        $out[$name] = $default;
        }            
        if ( $shortcode )
                $out = apply_filters( "shortcode_atts_{$shortcode}", $out, $pairs, $atts );

        return $out;
    }
        
    public static function strip_shortcodes( $content ) {
            $shortcode_tags = self::$static_shortcode_tags;

            if (empty($shortcode_tags) || !is_array($shortcode_tags))
                    return $content;

            $pattern = $this->get_shortcode_regex();

            return preg_replace_callback( "/$pattern/s", array($this,'strip_shortcode_tag'), $content );
    }

    public static function strip_shortcode_tag( $m ) {
            if ( $m[1] == '[' && $m[6] == ']' ) {
                    return substr($m[0], 1, -1);
            }

            return $m[1] . $m[6];
    }
    
    public function parse ($str,$hook_name = '')
    {                   
        return self::do_shortcode($str,$hook_name);
    } 
	public static function getProductbycat($id_category,$order_by=null,$orderway=null){
                    $id_lang = (int)Context::getContext()->language->id;
                    $id_shop = (int)Context::getContext()->shop->id;
					if($order_by == null){
						$order_by = 'price';
					}
					if($orderway == null){
						$orderway = 'ASC';
					}
      $sql = 'SELECT * FROM '._DB_PREFIX_.'product p INNER JOIN 
                '._DB_PREFIX_.'product_lang pl ON p.id_product=pl.id_product INNER JOIN 
                '._DB_PREFIX_.'product_shop ps ON p.id_product = ps.id_product AND ps.id_category_default= '.$id_category.' INNER JOIN
				'._DB_PREFIX_.'image img ON img.id_product=p.id_product AND img.cover = 1 INNER JOIN
				'._DB_PREFIX_.'category_product cp ON cp.id_product=p.id_product
                WHERE pl.id_lang='.$id_lang.'
                AND p.active= 1 AND ps.id_shop = '.$id_shop.' AND cp.id_category = '.$id_category.' AND p.id_category_default = '.$id_category.'  AND pl.id_shop = '.$id_shop.' ORDER BY p.'.$order_by.' '.$orderway;
        
			$result = Db::getInstance()->executeS($sql);
        return $result;
    }
	public function getBestSellers()
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return false;

		if (!($result = ProductSale::getBestSalesLight((int)$this->context->language->id, 0, 8)))
			return (Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY') ? array() : false);

		$currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
		$usetax = (Product::getTaxCalculationMethod((int)$this->context->customer->id) != PS_TAX_EXC);
		foreach ($result as &$row)
			$row['price'] = Tools::displayPrice(Product::getPriceStatic((int)$row['id_product'], $usetax), $currency);

		return $result;
	}
	public function getNewProducts()
	{
		if (!Configuration::get('NEW_PRODUCTS_NBR'))
			return;
		$newProducts = false;
		if (Configuration::get('PS_NB_DAYS_NEW_PRODUCT'))
			$newProducts = Product::getNewProducts((int) $this->context->language->id, 0, (int)Configuration::get('NEW_PRODUCTS_NBR'));
                if (!$newProducts && Configuration::get('PS_BLOCK_NEWPRODUCTS_DISPLAY'))
			return;     
		return $newProducts;
	}		
    public static function content_filter($content = '')
    {
        $sc = self::getInstance();
        $content = $sc->parse($content);
        return $content;
    }
    public static function getInstance()
    {
        if(SmartShortCode::$smartshortcode instanceof SmartShortCode)
            return SmartShortCode::$smartshortcode;
        else{
            SmartShortCode::$smartshortcode = new SmartShortCode;
            return SmartShortCode::$smartshortcode;                
        }
    }
	public function contenthookvalue($hook = '')
    {
        // executehookvalue
        $hook_name = $hook;
        $context = Context::getContext();
        $page = $context->controller->php_self;
        $vcaw = smartanywherecontent::GetInstance();
        $results = $vcaw->GetVcContentAnyWhereByHook($hook);
        if(isset($results) && !empty($results)){
            foreach($results as $result){
                $display_type = isset($result['display_type']) ? $result['display_type'] : '';
                $prd_page = isset($result['prd_page']) ? $result['prd_page'] : '';
                $prd_specify = isset($result['prd_specify']) ? $result['prd_specify'] :'';
                $cat_page = isset($result['cat_page']) ? $result['cat_page'] : '';
                $cat_specify = isset($result['cat_specify']) ? $result['cat_specify'] : '';
                $cms_page = isset($result['cms_page']) ? $result['cms_page'] : '';
                $cms_specify = isset($result['cms_specify']) ? $result['cms_specify'] : '';
                $srt_id_category = isset($result['id_category']) ? $result['id_category'] : 'no_value';
                $srt_id_product = isset($result['id_product']) ? $result['id_product'] : 'no_value';
                if($srt_id_category != 'no_value' || $srt_id_product != 'no_value'){
                    //start old value
                    if($page == 'category' && $srt_id_category != 'none')
                    {
                        $HOME_FEATURED_CAT = (int)Configuration::get('HOME_FEATURED_CAT');
                        if(Tools::getvalue('id_category') == $srt_id_category || $srt_id_category == '1' || $HOME_FEATURED_CAT == $srt_id_category){
                                $rs = smartanywherecontent::GetHookValeByCat($hook_name,$srt_id_category);
                                $this->smarty->assign( array(
                                    'results' => $rs
                                ));
                            return $this->display(__FILE__, 'views/templates/front/smartshortcontent.tpl');
                            }
                    }
                    elseif($page == 'product' && $srt_id_product != '-1')
                    {
                        if(Tools::getvalue('id_product') == $srt_id_product || $srt_id_product == '0'){
                            $rs = smartanywherecontent::GetHookValeByPrd($hook_name,$srt_id_product);                            
                            $this->smarty->assign( array(
                                'results'        => $rs
                            ));
                            return $this->display(__FILE__, 'views/templates/front/smartshortcontent.tpl');
                        }
                    }
                    elseif(($page != 'product' && $page != 'category'))
                    {                        
                        $rs = smartanywherecontent::GetHookValeByNone($hook_name);
                            $this->smarty->assign( array(
                                'results'        => $rs
                            ));
                        return $this->display(__FILE__, 'views/templates/front/smartshortcontent.tpl');
                    }
                    //end old value
                }else{
                    //start new version
                    if($result['display_type'] == 1){
                        $values = $vcaw->GetVcContentByAll($hook);
                        $this->smarty->assign(array(
                                        'results' => $values
                                    ));
                        return $this->display(__FILE__, 'views/templates/front/smartshortcontent.tpl');
                    }else{
                        if($page == 'product'){
                            if($result['prd_page'] == 1){
                                $values = $vcaw->GetVcContentByAllPRD($hook);
                                $this->smarty->assign(array(
                                                'results' => $values
                                            ));
                                return $this->display(__FILE__, 'views/templates/front/smartshortcontent.tpl');
                            }else{
                                $id_product = Tools::getValue('id_product');
                                $id_prd_cats = $vcaw->getProductCategories($id_product);
                                $prd_specify_arr = explode(',',$result['prd_specify']);

                                $prd_specify_prd_arr = array();
                                $prd_specify_cat_arr = array();
                                if(isset($prd_specify_arr) && !empty($prd_specify_arr)){
                                    foreach($prd_specify_arr as $prd_specify_ar){
                                        if(strpos($prd_specify_ar, 'CAT_') !== false){
                                            $id_cat = str_replace('CAT_','',$prd_specify_ar);
                                            if(in_array($id_cat, $id_prd_cats)){
                                                //Start execute and asign
                                                    $values = $vcaw->GetVcContentByAllPRDCATID($hook,$id_cat);
                                                    $this->smarty->assign(array(
                                                                    'results' => $values
                                                                ));
                                                    return $this->display(__FILE__, 'views/templates/front/smartshortcontent.tpl');
                                                //End execute and asign
                                            }
                                        }elseif(strpos($prd_specify_ar, 'PRD_') !== false){
                                            $id_prd = str_replace('PRD_','',$prd_specify_ar);
                                            if($id_prd == $id_product){
                                                //Start execute and asign
                                                    $values = $vcaw->GetVcContentByAllPRDID($hook,$id_product);
                                                $this->smarty->assign(array(
                                                                'results' => $values
                                                            ));
                                                return $this->display(__FILE__, 'views/templates/front/smartshortcontent.tpl');
                                                //End execute and asign    
                                            }
                                        }
                                    }
                                }
                                //exception product page
                            }
                        }
                        if($page == 'category'){
                            if($result['cat_page'] == 1){
                                    $values = $vcaw->GetVcContentByAllCAT($hook);
                                $this->smarty->assign(array(
                                                'results' => $values
                                            ));
                                return $this->display(__FILE__, 'views/templates/front/smartshortcontent.tpl');
                            }else{
                                $id_category = Tools::getValue('id_category');
                                $cat_specify_arr = explode(',',$result['cat_specify']);
                                if(in_array($id_category, $cat_specify_arr)){
                                        $values = $vcaw->GetVcContentByAllCATID($hook,$id_category);
                                    $this->smarty->assign(array(
                                                    'results' => $values
                                                ));
                                    return $this->display(__FILE__, 'views/templates/front/smartshortcontent.tpl');
                                }
                                //exception Category page
                            }
                        }
                        if($page == 'cms'){
                            if($result['cms_page'] == 1){
                                $values = $vcaw->GetVcContentByAllCMS($hook);
                                $this->smarty->assign(array(
                                                'results' => $values
                                            ));
                                return $this->display(__FILE__, 'views/templates/front/smartshortcontent.tpl');
                            }else{
                                $id_cms = Tools::getValue('id_cms');
                                $cms_specify_arr = explode(',',$result['cms_specify']);
                                if(in_array($id_cms, $cms_specify_arr)){
                                        $values = $vcaw->GetVcContentByAllCMSID($hook,$id_cms);
                                    $this->smarty->assign(array(
                                                    'results' => $values
                                                ));
                                    return $this->display(__FILE__, 'views/templates/front/smartshortcontent.tpl');
                                }
                                //exception CMS page
                            }
                        }
                    }
                    //new new version
                } 
            }
        }
    }
}

    function bold_cb($atts,$content){    
        extract(SmartShortCode::shortcode_atts(
                array(
                    'size'=>'12'
                ),$atts
        ));
        $html = '<strong>';
        if(isset($size) && !empty($size)){        
            $html .= "<span style='font-size:{$size}px'>{$content}</span>";        
        }
        $html .= "</strong>";
        return $html;
    }
    SmartShortCode::add_shortcode('bold','bold_cb');

    


    

    function smart_row($atts, $content = null, $tag, $hook_name) {
        return '<div class="row">' . SmartShortCode::do_shortcode($content,$hook_name) . '</div>';
    }
    SmartShortCode::add_shortcode('smart_row', 'smart_row');

    function smart_col($atts, $content = null, $tag, $hook_name) {
        extract(SmartShortCode::shortcode_atts(array(
            "class" => ''
                        ), $atts));
        return '<div class="'.$class.' ">' .SmartShortCode::do_shortcode($content,$hook_name). '</div>';
    }
    SmartShortCode::add_shortcode('smart_col', 'smart_col');

    function shortcode_break($atts, $content = null) {

        return '<div class="break clearfix">&nbsp;</div>';
    }

    SmartShortCode::add_shortcode('break', 'shortcode_break');

//divider

    function shortcode_divider($atts, $content = null) {

        return '<div class="divider clearfix">&nbsp;</div>';
    }

    SmartShortCode::add_shortcode('divider', 'shortcode_divider');

//divider top

    function shortcode_dividertop($atts, $content = null) {

        return '<div class="totop"><div class="gototop"></div></div>';
    }

    SmartShortCode::add_shortcode('dividertop', 'shortcode_dividertop');

//ribbon red

    function shortcode_ribbon_red($atts, $content = null, $tag, $hook_name) {

        extract(SmartShortCode::shortcode_atts(array(
            "url" => ''
                        ), $atts));

        return '<div class="ribbon"><div class="ribbon_left_red"></div><div class="ribbon_center_red"><a href ="' . $url . '">' . SmartShortCode::do_shortcode($content,$hook_name) . '</a></div><div class="ribbon_right_red"></div></div>';
    }

    SmartShortCode::add_shortcode('ribbon_red', 'shortcode_ribbon_red');

//ribbon blue

    function shortcode_ribbon_blue($atts, $content = null, $tag, $hook_name) {

        extract(SmartShortCode::shortcode_atts(array(
            "url" => ''
                        ), $atts));

        return '<div class="ribbon"><div class="ribbon_left_blue"></div><div class="ribbon_center_blue"><a href ="' . $url . '">' . SmartShortCode::do_shortcode($content,$hook_name) . '</a></div><div class="ribbon_right_blue"></div></div>';
    }

    SmartShortCode::add_shortcode('ribbon_blue', 'shortcode_ribbon_blue');

//ribbon yellow

    function shortcode_ribbon_yellow($atts, $content = null, $tag, $hook_name) {

        extract(SmartShortCode::shortcode_atts(array(
            "url" => ''
                        ), $atts));

        return '<div class="ribbon"><div class="ribbon_left_yellow"></div><div class="ribbon_center_yellow"><a href ="' . $url . '">' . SmartShortCode::do_shortcode($content,$hook_name) . '</a></div><div class="ribbon_right_yellow"></div></div>';
    }

    SmartShortCode::add_shortcode('ribbon_yellow', 'shortcode_ribbon_yellow');

//ribbon green

    function shortcode_ribbon_green($atts, $content = null, $tag, $hook_name) {

        extract(SmartShortCode::shortcode_atts(array(
            "url" => ''
                        ), $atts));

        return '<div class="ribbon"><div class="ribbon_left_green"></div><div class="ribbon_center_green"><a href ="' . $url . '">' . SmartShortCode::do_shortcode($content,$hook_name) . '</a></div><div class="ribbon_right_green"></div></div>';
    }

    SmartShortCode::add_shortcode('ribbon_green', 'shortcode_ribbon_green');

//ribbon white

    function shortcode_ribbon_white($atts, $content = null, $tag, $hook_name) {

        extract(SmartShortCode::shortcode_atts(array(
            "url" => ''
                        ), $atts));

        return '<div class="ribbon"><div class="ribbon_left_white"></div><div class="ribbon_center_white"><a href ="' . $url . '">' . SmartShortCode::do_shortcode($content,$hook_name) . '</a></div><div class="ribbon_right_white"></div></div>';
    }

    SmartShortCode::add_shortcode('ribbon_white', 'shortcode_ribbon_white');

//high light dark

    function shortcode_highlight_black($atts, $content = null, $tag, $hook_name) {

        return '<span class="black" >' . SmartShortCode::do_shortcode($content,$hook_name). '</span>';
    }

    SmartShortCode::add_shortcode('highlight_black', 'shortcode_highlight_black');

//high light yellow

    function shortcode_highlight_yellow($atts, $content = null, $tag, $hook_name) {

        return '<span class="yellow" >' . SmartShortCode::do_shortcode($content,$hook_name) . '</span>';
    }

    SmartShortCode::add_shortcode('highlight_yellow', 'shortcode_highlight_yellow');

//high light blue

    function shortcode_highlight_blue($atts, $content = null, $tag, $hook_name) {

        return '<span class="blue" >' . SmartShortCode::do_shortcode($content,$hook_name) . '</span>';
    }

    SmartShortCode::add_shortcode('highlight_blue', 'shortcode_highlight_blue');

//high light green

    function shortcode_highlight_green($atts, $content = null, $tag, $hook_name) {

        return '<span class="green" >' . SmartShortCode::do_shortcode($content,$hook_name). '</span>';
    }

    SmartShortCode::add_shortcode('highlight_green', 'shortcode_highlight_green');

//smart list shortcodes

    function remove_li_shortcode_directives($content) {
        $content = preg_replace('/\]/', '>', preg_replace('/\[/', '<', $content));

        return $content;
    }

    function smart_list_circle($atts, $content = null, $tag, $hook_name) {
        return '<ul>' . remove_li_shortcode_directives($content,$hook_name) . '</ul>';
    }

    SmartShortCode::add_shortcode('smart_list_circle', 'smart_list_circle');

    function smart_list_arrow($atts, $content = null, $tag, $hook_name) {

        return '<ul class="fa-ul i-caret-right" >' . remove_li_shortcode_directives($content,$hook_name) . '</ul>';
    }
    SmartShortCode::add_shortcode('smart_list_arrow', 'smart_list_arrow');

    function smart_list_sin_arrow($atts, $content = null, $tag, $hook_name) {

        return '<ul class="fa-ul i-angle-right" >' . remove_li_shortcode_directives($content,$hook_name) . '</ul>';
    }

    SmartShortCode::add_shortcode('smart_list_sin_arrow', 'smart_list_sin_arrow');

    function smart_list_icon($atts, $content = null, $tag, $hook_name) {

        return '<ul class="fa-ul i-bullseye" >' . remove_li_shortcode_directives($content,$hook_name) . '</ul>';
    }

    SmartShortCode::add_shortcode('smart_list_icon', 'smart_list_icon');

    function shortcode_list_checkbox2($atts, $content = null, $tag, $hook_name) {

        return '<ul class="list_style style5" >' . remove_li_shortcode_directives($content,$hook_name) . '</ul>';
    }

    SmartShortCode::add_shortcode('list_checkbox2', 'shortcode_list_checkbox2');

    function shortcode_list_cross2($atts, $content = null, $tag, $hook_name) {

        return '<ul class="list_style style6" >' . remove_li_shortcode_directives($content,$hook_name) . '</ul>';
    }

    SmartShortCode::add_shortcode('list_cross2', 'shortcode_list_cross2');

    function shortcode_list_rarrow2($atts, $content = null, $tag, $hook_name) {

        return '<ul class="list_style style7" >' . remove_li_shortcode_directives($content,$hook_name) . '</ul>';
    }

    SmartShortCode::add_shortcode('list_rarrow2', 'shortcode_list_rarrow2');

    function shortcode_list_circle2($atts, $content = null, $tag, $hook_name) {

        return '<ul class="list_style style8" >' . remove_li_shortcode_directives($content,$hook_name) . '</ul>';
    }

    SmartShortCode::add_shortcode('list_circle2', 'shortcode_list_circle2');

    function shortcode_list_green_checkbox($atts, $content = null, $tag, $hook_name) {

        return '<ul class="list_style style9" >' . remove_li_shortcode_directives($content,$hook_name) . '</ul>';
    }

    SmartShortCode::add_shortcode('list_checkbox3', 'shortcode_list_green_checkbox');

    function shortcode_list_cross3($atts, $content = null, $tag, $hook_name) {

        return '<ul class="list_style style10" >' . remove_li_shortcode_directives($content,$hook_name) . '</ul>';
    }

    SmartShortCode::add_shortcode('list_cross3', 'shortcode_list_cross3');

    function shortcode_list_rarrow3($atts, $content = null, $tag, $hook_name) {

        return '<ul class="list_style style11" >' . remove_li_shortcode_directives($content,$hook_name) . '</ul>';
    }

    SmartShortCode::add_shortcode('list_rarrow3', 'shortcode_list_rarrow3');

    function shortcode_list_circle3($atts, $content = null, $tag, $hook_name) {

        return '<ul class="list_style style12" >' . remove_li_shortcode_directives($content,$hook_name) . '</ul>';
    }

    SmartShortCode::add_shortcode('list_circle3', 'shortcode_list_circle3');

    //dropcaps

    function shortcode_dropcap_with_bg($atts, $content = null, $tag, $hook_name) {

        return '<span class="dropcap large bg">' . SmartShortCode::do_shortcode($content,$hook_name) . '</span>';
    }

    SmartShortCode::add_shortcode('dropcap_with_bg', 'shortcode_dropcap_with_bg');

    function shortcode_dropcap($atts, $content = null, $tag, $hook_name) {

        return '<span class="dropcap">' . SmartShortCode::do_shortcode($content,$hook_name) . '</span>';
    }

    SmartShortCode::add_shortcode('dropcap', 'shortcode_dropcap');

    //smart blockquotes

    
    SmartShortCode::add_shortcode('block-quote', 'shortcode_blockquote');


    

    SmartShortCode::add_shortcode('block-quote-right', 'shortcode_blockquote_right');

    //smart testimonial quote

    function shortcode_testmonial_quote($atts, $content = null, $tag, $hook_name) {

        $defaults = array(
            'name' => 'Name'
        );

        extract(SmartShortCode::shortcode_atts($defaults, $atts));

        return "<div class='testimonial-quote'><p>".SmartShortCode::do_shortcode($content,$hook_name)."</p><h6 class='name'>$name</h6></div>";
    }

    SmartShortCode::add_shortcode('testimonial_quote', 'shortcode_testmonial_quote');

    //smart buttons wrap


    function shortcode_button_small($atts, $content = null, $tag, $hook_name) {

        return "<div class='buttons'>" . SmartShortCode::do_shortcode($content,$hook_name) . "</div>";
    }

    SmartShortCode::add_shortcode('buttons_small', 'shortcode_button_small');

    function shortcode_button_medium($atts, $content = null, $tag, $hook_name) {

        return "<div class='buttons medium'>" . SmartShortCode::do_shortcode($content,$hook_name) . "</div>";
    }

    SmartShortCode::add_shortcode('buttons_medium', 'shortcode_button_medium');

    function shortcode_button_large($atts, $content = null, $tag, $hook_name) {

        return "<div class='buttons large'>" . SmartShortCode::do_shortcode($content,$hook_name) . "</div>";
    }

    SmartShortCode::add_shortcode('buttons_large', 'shortcode_button_large');

    //smart buttons


    function shortcode_button($atts, $content = null, $tag, $hook_name) {

        $defaults = array(
            'href' => '#',
            'class' => '#',
    		'target'=>'_blank',
    		'icon'=>''
        );
    	extract(SmartShortCode::shortcode_atts($defaults, $atts));
    if(empty($icon) && $icon == ''){
     return "<a class='sds-btn $class ' href='$href' target='_blank'>".SmartShortCode::do_shortcode($content,$hook_name)."</a>";
    }else{
     return "<a class='sds-btn $class ' href='$href' target='_blank'><i class='$icon'></i> ".SmartShortCode::do_shortcode($content,$hook_name)."</a>";
    }
    }

    SmartShortCode::add_shortcode('button', 'shortcode_button');

    function shortcode_button_gray($atts, $content = null, $tag, $hook_name) {

        $defaults = array(
            'href' => '#'
        );

        extract(SmartShortCode::shortcode_atts($defaults, $atts));

        return "<a class='shortcode-button gray' href='$href'>".SmartShortCode::do_shortcode($content,$hook_name)."</a>";
    }

    SmartShortCode::add_shortcode('button_grey', 'shortcode_button_gray');

    function shortcode_button_red($atts, $content = null, $tag, $hook_name) {

        $defaults = array(
            'href' => '#'
        );

        extract(SmartShortCode::shortcode_atts($defaults, $atts));

        return "<a class='shortcode-button red' href='$href'>".SmartShortCode::do_shortcode($content,$hook_name)."</a>";
    }

    SmartShortCode::add_shortcode('button_red', 'shortcode_button_red');

    function shortcode_button_yellow($atts, $content = null, $tag, $hook_name) {

        $defaults = array(
            'href' => '#'
        );

        extract(SmartShortCode::shortcode_atts($defaults, $atts));

        return "<a class='shortcode-button yellow' href='$href'>".SmartShortCode::do_shortcode($content,$hook_name)."</a>";
    }

    SmartShortCode::add_shortcode('button_yellow', 'shortcode_button_yellow');

    function shortcode_button_olive($atts, $content = null, $tag, $hook_name) {

        $defaults = array(
            'href' => '#'
        );

        extract(SmartShortCode::shortcode_atts($defaults, $atts));

        return "<a class='shortcode-button olive' href='$href'>".SmartShortCode::do_shortcode($content,$hook_name)."</a>";
    }

    SmartShortCode::add_shortcode('button_olive', 'shortcode_button_olive');

    function shortcode_button_lightblue($atts, $content = null, $tag, $hook_name) {

        $defaults = array(
            'href' => '#'
        );

        extract(SmartShortCode::shortcode_atts($defaults, $atts));

        return "<a class='shortcode-button lightblue' href='$href'>".SmartShortCode::do_shortcode($content,$hook_name)."</a>";
    }

    SmartShortCode::add_shortcode('button_lightblue', 'shortcode_button_lightblue');

    function shortcode_button_black($atts, $content = null, $tag, $hook_name) {

        $defaults = array(
            'href' => '#'
        );

        extract(SmartShortCode::shortcode_atts($defaults, $atts));

        return "<a class='shortcode-button black' href='$href'>".SmartShortCode::do_shortcode($content,$hook_name)."</a>";
    }

    SmartShortCode::add_shortcode('button_black', 'shortcode_button_black');

    //button buy

    function shortcode_button_purche($atts, $content = null, $tag, $hook_name) {

        extract(SmartShortCode::shortcode_atts(array(
            "url" => '',
            "bottom_text" => ''
                        ), $atts));

        return '<div class="button_purche"><a href="' . $url . '"><div class="button_purche_left"></div><div class="button_purche_right"><div class="button_purche_right_top">' . SmartShortCode::do_shortcode($content,$hook_name) . '</div><div class="button_purche_right_bottom">' . $bottom_text . '</div></div></a></div>';
    }

    SmartShortCode::add_shortcode('button_purche', 'shortcode_button_purche');

    //button download

    function shortcode_button_download($atts, $content = null, $tag, $hook_name) {

        extract(SmartShortCode::shortcode_atts(array(
            "url" => '',
            "bottom_text" => ''
                        ), $atts));

        return '<div class="button_download"><a href="' . $url . '"><div class="button_download_left"></div><div class="button_download_right"><div class="button_download_right_top">' . SmartShortCode::do_shortcode($content,$hook_name) . '</div><div class="button_download_right_bottom">' . $bottom_text . '</div></div></a></div>';
    }

    SmartShortCode::add_shortcode('button_download', 'shortcode_button_download');

    //button search

    function shortcode_button_search_c($atts, $content = null, $tag, $hook_name) {

        extract(SmartShortCode::shortcode_atts(array(
            "url" => '',
            "bottom_text" => ''
                        ), $atts));

        return '<div class="button_search"><a href="' . $url . '"><div class="button_search_left"></div><div class="button_search_right"><div class="button_search_right_top">' . SmartShortCode::do_shortcode($content,$hook_name) . '</div><div class="button_search_right_bottom">' . $bottom_text . '</div></div></a></div>';
    }

    SmartShortCode::add_shortcode('button_search_c', 'shortcode_button_search_c');

    // message boxes

    function shortcode_msgbox($atts, $content = null, $tag, $hook_name) {

        return '<div class="msgBox">' . SmartShortCode::do_shortcode($content,$hook_name) . '</div>';
    }

    SmartShortCode::add_shortcode('msgbox', 'shortcode_msgbox');

    function shortcode_msgbox2($atts, $content = null, $tag, $hook_name) {

        return '<div class="msgBox bg1">' . SmartShortCode::do_shortcode($content,$hook_name) . '</div>';
    }

    SmartShortCode::add_shortcode('msgbox2', 'shortcode_msgbox2');

    function shortcode_msgbox3($atts, $content = null, $tag, $hook_name) {

        return '<div class="msgBox bg2">' . SmartShortCode::do_shortcode($content,$hook_name) . '</div>';
    }

    SmartShortCode::add_shortcode('msgbox3', 'shortcode_msgbox3');

    function shortcode_msgbox4($atts, $content = null, $tag, $hook_name) {

        return '<div class="msgBox bg4">' . SmartShortCode::do_shortcode($content,$hook_name) . '</div>';
    }

    SmartShortCode::add_shortcode('msgbox4', 'shortcode_msgbox4');

 

    function flexslider_shortcode($atts, $content = null, $tag, $hook_name) {

        $defaults = array(
            'class' => 'flexslider slider-pull-left',
            'id' => 'flexslider'
        );

        extract(SmartShortCode::shortcode_atts($defaults, $atts));

        return "<section id='$id' class=\"$class\">
            <ul class=\"slides\">" . SmartShortCode::do_shortcode(str_replace('<br />', '', $content),$hook_name) . "</ul>
        </section>
        <script type='text/javascript'>
            jQuery(function($){         
                $('#$id').flexslider(); 
            });
        </script>";
    }

    SmartShortCode::add_shortcode('flexslider', 'flexslider_shortcode');

    function flextab_shortcode($atts, $content = null, $tag, $hook_name) {

        $defaults = array(
            'link' => '',
            'imageurl' => get_template_directory_uri() . '/image/responsive.png',
            'title' => 'title'
        );

        extract(SmartShortCode::shortcode_atts($defaults, $atts));

        ob_start();
        ?>
        <li>
        <?php if ($link) { ?>
                <a href="<?php echo $link; ?>"><img src="<?php echo $imageurl; ?>" alt="<?php echo $title; ?>" /></a>
        <?php } else { ?>
                <img src="<?php echo $imageurl; ?>" alt="<?php echo $title; ?>" />
        <?php } ?>
        </li> 

        <?php
        $output = ob_get_contents();

        ob_end_clean();

        return $output;
    }

    SmartShortCode::add_shortcode('ftab', 'flextab_shortcode');

    function cameraslider_shortcode($atts, $content = null, $tag, $hook_name) {

        $defaults = array(
            'class' => 'camera_wrap camera_azure_skin',
            'id' => 'camera_wrap'
        );

        extract(SmartShortCode::shortcode_atts($defaults, $atts));

        return "<section class='fluid_container slider-pull-left'><div id='$id' class=\"$class\">" . SmartShortCode::do_shortcode(str_replace('<br />', '', $content),$hook_name) . "</div></section>
        <script type='text/javascript'>
            jQuery(function($){         
                $('#$id').camera({
                    thumbnails: true,
                    loader: true,
                    hover: false
                }); 
            });
        </script>";
    }

    SmartShortCode::add_shortcode('cameraslider', 'cameraslider_shortcode');

    function camtab_shortcode($atts, $content) {

        $defaults = array(
            'link' => '',
            'imageurl' => get_template_directory_uri() . '/image/responsive.png',
        );

        extract(SmartShortCode::shortcode_atts($defaults, $atts));

        ob_start();
        ?>

        <div data-link="<?php echo $link; ?>" data-thumb="<?php echo $imageurl; ?>" data-src="<?php echo $imageurl; ?>"></div>

        <?php
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    SmartShortCode::add_shortcode('ctab', 'camtab_shortcode');

    //Today date is 31-03-2014
    function blockquotes_cb($atts, $content = null, $tag, $hook_name) {
        extract(SmartShortCode::shortcode_atts(array(
            'class' => '',
            'author' => ''
        ), $atts));
        return '<blockquote class="'.$class.'"><p>'.SmartShortCode::do_shortcode($content,$hook_name).'</p><br><footer>'.$author.'</footer></blockquote>';
    }
    SmartShortCode::add_shortcode('blockquote', 'blockquotes_cb');


    function shortquotes_block($atts, $content = null, $tag, $hook_name) {
        extract(SmartShortCode::shortcode_atts(array(
            'class' => '',
            'footer_title' => ''
                        ), $atts));
        return '<blockquote class="'.$class.'"><p>'.SmartShortCode::do_shortcode($content,$hook_name).'</p><br><footer>'.$footer_title.'</footer></blockquote>';
    }
    SmartShortCode::add_shortcode('quotes_block', 'shortquotes_block');

    function shortquotes_block_reverse($atts, $content = null, $tag, $hook_name) {
        extract(SmartShortCode::shortcode_atts(array(
            'class' => '',
            'footer_title' => ''
        ), $atts));
        return '<blockquote class="blockquote-reverse '.$class.'"><p>'.SmartShortCode::do_shortcode($content,$hook_name).'</p><br><footer>'.$footer_title.'</footer></blockquote>';
    }
    SmartShortCode::add_shortcode('quotes_block_reverse', 'shortquotes_block_reverse');





    function testimonial_1($atts, $content = null, $tag, $hook_name) {
        extract(SmartShortCode::shortcode_atts(array(
            'class' => '',
            'client' => ''
                        ), $atts));
        return '<div class="shortcode_testimonial_1 fix '.$class.'">'.SmartShortCode::do_shortcode($content,$hook_name).'<br><br><span>'.$client.'</span></div>';
    }
    SmartShortCode::add_shortcode('testimonial_1', 'testimonial_1');

    function testimonial_2($atts, $content = null, $tag, $hook_name) {
        extract(SmartShortCode::shortcode_atts(array(
            'class' => '',
            'client' => ''
                        ), $atts));
        return '<div class="shortcode_testimonial_2 fix '.$class.'">'.SmartShortCode::do_shortcode($content,$hook_name).'<br><br><span>'.$client.'</span></div>';
    }
    SmartShortCode::add_shortcode('testimonial_2', 'testimonial_2');


    function testimonial_3($atts, $content = null, $tag, $hook_name) {
        extract(SmartShortCode::shortcode_atts(array(
            'class' => '',
            'client' => '',
            'image_src' => ''
            ), $atts));
        return '<div class="shortcode_testimonial_3 fix">
    <img class="img-responsive" alt="" src="'.$image_src.'" />
    <div class="testimonial_3_body_text">'.SmartShortCode::do_shortcode($content,$hook_name).'<br><br><span>'.$client.'</span></div>
    </div>';
    }
    SmartShortCode::add_shortcode('testimonial_3', 'testimonial_3');

    function testimonial_4($atts, $content = null, $tag, $hook_name) {
        extract(SmartShortCode::shortcode_atts(array(
            'class' => '',
            'client' => '',
            'image_src' => ''
            ), $atts));
        return '<div class="shortcode_testimonial_4 bs-example-popover fix"><div class="popover top"><div class="arrow"></div><div class="popover-content">'.SmartShortCode::do_shortcode($content,$hook_name).'</div>
    </div><img alt="" src="'.$image_src.'" /><br><br><span>'.$client.'</span></div>';
    }
    SmartShortCode::add_shortcode('testimonial_4', 'testimonial_4');


    /*
     * grid
     */


    function sds_grid_row_cb($atts = array(),$content = null, $tag, $hook_name){
        
        
        return "<div class='row'>".SmartShortCode::do_shortcode($content,$hook_name)."</div>";
        
    }
    SmartShortCode::add_shortcode('gridrow', 'sds_grid_row_cb');

   
    /*
     * Video
     */

    /*
     * sds gallery
     */
    function sds_gallery_wrap_cb($atts = array(), $content = null, $tag, $hook_name){
        extract(SmartShortCode::shortcode_atts(array(
            'id' => '',            
        ), $atts));
        
        $html = '<div id="'.$id.'" class="sds_gallery_group">';
        $html .= SmartShortCode::do_shortcode($content,$hook_name);
        $html .= '</div>';
        
        ob_start();
        ?>
        <script type="text/javascript">
            $('#<?php echo $id?>').magnificPopup({
                delegate: 'a', // child items selector, by clicking on it popup will open
                type: 'image',
                gallery: {
                        enabled: true,
                        navigateByImgClick: true,
                        preload: [0,1] // Will preload 0 - before current, and 1 after the current image
                },
                // other options
            });
        </script>
        <?php
        $html .= ob_get_clean();
        return $html;
    }

    SmartShortCode::add_shortcode('sds_gallery_wrap', 'sds_gallery_wrap_cb');

  


    function sds_tslide_cb($atts = array(),$content = null, $tag, $hook_name){
        extract(SmartShortCode::shortcode_atts(array(        
            'author' => '',             
        ), $atts));
        $return = "<li>";
        
        ob_start();
        ?>
        <div class="quote_bottom_content">        
            <p><?php echo $content?></p>
            <p class="quote_author"><?php echo $author;?></p>
        </div>
        <?php    
        $return .= ob_get_clean();    
        $return .= '</li>';
        
        return $return;
        
    }
    SmartShortCode::add_shortcode('tslide', 'sds_tslide_cb');

    function sds_bx_slide_cb($atts = array(),$content = null, $tag, $hook_name){
        extract(SmartShortCode::shortcode_atts(array(
            'src' => '',
            'title' => '',             
        ), $atts));
        $return = "<li class='homeslider-container'>";
        if(!empty($src))
        $return .= "<img src='{$src}' alt='sds_slide' />";
        $return .= "<div class='homeslider-description'>";
        if(!empty($title)){
           $return .= "<h2>{$title}</h2>"; 
        }
        if(!empty($content)){    
           $return .= "<p>$content</p>"; 
        }
        
        $return .= "</div></li>";
        
        return $return;
        
    }
    SmartShortCode::add_shortcode('bx_slide', 'sds_bx_slide_cb');



    function smartshortcode_slider_cb($atts = array(), $content = null, $tag, $hook_name) {

        //jquery.flexslider.js
        extract( SmartShortCode::shortcode_atts(array(
            'id' => 'page_slider',
                        ), $atts));

    $output = "
    <script type='text/javascript'>
    $(document).ready(function(){ 
    $('#$id').flexslider({
            animation: 'slide',
            controlNav: true,              
            directionNav: false
        }); 
      }); 
    </script>
       "; 
          $output.= '<div id="' . $id . '" class="smart_shortcode_flex_container"><ul class="slides">' . SmartShortCode::do_shortcode($content,$hook_name) . '</ul></div>';
     return $output;
    }

     SmartShortCode::add_shortcode('slider', 'smartshortcode_slider_cb');

    function smartshortcode_slide_cb($atts = array(), $content = null, $tag, $hook_name) {

        extract(SmartShortCode::shortcode_atts(array(
            'imgsrc' => '',
            'imgalt' => ''
        ), $atts));

        return "<li><p class=\"flex-caption\">{$content}</p><img alt='{$imgalt}' src='{$imgsrc}' /></li>";
    }

     
     SmartShortCode::add_shortcode('slide', 'smartshortcode_slide_cb');

  


	function getTemplatePath($template='',$module_name ='smartshortcode'){


  if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/'.$module_name.'/'.$template))
   return _PS_THEME_DIR_.'modules/'.$module_name.'/'.$template;
  elseif (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/'.$module_name.'/views/templates/front/'.$template))
   return _PS_THEME_DIR_.'modules/'.$module_name.'/views/templates/front/'.$template;
  elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.$module_name.'/views/templates/front/'.$template))
   return _PS_MODULE_DIR_.$module_name.'/views/templates/front/'.$template;

  return false;

}
    

    /*
     * Smart Blog
     */	
    

  
    class SDS_UI_Accordion{
        static $accordion_contents = '', $parent_id, $counter = 0;
        public static function accordion_cb($atts, $content = null, $tag='',$hook_name=''){
            self::$parent_id = 'accordion-'.rand(00000,99999);
            SmartShortCode::do_shortcode($content,$hook_name);        
            $realcontent = self::$accordion_contents;
            $parentid = self::$parent_id;
            self::$parent_id = self::$accordion_contents = ''; //reset accordion content....
            self::$counter = 0;
            return '<div id="'.$parentid.'" class="panel-group">'.$realcontent.'</div>';
        }
        public static function accordion_tab_cb($atts, $content = null,$tag='',$hook_name=''){
            extract(SmartShortCode::shortcode_atts(array(
                'title' => '',
                'accordion' => 'true'
            ), $atts));
            $id = 'acc-'.rand(00000,99999);
            $parent = $in = '';
            
            if($accordion === 'true'){
                $parent = 'data-parent="#'.self::$parent_id.'"';
                if(++self::$counter < 2)
                    $in = 'in';
            }
            self::$accordion_contents .= '<div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a class="collapsed" href="#'.$id.'" '.$parent.' data-toggle="collapse">'.$title.'</a></h2></div>';
            self::$accordion_contents .= '<div id="'.$id.'" class="panel-collapse collapse '.$in.'"><div class="panel-body">'.$content.'</div></div></div>';
        }
    }

Hook::exec('sdsShortcodeFront');
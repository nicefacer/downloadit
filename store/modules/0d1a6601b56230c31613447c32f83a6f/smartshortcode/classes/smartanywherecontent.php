<?php
class smartanywherecontent extends ObjectModel
{
        public $id_smart_contentanywhere;   
        public $smartcat;	
        public $active = 1;
        public $id_category;
        public $id_product;
        public $hook_name;
        public $display_type;
        public $prd_page;
        public $prd_specify;
        public $cat_page;
        public $cat_specify;
        public $cms_page;
        public $cms_specify;
        public $blg_page;
        public $blg_specify;
        public $position;
        //lang field
	    public $title;
        public $content;
        
	public static $definition = array(
		'table' => 'smart_contentanywhere',
		'primary' => 'id_smart_contentanywhere',
        'multilang'=>true,
		'fields' => array(
                     'id_category' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                     'id_product' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                     'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
                     'display_type' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                     'prd_page' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                     'prd_specify' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                     'cat_page' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                     'cat_specify' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                     'cms_page' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                     'cms_specify' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                     'blg_page' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                     'blg_specify' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                     'position' => array('type' => self::TYPE_INT),
                     'hook_name' => array('type' => self::TYPE_STRING, 'validate' => 'isString','required' => true),
                        'title' => array('type' => self::TYPE_STRING, 'lang'=>true, 'validate' => 'isString','required' => true),
                        'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString','required'=>true)
		),
	);
    public function __construct($id = null, $id_lang = null, $id_shop = null)
        {
            Shop::addTableAssociation('smart_contentanywhere', array('type' => 'shop'));
                    parent::__construct($id, $id_lang, $id_shop);
        }

    public static function GetHookVale($hook_name,$id_lang = null){
        if($id_lang == null){
                    $id_lang = (int)Context::getContext()->language->id;
                }
        $sql = 'SELECT * FROM '._DB_PREFIX_.'smart_contentanywhere p INNER JOIN 
                '._DB_PREFIX_.'smart_contentanywhere_lang pl ON p.id_smart_contentanywhere=pl.id_smart_contentanywhere INNER JOIN 
                '._DB_PREFIX_.'smart_contentanywhere_shop ps ON p.id_smart_contentanywhere = ps.id_smart_contentanywhere AND ps.id_shop = '.(int) Context::getContext()->shop->id.' 
                WHERE pl.id_lang='.$id_lang.'
                AND p.hook_name= "'.$hook_name.'" AND p.active = 1';
        if (!$posts = Db::getInstance()->executeS($sql))
            return false;
        $i = 0;

            foreach($posts as $post){
                $result[$i]['id_smart_contentanywhere'] = $post['id_smart_contentanywhere'];
                $result[$i]['id_category'] = $post['id_category'];
                $result[$i]['id_product'] = $post['id_product'];
                $result[$i]['active'] = $post['active'];
                $result[$i]['hook_name'] = $post['hook_name'];
                $result[$i]['title'] = $post['title'];
                if((Module::isEnabled('smartshortcode') == 1) && (Module::isInstalled('smartshortcode') == 1)){
                require_once(_PS_MODULE_DIR_ . 'smartshortcode/smartshortcode.php');
                $smartshortcode = new SmartShortCode();
                
                $result[$i]['content'] = $smartshortcode->parse($post['content'],$hook_name);
                
                }else{
                 $result[$i]['content'] = $post['content'];
                 }
                $i++;
            }
        return $result;
    }
    public static function GetHookValeByCat($hook_name,$id_category,$id_lang = null){
        if($id_lang == null){
                    $id_lang = (int)Context::getContext()->language->id;
                }
        $sql = 'SELECT * FROM '._DB_PREFIX_.'smart_contentanywhere p INNER JOIN 
                '._DB_PREFIX_.'smart_contentanywhere_lang pl ON p.id_smart_contentanywhere=pl.id_smart_contentanywhere INNER JOIN 
                '._DB_PREFIX_.'smart_contentanywhere_shop ps ON p.id_smart_contentanywhere = ps.id_smart_contentanywhere AND ps.id_shop = '.(int) Context::getContext()->shop->id.' 
                WHERE pl.id_lang='.$id_lang.'
                AND p.hook_name= "'.$hook_name.'" AND p.active = 1 AND p.id_category = "'.$id_category.'"';
        
        if (!$posts = Db::getInstance()->executeS($sql))
            return false;
            
            $i = 0;
            $result = array();
            foreach($posts as $post){
                $result[$i] = array();
                $result[$i]['id_smart_contentanywhere'] = $post['id_smart_contentanywhere'];
                $result[$i]['id_category'] = $post['id_category'];
                $result[$i]['id_product'] = $post['id_product'];
                $result[$i]['active'] = $post['active'];
                $result[$i]['hook_name'] = $post['hook_name'];
                $result[$i]['title'] = $post['title'];
                if((Module::isEnabled('smartshortcode') == 1) && (Module::isInstalled('smartshortcode') == 1)){
                require_once(_PS_MODULE_DIR_ . 'smartshortcode/smartshortcode.php');
                $smartshortcode = new SmartShortCode();
                
                $result[$i]['content'] = $smartshortcode->parse($post['content'],$hook_name);
                }else{
                 $result[$i]['content'] = $post['content'];
                 }
                $i++;
            }
            
        return $result;
    }
    public static function GetHookValeByPrd($hook_name,$id_product,$id_lang = null){
        if($id_lang == null){
                    $id_lang = (int)Context::getContext()->language->id;
                }
        $sql = 'SELECT * FROM '._DB_PREFIX_.'smart_contentanywhere p INNER JOIN 
                '._DB_PREFIX_.'smart_contentanywhere_lang pl ON p.id_smart_contentanywhere=pl.id_smart_contentanywhere INNER JOIN 
                '._DB_PREFIX_.'smart_contentanywhere_shop ps ON p.id_smart_contentanywhere = ps.id_smart_contentanywhere AND ps.id_shop = '.(int) Context::getContext()->shop->id.' 
                WHERE pl.id_lang='.$id_lang.'
                AND p.hook_name= "'.$hook_name.'" AND p.active = 1 AND p.id_product = "'.$id_product.'"';
        if (!$posts = Db::getInstance()->executeS($sql))
            return false;
        $i = 0;

            foreach($posts as $post){
                $result[$i]['id_smart_contentanywhere'] = $post['id_smart_contentanywhere'];
                $result[$i]['id_category'] = $post['id_category'];
                $result[$i]['id_product'] = $post['id_product'];
                $result[$i]['active'] = $post['active'];
                $result[$i]['hook_name'] = $post['hook_name'];
                $result[$i]['title'] = $post['title'];
                if((Module::isEnabled('smartshortcode') == 1) && (Module::isInstalled('smartshortcode') == 1)){
                require_once(_PS_MODULE_DIR_ . 'smartshortcode/smartshortcode.php');
                $smartshortcode = SmartShortCode::getInstance();                
                $result[$i]['content'] = $smartshortcode->parse($post['content'],$hook_name);
                }else{                    
                 $result[$i]['content'] = $post['content'];
                 }
                $i++;
            }
            
        return $result;
    }
    public static function GetHookValeByNone($hook_name,$id_lang = null){
        $id_category = 'none';
        $id_product = '-1';
        if($id_lang == null){
                    $id_lang = (int)Context::getContext()->language->id;
                }
        $sql = 'SELECT * FROM '._DB_PREFIX_.'smart_contentanywhere p INNER JOIN 
                '._DB_PREFIX_.'smart_contentanywhere_lang pl ON p.id_smart_contentanywhere=pl.id_smart_contentanywhere INNER JOIN 
                '._DB_PREFIX_.'smart_contentanywhere_shop ps ON p.id_smart_contentanywhere = ps.id_smart_contentanywhere AND ps.id_shop = '.(int) Context::getContext()->shop->id.' 
                WHERE pl.id_lang='.$id_lang.'
                AND p.hook_name= "'.$hook_name.'" AND p.active = 1 AND p.id_product = "'.$id_product.'" AND p.id_category = "'.$id_category.'"';
        if (!$posts = Db::getInstance()->executeS($sql))
            return false;
        $i = 0;
            foreach($posts as $post){
                $result[$i]['id_smart_contentanywhere'] = $post['id_smart_contentanywhere'];
                $result[$i]['id_category'] = $post['id_category'];
                $result[$i]['id_product'] = $post['id_product'];
                $result[$i]['active'] = $post['active'];
                $result[$i]['hook_name'] = $post['hook_name'];
                $result[$i]['title'] = $post['title'];
                if((Module::isEnabled('smartshortcode') == 1) && (Module::isInstalled('smartshortcode') == 1)){
                require_once(_PS_MODULE_DIR_ . 'smartshortcode/smartshortcode.php');                
                $smartshortcode = new SmartShortCode();
                
                $result[$i]['content'] = $smartshortcode->parse($post['content'],$hook_name);
                }else{
                 $result[$i]['content'] = $post['content'];
                 }
                $i++;
            }
        return $result;
    }
    //START NEW UPDATE
    public static function GetInstance()
    {
        $ins = new smartanywherecontent();
        return $ins;
    }
    public function add($autodate = true, $null_values = false)
    {
        if ($this->position <= 0)
            $this->position = smartanywherecontent::getHigherPosition() + 1;
        if (!parent::add($autodate, $null_values) || !Validate::isLoadedObject($this))
            return false;
        return true;
    }
    public static function getHigherPosition()
    {
        $sql = 'SELECT MAX(`position`)
                FROM `'._DB_PREFIX_.'smart_contentanywhere`';
        $position = DB::getInstance()->getValue($sql);
        return (is_numeric($position)) ? $position : -1;
    }
    public function getAllCMSPage()
    {
        $results = array();
        $results[0]['id_cms'] = 'none';
        $results[0]['name'] = 'None';
        $i = 1;
        $allcategories = CMS::listCms();
        foreach($allcategories as  $value){
           $results[$i]['id_cms'] = $value['id_cms'];
           $results[$i]['name'] = $value['meta_title'];
           $i++;
        }
        return $results;
    }
    public function getAllProductsByCats()
    {
        $results = array();
        $results[0]['id_product'] = 'none';
        $results[0]['name'] = 'None';
        $i = 1;
        $allcategories = $this->generateCategoriesOption(Category::getNestedCategories(null, (int)Context::getContext()->language->id, true));
        foreach($allcategories as  $value){
           $results[$i]['id_product'] = 'CAT_'.$value['id_category'];
           $results[$i]['name'] = 'Category-------'.$value['name'];
                $catproducts = self::getProductsByCategoryID($value['id_category']);
                if(isset($catproducts) && !empty($catproducts)){
                     foreach($catproducts as  $catproduct){
                        $i++;
                        $results[$i]['id_product'] = 'PRD_'.$catproduct['id_product'];
                        $results[$i]['name'] = $catproduct['name'];
                     }
                }
           $i++;
        }
        return $results;
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
    public static function getProductsByCategoryID($category_id,$id_lang = null, $id_shop = null, $child_count = false, $order_by = 'id_product', $order_way = "DESC")
    {
            $context = Context::getContext(); 
            $id_lang = is_null($id_lang) ? $context->language->id : $id_lang ;
            $id_shop = is_null($id_shop) ? $context->shop->id : $id_shop ;
            $id_supplier = '';
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
           $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if(!$result)
            return array();    
        return Product::getProductsProperties($id_lang, $result);
    }
    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS('
            SELECT `id_smart_contentanywhere`, `position`
            FROM `'._DB_PREFIX_.'smart_contentanywhere`
            ORDER BY `position` ASC'
        ))
            return false;
        foreach ($res as $smart_contentanywhere)
            if ((int)$smart_contentanywhere['id_smart_contentanywhere'] == (int)$this->id)
                $moved_smart_contentanywhere = $smart_contentanywhere;
        if (!isset($moved_smart_contentanywhere) || !isset($position))
            return false;
        $query_1 = ' UPDATE `'._DB_PREFIX_.'smart_contentanywhere`
        SET `position`= `position` '.($way ? '- 1' : '+ 1').'
        WHERE `position`
        '.($way
        ? '> '.(int)$moved_smart_contentanywhere['position'].' AND `position` <= '.(int)$position
        : '< '.(int)$moved_smart_contentanywhere['position'].' AND `position` >= '.(int)$position.'
        ');
        $query_2 = ' UPDATE `'._DB_PREFIX_.'smart_contentanywhere`
        SET `position` = '.(int)$position.'
        WHERE `id_smart_contentanywhere` = '.(int)$moved_smart_contentanywhere['id_smart_contentanywhere'];
        return (Db::getInstance()->execute($query_1)
        && Db::getInstance()->execute($query_2));
    }
    // Update featured smart_contentanywhere GetVcContentAnyWhereByHook
    public function GetVcContentAnyWhereByHook($hook_name = '')
    {
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'smart_contentanywhere` v 
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_lang` vl ON (v.`id_smart_contentanywhere` = vl.`id_smart_contentanywhere` AND vl.`id_lang` = '.$id_lang.')
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_shop` vs ON (v.`id_smart_contentanywhere` = vs.`id_smart_contentanywhere` AND vs.`id_shop` = '.$id_shop.')
                WHERE ';
        if(isset($hook_name) && !empty($hook_name)){
            $hook_retro_name = Hook::getRetroHookName($hook_name);
            $sql .= '( v.`hook_name` = "'.$hook_name.'" or v.`hook_name` = "'.$hook_retro_name.'") AND ';
        }
        $sql .= ' v.`active` = 1 ORDER BY v.`position` ASC';
        $results = Db::getInstance()->executeS($sql);
        $outputs = $this->ContentFilterEngine($results);
        return $outputs;
    }
    public function GetVcContentByAll($hook_name = '')
    {
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'smart_contentanywhere` v 
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_lang` vl ON (v.`id_smart_contentanywhere` = vl.`id_smart_contentanywhere` AND vl.`id_lang` = '.$id_lang.')
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_shop` vs ON (v.`id_smart_contentanywhere` = vs.`id_smart_contentanywhere` AND vs.`id_shop` = '.$id_shop.')
                WHERE ';
        if(isset($hook_name) && !empty($hook_name)){
            $hook_retro_name = Hook::getRetroHookName($hook_name);
            $sql .= '( v.`hook_name` = "'.$hook_name.'" or v.`hook_name` = "'.$hook_retro_name.'") AND ';
        }
        $sql .= ' v.`display_type` = 1 AND ';
        $sql .= ' v.`active` = 1 ORDER BY v.`position` ASC';
        $results = Db::getInstance()->executeS($sql);
        $outputs = $this->ContentFilterEngine($results);
        return $outputs;
    }
    public function GetVcContentByAllPRD($hook_name = '')
    {
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'smart_contentanywhere` v 
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_lang` vl ON (v.`id_smart_contentanywhere` = vl.`id_smart_contentanywhere` AND vl.`id_lang` = '.$id_lang.')
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_shop` vs ON (v.`id_smart_contentanywhere` = vs.`id_smart_contentanywhere` AND vs.`id_shop` = '.$id_shop.')
                WHERE ';
        if(isset($hook_name) && !empty($hook_name)){
            $hook_retro_name = Hook::getRetroHookName($hook_name);
            $sql .= '( v.`hook_name` = "'.$hook_name.'" or v.`hook_name` = "'.$hook_retro_name.'") AND ';
        }
        $sql .= ' v.`display_type` = 0 AND ';
        $sql .= ' v.`prd_page` = 1 AND ';
        $sql .= ' v.`active` = 1 ORDER BY v.`position` ASC';
        $results = Db::getInstance()->executeS($sql);
        $outputs = $this->ContentFilterEngine($results);
        return $outputs;
    }
    public function getProductCategories($id_product = 1)
    {
        $reslt = array();
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT cp.`id_category` AS id
            FROM `'._DB_PREFIX_.'category_product` cp
            LEFT JOIN `'._DB_PREFIX_.'category` c ON (c.id_category = cp.id_category)
            '.Shop::addSqlAssociation('category', 'c').'
            WHERE cp.`id_product` = '.(int)$id_product
        );
        if(isset($results) && !empty($results)){
            foreach ($results as $result) {
                $reslt[] = $result['id'];
            }
        }
        return $reslt;
    }
    public function GetVcContentByAllPRDCATID($hook_name = '',$id_prd_cat = 1)
    {
        $reslt = array();
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'smart_contentanywhere` v 
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_lang` vl ON (v.`id_smart_contentanywhere` = vl.`id_smart_contentanywhere` AND vl.`id_lang` = '.$id_lang.')
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_shop` vs ON (v.`id_smart_contentanywhere` = vs.`id_smart_contentanywhere` AND vs.`id_shop` = '.$id_shop.')
                WHERE ';
        if(isset($hook_name) && !empty($hook_name)){
            $hook_retro_name = Hook::getRetroHookName($hook_name);
            $sql .= '( v.`hook_name` = "'.$hook_name.'" or v.`hook_name` = "'.$hook_retro_name.'") AND ';
        }
        $sql .= ' v.`display_type` = 0 AND ';
        $sql .= ' v.`prd_page` = 0 AND ';
        $sql .= ' v.`active` = 1 ORDER BY v.`position` ASC';
        $results = Db::getInstance()->executeS($sql);
        if(isset($results) && !empty($results)){
            $i = 0;
            foreach($results as $result){
                if(isset($result['prd_specify']) && !empty($result['prd_specify'])){
                    $prd_specify = explode(',',$result['prd_specify']);
                    if(isset($prd_specify) && !empty($prd_specify)){
                        if(in_array('CAT_'.$id_prd_cat,$prd_specify)){
                            $reslt[$i] = $result;
                        }
                    }
                }
            $i++;
            }
        }
        $outputs = $this->ContentFilterEngine($reslt);
        return $outputs;
    }
    public function GetVcContentByAllPRDID($hook_name = '',$id_product = 1)
    {
        $reslt = array();
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'smart_contentanywhere` v 
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_lang` vl ON (v.`id_smart_contentanywhere` = vl.`id_smart_contentanywhere` AND vl.`id_lang` = '.$id_lang.')
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_shop` vs ON (v.`id_smart_contentanywhere` = vs.`id_smart_contentanywhere` AND vs.`id_shop` = '.$id_shop.')
                WHERE ';
        if(isset($hook_name) && !empty($hook_name)){
            $hook_retro_name = Hook::getRetroHookName($hook_name);
            $sql .= '( v.`hook_name` = "'.$hook_name.'" or v.`hook_name` = "'.$hook_retro_name.'") AND ';
        }
        $sql .= ' v.`display_type` = 0 AND ';
        $sql .= ' v.`prd_page` = 0 AND ';
        $sql .= ' v.`active` = 1 ORDER BY v.`position` ASC';
        $results = Db::getInstance()->executeS($sql);
        if(isset($results) && !empty($results)){
            $i = 0;
            foreach($results as $result){
                if(isset($result['prd_specify']) && !empty($result['prd_specify'])){
                    $prd_specify = explode(',',$result['prd_specify']);
                    if(isset($prd_specify) && !empty($prd_specify)){
                        if(in_array('PRD_'.$id_product,$prd_specify)){
                            $reslt[$i] = $result;
                        }
                    }
                }
            $i++;
            }
        }
        $outputs = $this->ContentFilterEngine($reslt);
        return $outputs;
    }
    public function GetVcContentByAllCAT($hook_name = '')
    {
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'smart_contentanywhere` v 
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_lang` vl ON (v.`id_smart_contentanywhere` = vl.`id_smart_contentanywhere` AND vl.`id_lang` = '.$id_lang.')
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_shop` vs ON (v.`id_smart_contentanywhere` = vs.`id_smart_contentanywhere` AND vs.`id_shop` = '.$id_shop.')
                WHERE ';
        if(isset($hook_name) && !empty($hook_name)){
            $hook_retro_name = Hook::getRetroHookName($hook_name);
            $sql .= '( v.`hook_name` = "'.$hook_name.'" or v.`hook_name` = "'.$hook_retro_name.'") AND ';
        }
        $sql .= ' v.`display_type` = 0 AND ';
        $sql .= ' v.`cat_page` = 1 AND ';
        $sql .= ' v.`active` = 1 ORDER BY v.`position` ASC';
        $results = Db::getInstance()->executeS($sql);
        $outputs = $this->ContentFilterEngine($results);
        return $outputs;
    }
    public function GetVcContentByAllCATID($hook_name = '',$id_category = 1)
    {
        $reslt = array();
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'smart_contentanywhere` v 
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_lang` vl ON (v.`id_smart_contentanywhere` = vl.`id_smart_contentanywhere` AND vl.`id_lang` = '.$id_lang.')
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_shop` vs ON (v.`id_smart_contentanywhere` = vs.`id_smart_contentanywhere` AND vs.`id_shop` = '.$id_shop.')
                WHERE ';
        if(isset($hook_name) && !empty($hook_name)){
            $hook_retro_name = Hook::getRetroHookName($hook_name);
            $sql .= '( v.`hook_name` = "'.$hook_name.'" or v.`hook_name` = "'.$hook_retro_name.'") AND ';
        }
        $sql .= ' v.`display_type` = 0 AND ';
        $sql .= ' v.`cat_page` = 0 AND ';
        $sql .= ' v.`active` = 1 ORDER BY v.`position` ASC';
        $results = Db::getInstance()->executeS($sql);
        if(isset($results) && !empty($results)){
            $i = 0;
            foreach($results as $result){
                if(isset($result['cat_specify']) && !empty($result['cat_specify'])){
                    $cat_specify = explode(',',$result['cat_specify']);
                    if(isset($cat_specify) && !empty($cat_specify)){
                        if(in_array($id_category,$cat_specify)){
                            $reslt[$i] = $result;
                        }
                    }
                }
            $i++;
            }
        }
        $outputs = $this->ContentFilterEngine($reslt);
        return $outputs;
    }
    public function GetVcContentByAllCMS($hook_name = '')
    {
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'smart_contentanywhere` v 
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_lang` vl ON (v.`id_smart_contentanywhere` = vl.`id_smart_contentanywhere` AND vl.`id_lang` = '.$id_lang.')
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_shop` vs ON (v.`id_smart_contentanywhere` = vs.`id_smart_contentanywhere` AND vs.`id_shop` = '.$id_shop.')
                WHERE ';
        if(isset($hook_name) && !empty($hook_name)){
            $hook_retro_name = Hook::getRetroHookName($hook_name);
            $sql .= '( v.`hook_name` = "'.$hook_name.'" or v.`hook_name` = "'.$hook_retro_name.'") AND ';
        }
        $sql .= ' v.`display_type` = 0 AND ';
        $sql .= ' v.`cms_page` = 1 AND ';
        $sql .= ' v.`active` = 1 ORDER BY v.`position` ASC';
        $results = Db::getInstance()->executeS($sql);
        $outputs = $this->ContentFilterEngine($results);
        return $outputs;
    }
    public function GetVcContentByAllCMSID($hook_name = '',$id_cms = 1)
    {
        $reslt = array();
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'smart_contentanywhere` v 
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_lang` vl ON (v.`id_smart_contentanywhere` = vl.`id_smart_contentanywhere` AND vl.`id_lang` = '.$id_lang.')
                INNER JOIN `'._DB_PREFIX_.'smart_contentanywhere_shop` vs ON (v.`id_smart_contentanywhere` = vs.`id_smart_contentanywhere` AND vs.`id_shop` = '.$id_shop.')
                WHERE ';
        if(isset($hook_name) && !empty($hook_name)){
            $hook_retro_name = Hook::getRetroHookName($hook_name);
            $sql .= '( v.`hook_name` = "'.$hook_name.'" or v.`hook_name` = "'.$hook_retro_name.'") AND ';
        }
        $sql .= ' v.`display_type` = 0 AND ';
        $sql .= ' v.`cms_page` = 0 AND ';
        $sql .= ' v.`active` = 1 ORDER BY v.`position` ASC';
        $results = Db::getInstance()->executeS($sql);
        if(isset($results) && !empty($results)){
            $i = 0;
            foreach($results as $result){
                if(isset($result['cms_specify']) && !empty($result['cms_specify'])){
                    $cms_specify = explode(',',$result['cms_specify']);
                    if(isset($cms_specify) && !empty($cms_specify)){
                        if(in_array($id_cms,$cms_specify)){
                            $reslt[$i] = $result;
                        }
                    }
                }
            $i++;
            }
        }
        $outputs = $this->ContentFilterEngine($reslt);
        return $outputs;
    }
    public function ContentFilterEngine($results = array())
    {
        $outputs = array();
        if(isset($results) && !empty($results)){
            $i = 0;
            foreach($results as $vcvalues){
                foreach($vcvalues as $vckey => $vcval){
                    if($vckey == 'content'){
                        $outputs[$i][$vckey] = SmartShortCode::content_filter($vcval);
                    }else{
                        $outputs[$i][$vckey] = $vcval;
                    } 
                }
            $i++;
            }
        }
        return $outputs;
    }
}
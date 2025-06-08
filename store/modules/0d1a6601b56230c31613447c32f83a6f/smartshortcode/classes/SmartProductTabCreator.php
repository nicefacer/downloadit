<?php

class SmartProductTabClass extends ObjectModel
{
      public $id_smart_product_tab;
      public $id_product;
      public $active = 1;
      public $position = 0;
      public $title;
      public $content;
      
      public static $definition = array(
		'table' => 'smart_product_tab',
		'primary' => 'id_smart_product_tab',
    'multilang'=>true,
		'fields' => array(
                     'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
                     'position' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
                     'title' => array('type' => self::TYPE_STRING,  'validate' => 'isString','lang'=>true),
                     'content' => array('type' => self::TYPE_HTML, 'validate' => 'isString','lang'=>true),
                     'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool')
		),
	);
  public function __construct($id = null, $id_lang = null, $id_shop = null)
          {
              Shop::addTableAssociation('smart_product_tab', array('type' => 'shop'));
                      parent::__construct($id, $id_lang, $id_shop);
          }
  public static function getTabTitle($id_product,$id_lang){
        $result = array();
        $id_shop = Context::getcontext()->shop->id;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'smart_product_tab` pt INNER JOIN `'._DB_PREFIX_.'smart_product_tab_lang` ptl ON(pt.`id_smart_product_tab` = ptl.`id_smart_product_tab` AND ptl.`id_lang` = '.(int)($id_lang).' AND pt.active = 1 AND pt.id_product = '.$id_product.') INNER JOIN `'._DB_PREFIX_.'smart_product_tab_shop` pts ON(pt.id_smart_product_tab = pts.id_smart_product_tab AND pts.id_shop = '.$id_shop.') ORDER BY pt.position ASC';
        $posts = Db::getInstance()->executeS($sql);
        $i = 0;

            foreach($posts as $post){
                $result[$i]['id_smart_product_tab'] = $post['id_smart_product_tab'];
                $result[$i]['id_product'] = $post['id_product'];
                $result[$i]['position'] = $post['position'];
                $result[$i]['active'] = $post['active'];
                $result[$i]['title'] = $post['title'];
                if((Module::isEnabled('smartshortcode') == 1) && (Module::isInstalled('smartshortcode') == 1)){
                require_once(_PS_MODULE_DIR_ . 'smartshortcode/smartshortcode.php');
                $smartshortcode = new SmartShortCode();                
                $result[$i]['content'] = $smartshortcode->parse($post['content']);
                }else{
                 $result[$i]['content'] = $post['content'];
                 }
                $i++;
            }

        $sql2 = 'SELECT * FROM `'._DB_PREFIX_.'smart_product_tab` pt INNER JOIN `'._DB_PREFIX_.'smart_product_tab_lang` ptl ON(pt.`id_smart_product_tab` = ptl.`id_smart_product_tab` AND ptl.`id_lang` = '.(int)($id_lang).' AND pt.active = 1 AND pt.id_product = 0) INNER JOIN `'._DB_PREFIX_.'smart_product_tab_shop` pts ON(pt.id_smart_product_tab = pts.id_smart_product_tab AND pts.id_shop = '.$id_shop.') ORDER BY pt.position ASC';
        
        $posts2 = Db::getInstance()->executeS($sql2);
       $j = $i;
        foreach($posts2 as $post2){
                $result[$j]['id_smart_product_tab'] = $post2['id_smart_product_tab'];
                $result[$j]['id_product'] = $post2['id_product'];
                $result[$j]['position'] = $post2['position'];
                $result[$j]['active'] = $post2['active'];
                $result[$j]['title'] = $post2['title'];
                if((Module::isEnabled('smartshortcode') == 1) && (Module::isInstalled('smartshortcode') == 1)){
                require_once(_PS_MODULE_DIR_ . 'smartshortcode/smartshortcode.php');
                $smartshortcode = new SmartShortCode();
                $result[$j]['content'] = $smartshortcode->parse($post2['content']);
                }else{
                 $result[$j]['content'] = $post2['content'];
                 }
                $j++;
            }

        return $result;
    }
}

<?php
require_once (dirname(__FILE__) . '/../../classes/smartanywherecontent.php');
class AdminsmartanywherecontentController extends ModuleAdminController{
  protected $countries_array = array();
  protected $position_identifier = 'id_smart_contentanywhere';
  public $asso_type = 'shop';
  private $original_filter = '';
	public function __construct()
	{
	 	$this->table = 'smart_contentanywhere';
		$this->className = 'smartanywherecontent';
    $this->lang = true;
    $this->deleted = false;
    $this->module = 'smartshortcode';
    $this->explicitSelect = true;
    $this->allow_export = true;
    $this->_defaultOrderBy = 'position';
    $this->_defaultOrderWay = 'DESC';
		$this->bootstrap = true;
		$this->context = Context::getContext();
        if (Shop::isFeatureActive())
        Shop::addTableAssociation($this->table, array('type' => 'shop'));
        parent::__construct();
		$this->fields_list = array(
        'id_smart_contentanywhere' => array(
                'title' => $this->l('Id'),
                'width' => 100,
                'type' => 'text',
                'orderby' => false,
                'filter' => false,
                'search' => false
        ),
        'title' => array(
                'title' => $this->l('Title'),
                'width' => 440,
                'type' => 'text',
                'lang'=>true,
                'orderby' => false,
                'filter' => false,
                'search' => false
        ),
        'hook_name' => array(
          'title' => $this->l('Hook'),
          'type' => 'text',
        ),
        'position' => array(
            'title' => $this->l('Position'),
            'filter_key' => 'a!position',
            'position' => 'position',
            'align' => 'center'
          ),
        'active' => array(
            'title' => $this->l('Status'),
            'width' => '70',
            'align' => 'center',
            'active' => 'status',
            'type' => 'bool',
            'orderby' => false,
            'filter' => false,
            'search' => false
        )
		);
		parent::__construct();
	}
  public function init()
  {
       parent::init();
       $this->_join = 'LEFT JOIN '._DB_PREFIX_.'smart_contentanywhere_shop sbs ON a.id_smart_contentanywhere=sbs.id_smart_contentanywhere && sbs.id_shop IN('.implode(',',Shop::getContextListShopID()).')';
       $this->_select = 'sbs.id_shop';
       $this->_defaultOrderBy = 'a.position';
       $this->_defaultOrderWay = 'DESC';
       if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP)
       {
          $this->_group = 'GROUP BY a.id_smart_contentanywhere';
       }
       $this->_select = 'a.position position';  
  }
  public function setMedia()
  {          
        parent::setMedia();
        $this->addJqueryUi('ui.widget');
        $this->addJqueryPlugin('tagify');
  }
	public function renderList()
  {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
  }
	public function renderForm()
  {
        $prd_specify_values = '';
        $cat_specify_values = '';
        $cms_specify_values = '';
        $display_type_values  = '';
        $prd_page_values  = '';
        $cat_page_values  = '';
        $cms_page_values  = '';
        $prd  = '';
        $cat  = '';
        $id_category = 'no_value';
        $id_product = 'no_value';
        $vc_is_edit = '';
        $getAllCMSPage  = array();
      if(Tools::getvalue('id_smart_contentanywhere')){
        $smartanywherecontent = new smartanywherecontent(Tools::getvalue('id_smart_contentanywhere'));
        $prd_specify_values = $smartanywherecontent->prd_specify;
        $cat_specify_values = $smartanywherecontent->cat_specify;
        $cms_specify_values = $smartanywherecontent->cms_specify;
        $display_type_values = $smartanywherecontent->display_type;
        $prd_page_values = $smartanywherecontent->prd_page;
        $cat_page_values = $smartanywherecontent->cat_page;
        $cms_page_values = $smartanywherecontent->cms_page;
        $vc_is_edit = '1';
      }
      $vccaw = new smartanywherecontent();
      $getAllCMSPage = $vccaw->getAllCMSPage();
      $prd = $vccaw->getAllProductsByCats();
      $cat = $vccaw->generateCategoriesOption(Category::getNestedCategories(null, (int)Context::getContext()->language->id, true));
      $GetAllHooks = array();
      require_once(dirname(__FILE__) . '/../../sql/hook.php');
        $this->fields_form = array(
          'legend' => array(
          'title' => $this->l('Content Anywhere'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'title',
                    'rows' => 10,
                    'cols' => 62,
                    'lang'=>true,
                    'class' => 'rte',
                    'autoload_rte' => false,
                    'required' => true,
                     'desc' => $this->l('Enter Your Title')
                ),array(
                    'type' => 'textarea',
                    'label' => $this->l('Content'),
                    'name' => 'content',
                    'rows' => 10,
                    'cols' => 62,
                    'class' => 'rte',
                    'lang'=>true,
                    'autoload_rte' => true,
                    'required' => true,
                    'desc' => $this->l('Enter Your Description')
                ),
                array(
                    'type' => 'vc_content_type',
                    'name' => 'title',
                    'prd_specify_values' => $prd_specify_values,
                    'cat_specify_values' => $cat_specify_values,
                    'cms_specify_values' => $cms_specify_values,
                    'display_type_values' => $display_type_values,
                    'prd_page_values' => $prd_page_values,
                    'cat_page_values' => $cat_page_values,
                    'cms_page_values' => $cms_page_values,
                    'id_category' => $id_category,
                    'id_product' => $id_product,
                    'vc_is_edit' => $vc_is_edit,
                ),array(
                       'type' => 'switch',
                       'label' => $this->l('Show All Page'),
                       'name' => 'display_type',
                       'required' => false,
                       'class' => 'display_type_class',
                       'is_bool' => true,
                       'values' => array(
                              array(
                                'id' => 'display_type_id_1',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                              ),
                              array(
                                'id' => 'display_type_id_0',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                              )
                       )
                  ),array(
                       'type' => 'switch',
                       'label' => $this->l('Show All Product Page'),
                       'name' => 'prd_page',
                       'required' => false,
                       'class' => 'prd_page_class',
                       'is_bool' => true,
                       'values' => array(
                       array(
                         'id' => 'prd_page_id_1',
                         'value' => 1,
                         'label' => $this->l('Enabled')
                       ),
                       array(
                         'id' => 'prd_page_id_0',
                         'value' => 0,
                         'label' => $this->l('Disabled')
                         )
                       )
                  ),
                  array(
                  'type' => 'select',
                  'label' => $this->l('Select Product'),
                  'name' => 'prd_specify_temp',
                  'class' => 'prd_specify_class',
                  'id' => 'prd_specify_id',
                  'multiple' => true,
                  'options' => array(
                          'query' => $prd,
                          'id' => 'id_product',
                          'name' => 'name'
                        )
                ),
                array(
                       'type' => 'switch',
                       'label' => $this->l('Show All Category Page'),
                       'name' => 'cat_page',
                       'required' => false,
                       'class' => 'cat_page_class',
                       'is_bool' => true,
                       'values' => array(
                       array(
                       'id' => 'cat_page_id_1',
                       'value' => 1,
                       'label' => $this->l('Enabled')
                       ),
                       array(
                       'id' => 'cat_page_id_0',
                       'value' => 0,
                       'label' => $this->l('Disabled')
                         )
                       )
                ),
                array(
                  'type' => 'select',
                  'label' => $this->l('Select Category'),
                  'name' => 'cat_specify_temp',
                  'class' => 'cat_specify_class',
                  'id' => 'cat_specify_id',
                  'multiple' => true,
                  'options' => array(
                          'query' => $cat,
                          'id' => 'id_category',
                          'name' => 'name'
                        )
                ),
                array(
                       'type' => 'switch',
                       'label' => $this->l('Show All CMS Page'),
                       'name' => 'cms_page',
                       'required' => false,
                       'class' => 'cms_page_class',
                       'is_bool' => true,
                       'values' => array(
                       array(
                       'id' => 'cms_page_id_1',
                       'value' => 1,
                       'label' => $this->l('Enabled')
                       ),
                       array(
                       'id' => 'cms_page_id_0',
                       'value' => 0,
                       'label' => $this->l('Disabled')
                         )
                       )
                    ),
                array(
                  'type' => 'select',
                  'label' => $this->l('CMS Page'),
                  'name' => 'cms_specify_temp',
                  'class' => 'cms_specify_class',
                  'id' => 'cms_specify_id',
                  'multiple' => true,
                  'options' => array(
                          'query' => $getAllCMSPage,
                          'id' => 'id_cms',
                          'name' => 'name'
                        )
                ),
                array(
                  'type' => 'select',
                  'label' => $this->l('Select Hook'),
                  'name' => 'hook_name',
                  'options' => array(
                          'query' => $GetAllHooks,
                          'id' => 'id',
                          'name' => 'name'
                        ),
                  'desc' => $this->l('Select Your Hook Position where you want to show this!')
                ),
                array(
                       'type' => 'switch',
                       'label' => $this->l('Status'),
                       'name' => 'active',
                       'required' => false,
                       'class' => 't',
                       'is_bool' => true,
                       'values' => array(
                       array(
                       'id' => 'active',
                       'value' => 1,
                       'label' => $this->l('Enabled')
                       ),
                       array(
                       'id' => 'active',
                       'value' => 0,
                       'label' => $this->l('Disabled')
                         )
                       )
                  )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );
        if (Shop::isFeatureActive())
        {
          $this->fields_form['input'][] = array(
            'type' => 'shop',
            'label' => $this->l('Shop association:'),
            'name' => 'checkBoxShopAsso',
          );
        }
        if(!($smartanywherecontent = $this->loadObject(true)))
            return;
        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
            'class' => 'button'
        );
        if(!Tools::getvalue('id_smart_contentanywhere')){
            $this->fields_value['display_type'] = 1;
            $this->fields_value['prd_page'] = 1;
            $this->fields_value['cat_page'] = 1;
            $this->fields_value['cms_page'] = 1;
        }else{
          $smartanywherecontent = new smartanywherecontent(Tools::getvalue('id_smart_contentanywhere'));
              $this->fields_value['prd_specify_temp'] = $smartanywherecontent->prd_specify;
              $this->fields_value['cat_specify_temp'] = $smartanywherecontent->cat_specify;
              $this->fields_value['cms_specify_temp'] = $smartanywherecontent->cms_specify;
        }
        return parent::renderForm();
    }
    public function initToolbar()
    {
        parent::initToolbar();
    }
    public function processPosition()
    {
      if ($this->tabAccess['edit'] !== '1')
        $this->errors[] = Tools::displayError('You do not have permission to edit this.');
      else if (!Validate::isLoadedObject($object = new smartanywherecontent((int)Tools::getValue($this->identifier, Tools::getValue('id_smart_contentanywhere', 1)))))
        $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').' <b>'.
          $this->table.'</b> '.Tools::displayError('(cannot load object)');
      if (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position')))
        $this->errors[] = Tools::displayError('Failed to update the position.');
      else
      {
        $object->regenerateEntireNtree();
        Tools::redirectAdmin(self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=5'.(($id_smart_contentanywhere = (int)Tools::getValue($this->identifier)) ? ('&'.$this->identifier.'='.$id_smart_contentanywhere) : '').'&token='.Tools::getAdminTokenLite('Adminsmartanywherecontent'));
      }
    }
    public function ajaxProcessUpdatePositions()
    {
      $id_smart_contentanywhere = (int)(Tools::getValue('id'));
      $way = (int)(Tools::getValue('way'));
      $positions = Tools::getValue($this->table);
      if (is_array($positions))
        foreach ($positions as $key => $value)
        {
          $pos = explode('_', $value);
          if ((isset($pos[1]) && isset($pos[2])) && ($pos[2] == $id_smart_contentanywhere))
          {
            $position = $key + 1;
            break;
          }
        }
      $smartanywherecontent = new smartanywherecontent($id_smart_contentanywhere);
      if (Validate::isLoadedObject($smartanywherecontent))
      {
        if (isset($position) && $smartanywherecontent->updatePosition($way, $position))
        {
          Hook::exec('actionsmartanywherecontentUpdate');
          die(true);
        }
        else
          die('{"hasError" : true, errors : "Can not update smartanywherecontent position"}');
      }
      else
        die('{"hasError" : true, "errors" : "This smartanywherecontent can not be loaded"}');
    }
}


<?php
require_once (dirname(__FILE__) . '/../../classes/SmartProductTabCreator.php');
class AdminsmarttabcreatorController extends AdminController{
   
	protected $countries_array = array();
  public $asso_type = 'shop';
	public function __construct()
	{
	 	$this->table = 'smart_product_tab';
		$this->className = 'SmartProductTabClass';
	 	$this->lang = true;
	 	$this->deleted = false;
		$this->allow_export = false;
		$this->bootstrap = true;
		$this->context = Context::getContext();

        if (Shop::isFeatureActive())
        Shop::addTableAssociation($this->table, array('type' => 'shop'));
        parent::__construct();

		$this->fields_list = array(
                            'id_smart_product_tab' => array(
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

        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'smart_product_tab_shop sbs ON a.id_smart_product_tab=sbs.id_smart_product_tab && sbs.id_shop IN('.implode(',',Shop::getContextListShopID()).')';
        $this->_select = 'sbs.id_shop';
        $this->_defaultOrderBy = 'a.id_smart_product_tab';
        $this->_defaultOrderWay = 'DESC';
        
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP)
        {
           $this->_group = 'GROUP BY a.id_smart_product_tab';
        }


		parent::__construct();
	}

	public function renderList() {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
    }

	public function renderForm()
     {
      $sca = new SmartShortCode();
      $prd = $sca->getproduct();
        $this->fields_form = array(
          'legend' => array(
          'title' => $this->l('Tab Creator'),
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
                  'type' => 'select',
                  'label' => $this->l('Select specific Product'),
                  'name' => 'id_product',
                  'options' => array(
                          'query' => $prd,
                          'id' => 'id_product',
                          'name' => 'name'
                        )
                ),
                array(
                       'type' => 'radio',
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
        if (!($SmartProductTabClass = $this->loadObject(true)))
            return;

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save   '),
            'class' => 'button'
        );
        return parent::renderForm();
    }
    
    public function initToolbar() {
        parent::initToolbar();
    }
}


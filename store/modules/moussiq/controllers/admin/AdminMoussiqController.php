<?php
/**
 * Moussiq PRO
 *
 * @category  Module
 * @author    silbersaiten <info@silbersaiten.de>
 * @support   silbersaiten <support@silbersaiten.de>
 * @copyright 2014 silbersaiten
 * @version   2.2.0
 * @link      http://www.silbersaiten.de
 * @license   See joined file licence.txt
 */

require_once(dirname(__FILE__).'/../../classes/ExportTools.php');
require_once(dirname(__FILE__).'/../../classes/Export.php');
require_once(dirname(__FILE__).'/../../classes/MoussiqService.php');

class AdminMoussiqController extends ModuleAdminController
{
	public static $categories_tree = '';
	private static $done = null;

	public function __construct()
	{
		$this->className = 'MoussiqService';
		$this->table = 'moussiq_service';
		$this->context = Context::getContext();
		$this->addRowAction('view');
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->actions_available = array('view', 'edit', 'delete');

		$this->bootstrap = true;


		$this->fields_list = array(
			'id_moussiq_service' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'class' => 'fixed-width-xs'
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 300
			),
			'status' => array(
				'title' => $this->l('Enabled'),
				'class' => 'fixed-width-xs',
				'align' => 'center',
				'active' => 'status',
				'type' => 'bool',
				'orderby' => false
			)
		);

		$this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'icon' => 'icon-trash',
				'confirm' => $this->l('Delete selected items?')
			)
		);

		parent::__construct();
	}

	public function init()
	{
		$this->initOptionsList();
		parent::init();
	}

	public function initContent()
	{
		//$this->display = 'edit';
		$this->initTabModuleList();
		if (!$this->loadObject(true))
			return;

		//$this->initTabModuleList();
		$this->initToolbar();
		$this->initPageHeaderToolbar();

		if ($this->display == 'edit' || $this->display == 'add')
		{
			if (!($this->object = $this->loadObject(true)))
				return;
			$this->content .= $this->renderForm();
		}
		elseif ($this->display == 'view' && !$this->ajax)
		{
			if ($this->className)
				$this->loadObject(true);
			$this->content .= $this->renderView();
			$this->addCSS(__PS_BASE_URI__.'modules/'.$this->module->name.'/views/css/view.css');
		}
		else
		{
			//$this->content .= $this->renderForm();
			$this->content .= $this->renderList();
			$this->content .= $this->renderImportTemplateForm();
			$this->content .= $this->renderOptions();
		}

		$this->context->smarty->assign(array(
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,
			'show_page_header_toolbar' => $this->show_page_header_toolbar,
			'page_header_toolbar_title' => $this->page_header_toolbar_title,
			'page_header_toolbar_btn' => $this->page_header_toolbar_btn
		));
	}

	public function initToolbar()
	{
		switch ($this->display)
		{
			// @todo defining default buttons
			case 'add':
			case 'edit':
			case 'editAttributes':
				// Default save button - action dynamically handled in javascript
				$this->toolbar_btn['save'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);

				if ($this->display == 'editAttributes' && !$this->id_attribute)
					$this->toolbar_btn['save-and-stay'] = array(
						'short' => 'SaveAndStay',
						'href' => '#',
						'desc' => $this->l('Save then add another value', null, null, false),
						'force_desc' => true,
					);

				$this->toolbar_btn['back'] = array(
					'href' => self::$currentIndex.'&token='.$this->token,
					'desc' => $this->l('Back to list', null, null, false)
				);
				break;
			case 'view':
				$this->toolbar_btn['newAttributes'] = array(
					'href' => self::$currentIndex.'&updateattribute&id_attribute_group='.(int)Tools::getValue('id_attribute_group').'&token='.$this->token,
					'desc' => $this->l('Add New Values', null, null, false),
					'class' => 'toolbar-new'
				);

				$this->toolbar_btn['back'] = array(
					'href' => self::$currentIndex.'&token='.$this->token,
					'desc' => $this->l('Back to list', null, null, false)
				);
				break;
			default: // list
				$this->toolbar_btn['new'] = array(
					'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
					'desc' => $this->l('Add New Service', null, null, false)
				);
		}
		/*
		$this->toolbar_btn['import'] = array(
			'href' => $this->context->link->getAdminLink('AdminImport', true).'&import_type=combinations',
			'desc' => $this->l('Import', null, null, false)
		);
		*/
	}

	public function renderView()
	{
		$obj = $this->loadObject(true);
		$currentIndex = $_SERVER['SCRIPT_NAME'].(($controller = Tools::getValue('controller')) ? '?controller='.$controller : '');

		$helper = new HelperView($this);
		$helper->module = $this->module;
		$this->setHelperDisplay($helper);

		$helper->tpl_vars = array(
			'back_url' => $currentIndex.'&token='.Tools::getAdminTokenLite('AdminMoussiq'),
		);

		$file_name = sha1($obj->name._COOKIE_KEY_).'.csv';
		$file_dir = dirname(__FILE__).'/../../export/';
		$file_link = _PS_BASE_URL_._MODULE_DIR_.$this->module->name.'/export/'.$file_name;
		$delimiter = (isset($obj->delimiter) && Tools::strlen($obj->delimiter) > 0) ? $obj->delimiter : Configuration::get('EXPORT_DELIMITER');
		$delimiter = ExportTools::delimiterByKeyWord($delimiter);
		$enclosure = (isset($obj->enclosure) && Tools::strlen($obj->enclosure) > 0) ? $obj->enclosure : Configuration::get('EXPORT_ENCLOSURE');
		$enclosure = ExportTools::getEnclosureFromId($enclosure);

		if (file_exists($file_dir.$file_name))
		{
			$helper->tpl_vars['file_exists'] = true;
			$helper->tpl_vars['fileLink'] = $file_link;

			$file_size = ((float)filesize($file_dir.$file_name)) / pow(1024, 1);
			$file_modified = date('d/m/Y H:i:s', filemtime($file_dir.$file_name));

			$helper->tpl_vars['fileSize'] = number_format($file_size, 2).' '.$this->l('Kb');
			$helper->tpl_vars['fileModified'] = $file_modified;

			$file_not_too_big = $file_size < 1024;
			$helper->tpl_vars['fileNotTooBig'] = $file_not_too_big;

			if ($file_not_too_big)
			{
				if (($handle = fopen($file_dir.$file_name, 'r')) !== false)
				{
					$index = 0;
					$file_data = array();

					$header = (int)($obj->header == '' ? Configuration::get('EXPORT_HEADER') : $obj->header);

					while (($data = fgetcsv($handle, 1000, $delimiter, $enclosure)) !== false)
					{
						$file_data[$index] = $data;
						$index++;
					}
					fclose($handle);

					$helper->tpl_vars['fileData'] = $file_data;
					$helper->tpl_vars['header_exists'] = $header;
				}
			}
		}
		else
			$helper->tpl_vars['file_exists'] = false;

		$view = $helper->generateView();
		return $view;
	}


	public static function recurseCategoryForInclude($id_obj, $indexedCategories, $categories, $current, $id_category = 1, $id_category_default = null, $has_suite = array())
	{
		static $irow;

		if (!isset(self::$done[$current['infos']['id_parent']]))
			self::$done[$current['infos']['id_parent']] = 0;
		self::$done[$current['infos']['id_parent']] += 1;

		$todo = count($categories[$current['infos']['id_parent']]);
		$doneC = self::$done[$current['infos']['id_parent']];

		$level = $current['infos']['level_depth'] + 1;
		$selected = false;
		$name = false;

		foreach ($indexedCategories as $categoryData)
		{
			if (array_key_exists('id_category', $categoryData) && array_key_exists('name', $categoryData))
			{
				if ($id_category == (int)$categoryData['id_category'])
				{
					$selected = true;
					$name = $categoryData['name'];
				}
			}
		}

		self::$categories_tree .= '
		<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
			<td>
				<input type="checkbox" name="categoryBox['.$id_category.'][id_category]" class="categoryBox'.($id_category_default == $id_category ? ' id_category_default' : '').'" id="categoryBox_'.$id_category.'" value="'.$id_category.'"'.(($selected) ? ' checked="checked"' : '').' />
			</td>
			<td>
				'.$id_category.'
			</td>
			<td>';

		for ($i = 2; $i < $level; $i++)
			if (isset($has_suite[$i - 2]))
				self::$categories_tree .= '<img src="../img/admin/lvl_'.$has_suite[$i - 2].'.gif" alt="" />';
			else
				self::$categories_tree .= ' ';

		self::$categories_tree .= '<img src="../img/admin/'.($level == 1 ? 'lv1.gif' : 'lv2_'.($todo == $doneC ? 'f' : 'b').'.gif').'" alt="" /> &nbsp;
			<label for="categoryBox_'.$id_category.'" class="t">'.Tools::stripslashes($current['infos']['name']).'</label>
			</td>
			<td>
				<input type="text" name="categoryBox['.$id_category.'][name]" value="'.$name.'" />
			</td>
		</tr>';

		if ($level > 1)
			$has_suite[] = ($todo == $doneC ? 0 : 1);
		if (isset($categories[$id_category]))
			foreach ($categories[$id_category] as $key => $row)
				if ($key != 'infos')
					self::recurseCategoryForInclude($id_obj, $indexedCategories, $categories, $categories[$id_category][$key], $key, $id_category_default, $has_suite);
	}

	public function renderForm()
	{
		$this->multiple_fieldsets = true;

		$obj = $this->loadObject(true);
		$fields = $this->getReadableFields($this->getDbFieldNames(array('product', 'product_lang')));
		$countries = Country::getCountries((int)$this->context->language->id);
		$carriers = Carrier::getCarriers((int)$this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
		$languages = Language::getLanguages();
		$customerGroups = Group::getGroups((int)$this->context->language->id);
		$engines = $this->getExportEngines();
		$list_engines = array();
		foreach ($engines as $engine => $name)
			$list_engines[] = array('engine' => $engine, 'name' => $name);

		$shops = Db::getInstance()->ExecuteS('SELECT `id_shop`, `name` FROM `'._DB_PREFIX_.'shop`');
		$conditions = array(
			array('condition' => 'new:used:refurbished', 'name' => $this->l('New').', '.$this->l('Used').', '.$this->l('Refurbished')),
			array('condition' => 'new', 'name' => $this->l('New')),
			array('condition' => 'used', 'name' => $this->l('Used')),
			array('condition' => 'refurbished', 'name' => $this->l('Refurbished')),
			array('condition' => 'new:used', 'name' => $this->l('New').', '.$this->l('Used')),
			array('condition' => 'new:refurbished', 'name' => $this->l('New').', '.$this->l('Refurbished')),
			array('condition' => 'used:refurbished', 'name' => $this->l('Used').', '.$this->l('Refurbished')),
		);

		/*
		$combinationsSet    = self::getSettingsValue(Tools::getValue('combinations', null), $obj->combinations);
		$combinationnameSet    = self::getSettingsValue(Tools::getValue('combination_name', null), $obj->combination_name);
		$headerSet          = self::getSettingsValue(Tools::getValue('header', null), $obj->header);
		$export_engine      = self::getSettingsValue(Tools::getValue('export_engine', null), $obj->export_engine);
		$export_inactiveSet = self::getSettingsValue(Tools::getValue('export_inactive', null), $obj->export_inactive);
		$enclosureSet       = self::getSettingsValue(Tools::getValue('enclosure', null), $obj->enclosure);
		$carrierSet         = self::getSettingsValue(Tools::getValue('id_carrier', null), $obj->id_carrier);
		$countrySet         = self::getSettingsValue(Tools::getValue('id_country', null), $obj->id_country);
		$stateSet           = self::getSettingsValue(Tools::getValue('id_state', null), $obj->id_state);
		$conditionSet       = self::getSettingsStringValue(Tools::getValue('condition', null), $obj->condition);
		$langSet            = self::getSettingsValue(Tools::getValue('id_lang', null), $obj->id_lang);
		$groupSet           = self::getSettingsValue(Tools::getValue('id_group', null), $obj->id_group);
		$shopSet          	= self::getSettingsValue(Tools::getValue('id_shop', null), $obj->id_shop);
		*/


		$psVersion = explode('.', _PS_VERSION_);
		$reductionAllowed = ((int)$psVersion[0] == 1 && (int)$psVersion[1] < 4);

		$this->addCSS(__PS_BASE_URI__.'modules/'.$this->module->name.'/views/css/style.css');
		$this->addJS(__PS_BASE_URI__.'modules/'.$this->module->name.'/js/jquery-ui.min.js');
		$this->addJS(__PS_BASE_URI__.'modules/'.$this->module->name.'/js/json2.js');

		$this->addJS(__PS_BASE_URI__.'modules/'.$this->module->name.'/js/helper.js');
		$this->addJS(__PS_BASE_URI__.'modules/'.$this->module->name.'/js/exporter.js');
		$this->addJS(__PS_BASE_URI__.'modules/'.$this->module->name.'/js/init.js');
		$this->addJS(__PS_BASE_URI__.'modules/'.$this->module->name.'/js/timepicker.js');


		$this->fields_form['hoho'] = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Service name').': '.(isset($obj->name) ? $obj->name : ''),
					'icon' => 'icon-info-sign'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Name'),
						'name' => 'name',
						'required' => true,
						'desc' => $this->l('Service name')
					),
					array(
						'type' => 'html',
						'name' => 'moussiqSettings',
						'html_content' => '
							<span id="toggleMousiqueSettings" class="btn btn-default">
								<i class="icon-arrow-down"></i> '.$this->l('Click here to display template settings').'<i class="icon-arrow-down"></i>
							</span>
						'
					),
				)
			)
		);

		self::$categories_tree = '';

		$categories = Category::getCategories((int)($this->context->language->id), false);
		$indexedCategories = $obj->id ? MoussiqService::getCategories($obj->id) : array();

		self::recurseCategoryForInclude((int)(Tools::getValue($this->identifier)), $indexedCategories, $categories, $categories[0][1], 1, $obj->id);

		$this->fields_form['moussiqSettings'] = array(
			'form' => array(
				'legend' => array(
					'title' => ($obj->id ? $this->l('Update existing service') : $this->l('Add new service')),
					'icon' => 'icon-times icon-spin'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->l('Status:'),
						'name' => 'status',
						'is_bool' => true,
						'values' => array(
							array(
								'value' => 1,
							),
							array(
								'value' => 0,
							)
						),
						'desc' => $this->l('Is this service active? (If set to "off" module will not generate csv for it)')
					),
					array(
						'type' => 'html',
						'name' => 'categories',
						'label' => $this->l('Categories:'),
						'html_content' => '
					<table cellspacing="0" cellpadding="0" class="table" style="width: 600px;">
						<tr>
							<th>
								<input type="checkbox" name="checkme" class="noborder" onclick="processCheckBoxes(this.checked)" />
							</th>
							<th>'.$this->l('ID').'</th>
							<th>'.$this->l('Name').'</th>
							<th>'.$this->l('Export Name').'</th>
						</tr>'.self::$categories_tree.'</table>'
					),
					array(
						'type' => 'html',
						'form_group_class' => '',
						'name' => 'autoupdate',
						'label' => $this->l('Auto-update'),
						'html_content' => '<div class="row" id="timeSelect"></div>',
						'desc' => '<span>'.$this->l('Select the time for this CSV to update automatically (cron must be enabled)').'</span>
                        <span id="whatSCron">'.$this->l('What is cron?').'</span>
                        <p id="wtfsCron" style="display: none;">
                            <span>'.$this->l('Cron is a job scheduler for Unix-based systems and it\'s a very handy tool, as you can schedule some routine tasks to run automatically, no matter if you or anyone else is present on your website: as long as the server hosting your site is running, cron will do it\'s job. To activate cron for this module, add the line below to your crontab file').'</span>
                            <code>* * * * * php -f '.str_replace('/controllers/admin', '', dirname(__FILE__)).DIRECTORY_SEPARATOR.'update.php</code>
                        </p>'
					),
					array(
						'type' => 'select',
						'label' => $this->l('Inactive products policy'),
						'name' => 'export_inactive',
						'options' => array(
							'id' => 'id',
							'name' => 'name',
							'query' => array(
								array(
									'id' => '-1',
									'name' => $this->l('Use default settings')
								),
								array(
									'id' => '1',
									'name' => $this->l('Export')
								),
								array(
									'id' => '0',
									'name' => $this->l('Do not Export')
								)
							)
						),
						'desc' => $this->l('Choose if you want to export inactive products for this service (overrides default settings).')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Export products in condition(s)'),
						'name' => 'condition',
						'options' => array(
							'id' => 'condition',
							'name' => 'name',
							'query' =>
								array_merge(array(array(
									'condition' => '-1',
									'name' => $this->l('Use default settings')
								)), $conditions)
						),
						'desc' => $this->l('Choose if you want to export inactive products for this service (overrides default settings).')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Language'),
						'name' => 'id_lang',
						'options' => array(
							'id' => 'id_lang',
							'name' => 'name',
							'query' =>
								array_merge(array(array(
									'id_lang' => '-1',
									'name' => $this->l('Use default settings')
								)), $languages)
						),
						'desc' => $this->l('In what language would you like products to be exported')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Customer Group'),
						'name' => 'id_group',
						'options' => array(
							'id' => 'id_group',
							'name' => 'name',
							'query' =>
								array_merge(array(array(
									'id_group' => '-1',
									'name' => $this->l('Use default settings')
								)), $customerGroups)
						),
						'desc' => $this->l('In what customer groups would you like products to be exported')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Delimiter'),
						'name' => 'delimiter',
						'desc' => $this->l('Specify field delimiter for this service. Leave blank to use default delimiter.')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Enclosure'),
						'name' => 'enclosure',
						'options' => array(
							'id' => 'id',
							'name' => 'name',
							'query' => array(
								array(
									'id' => '-1',
									'name' => $this->l('Use default settings')
								),
								array(
									'id' => '1',
									'name' => $this->l('Double Quote')
								),
								array(
									'id' => '2',
									'name' => $this->l('Single Quote')
								)
							)
						),
						'desc' => $this->l('Field enclosure for CSV data (eg.: "My Product" - double quotes here is an
						enclosure character)')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Generate Header'),
						'name' => 'header',
						'options' => array(
							'id' => 'id',
							'name' => 'name',
							'query' => array(
								array(
									'id' => '-1',
									'name' => $this->l('Use default settings')
								),
								array(
									'id' => '1',
									'name' => $this->l('Yes')
								),
								array(
									'id' => '0',
									'name' => $this->l('No')
								)
							)
						),
						'desc' => $this->l('Csv header is the first line that does not include product information, but
						contains field names, like "Product name", "Product link", etc.')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Country'),
						'name' => 'id_country',
						'options' => array(
							'id' => 'id_country',
							'name' => 'name',
							'query' =>
								array_merge(array(array(
									'id_country' => '-1',
									'name' => $this->l('Use default settings')
								)), $countries)
						),
						'desc' => $this->l('This setting overrides default zone selected in module settings')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Carrier'),
						'name' => 'id_carrier',
						'options' => array(
							'id' => 'id_carrier',
							'name' => 'name',
							'query' =>
								array_merge(array(array(
									'id_carrier' => '-1',
									'name' => $this->l('Use default settings')
								)), $carriers)
						),
						'desc' => $this->l('This setting overrides default carrier selected in module settings')
					),
					array(
						'form_group_class' => (count($list_engines) == 1) ? 'hide' : '',
						'type' => 'select',
						'label' => $this->l('Export engine'),
						'name' => 'export_engine',
						'options' => array(
							'id' => 'engine',
							'name' => 'name',
							'query' => $list_engines
						),
						'desc' => $this->l('An export engine to be used for this service.')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Export shop'),
						'name' => 'id_shop',
						'options' => array(
							'id' => 'id_shop',
							'name' => 'name',
							'query' => $shops
						),
						'desc' => $this->l('Export product from this shop.')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Export combinations'),
						'name' => 'combinations',
						'options' => array(
							'id' => 'id',
							'name' => 'name',
							'query' => array(
								array(
									'id' => '-1',
									'name' => $this->l('Use default settings')
								),
								array(
									'id' => '2',
									'name' => $this->l('Yes, export as separate products with ID of combination')
								),
								array(
									'id' => '1',
									'name' => $this->l('Yes, export as separate products')
								),
								array(
									'id' => '0',
									'name' => $this->l('No, do not export combinations')
								),
							)
						),
						'desc' => $this->l('If set to "on", each product combination will be exported as a separate product.')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Export combination name'),
						'name' => 'combination_name',
						'options' => array(
							'id' => 'id',
							'name' => 'name',
							'query' => array(
								array(
									'id' => '-1',
									'name' => $this->l('Use default settings')
								),
								array(
									'id' => '1',
									'name' => $this->l('Yes, add combination name in product name')
								),
								array(
									'id' => '0',
									'name' => $this->l('No, do not  add combination name in product name')
								),
							)
						),
						'desc' => $this->l('Please specify if attribute values should be exported as a part of a product name (e.g. iPod Nano (Color: blue, Disk space: 16 GB))Recomended, if atributes are not exported separately')
					),
				)
			)
		);


		$available_fields = '';
		foreach ($fields as $field => $fieldData)
		{
			$available_fields .= '
                        <li class="'.$field.' '.$fieldData['class'].'">
                            <span class="fieldLegendWrapper">
                                <i class="fieldLegend"></i>
                            </span>
                            <span class="fieldFancyName">'.$fieldData['name'].'</span>
                        </li>';
		}

		$target_fields = '';
		$template = Tools::getValue('template', Moussiq::decodeString($this->getFieldValue($obj, 'template')));
		//print_r($template);
		if (stristr($template, '\"'))
		{
			unset($template);
			// old type of templates
			//echo "pos = ".stristr($template, '\"').'===';
			$template = Tools::getValue('template', Tools::stripslashes(Moussiq::decodeString($this->getFieldValue($obj, 'template'))));
			//print_r($template);
			//echo '***1***';
			$template_str = stripcslashes(Tools::getValue('template', (Moussiq::decodeString($obj->template))));
		}
		else
		{
			unset($template);
			// new type of templates(supporting \n )
			$template = Tools::getValue('template', Moussiq::decodeString($this->getFieldValue($obj, 'template')));
			//print_r($template);
			//echo '---2---';
			$template_str = addcslashes((Tools::getValue('template', (Moussiq::decodeString($obj->template)))), '\\');
		}


		if ($template != '')
		{
			$template = Tools::jsonDecode($template);


			foreach ($template->fields as $field)
			{
				$fieldFancyName = Tools::stripslashes($field->field);

				if (isset($fields[Tools::stripslashes($field->field)]))
				{
					$fieldFancyName = $fields[Tools::stripslashes($field->field)]['name'];
					$fieldClass = $fields[Tools::stripslashes($field->field)]['class'];
				}

				$target_fields .= '
                        <li class="'.Tools::stripslashes($field->field).' '.$fieldClass.'">
							<span class="fieldLegendWrapper">
								<i class="fieldLegend"></i>
							</span>
                            <span class="fieldFancyName">'.$fieldFancyName.'</span>
                        </li>';
			}
		}

		$this->fields_form['availFields'] = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Fields'),
					'icon' => 'icon-map-marker'
				),
				'availfields' => '
					<fieldset class="fields availFields col-lg-6">
					<legend>
						<img src="../modules/'.$this->module->name.'/img/fields.png" />
						'.$this->l('Available fields').'
					</legend>
					<ul class="fields">'.$available_fields.'
					</ul>
					</fieldset>
					<fieldset class="fields targetFields col-lg-6">
						<legend>
							<img src="../modules/'.$this->module->name.'/img/add.png" />
							'.$this->l('Export fields').'
							<span class="purgeFields">'.$this->l('Remove all fields').'</span>
						</legend>
                <ul id="export">'.$target_fields.'
                </ul>
                <textarea name="template" id="template">'.$template_str.'</textarea>
            </fieldset>'
			)
		);

		$this->fields_form['availFields']['form']['submit'] = array(
			'title' => $this->l('Save'),
			'class' => 'btn btn-primary pull-right'
		);

		$this->fields_form['availFields']['form']['buttons']['save-and-stay'] = array(
			'title' => $this->l('Save and stay'),
			'name' => 'submitAdd'.$this->table.'AndStay',
			'type' => 'submit',
			'class' => 'btn btn-default pull-right',
			'icon' => 'process-icon-save'
		);

		if ($obj->id)
			$this->fields_form['availFields']['form']['buttons'][] = array(
				'type' => 'submit',
				'class' => 'pull-right',
				'name' => 'createTemplate',
				'icon' => 'process-icon-export',
				'title' => $this->l('Export this template'),
			);

		if (!($obj = $this->loadObject(true)))
			return;

		$script = '<script type="text/javascript">
            var fNameLabel            = "'.$this->l('Field Name').'";
            var fBeforeLabel          = "'.$this->l('Before Value').'";
            var fAfterLabel           = "'.$this->l('After Value').'";
            var fValueLabel           = "'.$this->l('Value').'";
            var fWithTaxLabel         = "'.$this->l('Apply Tax').'";
            var fWithShippingLabel    = "'.$this->l('Add Shipping Price').'";
            var fWithReductionLabel   = "'.$this->l('Include Price Reduction').'";
            var fUrlsOfAllPictures   = "'.$this->l('Urls of all pictures').'";
            var fLargePicture   	  = "'.$this->l('Large picture instead of original picture').'";
			var fHtmlTags   	      = "'.$this->l('Don\'t remove HTML tags').'";
            var FControlDelete        = "'.$this->l('Delete this Field').'";
            var FControlEdit          = "'.$this->l('Show Field\'s Settings').'";
            var FCloseSettings        = "'.$this->l('Hide this Field\'s settings').'";
            var fCodFee               = "'.$this->l('COD Fee').'";
            var tpDaysLabel           = "'.$this->l('Days').'";
            var tpHourLabel           = "'.$this->l('Hour').'";
            var tpMinuteLabel         = "'.$this->l('Minute').'";
			var showReduction         = '.($reductionAllowed ? 'true' : 'false').';
            var tpDaysNames = new Array(
		"'.$this->l('Monday').'",
		"'.$this->l('Tuesday').'",
		"'.$this->l('Wednesday').'",
		"'.$this->l('Thursday').'",
		"'.$this->l('Friday').'",
		"'.$this->l('Saturday').'",
		"'.$this->l('Sunday').'"
	);
            var pickedTime = "'.$obj->cron_schedule.'";
			function processCheckBoxes(currentValue) {
				$("input[name^=categoryBox]").attr("checked", currentValue);
			}
        </script>';

		$download_template = '';
		if ($template = Tools::getValue('tplFile', false))
		{
			$download_template = '
			<div class="alert alert-info">
				'.$this->l('You can now download your template file:').' <a href="'._MODULE_DIR_.'moussiq/download.php?key='.(md5(_COOKIE_KEY_)).'&template='.$template.'" class="btn btn-success"><i class="icon-download"></i> '.$template.'</a>
			</div>';
		}

		return $script.$download_template.parent::renderForm();
	}


	protected function createTemplateForService($id_service)
	{
		$currentIndex = $_SERVER['SCRIPT_NAME'].(($controller = Tools::getValue('controller')) ? '?controller='.$controller : '');

		$url = $currentIndex.'&token='.Tools::getAdminTokenLite('AdminMoussiq');

		$obj = new $this->className((int)$id_service);

		if (Validate::isLoadedObject($obj))
		{
			$tpl = '';
			$path = dirname(__FILE__).'/../../templates/';


			if ((!file_exists($path) || !is_dir($path)) && !mkdir($path, 0777))
			{
				$this->errors[] = $this->l('Please create "templates" directory in your moussique folder');
				return false;
			}
			elseif (!is_writable($path))
			{
				$this->errors[] = $this->l('Your "templates" directory is not writeable.');
				return false;
			}

			$exportFields = array('name', 'template', 'enclosure', 'delimiter', 'combination_name', 'combinations', 'header');

			foreach ($exportFields as $prop)
			{
				if (!property_exists($obj, $prop))
				{
					$this->errors[] = $this->l('Missing property:').' "'.$prop.'"';

					return false;
				}

				$tpl .= Moussiq::encodeString($prop).'|^|'.Moussiq::encodeString($obj->{$prop})."\n";
			}

			if (!count($this->errors) && trim($tpl) != '')
			{
				$handler = fopen($path.Export::transliterate(str_replace(' ', '_', $obj->name)).'.mtpl', 'w');

				if (!$handler)
				{
					$this->errors[] = $this->l('Could not create template file');

					return false;
				}

				fwrite($handler, $tpl);

				fclose($handler);
				Tools::redirectAdmin($url.'&id_moussiq_service='.$obj->id.'&updatemoussiq_service&tplFile='.Export::transliterate(str_replace(' ', '_', $obj->name)));
			}
			else
			{
				$this->errors[] = $this->l('Unable to select necessary template fields');

				return false;
			}
		}

		$this->errors[] = $this->l('Unable to load selected service');

		return false;
	}


	protected function createServiceFromTemplate($file)
	{
		if (empty($file['name']))
		{
			$this->errors[] = $this->l('Please select a file from your computer');

			return false;
		}

		$extension = strrchr($file['name'], '.');

		if ($extension == '.gz' && !function_exists('gzopen'))
		{
			$this->errors[] = $this->l('Your server does not support gz functions, please unpack the file locally and upload again');

			return false;
		}

		if ($extension == '.mtpl')
			$template = file($file['tmp_name'], FILE_SKIP_EMPTY_LINES);
		elseif ($extension == '.gz')
			$template = gzfile($file['tmp_name']);
		else
		{
			$this->errors[] = $this->l('Unknown file format');

			return false;
		}

		$defaultLanguage = (int)Configuration::get('PS_LANG_DEFAULT');
		$defaultCarrier = 0;
		$defaultCountry = (int)Configuration::get('PS_COUNTRY_DEFAULT');
		$defaultState = 0;
		$defaultCondition = 'new:used:refurbished';
		$engines = self::getExportEnginesForSetup();

		if (!count($engines))
		{
			$this->errors[] = $this->l('Please add export engines first');

			return false;
		}

		$engine = $engines[0]['engine'];

		if (Country::containsStates($defaultCountry))
		{
			$states = State::getStatesByIdCountry($defaultCountry);

			if (count($states))
				$defaultState = (int)$states[0]['id_state'];
		}

		$carriers = Carrier::getCarriers($defaultLanguage, true, false, false, null, Carrier::ALL_CARRIERS);

		if ($carriers && count($carriers))
			$defaultCarrier = (int)$carriers[0]['id_carrier'];

		$additionalProperties = array(
			'id_lang' => $defaultLanguage,
			'id_country' => $defaultCountry,
			'id_state' => $defaultState,
			'id_carrier' => $defaultCarrier,
			'export_engine' => $engine,
			'condition' => $defaultCondition
		);
		if (count($template) > 0)
		{
			$obj = new $this->className();

			foreach ($template as $line)
			{
				$line = explode('|^|', $line);
				$field = pSQL(Moussiq::decodeString($line[0]));

				if ($field == 'template')
					$line[1] = Moussiq::decodeString($line[1]);

				$value = Moussiq::decodeString($line[1]);

				if (property_exists($obj, $field))
					$obj->{$field} = trim($value);

				foreach ($additionalProperties as $prop => $val)
				{
					if (property_exists($obj, $prop))
						$obj->{$prop} = $val;
				}
			}

			if (!$obj->save())
			{
				$this->errors[] = $this->l('A service could not be added, an error occured during an attemt to copy file data into module');

				return false;
			}
		}
	}

	protected function getReadableFields($fields)
	{
		$titles = array(
			'id_product' => array(
				'name' => $this->l('Product ID', __CLASS__),
				'class' => 'prodRel'
			),
			'active' => array(
				'name' => $this->l('Product status', __CLASS__),
				'class' => 'prodRel'
			),
			'date_add' => array(
				'name' => $this->l('Addition date', __CLASS__),
				'class' => 'prodRel'
			),
			'date_upd' => array(
				'name' => $this->l('Update date', __CLASS__),
				'class' => 'prodRel'
			),
			'id_category_default' => array(
				'name' => $this->l('Default Category ID', __CLASS__),
				'class' => 'prodRel' //'catRel'
			),
			'id_manufacturer' => array(
				'name' => $this->l('Manufacturer ID', __CLASS__),
				'class' => 'prodRel' //'manRel'
			),
			'id_supplier' => array(
				'name' => $this->l('Supplier ID', __CLASS__),
				'class' => 'prodRel' //'supRel'
			),
			'id_tax' => array(
				'name' => $this->l('Tax ID', __CLASS__),
				'class' => 'prodRel' //'taxRel'
			),
			'description' => array(
				'name' => $this->l('Description', __CLASS__),
				'class' => 'prodRel'
			),
			'description_short' => array(
				'name' => $this->l('Short Description', __CLASS__),
				'class' => 'prodRel'
			),
			'meta_description' => array(
				'name' => $this->l('Meta Description', __CLASS__),
				'class' => 'prodRel' //'metaRel'
			),
			'meta_keywords' => array(
				'name' => $this->l('Meta Keywords', __CLASS__),
				'class' => 'prodRel' //'metaRel'
			),
			'meta_title' => array(
				'name' => $this->l('Meta Title', __CLASS__),
				'class' => 'prodRel' //'metaRel'
			),
			'quantity' => array(
				'name' => $this->l('Quantity in stock', __CLASS__),
				'class' => 'prodRel'
			),
			'name' => array(
				'name' => $this->l('Product\'s name', __CLASS__),
				'class' => 'prodRel'
			),
			'reference' => array(
				'name' => $this->l('Reference', __CLASS__),
				'class' => 'prodRel'
			),
			'reference_product' => array(
				'name' => $this->l('Reference of parent product', __CLASS__),
				'class' => 'prodRel'
			),
			'supplier_reference' => array(
				'name' => $this->l('Supplier\'s Reference', __CLASS__),
				'class' => 'prodRel'
			),
			'weight' => array(
				'name' => $this->l('Weight', __CLASS__),
				'class' => 'prodRel' //false
			),
			'wholesale_price' => array(
				'name' => $this->l('Wholesale Price', __CLASS__),
				'class' => 'prodRel' //'priceRel'
			),
			'price' => array(
				'name' => $this->l('Price', __CLASS__),
				'class' => 'prodRel' //'priceRel'
			),
			'ecotax' => array(
				'name' => $this->l('Ecotax', __CLASS__),
				'class' => 'prodRel' //false
			),
			'location' => array(
				'name' => $this->l('Location', __CLASS__),
				'class' => 'prodRel' //'prodRel'
			),
			'ean13' => array(
				'name' => $this->l('Ean13', __CLASS__),
				'class' => 'prodRel' //false
			),
			'reduction_from' => array(
				'name' => $this->l('Reduction from', __CLASS__),
				'class' => 'prodRel' //false
			),
			'reduction_to' => array(
				'name' => $this->l('Reduction to', __CLASS__),
				'class' => 'prodRel' //false
			),
			'reduction_percent' => array(
				'name' => $this->l('Reduction percent', __CLASS__),
				'class' => 'prodRel' //false
			),
			'reduction_price' => array(
				'name' => $this->l('Reduction price', __CLASS__),
				'class' => 'prodRel' //'priceRel'
			),
			'available_now' => array(
				'name' => $this->l('Available now', __CLASS__),
				'class' => 'prodRel' //false
			),
			'available_later' => array(
				'name' => $this->l('Available later', __CLASS__),
				'class' => 'prodRel' //false
			),
			'available_for_order' => array(
				'name' => $this->l('Available for order', __CLASS__),
				'class' => 'prodRel' //false
			),
			'additional_shipping_cost' => array(
				'name' => $this->l('Additional shipping cost', __CLASS__),
				'class' => 'prodRel' //false
			),
			'condition' => array(
				'name' => $this->l('Condition', __CLASS__),
				'class' => 'prodRel' //false
			),
			'height' => array(
				'name' => $this->l('Height', __CLASS__),
				'class' => 'prodRel' //false
			),
			'width' => array(
				'name' => $this->l('Width', __CLASS__),
				'class' => 'prodRel' //false
			),
			'depth' => array(
				'name' => $this->l('Depth', __CLASS__),
				'class' => 'prodRel' //false
			),
			'upc' => array(
				'name' => $this->l('UPC', __CLASS__),
				'class' => 'prodRel' //false
			),
			'show_price' => array(
				'name' => $this->l('Show price', __CLASS__),
				'class' => 'prodRel' //false
			),
			'unit_price_ratio' => array(
				'name' => $this->l('Unit Price ratio', __CLASS__),
				'class' => 'prodRel' //false
			),
			'online_only' => array(
				'name' => $this->l('Online Only', __CLASS__),
				'class' => 'prodRel' //false
			),
			'minimal_quantity' => array(
				'name' => $this->l('Minimal quantity', __CLASS__),
				'class' => 'prodRel' //false
			),
			'id_tax_rules_group' => array(
				'name' => $this->l('Tax Rule Group ID', __CLASS__),
				'class' => 'prodRel' //false
			),
			'unity' => array(
				'name' => $this->l('Unity', __CLASS__),
				'class' => 'prodRel' //false
			),
		);

		$result = array();

		foreach ($fields as $field)
			$result[$field] = isset($titles[$field]) ? $titles[$field] : array('name' => $field, 'class' => 'prodRel');

		$result['tax_rate'] = array(
			'name' => $this->l('Tax Rate', __CLASS__),
			'class' => 'prodRel' //'taxRel'
		);
		$result['total_tax'] = array(
			'name' => $this->l('Total Tax', __CLASS__),
			'class' => 'prodRel' //'taxRel'
		);
		$result['manufacturer_name'] = array(
			'name' => $this->l('Manufacturer', __CLASS__),
			'class' => 'prodRel' //'manRel'
		);
		$result['supplier_name'] = array(
			'name' => $this->l('Supplier', __CLASS__),
			'class' => 'prodRel' //'supRel'
		);
		$result['picture_link'] = array(
			'name' => $this->l('Picture url', __CLASS__),
			'class' => 'prodRel' //'prodRel'
		);
		$result['product_link'] = array(
			'name' => $this->l('Product url', __CLASS__),
			'class' => 'prodRel' //'prodRel'
		);
		$result['shipping_price'] = array(
			'name' => $this->l('Shipping Price', __CLASS__),
			'class' => 'prodRel' //'shippingRel'
		);
		$result['category_name'] = array(
			'name' => $this->l('Category Name', __CLASS__),
			'class' => 'prodRel' //'catRel'
		);
		$result['price_with_tax'] = array(
			'name' => $this->l('Price (tax incl)', __CLASS__),
			'class' => 'prodRel' //'priceRel'
		);

		uasort($result, array($this, 'cmpField'));

		//get group of attributes
		$attr_result = array();
		$attrGroups = AttributeGroup::getAttributesGroups($this->context->language->id);
		foreach ($attrGroups as $ag)
		{
			$attr_result['ag'.$ag['id_attribute_group']] = array(
				'name' => $ag['name'],
				'class' => 'attrRel'
			);
		}
		uasort($attr_result, array($this, 'cmpField'));
		$result = $result + $attr_result;

		//get features
		$feat_result = array();
		$features = Feature::getFeatures($this->context->language->id);
		foreach ($features as $ft)
		{
			$feat_result['ft'.$ft['id_feature']] = array(
				'name' => $ft['name'],
				'class' => 'featRel'
			);
		}
		uasort($feat_result, array($this, 'cmpField'));
		$result = $result + $feat_result;

		$result['empty_field'] = array(
			'name' => $this->l('Empty Field', __CLASS__),
			'class' => 'specialRel'
		);

		return $result;
	}

	public function cmpField($a, $b)
	{
		return strcasecmp($a['name'], $b['name']);
	}

	protected function getDbFieldNames($table)
	{
		if (!is_array($table))
		{
			if ($result = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.$table.'`'))
				return array_keys($result);

			return false;
		}

		$fields = array();

		foreach ($table as $tbl)
		{
			if ($result = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.$tbl.'`'))
				$fields = array_merge($fields, array_keys($result));
			else
				return false;
		}

		$fields = array_flip($fields);

		// We definitely don't need those
		// fields.
		unset(
			$fields['customizable'],
			$fields['id_color_default'],
			$fields['id_lang'],
			$fields['indexed'],
			$fields['text_fields'],
			$fields['uploadable_files'],
			$fields['link_rewrite'],
			$fields['on_sale'],
			$fields['out_of_stock'],
			$fields['quantity_discount'],
			$fields['cache_has_attachments'],
			$fields['cache_is_pack'],
			$fields['cache_default_attribute'],
			$fields['id_product_redirected']
		);

		// add virtual field
		$fields['reference_product'] = '';

		$fields = array_flip($fields);
		sort($fields);

		return $fields;
	}

	protected function getExportEngines()
	{
		require_once(dirname(__FILE__).'/../../classes/Export.php');

		$enginesDir = dirname(__FILE__).'/../../engines/';

		if (!is_dir($enginesDir))
			die(Tools::displayError('Export engines directory does not exist'));

		$scan = scandir($enginesDir);
		$preparedEngines = array();

		foreach ($scan as $exportEngine)
		{
			if (!in_array($exportEngine, array('.', '..')))
			{
				$tmp = explode('.', $exportEngine);
				if ((Tools::strtolower(array_pop($tmp)) == 'php') && (Tools::strtolower(Tools::substr($exportEngine, 0, 6)) == 'export')
					&& (is_object($exportEngineObj = Export::setExportEngine(Tools::substr($exportEngine, 0, -4), false))))

					$preparedEngines[Tools::substr($exportEngine, 0, -4)] = $exportEngineObj->name.' (*.'.$exportEngineObj->extension.')';
			}
		}

		if (!count($preparedEngines))
			die(Tools::displayError('No export engines were found'));

		return $preparedEngines;
	}

	protected function getExportEnginesForSetup()
	{
		$preparedEngines = array();
		if ($engines = $this->getExportEngines())
		{
			foreach ($engines as $engineFile => $engineName)
				$preparedEngines[] = array('engine' => $engineFile, 'name' => $engineName);

			return $preparedEngines;
		}
		return false;
	}

	private static function getSettingsValue($postVal, $objVal)
	{
		return (isset($postVal) ? ((int)($postVal) < 0 ? false : (int)($postVal)) : ($objVal == '') ? false : (int)($objVal));
	}

	private static function getSettingsStringValue($postVal, $objVal)
	{
		return (isset($postVal) ? ($postVal) : ($objVal == '') ? false : ($objVal));
	}

	public function renderImportTemplateForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Import'),
				'icon' => 'icon-cloud'
			),
			'input' => array(
				array(
					'type' => 'file',
					'label' => $this->l('Template file'),
					'name' => 'template',
					'required' => true,
					'desc' => $this->l('Please choose the file you recieved from Silbersaiten Mediengruppe')
				),
			),
			'submit' => array(
				'title' => $this->l('Add service'),
				'id' => 'submitImport',
				'icon' => 'process-icon-import',
				'name' => 'createServiceFromFile'
			)
		);
		return parent::renderForm();
	}

	public function initOptionsList()
	{
		$countries = Country::getCountries((int)($this->context->language->id));
		$carriers = Carrier::getCarriers((int)($this->context->language->id), true, false, false, null, Carrier::ALL_CARRIERS);
		$languages = Language::getLanguages();
		$conditions = array(
			array('condition' => 'new:used:refurbished', 'name' => $this->l('New').', '.$this->l('Used').', '.$this->l('Refurbished')),
			array('condition' => 'new', 'name' => $this->l('New')),
			array('condition' => 'used', 'name' => $this->l('Used')),
			array('condition' => 'refurbished', 'name' => $this->l('Refurbished')),
			array('condition' => 'new:used', 'name' => $this->l('New').', '.$this->l('Used')),
			array('condition' => 'new:refurbished', 'name' => $this->l('New').', '.$this->l('Refurbished')),
			array('condition' => 'used:refurbished', 'name' => $this->l('Used').', '.$this->l('Refurbished')),
		);

		$this->fields_options = array(
			'general' => array(
				'title' => $this->l('Export settings'),
				'fields' => array(
					'EXPORT_INACTIVE' => array(
						'title' => $this->l('Export inactive products'),
						'desc' => $this->l('Set to "No" to export active products only'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
					'EXPORT_CONDITION' => array(
						'title' => $this->l('Export products with condition'),
						'desc' => $this->l('Select conditions in which you want your products to be exported'),
						//'size'          => 3,
						'type' => 'select',
						'list' => $conditions,
						'identifier' => 'condition'
					),
					'EXPORT_LANGUAGE' => array(
						'title' => $this->l('Export language'),
						'desc' => $this->l('Select language in which you want your products to be exported'),
						'cast' => 'intval',
						//'size'          => 3,
						'type' => 'select',
						'list' => $languages,
						'identifier' => 'id_lang'
					),
					'EXPORT_GROUP' => array(
						'title' => $this->l('Customer group'),
						'desc' => $this->l('Export prices for selected customer group'),
						'cast' => 'intval',
						//'size'          => 3,
						'type' => 'select',
						'list' => Group::getGroups((int)$this->context->language->id),
						'identifier' => 'id_group'
					),
					'EXPORT_HEADER' => array(
						'title' => $this->l('CSV header'),
						'desc' => $this->l('This will add header with customizable field names to csv files generated by module'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
					'EXPORT_DELIMITER' => array(
						'title' => $this->l('Fields Delimiter'),
						'desc' => $this->l('Specify default delimiter for fields in exported csv files. You can set up different delimiters for different services or have them use the default.'),
						'size' => 3,
						'type' => 'text',
						'class' => 'fixed-width-xs'
					),
					'EXPORT_ENCLOSURE' => array(
						'title' => $this->l('Enclosure character'),
						'desc' => $this->l('Select an enclosure character for CSV data.'),
						'cast' => 'intval',
						//'size'          => 3,
						'type' => 'select',
						'list' => array(array('enclosure' => 1, 'name' => $this->l('Double Quotes')), array('enclosure' => 2, 'name' => $this->l('Single Quotes'))),
						'identifier' => 'enclosure'
					),
					'EXPORT_COMBINATIONS' => array(
						'title' => $this->l('Export combinations'),
						'desc' => $this->l('Choose whether or not this module should export product\'s combinations as separate products'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
					'EXPORT_COMBINATION_NAME' => array(
						'title' => $this->l('Export combination name'),
						'desc' => $this->l('Choose whether or not this module should export product\'s name with combination name'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
					'EXPORT_COUNTRY' => array(
						'title' => $this->l('Default country'),
						'desc' => $this->l('Set default country to export prices for. You can change it for each service individually'),
						'cast' => 'intval',
						//'size'          => 3,
						'type' => 'select',
						'list' => $countries,
						'identifier' => 'id_country'
					),
					'EXPORT_CARRIER' => array(
						'title' => $this->l('Default carrier'),
						'desc' => $this->l('Prices of this carrier will be exported as "delivery price" for each product'),
						'cast' => 'intval',
						//'size'          => 3,
						'type' => 'select',
						'list' => $carriers,
						'identifier' => 'id_carrier'
					),
					'EXPORT_ENGINE' => array(
						'form_group_class' => (count($this->module->getExportEnginesForSetup()) == 1) ? 'hide' : '',
						'title' => $this->l('Export Engine'),
						'desc' => $this->l('Default export engine'),
						//'size'          => 3,
						'type' => 'select',
						'list' => $this->module->getExportEnginesForSetup(),
						'identifier' => 'engine'
					)
				),
				'submit' => array('title' => $this->l('Save'))
			)
		);
	}

	public function renderOptions()
	{
		return parent::renderOptions();
	}

	public function postProcess()
	{
		if (Tools::getIsset('daysList_input') && Tools::getIsset('hoursList_input') && Tools::getIsset('minutesList_input'))
			$_POST['cron_schedule'] = str_replace(' ', '', Tools::getValue('minutesList_input', 0)).' '.str_replace(' ', '', Tools::getValue('hoursList_input', 0)).' * * '.str_replace(' ', '', Tools::getValue('daysList_input', 0));

		if (Tools::isSubmit('csvGen'))
		{
			$obj = $this->loadObject(true);
			require_once(dirname(__FILE__).'/../../classes/Export.php');

			if (!Export::checkDir(dirname(__FILE__).'/../../export/'))
				$this->errors[] = $this->l('Your export directory is not writeable');
			else if (is_object($exportEngine = Export::setExportEngine($obj->export_engine, $obj->id)))
				$exportEngine->startImport();
		}

		if (Tools::isSubmit('createServiceFromFile'))
		{
			$this->createServiceFromTemplate($_FILES['template']);
			return;
		}

		if (Tools::getIsset('createTemplate'))
			$this->createTemplateForService(Tools::getValue($this->identifier, false));

		if (Tools::getIsset('submitAdd'.$this->table) || Tools::getIsset('submitAdd'.$this->table.'AndStay'))
		{
			$rules = call_user_func(array($this->className, 'getValidationRules'), $this->className);
			//$defaultLanguage = new Language((int)(Configuration::get('PS_LANG_DEFAULT')));
			//$languages = Language::getLanguages(false);

			/* Check required fields */
			foreach ($rules['required'] as $field)
			{
				if ((($value = Tools::getValue($field)) == false) && ($value != '0'))
				{
					if ((Tools::getValue('id_'.$this->table)) && ($field == 'passwd'))
						continue;

					$this->errors[] = $this->l('the field').' <b>'.call_user_func(array($this->className, 'displayFieldName'), $field, $this->className).'</b> '.$this->l('is required');
				}
			}
			if (!count($this->errors))
			{
				$obj = new $this->className(Tools::getValue('id_moussiq_service', null));

				foreach ($_POST as $key => $value)
				{
					if (property_exists($obj, $key))
					{
						if (($key == 'template') && (get_magic_quotes_gpc() == true))
							$obj->{$key} = Tools::stripslashes($value);
						else
							$obj->{$key} = $value;
					}
				}

				if (!$obj->save())
					$this->errors[] = $this->l('Unable to save service');
				else
				{
					if (Tools::getIsset('submitAdd'.$this->table.'AndStay'))
						$url = self::$currentIndex.'&id_moussiq_service='.$obj->id.'&updatemoussiq_service&token='.Tools::getAdminTokenLite('AdminMoussiq');
					else
						$url = self::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminMoussiq');
					
					Tools::redirectAdmin($url);
				}
			}
		}

		return parent::postProcess();
	}
}
<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2015 PrestaShop SA
 *  @version	Release: $Revision: 17142 $
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_'))
	exit;

class Gadwordsfree extends Module
{

	public function __construct()
	{
		$this->name = 'gadwordsfree';
		$this->tab = 'advertising_marketing';
		$this->version = '1.3.6';
		$this->author = 'dh42';
		$this->need_instance = 1;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Google AdWords Conversion Tracking');
		$this->description = $this->l('Track your Google Adwords conversions easily');


	}

	public function install()
	{
		Configuration::updateValue('GADWORDS_CONVERSION_TRACKING_ID', null);
		Configuration::updateValue('GADWORDS_CONVERSION_TRACKING_LABEL', null);

		include(dirname(__FILE__).'/sql/install.php');

		return parent::install() &&
		$this->registerHook('header') &&
		$this->registerHook('backOfficeHeader') &&
		$this->registerHook('actionOrderDetail') &&
		$this->registerHook('actionPaymentConfirmation') &&
		$this->registerHook('displayOrderConfirmation');
	}

	public function uninstall()
	{
		Configuration::deleteByName('GADWORDS_LIVE_MODE');

		include(dirname(__FILE__).'/sql/uninstall.php');

		return parent::uninstall();
	}

	/**
	 * Load the configuration form
	 */
	public function getContent()
	{
		/**
		 * If values have been submitted in the form, process.
		 */
		if (((bool)Tools::isSubmit('submitGadwordsfreeModule')) == true) {
			$this->postProcess();
		}

		$this->context->smarty->assign(array(
			'module_dir' => $this->_path,
			'GADWORDS_CONVERSION_TRACKING_ID' => Configuration::get('GADWORDS_CONVERSION_TRACKING_ID'),
			'GADWORDS_CONVERSION_TRACKING_LABEL' => Configuration::get('GADWORDS_CONVERSION_TRACKING_LABEL')
		));

		$output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

		return $output.$this->renderForm();
	}

	/**
	 * Create the form that will be displayed in the configuration of your module.
	 */
	protected function renderForm()
	{
		$helper = new HelperForm();

		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitGadwordsfreeModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
			.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
		);

		return $helper->generateForm(array($this->getConfigForm()));
	}

	/**
	 * Create the structure of your form.
	 */
	protected function getConfigForm()
	{
		return array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs',
				),
				'input' => array(

					array(
						'col' => 4,
						'type' => 'text',
						'prefix' => '<i class="icon icon-key"></i>',
						'desc' => $this->l('Enter a valid email address'),
						'name' => 'GADWORDS_CONVERSION_TRACKING_ID',
						'label' => $this->l('Add your Google Conversion Tracking ID'),
					),
					array(
						'col' => 4,
						'type' => 'text',
						'prefix' => '<i class="icon icon-key"></i>',
						'name' => 'GADWORDS_CONVERSION_TRACKING_LABEL',
						'label' => $this->l('Add your Google Conversion Tracking Label'),
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				),
			),
		);
	}

	/**
	 * Set values for the inputs.
	 */
	protected function getConfigFormValues()
	{
		return array(
			'GADWORDS_CONVERSION_TRACKING_ID' => Configuration::get('GADWORDS_CONVERSION_TRACKING_ID'),
			'GADWORDS_CONVERSION_TRACKING_LABEL' => Configuration::get('GADWORDS_CONVERSION_TRACKING_LABEL'),
		);
	}

	/**
	 * Save form data.
	 */
	protected function postProcess()
	{
		$form_values = $this->getConfigFormValues();

		foreach (array_keys($form_values) as $key) {
			Configuration::updateValue($key, Tools::getValue($key));
		}
	}

	/**
	 * Add the CSS & JavaScript files you want to be loaded in the BO.
	 */
	public function hookBackOfficeHeader()
	{
		if (Tools::getValue('module_name') == $this->name) {
//			$this->context->controller->addJS($this->_path.'views/js/back.js');
//			$this->context->controller->addCSS($this->_path.'views/css/back.css');
		}
	}

	/**
	 * Add the CSS & JavaScript files you want to be added on the FO.
	 */
	public function hookHeader()
	{
		/*$this->context->controller->addJS($this->_path.'/views/js/front.js');
		$this->context->controller->addCSS($this->_path.'/views/css/front.css');*/
	}

	public function hookActionOrderDetail()
	{
		/* Place your code here. */
	}


	public function hookDisplayOrderConfirmation()
	{
		$order = new Order(Tools::getValue('id_order'));
		$currency = new Currency($order->id_currency);

		$this->context->smarty->assign(
			array(
				'GADWORDS_CONVERSION_TRACKING_ID' => Configuration::get('GADWORDS_CONVERSION_TRACKING_ID'),
				'GADWORDS_CONVERSION_TRACKING_LABEL' => Configuration::get('GADWORDS_CONVERSION_TRACKING_LABEL'),
				'CURRENCY' => $currency->iso_code,
				'TOTAL_ORDER' => $order->getOrdersTotalPaid(),
				'LANG' => $this->context->language->iso_code
			)
		);

		return $this->display(__FILE__, 'gadwords.tpl');
	}
}
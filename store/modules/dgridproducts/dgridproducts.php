<?php
/**
 * 2007-2016 PrestaShop
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
 * @author    Goryachev Dmitry    <dariusakafest@gmail.com>
 * @copyright 2007-2016 Goryachev Dmitry
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_CORE_DIR_'))
	define('_PS_CORE_DIR_', _PS_ROOT_DIR_);

class DGridProducts extends Module
{
	public function __construct()
	{
		$this->name = 'dgridproducts';
		$this->tab = 'front_office_features';
		$this->version = '2.6.35';
		$this->bootstrap = 1;
		$this->author = 'SeoSA';
		$this->need_instance = '0';
		parent::__construct();
		$this->displayName = $this->l('Quick edit products');
		$this->description = $this->l('Edit information about products');
		$this->module_key = '8b66a93d04fa5f3b47d6e9df6709c156';
	}
	public function install()
	{
		$this->installTab();
		return parent::install() && $this->registerHook('displayBackOfficeHeader');
	}
	public function uninstall()
	{
		$this->uninstallTab();
		return parent::uninstall();
	}
	public function installTab()
	{
		$languages = Language::getLanguages(false);
		$tab = new Tab();
		$name = array();
		foreach ($languages as $lang)
			$name[$lang['id_lang']] = $this->l('Grid Products');
		$tab->name = $name;
		$tab->module = $this->name;
		$tab->id_parent = Tab::getIdFromClassName('AdminCatalog');
		$tab->class_name = 'AdminProductGrid';
		$tab->save();
	}
	public function uninstallTab()
	{
		$tab = Tab::getInstanceFromClassName('AdminProductGrid');
		$tab->delete();
	}
	public function hookDisplayBackOfficeHeader()
	{
		if (Tools::getValue('controller') == 'AdminProductGrid' || Tools::getValue('controller') == 'adminproductgrid')
		{
			$attribute_groups = AttributeGroup::getAttributesGroups($this->context->language->id);
			foreach ($attribute_groups as &$attribute_group)
				$attribute_group['attributes'] = AttributeGroup::getAttributes($this->context->language->id, $attribute_group['id_attribute_group']);
			$this->context->smarty->assign(array(
				'attribute_groups' => $attribute_groups
			));
			return $this->display(__FILE__, 'backoffice_header.tpl');
		}
	}
} 
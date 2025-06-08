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

class MoussiqService extends ObjectModel
{
	public $id;
	public $name;
	public $id_lang;
	public $id_group;
	public $id_shop;
	public $id_country;
	public $id_state;
	public $condition;
	public $id_carrier;
	public $categories;
	public $export_inactive;
	public $delimiter;
	public $enclosure;
	public $combinations;
	public $combination_name;
	public $header;
	public $cron_schedule;
	public $export_engine;
	public $last_upd;
	public $template;
	public $status = true;
	protected $fieldsRequired = array('name', 'template', 'export_engine');
	protected $fieldsSize = array('name' => 128);
	protected $fieldsValidate = array('name' => 'isGenericName');
	protected $table = 'moussiq_service';
	protected $identifier = 'id_moussiq_service';

	public function getFields()
	{
		parent::validateFields();

		$fields = array();
		if ($this->id)
			$fields[$this->identifier] = (int)$this->id;

		$fields['name'] = pSQL($this->name);
		$fields['template'] = Moussiq::encodeString($this->template);
		$fields['id_lang'] = $this->getIdOrNull('id_lang');
		$fields['id_group'] = $this->getIdOrNull('id_group');
		$fields['id_shop'] = $this->getIdOrNull('id_shop');
		$fields['id_country'] = $this->getIdOrNull('id_country');
		$fields['id_state'] = $this->getIdOrNull('id_state');
		$fields['condition'] = pSQL($this->condition);
		$fields['id_carrier'] = $this->getIdOrNull('id_carrier');
		$fields['header'] = $this->getIdOrNull('header');
		$fields['enclosure'] = $this->getIdOrNull('enclosure');
		$fields['combinations'] = $this->getIdOrNull('combinations');
		$fields['combination_name'] = $this->getIdOrNull('combination_name');
		$fields['export_inactive'] = $this->getIdOrNull('export_inactive');
		$fields['delimiter'] = pSQL($this->delimiter);
		$fields['cron_schedule'] = isset($this->cron_schedule) ? pSQL($this->cron_schedule) : null;
		$fields['export_engine'] = pSQL($this->export_engine);
		$fields['last_upd'] = isset($this->last_upd) ? pSQL($this->last_upd) : time();
		$fields['status'] = (int)$this->status;
		//print_r($fields); exit;
		return $fields;
	}

	private function getState()
	{
		$result = null;

		if ((int)$this->state == 0)
			return $result;

		if (Country::containsStates((int)$this->id_country))
		{
			$state = new State((int)$this->id_state);

			if (!Validate::isLoadedObject($state))
				$result = null;
			elseif (!$state->id_country == (int)$this->id_country)
				$result = null;
			else
				$result = (int)$state->id;
		}

		return $result;
	}

	public function updateCategories()
	{
		$categories = Tools::getValue('categoryBox', false);

		$prepared_categories = array();

		if ($categories)
			foreach ($categories as $category)
				if (array_key_exists('id_category', $category) && array_key_exists('name', $category))
					$prepared_categories[] = $category;

		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'moussiq_service_categories` WHERE `id_moussiq_service` = '.(int)$this->id);

		if (count($prepared_categories))
		{
			foreach ($prepared_categories as $category)
			{
				Db::getInstance()->Execute('
					INSERT INTO `'._DB_PREFIX_.'moussiq_service_categories`
					(`id_moussiq_service`, `id_category`, `name`) VALUES
					('.(int)$this->id.', '.(int)$category['id_category'].', '.(trim($category['name']) != '' ? '"'.pSQL($category['name']).'"' : 'NULL').')');
			}
		}
	}

	public static function getCategories($service_id)
	{
		$categories = array();

		if (Validate::isUnsignedId($service_id))
		{
			$query = Db::getInstance()->ExecuteS('SELECT `id_category`, `name` FROM `'.
				_DB_PREFIX_.'moussiq_service_categories` WHERE `id_moussiq_service` = '.(int)$service_id);

			if ($query && count($query))
				foreach ($query as $category)
					$categories[$category['id_category']] = $category;
		}

		return $categories;
	}

	private function getIdOrNull($field)
	{
		return (isset($this->{$field}) && (int)$this->{$field} >= -1) ? (int)$this->{$field} : null;
	}

	public function save($null_values = true, $autodate = true)
	{
		return parent::save($null_values, $autodate);
	}

	public function update($null_values = true)
	{
		if (parent::update($null_values))
		{
			$this->updateCategories();
			require_once(dirname(__FILE__).'/Export.php');

			if (is_object($engine = Export::setExportEngine($this->name, $this->id)))
				$engine->startImport();

			return true;
		}

		return false;
	}


	public function delete()
	{
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'moussiq_service_categories` WHERE `id_moussiq_service` = '.(int)$this->id);

		return parent::delete();
	}


	public function add($autodate = false, $null_values = true)
	{
		if (parent::add($autodate, $null_values))
		{
			$this->updateCategories();
			return true;
		}

		return false;
	}

	public function toggleStatus()
	{
		if (!Validate::isUnsignedId($this->id))
			die(Tools::displayError());

		return (Db::getInstance()->Execute('
        UPDATE `'._DB_PREFIX_.$this->table.'`
        SET `status` = !`status`
        WHERE `'.$this->identifier.'` = '.(int)$this->id));
	}
}
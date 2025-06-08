<?php
/**
 * Moussiq PRO
 *
 * @category  Module
 * @author    silbersaiten <info@silbersaiten.de>
 * @support   silbersaiten <support@silbersaiten.de>
 * @copyright 2014 silbersaiten
 * @version   2.4.0
 * @link      http://www.silbersaiten.de
 * @license   See joined file licence.txt
 */

require_once(dirname(__FILE__).'/classes/MoussiqService.php');

class Moussiq extends Module
{
	private $_html = '';
	private $_postErrors = array();
	private static $_tblCache = array();
	private static $_miscTranslations = array();

	public function __construct()
	{
		$this->name = 'moussiq';
		$this->author = 'silbersaiten';
		$this->tab = 'export';
		$this->version = '2.4.2';
		$this->module_key = '165500ec94ed911a9e584c098e0ebe0b';

		parent::__construct();

		$this->displayName = $this->l('Moussiq PRO');
		$this->description = $this->l('Exports your products into a csv file for various price comparison engines');

		self::$_miscTranslations = array(
			'new' => $this->l('New'),
			'used' => $this->l('Used'),
			'refurbished' => $this->l('Refurbished')
		);
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('backOfficeHeader'))
			return false;

		$queries = array(
			'CREATE TABLE `'._DB_PREFIX_.'moussiq_service`
            (
                `id_moussiq_service` INT(10) unsigned NOT NULL AUTO_INCREMENT  ,
                `name`               VARCHAR(128) NOT NULL                     ,
                `id_lang`            INT(10)      DEFAULT NULL                 ,
                `id_group`           INT(10)      DEFAULT NULL                 ,
                `id_store`           INT(10)      DEFAULT NULL                 ,
                `id_shop`            INT(10)      DEFAULT NULL                 ,
                `id_country`         INT(10)      DEFAULT NULL                 ,
                `id_state`           INT(10)      DEFAULT NULL                 ,
                `condition`          VARCHAR(255) DEFAULT NULL                 ,
                `id_carrier`         INT(10)      DEFAULT NULL                 ,
                `export_inactive`    TINYINT(1)   DEFAULT NULL                 ,
                `delimiter`          VARCHAR(3)   DEFAULT NULL                 ,
                `enclosure`          TINYINT(1)   DEFAULT NULL                 ,
                `combinations`       TINYINT(1)   DEFAULT NULL                 ,
                `combination_name`   TINYINT(1)   DEFAULT NULL                 ,
                `header`             TINYINT(1)   DEFAULT NULL                 ,
                `cron_schedule`      VARCHAR(255) DEFAULT NULL                 ,
                `export_engine`      VARCHAR(255) DEFAULT NULL                 ,
                `last_upd`           INT(11)      NOT NULL                     ,
                `template`           TEXT                                      ,
                `status`             TINYINT(1) unsigned NOT NULL DEFAULT "0"  ,
                PRIMARY KEY ( `id_moussiq_service` )
            )
            ENGINE=MyISAM DEFAULT CHARSET=utf8',

			'CREATE TABLE `'._DB_PREFIX_.'moussiq_service_categories` (
              `id_moussiq_service`   INT(10)      unsigned NOT NULL    ,
              `id_category`          INT(10)      unsigned NOT NULL    ,
              `name`                 VARCHAR(255)          DEFAULT NULL,
              KEY `moussiq_category_index` (`id_moussiq_service`, `id_category`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
		);

		foreach ($queries as $query)
		{
			if (!Db::getInstance()->Execute($query))
			{
				$this->uninstall();
				return false;
			}
		}

		$carriers = Carrier::getCarriers((int)(Configuration::get('PS_LANG_DEFAULT')), true, false, false, null, Carrier::ALL_CARRIERS);

		if (count($carriers) == 0)
			return false;

		$defaultCountry = (int)(Configuration::get('PS_COUNTRY_DEFAULT'));
		$defaultState = 0;

		if (Country::containsStates($defaultCountry))
		{
			$states = State::getStatesByIdCountry($defaultCountry);

			if ($states && count($states))
				$defaultState = $states[0]['id_state'];
		}
		$defaultCondition = 'new:used:refurbished';

		Configuration::updateValue('EXPORT_LANGUAGE', (int)(Configuration::get('PS_LANG_DEFAULT')));
		Configuration::updateValue('EXPORT_DELIMITER', ',');
		Configuration::updateValue('EXPORT_COMBINATIONS', 1);
		Configuration::updateValue('EXPORT_COMBINATION_NAME', 1);
		Configuration::updateValue('EXPORT_ENCLOSURE', 1);
		Configuration::updateValue('EXPORT_HEADER', 1);
		Configuration::updateValue('EXPORT_COUNTRY', (int)$defaultCountry);
		Configuration::updateValue('EXPORT_STATE', (int)$defaultState);
		Configuration::updateValue('EXPORT_CONDITION', $defaultCondition);
		Configuration::updateValue('EXPORT_CARRIER', (int)(Configuration::get('PS_CARRIER_DEFAULT')));
		Configuration::updateValue('EXPORT_SHOP', (int)Db::getInstance()->getValue('SELECT `id_shop` FROM `'._DB_PREFIX_.'shop` ORDER BY `id_shop` ASC'));
		Configuration::updateValue('EXPORT_INACTIVE', 0);
		Configuration::updateValue('EXPORT_GROUP', (int)Db::getInstance()->getValue('SELECT `id_group` FROM `'._DB_PREFIX_.'group` ORDER BY `id_group` ASC'));

		$this->createPredefServices();

		return $this->installModuleTab('AdminMoussiq', 'Moussiq');
	}

	public function createPredefServices($dir = 'templates_predef')
	{
		// create predefined services from templates_predef directory
		$cdir = dirname(__FILE__);
		$sdir = dir($cdir.'/'.$dir);
		while (($item = $sdir->read()) !== false)
		{
			if (!is_dir($cdir.'/'.$dir.'/'.$item))
			{
				$file = $cdir.'/'.$dir.'/'.$item;
				$extension = strrchr($file, '.');
				if ($extension == '.mtpl')
				{
					$template = file($file, FILE_SKIP_EMPTY_LINES);

					$defaultLanguage = (int)Configuration::get('PS_LANG_DEFAULT');
					$defaultCarrier = 0;
					$defaultCountry = (int)Configuration::get('PS_COUNTRY_DEFAULT');
					$defaultState = 0;
					$defaultCondition = 'new:used:refurbished';
					$defaultGroup = (int)Db::getInstance()->getValue('SELECT `id_group` FROM `'._DB_PREFIX_.'group`');

					$engine = 'ExportCSV';

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
						'id_group' => $defaultGroup,
						'export_engine' => $engine,
						'condition' => $defaultCondition
					);
					if (count($template) > 0)
					{
						$obj = new MoussiqService();

						foreach ($template as $line)
						{
							$line = explode('|^|', $line);
							$field = pSQL(self::decodeString($line[0]));

							if ($field == 'template')
								$line[1] = self::decodeString($line[1]);

							$value = self::decodeString($line[1]);

							if (property_exists($obj, $field))
								$obj->{$field} = trim($value);

							foreach ($additionalProperties as $prop => $val)
							{
								if (property_exists($obj, $prop))
									$obj->{$prop} = $val;
							}
						}

						if (!$obj->save())
							return false;
					}
				}
			}
		}
	}

	public static function decodeString($str)
	{
		return $str;
		//return base64_decode($str);
		//return unserialize($str);
	}

	public static function encodeString($str)
	{
		return $str;
		//return base64_encode($str);
		//return serialize($str);
	}

	public function uninstall()
	{
		$sql = '
		SELECT `id_tab` FROM `'._DB_PREFIX_.'tab` WHERE `module` = "'.pSQL($this->name).'"';

		$result = Db::getInstance()->ExecuteS($sql);

		if ($result && count($result))
		{
			foreach ($result as $tabData)
			{
				$tab = new Tab($tabData['id_tab']);

				if (Validate::isLoadedObject($tab))
					$tab->delete();
			}
		}

		if (self::tableExists(_DB_PREFIX_.'moussiq_service'))
			Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'moussiq_service`');

		if (self::tableExists(_DB_PREFIX_.'moussiq_service_categories'))
			Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'moussiq_service_categories`');

		Configuration::deleteByName('EXPORT_LANGUAGE');
		Configuration::deleteByName('EXPORT_DELIMITER');
		Configuration::deleteByName('EXPORT_ENCLOSURE');
		Configuration::deleteByName('EXPORT_COMBINATIONS');
		Configuration::deleteByName('EXPORT_COMBINATION_NAME');
		Configuration::deleteByName('EXPORT_HEADER');
		Configuration::deleteByName('EXPORT_COUNTRY');
		Configuration::deleteByName('EXPORT_STATE');
		Configuration::deleteByName('EXPORT_CONDITION');
		Configuration::deleteByName('EXPORT_STORE');
		Configuration::deleteByName('EXPORT_CARRIER');
		Configuration::deleteByName('EXPORT_INACTIVE');

		return parent::uninstall();
	}

	/*
	 * Checks if table exists in the database
	 *
	 * @access private
	 * @scope static
	 * @param string $table    - Table name to check
	 *
	 * @return boolean
	 */
	public static function tableExists($table, $useCache = true)
	{
		if (!count(self::$_tblCache) || !$useCache)
		{
			$tmp = Db::getInstance()->ExecuteS('SHOW TABLES');

			foreach ($tmp as $entry)
			{
				reset($entry);

				$tableTmp = Tools::strtolower($entry[key($entry)]);

				if (!array_search($tableTmp, self::$_tblCache))
					self::$_tblCache[] = $tableTmp;
			}
		}

		return array_search(Tools::strtolower($table), self::$_tblCache) ? true : false;
	}

	/*
	 * Copies Moussiq logo to img/t, so that it would display in the backoffice
	 * like other tabs do.
	 *
	 * @access private
	 * @param string $class    - Class name, like "AdminCatalog"
	 *
	 * @return boolean
	 */
	private function copyLogo($class)
	{
		return @copy(dirname(__FILE__).'/logo.gif', _PS_IMG_DIR_.'t/'.$class.'.gif');
	}


	/*
	 * Creates a "subtab" in "Catalog" tab.
	 *
	 * @access private
	 * @param string $class    - Class name, like "AdminCatalog"
	 * @param string $name     - Tab title
	 *
	 * @return boolean
	 */
	private function installModuleTab($class, $name)
	{
		$sql = '
		SELECT `id_tab` FROM `'._DB_PREFIX_.'tab` WHERE `class_name` = "AdminCatalog"';

		$tabParent = (int)(Db::getInstance()->getValue($sql));

		if (!is_array($name))
			$name = self::getMultilangField($name);

		if (self::fileExistsInModulesDir('logo.gif') && is_writeable(_PS_IMG_DIR_.'t/'))
			$this->copyLogo($class);

		$tab = new Tab();
		$tab->name = $name;
		$tab->class_name = $class;
		$tab->module = $this->name;
		$tab->id_parent = $tabParent;

		return $tab->save();
	}

	/*
	 * Turns a string into an array with language IDs as keys. This array can
	 * be used to create multilingual fields for prestashop
	 *
	 * @access private
	 * @scope static
	 * @param mixed $field    - A field to turn into multilingual
	 *
	 * @return array
	 */
	private static function getMultilangField($field)
	{
		$languages = Language::getLanguages();
		$res = array();

		foreach ($languages as $lang)
			$res[$lang['id_lang']] = $field;

		return $res;
	}

	/*
	 * Tests if a file exists in /modules/moussiq
	 *
	 * @access private
	 * @scope static
	 * @param string $file    - A file to look for
	 *
	 * @return array
	 */
	private static function fileExistsInModulesDir($file)
	{
		return file_exists(dirname(__FILE__).'/'.$file);
	}


	public static function getTranslation($string)
	{
		return array_key_exists($string, self::$_miscTranslations) ? self::$_miscTranslations[$string] : $string;
	}

	public function hookBackOfficeHeader($params)
	{
		if ((Tools::getIsset('tab') && Tools::getValue('tab') == 'AdminMoussiq')
			|| (Tools::getIsset('controller') && (Tools::getValue('controller') == 'adminmoussiq'
				|| Tools::getValue('controller') == 'AdminMoussiq')))
		{
			return '
            <script type="text/javascript">
                var labelStateSelect = "'.$this->l('State').'",
                    stateDefault = '.(int)Configuration::get('EXPORT_STATE').';
            </script>
            <script type="text/javascript" src="'._MODULE_DIR_.'/'.$this->name.'/js/admin.js"></script>';
		}
	}

	public function getContent()
	{
		$tab = 'AdminMoussiq';
		Tools::redirectAdmin('index.php?tab='.$tab.'&token='.Tools::getAdminTokenLite($tab));
	}

	public function _outputErrors()
	{
		if (count($this->_postErrors))
			foreach ($this->_postErrors as $error)
				echo $this->displayError($error);
	}

	public function getExportEngines()
	{
		require_once(dirname(__FILE__).'/classes/Export.php');

		$enginesDir = dirname(__FILE__).'/engines/';

		if (!is_dir($enginesDir))
			die(Tools::displayError('Export engines directory does not exist'));

		$scan = scandir($enginesDir);
		$preparedEngines = array();

		foreach ($scan as $exportEngine)
		{
			if (!in_array($exportEngine, array('.', '..')))
			{
				$tmp = explode('.', $exportEngine);

				if (Tools::strtolower(array_pop($tmp)) == 'php'
					&& Tools::strtolower(Tools::substr($exportEngine, 0, 6)) == 'export'
					&& is_object($exportEngineObj = Export::setExportEngine(Tools::substr($exportEngine, 0, -4), false)))

					$preparedEngines[Tools::substr($exportEngine, 0, -4)] = $exportEngineObj->name.' (*.'.$exportEngineObj->extension.')';
			}
		}

		if (!count($preparedEngines))
			die(Tools::displayError('No export engines were found'));

		return $preparedEngines;
	}

	public function getExportEnginesForSetup()
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
}
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

abstract class Export
{
	public $service_id;
	public $name;
	public $extension;
	protected $filename;
	protected $exp_header;
	protected static $exp_service_data;
	protected static $exp_billing_mode;
	protected static $exp_shipping_carrier_data;
	protected static $exp_specific_prices;
	protected static $exp_product_priorities;

	protected static $exp_product_categories;
	protected static $exp_export_categories;
	protected static $exp_export_start_time;
	protected static $exp_existing_pictures;
	protected static $exp_existing_categories;
	protected static $exp_existing_combinations;
	protected static $exp_tax_rates;
	protected static $exp_export_country;
	protected static $exp_export_currency;
	protected static $exp_export_state;
	protected static $exp_export_condition;
	protected static $exp_export_group;
	protected static $exp_export_shop;
	protected static $exp_export_language;
	protected static $exp_carrier_id;
	protected static $exp_carrier_tax;
	protected static $exp_carrier_method;
	protected static $exp_free_price_shipping_requirements;
	protected static $exp_free_weight_shipping_requirements;
	protected static $exp_zone;
	protected static $exp_sef;
	private static $exp_export_languageObj;

	protected static $_link;

	protected $exp_file_contents;


	public function __construct($id_service, $filename = false)
	{
		$this->name = Tools::isEmpty($this->name) ? 'Unknown Export Engine' : $this->name;

		if (Validate::isUnsignedId($id_service))
		{
			$this->service_id = (int)$id_service;

			if (!$filename)
				$filename = sha1($this->getServiceName()._COOKIE_KEY_);

			$this->filename = _PS_ROOT_DIR_.'/modules/moussiq/export/'.$filename.'.'.$this->extension;
			self::$_link = new Link();
			self::$exp_service_data = self::getCsvDefaults((int)$id_service);
		}
	}

	private function getServiceName()
	{
		return Db::getInstance()->getValue('SELECT `name` FROM `'._DB_PREFIX_.'moussiq_service` WHERE `id_moussiq_service` = '.$this->service_id);
	}

	public static function setExportEngine($engine_name, $service_id)
	{
		$engine_path = _PS_ROOT_DIR_.'/modules/moussiq/engines/'.$engine_name.'.php';

		if (!file_exists($engine_path))
			return false;

		require_once($engine_path);

		if (class_exists($engine_name, false))
			return new $engine_name($service_id);

		return false;
	}

	public function getCurrencyByCountry($id_country)
	{
		return Db::getInstance()->getValue('SELECT `id_currency` FROM `'._DB_PREFIX_.'country` WHERE `id_country` = '.$id_country);
	}


	public function startImport()
	{
		ini_set('max_execution_time', 0);
		set_time_limit(0);
		ini_set('memory_limit', '1024M');

		self::$exp_export_start_time = time();

		$this->exp_header = self::$exp_service_data['header'] == 1;

		self::$exp_sef = (int)Configuration::get('PS_REWRITING_SETTINGS');
		self::$exp_export_shop = (int)self::$exp_service_data['id_shop'];
		self::$exp_export_group = (int)self::$exp_service_data['id_group'];
		self::$exp_export_language = (int)self::$exp_service_data['id_lang'];
		self::$exp_carrier_id = (int)self::$exp_service_data['id_carrier'];
		self::$exp_carrier_tax = self::getShippingTax(self::$exp_carrier_id, self::$exp_export_shop);
		$carrier = new Carrier((int)self::$exp_carrier_id);
		self::$exp_carrier_method = $carrier->getShippingMethod();
		self::$exp_free_price_shipping_requirements = self::getFreeShippingRequirements(0);
		self::$exp_free_weight_shipping_requirements = self::getFreeShippingRequirements(1);
		self::$exp_export_country = (int)self::$exp_service_data['id_country'];
		self::$exp_export_currency = (int)self::getCurrencyByCountry(self::$exp_export_country);
		self::$exp_export_state = (int)self::$exp_service_data['id_state'];
		self::$exp_export_condition = self::$exp_service_data['condition'];
		self::$exp_zone = Country::getIdZone(self::$exp_export_country);
		self::$exp_billing_mode = (int)Configuration::get('PS_SHIPPING_METHOD');
		self::$exp_shipping_carrier_data = self::getCarrierShippingRanges();
		//self::$exp_existing_pictures = self::getExistingPictures();
		self::$exp_existing_categories = self::getExistingCategories(self::$exp_export_language);
		self::$exp_specific_prices = self::getSpecificPrices(self::$exp_export_shop);
		self::$exp_product_priorities = self::getProductsPriorities();
		self::$exp_tax_rates = self::getTaxes();
		self::$exp_export_categories = MoussiqService::getCategories($this->service_id);
		self::$exp_product_categories = self::getProductCategories();
		self::$exp_export_languageObj = new Language(self::$exp_export_language);

		if (self::$exp_export_currency == 0)
			self::$exp_export_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');

		if (count(self::$exp_product_categories))
		{
			$chunk_size = 3000;
			$current_size = 0;
			$this->beforeImport(self::$exp_service_data['template'], array());
			do
			{
				$products = self::getProducts(self::$exp_export_language, $current_size, $chunk_size, 'date_add',
					'ASC', implode(array_keys(self::$exp_export_categories), ','), self::$exp_service_data['export_inactive'] == true ? false : true,
					self::$exp_export_country, self::$exp_export_shop, self::$exp_export_condition);
				$current_size += $chunk_size;

				$file_dir = dirname(__FILE__).'/../export/';

				if (!self::checkDir($file_dir))
				{
					$this->_errors[] = Tools::displayError('The directory is not writeable');

					return false;
				}

				foreach ($products as $product)
				{
					if (array_key_exists($product['id_product'], self::$exp_product_categories))
					{
						$product['id_product_orig'] = $product['id_product'];
						$product['id_product_attribute'] = 0;

						$product['categories'] = self::$exp_product_categories[$product['id_product']];
						$product['reduction'] = self::getProductSpecificPrice($product['id_product'], 0,
							self::$exp_export_shop, Configuration::get('PS_CURRENCY_DEFAULT'), self::$exp_export_country,
							self::$exp_export_group);

						$product['quantity'] = (int)StockAvailable::getQuantityAvailableByProduct($product['id_product'], null, self::$exp_export_shop);

						//to fix, get cover id_image
						$product['id_image'] = self::getProductCoverWs($product['id_product']);
						$product['id_product_image'] = $product['id_product'];

						$features = self::collectFeatures(self::$exp_export_language, $product['id_product']);
						if (is_array($features))
						{
							foreach ($features as $id_feature => $feature)
								$product['ft'.$id_feature] = trim($feature);
						}

						//FIXED: out of memory
						if (self::$exp_service_data['combinations'])
							self::$exp_existing_combinations = self::collectCombinations(self::$exp_export_language,
								$product['id_product'], self::$exp_export_shop);
						if (is_array(self::$exp_existing_combinations) && isset(self::$exp_existing_combinations[$product['id_product']]))
						{
							$product_combinations = self::$exp_existing_combinations[$product['id_product']];

							$name = $product['name'];
							$id_product = $product['id_product'];

							$product['id_product_orig'] = $id_product;
							$product['reference_product'] = $product['reference'];
							$product['id_product_attribute'] = 0;

							$product_information = Supplier::getProductInformationsBySupplier($product['id_supplier'],
								$product['id_product_orig'], $product['id_product_attribute']);

							$product['supplier_reference'] = $product_information['product_supplier_reference'];

							$product_wholesale_price = $product['wholesale_price'];

							//to fix, save cover id_image
							$id_image = $product['id_image'];
							$id_supplier = $product['id_supplier'];

							foreach ($product_combinations as $id_product_attribute => $combination)
							{
								if (self::$exp_service_data['combinations'] == 2)
								{
									$product['id_product'] = $id_product.'-'.$id_product_attribute; //(++$combination_id);
									$product['id_product_orig'] = $id_product;
									$product['id_product_attribute'] = $id_product_attribute;
								}

								$product['reduction'] = self::getProductSpecificPrice($id_product, $id_product_attribute,
									self::$exp_export_shop, Configuration::get('PS_CURRENCY_DEFAULT'),
									self::$exp_export_country, self::$exp_export_group);

								$new_product_name = array();

								foreach ($combination['attributes'] as $id_attribute_group => $attribute)
								{
									$new_product_name[] = trim($attribute['group']).': '.trim($attribute['attribute']);

									$product['ag'.$id_attribute_group] = trim($attribute['attribute']);
								}

								if (self::$exp_service_data['combination_name'])
									$new_product_name = $name.' ('.implode(', ', $new_product_name).')';
								else
									$new_product_name = $name;

								$product['name'] = $new_product_name;

								if (isset($combination['reference']))
									$product['reference'] = $combination['reference'];
								else
								{
									if (!isset($product['reference']))
										$product['reference'] = '';
								}

								$product_information = Supplier::getProductInformationsBySupplier($id_supplier, $id_product, $id_product_attribute);
								$product['supplier_reference'] = $product_information['product_supplier_reference'];

								/*
								 if (array_key_exists('supplier_reference', $combination))
									$product['supplier_reference'] = $combination['supplier_reference'];
								*/
								if (array_key_exists('location', $combination) && (int)$combination['location'] != 0)
									$product['location'] = $combination['location'];

								if (isset($combination['ean13']))
								{
									if (Validate::isEan13($combination['ean13']) && $combination['ean13'])
										$product['ean13'] = $combination['ean13'];
									elseif (Validate::isEan13($combination['prean']) && $combination['prean'])
										$product['ean13'] = $combination['prean'];
									else
										$product['ean13'] = '';
								}
								else
								{
									if (!isset($product['ean13']))
										$product['ean13'] = '';
								}

								if (isset($combination['upc']))
									$product['upc'] = $combination['upc'];
								else
								{
									if (!isset($product['upc']))
										$product['upc'] = '';
								}

								$combination['quantity'] = (int)StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute, self::$exp_export_shop);
								if (Validate::isInt($combination['quantity']))
									$product['quantity'] = (int)$combination['quantity'];

								if (Validate::isPrice($combination['wholesale_price']))
								{
									if ($combination['wholesale_price'] == 0)
										$product['wholesale_price'] = $product_wholesale_price;
									else
										$product['wholesale_price'] = (float)$combination['wholesale_price'];
								}

								$product['attribute_price'] = $combination['price'];
								$product['attribute_weight'] = $combination['weight'];

								//to fix : get combination id_image

								if (array_key_exists('id_image', $combination) && (int)$combination['id_image'] != 0)
									$product['id_image'] = $combination['id_image'];
								else
									$product['id_image'] = $id_image;

								$this->addProductLine($product, self::$exp_service_data['template']);
							}
						}
						else
						{
							$product_information = Supplier::getProductInformationsBySupplier($product['id_supplier'], $product['id_product_orig'], $product['id_product_attribute']);
							$product['supplier_reference'] = $product_information['product_supplier_reference'];
							
							$this->addProductLine($product, self::$exp_service_data['template']);
						}
					}
				}
				$count_products = count($products);
			} while ($chunk_size == $count_products);
			$this->postProcess();
			
			return $this->saveFile();
		}
	}

	public static function getProductCoverWs($id_product)
	{
		$result = Product::getCover($id_product);
		return $result['id_image'];
	}

	public static function getProducts($id_lang, $start, $limit, $order_by, $order_way, $id_category = false, $only_active = false, $country_id = null, $id_shop = null, $condition = null)
	{
		$front = true;

		if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way))
			die (Tools::displayError());
		if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd')
			$order_by_prefix = 'p';
		else if ($order_by == 'name')
			$order_by_prefix = 'pl';
		else if ($order_by == 'position')
			$order_by_prefix = 'c';

		if (strpos($order_by, '.') > 0)
		{
			$order_by = explode('.', $order_by);
			$order_by_prefix = $order_by[0];
			$order_by = $order_by[1];
		}

		$product_condition = '';
		if ($condition != null && $condition != -1)
		{
			$cond = explode(':', $condition);
			if (is_array($cond))
			{
				$str = array();
				foreach ($cond as $c)
					$str[] = "'".$c."'";

				$product_condition = ' AND product_shop.`condition` in ('.implode(', ', $str).') ';
			}
			else
				$product_condition = ' AND product_shop.`condition` = \''.$cond.'\' ';
		}

		$sql = 'SELECT p.*, product_shop.*, pl.* , t.`rate` AS tax_rate, m.`name` AS manufacturer_name, s.`name` AS supplier_name
				FROM `'._DB_PREFIX_.'product` p
				'.self::addShopSqlAssociation('product', 'p', true, null, false, $id_shop).'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl', $id_shop).')
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`
		 		  AND tr.`id_country` = '.$country_id.'
		 		  AND tr.`id_state` = 0)
	  		 	LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)'.
			($id_category ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '').'
				WHERE pl.`id_lang` = '.(int)$id_lang.
			($id_category ? ' AND c.`id_category` IN ('.$id_category.') ': '').
			($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
			($only_active ? ' AND product_shop.`active` = 1' : '').
			$product_condition.
			// ' ORDER BY '.(isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').'`'.pSQL($order_by).'` '.pSQL($order_way).
			' GROUP BY pl.`id_product` ORDER BY '.(isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').'`'.pSQL($order_by).'` '.pSQL($order_way).
			($limit > 0 ? ' LIMIT '.(int)$start.','.(int)$limit : '');
		$rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		if ($order_by == 'price')
			Tools::orderbyPrice($rq, $order_way);
		return ($rq);
	}


	public static function addShopSqlAssociation($table, $alias, $inner_join = true, $on = null, $force_not_default = false, $id_shop = null)
	{
		$table_alias = $table.'_shop';
		if (strpos($table, '.') !== false)
			list($table_alias, $table) = explode('.', $table);

		$asso_table = Shop::getAssoTable($table);
		if ($asso_table === false || $asso_table['type'] != 'shop')
			return;
		$sql = (($inner_join) ? ' INNER' : ' LEFT').' JOIN '._DB_PREFIX_.$table.'_shop '.$table_alias.'
		ON ('.$table_alias.'.id_'.$table.' = '.$alias.'.id_'.$table;
		if ((int)$id_shop)
			$sql .= ' AND '.$table_alias.'.id_shop = '.(int)$id_shop;
		elseif (Shop::checkIdShopDefault($table) && !$force_not_default)
			$sql .= ' AND '.$table_alias.'.id_shop = '.$alias.'.id_shop_default';
		else
			$sql .= ' AND '.$table_alias.'.id_shop IN ('.implode(', ', Shop::getContextListShopID()).')';
		$sql .= (($on) ? ' AND '.$on : '').')';
		//echo '****'.$sql.'****';
		return $sql;
	}


	public function beforeImport($template, $products)
	{
		return true;
	}


	public function saveFile()
	{
		if (!$handle = fopen($this->filename, 'wb'))
		{
			$this->_errors[] = $this->l('Unable to open or create the CSV file');
			return false;
		}

		fwrite($handle, $this->exp_file_contents);
		fclose($handle);
		chmod($this->filename, 0777);

		return true;
	}


	public function postProcess()
	{
		return true;
	}


	public function addProductLine($product, $template, $params = false)
	{
		return $this->prepareProductInfo($product, $template, $params);
	}


	public function prepareProductInfo($product, $template, $params = false)
	{
		$preparedInfo = array();
		$context = Context::getContext();

		foreach ($template->fields as $field)
		{
			$fieldName = self::prepareTemplateMiscFields($field->field);
			$fieldTitle = self::prepareTemplateMiscFields($field->fieldTitle, $field->field);
			$before = self::prepareTemplateMiscFields($field->before);
			$after = self::prepareTemplateMiscFields($field->after);
			$value = self::prepareTemplateMiscFields($field->value);
			$withTax = (int)self::prepareTemplateMiscFields($field->withTax, false, true) == 1;
			$withShipping = (int)self::prepareTemplateMiscFields($field->withShipping, false, true) == 1;
			$withReduction = (int)self::prepareTemplateMiscFields($field->withReduction, false, true) == 1;

			if (!isset($field->htmlTags))
				$field->htmlTags = 0;
			$htmlTags = (int)self::prepareTemplateMiscFields($field->htmlTags, false, true) == 1;

			if (!isset($field->allPictures))
				$field->allPictures = 0;
			$allPictures = (int)self::prepareTemplateMiscFields($field->allPictures, false, true) == 1;

			if (!isset($field->largePicture))
				$field->largePicture = 0;
			$largePicture = (int)self::prepareTemplateMiscFields($field->largePicture, false, true) == 1;

			$preparedValue = array(
				'title' => $fieldTitle,
				'before' => $before,
				'after' => $after,
				'originalValue' => $value
			);

			if ($product)
			{
				$taxRate = array_key_exists($product['id_tax_rules_group'], self::$exp_tax_rates) ? self::$exp_tax_rates[$product['id_tax_rules_group']] : 0;
				$fieldPrice = $priceNoTax = $price = $product['price'];
				$weight = $product['weight'];
				$taxedPrice = self::getPriceWithTax($price, $taxRate);

				if ($withTax)
					$fieldPrice = self::getPriceWithTax($price, $taxRate);

				// attribute price without TAX in db
				if (isset($product['attribute_price']))
				{
					$taxedPrice += self::getPriceWithTax($product['attribute_price'], $taxRate);
					$priceNoTax += $product['attribute_price'];
					$fieldPrice = ($withTax == 1) ? $taxedPrice : $priceNoTax;
					$weight = $weight + $product['attribute_weight'];
				}

				$taxedReduction = $reductionPrice = 0;

				if (self::$exp_export_group)	
				{
					$reduction_group = self::getProductPriceWithGroupReduction(self::$exp_export_group);
					if ($reduction_group['reduction'] != 0 && $reduction_group !== false) 
					{
						$reductionPrice = self::getProductReduction(
							$reduction_group['reduction_type'],
							$reduction_group['reduction'],
							$fieldPrice,
							$taxRate
						);
					}
				}

				if (array_key_exists('reduction', $product) && $product['reduction'] !== false)
				{
					$reduction = $product['reduction'];

					$reductionPrice = self::getProductReduction(
						$reduction['reduction_type'],
						$reduction['reduction'],
						$reductionPrice == 0 ? $fieldPrice : $reductionPrice,
						$taxRate
					);

					$taxedReduction = !$withTax ? self::getPriceWithTax($reductionPrice, $taxRate) : $reductionPrice;
				}

				$shippingPrice = self::calculateCarrierShippingPrice(
					($taxedPrice - $reductionPrice),
					$product['weight']
				);

				$shippingPrice = $withTax ? self::getPriceWithTax($shippingPrice, self::$exp_carrier_tax) : $shippingPrice;

				$skipFields = self::getFromParams($params, 'skip');
				$formatAsNumbers = self::getFromParams($params, 'numbers');
				$stripNewLines = self::getFromParams($params, 'nolinebreak');

				if (array_key_exists($fieldName, $product) && (!$skipFields || !in_array($fieldName, $skipFields)))
				{
					switch ($fieldName)
					{
						case 'tax_rate':
							$product[$fieldName] = $taxRate;
							break;
						case 'condition':
							$product[$fieldName] = Moussiq::getTranslation($product[$fieldName]);
							break;
					}
					if ($formatAsNumbers && in_array($fieldName, $formatAsNumbers))
						$product[$fieldName] = self::formatNumber($product[$fieldName]);

					// remove new lines -  look engines/ExportCSV.php
					if ($stripNewLines && in_array($fieldName, $stripNewLines))
					{
						// remove newlines -  only  \r\n
						$product[$fieldName] = self::stripNewLines($product[$fieldName]);
						// decode  umlauts, like &Uuml; => Ü
						$product[$fieldName] = html_entity_decode($product[$fieldName]);
						// remove html tags
						if (!$htmlTags)
							$product[$fieldName] = strip_tags($product[$fieldName]);
					}

					$preparedValue['value'] = $product[$fieldName];
				}
				else
				{
					switch ($fieldName)
					{
						case 'price_with_tax':
							$preparedValue['value'] = self::formatNumber(Tools::convertPrice($taxedPrice, self::$exp_export_currency));
							break;

						case 'price':
							$preparedValue['value'] = $fieldPrice;

							if ($withReduction && isset($reductionPrice))
								$preparedValue['value'] = $preparedValue['value'] - $reductionPrice;

							if ($withShipping)
								$preparedValue['value'] = $preparedValue['value'] + $shippingPrice;

							$preparedValue['value'] = self::formatNumber(Tools::convertPrice($preparedValue['value'], self::$exp_export_currency));
							break;

						case 'reduction_price':
							$preparedValue['value'] = self::formatNumber(Tools::convertPrice($reductionPrice, self::$exp_export_currency));
							break;

						case 'total_tax':
							$preparedValue['value'] = self::formatNumber(Tools::convertPrice($taxedPrice - $priceNoTax, self::$exp_export_currency));
							break;

						case 'weight':
							$preparedValue['value'] = self::formatNumber($weight);
							break;

						case 'product_link':
							$link = self::getProductLink(
								$product,
								self::$exp_export_shop,
								$context
							);

							$preparedValue['value'] = $link;
							break;

						case 'picture_link':
							$link = self::getPictureLink($product['link_rewrite'], $product['id_product_image'], $product['id_product'], array_key_exists('id_image', $product) ? $product['id_image'] : false, $allPictures, $largePicture);

							$preparedValue['value'] = $link;
							break;

						case 'shipping_price':
							$preparedValue['value'] = self::formatNumber(Tools::convertPrice($shippingPrice, self::$exp_export_currency));
							break;

						case 'with_shipping_price':
							$preparedValue['value'] = self::formatNumber(Tools::convertPrice($shippingPrice + $fieldPrice, self::$exp_export_currency));
							break;

						case 'category_name':
							if (count($product['categories']) && array_key_exists($product['id_category_default'], $product['categories']))
								$preparedValue['value'] = $product['categories'][$product['id_category_default']];
							else
								$preparedValue['value'] = '';
							break;

						case 'empty_field':
							$preparedValue['value'] = ($value !== false) ? $value : '';
							break;

						default:
							$preparedValue['value'] = '';
							break;
					} // switch
				}

				$preparedInfo[] = array('field' => $fieldName, 'data' => $preparedValue);
			} // if product
		} // template fields

		return $preparedInfo;
	}


	private static function getFromParams($params, $key)
	{
		if ($params && is_array($params) && array_key_exists($key, $params) && is_array($params[$key]))
			return $params[$key];

		return false;
	}


	public static function findCorrectDefault($dbValue, $configValue, $returnBool = false)
	{
		if (!$returnBool)
			return (($dbValue == '' || $dbValue == -1) ? $configValue : $dbValue);

		return (((int)(($dbValue == '') ? $configValue : $dbValue)) == 1) ? true : false;
	}

	public static function findCorrectDefaultString($dbValue, $configValue)
	{
		return ($dbValue == '' ? $configValue : $dbValue);
	}


	/*
	 * Get default template settings (as some settings can have "use default"
	 * value, this method has to actually look what the default value is)
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  integer  $service_id      - Service ID to get the data for
	 *
	 * @return array
	 */
	public static function getCsvDefaults($service_id)
	{
		$sql = '
        SELECT *
        FROM `'._DB_PREFIX_.'moussiq_service`
        WHERE `id_moussiq_service` = '.(int)$service_id;

		if (!$result = Db::getInstance()->getRow($sql))
			return false;

		$configuration = Configuration::getMultiple(array(
			'EXPORT_COUNTRY',
			'EXPORT_STATE',
			'EXPORT_CARRIER',
			'EXPORT_SHOP',
			'EXPORT_GROUP',
			'EXPORT_DELIMITER',
			'EXPORT_HEADER',
			'EXPORT_INACTIVE',
			'EXPORT_LANGUAGE',
			'EXPORT_ENCLOSURE',
			'EXPORT_COMBINATIONS',
			'EXPORT_COMBINATION_NAME',
			'EXPORT_CONDITION'
		));

		$data = $result;

		$data['id_country'] = (int)self::findCorrectDefault($result['id_country'], $configuration['EXPORT_COUNTRY']);
		$data['id_state'] = (int)self::findCorrectDefault($result['id_state'], $configuration['EXPORT_STATE']);
		$data['condition'] = self::findCorrectDefaultString($result['condition'], $configuration['EXPORT_CONDITION']);
		$data['id_carrier'] = (int)self::findCorrectDefault($result['id_carrier'], $configuration['EXPORT_CARRIER']);
		$data['id_shop'] = (int)self::findCorrectDefault($result['id_shop'], $configuration['EXPORT_SHOP']);
		$data['id_group'] = (int)self::findCorrectDefault($result['id_group'], $configuration['EXPORT_GROUP']);
		$data['header'] = (int)self::findCorrectDefault($result['header'], $configuration['EXPORT_HEADER']);
		$data['id_lang'] = (int)self::findCorrectDefault($result['id_lang'], $configuration['EXPORT_LANGUAGE']);
		$data['export_inactive'] = self::findCorrectDefault($result['export_inactive'], $configuration['EXPORT_INACTIVE'], true);
		$data['combinations'] = self::findCorrectDefault($result['combinations'], $configuration['EXPORT_COMBINATIONS']);
		$data['combination_name'] = self::findCorrectDefault($result['combination_name'], $configuration['EXPORT_COMBINATION_NAME']);
		$data['name'] = pSQL($result['name']);
		$data['template'] = ExportTools::jsonDecode(addcslashes(Moussiq::decodeString($result['template']), '\\'));

		if (!Country::containsStates($data['id_country']))
			$data['id_state'] = false;

		return $data;
	}


	/*
	 * Transliteration method. A "just in case" thing, basically :)
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  string   $string         - A string to transliterate
	 *
	 * @return string
	 */
	public static function transliterate($string)
	{
		if (function_exists('mb_strtolower'))
			$string = mb_strtolower($string);
		else
			$string = Tools::strtolower($string);

		$tr = array(
			'і' => 'i', 'ґ' => 'g',
			'ё' => 'yo', '№' => '#',
			'є' => 'e', 'ї' => 'yi',
			'а' => 'a', 'б' => 'b',
			'в' => 'v', 'г' => 'g',
			'д' => 'd', 'е' => 'e',
			'ж' => 'zh', 'з' => 'z',
			'и' => 'i', 'й' => 'y',
			'к' => 'k', 'л' => 'l',
			'м' => 'm', 'н' => 'n',
			'о' => 'o', 'п' => 'p',
			'р' => 'r', 'с' => 's',
			'т' => 't', 'у' => 'u',
			'ф' => 'f', 'х' => 'h',
			'ц' => 'ts', 'ч' => 'ch',
			'ш' => 'sh', 'щ' => 'sch',
			'ъ' => '\'', 'ы' => 'yi',
			'ь' => '', 'э' => 'e',
			'ю' => 'yu', 'я' => 'ya',
			'ä' => 'ae', 'ü' => 'ue',
			'ö' => 'oe', 'ß' => 's'
		);

		$replace = array(
			'\\', '/', '|', ',',
			'.', '!', '@', '#',
			'$', '%', '^', '&',
			'*', "\n", "\r", "\t",
			"\r\n", '<', '>', '?'
		);

		return str_replace($replace, '', strtr($string, $tr));
	}


	/*
	 * Formats number to be "user friendly"
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  float    $price          - Number to format
	 *
	 * @return string                   Formatted number
	 */
	public static function formatNumber($number)
	{
		return number_format((float)$number, 2, '.', '');
	}


	/*
	 * Takes price and tax rate, applies tax to price and returns the result.
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  float    $price          - Price
	 * @param  float    $taxRate        - Tax rate
	 *
	 * @return float
	 */
	public static function getPriceWithTax($price, $taxRate)
	{
		return (float)($price * (1 + ($taxRate / 100)));
	}


	/*
	 * Takes taxed price and tax rate and returns price before tax was applied.
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  float    $price          - Taxed price
	 * @param  float    $taxRate        - Tax rate
	 *
	 * @return float
	 */
	public static function removeTaxFromPrice($price, $taxRate)
	{
		return (float)($price / (1 + ($taxRate / 100)));
	}


	/*
	 * Strips all sorts of new lines from a given string.
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  string   $str            - String to strip new lines from
	 *
	 * @return string
	 */
	public static function stripNewLines($str)
	{
		return (string)trim(str_replace(array("\r", "\r\n", "\n"), ' ', $str));
	}


	/*
	 * Checks if a directory is writeable
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  string   $dir            - Path to directory
	 *
	 * @return boolean
	 */
	public static function checkDir($dir)
	{
		return is_writable($dir);
	}


	/*
	 * Returns a link to a specified product thickbox picture.
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  integer  $productId      - ID of the product.
	 * @param  array    $pictures       - An array of existing pictures
	 *
	 * @return string
	 */
	public static function getPictureLink($link_rewrite, $productId, $productcombId, $idImage = false, $allPictures = 0, $largePicture = 0)
	{
		$exp_existing_pictures = self::getExistingPictures($productId);
		$legend = null;
		if ($idImage)
		{
			if (($allPictures) && ($productId == $productcombId))
			{
				$links = array();

				foreach ($exp_existing_pictures[$productId] as $key => $value)
				{
					$pictureName = $productId.'-'.$exp_existing_pictures[$productId][$key]['id_image'];
					$legend = $exp_existing_pictures[$productId][$key]['legend'];

					if (!Validate::isLinkRewrite($legend))
						$legend = Tools::strtolower(Tools::link_rewrite($legend));
					$links[] = Tools::strtolower(self::getImageLink($link_rewrite, $pictureName, ($largePicture) ? 'large' : 'thickbox'));
				}
				return implode(',', $links);
			}
			else
			{
				$pictureName = $productId.'-'.$idImage;
				$legend = null;
				return Tools::strtolower(self::getImageLink($link_rewrite, $pictureName, ($largePicture) ? 'large' : 'thickbox'));
			}
		}
		elseif (array_key_exists($productId, $exp_existing_pictures))
		{
			if ($allPictures && ($productId == $productcombId))
			{
				$links = array();
				foreach ($exp_existing_pictures[$productId] as $key => $value)
				{
					$pictureName = $productId.'-'.$exp_existing_pictures[$productId][$key]['id_image'];
					$legend = $exp_existing_pictures[$productId][$key]['legend'];

					if (!Validate::isLinkRewrite($legend))
						$legend = Tools::strtolower(Tools::link_rewrite($legend));
					$links[] = Tools::strtolower(self::getImageLink($link_rewrite, $pictureName, ($largePicture) ? 'large' : 'thickbox'));
				}
				return implode(',', $links);
			}
			else
			{
				$pictureName = $productId.'-'.$exp_existing_pictures[$productId][0]['id_image'];
				$legend = $exp_existing_pictures[$productId][0]['legend'];

				if (!Validate::isLinkRewrite($legend))
					$legend = Tools::strtolower(Tools::link_rewrite($legend));
				return Tools::strtolower(self::getImageLink($link_rewrite, $pictureName, ($largePicture) ? 'large' : 'thickbox'));
			}
		}
		return '';
	}

	public static function getImageLink($name, $ids, $type = null)
	{
		$allow = (int)Configuration::get('PS_REWRITING_SETTINGS');
		//echo   '+'.$name.', '.$ids.', '.$type.'=';
		$not_default = false;
		// legacy mode or default image
		$theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').'-'.(int)Context::getContext()->shop->id_theme.'.jpg')) ? '-'.Context::getContext()->shop->id_theme : '');

		if (!file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').$theme.'.jpg'))
			$type = $type.'_default';

		if ((Configuration::get('PS_LEGACY_IMAGES')
			&& (file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').$theme.'.jpg')))
			|| ($not_default = strpos($ids, 'default') !== false))
		{
			if ($allow == 1 && !$not_default)
				$uri_path = $ids.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
			else
				$uri_path = $ids.($type ? '-'.$type : '').$theme.'.jpg';
		}
		else
		{
			// if ids if of the form id_product-id_image, we want to extract the id_image part
			$split_ids = explode('-', $ids);
			$id_image = (isset($split_ids[1]) ? $split_ids[1] : $split_ids[0]);
			$theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.Image::getImgFolderStatic($id_image).$id_image.($type ? '-'.$type : '').'-'.(int)Context::getContext()->shop->id_theme.'.jpg')) ? '-'.Context::getContext()->shop->id_theme : '');
			if ($allow == 1)
				$uri_path = $id_image.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
			else
				$uri_path = Image::getImgFolderStatic($id_image).$id_image.($type ? '-'.$type : '').$theme.'.jpg';
		}

		$id_shop = Context::getContext()->shop->id;

		if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $id_shop !== null)
			$shop = new Shop($id_shop);
		else
			$shop = Context::getContext()->shop;

		$base = $shop->getBaseURL();

		return $base.$uri_path;
	}


	/*
	 * Returns link to product in store. Depending on store settings, will
	 * return search engine friendly link or a usual one.
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  boolean  $sef            - Search engine friendly link (on/off)
	 * @param  integer  $productId      - ID of the product.
	 * @param  string   $rewrite        - Product's "link_rewrite"
	 * @param  string   $ean13          - Product's ean code (used in sef links)
	 *
	 * @return string
	 */
	public static function getProductLink($product, $id_shop = null, $context = false)
	{
		if (!$context)
			$context = Context::getContext();

		return $context->link->getProductLink($product['id_product_orig'], null, null, null, self::$exp_export_languageObj->id, $id_shop, $product['id_product_attribute']);
	}

	public static function calculateCarrierShippingPrice($price, $weight)
	{
		if (self::$exp_carrier_method == Carrier::SHIPPING_METHOD_WEIGHT)
		{
			$freeShipping = self::$exp_free_weight_shipping_requirements;

			if (!$freeShipping === false && $weight > $freeShipping)
				return 0;

			foreach (self::$exp_shipping_carrier_data as $range)
				if ($weight >= $range['from'] && $weight < $range['to'])
				{
					$ps_shipping_handling = Configuration::get('PS_SHIPPING_HANDLING');
					if (isset($ps_shipping_handling) && $range['shipping_handling'])
						return (float)($range['price'] + $ps_shipping_handling);

					return (float)$range['price'];
				}
		}
		if (self::$exp_carrier_method == Carrier::SHIPPING_METHOD_PRICE)
		{
			$freeShipping = self::$exp_free_price_shipping_requirements;

			if (!$freeShipping === false && $price > $freeShipping)
				return 0;

			foreach (self::$exp_shipping_carrier_data as $range)
				if ($price >= $range['from'] && $price < $range['to'])
				{
					$ps_shipping_handling = Configuration::get('PS_SHIPPING_HANDLING');
					if (isset($ps_shipping_handling) && $range['shipping_handling'])
						return (float)($range['price'] + $ps_shipping_handling);

					return (float)$range['price'];
				}
		}

		return 0;
	}


	/*
	 * Returns product reduction price.
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  float    $rprice         - Reduction price from database (can be 0)
	 * @param  float    $rpercent       - Reduction percent from database (can be 0)
	 * @param  string   $date_from      - "Reduction from" date
	 * @param  string   $date_to        - "Reduction to" date
	 * @param  float    $product_price  - Product price without reduction
	 * @param  float    $taxrate        - Product's tax rate
	 *
	 * @return float
	 */
	public static function getProductReduction($type, $reduction, $product_price, $taxrate)
	{
		if ($type == 'amount' && $reduction > 0)
		{
			if ($reduction >= $product_price)
				$ret = $product_price;
			else
				$ret = $reduction;
		}
		elseif ($type == 'percentage' && $reduction > 0)
		{
			$reduction *= 100;

			if ($reduction >= 100)
				$ret = $product_price;
			else
				$ret = $product_price * $reduction / 100;
		}

		$ret = isset($ret) ? $ret : 0;

		return $ret;
	}


	/*
	 * Returns "free shipping starts at" value. Either price or weight.
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  integer  $billing        - 0 for price range or 1 for weight range
	 *
	 * @return mixed                    (Float or boolean false)
	 */
	public static function getFreeShippingRequirements($billing)
	{
		if ($billing == 0)
			$result = Configuration::get('PS_SHIPPING_FREE_PRICE');
		else
			$result = Configuration::get('PS_SHIPPING_FREE_WEIGHT');

		return $result > 0 ? (float)$result : false;
	}

	/*
	 * Collects all existing categories with names in given language
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  integer  $langId         - Language id
	 *
	 * @return array
	 */
	public static function getExistingCategories($langId)
	{
		$sql = '
        SELECT c.`id_category`,
              cl.`name`
        FROM `'._DB_PREFIX_.'category` c
        
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
            ON  (
                    c.`id_category` = cl.`id_category`
                )
                
        WHERE cl.`id_lang` = '.(int)$langId;

		$result = Db::getInstance()->ExecuteS($sql);

		$categories = array();

		if ($result)
		{
			foreach ($result as $category)
				$categories[$category['id_category']] = $category['name'];
		}
		return $categories;
	}


	public static function getTaxes()
	{
		if (Tools::substr(_PS_VERSION_, 0, 3) >= '1.6')
		{
			$taxRules = Db::getInstance()->ExecuteS('
                SELECT *
                FROM `'._DB_PREFIX_.'tax_rule`
                WHERE `id_country` = '.self::$exp_export_country.'
                AND `id_state` IN (0, '.self::$exp_export_state.')
                ORDER BY `id_state` DESC'
			);

			$rows = array();

			if ($taxRules && count($taxRules))
			{
				foreach ($taxRules as $taxRule)
				{
					if (!array_key_exists($taxRule['id_tax_rules_group'], $rows))
						$rows[$taxRule['id_tax_rules_group']] = array();

					$rows[$taxRule['id_tax_rules_group']][] = $taxRule;
				}
			}

			$taxes = array();

			$continueCurrent = false;

			foreach ($rows as $taxRuleGroup => $groupTaxes)
			{
				foreach ($groupTaxes as $row)
				{

					if (!array_key_exists($taxRuleGroup, $taxes))
						$taxes[$taxRuleGroup] = array();

					if ($row['id_state'] != 0)
					{
						switch ($row['behavior'])
						{
							case PS_STATE_TAX:
								$taxes[$taxRuleGroup] = new Tax($row['id_tax'], self::$exp_export_language);

								$continueCurrent = true;
								break;

							case PS_BOTH_TAX:
								$taxes[$taxRuleGroup][] = new Tax($row['id_tax'], self::$exp_export_language);
								break;

							case PS_PRODUCT_TAX:
								break;
						}

						if ($continueCurrent)
							continue 2;
					}
					else
						$taxes[$taxRuleGroup][] = new Tax((int)$row['id_tax'], self::$exp_export_language);
				}
			}

			$result = array();

			if (count($taxes))
			{
				foreach ($taxes as $taxRule => $groupTaxes)
				{
					if (is_object($groupTaxes))
						$result[$taxRule] = (float)$groupTaxes->rate;
					elseif (is_array($groupTaxes) && count($groupTaxes))
					{
						$rate = 0;
						foreach ($groupTaxes as $tax)
						{
							if (is_object($tax))
								$rate += (float)$tax->rate;
						}

						$result[$taxRule] = $rate;
					}
				}
			}

			return $result;
		}
		else
		{
			$taxRules = Db::getInstance()->ExecuteS('
                SELECT *
                FROM `'._DB_PREFIX_.'tax_rule`
                WHERE `id_country` = '.self::$exp_export_country.'
                AND `id_state` IN (0, '.self::$exp_export_state.')
                ORDER BY `id_county` DESC, `id_state` DESC'
			);

			$rows = array();

			if ($taxRules && count($taxRules))
			{
				foreach ($taxRules as $taxRule)
				{
					if (!array_key_exists($taxRule['id_tax_rules_group'], $rows))
						$rows[$taxRule['id_tax_rules_group']] = array();

					$rows[$taxRule['id_tax_rules_group']][] = $taxRule;
				}
			}

			$taxes = array();

			$continueCurrent = false;

			foreach ($rows as $taxRuleGroup => $groupTaxes)
			{
				foreach ($groupTaxes as $row)
				{

					if (!array_key_exists($taxRuleGroup, $taxes))
						$taxes[$taxRuleGroup] = array();

					if ($row['id_county'] != 0)
					{
						switch ($row['county_behavior'])
						{
							case County::USE_BOTH_TAX:
								$taxes[$taxRuleGroup][] = new Tax($row['id_tax'], self::$exp_export_language);
								break;

							case County::USE_COUNTY_TAX:
								$taxes[$taxRuleGroup] = array(new Tax($row['id_tax'], self::$exp_export_language));

								$continueCurrent = true;
								break;

							case County::USE_STATE_TAX:
								break;
						}

						if ($continueCurrent)
							continue 2;
					}

					elseif ($row['id_state'] != 0)
					{
						switch ($row['state_behavior'])
						{
							case PS_STATE_TAX:
								$taxes[$taxRuleGroup] = new Tax($row['id_tax'], self::$exp_export_language);

								$continueCurrent = true;
								break;

							case PS_BOTH_TAX:
								$taxes[$taxRuleGroup][] = new Tax($row['id_tax'], self::$exp_export_language);
								break;

							case PS_PRODUCT_TAX:
								break;
						}

						if ($continueCurrent)
							continue 2;
					}
					else
						$taxes[$taxRuleGroup][] = new Tax((int)$row['id_tax'], self::$exp_export_language);
				}
			}

			$result = array();

			if (count($taxes))
			{
				foreach ($taxes as $taxRule => $groupTaxes)
				{
					if (is_object($groupTaxes))
						$result[$taxRule] = (float)$groupTaxes->rate;
					elseif (is_array($groupTaxes) && count($groupTaxes))
					{
						$rate = 0;
						foreach ($groupTaxes as $tax)
						{
							if (is_object($tax))
								$rate += (float)$tax->rate;
						}

						$result[$taxRule] = $rate;
					}
				}
			}

			return $result;
		}
	}


	/*
	 * Collects all existing product pictures for future export
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @return array
	 */
	public static function getExistingPictures($productId)
	{
		$sql = '
        SELECT i.*, il.`legend`
        FROM `'._DB_PREFIX_.'image` i
        LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image`)
        WHERE il.`id_lang` = '.self::$exp_export_language.' AND i.`id_product` = '.$productId.'
        ORDER BY i.`id_product`,
                 i.`position`';

		$result = Db::getInstance()->ExecuteS($sql);

		$pictures = array();

		foreach ($result as $picture)
			$pictures[$picture['id_product']][] = array('id_image' => $picture['id_image'], 'legend' => $picture['legend']);
		return $pictures;
	}

	public static function getProductsPriorities()
	{
		$priorities = array();

		$result = Db::getInstance()->ExecuteS('
            SELECT * FROM 
            (
                SELECT * FROM `'._DB_PREFIX_.'specific_price_priority` ORDER BY `id_specific_price_priority` DESC
            ) AS `order_tmp`
            
            GROUP BY `id_product`');

		if ($result && count($result))
			foreach ($result as $priority)
				$priorities[$priority['id_product']] = $priority['priority'];

		return $priorities;
	}

	public static function getProductPriority($id_product)
	{
		$priority = (is_array(self::$exp_product_priorities) && array_key_exists($id_product, self::$exp_product_priorities)) ? self::$exp_product_priorities[$id_product] : Configuration::get('PS_SPECIFIC_PRICE_PRIORITIES');

		return preg_split('/;/', $priority);
	}

	public static function getProductPriceWithGroupReduction($id_group)
	{
		if ($id_group)
		{
			$sql = '
			SELECT `reduction`
			FROM `'._DB_PREFIX_.'group`
			WHERE `id_group` = '.(int)$id_group;

			$reduction = Db::getInstance()->getRow($sql);
			
			return array(
				'reduction_type' => 'percentage',
				'reduction' => ($reduction['reduction'] / 100)
			);
		}

		return false;
	}

	public static function getProductSpecificPrice($id_product, $id_product_attribute, $id_shop, $id_currency, $id_country, $id_group)
	{
		if (array_key_exists($id_product, self::$exp_specific_prices))
		{
			$scores = array();
			$priorities = self::getProductPriority($id_product);

			foreach (self::$exp_specific_prices[$id_product] as $key => $value)
			{
				$score = 0;

				if ((($value['from'] != '0000-00-00 00:00:00'
					&& self::$exp_export_start_time >= strtotime($value['from']))
					|| $value['from'] == '0000-00-00 00:00:00')
					&& (($value['to'] != '0000-00-00 00:00:00'
						&& self::$exp_export_start_time <= strtotime($value['to']))
					|| $value['to'] == '0000-00-00 00:00:00'))
					$score += 1;

				if ($value['from_quantity'] >= 0 && $value['from_quantity'] <= 1)
					$score += 1;

				if ($id_product_attribute > 0 && $value['id_product_attribute'] == $id_product_attribute)
					$score += 1;
				elseif ($id_product_attribute > 0 && $value['id_product_attribute'] == 0)
					$score += 0;
				else if ($id_product_attribute > 0 && $value['id_product_attribute'] != $id_product_attribute)
					$score = -1;
				else if ($id_product_attribute == 0 && $value['id_product_attribute'] > 0)
					$score = -1;

				foreach (($priorities) as $k => $field)
					if ((int)${$field} == (int)$value[$field])
						$score += pow(2, $k + 1);

				if ($score >= 0)
					$scores[$key] = $score;
			}

			if (!count($scores))
				return false;

			$max = (array_keys($scores, max($scores)));
			return self::$exp_specific_prices[$id_product][$max[0]];
		}

		return false;
	}

	public static function getSpecificPrices($id_shop = 0)
	{
		$prices = array();
		if (Moussiq::tableExists(_DB_PREFIX_.'specific_price'))
		{
			$result = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'specific_price` WHERE `id_shop` IN (0, '.(int)$id_shop.') ORDER BY from_quantity');

			if ($result && count($result))
			{
				foreach ($result as $specificPrice)
				{
					if (!array_key_exists($specificPrice['id_product'], $prices))
						$prices[$specificPrice['id_product']] = array();

					array_push($prices[$specificPrice['id_product']], $specificPrice);
				}
				return $prices;
			}
		}

		return $prices;
	}

	protected static function getProductCategories()
	{
		if (count(self::$exp_export_categories))
			$categories = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'category_product` WHERE `id_category` IN ('.implode(',', array_keys(self::$exp_export_categories)).')');
		else
			$categories = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'category_product`');

		$prepared = array();

		if ($categories && count($categories))
		{
			foreach ($categories as $category)
			{
				if (!array_key_exists($category['id_product'], $prepared))
					$prepared[$category['id_product']] = array();

				$prepared[$category['id_product']][$category['id_category']] = (isset(self::$exp_export_categories[$category['id_category']]['name'])
					&& self::$exp_export_categories[$category['id_category']]['name'] != '')?
					self::$exp_export_categories[$category['id_category']]['name']:
					(isset(self::$exp_existing_categories[$category['id_category']])?self::$exp_existing_categories[$category['id_category']]:'');
			}
		}
		return $prepared;
	}

	private static function getCarrierShippingRanges()
	{
		$carrier = new Carrier((int)self::$exp_carrier_id);

		// Get only carriers that are compliant with shipping method
		if (($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT && $carrier->getMaxDeliveryPriceByWeight((int)self::$exp_zone) === false)
			|| ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE && $carrier->getMaxDeliveryPriceByPrice((int)self::$exp_zone) === false))
			return array();

		$table = '';
		if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT)
			$table = 'range_weight';

		if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE)
			$table = 'range_price';

		if (!in_array($table, array('range_price', 'range_weight')))
			return array();

		$sql = '
        SELECT d.`id_'.$table.'` ,
               d.`id_carrier`        ,
               d.`id_zone`           ,
               d.`price`             ,
               r.`delimiter1`        ,
               r.`delimiter2`
        FROM `'._DB_PREFIX_.'delivery` d

            LEFT JOIN `'._DB_PREFIX_.$table.'` r
            ON  r.`id_'.$table.'` = d.`id_'.$table.'`

        WHERE
                d.`id_'.$table.'` IS NOT NULL
            AND d.`id_'.$table.'` != 0
            AND d.price > 0
            AND d.id_carrier = '.(int)self::$exp_carrier_id.'
            AND d.id_zone = '.(int)self::$exp_zone;

		$result = Db::getInstance()->ExecuteS($sql);

		$priceRanges = array();

		$i = 0;
		foreach ($result as $range)
		{
			$priceRanges[$i]['price'] = $range['price'];
			$priceRanges[$i]['from'] = $range['delimiter1'];
			$priceRanges[$i]['to'] = $range['delimiter2'];
			$priceRanges[$i]['shipping_handling'] = $carrier->shipping_handling;

			$i++;
		}

		return $priceRanges;
	}


	/*
	 * Get tax rate for a specific carrier
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  integer  $carrier        - Carrier is for which to select tax rate
	 *                                    for
	 *
	 * @return float
	 */
	public static function getShippingTax($carrier, $id_shop)
	{
		if (!Validate::isUnsignedId($carrier))
			return 0;

		$sql = '
        SELECT t.`rate`
        FROM `'._DB_PREFIX_.'carrier_tax_rules_group_shop` ctrgs
            LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr
            ON (ctrgs.`id_tax_rules_group` = tr.`id_tax_rules_group`)
            LEFT JOIN `'._DB_PREFIX_.'tax` t
            ON  (
                    t.`id_tax` = tr.`id_tax`
                )
        WHERE ctrgs.`id_carrier` = '.(int)$carrier.' AND ctrgs.`id_shop` IN(0, '.(int)$id_shop.') ';

		$result = Db::getInstance()->getRow($sql);

		return isset($result['rate']) ? (float)$result['rate'] : 0;
	}


	/*
	 * Collects attribute combinations for product.
	 * for export.
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  integer  $id_lang        - Language id for multilang attribute
	 *                                    tables
	 *
	 * @return array
	 */
	/*FIXED: out of memory*/
	public static function collectCombinations($id_lang, $id_product, $id_shop)
	{
		$sql = '
        SELECT pa.*                          ,
               ai.`id_image`                 ,
               ag.`id_attribute_group`       ,
              agl.`name` AS group_name       ,
               al.`name`  AS attribute_name  ,
                a.`id_attribute`			 ,
                pr.`ean13` AS prean
              
        FROM `'._DB_PREFIX_.'product_attribute` pa
        
            LEFT JOIN `'._DB_PREFIX_.'product_attribute_image` ai
            ON  ai.`id_product_attribute` = pa.`id_product_attribute`

			LEFT JOIN `'._DB_PREFIX_.'product` pr
			ON  pr.`id_product` = pa.`id_product`
        
            LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
            ON  pac.`id_product_attribute` = pa.`id_product_attribute`
            
            LEFT JOIN `'._DB_PREFIX_.'attribute` a
            ON  a.`id_attribute` = pac.`id_attribute`
            
            LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag
            ON  ag.`id_attribute_group` = a.`id_attribute_group`
            
            LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
            ON  a.`id_attribute` = al.`id_attribute`
            
            LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
            ON  ag.`id_attribute_group` = agl.`id_attribute_group`

            '.self::addShopSqlAssociation('product_attribute', 'pa', true, null, false, $id_shop).'
            
        WHERE al.`id_lang`              = '.(int)$id_lang.'
        AND  agl.`id_lang`              = '.(int)$id_lang.'
        AND  pa.`id_product`            = '.(int)$id_product.'
        
        ORDER BY ag.`id_attribute_group` ASC';
		$result = Db::getInstance()->ExecuteS($sql);
		$combinations = array();

		$fieldsToPut = array(
			'reference',
			'id_image',
			'supplier_reference',
			'location',
			'ean13',
			'prean',
			'wholesale_price',
			'price',
			'ecotax',
			'quantity',
			'weight',
			'upc'
		);

		foreach ($result as $combination)
		{
			$combinations[$combination['id_product']][$combination['id_product_attribute']]['attributes'][$combination['id_attribute_group']] = array('group' => $combination['group_name'], 'attribute' => $combination['attribute_name']);

			foreach ($fieldsToPut as $field)
			{
				if (isset($combination[$field]) && !isset($combinations[$combination['id_product']][$combination['id_product_attribute']][$field]))
					$combinations[$combination['id_product']][$combination['id_product_attribute']][$field] = $combination[$field];
			}
		}

		return $combinations;
	}

	public static function collectFeatures($id_lang, $id_product)
	{
		$sql = '
        SELECT fp.`id_feature`, fvl.`value`
			FROM `'._DB_PREFIX_.'feature_product` fp
			LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl
            ON  fp.`id_feature_value`=fvl.`id_feature_value`
			WHERE fp.`id_product` = '.(int)$id_product.' AND fvl.`id_lang`='.(int)$id_lang;
		$result = Db::getInstance()->ExecuteS($sql);
		$features = array();
		foreach ($result as $v)
			$features[$v['id_feature']] = $v['value'];

		return $features;
	}


	public static function getTaxRules()
	{

	}


	/*
	 * Prepares additional template fields (eg. "Value Before", "Value After")
	 * for export.
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  mixed    $value          - Initial value, whatever was given by
	 *                                    the user
	 * @param  mixed    $defValue       - A default value to put if the initial
	 *                                    value was empty.
	 * @param  bool     $returnFloat    - If set to true, the second parameter
	 *                                    is ignored, method returns float value
	 *                                    of the first parameter
	 *
	 * @return mixed
	 */
	public static function prepareTemplateMiscFields($value, $defValue = false, $returnFloat = false)
	{
		if (!$returnFloat)
			return Tools::stripslashes((Tools::strlen($value) == 0 || $value === 'undefined') ? $defValue : $value);

		return (float)Tools::stripslashes($value);
	}
}
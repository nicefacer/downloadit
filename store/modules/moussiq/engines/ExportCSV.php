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

class ExportCSV extends Export
{
	public static $exp_delimiter;
	public static $exp_enclosure;

	public function __construct($id_service, $filename = false)
	{
		$this->name = 'CSV';
		$this->extension = 'csv';

		parent::__construct($id_service, $filename);

		self::$exp_delimiter = trim(self::$exp_service_data['delimiter']) == '' ? Configuration::get('EXPORT_DELIMITER') : self::$exp_service_data['delimiter'];
		self::$exp_enclosure = trim(self::$exp_service_data['enclosure']) == '' ? Configuration::get('EXPORT_ENCLOSURE') : self::$exp_service_data['enclosure'];

		self::$exp_delimiter = ExportTools::delimiterByKeyWord(self::$exp_delimiter);
		self::$exp_enclosure = ExportTools::getEnclosureFromId(self::$exp_enclosure);
	}

	public function postProcess()
	{
		$this->exp_file_contents = implode("\r\n", $this->exp_file_contents);
	}

	public function prepareProductInfo($product, $template, $params = false)
	{
		return parent::prepareProductInfo($product, $template, array(
			'numbers' => array(
				'price',
				'wholesale_price',
				'tax_rate',
				'quantity',
				'weight',
				'wholesale_price',
				'ecotax',
				'reduction_from',
				'reduction_to',
				'reduction_percent',
				'reduction_price',
				'additional_shipping_cost',
				'height',
				'width',
				'depth',
				'unit_price_ratio',
				'minimal_quantity',
				'tax_rate',
				'total_tax',
				'shipping_price',
				'price_with_tax',
			),
			'nolinebreak' => array(
				'name',
				'description',
				'description_short'
			),
			'skip' => array(
				'price',
				'weight'
			)
		));
	}

	public function addProductLine($product, $template, $params = false)
	{
		$product = parent::addProductLine($product, $template, $params);
		$line = array();

		foreach ($product as $fieldData)
		{
			$field = $fieldData['field'];
			$data = $fieldData['data'];

			$value = $data['value'];
			$before = $data['before'];
			$after = $data['after'];

			$value = (($before !== false) ? $before : '').$value.(($after !== false) ? $after : '');

			array_push($line, self::prepareValueForCsv($value));
		}

		$this->exp_file_contents[] = implode(self::$exp_delimiter, $line);
	}

	public function beforeImport($template, $products)
	{
		$this->exp_file_contents = array();

		if (self::$exp_service_data['header'] == 1)
		{
			$tmpArr = array();

			foreach ($template->fields as $field)
			{
				$fieldName = Tools::stripslashes($field->field);
				$fieldTitle = Tools::stripslashes($field->fieldTitle);

				if (Tools::strlen($fieldTitle) == 0)
					$fieldTitle = $fieldName;

				$fieldTitle = self::prepareValueForCsv($fieldTitle);
				$tmpArr[] = $fieldTitle;
			}

			$this->exp_file_contents[] = implode(self::$exp_delimiter, $tmpArr);
		}
	}

	/*
	 * Prepares a value for CSV input. (Simply enclosing a value in quotes or
	 * double quotes is not correct). This method tries to do it properly.
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  mixed    $value          - A value to prepare
	 * @param  string   $delimiter      - CSV field delimiter
	 * @param  string   $returnBool     - Enclosure character (quote or double
	 *                                    quote)
	 *
	 * @return string                   Prepared value
	 */
	public static function prepareValueForCsv($value)
	{
		if (stripos($value, self::$exp_enclosure) !== false)
			$value = str_replace(self::$exp_enclosure, self::$exp_enclosure.self::$exp_enclosure, $value);

		if ((stripos($value, self::$exp_delimiter) !== false)
			|| (stripos($value, self::$exp_enclosure) !== false)
			|| (stripos($value, "\n") !== false))
			$value = self::$exp_enclosure.$value.self::$exp_enclosure;

		return $value;
	}
}
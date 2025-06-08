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

class ExportTools
{
	public static function jsonEncode($json)
	{
		if (method_exists('Tools', 'jsonEncode'))
			return Tools::jsonEncode($json);
	}

	public static function jsonDecode($json)
	{
		if (method_exists('Tools', 'jsonDecode'))
			return Tools::jsonDecode($json);
	}

	/*
	 * A helper method that turns letter abbreviations into correct delimiters.
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  string   $delimiter
	 *
	 * @return string
	 */
	public static function delimiterByKeyWord($delimiter)
	{
		$replacePairs = array(
			'tab' => "\t",
			'com' => ',',
			'exc' => '!',
			'car' => '^',
			'bar' => '|',
			'td' => '~'
		);

		$newDelimiter = strtr($delimiter, $replacePairs);

		if ($newDelimiter !== false)
			return $newDelimiter;

		return $delimiter;
	}


	/*
	 * Basically just a switch for now - will return double quote character
	 * if $enclosure is 1 or a single quote is it's 2. In any other case
	 * will return double quote.
	 *
	 * @access public
	 *
	 * @scope  static
	 *
	 * @param  integer  $enclosure
	 *
	 * @return string
	 */
	public static function getEnclosureFromId($enclosure)
	{
		switch ((int)$enclosure)
		{
			case 1:
				return '"';
				break;
			case 2:
				return '\'';
				break;
			default:
				return '"';
				break;
		}
	}
}
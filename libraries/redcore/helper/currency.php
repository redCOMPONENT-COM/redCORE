<?php
/**
 * @package     Redcore
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Currency helper class.
 *
 * @package     Redcore
 * @subpackage  Helper
 * @since       1.0
 */
final class RHelperCurrency
{
	protected static $codes;

	/**
	 * Return supported currencies from http://www.currency-iso.org
	 *
	 * @return array
	 */
	protected static function get_iso_4217_currency_codes()
	{
		if (!self::$codes)
		{
			// Xml file was pulled from http://www.currency-iso.org/dam/downloads/table_a1.xml. Should be refreshed sometimes !
			$xml = file_get_contents(dirname(__FILE__) . '/currency_iso_4217_table.xml');
			$obj = new SimpleXMLElement($xml);

			$currencies = array();

			foreach ($obj->children()->children() as $country)
			{
				$code = (string) $country->Ccy;

				if ($code && !isset($currencies[$code]))
				{
					$obj = new stdclass;
					$obj->name   = (string) $country->CcyNm;
					$obj->code   = (string) $country->Ccy;
					$obj->number = (string) $country->CcyNbr;

					$currencies[$code] = $obj;
				}
			}

			self::$codes = $currencies;
		}

		return self::$codes;
	}

	/**
	 * Return iso code from iso number
	 *
	 * @param   string  $number  iso number
	 *
	 * @return multitype:string |boolean code or false if not found
	 */
	public static function getIsoCode($number)
	{
		$cur = self::get_iso_4217_currency_codes();

		foreach ($cur as $code => $currency)
		{
			if ($currency->number == $number)
			{
				return $code;
			}
		}

		return false;
	}

	/**
	 * Return number corresponding to iso code
	 *
	 * @param   string  $code  3 letters code (e.g USD, EUR,...)
	 *
	 * @return string
	 */
	public static function getIsoNumber($code)
	{
		$cur = self::get_iso_4217_currency_codes();

		if (in_array($code, array_keys($cur)))
		{
			return $cur[$code]->number;
		}
		else
		{
			return false;
		}
	}

	/**
	 * get currencies as options, with code as value
	 *
	 * @return array
	 */
	public static function getCurrencyOptions()
	{
		$cur = self::get_iso_4217_currency_codes();
		$options = array();

		foreach ($cur as $code => $currency)
		{
			$options[] = JHTML::_('select.option', $code, $code . ' - ' . $currency->name);
		}

		return $options;
	}

	/**
	 * Check if given currency code is valid
	 *
	 * @param   string  $currency  iso 4217 currency code
	 *
	 * @return boolean true if exists
	 */
	public static function isValid($currency)
	{
		if (!$currency)
		{
			return false;
		}

		$cur = self::get_iso_4217_currency_codes();

		return isset($cur[$currency]);
	}
}

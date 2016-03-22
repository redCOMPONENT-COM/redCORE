<?php
/**
 * @package     Redcore
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
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
	/**
	 * Currency cache
	 *
	 * @var  array
	 */
	protected static $currencies = array();

	/**
	 * Reload currencies
	 *
	 * @var  bool
	 */
	protected static $completeLoad = false;

	/**
	 * Get Currency object by symbol or id
	 *
	 * @param   mixed  $currency  currency symbol or id
	 *
	 * @return null/object
	 */
	public static function getCurrency($currency = 'DKK')
	{
		$currency = is_numeric($currency) ? (int) $currency : trim($currency);

		if (!$currency)
		{
			return null;
		}

		if (!isset(self::$currencies[(string) $currency]))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__redcore_currency'));

			if (is_numeric($currency))
			{
				$query->where('id = ' . (int) ($currency));
			}
			else
			{
				$query->where('alpha3 = ' . $db->q($currency));
			}

			$db->setQuery($query);
			$item = $db->loadObject();

			if ($item)
			{
				$item->precision = $item->decimals;
				self::$currencies[$currency] = $item;
			}
		}

		return isset(self::$currencies[$currency]) ? self::$currencies[$currency] : null;
	}

	/**
	 * Load all currencies
	 *
	 * @return array
	 */
	public static function getAllCurrencies()
	{
		if (self::$completeLoad == false)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__redcore_currency'));

			$db->setQuery($query);
			$items = $db->loadObjectList();

			foreach ($items as $item)
			{
				$item->precision = $item->decimals;
				self::$currencies[$item->alpha3] = $item;
			}

			self::$completeLoad = true;
		}

		return self::$currencies;
	}

	/**
	 * Formatted Price
	 *
	 * @param   float   $amount          Amount
	 * @param   string  $currencySymbol  Currency symbol
	 * @param   bool    $appendSymbol    Append currency symbol to results?
	 *
	 * @return  string  Formatted Price
	 */
	public static function getFormattedPrice($amount, $currencySymbol = 'DKK', $appendSymbol = true)
	{
		$price = '';
		$currency = self::getCurrency($currencySymbol);

		if (!$currency)
		{
			return $amount;
		}

		if (is_numeric($amount))
		{
			/*
			 * $currency->decimals: Sets the number of decimal points.
			 * $currency->decimal_separator: Sets the separator for the decimal point.
			 * $currency->thousands_separator: Sets the thousands separator
			 */
			$price = number_format(
				(double) $amount,
				$currency->decimals,
				$currency->decimal_separator,
				$currency->thousands_separator
			);

			// Sets blank space between the currency symbol and the price
			$blankSpace = ($currency->blank_space == 1) ? ' ' : '';

			if ($currency->symbol_position == 0 && $appendSymbol)
			{
				$price = $currency->symbol . $blankSpace . $price;
			}
			elseif ($currency->symbol_position == 1 && $appendSymbol)
			{
				$price .= $blankSpace . $currency->symbol;
			}
		}

		return $price;
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
		$cur = self::getAllCurrencies();

		foreach ($cur as $code => $currency)
		{
			if ($currency->numeric == $number)
			{
				return $code;
			}
		}

		return false;
	}

	/**
	 * Return number corresponding to iso code
	 *
	 * @param   string  $code  iso 4217 3 letters currency code
	 *
	 * @return string
	 */
	public static function getIsoNumber($code)
	{
		$currency = !empty($code) ? self::getCurrency($code) : null;

		return $currency ? $currency->numeric : $code;
	}

	/**
	 * get currencies as options, with code as value
	 *
	 * @return array
	 */
	public static function getCurrencyOptions()
	{
		$cur = self::getAllCurrencies();
		$options = array();

		foreach ($cur as $code => $currency)
		{
			$options[] = JHTML::_('select.option', $code, $code . ' - ' . $currency->name);
		}

		return $options;
	}

	/**
	 * Return decimal precision in number of digits
	 *
	 * @param   string  $code  iso 4217 3 letters currency code
	 *
	 * @throws OutOfRangeException
	 *
	 * @return int
	 */
	public static function getPrecision($code)
	{
		$currency = self::getCurrency($code);

		return $currency ? $currency->decimals : 0;
	}

	/**
	 * Check if given currency code is valid
	 *
	 * @param   string  $code  iso 4217 3 letters currency code
	 *
	 * @return boolean true if exists
	 */
	public static function isValid($code)
	{
		if (!$code)
		{
			return false;
		}

		self::getCurrency($code);

		return !empty(self::$currencies[$code]);
	}
}

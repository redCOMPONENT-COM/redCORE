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
final class RHelperCountry
{
	protected static $codes;

	/**
	 * Return supported currencies from http://www.currency-iso.org
	 *
	 * @return array
	 */
	protected static function get_iso_codes()
	{
		if (!self::$codes)
		{
			// Xml file was pulled from http://www.currency-iso.org/dam/downloads/table_a1.xml. Should be refreshed sometimes !
			$xml = file_get_contents(dirname(__FILE__) . '/countries_iso_3166_alpha_2_table.xml');
			$obj = new SimpleXMLElement($xml);

			$codes = array();

			foreach ($obj->children() as $country)
			{
				$code = (string) $country->{'ISO_3166-1_Alpha-2_Code_element'};

				if ($code && !isset($currencies[$code]))
				{
					$obj = new stdclass;
					$obj->name   = (string) $country->{'ISO_3166-1_Country_name'};
					$obj->code   = (string) $country->{'ISO_3166-1_Alpha-2_Code_element'};

					$codes[$code] = $obj;
				}
			}

			self::$codes = $codes;
		}

		return self::$codes;
	}

	/**
	 * get currencies as options, with code as value
	 *
	 * @return array
	 */
	public static function getOptions()
	{
		$cur = self::get_iso_codes();
		$options = array();

		foreach ($cur as $code => $country)
		{
			$options[] = JHTML::_('select.option', $code, $code . ' - ' . $country->name);
		}

		return $options;
	}

	/**
	 * Check if given country code is valid
	 *
	 * @param   string  $country  iso 3166 country code
	 *
	 * @return boolean true if exists
	 */
	public static function isValid($country)
	{
		if (!$country)
		{
			return false;
		}

		$cur = self::get_iso_codes();

		return isset($cur[$country]);
	}
}

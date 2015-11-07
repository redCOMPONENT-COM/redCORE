<?php
/**
 * @package     Redcore
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Country helper class.
 *
 * @package     Redcore
 * @subpackage  Helper
 * @since       1.0
 */
final class RHelperCountry
{
	/**
	 * Country cache
	 *
	 * @var  array
	 */
	protected static $countries = array();

	/**
	 * Reload countries
	 *
	 * @var  bool
	 */
	protected static $completeLoad = false;

	/**
	 * Get Country object by symbol or id
	 *
	 * @param   mixed  $country  country symbol or id
	 *
	 * @return null/object
	 */
	public static function getCountry($country = 'DK')
	{
		if (!isset(self::$countries[(string) $country]))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__redcore_country'));

			if (is_numeric($country))
			{
				$query->where('id = ' . (int) ($country));
			}
			else
			{
				$query->where('alpha2 = ' . $db->q($country));
			}

			$db->setQuery($query);
			$item = $db->loadObject();

			if ($item)
			{
				$item->code = $item->alpha2;
			}

			self::$countries[$country] = $item;
		}

		return self::$countries[$country];
	}

	/**
	 * Load all countries
	 *
	 * @return array
	 */
	public static function getAllCountries()
	{
		if (self::$completeLoad == false)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__redcore_country'));

			$db->setQuery($query);
			$items = $db->loadObjectList();

			foreach ($items as $item)
			{
				$item->code = $item->alpha2;
				self::$countries[$item->alpha2] = $item;
			}

			self::$completeLoad = true;
		}

		return self::$countries;
	}

	/**
	 * get countries as options, with code as value
	 *
	 * @return array
	 */
	public static function getOptions()
	{
		$cur = self::getAllCountries();
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

		self::getCountry($country);

		return !empty(self::$countries[$country]);
	}
}

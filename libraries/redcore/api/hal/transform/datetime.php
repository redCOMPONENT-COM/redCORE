<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Transform api output
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.2
 */
class RApiHalTransformDatetime extends RApiHalTransformBase
{
	/**
	 * Method to transform an internal representation to an external one.
	 *
	 * @param   string  $definition  Field definition.
	 *
	 * @return string The date string in ISO 8601 format.
	 */
	public static function toExternal($definition)
	{
		if (empty($definition) || $definition == '0000-00-00 00:00:00')
		{
			return '';
		}

		$date = new JDate($definition);

		return $date->toISO8601();
	}
}

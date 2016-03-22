<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * Transform api output
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.4
 */
class RApiHalTransformJson extends RApiHalTransformBase
{
	/**
	 * Method to transform an internal representation to an external one.
	 *
	 * @param   mixed  $definition  Field definition.
	 *
	 * @return array Transformed value.
	 */
	public static function toExternal($definition)
	{
		// Check for defined constants
		if (!defined('JSON_UNESCAPED_SLASHES'))
		{
			define('JSON_UNESCAPED_SLASHES', 64);
		}

		return ((is_string($definition) || is_object($definition)) && self::isJson($definition)) ?
			$definition : json_encode($definition, JSON_UNESCAPED_SLASHES);
	}

	/**
	 * Method to transform an external representation to an internal one.
	 *
	 * @param   mixed  $definition  Field definition.
	 *
	 * @return array Transformed value.
	 */
	public static function toInternal($definition)
	{
		return json_decode($definition);
	}

	/**
	 * Checks string to see if it is already a json
	 *
	 * @param   mixed  $string  String value
	 *
	 * @return boolean True if the string is already json
	 */
	public static function isJson($string)
	{
		json_decode($string);

		return (json_last_error() == JSON_ERROR_NONE);
	}
}

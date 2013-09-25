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
 * Array helper class.
 *
 * @package     Redcore
 * @subpackage  Helper
 * @since       1.0
 */
final class RHelperArray
{
	/**
	 * Quote an array of values.
	 *
	 * @param   array  $values  The values.
	 *
	 * @return  array  The quoted values
	 */
	public static function quote(array $values)
	{
		$db = JFactory::getDbo();

		return array_map(
			function ($value) use ($db) {
				return $db->quote($value);
			},
			$values
		);
	}
}

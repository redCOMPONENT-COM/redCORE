<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2021 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * Transform api output
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.2
 */
class RApiHalTransformBoolean extends RApiHalTransformBase
{
	/**
	 * Method to transform an internal representation to an external one.
	 *
	 * @param   string  $definition  Field definition.
	 *
	 * @return boolean Transformed value.
	 */
	public static function toExternal($definition)
	{
		if ($definition == 'true' || $definition == '1')
		{
			return true;
		}

		if ($definition == 'false' || $definition == '0')
		{
			return false;
		}

		return (boolean) $definition;
	}
}

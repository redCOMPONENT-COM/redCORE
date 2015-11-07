<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
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
class RApiHalTransformTarget extends RApiHalTransformBase
{
	/**
	 * Method to transform an internal representation to an external one.
	 *
	 * @param   string  $definition  Field definition.
	 *
	 * @return string Transformed value.
	 */
	public static function toExternal($definition)
	{
		switch ($definition)
		{
			case '':
				$return = 'global';
				break;
			case 0:
				$return = 'parent';
				break;
			case 1:
				$return = 'new';
				break;
			case 2:
				$return = 'popup';
				break;
			case 3:
				$return = 'modal';
				break;
			default:
				$return = 'undefined';
				break;
		}

		return $return;
	}
}

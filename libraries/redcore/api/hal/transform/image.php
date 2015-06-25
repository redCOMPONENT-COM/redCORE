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
 * @since       1.6.0
 */
class RApiHalTransformImage extends RApiHalTransformBase
{
	/**
	 * Method to transform an internal representation to an external one.
	 *
	 * @param   mixed  $definition  Field definition.
	 *
	 * @return mixed Transformed value.
	 */
	public static function toExternal($definition)
	{
		// This is already converted to image type so we are returning it as is
		if (!is_array($definition))
		{
			return $definition;
		}

		return JUri::root(true) . '/' . RedshopbHelperThumbnail::getFullImagePath($definition['name'], $definition['section']);
	}

	/**
	 * Method to transform an external representation to an internal one.
	 *
	 * @param   mixed  $definition  Field definition.
	 *
	 * @return mixed Transformed value.
	 */
	public static function toInternal($definition)
	{
		// This is already converted to image type so we are returning it as is
		if (!is_array($definition))
		{
			return $definition;
		}

		$id = (int) preg_replace('/\D/', '', $definition['id']);

		return RedshopbHelperThumbnail::savingImage(
			$definition['fullPath'], $definition['fullPath'], $id, true, $definition['section']
		);
	}
}

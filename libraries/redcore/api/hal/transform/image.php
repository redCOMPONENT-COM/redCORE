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

		$fileName = $definition['name'];
		$section = $definition['section'];

		return JUri::root(true) . '/' . RedshopbHelperThumbnail::getFullImagePath($fileName, $section);
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

		$fullPath = $definition['name'];
		$fileName = $definition['name'];
		$id = (int) preg_replace('/\D/', '', $definition['id']);
		$section = $definition['section'];

		return RedshopbHelperThumbnail::savingImage($fullPath, $fileName, $id, true, $section);
	}
}

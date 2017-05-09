<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

/**
 * Helper to render a JLayout object, storing a base path
 *
 * @see    https://docs.joomla.org/Sharing_layouts_across_views_or_extensions_with_JLayout
 * @since  3.1
 */
class RLayoutHelper extends JLayoutHelper
{
	/**
	 * Method to render the layout.
	 *
	 * @param   string  $layoutFile   Dot separated path to the layout file, relative to base path
	 * @param   object  $displayData  Object which properties are used inside the layout file to build displayed output
	 * @param   string  $basePath     Base path to use when loading layout files
	 * @param   mixed   $options      Optional custom options to load. Registry or array format
	 *
	 * @return  string
	 *
	 * @since   3.1
	 */
	public static function render($layoutFile, $displayData = null, $basePath = '', $options = null)
	{
		$basePath = empty($basePath) ? self::$defaultBasePath : $basePath;

		// Make sure we send null to JLayoutFile if no path set
		$basePath = empty($basePath) ? null : $basePath;
		$layout = new RLayoutFile($layoutFile, $basePath, $options);

		return $layout->render($displayData);
	}
}

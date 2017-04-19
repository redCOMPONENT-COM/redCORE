<?php
/**
 * @package     Redcore.Libraries
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

/**
 * Helper to render a RLayout object, storing a base path.
 * Based on JLayout, introduced in Joomla! 3.2.0.
 *
 * @see    https://docs.joomla.org/Sharing_layouts_across_views_or_extensions_with_JLayout
 * @since  1.8.8
 */
class RLayoutHelper
{
	/**
	 * A default base path that will be used if none is provided when calling the render method.
	 * Note that JLayoutFile itself will defaults to JPATH_ROOT . '/layouts' if no basePath is supplied at all
	 *
	 * @var    string
	 * @since  1.8.8
	 */
	public static $defaultBasePath = '';

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
	 * @since   1.8.8
	 */
	public static function render($layoutFile, $displayData = null, $basePath = '', $options = null)
	{
		$basePath = empty($basePath) ? self::$defaultBasePath : $basePath;

		// Make sure we send null to RLayoutFile if no path set
		$basePath = empty($basePath) ? null : $basePath;
		$layout = new RLayoutFile($layoutFile, $basePath, $options);
		$renderedLayout = $layout->render($displayData);

		return $renderedLayout;
	}

	/**
	 * Method to render a layout with debug info
	 *
	 * @param   string  $layoutFile   Dot separated path to the layout file, relative to base path
	 * @param   object  $displayData  Object which properties are used inside the layout file to build displayed output
	 * @param   string  $basePath     Base path to use when loading layout files
	 * @param   mixed   $options      Optional custom options to load. Registry or array format
	 *
	 * @return  string
	 *
	 * @since   1.8.8
	 */
	public static function debug($layoutFile, $displayData = null, $basePath = '', $options = null)
	{
		$basePath = empty($basePath) ? self::$defaultBasePath : $basePath;

		// Make sure we send null to RLayoutFile if no path set
		$basePath = empty($basePath) ? null : $basePath;
		$layout = new RLayoutFile($layoutFile, $basePath, $options);
		$renderedLayout = $layout->debug($displayData);

		return $renderedLayout;
	}
}

<?php
/**
 * @package     RedRad
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDRAD') or die;

/**
 * Layout helper library
 *
 * @package     RedRad
 * @subpackage  Layout
 * @since       1.0
 */
class RLayoutHelper
{
	/**
	 * Method to render the layout.
	 *
	 * @param   string  $layoutFile   Dot separated path to the layout file, relative to base path
	 * @param   object  $displayData  Object which properties are used inside the layout file to build displayed output
	 * @param   string  $basePath     Base path to use when loading layout files
	 * @param   string  $component    Name of the component to use as source for the layout
	 * @param   mixed   $client       Client to search the layout (0, 'site' , 1, 'admin')
	 *
	 * @return  string
	 */
	public static function render($layoutFile, $displayData = null, $basePath = '', $component = 'auto', $client = 'auto')
	{
		$basePath = empty($basePath) ? null : $basePath;

		// Make sure we send null to JLayoutFile if no path set
		$layout = new RLayout($layoutFile, $component, $client);

		if (!empty($basePath))
		{
			$layout->addIncludePaths($basePath);
		}

		$renderedLayout = $layout->render($displayData);

		return $renderedLayout;
	}
}

<?php
/**
 * @package     Redcore
 * @subpackage  Module
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

/**
 * redCORE Module Helper
 *
 * @package     Redcore
 * @subpackage  Module
 * @since       1.0
 */
class RModuleHelper extends JModuleHelper
{
	/**
	 * Get the path to a layout for a module.  Includes framework-specific prefixes
	 *
	 * @param   string  $module  The name of the module
	 * @param   string  $layout  The name of the module layout. If alternative layout, in the form template:filename.
	 *
	 * @return  string  The path to the module layout
	 *
	 * @since   1.5
	 */
	public static function getLayoutPath($module, $layout = 'default')
	{
		// Uses the parent function if no framework suffix is present
		if (RHtmlMedia::$frameworkSuffix == '')
		{
			return parent::getLayoutPath($module, $layout);
		}

		$template      = JFactory::getApplication()->getTemplate();
		$defaultLayout = $layout;

		if (strpos($layout, ':') !== false)
		{
			// Get the template and file name from the string
			$temp          = explode(':', $layout);
			$template      = ($temp[0] == '_') ? $template : $temp[0];
			$layout        = $temp[1];
			$defaultLayout = $temp[1] ?: 'default';
		}

		// Build the template and base path for the layout - specific template framework
		$tPath = JPATH_THEMES . '/' . $template . '/html/' . $module . '/' . $layout . '.' . RHtmlMedia::$frameworkSuffix . '.php';
		$bPath = JPATH_BASE . '/modules/' . $module . '/tmpl/' . $defaultLayout . '.' . RHtmlMedia::$frameworkSuffix . '.php';
		$dPath = JPATH_BASE . '/modules/' . $module . '/tmpl/default.' . RHtmlMedia::$frameworkSuffix . '.php';

		if (file_exists($tPath))
		{
			return $tPath;
		}

		if (file_exists($bPath))
		{
			return $bPath;
		}

		if (file_exists($dPath))
		{
			return $dPath;
		}

		// If no specific-framework layout was found, use the default function
		return parent::getLayoutPath($module, $layout);
	}
}

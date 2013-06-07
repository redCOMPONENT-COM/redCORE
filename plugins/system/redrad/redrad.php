<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Redrad
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * System plugin for redRAD
 *
 * @package     Joomla.Plugin
 * @subpackage  System
 * @since       1.0
 */
class PlgSystemRedRad extends JPlugin
{
	/**
     * Method to register custom library.
     *
     * @return  void
     */
	public function onAfterInitialise()
	{
		require_once JPATH_REDRAD . 'bootstrap.php';

		// For Joomla! 2.5 compatibility we add some core functions
		if (version_compare(JVERSION, '3.0', '<'))
		{
			JLoader::registerPrefix('J',  JPATH_REDRAD . '/joomla');
		}
	}
}

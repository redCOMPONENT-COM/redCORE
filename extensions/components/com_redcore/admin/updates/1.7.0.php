<?php
/**
 * @package     Redcore
 * @subpackage  Upgrade
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Upgrade script for redCORE.
 *
 * @package     Redcore
 * @subpackage  Upgrade
 * @since       1.5
 */
class Com_RedcoreUpdateScript_1_7_0
{
	/**
	 * Performs the upgrade after initial Joomla update for this version
	 *
	 * @param   JInstallerAdapter  $parent  Class calling this method
	 *
	 * @return  bool
	 */
	public function execute($parent)
	{
		// Clean media folder because of the redCORE restructure
		$folders = array(
			JPATH_SITE . '/media/redcore/css/lib',
			JPATH_SITE . '/media/redcore/js/lib',
		);

		$files = array(
			JPATH_SITE . '/media/redcore/css/jquery.searchtools.css',
			JPATH_SITE . '/media/redcore/css/rdatepicker.css',
			JPATH_SITE . '/media/redcore/js/jquery.searchtools.min.js',
		);

		if (!empty($folders))
		{
			foreach ($folders as $path)
			{
				if (JFolder::exists($path))
				{
					JFolder::delete($path);
				}
			}
		}

		if (!empty($files))
		{
			foreach ($files as $path)
			{
				if (JFile::exists($path))
				{
					JFile::delete($path);
				}
			}
		}

		return true;
	}
}

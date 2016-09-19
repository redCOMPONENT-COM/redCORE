<?php
/**
 * @package     Redcore
 * @subpackage  Upgrade
 *
 * @copyright   Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
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
class Com_RedcoreUpdateScript_1_8_4
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
		// Clean old library files because we moved them to the extension folder
		$files = array(
			JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_redcore.ini',
			JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_redcore.sys.ini',
			JPATH_ADMINISTRATOR . '/language/da-DK/da-DK.com_redcore.ini',
			JPATH_ADMINISTRATOR . '/language/da-DK/da-DK.com_redcore.sys.ini',
		);

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

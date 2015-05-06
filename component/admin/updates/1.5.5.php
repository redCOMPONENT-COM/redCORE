<?php
/**
 * @package     Redshopb
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
class Com_RedcoreUpdateScript_1_5_5
{
	/**
	 * Performs the upgrade after initial Joomla update for this version
	 *
	 * @param   JInstallerAdapter  $parent  Class calling this method
	 *
	 * @return  bool
	 */
	public function executeAfterUpdate($parent)
	{
		// Add currency data
		$currencySqlPath = JPATH_ADMINISTRATOR . '/components/com_redcore/sql/install/mysql/currency.sql';
		RHelperQuery::executeFileQueries($currencySqlPath);

		// Add country data
		$countrySqlPath = JPATH_ADMINISTRATOR . '/components/com_redcore/sql/install/mysql/country.sql';
		RHelperQuery::executeFileQueries($countrySqlPath);

		return true;
	}
}

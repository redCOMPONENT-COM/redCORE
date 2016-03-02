<?php
/**
 * @package     redCORE
 * @subpackage  Cept
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

class InstallJoomlaCest
{
	public function installJoomla(\AcceptanceTester $I)
	{
		$I->wantToTest('Joomla 3 Installation');
		$I->installJoomlaRemovingInstallationFolder();
		$I->doAdministratorLogin();
		// @todo: uncomment when testing against joomla 3.5 and beyond $I->disableStatistics();
		$I->setErrorReportingToDevelopment();
	}
}

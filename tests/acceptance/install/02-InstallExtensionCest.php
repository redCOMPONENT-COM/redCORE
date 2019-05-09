<?php
/**
 * @package     redCORE
 * @subpackage  Cest
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
use Step\Acceptance\AbstractStep as AdminTester;

/**
 * Class InstallExtensionCest
 */
class InstallExtensionCest
{
	/**
	 * @param AcceptanceTester $I
	 * @throws Exception
	 */
	public function install(\AcceptanceTester $I)
	{
		$I->wantToTest('redCORE installation in Joomla 3');
		$I->doAdministratorLogin();
		$path = $I->getConfiguration('install packages url');
		$I->installExtensionFromUrl($path . 'redCORE.zip');
	}

	/**
	 * @param AdminTester $I
	 * @throws Exception
	 */
	public function activateWebservices(AdminTester $I)
	{
		$I->wantToTest('Active Webservices');
		$I->doAdministratorLogin();
		$I->activateWebservices();
		$I->installWebservices();
	}
}

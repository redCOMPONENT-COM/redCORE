<?php
/**
 * @package     RedCORE
 * @subpackage  Cest
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
use Step\Acceptance\AbstractStep as AdminTester;

/**
 * Class UninstallExtensionCest
 */
class UninstallExtensionCest
{
	/**
	 * @param AdminTester $I
	 * @throws Exception
	 */
	public function uninstallExtension(AdminTester $I)
	{
		$I->wantTo('Uninstall redCORE Extensions');
		$I->doAdministratorLogin();
		$I->uninstallRedCore();
	}
}

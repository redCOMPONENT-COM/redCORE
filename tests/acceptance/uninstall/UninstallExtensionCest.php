<?php
/**
 * @package     RedCORE
 * @subpackage  Cest
 * @copyright   Copyright (C) 2008 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Class UninstallExtensionCest
 */
class UninstallExtensionCest
{
    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
	public function uninstallExtension(\AcceptanceTester $I)
	{
		$I->wantTo('Uninstall redCORE Extensions');
		$I->doAdministratorLogin();
		$I->uninstallExtension('redCORE');
	}
}

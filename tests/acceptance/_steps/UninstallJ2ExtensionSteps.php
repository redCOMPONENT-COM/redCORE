<?php
/**
 * @package     redCORE
 * @subpackage  Step Class
 * @copyright   Copyright (C) 2012 - 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace AcceptanceTester;
/**
 * Class UninstallJ2ExtensionSteps
 *
 * @package  AcceptanceTester
 *
 * @link     http://codeception.com/docs/07-AdvancedUsage#StepObjects
 *
 * @since    1.4
 */
class UninstallJ2ExtensionSteps extends \AcceptanceTester
{
	/**
	 * Function to Uninstall Extension
	 *
	 * @param   String  $extensionName  Name of the Extension
	 *
	 * @return void
	 */
	public function uninstallExtension($extensionName)
	{
		$I = $this;
		$I->amOnPage(\ExtensionManagerPage::$URL);
		$I->click("Manage");
		$I->fillField(\ExtensionManagerPage::$extensionSearchJ2, $extensionName);
		$I->click(\ExtensionManagerPage::$searchButtonJ2);
		$name = $I->grabTextFrom(\ExtensionManagerPage::$extensionTableJ2);

		while (strtolower($name) != strtolower($extensionName))
		{
			$I->click(\ExtensionManagerPage::$firstCheck);
			$I->click("Uninstall");
			$I->seeElement(\ExtensionManagerPage::$uninstallSuccessMessageJ2);
			$name = $I->grabTextFrom(\ExtensionManagerPage::$extensionTableJ2);
		}

		$I->click(\ExtensionManagerPage::$firstCheck);
		$I->click("Uninstall");
		$I->seeElement(\ExtensionManagerPage::$uninstallSuccessMessageJ2);
	}
}

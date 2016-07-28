<?php
/**
 * @package     RedCORE
 * @subpackage  Cept
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Class UninstallExtensionCest
 *
 * @package  AcceptanceTester
 *
 * @link     http://codeception.com/docs/07-AdvancedUsage
 *
 * @since    1.4
 */
class UninstallExtensionCest
{
	/**
	 * Function to Uninstall redSHOP extension
	 *
	 * @return void
	 */
	public function uninstallExtension(AcceptanceTester $I, $scenario)
	{
		$I->wantTo('Uninstall redCORE Extensions');
		$I->doAdministratorLogin();
		$I->amOnPage('/administrator/index.php?option=com_installer&view=manage');
		$I->fillField(['id' => 'filter_search'], 'redCORE - component');
		$I->pressKey(['id' => 'filter_search'], WebDriverKeys::ENTER);
		$I->waitForElement(['id' => 'manageList']);
		$I->click(['xpath' => "//input[@id='cb0']"]);
		$I->click(['xpath' => "//div[@id='toolbar-delete']/button"]);
		$I->acceptPopup();
		$I->waitForText('Uninstalling the component was successful', 60, ['id' => 'system-message-container']);
		$I->see('Uninstalling the component was successful', ['id' => 'system-message-container']);
		$I->fillField(['id' => 'filter_search'], 'redCORE - component');
		$I->pressKey(['id' => 'filter_search'], WebDriverKeys::ENTER);
		$I->waitForText('There are no extensions installed matching your query.', 60, ['class' => 'alert-no-items']);
		$I->see('There are no extensions installed matching your query.', ['class' => 'alert-no-items']);
	}
}

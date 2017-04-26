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
		$I->installJoomlaMultilingualSite();
		$I->doAdministratorLogin();
		$I->disableStatistics();
		$I->setErrorReportingToDevelopment();
	}

	public function disableTemplateFloatingToolbars(AcceptanceTester $I)
	{
		$I->am('administrator');
		$I->wantTo('disable the floating template toolbars');
		$I->doAdministratorLogin();
		$I->waitForText('Control Panel', 60, ['css' => 'h1']);
		$I->click(['link' => 'Extensions']);
		$I->waitForElement(['link' => 'Templates'], 60);
		$I->click(['link' => 'Templates']);
		$I->waitForText('Templates: Styles', 60, ['css' => 'h1']);
		$I->selectOptionInChosen('#client_id', 'Administrator');
		$I->waitForText('Templates: Styles (Administrator)', 60, ['css' => 'h1']);
		$I->click(['link' => 'isis - Default']);
		$I->waitForText('Templates: Edit Style', 60, ['css' => 'h1']);
		$I->click(['link' => 'Advanced']);
		$I->waitForElement(['css' => "label[data-original-title='Status Module Position']"], 60);
		$I->executeJS("window.scrollTo(0, document.body.scrollHeight);");
		$I->selectOptionInChosen('Status Module Position', 'Top');
		$I->selectOptionInRadioField('Pinned Toolbar', 'No');
		$I->click('Save & Close');
		$I->waitForText('Style successfully saved.', 60, ['id' => 'system-message-container']);
		$I->see('Style successfully saved.', ['id' => 'system-message-container']);
	}
}

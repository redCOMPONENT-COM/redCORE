<?php
/**
 * @package     redCORE
 * @subpackage  Cest
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Step\Acceptance\TemplateSteps as Template;

/**
 * Class InstallJoomlaCest
 */
class InstallJoomlaCest
{
	/**
	 * @param AcceptanceTester $I
	 * @throws Exception
	 */
	public function installJoomla(\AcceptanceTester $I)
	{
		$I->wantToTest('Joomla 3 Installation');
		$I->installJoomlaMultilingualSite();
		$I->doAdministratorLogin();
		$I->disableStatistics();
		$I->setErrorReportingToDevelopment();
	}

	/**
	 * @param AdminTester $I
	 * @throws Exception
	 */
	public function disableTemplateFloatingToolbars(Template $I)
	{
		$I->wantToTest('Disable Template Floating Toolbars');
		$I->disableTemplateFloatingToolbars();
	}
}

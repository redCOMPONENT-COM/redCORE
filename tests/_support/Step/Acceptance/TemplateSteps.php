<?php
/**
 * @package     redCORE
 * @subpackage  Step
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @licence     GNU General Public License version 2 or later; see LICENSE.TXT
 */
namespace Step\Acceptance;

use Page\TemplatePage as disableToolbars;

/**
 * Class TemplateSteps
 * @package Step\Acceptance
 */
class TemplateSteps extends \AcceptanceTester
{
	/**
	 * @throws \Exception
	 */
	public function disableTemplateFloatingToolbars()
	{
		$I = $this;
		$I->comment('I disable the template floating toolbars');
		$I->am(disableToolbars::$amAdmin);
		$I->wantTo('disable the floating template toolbars');
		$I->doAdministratorLogin();
		$I->amOnPage(disableToolbars::$amAdmin);
		$I->waitForText(disableToolbars::$cPanel, 60, disableToolbars::$h1);
		$I->click(disableToolbars::$buttonExtensions);
		$I->waitForElement(disableToolbars::$templatesElement, 60);
		$I->click(disableToolbars::$buttonTemplates);
		$I->waitForText(disableToolbars::$templatesStyles, 60, disableToolbars::$h1);
		$I->selectOptionInChosen(disableToolbars::$clientId, disableToolbars::$labelAdmin);
		$I->waitForText(disableToolbars::$templatesStylesAdmin, 60, disableToolbars::$h1);
		$I->click(disableToolbars::$isisDefault);
		$I->waitForText(disableToolbars::$templatesEditStyle, 60, disableToolbars::$h1);
		$I->click(disableToolbars::$linkAdvanced);
		$I->waitForElement(disableToolbars::$labelModulePosition, 60);
		$I->executeJS("window.scrollTo(0, document.body.scrollHeight);");
		$I->selectOptionInChosen(disableToolbars::$statusModulePosition, disableToolbars::$positionTop);
		$I->selectOptionInRadioField(disableToolbars::$pinnedToolbar, disableToolbars::$chooseNo);
		$I->click(disableToolbars::$buttonSaveAndClose);
		$I->waitForText(disableToolbars::$templatesStyleSaved, 60, disableToolbars::$messageContainer);
		$I->waitForText(disableToolbars::$templatesStyleSaved, 60, disableToolbars::$messageContainer);
	}
}

<?php
/**
 * @package     redCORE
 * @subpackage  Step
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @licence     GNU General Public License version 2 or later; see LICENSE.TXT
 */
namespace Step\Acceptance;

use Page\TemplatePage;

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
		$I->am(TemplatePage::$amAdmin);
		$I->wantTo('disable the floating template toolbars');
		$I->doAdministratorLogin();
		$I->amOnPage(TemplatePage::$amAdmin);
		$I->waitForText(TemplatePage::$cPanel, 60, TemplatePage::$h1);
		$I->click(TemplatePage::$buttonExtensions);
		$I->waitForElement(TemplatePage::$templatesElement, 60);
		$I->click(TemplatePage::$buttonTemplates);
		$I->waitForText(TemplatePage::$templatesStyles, 60, TemplatePage::$h1);
		$I->selectOptionInChosen(TemplatePage::$clientId, TemplatePage::$labelAdmin);
		$I->waitForText(TemplatePage::$templatesStylesAdmin, 60, TemplatePage::$h1);
		$I->click(TemplatePage::$isisDefault);
		$I->waitForText(TemplatePage::$templatesEditStyle, 60, TemplatePage::$h1);
		$I->click(TemplatePage::$linkAdvanced);
		$I->waitForElement(TemplatePage::$labelModulePosition, 60);
		$I->executeJS("window.scrollTo(0, document.body.scrollHeight);");
		$I->selectOptionInChosen(TemplatePage::$statusModulePosition, TemplatePage::$positionTop);
		$I->selectOptionInRadioField(TemplatePage::$pinnedToolbar, TemplatePage::$chooseNo);
		$I->click(TemplatePage::$buttonSaveAndClose);
		$I->waitForText(TemplatePage::$templatesStyleSaved, 60, TemplatePage::$messageContainer);
		$I->waitForText(TemplatePage::$templatesStyleSaved, 60, TemplatePage::$messageContainer);
	}
}

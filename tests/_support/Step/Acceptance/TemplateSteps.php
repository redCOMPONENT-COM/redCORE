<?php
/**
 * @package     redCORE
 * @subpackage  Step
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @licence     GNU General Public License version 2 or later; see LICENSE.TXT
 */
namespace Step\Acceptance;

use Page\TemplatePage as Template;

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
		$I->am(Template::$amAdmin);
		$I->wantTo('disable the floating template toolbars');
		$I->doAdministratorLogin();
		$I->amOnPage(Template::$amAdmin);
		$I->waitForText(Template::$cPanel, 60, Template::$h1);
		$I->click(Template::$buttonExtensions);
		$I->waitForElement(Template::$templatesElement, 60);
		$I->click(Template::$buttonTemplates);
		$I->waitForText(Template::$templatesStyles, 60, Template::$h1);
		$I->selectOptionInChosen(Template::$clientId, Template::$labelAdmin);
		$I->waitForText(Template::$templatesStylesAdmin, 60, Template::$h1);
		$I->click(Template::$isisDefault);
		$I->waitForText(Template::$templatesEditStyle, 60, Template::$h1);
		$I->click(Template::$linkAdvanced);
		$I->waitForElement(Template::$labelModulePosition, 60);
		$I->executeJS("window.scrollTo(0, document.body.scrollHeight);");
		$I->selectOptionInChosen(Template::$statusModulePosition, Template::$positionTop);
		$I->selectOptionInRadioField(Template::$pinnedToolbar, Template::$chooseNo);
		$I->click(Template::$buttonSaveAndClose);
		$I->waitForText(Template::$templatesStyleSaved, 60, Template::$messageContainer);
		$I->waitForText(Template::$templatesStyleSaved, 60, Template::$messageContainer);
	}
}

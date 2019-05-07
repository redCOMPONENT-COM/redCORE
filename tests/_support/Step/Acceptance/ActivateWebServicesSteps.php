<?php
/**
 * @package     redCORE
 * @subpackage  Step
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Step\Acceptance;

use Page\ActivateWebServicesPage as ActWebPage;

/**
 * Class ActivateWebServicesSteps
 * @package Step\Acceptance
 */
class ActivateWebServicesSteps extends \AcceptanceTester
{
	/**
	 * @throws \Exception
	 */
	public function doActivateWebservices()
	{
		$I = $this;
		$I->comment('I enable basic authentication');
		$I->amOnPage(ActWebPage::$URL);
		$I->waitForText(ActWebPage::$titleRedConf, 30, ActWebPage::$h1);
		$I->click(ActWebPage::$buttonWebservice);
		$I->waitForElementVisible(ActWebPage::$form);
		$I->executeJS("javascript:document.getElementById(\"REDCORE_WEBSERVICES_OPTIONS\").scrollIntoView();");
		$I->selectOptionInRadioField(ActWebPage::$labelWebServices, ActWebPage::$choose);
		$I->selectOptionInChosen(ActWebPage::$labelCheckUser, ActWebPage::$optional);
		$I->scrollTo(ActWebPage::$selectorFormScroll);
		$I->selectOptionInRadioField(ActWebPage::$labelSOAP, ActWebPage::$choose);
		$I->executeJS('window.scrollTo(0,0)');
		$I->click(ActWebPage::$buttonSave);
		$I->waitForText(ActWebPage::$messageSaveSuccess, 30, ActWebPage::$messageContainer);
	}

	/**
	 * @throws \Exception
	 */
	public function doWebserviceManager(){
		$I= $this;
		$I->amOnPage(ActWebPage::$URL2);
		$I->waitForText(ActWebPage::$textManager, 30, ActWebPage::$h1);
		$I->click(ActWebPage::$buttonNotInstall);
		$I->waitForElement(ActWebPage::$installElement, 30);
		$I->executeJS("javascript:window.scrollBy(0,200);");
		$I->click(ActWebPage::$buttonInstall);
		$I->waitForElement(ActWebPage::$tableForm, 60);
		$I->waitForText(ActWebPage::$pathContactWebservice1, 30, ActWebPage::returnXpath(ActWebPage::$pathContactWebservice1));
		$I->waitForText(ActWebPage::$pathContactWebservice2, 30, ActWebPage::returnXpath(ActWebPage::$pathContactWebservice2));
		$I->waitForText(ActWebPage::$pathUserWebservice, 30, ActWebPage::returnXpath(ActWebPage::$pathUserWebservice));
	}
}

<?php
/**
 * @package     redCORE
 * @subpackage  Step
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Step\Acceptance;

use Page\redCOREConfigPage as configPage;
use Page\WebserviceManagerPage as webPage;

/**
 * Class AbstractStep
 * @package Step\Acceptance
 * @since 1.10.7
 */
class AbstractStep extends \AcceptanceTester
{
	/**
	 * @throws \Exception
	 * @since 1.10.7
	 */
	public function activateWebservices()
	{
		$I = $this;
		$I->comment('I enable basic authentication');
		$I->amOnPage(configPage::$URL);
		$I->waitForText(configPage::$titleRedConf, 30, configPage::$h1);
		$I->click(configPage::$buttonWebservice);
		$I->waitForElementVisible(configPage::$form);
		$I->executeJS("javascript:document.getElementById(\"REDCORE_WEBSERVICES_OPTIONS\").scrollIntoView();");
		$I->selectOptionInRadioField(configPage::$labelWebServices, configPage::$chooseYes);
		$I->selectOptionInChosen(configPage::$labelCheckUser, configPage::$optional);
		$I->scrollTo(configPage::$selectorFormScroll);
		$I->selectOptionInRadioField(configPage::$labelSOAP, configPage::$chooseYes);
		$I->executeJS('window.scrollTo(0,0)');
		$I->click(configPage::$buttonSave);
		$I->waitForText(configPage::$messageSaveSuccess, 30, configPage::$messageContainer);
	}

	/**
	 * @throws \Exception
	 * @since 1.10.7
	 */
	public function installWebservices(){
		$I= $this;
		$I->amOnPage(webPage::$URL2);
		$I->waitForText(webPage::$textManager, 30, webPage::$h1);
		$I->click(webPage::$buttonNotInstall);
		$I->waitForElement(webPage::$installElement, 30);
		$I->executeJS("javascript:window.scrollBy(0,200);");
		$I->click(webPage::$buttonInstall);
		$I->waitForElement(webPage::$tableForm, 60);
		$I->waitForText(webPage::$pathContactWebservice1, 30, webPage::returnXpath(webPage::$pathContactWebservice1));
		$I->waitForText(webPage::$pathContactWebservice2, 30, webPage::returnXpath(webPage::$pathContactWebservice2));
		$I->waitForText(webPage::$pathUserWebservice, 30, webPage::returnXpath(webPage::$pathUserWebservice));
	}

	/**
	 * @throws \Exception
	 * @since 1.10.7
	 */
	public function Enabletranslations()
	{
		$I = $this;
		$I->wantToTest(' that there are no Warnings or Notices in redCORE');
		$I->wantTo('Activate redCORE system plugin features');
		$I->amOnPage(configPage::$URL);
		$I->waitForText(configPage::$titleRedConf, 30, configPage::$h1);
		$I->click(configPage::$tabTranslations);
		$I->waitForElementVisible(configPage::$id, 3);
		$I->executeJS("javascript:document.getElementById(\"REDCORE_TRANSLATIONS_OPTIONS\").scrollIntoView();");
		$I->selectOptionInRadioField(configPage::$enableTranslations, configPage::$chooseYes);
		$I->executeJS('window.scrollTo(0,0)');
		$I->click(configPage::$tabWebServices);
		$I->waitForElementVisible(configPage::$id);
		$I->executeJS("javascript:document.getElementById(\"REDCORE_WEBSERVICES_OPTIONS\").scrollIntoView();");
		$I->selectOptionInRadioField(configPage::$enableWebservices, configPage::$chooseYes);
		$I->executeJS('window.scrollTo(0,0)');
		$I->click(configPage::$buttonSave);
		$I->waitForText(configPage::$messageSaveSuccess, 30);
	}

	/**
	 * @throws \Exception
	 * @since 1.10.7
	 */
	public function activateTheOAuth2(){
		$I= $this;
		$I->amOnPage(configPage::$URL);
		$I->waitForText(configPage::$titleRedConf, 30, configPage::$h1);
		$I->click((configPage::$buttonOAuth2));
		$I->waitForElementVisible(configPage::$formOAuth2);
		$I->selectOptionInRadioField(configPage::$labelOauth2, configPage::$chooseYes);
		$I->click(configPage::$buttonSave);
		$I->waitForText(configPage::$messageSaveSuccess, 30, configPage::$messageContainer);
	}
}
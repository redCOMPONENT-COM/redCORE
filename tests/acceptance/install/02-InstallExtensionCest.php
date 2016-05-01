<?php
/**
* @package     redCORE
* @subpackage  Cept
* @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

class InstallExtensionCest
{
	public function install(\AcceptanceTester $I)
	{
		$I->wantToTest('redCORE installation in Joomla 3');
		$I->doAdministratorLogin();
		$path = $I->getConfiguration('install packages url');
		$I->installExtensionFromUrl($path . 'redCORE.zip');
	}

	public function activateWebservices(\AcceptanceTester $I)
	{
		$I->wantToTest('Activate the default webservices available in redCORE');
		$I->doAdministratorLogin();
		$I->comment('I enable basic authentication');
		$I->amOnPage('administrator/index.php?option=com_redcore&view=config&layout=edit&component=com_redcore');
		$I->waitForText('redCORE - component Config', 30, ['css' => 'h1']);
		$I->click(['link' => 'Webservice options']);
		$I->executeJS("javascript:document.getElementById(\"REDCORE_WEBSERVICES_OPTIONS\").scrollIntoView();");
		$I->waitForElementVisible(['id' => 'REDCORE_WEBSERVICES_OPTIONS']);
		$I->selectOptionInRadioField('Enable webservices', 'Yes');
		$I->executeJS("javascript:window.scrollBy(0,200);");
		$I->selectOptionInChosen('Check user permission against', 'Joomla - Use already defined authorization checks in Joomla');
		$I->selectOptionInRadioField('Enable SOAP Server', 'Yes');
		$I->executeJS('window.scrollTo(0,0)');
		$I->click(['xpath' => "//button[contains(@onclick, 'config.apply')]"]);
		$I->waitForText('Save success', 30, ['id' => 'system-message-container']);
		$I->amOnPage('administrator/index.php?option=com_redcore&view=webservices');
		$I->waitForText('Webservice Manager', 30, ['css' => 'H1']);
		$I->click(['class' => 'lc-not_installed_webservices']);
		$I->waitForElement(['class' => 'lc-install_all_webservices'], 60);
		$I->executeJS("javascript:window.scrollBy(0,200);");
		$I->click(['class' => 'lc-install_all_webservices']);
		$I->waitForElement(['id' => 'oauthClientsList'], 30);
		$I->see('administrator.contact.1.0.0.xml', ['class' => 'lc-webservice-file']);
		$I->see('site.contact.1.0.0.xml', ['class' => 'lc-webservice-file']);
		$I->see('site.users.1.0.0.xml', ['class' => 'lc-webservice-file']);
	}

	/*public function configure(\Step\Acceptance\redshopb2b $I)
	{
		$I->wantToTest('Edit redSHOPB2B configuration');
		$I->doAdministratorLogin();
		$I->amOnPage('/administrator/index.php?option=com_redcore&view=config&layout=edit&component=com_redshopb');
		$I->waitForElement(['link' => 'redSHOPB2B - component'], 30);
		$I->selectOptionInChosenjs('Default currency', 'Euro');
		$I->scrollUp();
		$I->click(['xpath' => "//button[contains(normalize-space(), 'Save & Close')]"]);
		$I->waitForText('Save success', 30, ['id' => 'system-message-container']);
	}

	public function activateWebservicesAndTranslations(\Step\Acceptance\redshopb2b $I)
	{
		$I->wantToTest('Activate the default webservices available in redCORE');
		$I->doAdministratorLogin();
		$I->comment('I enable basic authentication');
		$I->amOnPage('administrator/index.php?option=com_plugins');
		$I->waitForText('Plugins', 30, ['css' => 'H1']);
		$I->fillField(['id' => 'filter_search'], 'redcore - system plugin');
		$I->click(['xpath' => "//div[@id='filter-bar']/div[2]/button"]); // search button
		$I->waitForElement(['link' => 'redCORE - System plugin'], 60);
		$I->click(['link' => 'redCORE - System plugin']);
		$I->waitForText('Plugins: redCORE - System plugin', 30, ['css' => 'h1']);
		$I->click(['link' => 'Frontend components/modules options']);
		$I->waitForElementVisible(['id' => 'jform_params_frontend_css']);
		$I->selectOptionInRadioField('Include redCORE CSS and JS', 'Yes');
		$I->click(['link' => 'Translation options']);
		$I->waitForElementVisible(['id' => 'attrib-PLG_SYSTEM_REDCORE_TRANSLATIONS_OPTIONS']);
		$I->selectOptionInRadioField('Enable translations', 'Yes');
		$I->click(['link' => 'Webservice options']);
		$I->waitForElementVisible(['id' => 'attrib-PLG_SYSTEM_REDCORE_WEBSERVICES_OPTIONS']);
		$I->selectOptionInRadioField('Enable webservices', 'Yes');
		$I->selectOptionInRadioField('Enable SOAP Server', 'Yes');
		$I->selectOptionInChosenjs('Check user permission against','Joomla - Use already defined authorization checks in Joomla');
		$I->click(['xpath' => "//div[@id='toolbar-apply']/button"]);
		$I->waitForText('Plugin successfully saved.', 30, ['id' => 'system-message-container']);
		$I->click(['xpath' => "//div[@id='toolbar-cancel']/button"]);
		$I->amOnPage('administrator/index.php?option=com_redcore&view=webservices');
		$I->waitForText('Webservice Manager', 30, ['css' => 'H1']);
		$I->click(['class' => 'lc-not_installed_webservices']);
		$I->click(['class' => 'lc-install_all_webservices']);
		$I->waitForElement(['id' => 'oauthClientsList'], 30);
		$I->fillField(['id' => 'filter_search_webservices'], 'redSHOP B2B - Category Webservice');
		$I->pressKey(['id' => 'filter_search_webservices'], WebDriverKeys::ENTER);
		$I->waitForElement(['id' => 'oauthClientsList'], 30);
		$I->see('site.redshopb-category.1.0.0.xml', ['class' => 'lc-webservice-file']);
	}*/
}

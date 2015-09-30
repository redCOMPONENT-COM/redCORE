<?php
/**
 * @package     redCORE
 * @subpackage  Cept
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// Load the Step Object Page
$I = new \AcceptanceTester($scenario);
$I->wantToTest('Activate the default webservices available in redCORE');
$I->doAdministratorLogin();
$I->comment('I enable basic authentication');
$I->amOnPage('administrator/index.php?option=com_plugins');
$I->waitForText('Plugins', 30, ['css' => 'H1']);
$I->fillField(['id' => 'filter_search'], 'redcore - system plugin');
$I->click(['xpath' => "//div[@id='filter-bar']/div[2]/button"]); // search button
$I->click(['link' => 'redCORE - System plugin']);
$I->waitForText('Plugins: redCORE - System plugin', 30, ['css' => 'h1']);
$I->click(['link' => 'Webservice options']);
$I->selectOptionInRadioField('Enable webservices', 'Yes');
$I->selectOptionInChosen('Check user permission against','Joomla - Use already defined authorization checks in Joomla');
$I->click(['xpath' => "//div[@id='toolbar-apply']/button"]);
$I->waitForText('Plugin successfully saved.', 30, ['id' => 'system-message-container']);
$I->amOnPage('administrator/index.php?option=com_redcore&view=webservices');
$I->waitForText('Webservice Manager', 30, ['css' => 'H1']);
$I->click(['class' => 'lc-not_installed_webservices']);
$I->click(['class' => 'lc-install_all_webservices']);
$I->waitForElement(['id' => 'oauthClientsList'], 30);
$I->see('administrator.contact.1.0.0.xml',['class' => 'lc-webservice-file']);
$I->see('site.contact.1.0.0.xml',['class' => 'lc-webservice-file']);
$I->see('site.users.1.0.0.xml',['class' => 'lc-webservice-file']);

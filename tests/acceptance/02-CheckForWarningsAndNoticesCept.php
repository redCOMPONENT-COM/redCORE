<?php
/**
 * @package     redMEMBER
 * @subpackage  Cept
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Load the Step Object Page
$I = new \AcceptanceTester($scenario);

$I->wantToTest(' that there are no Warnings or Notices in redCORE');
$I->doAdministratorLogin();
$I->wantTo('Activate redSHOP system plugin features');
$I->amOnPage('administrator/index.php?option=com_plugins');
$I->fillField(['id' => 'filter_search'], 'redcore - system plugin');
$I->click(['xpath' => "//div[@id='filter-bar']/div[2]/button"]); // search button
$I->click(['link' => 'redCORE - System plugin']);
$I->waitForText('Plugin Manager: redCORE - System plugin', 5, ['css' => 'h1']);
$I->click(['link' => 'Translation options']);
$I->selectOptionInChosen('Enable translations', 'Yes');
$I->click(['link' => 'Webservice options']);
$I->selectOptionInChosen('Enable webservices', 'Yes');
$I->click(['link' => 'OAuth2 Server options']);
$I->selectOptionInChosen('Enable Oauth2 Server', 'Yes');
$I->clic(['xpath' => "//div[@id='toolbar-apply']/button"]);
$I->waitForText('Plugin successfully saved.', 5, ['id' => 'system-message-container']);
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redmember&view=rmadminusers');

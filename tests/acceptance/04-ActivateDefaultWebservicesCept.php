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
$I->amOnPage('administrator/index.php?option=com_redcore&view=webservices');
$I->click(['link' => 'Not installed webservices']);
$I->click(['class' => 'lc-install_all_webservices']);



$I->fillField(['id' => 'filter_search'], 'redcore - system plugin');
$I->click(['xpath' => "//div[@id='filter-bar']/div[2]/button"]); // search button
$I->waitForText('Plugin Manager: redCORE - System plugin', 5, ['css' => 'h1']);
$I->click(['link' => 'Translation options']);
$I->selectOptionInChosen('Enable translations', 'Yes');
$I->click(['link' => 'Webservice options']);
$I->selectOptionInChosen('Enable webservices', 'Yes');
$I->click(['xpath' => "//div[@id='toolbar-apply']/button"]);
$I->waitForText('Plugin successfully saved.', 5, ['id' => 'system-message-container']);
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&contentelement=&layout=manage');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&component=com_banners&contentelement=banner_clients');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&component=com_banners&contentelement=banners&return');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&component=com_banners&contentelement=categories');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&component=com_banners&contentelement=contact_details');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&component=com_banners&contentelement=content');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&component=com_banners&contentelement=languages');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&component=com_banners&contentelement=menu');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&component=com_banners&contentelement=modules');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&component=com_banners&contentelement=extensions');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&component=com_banners&contentelement=redcore_country');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&component=com_banners&contentelement=redcore_currency');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&component=com_banners&contentelement=users');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=webservices');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=oauth_clients');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=payment_dashboard');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=payment_configurations');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=payments');

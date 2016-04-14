<?php
/**
 * @package     redCORE
 * @subpackage  Cept
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// Load the Step Object Page
$I = new \AcceptanceTester($scenario);
$I->wantToTest(' that there are no Warnings or Notices in redCORE');
$I->doAdministratorLogin();
$I->wantTo('Activate redCORE system plugin features');
$I->amOnPage('administrator/index.php?option=com_plugins');
$I->fillField(['id' => 'filter_search'], 'redcore - system plugin');
$I->click(['css' => "button[data-original-title='Search']"]);
$I->waitForElement(['link' => 'redCORE - System plugin'], 60);
$I->click(['link' => 'redCORE - System plugin']);
$I->waitForText('Plugins: redCORE - System plugin', 30, ['css' => 'h1']);
$I->click(['link' => 'Translation options']);
$I->selectOptionInRadioField('Enable translations', 'Yes');
$I->click(['link' => 'Webservice options']);
$I->selectOptionInRadioField('Enable webservices', 'Yes');
$I->click(['xpath' => "//div[@id='toolbar-apply']/button"]);
$I->waitForText('Plugin successfully saved.', 30, ['id' => 'system-message-container']);
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translation_tables');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=banner_clients');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=banners&return');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=categories');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=contact_details');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=content');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=languages');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=menu');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=modules');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=extensions');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=redcore_country');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=redcore_currency');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=users');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=webservices');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=oauth_clients');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=payment_dashboard');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=payment_configurations');
$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=payments');

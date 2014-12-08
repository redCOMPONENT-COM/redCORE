<?php
/**
 * @package     redCORE
 * @subpackage  Cept
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Before executing this tests configuration.php is removed at tests/_groups/InstallationGroup.php
$scenario->group('installationJ2');
$scenario->group('Joomla2');

// Load the Step Object Page
$I = new AcceptanceTester\InstallJoomla2LanguageSteps($scenario);

$I->wantTo('Execute Joomla Installation');
$I->selectLanguage();
$I = new AcceptanceTester\InstallJoomla2DatabaseSteps($scenario);
$I->setupDatabaseConnection();
$I = new AcceptanceTester\InstallJoomla2SiteConfigurationSteps($scenario);
$I->setupConfiguration();
$I = new AcceptanceTester\LoginSteps($scenario);

$I->wantTo('Execute Log in at Joomla Administrator');
$I->doAdminLogin();
$I = new AcceptanceTester\InstallExtensionJ2Steps($scenario);

$I->wantTo('Install redCORE extension');
$I->installExtension('redCORE');

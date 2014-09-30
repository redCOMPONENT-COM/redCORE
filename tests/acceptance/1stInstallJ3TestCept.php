<?php
/**
 * @package     redMEMBER
 * @subpackage  Cept
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Before executing this tests configuration.php is removed at tests/_groups/InstallationGroup.php
$scenario->group('installationJ3');
$scenario->group('Joomla3');

// Load the Step Object Page
$I = new AcceptanceTester\InstallJoomla3Steps($scenario);

$I->wantTo('Execute Joomla Installation');
$I->installJoomla3();
$I = new AcceptanceTester\LoginSteps($scenario);

$I->wantTo('Execute Log in at Joomla Administrator');
$I->doAdminLogin();
$I = new AcceptanceTester\InstallExtensionJ3Steps($scenario);

$I->wantTo('Install redCORE extension');
$I->installExtension('redCORE');

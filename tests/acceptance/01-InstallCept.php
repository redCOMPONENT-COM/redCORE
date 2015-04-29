<?php
/**
 * @package     redCORE
 * @subpackage  Cept
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Load the Step Object Page
$I = new \AcceptanceTester($scenario);

$I->wantToTest('redCORE installation in Joomla 3');
$I->installJoomla();
$I->doAdministratorLogin();
$I->setErrorReportingToDevelopment();
$I->installExtensionFromDirectory($I->getConfiguration('repo folder'));
$I->click('#install-demo-content');
$I->waitForText('Sample data successfully installed', 10, '#system-message-container');
$I->see('Sample data successfully installed', 10, '#system-message-container');

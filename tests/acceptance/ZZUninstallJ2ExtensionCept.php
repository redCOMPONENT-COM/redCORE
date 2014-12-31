<?php
/**
 * @package     redCORE
 * @subpackage  Cept
 * @copyright   Copyright (C) 2008 - 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
/* Name of the File is Kept as ZZUninstallExtension instead of UninstallExtension
   So that this tests is loaded at the last during the test execution */

// Load the Step Object Page
$scenario->group('Joomla2');
$I = new AcceptanceTester\LoginSteps($scenario);

$I->wantTo('Uninstall Extension');
$I->doAdminLogin();

$I = new AcceptanceTester\UninstallJ2ExtensionSteps($scenario);
$I->uninstallExtension('redcore');

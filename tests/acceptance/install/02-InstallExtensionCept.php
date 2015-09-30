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
$I->doAdministratorLogin();
$path = $I->getConfiguration('repo_folder');
$I->installExtensionFromDirectory($path);
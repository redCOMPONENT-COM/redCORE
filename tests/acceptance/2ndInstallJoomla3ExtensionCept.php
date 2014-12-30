<?php
/**
 * @package     RedShop
 * @subpackage  Cept
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
$scenario->group('Joomla3');

$I = new AcceptanceTester\LoginSteps($scenario);

$I->wantTo('Install Extension');
$I->doAdminLogin();

$I = new AcceptanceTester\InstallExtensionJ3Steps($scenario);

$config = $I->getConfig();

$I->wantTo('Install redCORE extension');
$I->installExtension('redCORE');


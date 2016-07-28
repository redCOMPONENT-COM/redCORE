<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

class AdministratorContacts100AvailabilityCest
{
	public function WebserviceIsAvailable(ApiTester $I)
	{
		$I->wantTo("check the availability of the webservice");
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendGET('index.php'
			. '?option=contact'
			. '&api=Hal'
			. '&webserviceClient=administrator'
			. '&webserviceVersion=1.0.0'
		);

		// Checking for 204 code since there are no contacts before inserting them
		$I->seeResponseCodeIs(204);
		$I->seeHttpHeader('X-Webservice-name', 'contact');
		$I->seeHttpHeader('X-Webservice-version', '1.0.0');
	}
}

<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

class ContactsCest
{
	/**
	 * The new contact id
	 *
	 * @var int
	 */
	private $contactID = 0;

	private $contactName = '';


	public function __construct()
	{
		$this->contactName = 'contact' . rand(0,1000);
	}

	/**
	 * Create a new contact using Contacts Webservices API
	 *
	 * @param ApiTester $I
	 */
	public function administratorCreateContact(ApiTester $I)
	{
		$I->wantTo('POST via webservices a new Contact in com_contacts');
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendPOST('/administrator/index.php?option=contact&api=Hal', [
			'option' => 'contact',
			'api' => 'Hal',
			'webserviceVersion' => '1.0.0',
			'webserviceClient' => 'administrator',
			'name' => $this->contactName,
		    'catid' => 4 // Uncategorised default category
		]);
		$I->seeResponseCodeIs(201);
		$I->seeResponseIsJson();
		$contactIDs = $I->grabDataFromResponseByJsonPath('$.id');
		$this->contactID = $contactIDs[0];
		$I->comment("The id of the new created user is: $this->contactID");
	}

	/**
	 * Get a contact using Contact Webservices API
	 *
	 * @depends administratorCreateContact
	 *
	 * @param ApiTester $I
	 */
	public function administratorGetContact(ApiTester $I)
	{
		$I->wantTo("GET via webservices an existing Contact");
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendGET('/administrator/index.php?option=contact&api=Hal', [
			'option' => 'contact',
			'webserviceClient' => 'administrator',
			'api' => 'Hal',
			'id' => $this->contactID]);
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$I->seeResponseContains('"name":"'. $this->contactName.'"');
	}

	/**
	 * Update an existing contact using Contacts Webservices API
	 *
	 * @depends administratorGetContact
	 *
	 * @param ApiTester $I
	 */
	public function administratorUpdateContact(ApiTester $I)
	{
		$I->wantTo('Update via webservices a new Contact in com_contacts using PUT');
		$I->amHttpAuthenticated('admin', 'admin');

		$this->contactName = 'new_' . $this->contactName;
		$I->sendPUT('/administrator/index.php?option=contact&api=Hal', [
			'option' => 'contact',
			'api' => 'Hal',
			'webserviceVersion' => '1.0.0',
			'webserviceClient' => 'administrator',
			'id' => $this->contactID,
			'name' => $this->contactName,
			'catid' => 4 // Uncategorised default category
		]);
		$I->seeResponseCodeIs(200);

		$I->sendGET('/administrator/index.php?option=contact&api=Hal', [
			'option' => 'contact',
			'webserviceClient' => 'administrator',
			'api' => 'Hal',
			'id' => $this->contactID]);
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$I->seeResponseContains('"name":"'. $this->contactName.'"');
		$I->comment("The contact name has been modified to: $this->contactName");
	}

	/**
	 * Delete an existing contact using Contacts Webservices API
	 *
	 * @depends administratorUpdateContact
	 *
	 * @param ApiTester $I
	 */
	public function administratorDeleteContact(ApiTester $I)
	{
		/*
		 * @Todo: this issue can't be fished until https://redweb.atlassian.net/browse/REDCORE-418 gets resolved

		$I->wantTo('Delete via webservices a new Contact in com_contacts using DELETE');
		$I->amHttpAuthenticated('admin', 'admin');

		$this->contactName = 'new_' . $this->contactName;
		$I->sendDELETE('/administrator/index.php?option=contact&api=Hal', [
			'option' => 'contact',
			'api' => 'Hal',
			'webserviceVersion' => '1.0.0',
			'webserviceClient' => 'administrator',
			'id' => $this->contactID,
		]);
		$I->seeResponseCodeIs(200);

		$I->sendGET('/administrator/index.php?option=contact&api=Hal', [
			'option' => 'contact',
			'webserviceClient' => 'administrator',
			'api' => 'Hal',
			'id' => $this->contactID]);
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$I->seeResponseContains('"name":"'. $this->contactName.'"');
		$I->comment("The contact name has been modified to: $this->contactName");
		*/
	}

	/**
	 * Create a new contact using Contacts Webservices API without specify Client in the request
	 *
	 * @param ApiTester $I
	 */
	public function administratorCreateContactWithoutClient(ApiTester $I)
	{
		$I->wantTo("POST via webservices a new Contact in com_contacts without specifying client in the request params");
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendPOST('/administrator/index.php?option=contact&api=Hal', [
			'option' => 'contact',
			'api' => 'Hal',
			'webserviceVersion' => '1.0.0',
			'name' => $this->contactName,
			'catid' => 4 // Uncategorised default category
		]);
		$I->seeResponseCodeIs(201);
		$I->seeResponseIsJson();
		$contactIDs = $I->grabDataFromResponseByJsonPath('$.id');
		$this->contactID = $contactIDs[0];
		$I->comment("The id of the new created user is: $this->contactID");
	}

	/**
	 * Get a contact using Contact Webservices API without specify Client in the request
	 *
	 * @depends administratorCreateContactWithoutClient
	 *
	 * @param ApiTester $I
	 */
	public function administratorGetContactWithoutClient(ApiTester $I)
	{
		$I->wantTo('GET via webservices an existing Contact without specify Client in the request');
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendGET('/administrator/index.php?option=contact&api=Hal', [
			'option' => 'contact',
			'api' => 'Hal',
			'id' => $this->contactID]);
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$I->seeResponseContains('"name":"'. $this->contactName.'"');
	}
}
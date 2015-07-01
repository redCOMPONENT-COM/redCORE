<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

class AdministratorContacts1Cest
{
	/**
	 * The new contact id
	 *
	 * @var int
	 */
	private $contactID = 0;

	/**
	 * The new contact name
	 *
	 * @var int
	 */
	private $contactName = '';

	/**
	 * Set up the contact stub
	 */
	public function __construct()
	{
		$this->contactName = 'contact' . rand(0,1000);
	}

	public function WebserviceIsAvailable(ApiTester $I)
	{
		$I->wantTo("check the availability of the webservice");
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendGET('index.php',
		            [
			            'option' => 'contact',
			            'api' => 'Hal',
			            'webserviceClient' => 'administrator',
		            ]
		);
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$I->seeHttpHeader('Webservice-name', 'contact');
		$I->seeHttpHeader('Webservice-version', '1.0.0');
	}

	/**
	 * Create a new contact using Contacts Webservices API
	 *
	 * @depends WebserviceIsAvailable
	 *
	 * @param ApiTester $I
	 */
	public function create(ApiTester $I)
	{
		$I->wantTo('POST via webservices a new Contact in com_contacts');
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendPOST('administrator/index.php?option=contact&api=Hal', [
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
	 * @depends create
	 *
	 * @param ApiTester $I
	 */
	public function readItem(ApiTester $I)
	{
		$I->wantTo("GET via webservices an existing Contact");
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendGET('administrator/index.php?option=contact&api=Hal', [
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
	 * @depends readItem
	 *
	 * @param ApiTester $I
	 */
	public function update(ApiTester $I)
	{
		$I->wantTo('Update via webservices a new Contact in com_contacts using PUT');
		$I->amHttpAuthenticated('admin', 'admin');

		$this->contactName = 'new_' . $this->contactName;
		$I->sendPUT('administrator/index.php?option=contact&api=Hal', [
			'option' => 'contact',
			'api' => 'Hal',
			'webserviceVersion' => '1.0.0',
			'webserviceClient' => 'administrator',
			'id' => $this->contactID,
			'name' => $this->contactName
		]);
		$I->seeResponseCodeIs(200);

		$I->sendGET('administrator/index.php?option=contact&api=Hal', [
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
	 * @depends update
	 *
	 * @param ApiTester $I
	 */
	public function delete(ApiTester $I)
	{
		$I->wantTo('Delete via webservices a new Contact in com_contacts using DELETE');
		$I->amHttpAuthenticated('admin', 'admin');

		$I->sendDELETE("administrator/index.php?option=contact&api=Hal&id=$this->contactID", [
			'option' => 'contact',
			'api' => 'Hal',
			'webserviceVersion' => '1.0.0',
			'webserviceClient' => 'administrator']
		);
		$I->seeResponseCodeIs(200);

		$I->sendGET('/administrator/index.php?option=contact&api=Hal', [
			'option' => 'contact',
			'webserviceClient' => 'administrator',
			'api' => 'Hal',
			'id' => $this->contactID]
		);
		$I->seeResponseCodeIs(404);
		$I->seeResponseIsJson();
		$I->seeResponseContains('"message":"Item not found with given key.","code":404,"type":"Exception"');
	}

	/**
	 * Create a new contact using Contacts Webservices API without specify Client in the request
	 *
	 * @param ApiTester $I
	 */
	public function createWithoutClient(ApiTester $I)
	{
		$I->wantTo("POST via webservices a new Contact in com_contacts without specifying client in the request params");
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendPOST('administrator/index.php?option=contact&api=Hal', [
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
	public function readWithoutClient(ApiTester $I)
	{
		$I->wantTo('GET via webservices an existing Contact without specify Client in the request');
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendGET('administrator/index.php?option=contact&api=Hal', [
			'option' => 'contact',
			'api' => 'Hal',
			'id' => $this->contactID]);
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$I->seeResponseContains('"name":"'. $this->contactName.'"');
	}
}
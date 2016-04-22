<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

class AdministratorContacts100CrudCest
{
	public function __construct()
	{
		$this->faker = Faker\Factory::create();
		$this->name = $this->faker->bothify('AdministratorContacts100CrudCest contact ?##?');
		$this->id = 0;
	}

	public function create(ApiTester $I)
	{
		$I->wantTo('POST a new Contact in com_contacts');
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendPOST('index.php'
			. '?option=contact'
			. '&api=Hal'
			. '&webserviceClient=administrator'
			. '&webserviceVersion=1.0.0'
			. "&name=$this->name"
			// Uncategorised default category
			. '&catid=4'
		);

		$I->seeResponseCodeIs(201);
		$I->seeResponseIsJson();
		$contactIDs = $I->grabDataFromResponseByJsonPath('$.id');
		$this->id = $contactIDs[0];
		$I->comment("The id of the new created user is: $this->id");
	}

	/**
	 * @depends create
	 */
	public function readList(ApiTester $I)
	{
		$I->wantTo("check the read list operation of the webservice");
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendGET('index.php'
			. '?option=contact'
			. '&api=Hal'
			. '&webserviceClient=administrator'
			. '&webserviceVersion=1.0.0'
		);

		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$I->seeHttpHeader('X-Webservice-name', 'contact');
		$I->seeHttpHeader('X-Webservice-version', '1.0.0');
		$I->seeResponseContainsJson(['id' => $this->id]);
		$I->seeResponseContainsJson(['name' => $this->name]);
	}

	/**
	 * @depends create
	 */
	public function readItem(ApiTester $I)
	{
		$I->wantTo("GET an existing Contact");
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendGET('index.php'
			. '?option=contact'
			. '&api=Hal'
			. '&webserviceClient=administrator'
			. '&webserviceVersion=1.0.0'
			. "&id=$this->id"
		);

		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$I->seeResponseContainsJson(['name' => $this->name]);
	}

	/**
	 * @depends readItem
	 */
	public function update(ApiTester $I)
	{
		$I->wantTo('Update a new Contact in com_contacts using PUT');
		$I->amHttpAuthenticated('admin', 'admin');

		$this->name = 'new_' . $this->name;
		$I->sendPUT('index.php'
			. '?option=contact'
			. '&api=Hal'
			. '&webserviceClient=administrator'
			. '&webserviceVersion=1.0.0'
			. "&id=$this->id"
			. "&name=$this->name"
		);

		$I->seeResponseCodeIs(200);

		$I->sendGET('index.php'
			. '?option=contact'
			. '&api=Hal'
			. '&webserviceClient=administrator'
			. '&webserviceVersion=1.0.0'
			. "&id=$this->id"
		);

		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$I->seeResponseContainsJson(['name' => $this->name]);
		$I->comment("The contact name has been modified to: $this->name");
	}

	/**
	 * @depends update
	 */
	public function delete(ApiTester $I)
	{
		$I->wantTo('Delete a new Contact in com_contacts using DELETE');
		$I->amHttpAuthenticated('admin', 'admin');

		$I->sendDELETE('index.php'
			. '?option=contact'
			. '&api=Hal'
			. '&webserviceClient=administrator'
			. '&webserviceVersion=1.0.0'
			. "&id=$this->id"
		);

		$I->seeResponseCodeIs(200);

		$I->sendGET('index.php'
			. '?option=contact'
			. '&api=Hal'
			. '&webserviceClient=administrator'
			. '&webserviceVersion=1.0.0'
			. "&id=$this->id"
		);

		$I->seeResponseCodeIs(404);
		$I->seeResponseIsJson();
		$I->seeResponseContains('"message":"Item not found with given key.","code":404,"type":"Exception"');
	}
}

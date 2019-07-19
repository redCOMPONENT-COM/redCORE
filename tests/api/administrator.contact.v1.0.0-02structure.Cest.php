<?php
/**
 * @package     Redshopb
 * @subpackage  Tests
 *
 * @copyright   Copyright (C) 2012 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

class AdministratorContact100StructureCest
{
	public function prepare(ApiTester $I)
	{
		$I->wantTo('POST a new contact');
		$this->faker = Faker\Factory::create();
		$this->faker->addProvider(new Faker\Provider\en_US\Address($this->faker));

		$this->faker->seed(1234);
		$this->contact['name']  = (string) $this->faker->bothify('AdministratorContact100StructureCest contact ?##?');


		$this->adminId = $mail = $I->grabFromDatabase('jos_users', 'id', array('username' => 'admin'));
		$I->comment('The administrator id is: ' . $this->adminId);

		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendPOST('index.php'
			. '?option=contact'
			. '&api=Hal'
			. '&webserviceClient=administrator'
			. '&webserviceVersion=1.0.0',
			[
				'name' => $this->contact['name'],
				// Uncategorised default category
				'catid' => 4,
				'address' => '7217 Collins Row Apt. 719',
				'suburb' => 'Douglasfort',
				'state' => 'Washington',
				'country' => 'United States',
				'postcode' => '08740',
				'telephone' => '934454545',
				'fax' => '934454546',
				'misc' => 'miscelaneous info',
				'image' => '',
				'email_to' => 'test@test.com'
			]
		);

		$I->seeResponseCodeIs(201);
		$I->seeResponseIsJson();
		$contactIDs = $I->grabDataFromResponseByJsonPath('$.id');
		$this->id = $contactIDs[0];
		$I->comment('The id of the new created category with name ' . $this->contact['name'] . ' is ' . $this->id);
	}

	public function readItemAndCheckItsStructure(ApiTester $I)
	{
		$I->wantTo("GET an existing category with its internal id");
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendGET(
				'index.php'
				. '?option=contact'
				. '&api=Hal'
				. '&webserviceClient=administrator'
				. '&webserviceVersion=1.0.0'
				. "&id=$this->id"
		);

		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();

		$baseUrl = $I->getWebserviceBaseUrl();

		$I->seeResponseContainsJson(
			[
				"_links" => [
					"curies" => [
						[
							"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&format=doc&api=Hal#{rel}",
							"title" => "Documentation",
							"name" => "contact",
							"templated" => true
						]
					],
					"base" => [
						"href" => "$baseUrl/?webserviceClient=administrator&api=Hal",
						"title" => "Default page"
					],
					"contact:self" => [
						"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&id=$this->id&api=Hal"
					],
					"contact:list" => [
						"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&api=Hal"
					],
					"contact:checkout" => [
						"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&id=$this->id&task=checkout&api=Hal"
					],
					"contact:checkin" => [
						"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&id=$this->id&task=checkin&api=Hal"
					],
					"contact:featured" => [
						"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&id=$this->id&task=featured&api=Hal"
					],
					"contact:unfeatured" => [
						"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&id=$this->id&task=unfeatured&api=Hal"
					],
					"contact:publish" => [
						"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&id=$this->id&task=publish&api=Hal"
					],
					"contact:unpublish" => [
						"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&id=$this->id&task=unpublish&api=Hal"
					]
				],
				'id'                => $this->id,
				'name'              => $this->contact['name'],
				// @todo change the following line once REDCORE-475 gets fixed: 'alias' => $I->getAlias($this->contact['name']),
				'alias'             => '',
				'con_position' => '',
				'address' => '7217 Collins Row Apt. 719',
				'suburb' => 'Douglasfort',
				'state' => 'Washington',
				'country' => 'United States',
				'postcode' => '08740',
				'telephone' => '934454545',
				'fax' => '934454546',
				'misc' => 'miscelaneous info',
				'image' => '',
				'email_to' => 'test@test.com',
				'default_con' => 0,
				'published' => 1,
				'checked_out' => 0,
				'checked_out_time' => '',
				'ordering' => 0,
				'params' => '[]',
				'user_id' => 0,
				'catid' => 4,
				'access' => 1,
				'mobile' => '',
				'webpage' => '',
				'sortname1' => '',
				'sortname2' => '',
				'sortname3' => '',
				'language' => '*',
				'created' => '',
				'created_by' => "$this->adminId",
				'modified' => '',
				'modified_by' => "$this->adminId",
				'metakey' => '',
				'metadesc' => '',
				'metadata' => '',
				'featured' => 0,
				'publish_up' => '',
				'publish_down' => '',
				'version' => '1',
				'hits' => 0
			]
		);
	}

	public function cleanUp(ApiTester $I)
	{
		$I->wantTo('Clear up all created items by the test');
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendDELETE(
			'index.php'
			. '?option=contact'
			. '&api=Hal'
			. '&webserviceClient=administrator'
			. '&webserviceVersion=1.0.0'
			. "&id=$this->id"
		);
		$I->seeResponseCodeIs(200);
	}
}

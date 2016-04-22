<?php
/**
 * @package     Redshopb
 * @subpackage  Tests
 *
 * @copyright   Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

class AdministratorContact100StructureCest
{
	public function prepare(ApiTester $I)
	{
		$I->wantTo('POST a new contact');
		$this->faker = Faker\Factory::create();
		$this->name  = $this->faker->bothify('AdministratorContact100StructureCest contact ?##?');

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
		$I->comment("The id of the new created category with name '$this->name' is => $this->id");
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
							"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&format=doc&api=Hal#[rel}",
							"title" => "Documentation",
							"name" => "contact",
							"templated" => true
						]
					],
					"base" => [
						"href" => "$baseUrl/?webserviceClient=administrator&api=Hal",
						"title" => "Default page"
					],
					"contact =>self" => [
						"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&id=17&api=Hal"
					],
					"contact =>list" => [
						"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&api=Hal"
					],
					"contact =>checkout" => [
						"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&id=17&task=checkout&api=Hal"
					],
					"contact =>checkin" => [
						"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&id=17&task=checkin&api=Hal"
					],
					"contact =>featured" => [
						"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&id=17&task=featured&api=Hal"
					],
					"contact =>unfeatured" => [
						"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&id=17&task=unfeatured&api=Hal"
					],
					"contact =>publish" => [
						"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&id=17&task=publish&api=Hal"
					],
					"contact =>unpublish" => [
						"href" => "$baseUrl/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&id=17&task=unpublish&api=Hal"
					]
				],
				'id'                => $this->id,
				'name'              => $this->name,
				'alias'             => $I->getAlias($this->name),
				'con_position' => '',
				'address' => '',
				'suburb' => '',
				'state' => '',
				'country' => '',
				'postcode' => '',
				'telephone' => '',
				'fax' => '',
				'misc' => '',
				'image' => '',
				'email_to' => '',
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
				'created_by' => 606,
				'modified' => '',
				'modified_by' => 606,
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
		$I->sendDELETE('index.php'
			. '?option=redshopb&view=category'
			. '&api=Hal'
			. '&webserviceClient=site'
			. '&webserviceVersion=1.0.0'
			. "&id=$this->id"
		);
		$I->seeResponseCodeIs(200);
	}
}

<?php
/**
 * @package     redCORE
 * @subpackage  Cest
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
use Faker\Generator;
use Faker\Factory;
use Step\Acceptance\OAuthClientsStep as AdminTester;
use Page\OAuthClientsPage;

/**
 * Class OAuthClientsCest
 * @since 1.10.7
 */
class OAuthClientsCest
{
	/**
	 * @var Generator
	 * @since 1.10.7
	 */
	protected $faker;

	/**
	 * @var string
	 * @since 1.10.7
	 */
	protected $clientID;

	/**
	 * @var
	 * @since 1.10.7
	 */
	protected $clientID2;

	/**
	 * @var string
	 * @since 1.10.7
	 */
	protected $redirectURI;

	/**
	 * OAuthClientsCest constructor.
	 * @since 1.10.7
	 */
	public function __construct()
	{
		$this->faker = Factory::create();
		$this->clientID = $this->faker->bothify("Client ID ##??");
		$this->clientID2 = $this->faker->bothify("Client ID ##??");
		$this->redirectURI = $this->faker->bothify("http://?????.com");
	}

	/**
	 * @param AcceptanceTester $I
	 * @throws Exception
	 * @since 1.10.7
	 */
	public function _before(AcceptanceTester $I){
		$I->doAdministratorLogin();
	}

	/**
	 * @param AdminTester $I
	 * @throws Exception
	 * @since 1.10.7
	 */
	public function OAuthClients(AdminTester $I){
		$I->wantToTest("Activate The OAuth2");
		$I->activateTheOAuth2();

		$I->wantToTest("Create New OAuth Client");
		$I->createNewOAuthClient($this->clientID, $this->redirectURI);
		$I->waitForSuccess();

		$I->wantToTest("Edit OAuthClient");
		$I->editOAuthClient($this->clientID, $this->clientID2);
		$I->waitForSuccess();
		$I->click(OAuthClientsPage::$buttonClear);
		$I->see($this->clientID2);

		$I->wantToTest("Delete OAuthClient");
		$I->deleteOAuthClient($this->clientID2);
		$I->click(OAuthClientsPage::$buttonClear);
		$I->searchOAuthClient($this->clientID2);
		$I->dontSee($this->clientID2);
	}
}
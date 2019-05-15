<?php
/**
 * @package     redCORE
 * @subpackage  Step
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Step\Acceptance;
use Page\OAuthClientsPage;

/**
 * Class OAuthClientsStep
 * @package Step\Acceptance
 */
class OAuthClientsStep extends AbstractStep
{
	/**
	 * @param $clientID
	 * @param $redirectURI
	 * @throws \Exception
	 */
	public function createNewOAuthClient($clientID,$redirectURI)
	{
		$I = $this;
		$I->amOnPage(OAuthClientsPage::$URL);
		$I->waitForText(OAuthClientsPage::$titleOAuth, 30, OAuthClientsPage::$h1);
		$I->click(OAuthClientsPage::$buttonNew);
		$I->waitForText(OAuthClientsPage::$titleOAuthClient, 30, OAuthClientsPage::$h1);
		$I->fillField(OAuthClientsPage::$fieldClientID, $clientID);
		$I->fillField(OAuthClientsPage::$fieldURI, $redirectURI);
		$I->click(OAuthClientsPage::$buttonSaveClose);
	}

	/**
	 * @throws \Exception
	 */
	public function waitForSuccess()
	{
		$I = $this;
		$I->waitForText(OAuthClientsPage::$messageSuccess, 30, OAuthClientsPage::$messageContainer);
		$I->waitForText(OAuthClientsPage::$messageSaveSuccess, 30, OAuthClientsPage::$messageContainer);
	}

	/**
	 * @param $clientID
	 */
	public function searchOAuthClient($clientID)
	{
		$I= $this;
		$I->fillField(OAuthClientsPage::$fieldSearch, $clientID);
		$I->click(OAuthClientsPage::$search);
	}

	/**
	 * @param $clientID
	 * @param $clientID2
	 * @throws \Exception
	 */
	public function editOAuthClient($clientID,$clientID2)
	{
		$I = $this;
		$I->amOnPage(OAuthClientsPage::$URL);
		$I->waitForText(OAuthClientsPage::$titleOAuth, 30, OAuthClientsPage::$h1);
		$I->searchOAuthClient($clientID);
		$I->see($clientID, OAuthClientsPage::$oauthClientsList);
		$I->click($clientID);
		$I->fillField(OAuthClientsPage::$fieldClientID, $clientID2);
		$I->click(OAuthClientsPage::$buttonSaveClose);
	}

	/**
	 * @param $clientID2
	 * @throws \Exception
	 */
	public function deleteOAuthClient($clientID2)
	{
		$I = $this;
		$I->amOnPage(OAuthClientsPage::$URL);
		$I->waitForText(OAuthClientsPage::$titleOAuth, 30, OAuthClientsPage::$h1);
		$I->searchOAuthClient($clientID2);
		$I->click(OAuthClientsPage::$check);
		$I->click(OAuthClientsPage::$buttonDelete);
		$I->waitForText(OAuthClientsPage::$message, 30, OAuthClientsPage::$messageContainer);
		$I->waitForText(OAuthClientsPage::$textDeleteSuccess, 30, OAuthClientsPage::$messageContainer);
	}
}
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
 * @since 1.10.7
 */
class OAuthClientsStep extends AbstractStep
{
	/**
	 * @param $clientID
	 * @param $redirectURI
	 * @throws \Exception
	 * @since 1.10.7
	 */
	public function createNewOAuthClient($clientID, $redirectURI)
	{
		$I = $this;
		$I->amOnPage(OAuthClientsPage::$URL);
		$I->waitForText(OAuthClientsPage::$titleOAuth, 30, OAuthClientsPage::$h1);
		$I->click(OAuthClientsPage::$buttonNew);
		$I->waitForText(OAuthClientsPage::$titleOAuthClient, 30, OAuthClientsPage::$h1);
		$I->fillField(OAuthClientsPage::$fieldClientID, $clientID);
		$I->fillField(OAuthClientsPage::$fieldURI, $redirectURI);
		$I->waitForText(OAuthClientsPage::$grantTypes, 30, OAuthClientsPage::$labelGrant);
		$I->waitForText(OAuthClientsPage::$userCredentials, 30, OAuthClientsPage::$fieldSet);
		$I->click(OAuthClientsPage::$checkbox);
		$I->waitForText(OAuthClientsPage::$clientScopes, 30, OAuthClientsPage::$labelScopes);
		$I->waitForText(OAuthClientsPage::$allWebservices, 30, OAuthClientsPage::$labelAllWeb);
		$I->click(OAuthClientsPage::$scopeCheckAll);
		$I->click(OAuthClientsPage::$buttonSaveClose);
	}

	/**
	 * @throws \Exception
	 * @since 1.10.7
	 */
	public function waitForSuccess()
	{
		$I = $this;
		$I->waitForText(OAuthClientsPage::$messageSuccess, 30, OAuthClientsPage::$messageContainer);
		$I->waitForText(OAuthClientsPage::$messageSaveSuccess, 30, OAuthClientsPage::$messageContainer);
	}

	/**
	 * @param $clientID
	 * @since 1.10.7
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
	 * @since 1.10.7
	 */
	public function editOAuthClient($clientID, $clientID2)
	{
		$I = $this;
		$I->amOnPage(OAuthClientsPage::$URL);
		$I->waitForText(OAuthClientsPage::$titleOAuth, 30, OAuthClientsPage::$h1);
		$I->searchOAuthClient($clientID);
		$I->waitForText($clientID, 30, OAuthClientsPage::$oauthClientsList);
		$I->see($clientID);
		$I->click($clientID);
		$I->fillField(OAuthClientsPage::$fieldClientID, $clientID2);
		$I->click(OAuthClientsPage::$buttonSaveClose);
	}

	/**
	 * @param $clientID2
	 * @throws \Exception
	 * @since 1.10.7
	 */
	public function deleteOAuthClient($clientID2)
	{
		$I = $this;
		$I->amOnPage(OAuthClientsPage::$URL);
		$I->waitForText(OAuthClientsPage::$titleOAuth, 30, OAuthClientsPage::$h1);
		$I->searchOAuthClient($clientID2);
		$I->waitForText($clientID2, 30, OAuthClientsPage::$oauthClientsList);
		$I->see($clientID2);
		$I->click(OAuthClientsPage::$check);
		$I->click(OAuthClientsPage::$buttonDelete);
		$I->waitForText(OAuthClientsPage::$message, 30, OAuthClientsPage::$messageContainer);
		$I->waitForText(OAuthClientsPage::$textDeleteSuccess, 30, OAuthClientsPage::$messageContainer);
	}
}
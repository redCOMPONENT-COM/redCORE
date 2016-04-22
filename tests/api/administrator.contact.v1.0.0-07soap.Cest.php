<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

class AdministratorContacts100SoapCest
{
	public function _before(ApiTester $I, \Helper\SoapModule $soapModule, \Codeception\TestCase\Cest $testCase)
	{
		$endpoint = $I->getWebserviceBaseUrl() . '/administrator/index.php?webserviceClient=administrator&webserviceVersion=1.0.0&option=contact&api=soap';
		$schema = $I->getSoapWsdlDinamically($endpoint);

		$soapModule->configure(
			$testCase,
			$endpoint,
			$schema
		);

		$this->faker = Faker\Factory::create();
		$this->name = $this->faker->bothify('AdministratorContacts1SoapCest contact ?##?');

		// 4 is by default the id of Uncategorised category
		$this->category = 4;
		$this->id = 'this property will contain the id of the created contact';

	}

	public function create(ApiTester $I)
	{
		$I->wantTo('create a Contact in Joomla using SOAP');
		$I->amHttpAuthenticated('admin', 'admin');

		// Following line fails until https://github.com/Codeception/Codeception/issues/2540 gets fixed
		// $I->sendSoapRequest('create', '<name>test</name><catid>4</catid>');
		$I->sendSoapRequest('create', ['name' => $this->name, 'catid' => '4']);
		$I->seeSoapResponseIncludes("<result>true</result>");
	}


	public function readList(ApiTester $I)
	{
		$I->wantTo('list contacts in Joomla using SOAP');
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendSoapRequest('readList', "<filterSearch>$this->name</filterSearch>");
		$I->seeSoapResponseIncludes("<name>$this->name</name>");
		$this->id = $I->grabTextContentFrom('//list//item//id');
	}

	public function readItem(ApiTester $I)
	{
		$I->wantTo('read 1 contact in Joomla using SOAP');
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendSoapRequest('readItem', ['id' => $this->id]);
		$I->seeSoapResponseIncludes("<name>$this->name</name>");
		$I->seeSoapResponseIncludes("<published>1</published>");
	}


	public function unpublish(ApiTester $I)
	{
		$I->wantTo('unpublish 1 contact in Joomla using SOAP');
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendSoapRequest('task_unpublish', ['id' => $this->id]);
		$I->seeSoapResponseIncludes("<result>true</result>");

		$I->sendSoapRequest('readItem', ['id' => $this->id]);
		$I->seeSoapResponseIncludes("<name>$this->name</name>");
		$I->seeSoapResponseIncludes("<published>0</published>");
	}

	public function delete(ApiTester $I)
	{
		$I->wantTo('delete 1 contact in Joomla using SOAP');
		$I->amHttpAuthenticated('admin', 'admin');
		$I->sendSoapRequest('delete', ['id' => $this->id]);
		$I->seeSoapResponseIncludes("<result>true</result>");

		$I->sendSoapRequest('readItem', ['id' => $this->id]);
		$I->dontSeeSoapResponseIncludes("<name>$this->name</name>");
	}
}
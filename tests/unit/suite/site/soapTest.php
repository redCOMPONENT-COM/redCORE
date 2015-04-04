<?php
/**
 * redCORE soap helper test
 *
 * @package    Redcore.UnitTest
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com
 * @license    GNU General Public License version 2 or later
 */

/**
 * Test class for Soap Server
 *
 * @package  Redcore.UnitTest
 * @since    1.4
 */
class soapTest
{
	/**
	 * Test SoapClient
	 *
	 * @param   string  $wsdlUrl  Url to the wsdl file
	 *
	 * @return void
	 */
	public function testSoapClient(
		$wsdlUrl = 'http://localhost/redComponent/red33test/index.php?option=com_redshopb&amp;view=country&amp;webserviceVersion=1.0.0&amp;api=soap&amp;wsdl')
	{
		//$wsdlUrl = 'http://dje.redhost.dk/index.php?webserviceClient=site&webserviceVersion=1.0.0&option=redshopb&view=country&api=soap&wsdl';
		$wsdlUrl = 'http://dje.redhost.dk/index.php?webserviceClient=site&webserviceVersion=1.0.0&option=redshopb&view=language&api=soap&wsdl';
		ini_set("soap.wsdl_cache_enabled", "0");
		$params = array(
			'soap_version' => SOAP_1_2,
			'exceptions' => true,
			'trace' => 1,
			'cache_wsdl' => WSDL_CACHE_NONE,
			'login' => 'super',//super
			'password' => 'mgLJnL@Q3RJ2$xArsEQMNnb97p',//mgLJnL@Q3RJ2$xArsEQMNnb97p
		);

		try
		{
			$client = new SoapClient($wsdlUrl, $params);

			/*
			 * Additional tests

			*/
			//$response = $client->task_hit();
			//$response = $client->readList(0, 2, '');
			$response = $client->readItem(1, $language = 'en');

			// Dump request / response
			$this->dumpSoapMessages($client);
			var_dump($response);
		}
		catch (SoapFault $ex)
		{
			var_dump($ex);
		}

		// $this->assertTrue(!is_null($response));
	}

	/**
	 * Dump Client response and request messages
	 *
	 * @param   SoapClient  $client  Client instance
	 *
	 * @return void
	 */
	private function dumpSoapMessages($client)
	{
		echo '<br />$client->__getLastRequest():<br />';
		var_dump($client->__getLastRequest());
		echo '<br />$client->__getLastRequestHeaders():<br />';
		var_dump($client->__getLastRequestHeaders());
		echo '<br />$client->__getLastResponse():<br />';
		var_dump($client->__getLastResponse());
		echo '<br />$client->__getLastResponseHeaders():<br />';
		var_dump($client->__getLastResponseHeaders());
	}
}

$test = new soapTest;
$test->testSoapClient();

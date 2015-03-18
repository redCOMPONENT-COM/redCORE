<?php
/**
 * redCORE lib currency helper test
 *
 * @package    Redcore.UnitTest
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com
 * @license    GNU General Public License version 2 or later
 */

/**
 * Test class for Redevent lib helper class
 *
 * @package  Redevent.UnitTest
 * @since    1.2.0
 */
class soapTest// extends JoomlaTestCase
{
	/**
	 * Test GetIsoCode
	 *
	 * @return void
	 */
	public function testSoapClient()
	{
		ini_set("soap.wsdl_cache_enabled", "0");
		$params = array(
			'soap_version' => SOAP_1_2,
			'exceptions' => true,
			'trace' => 1,
			'cache_wsdl' => WSDL_CACHE_NONE,
			'login' => 'admin',
			'password' => 'admin',
			'exceptions' => true
			//'location' => 'http://localhost/redComponent/red33test/'
		);

		$requestParams = array(
			'CityName' => 'Zagreb',
			'CountryName' => 'Croatia'
		);

		/*$client = new SoapClient('http://www.webservicex.net/globalweather.asmx?WSDL', $params);
		$response = $client->GetWeather($requestParams);

		print_r($response);*/
		try {
			//
			$client = new SoapClient('http://localhost/redComponent/red33test/index.php?option=com_contact&amp;webserviceVersion=1.0.0&amp;api=soap&amp;wsdl', $params);
			//$response = $client->sayHello();

			//$response = $client->readItem(array('id' => 4));
			$response = $client->readList(0,2,'aaa');
			//$response = $client->taskHit();

			/*var_dump($client->__getLastRequest());
			var_dump($client->__getLastRequestHeaders());
			var_dump($client->__getLastResponse());
			var_dump($client->__getLastResponseHeaders());*/
			//var_dump($response);
			var_dump($response);
		}
		catch(SoapFault $ex) {
			var_dump($ex);
			//print $ex->getMessage();
			//print $ex->getTraceAsString();
    }

		/*$client = new SoapClient('http://localhost/redComponent/red33test/index.php/hr/?option=com_contact&webserviceVersion=1.0.0&id=4&api=soap&wsdl', $params);

		var_dump($client);
		$response = $client->sayHello('Kixo');



		print_r($response);*/

		//$this->assertTrue(is_array($options) && count($options));
	}
}

$test = new soapTest();
$test->testSoapClient();


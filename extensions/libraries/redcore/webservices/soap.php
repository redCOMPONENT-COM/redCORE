<?php
/**
 * @package     Redcore
 * @subpackage  Factory
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Class for interacting with webservices through SOAP protocol.
 *
 * @package     Redcore
 * @subpackage  soap
 * @since       1.8
 */
class RWebservicesSoap extends RWebservicesBase
{
	/**
	 * Executes Query from remote server location
	 *
	 * @param   string  $url      The URL for the request.
	 * @param   mixed   $data     The data to include in the request
	 * @param   array   $headers  The headers to send with the request
	 * @param   string  $method   The method with which to send the request
	 *
	 * @return  mixed  Response from the function
	 *
	 * @since   1.8
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 * @throws  SoapFault
	 */
	public function executeRemoteQuery($url, $data = null, $headers = array(), $method = 'get')
	{
		$client = null;
		$params = array(
			'soap_version' => $this->getOption('soapversion', SOAP_1_2),
			'exceptions' => true,
			'trace' => $this->getOption('debug') ? 1 : 0,
			'cache_wsdl' => $this->getOption('wsdlcache', WSDL_CACHE_NONE),
			'login' => $this->getOption('authorization.username'),
			'password' => $this->getOption('authorization.password'),
			'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | $this->getOption('compressionLevel', 1)
		);

		try
		{
			$client = new SoapClient($url, $params);

			if (!empty($headers))
			{
				$client->__setSoapHeaders($headers);
			}

			$response = $client->{$method}($data);
			$this->outputSoapMessages($client);
		}
		catch (SoapFault $ex)
		{
			$this->outputSoapMessages($client);
			throw $ex;
		}

		return $response;
	}

	/**
	 * Dump Client response and request messages in the Joomla message queue
	 *
	 * @param   SoapClient  $client  Client instance
	 *
	 * @return void
	 */
	private function outputSoapMessages($client)
	{
		if ($this->getOption('debug') && $client)
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage('LastRequestHeaders: ' . $client->__getLastRequestHeaders());
			$app->enqueueMessage('LastRequest: ' . $client->__getLastRequest());
			$app->enqueueMessage('LastResponseHeaders: ' . $client->__getLastResponseHeaders());
			$app->enqueueMessage('LastResponse: ' . $client->__getLastResponse());
		}
	}
}

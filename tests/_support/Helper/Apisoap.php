<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Apisoap extends \Codeception\Module
{
	/**
	 * Cached WSDL files
	 */
	static private $schemas = array();

	/**
	 * Returns the location of the Wsdl file generated dinamically by redCORE.
	 *
	 * @param   string  $endpoint  The webservice url.
	 *
	 * @return mixed
	 */
	public function getSoapWsdlDinamically($endpoint)
	{
		// Gets cached WSDL static file from dynamic file
		if (!isset(self::$schemas[$endpoint]))
		{
			$wsdl = simplexml_load_file($endpoint . '&wsdl');
			$schema = $wsdl['targetNamespace'];
			self::$schemas[$endpoint] = (string) $schema;
		}

		return self::$schemas[$endpoint];
	}

	public function switchEndPoint ($endpoint)
	{
		$this->getModule('SOAP')->_reconfigure(['endpoint' => $endpoint]);
	}
}

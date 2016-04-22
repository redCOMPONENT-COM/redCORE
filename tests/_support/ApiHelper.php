<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class ApiHelper extends \Codeception\Module
{
	/**
	 * Cached WSDL files
	 */
	static private $schemas = [];

	/**
	 * Returns a url safe string removing spaces and captial letters
	 *
	 * @param $name
	 */
	public function getAlias($name)
	{
		$alias  = strtolower($name);
		$alias  = str_replace(" ", "-", $alias);

		return $alias;
	}

	public function getWebserviceBaseUrl()
	{
		// @todo: to be updated with QA-35
		$yaml = new Parser();

		$config = $yaml->parse(file_get_contents(dirname(__DIR__) . '/api.suite.yml'));

		$url = $config['modules']['enabled'][2]['REST']['url'];
		$url = rtrim($url, "/");

		return $url;
	}

	/**
	 * Returns the location of the Wsdl file generated dynamically
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
			self::$schemas[$endpoint] = $schema;
		}

		return self::$schemas[$endpoint];
	}
}

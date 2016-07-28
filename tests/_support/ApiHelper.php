<?php
namespace Codeception\Module;


// here you can define custom actions
// all public methods declared in helper class will be available in $I

class ApiHelper extends \Codeception\Module
{
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

	// @todo: I haven't seen an easy way to get the parameter from the config module but there must be a way. See http://phptest.club/t/how-to-get-a-config-parameter-from-a-module/701
	public function getWebserviceBaseUrl()
	{
		$yaml = new \Symfony\Component\Yaml\Parser();

		$config = $yaml->parse(file_get_contents(dirname(__DIR__) . '/api.suite.yml'));

		$url = $config['modules']['enabled'][2]['REST']['url'];
		$url = rtrim($url, "/");

		return $url;
	}
}

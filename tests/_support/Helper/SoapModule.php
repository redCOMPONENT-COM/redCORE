<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class SoapModule extends \Codeception\Module
{
	public function configure($testcase, $endpoint, $schema)
	{
		$this->getModule('SOAP')->_reconfigure(
			array(
				'endpoint' => $endpoint,
				'schema' => $schema,
			)
		);
		//$this->getModule('SOAP')->buildRequest();
		$this->getModule('SOAP')->_before($testcase);
	}
}

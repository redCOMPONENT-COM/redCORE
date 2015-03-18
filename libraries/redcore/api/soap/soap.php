<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

/**
 * Class to represent a SOAP standard object.
 *
 * @since  1.2
 */
class RApiSoapSoap extends RApi
{
	/**
	 * Webservice object
	 *
	 * @var    RApiHalHal  Webservice
	 * @since  1.4
	 */
	public $webservice = null;

	/**
	 * Container for WSDL file
	 *
	 * @var    SimpleXMLElement  Generated Web service Description language file
	 * @since  1.4
	 */
	public $wsdl = null;

	/**
	 * Soap server response
	 *
	 * @var    string  XML Output from Soap server
	 * @since  1.4
	 */
	public $soapResponse = null;

	/**
	 * Method to instantiate the file-based api call.
	 *
	 * @param   mixed  $options  Optional custom options to load. JRegistry or array format
	 *
	 * @throws Exception
	 * @since   1.4
	 */
	public function __construct($options = null)
	{
		parent::__construct($options);

		JPluginHelper::importPlugin('redcore');

		try
		{
			$this->webservice = new RApiHalHal($options);
			$this->webservice->authorizationCheck = 'joomla';
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Init Environment
		$this->triggerFunction('setApiOperation');

		// Set initial status code
		$this->setStatusCode($this->statusCode);
	}

	/**
	 * Set Method for Api to be performed
	 *
	 * @return  RApi
	 *
	 * @since   1.4
	 */
	public function setApiOperation()
	{
		$dataGet = $this->options->get('dataGet', array());
		$method = 'soap';

		if (isset($dataGet->wsdl))
		{
			$method = 'wsdl';
		}

		$this->operation = strtolower($method);

		return $this;
	}

	/**
	 * Execute the Api operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.4
	 * @throws  Exception
	 */
	public function execute()
	{
		// We do not want some unwanted text to appear before output
		ob_start();

		try
		{
			switch ($this->operation)
			{
				case 'soap':
					$this->triggerFunction('apiSoap');
					break;

				case 'wsdl':
				default:

					$this->triggerFunction('apiWsdl');
				break;
			}

			$messages = JFactory::getApplication()->getMessageQueue();

			$executionErrors = ob_get_contents();
			ob_end_clean();
		}
		catch (Exception $e)
		{
			$executionErrors = ob_get_contents();
			ob_end_clean();

			throw $e;
		}

		if (!empty($executionErrors))
		{
			$messages[] = array('message' => $executionErrors, 'type' => 'notice');
		}

		if (!empty($messages))
		{
			$this->webservice->hal->setData('_messages', $messages);
		}

		return $this;
	}

	/**
	 * Main Soap server
	 *
	 * @return  string  Full URL to the webservice
	 *
	 * @since   1.4
	 */
	public function apiSoap()
	{
		ini_set("soap.wsdl_cache_enabled", "0");
		$params = array(
			'soap_version' => SOAP_1_2,
		);
		$operation = new RApiSoapOperationOperation($this->webservice);
		$server = new SoapServer(
			RApiHalHelper::buildWebserviceFullUrl(
				$this->webservice->client, $this->webservice->webserviceName, $this->webservice->webserviceVersion, 'soap'
			) . '&wsdl',
			$params
		);
		$server->setObject($operation);

		ob_start();
		$server->handle();
		$response = ob_get_contents();
		ob_end_clean();

		$this->soapResponse = $response;
	}

	/**
	 * Returns Wsdl file
	 *
	 * @return  SimpleXMLElement  WSDL file in xml format
	 *
	 * @since   1.4
	 */
	public function apiWsdl()
	{
		try
		{
			$wsdlFullPath = RApiHalHelper::getWebserviceFile(
				$this->webservice->client,
				strtolower($this->webservice->webserviceName),
				$this->webservice->webserviceVersion,
				'wsdl',
				$this->webservice->webservicePath
			);

			if (is_readable($wsdlFullPath))
			{
				$content = @file_get_contents($wsdlFullPath);

				if (is_string($content))
				{
					$this->wsdl = new SimpleXMLElement($content);
				}
			}
		}
		catch (Exception $e)
		{
		}

		// Something went wrong, we are going to generate it on the fly
		if (empty($this->wsdl))
		{
			$this->wsdl = RApiSoapHelper::generateWsdl($this->webservice->configuration);
		}

		return $this->wsdl;
	}

	/**
	 * Method to send the application response to the client.  All headers will be sent prior to the main
	 * application output data.
	 *
	 * @return  void
	 *
	 * @since   1.4
	 */
	public function render()
	{
		$documentOptions = array(
			'absoluteHrefs' => $this->options->get('absoluteHrefs', false),
			'documentFormat' => 'xml',
		);

		if ($this->operation == 'wsdl')
		{
			// Needed for formatting
			$dom = dom_import_simplexml($this->wsdl)->ownerDocument;
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$body = $dom->saveXML();
		}
		else
		{
			$body = $this->getBody();
		}

		JFactory::$document = new RApiSoapDocumentDocument($documentOptions);
		$body = $this->triggerFunction('prepareBody', $body);

		// Push results into the document.
		JFactory::$document
			->setApiObject($this)
			->setBuffer($body)
			->render(false);
	}

	/**
	 * Method to fill response with requested data
	 *
	 * @return  string  Api call output
	 *
	 * @since   1.4
	 */
	public function getBody()
	{
		return $this->soapResponse;
	}

	/**
	 * Prepares body for response
	 *
	 * @param   string  $message  The return message
	 *
	 * @return  string	The message prepared
	 *
	 * @since   1.4
	 */
	public function prepareBody($message)
	{
		return $message;
	}

	/**
	 * Calls method from method from this class,
	 * Additionally it Triggers plugin call for specific function in a format RApiHalFunctionName
	 *
	 * @param   string  $functionName  Field type.
	 *
	 * @return mixed Result from callback function
	 */
	public function triggerFunction($functionName)
	{
		$args = func_get_args();

		// Remove function name from arguments
		array_shift($args);

		// PHP 5.3 workaround
		$temp = array();

		foreach ($args as &$arg)
		{
			$temp[] = &$arg;
		}

		// We will add this instance of the object as last argument for manipulation in plugin and helper
		$temp[] = &$this;

		// Checks if that method exists in helper file and executes it
		$result = call_user_func_array(array($this, $functionName), $temp);

		JFactory::getApplication()->triggerEvent('RApiSoapAfter' . $functionName, $temp);

		return $result;
	}
}

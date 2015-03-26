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
 * Wsdl class for redCORE webservice
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.4
 */
class RApiSoapWsdl
{
	/**
	 * SimpleXMLElement object
	 *
	 * @var    SimpleXMLElement  Webservice xml file
	 * @since  1.4
	 */
	public $webserviceXml = null;

	/**
	 * SimpleXMLElement object
	 *
	 * @var    SimpleXMLElement  Wsdl xml file
	 * @since  1.4
	 */
	public $wsdl = null;

	/**
	 * Wsdl Services which are needed across operations
	 *
	 * @var    SimpleXMLElement  Wsdl xml element file
	 * @since  1.4
	 */
	public $wsdlServices = null;

	/**
	 * Url to the webservice
	 *
	 * @var    string  Url to the webservice
	 * @since  1.4
	 */
	public $webserviceUrl = null;

	/**
	 * portType xml element
	 *
	 * @var    SimpleXMLElement  portType xml element file
	 * @since  1.4
	 */
	public $portType = null;

	/**
	 * binding xml element
	 *
	 * @var    SimpleXMLElement  binding xml element file
	 * @since  1.4
	 */
	public $binding = null;

	/**
	 * binding12 xml element
	 *
	 * @var    SimpleXMLElement  binding12 xml element file
	 * @since  1.4
	 */
	public $binding12 = null;

	/**
	 * Method to instantiate Wsdl file
	 *
	 * @param   SimpleXMLElement  $webservice  Webservice XML file
	 *
	 * @throws Exception
	 * @since   1.4
	 */
	public function __construct($webservice = null)
	{
		$this->webserviceXml = $webservice;
	}

	/**
	 * Add global types that do not exists in native SOAP xsd schema
	 *
	 * @param   SimpleXMLElement  &$typeSchema  Type Schema
	 *
	 * @return  void
	 */
	public function addGlobalTypes(&$typeSchema)
	{
		// Add ArrayOfString complex type
		$complexTypeArrayOfString = $typeSchema->addChild('s:complexType', null, 'http://www.w3.org/2001/XMLSchema');
		$complexTypeArrayOfString->addAttribute('name', 'ArrayOfStringType');

		// Add sequence for ArrayOfString
		$complexTypeSequenceArrayOfString = $complexTypeArrayOfString->addChild('s:sequence', null, 'http://www.w3.org/2001/XMLSchema');

		// Add element for ArrayOfString
		$complexTypeSequenceElementArrayOfString = $complexTypeSequenceArrayOfString->addChild('s:element', null, 'http://www.w3.org/2001/XMLSchema');
		$complexTypeSequenceElementArrayOfString->addAttribute('minOccurs', '0');
		$complexTypeSequenceElementArrayOfString->addAttribute('maxOccurs', 'unbounded');
		$complexTypeSequenceElementArrayOfString->addAttribute('name', 'string');
		$complexTypeSequenceElementArrayOfString->addAttribute('nillable', 'true');
		$complexTypeSequenceElementArrayOfString->addAttribute('type', 's:string');

		// Add ArrayOfString element
		$elementArrayOfString = $typeSchema->addChild('s:element', null, 'http://www.w3.org/2001/XMLSchema');
		$elementArrayOfString->addAttribute('name', 'ArrayOfString');
		$elementArrayOfString->addAttribute('type', 'tns:ArrayOfStringType');
	}

	/**
	 * Add messages to the WSDL document
	 *
	 * @param   SimpleXMLElement  &$wsdl        Wsdl document
	 * @param   string            $messageName  Message name
	 * @param   array             $parts        Parts of the message
	 *
	 * @return  void
	 */
	public function addMessage(&$wsdl, $messageName, $parts = array())
	{
		// Add new message
		$message = $wsdl->addChild('message');
		$message->addAttribute('name', $messageName);

		foreach ($parts as $part)
		{
			$messagePart = $message->addChild('part');
			$messagePart->addAttribute('name', $part['name']);

			if ($part['type'] != '')
			{
				$messagePart->addAttribute('type', $part['type']);
			}

			if ($part['element'] != '')
			{
				$messagePart->addAttribute('element', $part['element']);
			}
		}
	}

	/**
	 * Add port types that we need for our messages to group them together
	 *
	 * @param   SimpleXMLElement  &$wsdl     Wsdl document
	 * @param   string            $portName  Message name
	 *
	 * @return  void
	 */
	public function addPortType(&$wsdl, $portName)
	{
		if (!$this->portType)
		{
			// Add new port type
			$this->portType = $wsdl->addChild('portType');
			$this->portType->addAttribute('name', ucfirst($this->webserviceXml->config->name));
		}

		// Add port operation
		$portOperation = $this->portType->addChild('operation');
		$portOperation->addAttribute('name', $portName);

		// Input operation
		$inputOperation = $portOperation->addChild('input');
		$inputOperation->addAttribute('message', 'tns:' . ucfirst($portName) . 'Request');

		// Output operation
		$outputOperation = $portOperation->addChild('output');
		$outputOperation->addAttribute('message', 'tns:' . ucfirst($portName) . 'Response');
	}

	/**
	 * Add soap binding for an specific and existing binding
	 *
	 * @param   SimpleXMLElement  &$binding       Binding element
	 * @param   string            $operationName  Message name
	 * @param   string            $document       Document
	 *
	 * @return  void
	 */
	protected function addSpecificBinding(&$binding, $operationName, $document)
	{
		// Add binding operation
		$bindingOperation = $binding->addChild('operation');
		$bindingOperation->addAttribute('name', $operationName);

		// Add soap binding operation
		$soapBindingOperation = $bindingOperation->addChild('operation', null, $document);
		$soapBindingOperation->addAttribute('soapAction', $operationName);
		$soapBindingOperation->addAttribute('type', 'document');

		// Add input binding operation
		$bindingInputOperation = $bindingOperation->addChild('input');
		$bindingInputOperationBody = $bindingInputOperation->addChild('body', null, $document);
		$bindingInputOperationBody->addAttribute('use', 'literal');

		// Add output binding operation
		$bindingOutputOperation = $bindingOperation->addChild('output');
		$bindingOutputOperationBody = $bindingOutputOperation->addChild('body', null, $document);
		$bindingOutputOperationBody->addAttribute('use', 'literal');
	}

	/**
	 * Add soap binding for our operation
	 *
	 * @param   SimpleXMLElement  &$wsdl          Wsdl document
	 * @param   string            $operationName  Message name
	 *
	 * @return  void
	 */
	public function addBinding(&$wsdl, $operationName)
	{
		if (!$this->binding)
		{
			// Add new binding element
			$this->binding = $wsdl->addChild('binding');
			$this->binding->addAttribute('name', ucfirst($this->webserviceXml->config->name));
			$this->binding->addAttribute('type', 'tns:' . ucfirst($this->webserviceXml->config->name));

			// Apply soap binding
			$soapBinding = $this->binding->addChild('soap:binding', null, 'http://schemas.xmlsoap.org/wsdl/soap/');
			$soapBinding->addAttribute('transport', 'http://schemas.xmlsoap.org/soap/http');

			// Add new binding12 element
			$this->binding12 = $wsdl->addChild('binding');
			$this->binding12->addAttribute('name', ucfirst($this->webserviceXml->config->name) . '12');
			$this->binding12->addAttribute('type', 'tns:' . ucfirst($this->webserviceXml->config->name));

			// Apply soap binding (12)
			$soapBinding = $this->binding12->addChild('soap:binding', null, 'http://schemas.xmlsoap.org/wsdl/soap12/');
			$soapBinding->addAttribute('transport', 'http://schemas.xmlsoap.org/soap/http');
		}

		$this->addSpecificBinding($this->binding, $operationName, 'http://schemas.xmlsoap.org/wsdl/soap/');
		$this->addSpecificBinding($this->binding12, $operationName, 'http://schemas.xmlsoap.org/wsdl/soap12/');
	}

	/**
	 * Add operation to a Wsdl document
	 *
	 * @param   SimpleXMLElement  &$wsdl               Wsdl document
	 * @param   string            $name                Operation name
	 * @param   array             $messageInputParts   Message input parts
	 * @param   array             $messageOutputParts  Message output parts
	 *
	 * @return  void
	 */
	public function addOperation(&$wsdl, $name, $messageInputParts, $messageOutputParts)
	{
		$this->addMessage($wsdl, ucfirst($name) . 'Request', $messageInputParts);

		$this->addMessage($wsdl, ucfirst($name) . 'Response', $messageOutputParts);

		$this->addPortType($wsdl, $name);

		$this->addBinding($wsdl, $name);
	}

	/**
	 * Returns generated WSDL file for the webservice
	 *
	 * @return  SimpleXMLElement
	 */
	public function generateWsdl()
	{
		$client = RApiHalHelper::attributeToString($this->webserviceXml, 'client', 'site');
		$name = $this->webserviceXml->config->name;
		$version = !empty($this->webserviceXml->config->version) ? $this->webserviceXml->config->version : '1.0.0';
		$fullName = $client . '.' . $name . '.' . $version;
		$this->webserviceUrl = RApiHalHelper::buildWebserviceFullUrl($client, $name, $version, 'soap');

		// Root of the document
		$this->wsdl = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><wsdl:definitions'
			. ' xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/"'
			. ' xmlns:mime="http://schemas.xmlsoap.org/wsdl/mime/"'
			. ' xmlns:tns="' . str_replace('&', '&amp;', $this->webserviceUrl . '&wsdl') . '"'
			. ' xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"'
			. ' xmlns:s="http://www.w3.org/2001/XMLSchema"'
			. ' xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/"'
			. ' xmlns:http="http://schemas.xmlsoap.org/wsdl/http/"'
			. ' targetNamespace="' . str_replace('&', '&amp;', $this->webserviceUrl . '&wsdl') . '"'
			. ' xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"'
			. ' ></wsdl:definitions>',
			0, false, 'wsdl', true
		);

		$types = $this->wsdl->addChild('wsdl:types');
		$typeSchema = $types->addChild('s:schema', null, 'http://www.w3.org/2001/XMLSchema');
		$typeSchema->addAttribute('targetNamespace', $this->webserviceUrl . '&wsdl');
		$typeSchema->addAttribute('elementFormDefault', 'qualified');

		// Add global types (like array)
		$this->addGlobalTypes($typeSchema);

		// Adding service
		$this->wsdlServices = $this->wsdl->addChild('service');
		$this->wsdlServices->addAttribute('name', $fullName . '_Service');
		$this->wsdlServices->addChild('documentation', JText::sprintf('LIB_REDCORE_API_SOAP_WSDL_DESCRIPTION', $fullName . '_Service'));

		// Add new port binding
		$port = $this->wsdlServices->addChild('port');
		$port->addAttribute('name', ucfirst($name));
		$port->addAttribute('binding', 'tns:' . ucfirst($name));

		// Add soap addresses
		$soapAddress = $port->addChild('soap:address', null, 'http://schemas.xmlsoap.org/wsdl/soap/');
		$soapAddress->addAttribute('location', $this->webserviceUrl);

		// Add new port binding
		$port12 = $this->wsdlServices->addChild('port');
		$port12->addAttribute('name', ucfirst($name) . '12');
		$port12->addAttribute('binding', 'tns:' . ucfirst($name) . '12');

		// Add soap addresses
		$soapAddress12 = $port12->addChild('soap:address', null, 'http://schemas.xmlsoap.org/wsdl/soap12/');
		$soapAddress12->addAttribute('location', $this->webserviceUrl);

		// Add webservice operations
		if (isset($this->webserviceXml->operations))
		{
			// Read list
			if (isset($this->webserviceXml->operations->read->list))
			{
				// Add read list messages
				$messageInputParts = array(
					array('name' => 'limitStart', 'type' => 's:int'),
					array('name' => 'limit', 'type' => 's:int'),
					array('name' => 'filterSearch', 'type' => 's:string'),
					array('name' => 'filters', 'element' => 'tns:ArrayOfString'),
					array('name' => 'ordering', 'type' => 's:string'),
					array('name' => 'orderingDirection', 'type' => 's:string'),
					array('name' => 'language', 'type' => 's:string'),
				);

				// Add read list response messages
				$messageOutputParts = array(
					array('name' => 'parameters', 'element' => 'tns:ArrayOfString'),
				);

				$this->addOperation($this->wsdl, $name = 'readList', $messageInputParts, $messageOutputParts);
			}

			// Read item
			if (isset($this->webserviceXml->operations->read->item))
			{
				$primaryKeysFromFields = RApiHalHelper::getPrimaryKeysFromFields($this->webserviceXml->operations->read->item);

				if (count($primaryKeysFromFields) > 1)
				{
					$messageInputParts = array(
						array('name' => 'ids', 'element' => 'tns:ArrayOfString')
					);
				}
				else
				{
					$primaryKey = $primaryKeysFromFields[key($primaryKeysFromFields)];
					$primaryKeyType = isset($primaryKey['transform']) && $primaryKey['transform'] == 'int' ? 'int' : 'string';
					$messageInputParts = array(
						array('name' => key($primaryKeysFromFields), 'type' => 's:' . $primaryKeyType)
					);
				}

				// Add read item messages
				$messageInputParts[] = array('name' => 'language', 'type' => 's:string');

				// Add read item response messages
				$messageOutputParts = array(
					array('name' => 'parameters', 'element' => 'tns:ArrayOfString'),
				);

				$this->addOperation($this->wsdl, $name = 'readItem', $messageInputParts, $messageOutputParts);
			}

			// Create operation
			if (isset($this->webserviceXml->operations->create))
			{
				// Add create messages
				$messageInputParts = array(
					array('name' => 'parameters', 'element' => 'tns:ArrayOfString'),
				);

				// Add create response messages
				$messageOutputParts = array(
					array('name' => 'parameters', 'element' => 'tns:ArrayOfString'),
				);

				$this->addOperation($this->wsdl, $name = 'create', $messageInputParts, $messageOutputParts);
			}

			// Update operation
			if (isset($this->webserviceXml->operations->update))
			{
				// Add update messages
				$messageInputParts = array(
					array('name' => 'parameters', 'element' => 'tns:ArrayOfString'),
				);

				// Add update response messages
				$messageOutputParts = array(
					array('name' => 'parameters', 'element' => 'tns:ArrayOfString'),
				);

				$this->addOperation($this->wsdl, $name = 'update', $messageInputParts, $messageOutputParts);
			}

			// Delete operation
			if (isset($this->webserviceXml->operations->delete))
			{
				// Add delete messages
				$messageInputParts = array(
					array('name' => 'parameters', 'element' => 'tns:ArrayOfString'),
				);

				// Add delete response messages
				$messageOutputParts = array(
					array('name' => 'parameters', 'element' => 'tns:ArrayOfString'),
				);

				$this->addOperation($this->wsdl, $name = 'delete', $messageInputParts, $messageOutputParts);
			}

			// Task operation
			if (isset($this->webserviceXml->operations->task))
			{
				foreach ($this->webserviceXml->operations->task->children() as $taskName => $task)
				{
					// Add task messages
					$messageInputParts = array(
						array('name' => 'parameters', 'element' => 'tns:ArrayOfString'),
					);

					// Add task response messages
					$messageOutputParts = array(
						array('name' => 'parameters', 'element' => 'tns:ArrayOfString'),
					);

					$this->addOperation($this->wsdl, $name = 'task_' . $taskName, $messageInputParts, $messageOutputParts);
				}
			}
		}

		return $this->wsdl;
	}
}

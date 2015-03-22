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
		// Add array element
		$stringArrayElement = $typeSchema->addChild('element');
		$stringArrayElement->addAttribute('name', 'stringArray');

		// Add complex type
		$complexType = $stringArrayElement->addChild('complexType');
		$complexType->addAttribute('name', 'stringArray');

		// Add complex content
		$complexContent = $complexType->addChild('complexContent');

		// Add complex content restrictions
		$complexContentRestriction = $complexContent->addChild('restriction');
		$complexContentRestriction->addAttribute('base', 'SOAP-ENC:Array');

		// Add complex content restriction attribute
		$complexContentRestrictionAttribute = $complexContentRestriction->addChild('attribute');
		$complexContentRestrictionAttribute->addAttribute('ref', 'SOAP-ENC:arrayType');
		$complexContentRestrictionAttribute->addAttribute('tns:arrayType', 'xsd:string[][]');
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
		// Add new port type
		$portType = $wsdl->addChild('portType');
		$portType->addAttribute('name', ucfirst($portName) . '_PortType');

		// Add port operation
		$portOperation = $portType->addChild('operation');
		$portOperation->addAttribute('name', $portName);

		// Input operation
		$inputOperation = $portOperation->addChild('input');
		$inputOperation->addAttribute('message', 'tns:' . ucfirst($portName) . 'Request');

		// Output operation
		$outputOperation = $portOperation->addChild('output');
		$outputOperation->addAttribute('message', 'tns:' . ucfirst($portName) . 'Response');
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
		// Add new binding element
		$binding = $wsdl->addChild('binding');
		$binding->addAttribute('name', ucfirst($operationName) . '_Binding');
		$binding->addAttribute('type', 'tns:' . ucfirst($operationName) . '_PortType');

		// Apply soap binding
		$soapBinding = $binding->addChild('soap:binding', null, 'http://schemas.xmlsoap.org/wsdl/soap/');
		$soapBinding->addAttribute('style', 'rpc');
		$soapBinding->addAttribute('transport', 'http://schemas.xmlsoap.org/soap/http');

		// Add binding operation
		$bindingOperation = $binding->addChild('operation');
		$bindingOperation->addAttribute('name', $operationName);

		// Add soap binding operation
		$soapBindingOperation = $bindingOperation->addChild('soap:operation', null, 'http://schemas.xmlsoap.org/wsdl/soap/');
		$soapBindingOperation->addAttribute('soapAction', $operationName);

		// Add input binding operation
		$bindingInputOperation = $bindingOperation->addChild('input');
		$bindingInputOperationBody = $bindingInputOperation->addChild('soap:body', null, 'http://schemas.xmlsoap.org/wsdl/soap/');
		$bindingInputOperationBody->addAttribute('encodingStyle', 'http://schemas.xmlsoap.org/soap/encoding/');
		$bindingInputOperationBody->addAttribute('namespace', 'urn:soapstype:' . strtolower($operationName) . 'service');
		$bindingInputOperationBody->addAttribute('use', 'encoded');

		// Add output binding operation
		$bindingOutputOperation = $bindingOperation->addChild('output');
		$bindingOutputOperationBody = $bindingOutputOperation->addChild('soap:body', null, 'http://schemas.xmlsoap.org/wsdl/soap/');
		$bindingOutputOperationBody->addAttribute('encodingStyle', 'http://schemas.xmlsoap.org/soap/encoding/');
		$bindingOutputOperationBody->addAttribute('namespace', 'urn:soapstype:' . strtolower($operationName) . 'service');
		$bindingOutputOperationBody->addAttribute('use', 'encoded');
	}

	/**
	 * Add service port for operation
	 *
	 * @param   SimpleXMLElement  &$wsdl     Wsdl document
	 * @param   string            $portName  Port name
	 *
	 * @return  void
	 */
	public function addServicePort(&$wsdl, $portName)
	{
		// Add new port binding
		$port = $wsdl->addChild('port');
		$port->addAttribute('binding', 'tns:' . ucfirst($portName) . '_Binding');
		$port->addAttribute('name', ucfirst($portName) . '_Port');

		// Add soap address
		$soapAddress = $port->addChild('soap:address', null, 'http://schemas.xmlsoap.org/wsdl/soap/');
		$soapAddress->addAttribute('location', $this->webserviceUrl);
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

		$this->addServicePort($this->wsdlServices, $name);
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
		$this->wsdl = new SimpleXMLElement('<?xml version="1.0"?><definitions name="' . $fullName . '"'
			. ' xmlns="http://schemas.xmlsoap.org/wsdl/"'
			. ' xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"'
			. ' xmlns:tns="' . str_replace('&', '&amp;', $this->webserviceUrl . '&wsdl') . '"'
			. ' xmlns:xsd="http://www.w3.org/2001/XMLSchema"'
			. ' targetNamespace="' . str_replace('&', '&amp;', $this->webserviceUrl . '&wsdl') . '"'
			. ' ></definitions>');

		$types = $this->wsdl->addChild('types');
		$typeSchema = $types->addChild('schema');
		$typeSchema->addAttribute('targetNamespace', $this->webserviceUrl . '&wsdl');
		$typeSchema->addAttribute('xmlns', 'http://www.w3.org/2000/10/XMLSchema');

		// Add global types (like array)
		$this->addGlobalTypes($typeSchema);

		// Adding service
		$this->wsdlServices = $this->wsdl->addChild('service');
		$this->wsdlServices->addAttribute('name', $fullName . '_Service');
		$this->wsdlServices->addChild('documentation', JText::sprintf('LIB_REDCORE_API_SOAP_WSDL_DESCRIPTION', $fullName . '_Service'));

		// Add webservice operations
		if (isset($this->webserviceXml->operations))
		{
			// Read list
			if (isset($this->webserviceXml->operations->read->list))
			{
				// Add read list messages
				$messageInputParts = array(
					array('name' => 'limitStart', 'type' => 'xsd:int'),
					array('name' => 'limit', 'type' => 'xsd:int'),
					array('name' => 'filterSearch', 'type' => 'xsd:string'),
					array('name' => 'filters', 'element' => 'tns:stringArray'),
					array('name' => 'ordering', 'type' => 'xsd:string'),
					array('name' => 'orderingDirection', 'type' => 'xsd:string'),
					array('name' => 'language', 'type' => 'xsd:string'),
				);

				// Add read list response messages
				$messageOutputParts = array(
					array('name' => 'response', 'element' => 'tns:stringArray'),
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
						array('name' => 'ids', 'element' => 'tns:stringArray')
					);
				}
				else
				{
					$primaryKey = $primaryKeysFromFields[key($primaryKeysFromFields)];
					$primaryKeyType = isset($primaryKey['transform']) && $primaryKey['transform'] == 'int' ? 'int' : 'string';
					$messageInputParts = array(
						array('name' => key($primaryKeysFromFields), 'type' => 'xsd:' . $primaryKeyType)
					);
				}

				// Add read item messages
				$messageInputParts[] = array('name' => 'language', 'type' => 'xsd:string');

				// Add read item response messages
				$messageOutputParts = array(
					array('name' => 'response', 'element' => 'tns:stringArray'),
				);

				$this->addOperation($this->wsdl, $name = 'readItem', $messageInputParts, $messageOutputParts);
			}

			// Create operation
			if (isset($this->webserviceXml->operations->create))
			{
				// Add create messages
				$messageInputParts = array(
					array('name' => 'data', 'element' => 'tns:stringArray'),
				);

				// Add create response messages
				$messageOutputParts = array(
					array('name' => 'response', 'element' => 'tns:stringArray'),
				);

				$this->addOperation($this->wsdl, $name = 'create', $messageInputParts, $messageOutputParts);
			}

			// Update operation
			if (isset($this->webserviceXml->operations->update))
			{
				// Add update messages
				$messageInputParts = array(
					array('name' => 'data', 'element' => 'tns:stringArray'),
				);

				// Add update response messages
				$messageOutputParts = array(
					array('name' => 'response', 'element' => 'tns:stringArray'),
				);

				$this->addOperation($this->wsdl, $name = 'update', $messageInputParts, $messageOutputParts);
			}

			// Delete operation
			if (isset($this->webserviceXml->operations->delete))
			{
				// Add delete messages
				$messageInputParts = array(
					array('name' => 'id', 'element' => 'tns:stringArray'),
				);

				// Add delete response messages
				$messageOutputParts = array(
					array('name' => 'response', 'element' => 'tns:stringArray'),
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
						array('name' => 'data', 'element' => 'tns:stringArray'),
					);

					// Add task response messages
					$messageOutputParts = array(
						array('name' => 'response', 'element' => 'tns:stringArray'),
					);

					$this->addOperation($this->wsdl, $name = 'task_' . $taskName, $messageInputParts, $messageOutputParts);
				}
			}
		}

		return $this->wsdl;
	}
}

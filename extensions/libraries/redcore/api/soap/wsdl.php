<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
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
	 * Full name of the webservice
	 *
	 * @var    string  Full name of the webservice
	 * @since  1.4
	 */
	public $webserviceFullName = null;

	/**
	 * portType xml element
	 *
	 * @var    SimpleXMLElement  portType xml element file
	 * @since  1.4
	 */
	public $portType = null;

	/**
	 * typeSchema xml element
	 *
	 * @var    SimpleXMLElement  typeSchema xml element file
	 * @since  1.4
	 */
	public $typeSchema = null;

	/**
	 * binding xml element
	 *
	 * @var    SimpleXMLElement  binding xml element file
	 * @since  1.4
	 */
	public $binding = null;

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
	 * Returns generated WSDL file for the webservice
	 *
	 * @param   string  $wsdlPath  Path of WSDL file
	 *
	 * @return  SimpleXMLElement
	 */
	public function generateWsdl($wsdlPath)
	{
		$wsdlFullPath = JUri::root() . $wsdlPath;

		$client = RApiHalHelper::attributeToString($this->webserviceXml, 'client', 'site');
		$version = !empty($this->webserviceXml->config->version) ? $this->webserviceXml->config->version : '1.0.0';
		$this->webserviceFullName = $client . '.' . $this->webserviceXml->config->name . '.' . $version;
		$this->webserviceUrl = RApiHalHelper::buildWebserviceFullUrl($client, $this->webserviceXml->config->name, $version, 'soap');

		// Root of the document
		$this->wsdl = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><wsdl:definitions'
			. ' xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/"'
			. ' xmlns:mime="http://schemas.xmlsoap.org/wsdl/mime/"'
			. ' xmlns:tns="' . $wsdlFullPath . '"'
			. ' xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap12/"'
			. ' xmlns:s="http://www.w3.org/2001/XMLSchema"'
			. ' xmlns:http="http://schemas.xmlsoap.org/wsdl/http/"'
			. ' targetNamespace="' . $wsdlFullPath . '"'
			. ' xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"'
			. ' ></wsdl:definitions>',
			0, false, 'wsdl', true
		);

		$types = $this->wsdl->addChild('types');
		$this->typeSchema = $types->addChild('schema', null, 'http://www.w3.org/2001/XMLSchema');
		$this->typeSchema->addAttribute('targetNamespace', $wsdlFullPath);
		$this->typeSchema->addAttribute('elementFormDefault', 'unqualified');

		$this->addGlobalTypes($this->typeSchema);

		// Adding service
		$this->wsdlServices = $this->wsdl->addChild('service');
		$this->wsdlServices->addAttribute('name', $this->webserviceFullName);
		$this->wsdlServices->addChild('documentation', $this->webserviceXml->description);

		// Add new port binding
		$port = $this->wsdlServices->addChild('port');
		$port->addAttribute('name', $this->webserviceFullName . '_Soap');
		$port->addAttribute('binding', 'tns:' . $this->webserviceFullName);

		// Add soap addresses
		$soapAddress = $port->addChild('soap:address', null, 'http://schemas.xmlsoap.org/wsdl/soap12/');
		$soapAddress->addAttribute('location', $this->webserviceUrl);

		// Add webservice operations
		if (!isset($this->webserviceXml->operations))
		{
			return $this->wsdl;
		}

		$this->addReadListOperation();
		$this->addReadItemOperation();
		$this->addCreateOperation();
		$this->addUpdateOperation();
		$this->addDeleteOperation();
		$this->addTaskOperations();

		return $this->wsdl;
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
		$complexTypeArrayOfString = $typeSchema->addChild('complexType', null, 'http://www.w3.org/2001/XMLSchema');
		$complexTypeArrayOfString->addAttribute('name', 'ArrayOfStringType');

		// Add sequence for ArrayOfString
		$complexTypeSequenceArrayOfString = $complexTypeArrayOfString->addChild('sequence', null, 'http://www.w3.org/2001/XMLSchema');

		// Add element for ArrayOfString
		$complexTypeSequenceElementArrayOfString = $complexTypeSequenceArrayOfString->addChild('element', null, 'http://www.w3.org/2001/XMLSchema');
		$complexTypeSequenceElementArrayOfString->addAttribute('minOccurs', '0');
		$complexTypeSequenceElementArrayOfString->addAttribute('maxOccurs', 'unbounded');
		$complexTypeSequenceElementArrayOfString->addAttribute('name', 'string');
		$complexTypeSequenceElementArrayOfString->addAttribute('nillable', 'true');
		$complexTypeSequenceElementArrayOfString->addAttribute('type', 's:string');
	}

	/**
	 * Method to add readList operation to WSDL
	 *
	 * @return void
	 */
	private function addReadListOperation()
	{
		if (!isset($this->webserviceXml->operations->read->list))
		{
			return;
		}

		$filtersDef = array('name' => 'filters', 'transform' => 'array');
		$filters = RApiHalHelper::getFilterFields($this->webserviceXml->operations->read->list, true, true);

		if (!empty($filters))
		{
			$filtersDef = array('name' => 'filters', 'transform' => 'arraydefined', 'fields' => $filters);
		}

		// Add read list messages
		$inputFields = array(
			array('name' => 'limitStart', 'transform' => 'int'),
			array('name' => 'limit', 'transform' => 'int'),
			array('name' => 'filterSearch', 'transform' => 'string'),
			$filtersDef,
			array('name' => 'ordering', 'transform' => 'string'),
			array('name' => 'orderingDirection', 'transform' => 'string'),
			array('name' => 'language', 'transform' => 'string'),
		);

		// Add read list response messages
		$outputFields = array(
			array(
				'name' => 'list', 'transform' => 'arrayrequired', 'fields' =>
				array(
					array(
						'name' => 'item',
						'maxOccurs' => 'unbounded',
						'transform' => 'arrayrequired',
						'fields' => RApiSoapHelper::getOutputResources($this->webserviceXml->operations->read->list, 'listItem')
					)
				)
			)
		);

		$this->addOperation($this->wsdl, 'readList', $inputFields, $outputFields, true, true);
	}

	/**
	 * Add operation to a Wsdl document
	 *
	 * @param   SimpleXMLElement  &$wsdl                   Wsdl document
	 * @param   string            $name                    Operation name
	 * @param   array             $inputFields             Message input fields
	 * @param   array             $outputFields            Message output fields
	 * @param   boolean           $validateOptionalInput   Optional parameter to validate if the inputs are optional or if they're set as required
	 * @param   boolean           $validateOptionalOutput  Optional parameter to validate if the outputs are optional or if they're set as required
	 *
	 * @return  void
	 */
	public function addOperation(&$wsdl, $name, $inputFields, $outputFields, $validateOptionalInput = false, $validateOptionalOutput = false)
	{
		$this->addMessage($wsdl, $name . 'Request', $name);
		$this->addMessage($wsdl, $name . 'Response');
		$this->addPortType($wsdl, $name);
		$this->addBinding($wsdl, $name);

		$complexArrays = (isset($this->webserviceXml->complexArrays) ? $this->webserviceXml->complexArrays : null);

		RApiSoapHelper::addElementFields($inputFields, $this->typeSchema, '', $validateOptionalInput, $name, $complexArrays);
		RApiSoapHelper::addElementFields($outputFields, $this->typeSchema, '', $validateOptionalOutput, $name . 'Response', $complexArrays);
	}

	/**
	 * Add messages to the WSDL document
	 *
	 * @param   SimpleXMLElement  &$wsdl        Wsdl document
	 * @param   string            $messageName  Message name
	 * @param   string            $elementName  Element name
	 *
	 * @return  void
	 */
	public function addMessage(&$wsdl, $messageName, $elementName = '')
	{
		// Add new message
		$message = $wsdl->addChild('message');
		$message->addAttribute('name', $messageName);

		if ($elementName == '')
		{
			$elementName = $messageName;
		}

		$messagePart = $message->addChild('part');
		$messagePart->addAttribute('name', 'parameters');
		$messagePart->addAttribute('element', 'tns:' . $elementName);
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
			$this->portType->addAttribute('name', $this->webserviceFullName);
		}

		// Add port operation
		$portOperation = $this->portType->addChild('operation');
		$portOperation->addAttribute('name', $portName);

		// Input operation
		$inputOperation = $portOperation->addChild('input');
		$inputOperation->addAttribute('message', 'tns:' . $portName . 'Request');

		// Output operation
		$outputOperation = $portOperation->addChild('output');
		$outputOperation->addAttribute('message', 'tns:' . $portName . 'Response');
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
			$this->binding->addAttribute('name', $this->webserviceFullName);
			$this->binding->addAttribute('type', 'tns:' . $this->webserviceFullName);

			// Apply soap binding
			$soapBinding = $this->binding->addChild('soap:binding', null, 'http://schemas.xmlsoap.org/wsdl/soap12/');
			$soapBinding->addAttribute('transport', 'http://schemas.xmlsoap.org/soap/http');
		}

		$this->addSpecificBinding($this->binding, $operationName, 'http://schemas.xmlsoap.org/wsdl/soap12/');
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
	 * Method to add readItem operation to WSDL
	 *
	 * @return void
	 */
	private function addReadItemOperation()
	{
		if (!isset($this->webserviceXml->operations->read->item))
		{
			return;
		}

		// Add read item messages
		$inputFields = array_merge(
			RApiHalHelper::getFieldsArray($this->webserviceXml->operations->read->item, true),
			array(array('name' => 'language', 'transform' => 'string', 'optionalSoapField' => 'true'))
		);

		// Add read item response messages
		$outputFields = array(
			array(
				'name' => 'item',
				'typeName' => 'item',
				'transform' => 'arrayrequired',
				'fields' => RApiSoapHelper::getOutputResources($this->webserviceXml->operations->read->item)
			)
		);

		$this->addOperation($this->wsdl, 'readItem', $inputFields, $outputFields, false, true);
	}

	/**
	 * Method to add create operation to WSDL
	 *
	 * @return void
	 */
	private function addCreateOperation()
	{
		if (!isset($this->webserviceXml->operations->create))
		{
			return;
		}

		// Add create messages
		$inputFields = RApiHalHelper::getFieldsArray($this->webserviceXml->operations->create);

		// Add create response messages
		$outputFields = array(RApiSoapHelper::getResultResource($this->webserviceXml->operations->create));

		$this->addOperation($this->wsdl, 'create', $inputFields, $outputFields, true);
	}

	/**
	 * Method to add update operation to WSDL
	 *
	 * @return void
	 */
	private function addUpdateOperation()
	{
		if (!isset($this->webserviceXml->operations->update))
		{
			return;
		}

		// Add update messages
		$inputFields = RApiHalHelper::getFieldsArray($this->webserviceXml->operations->update);

		// Add update response messages
		$outputFields = array(RApiSoapHelper::getResultResource($this->webserviceXml->operations->update));

		$this->addOperation($this->wsdl, 'update', $inputFields, $outputFields, true);
	}

	/**
	 * Method to add delete operation to WSDL
	 *
	 * @return void
	 */
	private function addDeleteOperation()
	{
		if (!isset($this->webserviceXml->operations->delete))
		{
			return;
		}

		// Add delete messages
		$inputFields = RApiHalHelper::getFieldsArray($this->webserviceXml->operations->delete, true);

		// Add delete response messages
		$outputFields = array(RApiSoapHelper::getResultResource($this->webserviceXml->operations->delete));

		$this->addOperation($this->wsdl, 'delete', $inputFields, $outputFields);
	}

	/**
	 * Method to add task operations to WSDL
	 *
	 * @return void
	 */
	private function addTaskOperations()
	{
		if (!isset($this->webserviceXml->operations->task))
		{
			return;
		}

		foreach ($this->webserviceXml->operations->task->children() as $taskName => $task)
		{
			// Add task messages
			$inputFields = RApiHalHelper::getFieldsArray($task);

			// Add task response messages
			$outputFields = array(RApiSoapHelper::getResultResource($task));

			$this->addOperation($this->wsdl, 'task_' . $taskName, $inputFields, $outputFields, true);
		}
	}
}

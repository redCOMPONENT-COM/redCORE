<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');

/**
 * Webservice Model
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.4
 */
class RedcoreModelWebservice extends RModelAdmin
{
	/**
	 * @var SimpleXMLElement
	 */
	public $xmlFile;

	/**
	 * @var SimpleXMLElement
	 */
	public $defaultXmlFile;

	/**
	 * @var string
	 */
	public $operationXml;

	/**
	 * @var array
	 */
	public $formData = array();

	/**
	 * @var array
	 */
	public $fields;

	/**
	 * @var array
	 */
	public $resources;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  Configuration array
	 *
	 * @throws  RuntimeException
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->defaultXmlFile = new SimpleXMLElement(file_get_contents(JPATH_COMPONENT_ADMINISTRATOR . '/models/forms/webservice_defaults.xml'));
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @throws  RuntimeException
	 *
	 * @since   1.2
	 */
	public function save($data)
	{
		try
		{
			if (!$this->saveXml($data))
			{
				return false;
			}
		}
		catch (Exception $e)
		{
			$this->setError(JText::sprintf('COM_REDCORE_WEBSERVICE_ERROR_SAVING_XML', $e->getMessage()));

			return false;
		}

		/** @var RedcoreModelWebservices $model */
		$model = RModelAdmin::getAdminInstance('Webservices', array(), 'com_redcore');

		if ($id = $model->installWebservice(
				$data['main']['client'],
				$data['main']['name'],
				$data['main']['version'],
				$data['main']['path'],
				$data['main']['id']
		))
		{
			$this->setState($this->getName() . '.id', $id);
			$this->setState($this->getName() . '.new', empty($data['main']['id']));

			// Update created, modified flags
			return parent::save(array('id' => $id));
		}

		return false;
	}

	/**
	 * Method to save the form data to XML file.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @throws  RuntimeException
	 *
	 * @since   1.4
	 */
	public function saveXml($data)
	{
		$dataRegistry = new JRegistry($data);
		$item = null;

		if (empty($data['main']['name']))
		{
			$this->setError(JText::_('COM_REDCORE_WEBSERVICE_NAME_FIELD_CANNOT_BE_EMPTY'));

			return false;
		}

		if (!empty($data['main']['id']))
		{
			$item = $this->getItem($data['main']['id']);
		}

		$client = $dataRegistry->get('main.client', 'site');
		$name = $dataRegistry->get('main.name', '');
		$version = $dataRegistry->get('main.version', '1.0.0');
		$folder = $dataRegistry->get('main.path', '');
		$folder = !empty($folder) ? JPath::clean('/' . $folder) : '';

		if (!JFolder::exists(RApiHalHelper::getWebservicesPath() . $folder))
		{
			JFolder::create(RApiHalHelper::getWebservicesPath() . $folder);
		}

		$fullPath = JPath::clean(RApiHalHelper::getWebservicesPath() . $folder . '/' . $client . '.' . $name . '.' . $version . '.xml');

		$xml = new SimpleXMLElement('<?xml version="1.0"?><apiservice client="' . $client . '"></apiservice>');

		$xml->addChild('name', $dataRegistry->get('main.title', $name));
		$xml->addChild('author', $dataRegistry->get('main.author', ''));
		$xml->addChild('copyright', $dataRegistry->get('main.copyright', ''));
		$xml->addChild('description', $dataRegistry->get('main.description', ''));

		$configXml = $xml->addChild('config');
		$configXml->addChild('name', $dataRegistry->get('main.name', ''));
		$configXml->addChild('version', $version);
		$configXml->addChild('authorizationAssetName', $dataRegistry->get('main.authorizationAssetName', ''));

		$operationsXml = $xml->addChild('operations');
		$readXml = null;
		$taskXml = null;

		foreach ($data as $operationName => $operation)
		{
			if ($operationName == 'main'
				|| empty($operation['isEnabled']))
			{
				continue;
			}

			$operationNameSplit = explode('-', $operationName);

			if ($operationNameSplit[0] == 'read' && count($operationNameSplit) > 1)
			{
				if (is_null($readXml))
				{
					$readXml = $operationsXml->addChild('read');
				}

				$operationXml = $readXml->addChild($operationNameSplit[1]);
			}
			elseif ($operationNameSplit[0] == 'task' && count($operationNameSplit) > 1)
			{
				if (is_null($taskXml))
				{
					$taskXml = $operationsXml->addChild('task');
				}

				$operationXml = $taskXml->addChild($operationNameSplit[1]);
			}
			else
			{
				$operationXml = $operationsXml->addChild($operationNameSplit[0]);
			}

			$this->getOperationAttributesFromPost($operationXml, $data, $operationName);
			$this->getFieldsFromPost($operationXml, $data, $operationName);
			$this->getResourcesFromPost($operationXml, $data, $operationName);
		}

		$originalXml = simplexml_load_file($fullPath);
		$this->addComplexArrays($xml, $originalXml);

		// Needed for formatting
		$dom = dom_import_simplexml($xml)->ownerDocument;
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;

		if (!$dom->save($fullPath))
		{
			return false;
		}

		if (!empty($item->id))
		{
			$folder = !empty($item->path) ? '/' . $item->path : '';
			$oldPath = JPath::clean(RApiHalHelper::getWebservicesPath() . $folder . '/' . $item->xmlFile);

			if ($oldPath != $fullPath)
			{
				if (JFile::exists($oldPath))
				{
					JFile::delete($oldPath);
				}
			}
		}

		$wsdl = RApiSoapHelper::generateWsdl($xml);
		$fullWsdlPath = substr($fullPath, 0, -4) . '.wsdl';

		return RApiSoapHelper::saveWsdlContentToPath($wsdl, $fullWsdlPath);
	}

	/**
	 * Method to get operation attributes from Post
	 *
	 * @param   SimpleXMLElement  &$xml  Xml element
	 * @param   array             $data  The form data.
	 * @param   string            $name  Name to fetch
	 *
	 * @return  void
	 *
	 * @since   1.4
	 */
	public function getOperationAttributesFromPost(&$xml, $data, $name)
	{
		if (empty($data[$name]))
		{
			return;
		}

		foreach ($data[$name] as $attributeKey => $attributeValue)
		{
			if (in_array($attributeKey, array('isEnabled'))
				|| is_array($attributeValue))
			{
				continue;
			}

			if ($attributeKey != 'description')
			{
				$xml->addAttribute($attributeKey, $attributeValue);

				continue;
			}

			if (!empty($attributeValue))
			{
				$this->addChildWithCDATA($xml, $attributeKey, $attributeValue);
			}
		}
	}

	/**
	 * Method to get fields from Post
	 *
	 * @param   SimpleXMLElement  &$xml  Xml element
	 * @param   array             $data  The form data.
	 * @param   string            $name  Name to fetch
	 *
	 * @return  void
	 *
	 * @since   1.4
	 */
	public function getFieldsFromPost(&$xml, $data, $name)
	{
		$mainFieldsXml = null;

		if (!empty($data[$name]['fields']['field']) || !empty($data[$name]['fields']['description']))
		{
			$mainFieldsXml = $xml->addChild('fields');
		}

		if (!empty($data[$name]['fields']['field']))
		{
			foreach ($data[$name]['fields']['field'] as $fieldJson)
			{
				$field = json_decode($fieldJson, true);

				if (empty($field))
				{
					continue;
				}

				$fieldChild = $mainFieldsXml->addChild('field');

				foreach ($field as $attributeKey => $attributeValue)
				{
					if ($attributeKey != 'description')
					{
						$fieldChild->addAttribute($attributeKey, $attributeValue);

						continue;
					}

					if (!empty($attributeValue))
					{
						$this->addChildWithCDATA($fieldChild, 'description', $attributeValue);
					}
				}
			}
		}

		if (!empty($data[$name]['fields']['description']))
		{
			$this->addChildWithCDATA($mainFieldsXml, 'description', $data[$name]['fields']['description']);
		}
	}

	/**
	 * Method to get resources from Post
	 *
	 * @param   SimpleXMLElement  &$xml  Xml element to add resources
	 * @param   array             $data  The form data.
	 * @param   string            $name  Name to fetch
	 *
	 * @return  void
	 *
	 * @since   1.4
	 */
	public function getResourcesFromPost(&$xml, $data, $name)
	{
		$mainResourcesXml = null;

		if (!empty($data[$name]['resources']['resource']) || !empty($data[$name]['resources']['description']))
		{
			$mainResourcesXml = $xml->addChild('resources');
		}

		if (!empty($data[$name]['resources']['resource']))
		{
			foreach ($data[$name]['resources']['resource'] as $resourceJson)
			{
				$resource = json_decode($resourceJson, true);

				if (empty($resource))
				{
					continue;
				}

				$resourceChild = $mainResourcesXml->addChild('resource');

				foreach ($resource as $attributeKey => $attributeValue)
				{
					if ($attributeKey != 'description')
					{
						$resourceChild->addAttribute($attributeKey, $attributeValue);

						continue;
					}

					if (!empty($attributeValue))
					{
						$this->addChildWithCDATA($resourceChild, 'description', $attributeValue);
					}
				}
			}
		}

		if (!empty($data[$name]['resources']['description']))
		{
			$this->addChildWithCDATA($mainResourcesXml, 'description', $data[$name]['resources']['description']);
		}
	}

	/**
	 * Method to add child with text inside CDATA
	 *
	 * @param   SimpleXMLElement  &$xml   Xml element
	 * @param   string            $name   Name of the child
	 * @param   string            $value  Value of the child
	 *
	 * @return  SimpleXMLElement
	 *
	 * @since   1.4
	 */
	public function addChildWithCDATA(&$xml, $name, $value = '')
	{
		$newChild = $xml->addChild($name);

		if (is_null($newChild))
		{
			return $newChild;
		}

		$node = dom_import_simplexml($newChild);
		$no   = $node->ownerDocument;
		$node->appendChild($no->createCDATASection($value));

		return $newChild;
	}

	/**
	 * Method to transfer the orignal complexArray xml to the new WS xml
	 *
	 * @param   SimpleXMLElement  $xml          the new xml definition
	 * @param   SimpleXMLElement  $originalXml  the original definition
	 *
	 * @return void
	 */
	private function addComplexArrays($xml, $originalXml)
	{
		$nodes = $originalXml->xpath('//complexArrays/*');

		if (!$nodes)
		{
			return;
		}

		$complexArrays = $xml->addChild('complexArrays');

		foreach ($nodes AS $node)
		{
			$this->appendXML($complexArrays, $node);
		}
	}

	/**
	 * Add SimpleXMLElement fragment to another SimpleXMLElement
	 *
	 * @param   SimpleXMLElement  $targetXml    the xml to append to
	 * @param   SimpleXMLElement  $originalXml  the xml to append to targetXml
	 *
	 * @return void
	 */
	private function appendXML(SimpleXMLElement $targetXml, SimpleXMLElement $originalXml)
	{
		if ($originalXml && strlen(trim((string) $originalXml)) == 0)
		{
			$xml = $targetXml->addChild($originalXml->getName());

			foreach ($originalXml->children() as $child)
			{
				$this->appendXML($xml, $child);
			}
		}
		elseif($originalXml->getName() == 'description')
		{
			$xml = $this->addChildWithCDATA($targetXml, $originalXml->getName(), (string) $originalXml);
		}
		else
		{
			$xml = $targetXml->addChild($originalXml->getName(), (string) $originalXml);
		}

		foreach ($originalXml->attributes() as $name => $value)
		{
			// If it is not set then we just set it.
			if (!isset($xml[$name]))
			{
				$xml->addAttribute($name, '');
			}

			$xml[$name] = trim($value);
		}
	}

	/**
	 * Load item object
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   1.2
	 */
	public function getItem($pk = null)
	{
		if (!$item = parent::getItem($pk))
		{
			return $item;
		}

		if (!empty($item->id) && is_null($this->xmlFile))
		{
			try
			{
				$this->xmlFile = RApiHalHelper::loadWebserviceConfiguration(
					$item->name, $item->version, 'xml', $item->path, $item->client
				);
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_REDCORE_WEBSERVICE_ERROR_LOADING_XML', $e->getMessage()), 'error');
			}
		}

		// Add default webservice parameters since this is new webservice
		if (empty($this->xmlFile))
		{
			$this->xmlFile = $this->defaultXmlFile;
		}

		return $item;
	}

	/**
	 * Return mapped array for the form data
	 *
	 * @return  array
	 *
	 * @since   1.4
	 */
	public function bindXMLToForm()
	{
		// Read operation is a special because it is part of the two separate read types
		$this->formData = array('read-list' => array(), 'read-item' => array());

		if (empty($this->xmlFile))
		{
			return $this->formData;
		}

		$this->formData['main'] = array(
			'author' => (string) $this->xmlFile->author,
			'copyright' => (string) $this->xmlFile->copyright,
			'description' => (string) $this->xmlFile->description,
			'authorizationAssetName' => !empty($this->xmlFile->config->authorizationAssetName) ? (string) $this->xmlFile->config->authorizationAssetName : '',
		);

		// Get attributes and descriptions
		$this->bindAttributesToFormData();

		// Set default operations if not present in loaded XML file
		if (!$operations = $this->defaultXmlFile->xpath('//operations'))
		{
			return $this->formData;
		}

		$operations = $operations[0];

		foreach ($operations as $name => $operation)
		{
			if (!empty($this->formData[$name]))
			{
				continue;
			}

			if ($name == 'read')
			{
				if (empty($this->formData[$name . '-list']))
				{
					$this->formData[$name . '-list'] = $this->bindPathToArray('//operations/' . $name . '/list', $this->defaultXmlFile);
					$this->setFieldsAndResources($name . '-list', '//operations/' . $name . '/list', $this->defaultXmlFile);
				}

				if (empty($this->formData[$name . '-item']))
				{
					$this->formData[$name . '-item'] = $this->bindPathToArray('//operations/' . $name . '/item', $this->defaultXmlFile);
					$this->setFieldsAndResources($name . '-item', '//operations/' . $name . '/item', $this->defaultXmlFile);
				}

				continue;
			}

			$this->formData[$name] = $this->bindPathToArray('//operations/' . $name, $this->defaultXmlFile);
			$this->setFieldsAndResources($name, '//operations/' . $name, $this->defaultXmlFile);
		}

		return $this->formData;
	}

	/**
	 * Method to bind attributes to $this->formData
	 *
	 * @return void
	 */
	private function bindAttributesToFormData()
	{
		if (!$operations = $this->xmlFile->xpath('//operations'))
		{
			return;
		}

		$operations = $operations[0];

		foreach ($operations as $name => $operation)
		{
			switch ($name)
			{
				case 'read':
					$this->bindReadAttributesToFormData();
					break;
				case 'task':
					$this->bindTaskAttributesToFormData();
					break;
				default:
					$this->bindDefaultAttributesToFormData($name);
					break;
			}
		}
	}

	/**
	 * Method to bind read operation attributes to $this->formData
	 *
	 * @return void
	 */
	private function bindReadAttributesToFormData()
	{
		$this->formData['read-list'] = $this->bindPathToArray('//operations/read/list', $this->xmlFile);
		$this->formData['read-item'] = $this->bindPathToArray('//operations/read/item', $this->xmlFile);

		$this->setFieldsAndResources('read-list', '//operations/read/list', $this->xmlFile);
		$this->setFieldsAndResources('read-item', '//operations/read/item', $this->xmlFile);

		if (!empty($this->formData['read-list']) && !isset($this->formData['read-list']['isEnabled']))
		{
			// Since this operation exists in XML file we are enabling it by default
			$this->formData['read-list']['isEnabled'] = 1;
		}

		if (!empty($this->formData['read-item']) && !isset($this->formData['read-item']['isEnabled']))
		{
			// Since this operation exists in XML file we are enabling it by default
			$this->formData['read-item']['isEnabled'] = 1;
		}
	}

	/**
	 * Method to bind task operation attributes to $this->formData
	 *
	 * @return void
	 */
	private function bindTaskAttributesToFormData()
	{
		if (!$tasks = $this->xmlFile->xpath('//operations/task'))
		{
			return;
		}

		$tasks = $tasks[0];

		foreach ($tasks as $taskName => $task)
		{
			$this->formData['task-' . $taskName] = $this->bindPathToArray('//operations/task/' . $taskName, $this->xmlFile);
			$this->setFieldsAndResources('task-' . $taskName, '//operations/task/' . $taskName, $this->xmlFile);

			if (!empty($this->formData['task-' . $taskName]) && !isset($this->formData['task-' . $taskName]['isEnabled']))
			{
				// Since this operation exists in XML file we are enabling it by default
				$this->formData['task-' . $taskName]['isEnabled'] = 1;
			}
		}
	}

	/**
	 * Method to bind all other operation attributes to $this->formData
	 *
	 * @param   string  $name  of the operation
	 *
	 * @return void
	 */
	private function bindDefaultAttributesToFormData($name)
	{
		$this->formData[$name] = $this->bindPathToArray('//operations/' . $name, $this->xmlFile);
		$this->setFieldsAndResources($name, '//operations/' . $name, $this->xmlFile);

		if (!empty($this->formData[$name]) && !isset($this->formData[$name]['isEnabled']))
		{
			// Since this operation exists in XML file we are enabling it by default
			$this->formData[$name]['isEnabled'] = 1;
		}
	}

	/**
	 * Return mapped array for the form data
	 *
	 * @param   string            $path  Path to the XML element
	 * @param   SimpleXMLElement  $xml   XML file
	 *
	 * @return  array
	 *
	 * @since   1.4
	 */
	public function bindPathToArray($path, $xml)
	{
		if (!$element = $xml->xpath($path))
		{
			return array();
		}

		$element = $element[0];

		return $this->bindElementToArray($element);
	}

	/**
	 * Return mapped array for the form data
	 *
	 * @param   SimpleXMLElement  $element  XML element
	 *
	 * @return  array
	 *
	 * @since   1.4
	 */
	public function bindElementToArray($element)
	{
		$data = array();

		if (empty($element))
		{
			return $data;
		}

		foreach ($element->attributes() as $key => $val)
		{
			$data[$key] = (string) $val;
		}

		$data['description'] = !empty($element->description) ? (string) $element->description : '';

		if (!empty($element->fields->description))
		{
			$data['fields']['description'] = (string) $element->fields->description;
		}

		if (!empty($element->resources->description))
		{
			$data['resources']['description'] = (string) $element->resources->description;
		}

		return $data;
	}

	/**
	 * Gets Fields and Resources from given path
	 *
	 * @param   string            $name  Operation or task name
	 * @param   string            $path  Path to the operation or the task
	 * @param   SimpleXMLElement  $xml   XML file
	 *
	 * @return  void
	 *
	 * @since   1.4
	 */
	public function setFieldsAndResources($name, $path, $xml)
	{
		// Get fields
		$this->setFieldsByXpath($name, $path, $xml);

		// Get resources
		$this->setResourcesByXpath($name, $path, $xml);
	}

	/**
	 * sets Fields from given path to $this->fields
	 *
	 * @param   string            $name  Operation or task name
	 * @param   string            $path  Path to the operation or the task
	 * @param   SimpleXMLElement  $xml   XML file
	 *
	 * @return  void
	 *
	 * @since   1.7
	 */
	private function setFieldsByXpath($name, $path, $xml)
	{
		if (!$fields = $xml->xpath($path . '/fields/field'))
		{
			return;
		}

		foreach ($fields as $field)
		{
			$fieldArray = $this->bindElementToArray($field);
			$displayName = (string) $fieldArray['name'];
			$this->fields[$name][$displayName] = $fieldArray;
		}
	}

	/**
	 * sets Resources from given path to $this->resources
	 *
	 * @param   string            $name  Operation or task name
	 * @param   string            $path  Path to the operation or the task
	 * @param   SimpleXMLElement  $xml   XML file
	 *
	 * @return  void
	 *
	 * @since   1.7
	 */
	private function setResourcesByXpath($name, $path, $xml)
	{
		if (!$resources = $xml->xpath($path . '/resources/resource'))
		{
			return;
		}

		foreach ($resources as $resource)
		{
			$resourceArray = $this->bindElementToArray($resource);
			$displayName = (string) $resourceArray['displayName'];
			$resourceSpecific = !empty($resourceArray['resourceSpecific']) ? (string) $resourceArray['resourceSpecific'] : 'rcwsGlobal';

			$this->resources[$name][$resourceSpecific][$displayName] = $resourceArray;
		}
	}

	/**
	 * Method to load a operation form template.
	 *
	 * @return  string  Xml
	 */
	public function loadFormOperationXml()
	{
		if (is_null($this->operationXml))
		{
			$this->operationXml = @file_get_contents(JPATH_COMPONENT_ADMINISTRATOR . '/models/forms/webservice_operation.xml');
		}

		return $this->operationXml;
	}

	/**
	 * Method to get a form object.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		if (!$form = parent::getForm($data, $loadData))
		{
			return false;
		}

		// Load dynamic form for operations
		$form->load(str_replace('"operation"', '"create"', $this->loadFormOperationXml()));
		$form->load(str_replace('"operation"', '"read-list"', $this->loadFormOperationXml()));
		$form->load(str_replace('"operation"', '"read-item"', $this->loadFormOperationXml()));
		$form->load(str_replace('"operation"', '"update"', $this->loadFormOperationXml()));
		$form->load(str_replace('"operation"', '"delete"', $this->loadFormOperationXml()));

		if (!empty($data))
		{
			foreach ($data as $operationName => $operation)
			{
				if (substr($operationName, 0, strlen('task-')) === 'task-')
				{
					$form->load(str_replace('"operation"', '"' . $operationName . '"', $this->loadFormOperationXml()));
				}
			}
		}

		if (!empty($this->xmlFile) && $tasks = $this->xmlFile->xpath('//operations/task'))
		{
			$tasks = $tasks[0];

			foreach ($tasks as $taskName => $task)
			{
				$form->load(str_replace('"operation"', '"task-' . $taskName . '"', $this->loadFormOperationXml()));
			}
		}

		$form->bind($this->formData);

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The default data is an empty array.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState(
			$this->context . '.data',
			array()
		);

		if (!empty($data))
		{
			return $data;
		}

		$dataDb = $this->getItem();
		$data = $this->bindXMLToForm();

		$dataArray = JArrayHelper::fromObject($dataDb);
		$dataEmpty = array('main' => array());
		$data = array_merge($dataEmpty, $data);

		$data['main'] = array_merge($dataArray, $data['main']);

		return $data;
	}

	/**
	 * Method to get the additional complex transform types from the WS xml complexArray
	 *
	 * @return array
	 */
	public function getComplexTransforms()
	{
		$complexArrayItems = $this->xmlFile->xpath('//complexArrays/*');
		$transforms = array();

		foreach ($complexArrayItems AS $transformType)
		{
			$value = 'array[' . $transformType->getName() . ']';
			$transforms[] = array('value' => $value, 'text' => $value);
		}

		return $transforms;
	}
}

<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

use Joomla\Registry\Registry;

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');

use Joomla\Utilities\ArrayHelper;

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
	 * @var string
	 */
	public $complexTypeXml;

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
		$model = RModelAdmin::getAdminInstance('Webservices', array('ignore_request' => true), 'com_redcore');

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
	 * @return  boolean       True on success, False on error.
	 *
	 * @throws  RuntimeException
	 *
	 * @since   1.4
	 */
	public function saveXml($data)
	{
		$dataRegistry = new Registry($data);
		$item         = null;

		if (empty($data['main']['name']))
		{
			$this->setError(JText::_('COM_REDCORE_WEBSERVICE_NAME_FIELD_CANNOT_BE_EMPTY'));

			return false;
		}

		if (!empty($data['main']['id']))
		{
			$item = $this->getItem($data['main']['id']);
		}

		$client  = $dataRegistry->get('main.client', 'site');
		$name    = $dataRegistry->get('main.name', '');
		$version = $dataRegistry->get('main.version', '1.0.0');
		$folder  = $dataRegistry->get('main.path', '');
		$folder  = !empty($folder) ? JPath::clean('/' . $folder) : '';

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
		$readXml       = null;
		$taskXml       = null;

		foreach ($data as $operationName => $operation)
		{
			if ($operationName == 'main'
				|| empty($operation['isEnabled'])
				|| substr($operationName, 0, strlen('type-')) == 'type-')
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

		$complexArrays = $xml->addChild('complexArrays');

		foreach ($data as $typeName => $typeData)
		{
			if (substr($typeName, 0, strlen('type-')) != 'type-')
			{
				continue;
			}

			$typeNameSplit = explode('-', $typeName);

			$typeXml = $complexArrays->addChild($typeNameSplit[1]);
			$this->getOperationAttributesFromPost($typeXml, $data, $typeName);
			$this->getFieldsFromPost($typeXml, $data, $typeName);
		}

		// Needed for formatting
		$dom                     = dom_import_simplexml($xml)->ownerDocument;
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput       = true;

		if (!$dom->save($fullPath))
		{
			return false;
		}

		if (!empty($item->id))
		{
			$folder  = !empty($item->path) ? '/' . $item->path : '';
			$oldPath = JPath::clean(RApiHalHelper::getWebservicesPath() . $folder . '/' . $item->xmlFile);

			if ($oldPath != $fullPath)
			{
				if (JFile::exists($oldPath))
				{
					// Xml file
					JFile::delete($oldPath);

					// Wsdl file
					$oldPathWsdl = substr($oldPath, 0, strlen($oldPath) - 4) . '.wsdl';

					if (JFile::exists($oldPathWsdl))
					{
						JFile::delete($oldPathWsdl);
					}
				}
			}
		}

		$wsdl         = RApiSoapHelper::generateWsdl($xml, null, $data['main']['path']);
		$fullWsdlPath = substr($fullPath, 0, -4) . '.wsdl';

		return (boolean) RApiSoapHelper::saveWsdlContentToPath($wsdl, $fullWsdlPath);
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
		elseif ($originalXml->getName() == 'description')
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

				if (substr($operationName, 0, strlen('type-')) == 'type-')
				{
					$form->load(str_replace('"operation"', '"' . $operationName . '"', $this->loadFormComplexTypeXml()));
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

		if (!empty($this->xmlFile) && $complexTypes = $this->xmlFile->xpath('//complexArrays'))
		{
			$complexTypes = $complexTypes[0];

			foreach ($complexTypes AS $typeName => $type)
			{
				$form->load(str_replace('"operation"', '"type-' . $typeName . '"', $this->loadFormComplexTypeXml()));
			}
		}

		$form->bind($this->formData);

		return $form;
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
	 * Method to load a complex type form template.
	 *
	 * @return  string  Xml
	 */
	public function loadFormComplexTypeXml()
	{
		if (is_null($this->complexTypeXml))
		{
			$this->complexTypeXml = @file_get_contents(JPATH_COMPONENT_ADMINISTRATOR . '/models/forms/webservice_complex_type.xml');
		}

		return $this->complexTypeXml;
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
		$data   = $this->bindXMLToForm();

		$dataArray = ArrayHelper::fromObject($dataDb);
		$dataEmpty = array('main' => array());
		$data      = array_merge($dataEmpty, $data);

		$data['main'] = array_merge($dataArray, $data['main']);

		return $data;
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
		$this->bindAttributesToFormData($this->defaultXmlFile);
		$this->bindAttributesToFormData($this->xmlFile, true);

		return $this->formData;
	}

	/**
	 * Method to bind attributes to $this->formData
	 *
	 * @param   SimpleXMLElement  $xml        the xml file to bind
	 * @param   bool              $overwrite  should be overwrite existing operations in the form data
	 *
	 * @return void
	 */
	private function bindAttributesToFormData($xml, $overwrite = false)
	{
		// Bind complex types first
		$this->bindComplexTypesToFormData($xml, $overwrite);

		if (!$operations = $xml->xpath('//operations'))
		{
			return;
		}

		$operations = $operations[0];

		foreach ($operations as $name => $operation)
		{
			if (!empty($this->formData[$name]) && !$overwrite)
			{
				continue;
			}

			switch ($name)
			{
				case 'read':
					$this->bindReadAttributesToFormData($xml);
					break;
				case 'task':
					$this->bindTaskAttributesToFormData($xml);
					break;
				default:
					$this->bindDefaultAttributesToFormData($xml, $name);
					break;
			}
		}
	}

	/**
	 * Method to bind complex type attributes to $this->formData
	 *
	 * @param   SimpleXMLElement  $xml        the xml file to read from
	 * @param   bool              $overwrite  should be overwrite existing operations in the form data
	 *
	 * @return void
	 */
	private function bindComplexTypesToFormData($xml, $overwrite = false)
	{
		if (!$complexTypes = $xml->xpath('//complexArrays/*'))
		{
			return;
		}

		foreach ($complexTypes as $type)
		{
			$typeName = (string) $type->getName();

			if (!empty($this->formData['type-' . $typeName]) && !$overwrite)
			{
				continue;
			}

			$this->formData['type-' . $typeName] = $this->bindPathToArray('//complexArrays/' . $typeName, $xml);
			$this->setPropertyByXpath('fields', 'type-' . $typeName, '//complexArrays/' . $typeName . '/fields/field', $xml);
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
	 * Method to set a property from a given path to $this->{$propertyName}[name]
	 *
	 * @param   string            $propertyName  The name of the model property (I.E. fields,resources, types
	 * @param   string            $name          Operation or task name
	 * @param   string            $path          Path to the operation or the task
	 * @param   SimpleXMLElement  $xml           XML file
	 *
	 * @return  void
	 *
	 * @since   1.7
	 */
	public function setPropertyByXpath($propertyName, $name, $path, $xml)
	{
		if (!$nodes = $xml->xpath($path))
		{
			return;
		}

		foreach ($nodes as $node)
		{
			$properties = $this->bindElementToArray($node);

			if ($propertyName == 'resources')
			{
				$displayName                                                   = (string) $properties['displayName'];
				$resourceSpecific                                              = !empty($properties['resourceSpecific']) ? (string) $properties['resourceSpecific'] : 'rcwsGlobal';
				$this->{$propertyName}[$name][$resourceSpecific][$displayName] = $properties;

				continue;
			}

			$displayName                                = (string) $properties['name'];
			$this->{$propertyName}[$name][$displayName] = $properties;
		}
	}

	/**
	 * Method to bind read operation attributes to $this->formData
	 *
	 * @param   SimpleXMLElement  $xml  the xml file to read from
	 *
	 * @return void
	 */
	private function bindReadAttributesToFormData($xml)
	{
		$this->formData['read-list'] = $this->bindPathToArray('//operations/read/list', $xml);
		$this->formData['read-item'] = $this->bindPathToArray('//operations/read/item', $xml);

		$this->setFieldsAndResources('read-list', '//operations/read/list', $xml);
		$this->setFieldsAndResources('read-item', '//operations/read/item', $xml);

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
		$this->setPropertyByXpath('fields', $name, $path . '/fields/field', $xml);

		// Get resources
		$this->setPropertyByXpath('resources', $name, $path . '/resources/resource', $xml);
	}

	/**
	 * Method to bind task operation attributes to $this->formData
	 *
	 * @param   SimpleXMLElement  $xml  the xml file to read from
	 *
	 * @return void
	 */
	private function bindTaskAttributesToFormData($xml)
	{
		if (!$tasks = $xml->xpath('//operations/task'))
		{
			return;
		}

		$tasks = $tasks[0];

		foreach ($tasks as $taskName => $task)
		{
			$this->formData['task-' . $taskName] = $this->bindPathToArray('//operations/task/' . $taskName, $xml);
			$this->setFieldsAndResources('task-' . $taskName, '//operations/task/' . $taskName, $xml);

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
	 * @param   SimpleXMLElement  $xml   the xml file to read from
	 * @param   string            $name  of the operation
	 *
	 * @return void
	 */
	private function bindDefaultAttributesToFormData($xml, $name)
	{
		$this->formData[$name] = $this->bindPathToArray('//operations/' . $name, $xml);
		$this->setFieldsAndResources($name, '//operations/' . $name, $xml);

		if (!empty($this->formData[$name]) && !isset($this->formData[$name]['isEnabled']))
		{
			// Since this operation exists in XML file we are enabling it by default
			$this->formData[$name]['isEnabled'] = 1;
		}
	}

	/**
	 * Method to get the additional complex transform types from the WS xml complexArray
	 *
	 * @param   string  $operation  the name of current operation or complexArray requesting the list of transforms
	 *
	 * @return array
	 */
	public function getTransformTypes($operation = null)
	{
		$transforms = RApiHalHelper::getTransformElements();

		if (!($this->xmlFile instanceof SimpleXMLElement))
		{
			$this->loadFormData();
		}

		$complexArrayItems = $this->xmlFile->xpath('//complexArrays/*');

		foreach ($complexArrayItems AS $transformType)
		{
			$transformTypeName = $transformType->getName();

			if ('type-' . $transformTypeName == $operation)
			{
				// Do not allow recursive transforms at this time
				continue;
			}

			$value        = 'array[' . $transformTypeName . ']';
			$transforms[] = array('value' => $value, 'text' => $value);
		}

		return $transforms;
	}
}

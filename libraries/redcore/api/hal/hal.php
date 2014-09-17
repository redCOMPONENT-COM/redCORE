<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

/**
 * Class to represent a HAL standard object.
 *
 * @since  1.2
 */
class RApiHalHal extends RApi
{
	/**
	 * Webservice name
	 * @var string
	 */
	public $elementName = null;

	/**
	 * @var    string  Name of the Webservice
	 * @since  1.2
	 */
	public $webserviceName = '';

	/**
	 * @var    string  Version of the Webservice
	 * @since  1.2
	 */
	public $webserviceVersion = '';

	/**
	 * For easier access of current configuration parameters
	 * @var SimpleXMLElement
	 */
	public $operationConfiguration = null;

	/**
	 * Main HAL resource object
	 * @var RApiHalDocumentResource
	 */
	public $hal = null;

	/**
	 * Resource container that will be outputted
	 * @var array
	 */
	public $resources = array();

	/**
	 * Data container that will be used for resource binding
	 * @var array
	 */
	public $data = array();

	/**
	 * Uri parameters that will be added to each link
	 * @var array
	 */
	public $uriParams = array();

	/**
	 * @var    SimpleXMLElement  Api Configuration
	 * @since  1.2
	 */
	public $configuration = null;

	/**
	 * @var    object  Helper class object
	 * @since  1.2
	 */
	public $apiHelperClass = null;

	/**
	 * @var    array  Loaded resources from configuration file
	 * @since  1.2
	 */
	public $apiResources = null;

	/**
	 * Method to instantiate the file-based api call.
	 *
	 * @param   mixed  $options  Optional custom options to load. JRegistry or array format
	 *
	 * @since   1.2
	 */
	public function __construct($options = null)
	{
		parent::__construct($options);

		$this->setWebserviceName();
		$this->webserviceVersion = $this->options->get('webserviceVersion', '');
		$this->hal = new RApiHalDocumentResource('');

		if (!empty($this->webserviceName))
		{
			if (empty($this->webserviceVersion))
			{
				$this->webserviceVersion = RApiHalHelper::getNewestWebserviceVersion($this->webserviceName);
			}

			$this->configuration = RApiHalHelper::loadWebserviceConfiguration($this->webserviceName, $this->webserviceVersion);
			$this->triggerFunction('setResources');
		}

		// Init Environment
		$this->triggerFunction('setApiOperation');
	}

	/**
	 * Sets Webservice name according to given options
	 *
	 * @return  RApi
	 *
	 * @since   1.2
	 */
	public function setWebserviceName()
	{
		$task = $this->options->get('task', '');

		if (empty($task))
		{
			$taskSplit = explode(',', $task);

			if (count($taskSplit) > 1)
			{
				// We will set name of the webservice as a task controller name
				$this->webserviceName = $this->options->get('optionName', '') . '-' . $taskSplit[0];
				$task = $taskSplit[1];
				$this->options->set('task', $task);

				return $this;
			}
		}

		$this->webserviceName = $this->options->get('optionName', '');
		$viewName = $this->options->get('viewName', '');
		$this->webserviceName .= !empty($this->webserviceName) && !empty($viewName) ? '-' . $viewName : '';

		return $this;
	}

	/**
	 * Set Method for Api to be performed
	 *
	 * @return  RApi
	 *
	 * @since   1.2
	 */
	public function setApiOperation()
	{
		$method = $this->options->get('method', 'GET');
		$task = $this->options->get('task', '');

		// Set proper operation for given method
		switch ((string) $method)
		{
			case 'PUT':
				$method = 'CREATE';
				break;
			case 'GET':
				$method = !empty($task) ? 'TASK' : 'READ';
				break;
			case 'POST':
				$method = !empty($task) ? 'TASK' : 'UPDATE';

				break;
			case 'DELETE':
				$method = 'DELETE';
				break;

			default:
				$method = 'READ';
				break;
		}

		// If task is pointing to some other operation like apply, update or delete
		if (!empty($task) && !empty($this->configuration->operations->task->{$task}['useOperation']))
		{
			$operation = strtoupper((string) $this->configuration->operations->task->{$task}['useOperation']);

			if (in_array($operation, array('CREATE', 'READ', 'UPDATE', 'DELETE')))
			{
				$method = $operation;
			}
		}

		$this->operation = strtolower($method);

		return $this;
	}

	/**
	 * Execute the Api operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 * @throws  RuntimeException
	 */
	public function execute()
	{
		if (!empty($this->webserviceName))
		{
			if (!$this->triggerFunction('isOperationAllowed'))
			{
				throw new RuntimeException(JText::_('LIB_REDCORE_API_HAL_OPERATION_NOT_ALLOWED'));
			}

			$this->elementName = ucfirst(strtolower((string) $this->getConfig('config.name')));
			$this->operationConfiguration = $this->getConfig('operations.' . strtolower($this->operation));

			switch ($this->operation)
			{
				case 'create':
					$this->triggerFunction('apiCreate');
					break;
				case 'read':
					$this->triggerFunction('apiRead');
					break;
				case 'update':
					$this->triggerFunction('apiUpdate');
					break;
				case 'delete':
					$this->triggerFunction('apiDelete');
					break;
				case 'task':
					$this->triggerFunction('apiTask');
					break;
			}
		}
		else
		{
			// If default page needs authorization to access it
			$this->isAuthorized('', RTranslationHelper::$pluginParams->get('webservices_default_page_authorization', 0));

			// No webservice name. We display all webservices available
			$this->triggerFunction('apiDefaultPage');
		}

		$messages = JFactory::getApplication()->getMessageQueue();

		if (!empty($messages))
		{
			$this->hal->setData('_messages', $messages);
		}

		// Set links from resources to the main document
		$this->setDataValueToResource($this->hal, $this->resources, $this->data);

		return $this;
	}

	/**
	 * Execute the Api Default Page operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	public function apiDefaultPage()
	{
		// Add standard Joomla namespace as curie.
		$joomlaCurie = new RApiHalDocumentLink('http://docs.joomla.org/Link_relations/{rel}', 'curies');
		$joomlaCurie->setName('joomla')
			->setTemplated(true);

		// Add basic hypermedia links.
		$this->hal->setLink($joomlaCurie, false, true);
		$this->hal->setLink(new RApiHalDocumentLink(rtrim(JUri::base(), '/'), 'base'));

		$webservices = RApiHalHelper::getInstalledWebservices();

		if (!empty($webservices))
		{
			foreach ($webservices as $webserviceName => $webserviceVersions)
			{
				foreach ($webserviceVersions as $webserviceVersion => $webservice)
				{
					if ($webservice['state'] == 1)
					{
						// We will fetch only top level webservice
						$this->hal->setLink(new RApiHalDocumentLink('/index.php?option=' . $webservice['name'], $webservice['name'], $webservice['displayName']));

						break;
					}
				}
			}
		}

		return $this;
	}

	/**
	 * Execute the Api Read operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	public function apiRead()
	{
		$id = $this->options->get('id', '');
		$displayTarget = empty($id) ? 'list' : 'item';
		$currentConfiguration = $this->operationConfiguration->{$displayTarget};
		$model = $this->triggerFunction('loadModel', $this->elementName, $currentConfiguration);

		if ($displayTarget == 'list')
		{
			$getDataFunction = RApiHalHelper::attributeToString($currentConfiguration, 'getDataFunction', 'getItems');

			$items = method_exists($model, $getDataFunction) ? $model->{$getDataFunction}() : array();

			if (method_exists($model, 'getPagination'))
			{
				$pagination = $model->getPagination();
				$paginationPages = $pagination->getPaginationPages();

				$this->setData(
					'pagination.previous', isset($paginationPages['previous']['data']->base) ? $paginationPages['previous']['data']->base : $pagination->limitstart
				);
				$this->setData(
					'pagination.next', isset($paginationPages['next']['data']->base) ? $paginationPages['next']['data']->base : $pagination->limitstart
				);
				$this->setData('pagination.limit', $pagination->limit);
				$this->setData('pagination.limitstart', $pagination->limitstart);
				$this->setData('pagination.totalItems', $pagination->total);
				$this->setData('pagination.totalPages', max($pagination->pagesTotal, 1));
				$this->setData('pagination.page', max($pagination->pagesCurrent, 1));
				$this->setData('pagination.last', ((max($pagination->pagesTotal, 1) - 1) * $pagination->limit));
			}

			$this->triggerFunction('setForRenderList', $items, $currentConfiguration);

			return $this;
		}

		// Getting single item
		$getDataFunction = RApiHalHelper::attributeToString($currentConfiguration, 'getDataFunction', 'getItem');

		$itemObject = method_exists($model, $getDataFunction) ? $model->{$getDataFunction}($id) : array();

		$this->triggerFunction('setForRenderItem', $itemObject, $currentConfiguration);

		return $this;
	}

	/**
	 * Execute the Api Create operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	public function apiCreate()
	{
		// Get resource list from configuration
		$this->loadResourceFromConfiguration($this->operationConfiguration);

		$model = $this->triggerFunction('loadModel', $this->elementName, $this->operationConfiguration);
		$createDataFunction = RApiHalHelper::attributeToString($this->operationConfiguration, 'createDataFunction', 'save');
		$data = $this->triggerFunction('processPostData', $this->options->get('data', array()), $this->operationConfiguration);

		$result = method_exists($model, $createDataFunction) ? $model->{$createDataFunction}($data) : null;

		if (method_exists($model, 'getState'))
		{
			$this->setData('id', $model->getState(strtolower($this->elementName) . '.id'));
		}

		$this->setData('result', $result);
	}

	/**
	 * Execute the Api Delete operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	public function apiDelete()
	{
		// Get resource list from configuration
		$this->loadResourceFromConfiguration($this->operationConfiguration);
		$model = $this->triggerFunction('loadModel', $this->elementName, $this->operationConfiguration);
		$deleteDataFunction = RApiHalHelper::attributeToString($this->operationConfiguration, 'deleteDataFunction', 'delete');
		$data = $this->triggerFunction('processPostData', $this->options->get('data', array()), $this->operationConfiguration);

		$primaryKeys = RApiHalHelper::attributeToString($this->operationConfiguration, 'primaryKeys', 'id');
		$primaryKeys = explode(',', $primaryKeys);

		if (count($primaryKeys) == 1)
		{
			$itemId = isset($data[$primaryKeys[0]]) ? $data[$primaryKeys[0]] : 0;
		}
		else
		{
			$itemId = array();

			foreach ($primaryKeys as $key => $primaryKey)
			{
				$itemId[$key][] = isset($data[$primaryKey]) ? $data[$primaryKey] : 0;
			}
		}

		$result = method_exists($model, $deleteDataFunction) ? $model->{$deleteDataFunction}($itemId) : null;

		$this->setData('result', $result);
	}

	/**
	 * Execute the Api Update operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	public function apiUpdate()
	{
		// Get resource list from configuration
		$this->loadResourceFromConfiguration($this->operationConfiguration);
		$model = $this->triggerFunction('loadModel', $this->elementName, $this->operationConfiguration);
		$updateDataFunction = RApiHalHelper::attributeToString($this->operationConfiguration, 'updateDataFunction', 'save');
		$data = $this->triggerFunction('processPostData', $this->options->get('data', array()), $this->operationConfiguration);

		$result = method_exists($model, $updateDataFunction) ? $model->{$updateDataFunction}($data) : null;

		if (method_exists($model, 'getState'))
		{
			$this->setData('id', $model->getState(strtolower($this->elementName) . '.id'));
		}

		$this->setData('result', $result);
	}

	/**
	 * Execute the Api Task operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	public function apiTask()
	{
		$task = $this->options->get('task', '');
		$result = false;

		if (!empty($task))
		{
			// Get resource list from configuration
			$this->loadResourceFromConfiguration($this->operationConfiguration);

			// Load resources directly from task group
			if (!empty($this->operationConfiguration->{$task}->resources))
			{
				$this->loadResourceFromConfiguration($this->operationConfiguration->{$task});
			}

			$taskConfiguration = !empty($this->operationConfiguration->{$task}) ?
				$this->operationConfiguration->{$task} : $this->operationConfiguration;

			$model = $this->triggerFunction('loadModel', $this->elementName, $taskConfiguration);
			$functionName = RApiHalHelper::attributeToString($taskConfiguration, 'functionName', $task);
			$data = $this->triggerFunction('processPostData', $this->options->get('data', array()), $taskConfiguration);

			if (empty($taskConfiguration['functionName']) && in_array($task, RApiMethods::$methods))
			{
				$result = RApiMethods::$task($model, $taskConfiguration, $data);
			}
			else
			{
				// If primaryKeys is defined then we pass only Id(s)
				if (!empty($taskConfiguration['primaryKeys']))
				{
					$primaryKeys = explode(',', (string) $taskConfiguration['primaryKeys']);

					if (count($primaryKeys) == 1)
					{
						$data = isset($data[$primaryKeys[0]]) ? $data[$primaryKeys[0]] : 0;
					}
					else
					{
						$dataValues = array();

						foreach ($primaryKeys as $key => $primaryKey)
						{
							$dataValues[$key][] = isset($data[$primaryKey]) ? $data[$primaryKey] : 0;
						}

						$data = $dataValues;
					}
				}

				$result = method_exists($model, $functionName) ? $model->{$functionName}($data) : null;
			}

			if (method_exists($model, 'getState'))
			{
				$this->setData('id', $model->getState(strtolower($this->elementName) . '.id'));
			}
		}

		$this->setData('result', $result);
	}

	/**
	 * Set document content for List view
	 *
	 * @param   array             $items          List of items
	 * @param   SimpleXMLElement  $configuration  Configuration for displaying object
	 *
	 * @return void
	 */
	public function setForRenderList($items, $configuration)
	{
		// Get resource list from configuration
		$this->loadResourceFromConfiguration($configuration);

		$listResourcesKeys = array_keys($this->resources['listItem']);

		// Filter out all fields that are not in resource list and apply appropriate transform rules
		foreach ($items as $itemValue)
		{
			$item = JArrayHelper::fromObject($itemValue);

			foreach ($item as $key => $value)
			{
				if (!in_array($key, $listResourcesKeys))
				{
					unset($item[$key]);
					continue;
				}
				else
				{
					$item[$key] = $this->assignValueToResource($this->resources['listItem'][$key], $item);
				}
			}

			$embedItem = new RApiHalDocumentResource('contacts', $item);
			$embedItem = $this->setDataValueToResource($embedItem, $this->resources, $itemValue, 'listItem');
			$this->hal->setEmbedded('contacts', $embedItem);
		}
	}

	/**
	 * Loads Resource list from configuration file for specific method or task
	 *
	 * @param   RApiHalDocumentResource  $resourceDocument  Resource document for binding the resource
	 * @param   array                    $resources         Configuration for displaying object
	 * @param   mixed                    $data              Data to bind to the resources
	 * @param   string                   $resourceSpecific  Resource specific string that separates resources
	 *
	 * @return RApiHalDocumentResource
	 */
	public function setDataValueToResource($resourceDocument, $resources, $data, $resourceSpecific = 'rcwsGlobal')
	{
		if (!empty($resources[$resourceSpecific]))
		{
			// Add links from the resource
			foreach ($resources[$resourceSpecific] as $resource)
			{
				if (!empty($resource['displayGroup']))
				{
					if ($resource['displayGroup'] == '_links')
					{
						$resourceDocument->setLink(
							new RApiHalDocumentLink(
								$this->assignValueToResource($resource, $data),
								$resource['displayName'],
								$resource['linkName'],
								$resource['displayName'],
								$resource['hrefLang'],
								RApiHalHelper::isAttributeTrue($resource, 'linkTemplated')
							)
						);
					}
					else
					{
						$resourceDocument->setDataGrouped($resource['displayGroup'], $resource['displayName'], $this->assignValueToResource($resource, $data));
					}
				}
				else
				{
					$resourceDocument->setData($resource['displayName'], $this->assignValueToResource($resource, $data));
				}
			}
		}

		return $resourceDocument;
	}

	/**
	 * Loads Resource list from configuration file for specific method or task
	 *
	 * @param   SimpleXMLElement  $configuration  Configuration for displaying object
	 *
	 * @return void
	 */
	public function loadResourceFromConfiguration($configuration)
	{
		if (isset($configuration->resources->resource))
		{
			foreach ($configuration->resources->resource as $resourceXML)
			{
				$resource = RApiHalHelper::getXMLElementAttributes($resourceXML);
				$resource = RApiHalDocumentResource::defaultResourceField($resource);
				$resourceName = $resource['displayName'];
				$resourceSpecific = $resource['resourceSpecific'];

				if (isset($this->apiResources[$resourceSpecific][$resourceName]))
				{
					$this->resources[$resourceSpecific][$resourceName] = $this->apiResources[$resourceSpecific][$resourceName];
					$this->resources[$resourceSpecific][$resourceName] = RApiHalDocumentResource::mergeResourceFields(
						$this->apiResources[$resourceSpecific][$resourceName], $resource
					);
				}
				else
				{
					$this->resources[$resourceSpecific][$resourceName] = $resource;
				}
			}
		}
	}

	/**
	 * Set document content for Item view
	 *
	 * @param   object            $item           List of items
	 * @param   SimpleXMLElement  $configuration  Configuration for displaying object
	 *
	 * @return void
	 */
	public function setForRenderItem($item, $configuration)
	{
		// Get resource list from configuration
		$this->loadResourceFromConfiguration($configuration);

		// Filter out all fields that are not in resource list and apply appropriate transform rules
		foreach ($item as $key => $value)
		{
			$value = !empty($this->resources['rcwsGlobal'][$key]) ? $this->assignValueToResource($this->resources['rcwsGlobal'][$key], $item) : $value;
			$this->setData($key, $value);
		}
	}

	/**
	 * Method to send the application response to the client.  All headers will be sent prior to the main
	 * application output data.
	 *
	 * @return  void
	 *
	 * @since   1.2
	 */
	public function render()
	{
		$this->setUriParams('api', 'Hal');
		$redCoreApi = 'redCOREAPI';
		$redCoreApi .= !empty($this->webserviceName) ? ' / ' . $this->webserviceName . ' (version ' . $this->webserviceVersion . ')' : '';
		JFactory::getApplication()->setHeader('Via', $redCoreApi, true);

		$documentOptions = array(
			'absoluteHrefs' => $this->options->get('absoluteHrefs', false),
			'documentFormat' => $this->options->get('format', 'json'),
			'uriParams' => $this->uriParams,
		);

		JFactory::$document = new RApiHalDocumentDocument($documentOptions);

		$body = $this->getBody();
		$body = $this->triggerFunction('prepareBody', $body);

		// Push results into the document.
		JFactory::$document
			->setBuffer($body)
			->render(false, array('startTime' => $this->startTime));
	}

	/**
	 * Method to fill response with requested data
	 *
	 * @return  string  Api call output
	 *
	 * @since   1.2
	 */
	public function getBody()
	{
		// Add data
		$data = null;

		if (!empty($data))
		{
			foreach ($data as $k => $v)
			{
				$this->hal->$k = $v;
			}
		}

		return $this->hal;
	}

	/**
	 * Prepares body for response
	 *
	 * @param   string  $message  The return message
	 *
	 * @return  string	The message prepared
	 *
	 * @since   1.2
	 */
	public function prepareBody($message)
	{
		return $message;
	}

	/**
	 * Sets data for resource binding
	 *
	 * @param   string  $key   Rel element
	 * @param   mixed   $data  Data for the resource
	 *
	 * @return RApiHalHal
	 */
	public function setData($key, $data = null)
	{
		if (is_array($key) && null === $data)
		{
			foreach ($key as $k => $v)
			{
				$this->data[$k] = $v;
			}
		}
		else
		{
			$this->data[$key] = $data;
		}

		return $this;
	}

	/**
	 * Set the Uri parameters
	 *
	 * @param   string  $uriKey    Uri Key
	 * @param   string  $uriValue  Uri Value
	 *
	 * @return  RApiHalHal      An instance of itself for chaining
	 */
	public function setUriParams($uriKey, $uriValue)
	{
		$this->uriParams[$uriKey] = $uriValue;

		return $this;
	}

	/**
	 * Process posted data from json or object to array
	 *
	 * @param   mixed             $data           Raw Posted data
	 * @param   SimpleXMLElement  $configuration  Configuration for displaying object
	 *
	 * @return  mixed  Array with posted data.
	 *
	 * @since   1.2
	 */
	public function processPostData($data, $configuration)
	{
		if (is_object($data))
		{
			$data = JArrayHelper::fromObject($data);
		}
		elseif ($data_json = json_decode($data))
		{
			if (json_last_error() == JSON_ERROR_NONE)
			{
				$data = (array) $data_json;
			}
		}
		elseif (!empty($data) && !is_array($data))
		{
			parse_str($data, $data);
		}

		if (!empty($data) && !empty($configuration->fields))
		{
			foreach ($configuration->fields->field as $field)
			{
				$fieldAttributes = RApiHalHelper::getXMLElementAttributes($field);
				$fieldAttributes['transform'] = !empty($fieldAttributes['transform']) ? $fieldAttributes['transform'] : 'string';
				$data[$fieldAttributes['name']] = !empty($data[$fieldAttributes['name']]) ? $data[$fieldAttributes['name']] : '';
				$data[$fieldAttributes['name']] = $this->transformField($fieldAttributes['transform'], $data[$fieldAttributes['name']], false);
			}
		}

		// Common functions are not checking this field so we will
		$data['params'] = !empty($data['params']) ? $data['params'] : null;
		$data['associations'] = !empty($data['associations']) ? $data['associations'] : array();

		return $data;
	}

	/**
	 * Checks if operation is allowed from the configuration file
	 *
	 * @return object This method may be chained.
	 *
	 * @throws  RuntimeException
	 */
	public function isOperationAllowed()
	{
		// Check if webservice is published
		if (!RApiHalHelper::isPublishedWebservice($this->webserviceName, $this->webserviceVersion) && !empty($this->webserviceName))
		{
			throw new RuntimeException(JText::sprintf('LIB_REDCORE_API_HAL_WEBSERVICE_IS_UNPUBLISHED', $this->webserviceName));
		}

		// Check for allowed operations
		$allowedOperations = $this->getConfig('operations');
		$scope = $this->operation;
		$terminateIfNotAuthorized = true;

		if (!isset($allowedOperations->{$this->operation}))
		{
			return false;
		}

		if ($this->operation == 'task')
		{
			$task = $this->options->get('task', '');
			$scope = $task;

			if (!isset($allowedOperations->task->{$task}))
			{
				return false;
			}

			if (isset($allowedOperations->task->{$task}['authorizationNeeded'])
				&& strtolower($allowedOperations->task->{$task}['authorizationNeeded']) == 'false')
			{
				$terminateIfNotAuthorized = false;
			}
		}
		elseif (isset($allowedOperations->{$this->operation}['authorizationNeeded'])
			&& strtolower($allowedOperations->{$this->operation}['authorizationNeeded']) == 'false')
		{
			$terminateIfNotAuthorized = false;
		}

		// @todo finish scope initialization
		$scope = '';

		// Does user have permission
		$this->isAuthorized($scope, $terminateIfNotAuthorized);

		return true;
	}

	/**
	 * Log-in client if successful or terminate api if not authorized
	 *
	 * @param   string  $scope                     Name of the scope to test against
	 * @param   bool    $terminateIfNotAuthorized  Terminate api if client is not authorized
	 *
	 * @throws RuntimeException
	 * @return  void
	 *
	 * @since   1.2
	 */
	public function isAuthorized($scope, $terminateIfNotAuthorized)
	{
		/** @var $response OAuth2\Response */
		$response = RApiOauth2Helper::verifyResourceRequest($scope);

		if ($response instanceof OAuth2\Response)
		{
			if (!$response->isSuccessful() && $terminateIfNotAuthorized)
			{
				// OAuth2 Server response is in fact correct output for errors
				$response->send($this->options->get('format', 'json'));

				JFactory::getApplication()->close();
			}
		}
		elseif ($response === false && $terminateIfNotAuthorized)
		{
			throw new RuntimeException('LIB_REDCORE_API_OAUTH2_SERVER_IS_NOT_ACTIVE');
		}
		elseif (!empty($response['user_id']))
		{
			// Load the JUser class on application for this client
			JFactory::getApplication()->loadIdentity(JFactory::getUser($response['user_id']));
		}
	}

	/**
	 * Gets instance of helper object class if exists
	 *
	 * @return  mixed It will return Api helper class or false if it does not exists
	 *
	 * @since   1.2
	 */
	public function getHelperObject()
	{
		if (!empty($this->apiHelperClass))
		{
			return $this->apiHelperClass;
		}

		$version = $this->options->get('webserviceVersion', '');
		$helperFile = RApiHalHelper::getWebserviceFile(strtolower($this->webserviceName), $version, 'php');

		if (file_exists($helperFile))
		{
			require_once $helperFile;
		}

		$helperClassName = 'RApiHalHelper' . ucfirst(strtolower((string) $this->getConfig('config.name')));

		if (class_exists($helperClassName))
		{
			$this->apiHelperClass = new $helperClassName;
		}

		return $this->apiHelperClass;
	}

	/**
	 * Load model class for data manipulation
	 *
	 * @param   string            $elementName    Element name
	 * @param   SimpleXMLElement  $configuration  Configuration for current action
	 *
	 * @return  mixed  Model class for data manipulation
	 *
	 * @since   1.2
	 */
	public function loadModel($elementName, $configuration)
	{
		if (RApiHalHelper::isAttributeTrue($configuration, 'fromHelper'))
		{
			return $this->getHelperObject();
		}

		if (!empty($configuration['className']))
		{
			$modelClass = (string) $configuration['className'];

			if (!empty($configuration['classPath']))
			{
				require_once $configuration['classPath'];
			}

			if (class_exists($modelClass))
			{
				return new $modelClass;
			}

			$elementName = $modelClass;
		}

		$isAdmin = RApiHalHelper::isAttributeTrue($configuration, 'isAdminClass');

		$optionName = !empty($configuration['optionName']) ? $configuration['optionName'] : $elementName;

		// Add com_ to the element name if not exist
		$optionName = (strpos($optionName, 'com_') === 0 ? '' : 'com_') . $optionName;

		if ($isAdmin)
		{
			RModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/' . $optionName . '/models');
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/' . $optionName . '/tables');

			return RModel::getAdminInstance($elementName, array(), $optionName);
		}
		else
		{
			RModel::addIncludePath(JPATH_SITE . '/components/' . $optionName . '/models');
			JTable::addIncludePath(JPATH_SITE . '/components/' . $optionName . '/tables');

			return RModel::getFrontInstance($elementName, array(), $optionName);
		}
	}

	/**
	 * Checks if operation is allowed from the configuration file
	 *
	 * @param   string  $path  Path to the configuration setting
	 *
	 * @return mixed May return single value or array
	 */
	public function getConfig($path = '')
	{
		$path = explode('.', $path);
		$configuration = $this->configuration;

		foreach ($path as $pathInstance)
		{
			if (isset($configuration->{$pathInstance}))
			{
				$configuration = $configuration->{$pathInstance};
			}
		}

		return is_string($configuration) ? (string) $configuration : $configuration;
	}

	/**
	 * Set resources from configuration if available
	 *
	 * @return  void
	 *
	 * @since   1.2
	 */
	public function setResources()
	{
		$resourcesBase = $this->getConfig('resources');
		$resources = array();

		if (isset($resourcesBase->resource))
		{
			foreach ($resourcesBase->resource as $resourcesXml)
			{
				$resource = RApiHalHelper::getXMLElementAttributes($resourcesXml);
				$resource = RApiHalDocumentResource::defaultResourceField($resource);

				$resources[$resource['resourceSpecific']][$resource['displayName']] = $resource;
			}
		}

		$this->apiResources = $resources;
	}

	/**
	 * Assign value to Resource
	 *
	 * @param   array  $resource  Resource list with options
	 * @param   mixed  $value     Data values to set to resource format
	 *
	 * @return  string
	 *
	 * @since   1.2
	 */
	public function assignValueToResource($resource, $value)
	{
		$format = $resource['fieldFormat'];
		$transform = RApiHalHelper::attributeToString($resource, 'transform', '');

		$stringsToReplace = array();
		preg_match_all('/\{([^}]+)\}/', $format, $stringsToReplace);

		if (!empty($stringsToReplace[1]) && !RApiHalHelper::isAttributeTrue($resource, 'linkTemplated'))
		{
			foreach ($stringsToReplace[1] as $replacementKey)
			{
				if (is_object($value))
				{
					if (isset($value->{$replacementKey}))
					{
						$format = str_replace('{' . $replacementKey . '}', $this->transformField($transform, $value->{$replacementKey}), $format);
					}
				}
				elseif (is_array($value))
				{
					if (isset($value[$replacementKey]))
					{
						$format = str_replace('{' . $replacementKey . '}', $this->transformField($transform, $value[$replacementKey]), $format);
					}
				}
				else
				{
					$format = str_replace('{' . $replacementKey . '}', $this->transformField($transform, $value), $format);
				}
			}
		}

		return $format;
	}

	/**
	 * Get the name of the transform class for a given field type.
	 *
	 * First looks for the transform class in the /transform directory
	 * in the same directory as the web service file.  Then looks
	 * for it in the /api/transform directory.
	 *
	 * @param   string  $fieldType  Field type.
	 *
	 * @return string  Transform class name.
	 */
	private function getTransformClass($fieldType)
	{
		$fieldType = !empty($fieldType) ? $fieldType : 'string';

		// Cache for the class names.
		static $classNames = array();

		// If we already know the class name, just return it.
		if (isset($classNames[$fieldType]))
		{
			return $classNames[$fieldType];
		}

		// Construct the name of the class to do the transform (default is RApiHalTransformString).
		$className = 'RApiHalTransform' . ucfirst($fieldType);

		if (!class_exists($className))
		{
			$className = 'RApiHalTransform' . ucfirst($fieldType);
		}

		// Cache it for later.
		$classNames[$fieldType] = $className;

		return $className;
	}

	/**
	 * Transform a source field data value.
	 *
	 * Calls the static toExternal method of a transform class.
	 *
	 * @param   string   $fieldType          Field type.
	 * @param   string   $definition         Field definition.
	 * @param   boolean  $directionExternal  Transform direction
	 *
	 * @return mixed Transformed data.
	 */
	public function transformField($fieldType, $definition, $directionExternal = true)
	{
		// Get the transform class name.
		$className = $this->getTransformClass($fieldType);

		// Execute the transform.
		if ($className instanceof RApiHalTransformInterface)
		{
			return $directionExternal ? $className::toExternal($definition) : $className::toInternal($definition);
		}
		else
		{
			return $definition;
		}
	}

	/**
	 * Calls method from helper file if exists or method from this class,
	 * Additionally it Triggers plugin call for specific function in a format RApiHalFunctionName
	 *
	 * @param   string  $functionName  Field type.
	 *
	 * @return mixed Result from callback function
	 */
	public function triggerFunction($functionName)
	{
		$apiHelperClass = $this->getHelperObject();
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
		if (method_exists($apiHelperClass, $functionName))
		{
			$result = call_user_func_array(array($apiHelperClass, $functionName), $temp);
		}
		else
		{
			$result = call_user_func_array(array($this, $functionName), $temp);
		}

		JFactory::getApplication()->triggerEvent('RApiHal' . $functionName, $temp);

		return $result;
	}
}

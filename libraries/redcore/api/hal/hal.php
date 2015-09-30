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
 * Class to represent a HAL standard object.
 *
 * @since  1.2
 */
class RApiHalHal extends RApi
{
	/**
	 * Webservice element name
	 * @var string
	 */
	public $elementName = null;

	/**
	 * @var    string  Name of the Client
	 * @since  1.2
	 */
	public $client = '';

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
	 * @var    string  Folder path of the webservice
	 * @since  1.2
	 */
	public $webservicePath = '';

	/**
	 * @var    array  Installed webservice options
	 * @since  1.2
	 */
	public $webservice = '';

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
	 * @var    object  Dynamic model class object
	 * @since  1.3
	 */
	public $apiDynamicModelClass = null;

	/**
	 * @var    string  Dynamic model name used if dataMode="table"
	 * @since  1.3
	 */
	public $apiDynamicModelClassName = 'RApiHalModelItem';

	/**
	 * @var    string  Rendered Documentation
	 * @since  1.2
	 */
	public $documentation = '';

	/**
	 * @var    string  Option name (optional)
	 * @since  1.3
	 */
	public $optionName = '';

	/**
	 * @var    string  View name (optional)
	 * @since  1.3
	 */
	public $viewName = '';

	/**
	 * @var    string  Authorization check method
	 * @since  1.4
	 */
	public $authorizationCheck = 'oauth2';

	/**
	 * @var    object  Array for storing operation errors
	 * @since  1.6
	 */
	public $apiErrors = array();

	/**
	 * Method to instantiate the file-based api call.
	 *
	 * @param   mixed  $options  Optional custom options to load. JRegistry or array format
	 *
	 * @throws Exception
	 * @since   1.2
	 */
	public function __construct($options = null)
	{
		parent::__construct($options);

		JPluginHelper::importPlugin('redcore');

		$this->setWebserviceName();
		$this->client = $this->options->get('webserviceClient', 'site');
		$this->webserviceVersion = $this->options->get('webserviceVersion', '');
		$this->hal = new RApiHalDocumentResource('');

		if (!empty($this->webserviceName))
		{
			if (empty($this->webserviceVersion))
			{
				$this->webserviceVersion = RApiHalHelper::getNewestWebserviceVersion($this->client, $this->webserviceName);
			}

			$this->webservice = RApiHalHelper::getInstalledWebservice($this->client, $this->webserviceName, $this->webserviceVersion);

			if (empty($this->webservice))
			{
				throw new Exception(JText::sprintf('LIB_REDCORE_API_HAL_WEBSERVICE_NOT_INSTALLED', $this->webserviceName, $this->webserviceVersion));
			}

			if (empty($this->webservice['state']))
			{
				throw new Exception(JText::sprintf('LIB_REDCORE_API_HAL_WEBSERVICE_UNPUBLISHED', $this->webserviceName, $this->webserviceVersion));
			}

			$this->webservicePath = $this->webservice['path'];
			$this->configuration = RApiHalHelper::loadWebserviceConfiguration(
				$this->webserviceName, $this->webserviceVersion, 'xml', $this->webservicePath, $this->client
			);

			// Set option and view name
			$this->setOptionViewName($this->webserviceName, $this->configuration);

			// Set base data
			$this->setBaseDataValues();
		}

		// Init Environment
		$this->triggerFunction('setApiOperation');

		// Set initial status code
		$this->setStatusCode($this->statusCode);

		// Check for defined constants
		if (!defined('JSON_UNESCAPED_SLASHES'))
		{
			define('JSON_UNESCAPED_SLASHES', 64);
		}
		// OAuth2 check
		if (RBootstrap::getConfig('webservices_authorization_check', 0) == 0)
		{
			$this->authorizationCheck = 'oauth2';
		}
		elseif (RBootstrap::getConfig('webservices_authorization_check', 0) == 1)
		{
			$this->authorizationCheck = 'joomla';
		}
	}

	/**
	 * Sets default Base Data Values for resource binding
	 *
	 * @return  RApi
	 *
	 * @since   1.4
	 */
	public function setBaseDataValues()
	{
		$webserviceUrlPath = '/index.php?option=' . $this->optionName;

		if (!empty($this->viewName))
		{
			$webserviceUrlPath .= '&amp;view=' . $this->viewName;
		}

		if (!empty($this->webserviceVersion))
		{
			$webserviceUrlPath .= '&amp;webserviceVersion=' . $this->webserviceVersion;
		}

		$webserviceUrlPath .= '&amp;webserviceClient=' . $this->client;

		$this->data['webserviceUrlPath'] = $webserviceUrlPath;
		$this->data['webserviceName'] = $this->webserviceName;
		$this->data['webserviceVersion'] = $this->webserviceVersion;

		return $this;
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
		$format = $this->options->get('format', '');

		// Set proper operation for given method
		switch ((string) $method)
		{
			case 'PUT':
				$method = 'UPDATE';
				break;
			case 'GET':
				$method = !empty($task) ? 'TASK' : 'READ';
				break;
			case 'POST':
				$method = !empty($task) ? 'TASK' : 'CREATE';
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

			if (in_array($operation, array('CREATE', 'READ', 'UPDATE', 'DELETE', 'DOCUMENTATION')))
			{
				$method = $operation;
			}
		}

		if ($format == 'doc')
		{
			$method = 'documentation';
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
	 * @throws  Exception
	 */
	public function execute()
	{
		// Set initial status code to OK
		$this->setStatusCode(200);

		// We do not want some unwanted text to appear before output
		ob_start();

		try
		{
			if (!empty($this->webserviceName))
			{
				if ($this->triggerFunction('isOperationAllowed'))
				{
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
						case 'documentation':
							$this->triggerFunction('apiDocumentation');
							break;
					}
				}
			}
			else
			{
				// If default page needs authorization to access it
				if (!$this->isAuthorized('', RBootstrap::getConfig('webservices_default_page_authorization', 0)))
				{
					return false;
				}

				// No webservice name. We display all webservices available
				$this->triggerFunction('apiDefaultPage');
			}

			// Set links from resources to the main document
			$this->setDataValueToResource($this->hal, $this->resources, $this->data);
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
			// If we are not in debug mode we will take out everything except errors
			if (RBootstrap::getConfig('debug_webservices', 0) == 0)
			{
				$warnings = array();

				foreach ($messages as $key => $message)
				{
					if ($message['type'] == 'warning')
					{
						$warnings[] = $message;
					}

					if ($message['type'] != 'error')
					{
						unset($messages[$key]);
					}
				}

				// Showing 'warning' messages only if no 'error' are present
				if (!count($messages))
				{
					$messages = $warnings;
				}
			}

			$this->hal->setData('_messages', $messages);
		}

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
		$documentationCurieAdmin = new RApiHalDocumentLink('/index.php?option={rel}&amp;format=doc&amp;webserviceClient=administrator',
			'curies', 'Documentation Admin', 'Admin', null, true
		);
		$documentationCurieSite = new RApiHalDocumentLink('/index.php?option={rel}&amp;format=doc&amp;webserviceClient=site',
			'curies', 'Documentation Site', 'Site', null, true
		);

		// Add basic hypermedia links.
		$this->hal->setLink($documentationCurieAdmin, false, true);
		$this->hal->setLink($documentationCurieSite, false, true);
		$this->hal->setLink(new RApiHalDocumentLink(JUri::base(), 'base', JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_DEFAULT_PAGE')));

		$webservices = RApiHalHelper::getInstalledWebservices();

		if (!empty($webservices))
		{
			foreach ($webservices as $webserviceClient => $webserviceNames)
			{
				foreach ($webserviceNames as $webserviceName => $webserviceVersions)
				{
					foreach ($webserviceVersions as $webserviceVersion => $webservice)
					{
						if ($webservice['state'] == 1)
						{
							$documentation = $webserviceClient == 'site' ? 'Site' : 'Admin';

							// Set option and view name
							$this->setOptionViewName($webservice['name'], $this->configuration);
							$webserviceUrlPath = '/index.php?option=' . $this->optionName
								. '&amp;webserviceVersion=' . $webserviceVersion;

							if (!empty($this->viewName))
							{
								$webserviceUrlPath .= '&view=' . $this->viewName;
							}

							// We will fetch only top level webservice
							$this->hal->setLink(
								new RApiHalDocumentLink(
									$webserviceUrlPath . '&webserviceClient=' . $webserviceClient,
									$documentation . ':' . $webservice['name'],
									$webservice['title']
								)
							);

							break;
						}
					}
				}
			}
		}

		return $this;
	}

	/**
	 * Execute the Api Documentation operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	public function apiDocumentation()
	{
		$currentConfiguration = $this->configuration;
		$documentationNone = false;

		if ($this->operationConfiguration['source'] == 'url')
		{
			if (!empty($this->operationConfiguration['url']))
			{
				JFactory::getApplication()->redirect($this->operationConfiguration['url']);
				JFactory::getApplication()->close();
			}

			$documentationNone = true;
		}

		if ($this->operationConfiguration['source'] == 'none' || $documentationNone)
		{
			$currentConfiguration = null;
		}

		$dataGet = $this->options->get('dataGet', array());

		$this->documentation = RLayoutHelper::render(
			'webservice.documentation',
			array(
				'view' => $this,
				'options' => array (
					'xml' => $currentConfiguration,
					'soapEnabled' => RBootstrap::getConfig('enable_soap', 0),
					'print' => isset($dataGet->print)
				)
			)
		);

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
		$primaryKeys = array();
		$isReadItem = $this->apiFillPrimaryKeys($primaryKeys);

		$displayTarget = $isReadItem ? 'item' : 'list';
		$this->apiDynamicModelClassName = 'RApiHalModel' . ucfirst($displayTarget);
		$currentConfiguration = $this->operationConfiguration->{$displayTarget};
		$model = $this->triggerFunction('loadModel', $this->elementName, $currentConfiguration);
		$this->assignFiltersList($model);

		if ($displayTarget == 'list')
		{
			$functionName = RApiHalHelper::attributeToString($currentConfiguration, 'functionName', 'getItems');

			$items = method_exists($model, $functionName) ? $model->{$functionName}() : array();

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

		$primaryKeys = count($primaryKeys) > 1 ? array($primaryKeys) : $primaryKeys;

		// Getting single item
		$functionName = RApiHalHelper::attributeToString($currentConfiguration, 'functionName', 'getItem');
		$messagesBefore = JFactory::getApplication()->getMessageQueue();
		$itemObject = method_exists($model, $functionName) ? call_user_func_array(array(&$model, $functionName), $primaryKeys) : array();
		$messagesAfter = JFactory::getApplication()->getMessageQueue();

		// Check to see if we have the item or not since it might return default properties
		if (count($messagesBefore) != count($messagesAfter))
		{
			foreach ($messagesAfter as $messageKey => $messageValue)
			{
				$messageFound = false;

				foreach ($messagesBefore as $key => $value)
				{
					if ($messageValue['type'] == $value['type'] && $messageValue['message'] == $value['message'])
					{
						$messageFound = true;
						break;
					}
				}

				if (!$messageFound && $messageValue['type'] == 'error')
				{
					$itemObject = null;
					break;
				}
			}
		}

		if (RApiHalHelper::isAttributeTrue($currentConfiguration, 'enforcePKs', true))
		{
			// Checking if primary keys are found
			foreach ($primaryKeys as $primaryKey => $primaryKeyValue)
			{
				if (property_exists($itemObject, $primaryKey) && $itemObject->{$primaryKey} != $primaryKeyValue)
				{
					$itemObject = null;
					break;
				}
			}
		}

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
		$functionName = RApiHalHelper::attributeToString($this->operationConfiguration, 'functionName', 'save');

		$data = $this->triggerFunction('processPostData', $this->options->get('data', array()), $this->operationConfiguration);
		$data = $this->triggerFunction('validatePostData', $model, $data, $this->operationConfiguration);

		if ($data === false)
		{
			// Not Acceptable
			$this->setStatusCode(406);
			$this->triggerFunction('displayErrors', $model);
			$this->setData('result', $data);

			return;
		}

		// Prepare parameters for the function
		$args = $this->buildFunctionArgs($this->operationConfiguration, $data);
		$result = null;

		// Checks if that method exists in model class file and executes it
		if (method_exists($model, $functionName))
		{
			$result = $this->triggerCallFunction($model, $functionName, $args);
		}
		else
		{
			$customError = $this->triggerFunction('createCustomHttpError', 400, $this->apiErrors);
			$this->setStatusCode(400, $customError);
		}

		if (method_exists($model, 'getState'))
		{
			$this->setData('id', $model->getState($model->getName() . '.id'));
		}

		if (method_exists($model, 'getErrors'))
		{
			$modelErrors = $model->getErrors();

			if (!empty($modelErrors))
			{
				$this->apiErrors = array_merge($this->apiErrors, $modelErrors);
			}
		}

		$this->setData('result', $result);
		$this->triggerFunction('displayErrors', $model);

		if ($this->statusCode < 400)
		{
			if ($result === false)
			{
				$customError = $this->triggerFunction('createCustomHttpError', 404, $this->apiErrors);
				$this->setStatusCode(404, $customError);
			}
			else
			{
				$this->setStatusCode(201);
			}
		}
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

		// Delete function requires references and not values like we use in call_user_func_array so we use List delete function
		$this->apiDynamicModelClassName = 'RApiHalModelList';
		$model = $this->triggerFunction('loadModel', $this->elementName, $this->operationConfiguration);
		$functionName = RApiHalHelper::attributeToString($this->operationConfiguration, 'functionName', 'delete');
		$data = $this->triggerFunction('processPostData', $this->options->get('data', array()), $this->operationConfiguration);

		$data = $this->triggerFunction('validatePostData', $model, $data, $this->operationConfiguration);

		if ($data === false)
		{
			// Not Acceptable
			$customError = $this->triggerFunction('createCustomHttpError', 406, $this->apiErrors);
			$this->setStatusCode(406, $customError);
			$this->triggerFunction('displayErrors', $model);
			$this->setData('result', $data);

			return;
		}

		$result = null;
		$args = $this->buildFunctionArgs($this->operationConfiguration, $data);

		// Prepare parameters for the function
		if (strtolower(RApiHalHelper::attributeToString($this->operationConfiguration, 'dataMode', 'model')) == 'table')
		{
			$primaryKeys = array();
			$this->apiFillPrimaryKeys($primaryKeys, $this->operationConfiguration);

			if (!empty($primaryKeys))
			{
				$result = $model->{$functionName}($primaryKeys);
			}
			else
			{
				$result = $model->{$functionName}($args);
			}
		}
		else
		{
			// Checks if that method exists in model class file and executes it
			if (method_exists($model, $functionName))
			{
				$result = $this->triggerCallFunction($model, $functionName, $args);
			}
			else
			{
				$customError = $this->triggerFunction('createCustomHttpError', 400, $this->apiErrors);
				$this->setStatusCode(400, $customError);
			}
		}

		if (method_exists($model, 'getErrors'))
		{
			$modelErrors = $model->getErrors();

			if (!empty($modelErrors))
			{
				$this->apiErrors = array_merge($this->apiErrors, $modelErrors);
			}
		}

		$this->setData('result', $result);

		$this->triggerFunction('displayErrors', $model);

		if ($this->statusCode < 400)
		{
			if ($result === false)
			{
				// If delete failed then we set it to Internal Server Error status code
				$customError = $this->triggerFunction('createCustomHttpError', 500, $this->apiErrors);
				$this->setStatusCode(500, $customError);
			}
		}
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
		$functionName = RApiHalHelper::attributeToString($this->operationConfiguration, 'functionName', 'save');
		$data = $this->triggerFunction('processPostData', $this->options->get('data', array()), $this->operationConfiguration);

		$data = $this->triggerFunction('validatePostData', $model, $data, $this->operationConfiguration);

		if ($data === false)
		{
			// Not Acceptable
			$customError = $this->triggerFunction('createCustomHttpError', 406, $this->apiErrors);
			$this->setStatusCode(406, $customError);
			$this->triggerFunction('displayErrors', $model);
			$this->setData('result', $data);

			return;
		}

		// Prepare parameters for the function
		$args = $this->buildFunctionArgs($this->operationConfiguration, $data);
		$result = null;

		// Checks if that method exists in model class and executes it
		if (method_exists($model, $functionName))
		{
			$result = $this->triggerCallFunction($model, $functionName, $args);
		}
		else
		{
			$customError = $this->triggerFunction('createCustomHttpError', 400, $this->apiErrors);
			$this->setStatusCode(400, $customError);
		}

		if (method_exists($model, 'getState'))
		{
			$this->setData('id', $model->getState(strtolower($this->elementName) . '.id'));
		}

		if (method_exists($model, 'getErrors'))
		{
			$modelErrors = $model->getErrors();

			if (!empty($modelErrors))
			{
				$this->apiErrors = array_merge($this->apiErrors, $modelErrors);
			}
		}

		$this->setData('result', $result);
		$this->triggerFunction('displayErrors', $model);

		if ($this->statusCode < 400)
		{
			if ($result === false)
			{
				// If update failed then we set it to Internal Server Error status code
				$customError = $this->triggerFunction('createCustomHttpError', 500, $this->apiErrors);
				$this->setStatusCode(500, $customError);
			}
		}
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

			$data = $this->triggerFunction('validatePostData', $model, $data, $taskConfiguration);

			if ($data === false)
			{
				// Not Acceptable
				$customError = $this->triggerFunction('createCustomHttpError', 406, $this->apiErrors);
				$this->setStatusCode(406, $customError);
				$this->triggerFunction('displayErrors', $model);
				$this->setData('result', $data);

				return;
			}

			// Prepare parameters for the function
			$args = $this->buildFunctionArgs($taskConfiguration, $data);
			$result = null;

			// Checks if that method exists in model class and executes it
			if (method_exists($model, $functionName))
			{
				$result = $this->triggerCallFunction($model, $functionName, $args);
			}
			else
			{
				$customError = $this->triggerFunction('createCustomHttpError', 400, $this->apiErrors);
				$this->setStatusCode(400, $customError);
				$this->triggerFunction('displayErrors', $model);
			}

			if (method_exists($model, 'getErrors'))
			{
				$modelErrors = $model->getErrors();

				if (!empty($modelErrors))
				{
					$this->apiErrors = array_merge($this->apiErrors, $modelErrors);
				}
			}

			if (method_exists($model, 'getState'))
			{
				$this->setData('id', $model->getState(strtolower($this->elementName) . '.id'));
			}
		}

		$this->setData('result', $result);
		$this->triggerFunction('displayErrors', $model);
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

		if (!empty($items))
		{
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
						$item[$this->assignGlobalValueToResource($key)] = $this->assignValueToResource(
							$this->resources['listItem'][$key], $item
						);
					}
				}

				$embedItem = new RApiHalDocumentResource('item', $item);
				$embedItem = $this->setDataValueToResource($embedItem, $this->resources, $itemValue, 'listItem');
				$this->hal->setEmbedded('item', $embedItem);
			}
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
						$linkRel = !empty($resource['linkRel']) ? $resource['linkRel'] : $this->assignGlobalValueToResource($resource['displayName']);

						// We will force curries as link array
						$linkPlural = $linkRel == 'curies';

						$resourceDocument->setLink(
							new RApiHalDocumentLink(
								$this->assignValueToResource($resource, $data),
								$linkRel,
								$resource['linkTitle'],
								$this->assignGlobalValueToResource($resource['linkName']),
								$resource['hrefLang'],
								RApiHalHelper::isAttributeTrue($resource, 'linkTemplated')
							), $linkSingular = false, $linkPlural
						);
					}
					else
					{
						$resourceDocument->setDataGrouped(
							$resource['displayGroup'], $this->assignGlobalValueToResource($resource['displayName']), $this->assignValueToResource($resource, $data)
						);
					}
				}
				else
				{
					$resourceDocument->setData($this->assignGlobalValueToResource($resource['displayName']), $this->assignValueToResource($resource, $data));
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
	 * @return array
	 */
	public function loadResourceFromConfiguration($configuration)
	{
		if (isset($configuration->resources->resource))
		{
			foreach ($configuration->resources->resource as $resourceXML)
			{
				$resource = RApiHalHelper::getXMLElementAttributes($resourceXML);

				// Filters out specified displayGroup values
				if ($this->options->get('filterOutResourcesGroups') != ''
					&& in_array($resource['displayGroup'], $this->options->get('filterOutResourcesGroups')))
				{
					continue;
				}

				// Filters out if the optional resourceSpecific filter is not the one defined
				if ($this->options->get('filterResourcesSpecific') != ''
					&& $resource['resourceSpecific'] != $this->options->get('filterResourcesSpecific'))
				{
					continue;
				}

				// Filters out if the optional displayName filter is not the one defined
				if ($this->options->get('filterDisplayName') != ''
					&& $resource['displayName'] != $this->options->get('filterDisplayName'))
				{
					continue;
				}

				if (!empty($resourceXML->description))
				{
					$resource['description'] = $resourceXML->description;
				}

				$resource = RApiHalDocumentResource::defaultResourceField($resource);
				$resourceName = $resource['displayName'];
				$resourceSpecific = $resource['resourceSpecific'];

				$this->resources[$resourceSpecific][$resourceName] = $resource;
			}
		}

		return $this->resources;
	}

	/**
	 * Resets specific Resource list or all Resources
	 *
	 * @param   string  $resourceSpecific  Resource specific string that separates resources
	 *
	 * @return RApiHalHal
	 */
	public function resetDocumentResources($resourceSpecific = '')
	{
		if (!empty($resourceSpecific))
		{
			if (isset($this->resources[$resourceSpecific]))
			{
				unset($this->resources[$resourceSpecific]);
			}

			return $this;
		}

		$this->resources = array();

		return $this;
	}

	/**
	 * Used for ordering arrays
	 *
	 * @param   string  $a  Current array
	 * @param   string  $b  Next array
	 *
	 * @return RApiHalHal
	 */
	public function sortResourcesByDisplayGroup($a, $b)
	{
		$sort = strcmp($a["displayGroup"], $b["displayGroup"]);

		if (!$sort)
		{
			return ($a['original_order'] < $b['original_order'] ? -1 : 1);
		}

		return $sort;
	}

	/**
	 * Set document content for Item view
	 *
	 * @param   object|array      $item           Item content
	 * @param   SimpleXMLElement  $configuration  Configuration for displaying object
	 *
	 * @throws Exception
	 * @return void
	 */
	public function setForRenderItem($item, $configuration)
	{
		// Get resource list from configuration
		$this->loadResourceFromConfiguration($configuration);

		if (!empty($item) && (is_array($item) || is_object($item)))
		{
			// Filter out all fields that are not in resource list and apply appropriate transform rules
			foreach ($item as $key => $value)
			{
				$value = !empty($this->resources['rcwsGlobal'][$key]) ? $this->assignValueToResource($this->resources['rcwsGlobal'][$key], $item) : $value;
				$this->setData($this->assignGlobalValueToResource($key), $value);
			}
		}
		else
		{
			// 404 => 'Not found'
			$customError = $this->triggerFunction('createCustomHttpError', 404, $this->apiErrors);
			$this->setStatusCode(404, $customError);

			throw new Exception(JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_ERROR_NO_CONTENT'), 404);
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
		// Set token to uri if used in that way
		$token = $this->options->get('accessToken', '');
		$client = $this->options->get('webserviceClient', '');
		$format = $this->options->get('format', 'json');

		if (!empty($token))
		{
			$this->setUriParams(RBootstrap::getConfig('oauth2_token_param_name', 'access_token'), $token);
		}

		if ($client == 'administrator')
		{
			$this->setUriParams('webserviceClient', $client);
		}

		$this->setUriParams('api', 'Hal');

		if ($format == 'doc')
		{
			// This is already in HTML format
			echo $this->documentation;
		}
		else
		{
			$documentOptions = array(
				'absoluteHrefs' => $this->options->get('absoluteHrefs', false),
				'documentFormat' => $format,
				'uriParams' => $this->uriParams,
			);
			JFactory::$document = new RApiHalDocumentDocument($documentOptions);

			$body = $this->getBody();
			$body = $this->triggerFunction('prepareBody', $body);

			// Push results into the document.
			JFactory::$document
				->setHal($this)
				->setBuffer($body)
				->render(false);
		}
	}

	/**
	 * Method to fill response with requested data
	 *
	 * @param   array  $data  Data to set to Hal document if needed
	 *
	 * @return  string  Api call output
	 *
	 * @since   1.2
	 */
	public function getBody($data = array())
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
	 * @param   array             $data           Raw Posted data
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

		if (!is_array($data))
		{
			$data = (array) $data;
		}

		if (!empty($data) && !empty($configuration->fields))
		{
			$dataFields = array();

			foreach ($configuration->fields->field as $field)
			{
				$fieldAttributes = RApiHalHelper::getXMLElementAttributes($field);
				$fieldAttributes['transform'] = !is_null($fieldAttributes['transform']) ? $fieldAttributes['transform'] : 'string';
				$fieldAttributes['defaultValue'] = !is_null($fieldAttributes['defaultValue'])
					&& !RApiHalHelper::isAttributeTrue($fieldAttributes, 'isPrimaryField') ? $fieldAttributes['defaultValue'] : '';

				if (!isset($data[$fieldAttributes['name']]) || is_null($data[$fieldAttributes['name']]))
				{
					$data[$fieldAttributes['name']] = $fieldAttributes['defaultValue'];
				}

				$data[$fieldAttributes['name']] = $this->transformField($fieldAttributes['transform'], $data[$fieldAttributes['name']], false);
				$dataFields[$fieldAttributes['name']] = $data[$fieldAttributes['name']];
			}

			if (RApiHalHelper::isAttributeTrue($configuration, 'strictFields'))
			{
				$data = $dataFields;
			}
		}

		// Common functions are not checking this field so we will
		$data['params'] = isset($data['params']) ? $data['params'] : null;
		$data['associations'] = isset($data['associations']) ? $data['associations'] : array();

		return $data;
	}

	/**
	 * Validates posted data
	 *
	 * @param   object            $model          Model
	 * @param   array             $data           Raw Posted data
	 * @param   SimpleXMLElement  $configuration  Configuration for displaying object
	 *
	 * @return  mixed  Array with posted data or false.
	 *
	 * @since   1.3
	 */
	public function validatePostData($model, $data, $configuration)
	{
		$data = (array) $data;
		$app = JFactory::getApplication();

		// We are checking required fields set in webservice XMLs
		if (!$this->checkRequiredFields($data, $configuration))
		{
			return false;
		}

		$validateMethod = strtolower(RApiHalHelper::attributeToString($configuration, 'validateData', 'none'));

		if ($validateMethod == 'none')
		{
			return $data;
		}

		if ($validateMethod == 'form')
		{
			if (method_exists($model, 'getForm'))
			{
				// Validate the posted data.
				// Sometimes the form needs some posted data, such as for plugins and modules.
				$form = $model->getForm($data, false);

				if (!$form)
				{
					return $data;
				}

				// Test whether the data is valid.
				$validData = $model->validate($form, $data);

				// Common functions are not checking this field so we will
				$validData['params'] = isset($validData['params']) ? $validData['params'] : null;
				$validData['associations'] = isset($validData['associations']) ? $validData['associations'] : array();

				return $validData;
			}

			$app->enqueueMessage(JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_FUNCTION_DONT_EXIST'), 'error');

			return false;
		}

		if ($validateMethod == 'function')
		{
			$validateMethod = strtolower(RApiHalHelper::attributeToString($configuration, 'validateDataFunction', 'validate'));

			if (method_exists($model, $validateMethod))
			{
				$result = $model->{$validateMethod}($data);

				return $result;
			}

			$app->enqueueMessage(JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_FUNCTION_DONT_EXIST'), 'error');

			return false;
		}

		return false;
	}

	/**
	 * Validates posted data
	 *
	 * @param   array             $data           Raw Posted data
	 * @param   SimpleXMLElement  $configuration  Configuration for displaying object
	 *
	 * @return  mixed  Array with posted data or false.
	 *
	 * @since   1.3
	 */
	public function checkRequiredFields($data, $configuration)
	{
		$errors = array();

		if (!empty($configuration->fields))
		{
			foreach ($configuration->fields->field as $field)
			{
				if (RApiHalHelper::isAttributeTrue($field, 'isRequiredField'))
				{
					if (is_null($data[(string) $field['name']]) || $data[(string) $field['name']] === '')
					{
						JFactory::getApplication()->enqueueMessage(
						JText::sprintf('LIB_REDCORE_API_HAL_WEBSERVICE_ERROR_REQUIRED_FIELD', (string) $field['name']), 'error'
					);

						$errors[] = JText::sprintf('LIB_REDCORE_API_HAL_WEBSERVICE_ERROR_REQUIRED_FIELD', (string) $field['name']);
					}
				}
			}
		}

		if (!empty($errors))
		{
			$this->apiErrors = array_merge($this->apiErrors, $errors);

			return false;
		}

		return true;
	}

	/**
	 * Checks if operation is allowed from the configuration file
	 *
	 * @return object This method may be chained.
	 *
	 * @throws  Exception
	 */
	public function isOperationAllowed()
	{
		// Check if webservice is published
		if (!RApiHalHelper::isPublishedWebservice($this->client, $this->webserviceName, $this->webserviceVersion) && !empty($this->webserviceName))
		{
			throw new Exception(JText::sprintf('LIB_REDCORE_API_HAL_WEBSERVICE_IS_UNPUBLISHED', $this->webserviceName));
		}

		// Check for allowed operations
		$allowedOperations = $this->getConfig('operations');

		if (!isset($allowedOperations->{$this->operation}))
		{
			$customError = $this->triggerFunction('createCustomHttpError', 405, $this->apiErrors);
			$this->setStatusCode(405, $customError);

			return false;
		}

		$scope = $this->operation;
		$authorizationGroups = !empty($allowedOperations->{$this->operation}['authorization']) ?
			(string) $allowedOperations->{$this->operation}['authorization'] : '';
		$terminateIfNotAuthorized = true;

		if ($this->operation == 'task')
		{
			$task = $this->options->get('task', '');
			$scope .= '.' . $task;

			if (!isset($allowedOperations->task->{$task}))
			{
				$customError = $this->triggerFunction('createCustomHttpError', 405, $this->apiErrors);
				$this->setStatusCode(405, $customError);

				return false;
			}

			$authorizationGroups = !empty($allowedOperations->task->{$task}['authorization']) ?
				(string) $allowedOperations->task->{$task}['authorization'] : '';

			if (isset($allowedOperations->task->{$task}['authorizationNeeded'])
				&& strtolower($allowedOperations->task->{$task}['authorizationNeeded']) == 'false')
			{
				$terminateIfNotAuthorized = false;
			}
		}
		elseif ($this->operation == 'read')
		{
			// Disable authorization on operation read level
			if (isset($allowedOperations->{$this->operation}['authorizationNeeded'])
				&& strtolower($allowedOperations->{$this->operation}['authorizationNeeded']) == 'false')
			{
				$terminateIfNotAuthorized = false;
			}
			else
			{
				$primaryKeys = array();
				$isReadItem = $this->apiFillPrimaryKeys($primaryKeys);
				$readType = $isReadItem ? 'item' : 'list';

				if (isset($allowedOperations->read->{$readType}['authorizationNeeded'])
					&& strtolower($allowedOperations->read->{$readType}['authorizationNeeded']) == 'false')
				{
					$terminateIfNotAuthorized = false;
				}
			}
		}
		elseif (isset($allowedOperations->{$this->operation}['authorizationNeeded'])
			&& strtolower($allowedOperations->{$this->operation}['authorizationNeeded']) == 'false')
		{
			$terminateIfNotAuthorized = false;
		}

		// Does user have permission
		// OAuth2 check
		if ($this->authorizationCheck == 'oauth2')
		{
			// Use scopes to authorize
			$scope = array($this->client . '.' . $this->webserviceName . '.' . $scope);

			// Add in Global scope check
			$scope[] = $this->client . '.' . $this->operation;

			return $this->isAuthorized($scope, $terminateIfNotAuthorized) || !$terminateIfNotAuthorized;
		}
		// Joomla check
		elseif ($this->authorizationCheck == 'joomla')
		{
			$isAuthorized = $this->isAuthorized($scope = null, $terminateIfNotAuthorized);

			// Use Joomla to authorize
			if ($isAuthorized && $terminateIfNotAuthorized && !empty($authorizationGroups))
			{
				$authorizationGroups = explode(',', $authorizationGroups);
				$authorized = false;
				$configAssetName = !empty($this->configuration->config->authorizationAssetName) ?
					(string) $this->configuration->config->authorizationAssetName : null;

				foreach ($authorizationGroups as $authorizationGroup)
				{
					$authorization = explode(':', trim($authorizationGroup));
					$action = $authorization[0];
					$assetName = !empty($authorization[1]) ? $authorization[1] : $configAssetName;

					if (JFactory::getUser()->authorise(trim($action), trim($assetName)))
					{
						$authorized = true;
						break;
					}
				}

				if (!$authorized)
				{
					$customError = $this->triggerFunction('createCustomHttpError', 405, $this->apiErrors);
					$this->setStatusCode(405, $customError);

					return false;
				}
			}

			return $isAuthorized || !$terminateIfNotAuthorized;
		}

		return false;
	}

	/**
	 * Log-in client if successful or terminate api if not authorized
	 *
	 * @param   string  $scope                     Name of the scope to test against
	 * @param   bool    $terminateIfNotAuthorized  Terminate api if client is not authorized
	 *
	 * @throws Exception
	 * @return  bool
	 *
	 * @since   1.2
	 */
	public function isAuthorized($scope, $terminateIfNotAuthorized)
	{
		$authorized = false;
		JFactory::getApplication()->triggerEvent('RApiHalBeforeIsAuthorizedCheck',
			array($scope, $terminateIfNotAuthorized, $this->options, $this->authorizationCheck, &$authorized)
		);

		if ($authorized)
		{
			return $authorized;
		}

		// OAuth2 check
		if ($this->authorizationCheck == 'oauth2')
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
				throw new Exception(JText::_('LIB_REDCORE_API_OAUTH2_SERVER_IS_NOT_ACTIVE'));
			}
			else
			{
				$response = json_decode($response);

				if (!empty($response->user_id))
				{
					$user = JFactory::getUser($response->user_id);

					// Load the JUser class on application for this client
					JFactory::getApplication()->loadIdentity($user);
					JFactory::getSession()->set('user', $user);

					return true;
				}

				$authorized = false || !$terminateIfNotAuthorized;
			}
		}
		// Joomla check through Basic Authentication
		elseif ($this->authorizationCheck == 'joomla')
		{
			// Get username and password from globals
			$credentials = RApiHalHelper::getCredentialsFromGlobals();

			$authorized = RUser::userLogin($credentials) || !$terminateIfNotAuthorized;
		}

		if (!$authorized && $terminateIfNotAuthorized)
		{
			$customError = $this->triggerFunction('createCustomHttpError', 401, $this->apiErrors);
			$this->setStatusCode(401, $customError);
		}

		return $authorized || !$terminateIfNotAuthorized;
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
		$helperFile = RApiHalHelper::getWebserviceFile($this->client, strtolower($this->webserviceName), $version, 'php', $this->webservicePath);

		if (file_exists($helperFile))
		{
			require_once $helperFile;
		}

		$webserviceName = preg_replace('/[^A-Z0-9_\.]/i', '', $this->webserviceName);
		$helperClassName = 'RApiHalHelper' . ucfirst($this->client) . ucfirst(strtolower($webserviceName));

		if (class_exists($helperClassName))
		{
			$this->apiHelperClass = new $helperClassName;
		}

		return $this->apiHelperClass;
	}

	/**
	 * Gets instance of dynamic model object class (for table bind)
	 *
	 * @param   SimpleXMLElement  $configuration  Configuration for current action
	 *
	 * @return mixed It will return Api dynamic model class
	 *
	 * @throws Exception
	 * @since   1.3
	 */
	public function getDynamicModelObject($configuration)
	{
		if (!empty($this->apiDynamicModelClass))
		{
			return $this->apiDynamicModelClass;
		}

		$tableName = RApiHalHelper::attributeToString($configuration, 'tableName', '');

		if (empty($tableName))
		{
			throw new Exception(JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_TABLE_NAME_NOT_SET'));
		}

		$context = $this->webserviceName . '.' . $this->webserviceVersion;

		// We are not using prefix like str_replace(array('.', '-'), array('_', '_'), $context) . '_';
		$paginationPrefix = '';
		$filterFields = RApiHalHelper::getFilterFields($configuration);
		$primaryFields = $this->getPrimaryFields($configuration);
		$fields = $this->getAllFields($configuration);

		$config = array(
			'tableName' => $tableName,
			'context'   => $context,
			'paginationPrefix' => $paginationPrefix,
			'filterFields' => $filterFields,
			'primaryFields' => $primaryFields,
			'fields' => $fields,
		);

		$apiDynamicModelClassName = $this->apiDynamicModelClassName;

		if (class_exists($apiDynamicModelClassName))
		{
			$this->apiDynamicModelClass = new $apiDynamicModelClassName($config);
		}

		return $this->apiDynamicModelClass;
	}

	/**
	 * Gets list of primary fields from operation configuration
	 *
	 * @param   SimpleXMLElement  $configuration  Configuration for current action
	 *
	 * @return  array
	 *
	 * @since   1.3
	 */
	public function getPrimaryFields($configuration)
	{
		$primaryFields = array();

		if (!empty($configuration->fields))
		{
			foreach ($configuration->fields->field as $field)
			{
				$isPrimaryField = RApiHalHelper::isAttributeTrue($field, 'isPrimaryField');

				if ($isPrimaryField)
				{
					$primaryFields[] = (string) $field["name"];
				}
			}
		}

		return $primaryFields;
	}

	/**
	 * Gets list of all fields from operation configuration
	 *
	 * @param   SimpleXMLElement  $configuration  Configuration for current action
	 *
	 * @return  array
	 *
	 * @since   1.3
	 */
	public function getAllFields($configuration)
	{
		$fields = array();

		if (!empty($configuration->fields))
		{
			foreach ($configuration->fields->field as $field)
			{
				$fieldAttributes = RApiHalHelper::getXMLElementAttributes($field);
				$fields[] = $fieldAttributes;
			}
		}

		return $fields;
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
		$this->setOptionViewName($elementName, $configuration);
		$isAdmin = RApiHalHelper::isAttributeTrue($configuration, 'isAdminClass');
		$this->addModelIncludePaths($isAdmin, $this->optionName);
		$this->loadExtensionLanguage($this->optionName, $isAdmin ? JPATH_ADMINISTRATOR : JPATH_SITE);
		$this->triggerFunction('loadExtensionLibrary', $this->optionName);
		$dataMode = strtolower(RApiHalHelper::attributeToString($configuration, 'dataMode', 'model'));

		if ($dataMode == 'helper')
		{
			return $this->getHelperObject();
		}

		if ($dataMode == 'table')
		{
			return $this->getDynamicModelObject($configuration);
		}

		if (!empty($configuration['modelClassName']))
		{
			$modelClass = (string) $configuration['modelClassName'];

			if (!empty($configuration['modelClassPath']))
			{
				require_once JPATH_SITE . '/' . $configuration['modelClassPath'];

				if (class_exists($modelClass))
				{
					return new $modelClass;
				}
			}
			else
			{
				$componentName = ucfirst(strtolower(substr($this->optionName, 4)));
				$prefix = $componentName . 'Model';

				$model = RModel::getInstance($modelClass, $prefix);

				if ($model)
				{
					return $model;
				}
			}
		}

		if (!empty($this->viewName))
		{
			$elementName = $this->viewName;
		}

		if ($isAdmin)
		{
			return RModel::getAdminInstance($elementName, array(), $this->optionName);
		}

		return RModel::getFrontInstance($elementName, array(), $this->optionName);
	}

	/**
	 * Add include paths for model class
	 *
	 * @param   boolean  $isAdmin     Is client admin or site
	 * @param   string   $optionName  Option name
	 *
	 * @return  void
	 *
	 * @since   1.3
	 */
	public function addModelIncludePaths($isAdmin, $optionName)
	{
		if ($isAdmin)
		{
			$this->loadExtensionLanguage($optionName, JPATH_ADMINISTRATOR);
			$path = JPATH_ADMINISTRATOR . '/components/' . $optionName;
			RModel::addIncludePath($path . '/models');
			JTable::addIncludePath($path . '/tables');
			RForm::addFormPath($path . '/models/forms');
			RForm::addFieldPath($path . '/models/fields');
		}
		else
		{
			$this->loadExtensionLanguage($optionName);
			$path = JPATH_SITE . '/components/' . $optionName;
			RModel::addIncludePath($path . '/models');
			JTable::addIncludePath($path . '/tables');
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/' . $optionName . '/tables');
			RForm::addFormPath($path . '/models/forms');
			RForm::addFieldPath($path . '/models/fields');
		}

		if (!defined('JPATH_COMPONENT'))
		{
			define('JPATH_COMPONENT', $path);
		}
	}

	/**
	 * Include library classes
	 *
	 * @param   string  $element  Option name
	 *
	 * @return  void
	 *
	 * @since   1.4
	 */
	public function loadExtensionLibrary($element)
	{
		$element = strpos($element, 'com_') === 0 ? substr($element, 4) : $element;
		JLoader::import(strtolower($element) . '.library');
	}

	/**
	 * Sets option and view name
	 *
	 * @param   string            $elementName    Element name
	 * @param   SimpleXMLElement  $configuration  Configuration for current action
	 *
	 * @return  void
	 *
	 * @since   1.3
	 */
	public function setOptionViewName($elementName, $configuration)
	{
		// Views are separated by dash
		$view = explode('-', $elementName);
		$elementName = $view[0];
		$viewName = '';

		if (!empty($view[1]))
		{
			$viewName = $view[1];
		}

		$optionName = !empty($configuration['optionName']) ? $configuration['optionName'] : $elementName;

		// Add com_ to the element name if not exist
		$optionName = (strpos($optionName, 'com_') === 0 ? '' : 'com_') . $optionName;

		$this->optionName = $optionName;
		$this->viewName = $viewName;

		// We add separate view and option name if they were merged
		if (!empty($viewName))
		{
			$input = JFactory::getApplication()->input;
			$input->set('option', $optionName);
			$input->set('view', $viewName);
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
	 * Gets errors from model and places it into Application message queue
	 *
	 * @param   object  $model  Model
	 *
	 * @return void
	 */
	public function displayErrors($model)
	{
		if (method_exists($model, 'getErrors'))
		{
			$app = JFactory::getApplication();

			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up all validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'error');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'error');
				}
			}
		}
	}

	/**
	 * Assign value to Resource
	 *
	 * @param   array   $resource   Resource list with options
	 * @param   mixed   $value      Data values to set to resource format
	 * @param   string  $attribute  Attribute from array to replace the data
	 *
	 * @return  string
	 *
	 * @since   1.2
	 */
	public function assignValueToResource($resource, $value, $attribute = 'fieldFormat')
	{
		$format = $resource[$attribute];
		$transform = RApiHalHelper::attributeToString($resource, 'transform', '');

		// Filters out the complex SOAP arrays, to treat them as regular arrays
		if (preg_match('/^array\[(.+)\]$/im', $transform))
		{
			$transform = 'array';
		}

		$stringsToReplace = array();
		preg_match_all('/\{([^}]+)\}/', $format, $stringsToReplace);

		if (!empty($stringsToReplace[1]))
		{
			foreach ($stringsToReplace[1] as $replacementKey)
			{
				if (is_object($value))
				{
					if (property_exists($value, $replacementKey))
					{
						// We are transforming only value
						if ($format == '{' . $replacementKey . '}')
						{
							$format = $this->transformField($transform, $value->{$replacementKey});
						}
						// We are transforming only part of the string
						else
						{
							$format = str_replace('{' . $replacementKey . '}', $this->transformField($transform, $value->{$replacementKey}), $format);
						}
					}
				}
				elseif (is_array($value))
				{
					if (isset($value[$replacementKey]))
					{
						// We are transforming only value
						if ($format == '{' . $replacementKey . '}')
						{
							$format = $this->transformField($transform, $value[$replacementKey]);
						}
						// We are transforming only part of the string
						else
						{
							$format = str_replace('{' . $replacementKey . '}', $this->transformField($transform, $value[$replacementKey]), $format);
						}
					}
				}
				else
				{
					// We are transforming only value
					if ($format == '{' . $replacementKey . '}')
					{
						$format = $this->transformField($transform, $value);
					}
					// We are transforming only part of the string
					else
					{
						$format = str_replace('{' . $replacementKey . '}', $this->transformField($transform, $value), $format);
					}
				}
			}
		}

		// We replace global data as well
		$format = $this->assignGlobalValueToResource($format);

		if (!empty($stringsToReplace[1]))
		{
			// If we did not found data with that resource we will set it to 0, except for linkRel which is a documentation template
			if ($format === $resource[$attribute] && $resource['linkRel'] != 'curies')
			{
				$format = null;
			}
		}

		return $format;
	}

	/**
	 * Assign value to Resource
	 *
	 * @param   string  $format  String to parse
	 *
	 * @return  string
	 *
	 * @since   1.2
	 */
	public function assignGlobalValueToResource($format)
	{
		if (empty($format) || !is_string($format))
		{
			return $format;
		}

		$stringsToReplace = array();
		preg_match_all('/\{([^}]+)\}/', $format, $stringsToReplace);

		if (!empty($stringsToReplace[1]))
		{
			foreach ($stringsToReplace[1] as $replacementKey)
			{
				// Replace from global variables if present
				if (isset($this->data[$replacementKey]))
				{
					// We are transforming only value
					if ($format == '{' . $replacementKey . '}')
					{
						$format = $this->data[$replacementKey];
					}
					// We are transforming only part of the string
					else
					{
						$format = str_replace('{' . $replacementKey . '}', $this->data[$replacementKey], $format);
					}
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

		if (class_exists($className))
		{
			$classInstance = new $className;

			// Cache it for later.
			$classNames[$fieldType] = $classInstance;

			return $classNames[$fieldType];
		}

		return $this->getTransformClass('string');
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

		$result = JFactory::getApplication()->triggerEvent('RApiHalBefore' . $functionName, array($functionName, $temp));

		if ($result)
		{
			return $result;
		}

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

	/**
	 * Calls method from defined object as some Joomla methods require referenced parameters
	 *
	 * @param   object  $object        Object to run function on
	 * @param   string  $functionName  Function name
	 * @param   array   $args          Arguments for the function
	 *
	 * @return mixed Result from callback function
	 */
	public function triggerCallFunction($object, $functionName, $args)
	{
		switch (count($args))
		{
			case 0:
				return $object->{$functionName}();
			case 1:
				return $object->{$functionName}($args[0]);
			case 2:
				return $object->{$functionName}($args[0], $args[1]);
			case 3:
				return $object->{$functionName}($args[0], $args[1], $args[2]);
			case 4:
				return $object->{$functionName}($args[0], $args[1], $args[2], $args[3]);
			case 5:
				return $object->{$functionName}($args[0], $args[1], $args[2], $args[3], $args[4]);
			default:
				return call_user_func_array(array($object, $functionName), $args);
		}
	}

	/**
	 * Get all defined fields and transform them if needed to expected format. Then it puts it into array for function call
	 *
	 * @param   SimpleXMLElement  $configuration  Configuration for current action
	 * @param   array             $data           List of posted data
	 *
	 * @return array List of parameters to pass to the function
	 */
	public function buildFunctionArgs($configuration, $data)
	{
		$args = array();
		$result = null;

		if (!empty($configuration['functionArgs']))
		{
			$functionArgs = explode(',', (string) $configuration['functionArgs']);

			foreach ($functionArgs as $functionArg)
			{
				$parameter = explode('{', $functionArg);

				// First field is the name of the data field and second is transformation
				$parameter[0] = trim($parameter[0]);
				$parameter[1] = !empty($parameter[1]) ? strtolower(trim(str_replace('}', '', $parameter[1]))) : 'string';
				$parameterValue = null;

				// If we set argument to value, then it will not be transformed, instead we will take field name as a value
				if ($parameter[1] == 'value')
				{
					$parameterValue = $parameter[0];
				}
				else
				{
					if (isset($data[$parameter[0]]))
					{
						$parameterValue = $this->transformField($parameter[1], $data[$parameter[0]]);
					}
					else
					{
						$parameterValue = null;
					}
				}

				$args[] = $parameterValue;
			}
		}
		else
		{
			$args[] = $data;
		}

		return $args;
	}

	/**
	 * We set filters and List parameters to the model object
	 *
	 * @param   object  &$model  Model object
	 *
	 * @return  array
	 */
	public function assignFiltersList(&$model)
	{
		if (method_exists($model, 'getState'))
		{
			// To initialize populateState
			$model->getState();
		}

		$dataGet = $this->options->get('dataGet', array());

		if (is_object($dataGet))
		{
			$dataGet = JArrayHelper::fromObject($dataGet);
		}

		$limitField = 'limit';
		$limitStartField = 'limitstart';

		if (method_exists($model, 'get'))
		{
			// RedCORE limit fields
			$limitField = $model->get('limitField', $limitField);
			$limitStartField = $model->get('limitstartField', $limitStartField);
		}

		if (isset($dataGet['list']['limit']))
		{
			$dataGet[$limitField] = $dataGet['list']['limit'];
		}

		if (isset($dataGet['list']['limitstart']))
		{
			$dataGet[$limitStartField] = $dataGet['list']['limitstart'];
		}

		// Support for B/C custom limit fields
		if ($limitField != 'limit' && !empty($dataGet['limit']) && !isset($dataGet[$limitField]))
		{
			$dataGet[$limitField] = $dataGet['limit'];
		}

		if ($limitStartField != 'limitstart' && !empty($dataGet['limitstart']) && !isset($dataGet[$limitStartField]))
		{
			$dataGet[$limitStartField] = $dataGet['limitstart'];
		}

		// Set state for Filters and List
		if (method_exists($model, 'setState'))
		{
			if (isset($dataGet['list']))
			{
				foreach ($dataGet['list'] as $key => $value)
				{
					$model->setState('list.' . $key, $value);
				}
			}

			if (isset($dataGet['filter']))
			{
				foreach ($dataGet['filter'] as $key => $value)
				{
					$model->setState('filter.' . $key, $value);
				}
			}

			if (isset($dataGet[$limitField]))
			{
				$model->setState('limit', $dataGet[$limitField]);
				$model->setState('list.limit', $dataGet[$limitField]);
				$model->setState($limitField, $dataGet[$limitField]);
			}

			if (isset($dataGet[$limitStartField]))
			{
				$model->setState('limitstart', $dataGet[$limitStartField]);
				$model->setState('list.start', $dataGet[$limitStartField]);
				$model->setState($limitStartField, $dataGet[$limitStartField]);
			}
		}

		$this->options->set('dataGet', $dataGet);
	}

	/**
	 * Returns if all primary keys have set values
	 * Easily get read type (item or list) for current read operation and fills primary keys
	 *
	 * @param   array             &$primaryKeys   List of primary keys
	 * @param   SimpleXMLElement  $configuration  Configuration group
	 *
	 * @return  bool  Returns true if read type is Item
	 *
	 * @since   1.2
	 */
	public function apiFillPrimaryKeys(&$primaryKeys, $configuration = null)
	{
		if (is_null($configuration))
		{
			$operations = $this->getConfig('operations');

			if (!empty($operations->read->item))
			{
				$configuration = $operations->read->item;
			}

			$data = $this->triggerFunction('processPostData', $this->options->get('dataGet', array()), $configuration);
		}
		else
		{
			$data = $this->triggerFunction('processPostData', $this->options->get('data', array()), $configuration);
		}

		// Checking for primary keys
		if (!empty($configuration))
		{
			$primaryKeysFromFields = RApiHalHelper::getFieldsArray($configuration, true);

			if (!empty($primaryKeysFromFields))
			{
				foreach ($primaryKeysFromFields as $primaryKey => $primaryKeyField)
				{
					if (isset($data[$primaryKey]) && $data[$primaryKey] != '')
					{
						$primaryKeys[$primaryKey] = $this->transformField($primaryKeyField['transform'], $data[$primaryKey], false);
					}
					else
					{
						$primaryKeys[$primaryKey] = null;
					}
				}

				foreach ($primaryKeys as $primaryKey => $primaryKeyField)
				{
					if (is_null($primaryKeyField))
					{
						return false;
					}
				}
			}

			return true;
		}

		return false;
	}
}

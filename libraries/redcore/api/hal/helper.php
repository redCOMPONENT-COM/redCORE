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
 * Interface to handle api calls
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.2
 */
class RApiHalHelper
{
	/**
	 * An array to hold webservices xmls
	 *
	 * @var    array
	 * @since  1.2
	 */
	public static $webservices = array();

	/**
	 * An array to hold installed Webservices data
	 *
	 * @var    array
	 * @since  1.2
	 */
	public static $installedWebservices = null;

	/**
	 * Method to transform XML to array and get XML attributes
	 *
	 * @param   SimpleXMLElement  $xmlElement      XML object to transform
	 * @param   boolean           $onlyAttributes  return only attributes or all elements
	 *
	 * @return  array
	 *
	 * @since   1.2
	 */
	public static function getXMLElementAttributes($xmlElement, $onlyAttributes = true)
	{
		$transformedXML = json_decode(json_encode((array) $xmlElement), true);

		return $onlyAttributes ? $transformedXML['@attributes'] : $transformedXML;
	}

	/**
	 * Get Webservices path
	 *
	 * @return  string
	 *
	 * @since   1.2
	 */
	public static function getWebservicesPath()
	{
		return JPATH_ROOT . '/media/redcore/webservices';
	}

	/**
	 * Get Default scopes for all webservices
	 *
	 * @return  array
	 *
	 * @since   1.2
	 */
	public static function getDefaultScopes()
	{
		return array(
			array('scope' => 'site.create',
				'scopeDisplayName' => JText::_('JSITE') . ' - ' . JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_CREATE')),
			array('scope' => 'site.read',
				'scopeDisplayName' => JText::_('JSITE') . ' - ' . JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_READ')),
			array('scope' => 'site.update',
				'scopeDisplayName' => JText::_('JSITE') . ' - ' . JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_UPDATE')),
			array('scope' => 'site.delete',
				'scopeDisplayName' => JText::_('JSITE') . ' - ' . JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_DELETE')),
			array('scope' => 'site.task',
				'scopeDisplayName' => JText::_('JSITE') . ' - ' . JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_TASKS')),
			array('scope' => 'site.documentation',
				'scopeDisplayName' => JText::_('JSITE') . ' - ' . JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_DOCUMENTATION')),
			array('scope' => 'administrator.create',
				'scopeDisplayName' => JText::_('JADMINISTRATOR') . ' - ' . JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_CREATE')),
			array('scope' => 'administrator.read',
				'scopeDisplayName' => JText::_('JADMINISTRATOR') . ' - ' . JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_READ')),
			array('scope' => 'administrator.update',
				'scopeDisplayName' => JText::_('JADMINISTRATOR') . ' - ' . JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_UPDATE')),
			array('scope' => 'administrator.delete',
				'scopeDisplayName' => JText::_('JADMINISTRATOR') . ' - ' . JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_DELETE')),
			array('scope' => 'administrator.task',
				'scopeDisplayName' => JText::_('JADMINISTRATOR') . ' - ' . JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_TASKS')),
			array('scope' => 'administrator.documentation',
				'scopeDisplayName' => JText::_('JADMINISTRATOR') . ' - ' . JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_DOCUMENTATION')),
		);
	}

	/**
	 * Method to transform XML to array and get XML attributes
	 *
	 * @param   SimpleXMLElement|Array  $element  XML object or array
	 * @param   string                  $key      Key to check
	 * @param   boolean                 $default  Default value to return
	 *
	 * @return  boolean
	 *
	 * @since   1.2
	 */
	public static function isAttributeTrue($element, $key, $default = false)
	{
		if (!isset($element[$key]))
		{
			return $default;
		}

		return strtolower($element[$key]) == "true" ? true : false;
	}

	/**
	 * Method to transform XML to array and get XML attributes
	 *
	 * @param   SimpleXMLElement|Array  $element  XML object or array
	 * @param   string                  $key      Key to check
	 * @param   string                  $default  Default value to return
	 *
	 * @return  boolean
	 *
	 * @since   1.2
	 */
	public static function attributeToString($element, $key, $default = '')
	{
		if (!isset($element[$key]))
		{
			return $default;
		}

		$value = (string) $element[$key];

		return !empty($value) ? $value : $default;
	}

	/**
	 * Method to get Task from request
	 *
	 * @return  string Task name
	 *
	 * @since   1.2
	 */
	public static function getTask()
	{
		$command  = JFactory::getApplication()->input->get('task', '');

		// Check for array format.
		$filter = JFilterInput::getInstance();

		if (is_array($command))
		{
			$command = $filter->clean(array_pop(array_keys($command)), 'cmd');
		}
		else
		{
			$command = $filter->clean($command, 'cmd');
		}

		// Check for a controller.task command.
		if (strpos($command, '.') !== false)
		{
			// Explode the controller.task command.
			list ($type, $task) = explode('.', $command);
		}
		else
		{
			$task = $command;
		}

		return $task;
	}

	/**
	 * Loading of webservice XML file
	 *
	 * @param   string  $client             Client
	 * @param   string  $webserviceName     Webservice name
	 * @param   string  $version            Version of the webservice
	 * @param   string  $path               Path to webservice files
	 * @param   bool    $showNotifications  Show notifications
	 *
	 * @return  array  List of objects
	 */
	public static function getWebservices($client = '', $webserviceName = '', $version = '1.0.0', $path = '', $showNotifications = false)
	{
		if (empty(self::$webservices) || (!empty($webserviceName) && empty(self::$webservices[$client][$webserviceName][$version])))
		{
			self::loadWebservices($client, $webserviceName, $version, $path);
		}

		if (empty($webserviceName))
		{
			return self::$webservices;
		}

		if (!empty(self::$webservices[$client][$webserviceName][$version]))
		{
			return self::$webservices[$client][$webserviceName][$version];
		}

		return array();
	}

	/**
	 * Loading of related XML files
	 *
	 * @param   string  $client             Client
	 * @param   string  $webserviceName     Webservice name
	 * @param   string  $version            Version of the webservice
	 * @param   string  $path               Path to webservice files
	 * @param   bool    $showNotifications  Show notifications
	 *
	 * @throws Exception
	 * @return  array  List of objects
	 */
	public static function loadWebservices($client = '', $webserviceName = '', $version = '1.0.0', $path = '', $showNotifications = false)
	{
		jimport('joomla.filesystem.folder');

		if (empty($webserviceName))
		{
			$folders = JFolder::folders(self::getWebservicesPath(), '.', true);
			$webserviceXmls[' '] = JFolder::files(self::getWebservicesPath(), '.xml');

			foreach ($folders as $folder)
			{
				$webserviceXmls[$folder] = JFolder::files(self::getWebservicesPath() . '/' . $folder, '.xml');
			}

			foreach ($webserviceXmls as $webserviceXmlPath => $webservices)
			{
				foreach ($webservices as $webservice)
				{
					try
					{
						// Version, Extension and Client are already part of file name
						$xml = self::loadWebserviceConfiguration($webservice, $version = '', $extension = '', trim($webserviceXmlPath));

						if (!empty($xml))
						{
							$client = self::getWebserviceClient($xml);
							$version = !empty($xml->config->version) ? (string) $xml->config->version : $version;
							$xml->webservicePath = trim($webserviceXmlPath);
							self::$webservices[$client][(string) $xml->config->name][$version] = $xml;
						}
					}
					catch (Exception $e)
					{
						if ($showNotifications)
						{
							JFactory::getApplication()->enqueueMessage($e->getMessage(), 'message');
						}
						else
						{
							throw $e;
						}
					}
				}
			}
		}
		else
		{
			self::$webservices[$client][$webserviceName][$version] = self::loadWebserviceConfiguration($webserviceName, $version, 'xml', $path, $client);
		}
	}

	/**
	 * Method to finds the full real file path, checking possible overrides
	 *
	 * @param   string  $client          Client
	 * @param   string  $webserviceName  Name of the webservice
	 * @param   string  $version         Suffixes to the file name (ex. 1.0.0)
	 * @param   string  $extension       Extension of the file to search
	 * @param   string  $path            Path to webservice files
	 *
	 * @return  string  The full path to the api file
	 *
	 * @since   1.2
	 */
	public static function getWebserviceFile($client, $webserviceName, $version = '', $extension = 'xml', $path = '')
	{
		JLoader::import('joomla.filesystem.path');

		if (!empty($webserviceName))
		{
			$version = !empty($version) ? array(JPath::clean($version)) : array('1.0.0');
			$webservicePath = !empty($path) ? self::getWebservicesPath() . '/' . $path : self::getWebservicesPath();

			// Search for suffixed versions. Example: content.v1.xml
			if (!empty($version))
			{
				foreach ($version as $suffix)
				{
					$rawPath  = $webserviceName . '.' . $suffix . '.' . $extension;

					$rawPath = !empty($client) ? $client . '.' . $rawPath : $rawPath;

					if ($configurationFullPath = JPath::find($webservicePath, $rawPath))
					{
						return $configurationFullPath;
					}
				}
			}

			// Standard version
			$rawPath  = $webserviceName . '.' . $extension;
			$rawPath = !empty($client) ? $client . '.' . $rawPath : $rawPath;

			return JPath::find($webservicePath, $rawPath);
		}

		return null;
	}

	/**
	 * Load configuration file and set all Api parameters
	 *
	 * @param   array   $webserviceName  Name of the webservice file
	 * @param   string  $version         Suffixes for loading of webservice configuration file
	 * @param   string  $extension       File extension name
	 * @param   string  $path            Path to webservice files
	 * @param   string  $client          Client
	 *
	 * @return  SimpleXMLElement  Loaded configuration object
	 *
	 * @since   1.2
	 * @throws  Exception
	 */
	public static function loadWebserviceConfiguration($webserviceName, $version = '', $extension = 'xml', $path = '', $client = '')
	{
		// Check possible overrides, and build the full path to api file
		$configurationFullPath = self::getWebserviceFile($client, strtolower($webserviceName), $version, $extension, $path);

		if (!is_readable($configurationFullPath))
		{
			throw new Exception(JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_CONFIGURATION_FILE_UNREADABLE'));
		}

		$content = @file_get_contents($configurationFullPath);

		if (is_string($content))
		{
			return new SimpleXMLElement($content);
		}

		return null;
	}

	/**
	 * Returns string status of a given webservice
	 *
	 * @param   string  $client          Client
	 * @param   string  $webserviceName  Webservice name
	 * @param   string  $version         Version of the webservice
	 *
	 * @return  string  Status string
	 *
	 * @since   1.2
	 * @throws  RuntimeException
	 */
	public static function getStatus($client, $webserviceName, $version)
	{
		$installedWebservices = self::getInstalledWebservices();

		if (empty($installedWebservices[$client][$webserviceName][$version]))
		{
			return JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_NOT_INSTALLED');
		}

		if ($installedWebservices[$client][$webserviceName][$version]['state'] == 0)
		{
			return JText::_('JUNPUBLISHED');
		}

		if ($installedWebservices[$client][$webserviceName][$version]['state'] == 1)
		{
			return JText::_('JPUBLISHED');
		}

		return '';
	}

	/**
	 * Returns allowed methods of a given webservice
	 *
	 * @param   string  $client          Client
	 * @param   string  $webserviceName  Webservice name
	 * @param   string  $version         Version of the webservice
	 *
	 * @return  string  Status string
	 *
	 * @since   1.2
	 * @throws  RuntimeException
	 */
	public static function getMethods($client, $webserviceName, $version)
	{
		$installedWebservices = self::getInstalledWebservices();

		if (empty($installedWebservices[$client][$webserviceName][$version]['operations']))
		{
			return '--';
		}

		return $installedWebservices[$client][$webserviceName][$version]['operations'];
	}

	/**
	 * Returns allowed scopes of a given webservice
	 *
	 * @param   string  $client                Client
	 * @param   string  $webserviceName        Webservice name
	 * @param   string  $version               Version of the webservice
	 * @param   bool    $getScopeDisplayNames  Return Display name or scope name
	 *
	 * @return  string  Status string
	 *
	 * @since   1.2
	 * @throws  RuntimeException
	 */
	public static function getScopes($client, $webserviceName, $version, $getScopeDisplayNames = false)
	{
		$installedWebservices = self::getInstalledWebservices();
		$scopes = array();

		if (empty($installedWebservices[$client][$webserviceName][$version]['scopes']))
		{
			return '--';
		}

		foreach ($installedWebservices[$client][$webserviceName][$version]['scopes'] as $scope)
		{
			$scopes[] = $getScopeDisplayNames ? $scope['scopeDisplayName'] : $scope['scope'];
		}

		return implode(',', $scopes);
	}

	/**
	 * Uninstall Webservice from site
	 *
	 * @param   string  $client             Client
	 * @param   string  $webservice         Webservice name
	 * @param   string  $version            Webservice version
	 * @param   bool    $showNotifications  Show notifications
	 * @param   string  $path               Path to webservice files
	 *
	 * @return  boolean  Returns true if Webservice was successfully uninstalled
	 */
	public static function uninstallWebservice($client = '', $webservice = '', $version = '1.0.0', $showNotifications = true, $path = '')
	{
		self::getInstalledWebservices();

		if (!empty($webservice))
		{
			if (!empty(self::$installedWebservices[$client][$webservice][$version]))
			{
				if (count(self::$installedWebservices[$client][$webservice]) == 1)
				{
					unset(self::$installedWebservices[$client][$webservice]);
				}
				else
				{
					unset(self::$installedWebservices[$client][$webservice][$version]);
				}
			}
		}
		else
		{
			self::$installedWebservices = array();
		}

		self::saveRedcoreWebserviceConfig();
		self::saveOAuth2Scopes($client, $webservice, array(), $showNotifications);

		if ($showNotifications)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_UNINSTALLED'), 'message');
		}

		return true;
	}

	/**
	 * Uninstalls Webservice access and deletes XML file
	 *
	 * @param   string  $client             Client
	 * @param   string  $webservice         Webservice name
	 * @param   string  $version            Webservice version
	 * @param   bool    $showNotifications  Show notifications
	 * @param   string  $path               Path to webservice files
	 *
	 * @return  boolean  Returns true if Content element was successfully purged
	 */
	public static function deleteWebservice($client, $webservice = '', $version = '1.0.0', $showNotifications = true, $path = '')
	{
		if (self::uninstallWebservice($client, $webservice, $version, $showNotifications))
		{
			$xmlFilePath = self::getWebserviceFile($client, strtolower($webservice), $version, 'xml', $path);
			$helperFilePath = self::getWebserviceFile($client, strtolower($webservice), $version, 'php', $path);

			try
			{
				JFile::delete($xmlFilePath);

				if (!empty($helperFilePath))
				{
					JFile::delete($helperFilePath);
				}
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_REDCORE_WEBSERVICES_WEBSERVICE_DELETE_ERROR', $e->getMessage()), 'error');

				return false;
			}

			JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_DELETED'), 'message');

			return true;
		}

		return false;
	}

	/**
	 * Upload Webservices config files to redcore media location
	 *
	 * @param   array  $files  The array of Files (file descriptor returned by PHP)
	 *
	 * @return  boolean  Returns true if Upload was successful
	 */
	public static function uploadWebservice($files = array())
	{
		$uploadOptions = array(
			'allowedFileExtensions' => 'xml',
			'allowedMIMETypes'      => 'application/xml, text/xml',
			'overrideExistingFile'  => true,
		);

		foreach ($files as $key => &$file)
		{
			$objectFile = new JObject($file);

			try
			{
				$content = file_get_contents($objectFile->tmp_name);
				$fileContent = null;

				if (is_string($content))
				{
					$fileContent = new SimpleXMLElement($content);
				}

				$name = (string) $fileContent->config->name;
				$version = !empty($fileContent->config->version) ? (string) $fileContent->config->version : '1.0.0';

				$client = self::getWebserviceClient($fileContent);

				$file['name'] = $client . '.' . $name . '.' . $version . '.xml';
			}
			catch (Exception $e)
			{
				unset($files[$key]);
				JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_FILE_NOT_VALID'), 'message');
			}
		}

		return RFilesystemFile::uploadFiles($files, self::getWebservicesPath() . '/upload', $uploadOptions);
	}

	/**
	 * Install Webservice from site
	 *
	 * @param   string  $client             Client
	 * @param   string  $webservice         Webservice Name
	 * @param   string  $version            Webservice version
	 * @param   bool    $showNotifications  Show notifications
	 * @param   string  $path               Path to webservice files
	 *
	 * @return  boolean  Returns true if Webservice was successfully installed
	 */
	public static function installWebservice($client = '', $webservice = '', $version = '1.0.0', $showNotifications = true, $path = '')
	{
		self::getInstalledWebservices();

		$webserviceXml = self::getWebservices($client, $webservice, $version, $path);

		if (!empty($webserviceXml))
		{
			$operations = array();
			$scopes = array();
			$client = self::getWebserviceClient($webserviceXml);
			$version = !empty($webserviceXml->config->version) ? (string) $webserviceXml->config->version : $version;

			if (!empty($webserviceXml->operations))
			{
				foreach ($webserviceXml->operations as $operation)
				{
					foreach ($operation as $key => $method)
					{
						if ($key == 'task')
						{
							foreach ($method as $taskKey => $task)
							{
								$displayName = !empty($task['displayName']) ? (string) $task['displayName'] : $key . ' ' . $taskKey;
								$scopes[] = array(
									'scope' => strtolower($client . '.' . $webservice . '.' . $key . '.' . $taskKey),
									'scopeDisplayName' => ucfirst($displayName)
								);
							}
						}
						else
						{
							$operations[] = strtoupper(str_replace(array('read', 'create', 'update'), array('GET', 'PUT', 'POST'), $key));
							$displayName = !empty($method['displayName']) ? (string) $method['displayName'] : $key;
							$scopes[] = array(
								'scope' => strtolower($client . '.' . $webservice . '.' . $key),
								'scopeDisplayName' => ucfirst($displayName)
							);
						}
					}
				}
			}

			self::$installedWebservices[$client][$webservice][$version] = array(
				'name' => $webservice,
				'version' => $version,
				'displayName' => (string) $webserviceXml->name,
				'path' => (string) $webserviceXml->webservicePath,
				'xml' => $webservice . '.' . $version . '.xml',
				'operations' => implode(',', $operations),
				'scopes' => $scopes,
				'client' => $client,
				'state' => 1,
			);

			self::saveRedcoreWebserviceConfig();
			self::saveOAuth2Scopes($client, $webservice, $scopes, $showNotifications);

			if ($showNotifications)
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_UNINSTALLED'), 'message');
			}

			return true;
		}

		if ($showNotifications)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_FILE_NOT_FOUND'), 'message');
		}

		return false;
	}

	/**
	 * Preforms Batch action against all Webservices
	 *
	 * @param   string  $action             Action to preform
	 * @param   bool    $showNotifications  Show notification after each Action
	 *
	 * @return  boolean  Returns true if Action was successful
	 */
	public static function batchWebservices($action = '', $showNotifications = true)
	{
		$webservices = self::getWebservices();

		if (!empty($webservices))
		{
			foreach ($webservices as $webserviceNames)
			{
				foreach ($webserviceNames as $webserviceVersions)
				{
					foreach ($webserviceVersions as $webservice)
					{
						$client = self::getWebserviceClient($webservice);
						$path = $webservice->webservicePath;
						$name = (string) $webservice->config->name;
						$version = (string) $webservice->config->version;

						switch ($action)
						{
							case 'install':
								self::installWebservice($client, $name, $version, $showNotifications, $path);
								break;
							case 'uninstall':
								self::uninstallWebservice($client, $name, $version, $showNotifications, $path);
								break;
								break;
							case 'delete':
								self::deleteWebservice($client, $name, $version, $showNotifications, $path);
								break;
						}
					}
				}
			}
		}

		// Delete missing tables as well
		if ($action == 'uninstall')
		{
			self::uninstallWebservice();
		}

		return true;
	}

	/**
	 * Method to save the OAuth2 scopes
	 *
	 * @param   string  $client             Client
	 * @param   string  $webservice         Webservice name
	 * @param   array   $scopes             Scopes defined in webservice
	 * @param   bool    $showNotifications  Show notification after each Action
	 *
	 * @throws  Exception
	 * @return  bool   True on success, false on failure.
	 */
	public static function saveOAuth2Scopes($client, $webservice, $scopes = array(), $showNotifications = true)
	{
		$db = JFactory::getDbo();

		try
		{
			$db->transactionStart();

			$query = $db->getQuery(true)
				->delete('#__redcore_oauth_scopes')->where($db->qn('scope') . ' LIKE ' . $db->q($client . '.' . $webservice . '.%'));
			$db->setQuery($query);
			$db->execute();

			foreach ($scopes as $scope)
			{
				$query = $db->getQuery(true)
					->insert('#__redcore_oauth_scopes')->set($db->qn('scope') . ' = ' . $db->q($scope['scope']));
				$db->setQuery($query);
				$db->execute();
			}

			$db->transactionCommit();
		}
		catch (Exception $e)
		{
			$db->transactionRollback();

			if ($showNotifications)
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_SCOPE_ERROR'), 'error');
			}
		}
	}

	/**
	 * Method to save the configuration data.
	 *
	 * @return  bool   True on success, false on failure.
	 */
	public static function saveRedcoreWebserviceConfig()
	{
		$data = array();
		$component = JComponentHelper::getComponent('com_redcore');

		$installedWebservices = self::getInstalledWebservices();

		if (!empty($installedWebservices))
		{
			foreach ($installedWebservices as $webserviceClient => $webserviceNames)
			{
				foreach ($webserviceNames as $webserviceName => $webserviceVersions)
				{
					uasort($installedWebservices[$webserviceClient][$webserviceName], 'version_compare');
				}
			}
		}

		$component->params->set('webservices', $installedWebservices);

		$data['params'] = $component->params->toString('JSON');

		$dispatcher = RFactory::getDispatcher();
		$table = JTable::getInstance('Extension');
		$isNew = true;

		// Load the previous Data
		if (!$table->load($component->id))
		{
			return false;
		}

		// Bind the data.
		if (!$table->bind($data))
		{
			return false;
		}

		// Check the data.
		if (!$table->check())
		{
			return false;
		}

		// Trigger the onConfigurationBeforeSave event.
		$result = $dispatcher->trigger('onExtensionBeforeSave', array('com_redcore.config', $table, $isNew));

		if (in_array(false, $result, true))
		{
			return false;
		}

		// Store the data.
		if (!$table->store())
		{
			return false;
		}

		// Trigger the onConfigurationAfterSave event.
		$dispatcher->trigger('onExtensionAfterSave', array('com_redcore.config', $table, $isNew));

		return true;
	}

	/**
	 * Get list of all webservices from Redcore parameters
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function getInstalledWebservices()
	{
		if (!isset(self::$installedWebservices))
		{
			$db = JFactory::getDbo();

			// We do not want to translate this value
			$db->translate = false;

			$component = JComponentHelper::getComponent('com_redcore');

			// We put translation check back on
			$db->translate = true;
			$parameters = $component->params->toArray();
			self::$installedWebservices = !empty($parameters['webservices']) ? $parameters['webservices'] : array();
		}

		return self::$installedWebservices;
	}

	/**
	 * Get installed webservice options
	 *
	 * @param   string  $client          Client
	 * @param   string  $webserviceName  Webservice Name
	 * @param   string  $version         Webservice version
	 *
	 * @return  array  Array of webservice options
	 */
	public static function getInstalledWebservice($client, $webserviceName, $version)
	{
		// Initialise Installed webservices
		$webservices = self::getInstalledWebservices();

		if (!empty($webservices[$client][$webserviceName][$version]))
		{
			return $webservices[$client][$webserviceName][$version];
		}

		return null;
	}

	/**
	 * Checks if specific Webservice is installed and active
	 *
	 * @param   string  $client          Client
	 * @param   string  $webserviceName  Webservice Name
	 * @param   string  $version         Webservice version
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function isPublishedWebservice($client, $webserviceName, $version)
	{
		$installedWebservices = self::getInstalledWebservices();

		if (!empty($installedWebservices))
		{
			if (empty($version))
			{
				$version = self::getNewestWebserviceVersion($client, $webserviceName);
			}

			$webservice = $installedWebservices[$client][$webserviceName][$version];

			return !empty($webservice['state']);
		}

		return false;
	}

	/**
	 * Checks if specific Webservice is installed and active
	 *
	 * @param   string  $client          Client
	 * @param   string  $webserviceName  Webservice Name
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function getNewestWebserviceVersion($client, $webserviceName)
	{
		$installedWebservices = self::getInstalledWebservices();

		if (!empty($installedWebservices))
		{
			// First element is always newest
			foreach ($installedWebservices[$client][$webserviceName] as $version => $webservice)
			{
				return $version;
			}
		}

		return '1.0.0';
	}

	/**
	 * Returns Client of the webservice
	 *
	 * @param   SimpleXMLElement|array  $xmlElement  XML object
	 *
	 * @return  string
	 */
	public static function getWebserviceClient($xmlElement)
	{
		return !empty($xmlElement['client']) && strtolower($xmlElement['client']) == 'administrator' ? 'administrator' : 'site';
	}
}

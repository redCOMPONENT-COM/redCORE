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
			array('scope' => 'create', 'scopeDisplayName' => JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_CREATE')),
			array('scope' => 'read', 'scopeDisplayName' => JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_READ')),
			array('scope' => 'update', 'scopeDisplayName' => JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_UPDATE')),
			array('scope' => 'delete', 'scopeDisplayName' => JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_DELETE')),
			array('scope' => 'documentation', 'scopeDisplayName' => JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_DOCUMENTATION')),
			array('scope' => 'task', 'scopeDisplayName' => JText::_('LIB_REDCORE_API_OAUTH2_CLIENTS_SCOPES_ALL_WEBSERVICES_TASKS')),
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
	 * @param   string  $webserviceName  Webservice name
	 * @param   string  $version         Version of the webservice
	 *
	 * @return  array  List of objects
	 */
	public static function getWebservices($webserviceName = '', $version = '1.0.0')
	{
		if (empty(self::$webservices) || (!empty($webserviceName) && empty(self::$webservices[$webserviceName][$version])))
		{
			self::loadwebservices($webserviceName, $version);
		}

		if (empty($webserviceName))
		{
			return self::$webservices;
		}

		if (!empty(self::$webservices[$webserviceName][$version]))
		{
			return self::$webservices[$webserviceName][$version];
		}

		return array();
	}

	/**
	 * Loading of related XML files
	 *
	 * @param   string  $webserviceName  Webservice name
	 * @param   string  $version         Version of the webservice
	 *
	 * @return  array  List of objects
	 */
	public static function loadwebservices($webserviceName = '', $version = '1.0.0')
	{
		jimport('joomla.filesystem.folder');
		$webservices = array();

		if (empty($webserviceName))
		{
			$webserviceXmls = JFolder::files(self::getWebservicesPath(), '.xml', true);

			foreach ($webserviceXmls as $webserviceXml)
			{
				$xml = self::loadWebserviceConfiguration($webserviceXml, array(), '');

				if (!empty($xml))
				{
					$version = !empty($xml->config->version) ? (string) $xml->config->version : $version;
					self::$webservices[(string) $xml->config->name][$version] = $xml;
				}
			}
		}
		else
		{
			self::$webservices[$webserviceName][$version] = self::loadWebserviceConfiguration($webserviceName, $version, 'xml');
		}
	}

	/**
	 * Method to finds the full real file path, checking possible overrides
	 *
	 * @param   string  $webserviceName  Name of the webservice
	 * @param   string  $version         Suffixes to the file name (ex. 1.0.0)
	 * @param   string  $extension       Extension of the file to search
	 *
	 * @return  string  The full path to the api file
	 *
	 * @since   1.2
	 */
	public static function getWebserviceFile($webserviceName, $version = '', $extension = 'xml')
	{
		JLoader::import('joomla.filesystem.path');

		if (!empty($webserviceName))
		{
			$version = !empty($version) ? array(JPath::clean($version)) : array('1.0.0');

			// Search for suffixed versions. Example: content.v1.xml
			if (!empty($version))
			{
				foreach ($version as $suffix)
				{
					$rawPath  = $webserviceName . '.' . $suffix . '.' . $extension;

					if ($configurationFullPath = JPath::find(self::getWebservicesPath(), $rawPath))
					{
						return $configurationFullPath;
					}
				}
			}

			// Standard version
			$rawPath  = $webserviceName . '.' . $extension;

			return JPath::find(self::getWebservicesPath(), $rawPath);
		}

		return null;
	}

	/**
	 * Load configuration file and set all Api parameters
	 *
	 * @param   array   $webserviceName  Name of the webservice file
	 * @param   string  $version         Suffixes for loading of webservice configuration file
	 * @param   string  $extension       File extension name
	 *
	 * @return  object  Loaded configuration object
	 *
	 * @since   1.2
	 * @throws  RuntimeException
	 */
	public static function loadWebserviceConfiguration($webserviceName, $version = '', $extension = 'xml')
	{
		// Check possible overrides, and build the full path to api file
		$configurationFullPath = self::getWebserviceFile(strtolower($webserviceName), $version, $extension);

		if (!is_readable($configurationFullPath))
		{
			throw new RuntimeException(JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_CONFIGURATION_FILE_UNREADABLE'));
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
	 * @param   string  $webserviceName  Webservice name
	 * @param   string  $version         Version of the webservice
	 *
	 * @return  string  Status string
	 *
	 * @since   1.2
	 * @throws  RuntimeException
	 */
	public static function getStatus($webserviceName, $version)
	{
		$installedWebservices = self::getInstalledWebservices();

		if (empty($installedWebservices[$webserviceName][$version]))
		{
			return JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_NOT_INSTALLED');
		}

		if ($installedWebservices[$webserviceName][$version]['state'] == 0)
		{
			return JText::_('JUNPUBLISHED');
		}

		if ($installedWebservices[$webserviceName][$version]['state'] == 1)
		{
			return JText::_('JPUBLISHED');
		}

		return '';
	}

	/**
	 * Returns allowed methods of a given webservice
	 *
	 * @param   string  $webserviceName  Webservice name
	 * @param   string  $version         Version of the webservice
	 *
	 * @return  string  Status string
	 *
	 * @since   1.2
	 * @throws  RuntimeException
	 */
	public static function getMethods($webserviceName, $version)
	{
		$installedWebservices = self::getInstalledWebservices();

		if (empty($installedWebservices[$webserviceName][$version]['operations']))
		{
			return '--';
		}

		return $installedWebservices[$webserviceName][$version]['operations'];
	}

	/**
	 * Returns allowed scopes of a given webservice
	 *
	 * @param   string  $webserviceName        Webservice name
	 * @param   string  $version               Version of the webservice
	 * @param   bool    $getScopeDisplayNames  Return Display name or scope name
	 *
	 * @return  string  Status string
	 *
	 * @since   1.2
	 * @throws  RuntimeException
	 */
	public static function getScopes($webserviceName, $version, $getScopeDisplayNames = false)
	{
		$installedWebservices = self::getInstalledWebservices();
		$scopes = array();

		if (empty($installedWebservices[$webserviceName][$version]['scopes']))
		{
			return '--';
		}

		foreach ($installedWebservices[$webserviceName][$version]['scopes'] as $scope)
		{
			$scopes[] = $getScopeDisplayNames ? $scope['scopeDisplayName'] : $scope['scope'];
		}

		return implode(',', $scopes);
	}

	/**
	 * Uninstall Webservice from site
	 *
	 * @param   string  $webservice         Webservice name
	 * @param   string  $version            Webservice version
	 * @param   bool    $showNotifications  Show notifications
	 *
	 * @return  boolean  Returns true if Webservice was successfully uninstalled
	 */
	public static function uninstallWebservice($webservice = '', $version = '1.0.0', $showNotifications = true)
	{
		self::getInstalledWebservices();

		if (!empty($webservice))
		{
			if (!empty(self::$installedWebservices[$webservice][$version]))
			{
				if (count(self::$installedWebservices[$webservice]) == 1)
				{
					unset(self::$installedWebservices[$webservice]);
				}
				else
				{
					unset(self::$installedWebservices[$webservice][$version]);
				}
			}
		}
		else
		{
			self::$installedWebservices = array();
		}

		self::saveRedcoreWebserviceConfig();
		self::saveOAuth2Scopes($webservice, array(), $showNotifications);

		if ($showNotifications)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_UNINSTALLED'), 'message');
		}

		return true;
	}

	/**
	 * Uninstalls Webservice access and deletes XML file
	 *
	 * @param   string  $webservice         Webservice name
	 * @param   string  $version            Webservice version
	 * @param   bool    $showNotifications  Show notifications
	 *
	 * @return  boolean  Returns true if Content element was successfully purged
	 */
	public static function deleteWebservice($webservice = '', $version = '1.0.0', $showNotifications = true)
	{
		if (self::uninstallWebservice($webservice, $version, $showNotifications))
		{
			$xmlFilePath = self::getWebserviceFile(strtolower($webservice), $version);
			$helperFilePath = self::getWebserviceFile(strtolower($webservice), $version, 'php');

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

		foreach ($files as &$file)
		{
			$objectFile = new JObject($file);

			$content = @file_get_contents($objectFile->tmp_name);
			$fileContent = null;

			try
			{
				if (is_string($content))
				{
					$fileContent = new SimpleXMLElement($content);
				}

				$name = (string) $fileContent->config->name;
				$version = !empty($fileContent->config->version) ? (string) $fileContent->config->version : '1.0.0';

				$file['name'] = $name . '.' . $version . '.xml';
			}
			catch (Exception $e)
			{
				unset($file);
				JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_FILE_NOT_VALID'), 'message');
			}
		}

		return RFilesystemFile::uploadFiles($files, self::getWebservicesPath(), $uploadOptions);
	}

	/**
	 * Install Webservice from site
	 *
	 * @param   string  $webservice         Webservice Name
	 * @param   string  $version            Webservice version
	 * @param   bool    $showNotifications  Show notifications
	 *
	 * @return  boolean  Returns true if Webservice was successfully installed
	 */
	public static function installWebservice($webservice = '', $version = '1.0.0', $showNotifications = true)
	{
		self::getInstalledWebservices();

		$webserviceXml = self::getWebservices($webservice, $version);

		if (!empty($webserviceXml))
		{
			$operations = array();
			$scopes = array();
			$version = !empty($webserviceXml->config->version) ? (string) $webserviceXml->config->version : $version;
			$displayWebserviceName = !empty($webserviceXml->name) ? $webserviceXml->name : $webservice;

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
									'scope' => strtolower($webservice . '.' . $key . '.' . $taskKey),
									'scopeDisplayName' => ucfirst($displayName)
								);
							}
						}
						else
						{
							$operations[] = strtoupper(str_replace(array('read', 'create', 'update'), array('GET', 'PUT', 'POST'), $key));
							$displayName = !empty($method['displayName']) ? (string) $method['displayName'] : $key;
							$scopes[] = array(
								'scope' => strtolower($webservice . '.' . $key),
								'scopeDisplayName' => ucfirst($displayName)
							);
						}
					}
				}
			}

			self::$installedWebservices[$webservice][$version] = array(
				'name' => $webservice,
				'version' => $version,
				'displayName' => (string) $webserviceXml->name,
				'xml' => $webservice . '.' . $version . '.xml',
				'operations' => implode(',', $operations),
				'scopes' => $scopes,
				'state' => 1,
			);

			self::saveRedcoreWebserviceConfig();
			self::saveOAuth2Scopes($webservice, $scopes, $showNotifications);

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
			foreach ($webservices as $webserviceVersions)
			{
				foreach ($webserviceVersions as $webservice)
				{
					$name = (string) $webservice->config->name;
					$version = (string) $webservice->config->version;

					switch ($action)
					{
						case 'install':
							self::installWebservice($name, $version, $showNotifications);
							break;
						case 'uninstall':
							self::uninstallWebservice($name, $version, $showNotifications);
							break;
							break;
						case 'delete':
							self::deleteWebservice($name, $version, $showNotifications);
							break;
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
	 * @param   string  $webservice         Webservice name
	 * @param   array   $scopes             Scopes defined in webservice
	 * @param   bool    $showNotifications  Show notification after each Action
	 *
	 * @throws  Exception
	 * @return  bool   True on success, false on failure.
	 */
	public static function saveOAuth2Scopes($webservice, $scopes = array(), $showNotifications = true)
	{
		$db = JFactory::getDbo();

		try
		{
			$db->transactionStart();

			$query = $db->getQuery(true)
				->delete('#__redcore_oauth_scopes')->where($db->qn('scope') . ' LIKE ' . $db->q($webservice . '.%'));
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
			foreach ($installedWebservices as $webserviceName => $webserviceVersions)
			{
				uasort($installedWebservices[$webserviceName], 'version_compare');
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
	 * Checks if specific Webservice is installed and active
	 *
	 * @param   string  $webserviceName  Webservice Name
	 * @param   string  $version         Webservice version
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function isPublishedWebservice($webserviceName, $version)
	{
		$installedWebservices = self::getInstalledWebservices();

		if (!empty($installedWebservices))
		{
			if (empty($version))
			{
				$version = self::getNewestWebserviceVersion($webserviceName);
			}

			$webservice = $installedWebservices[$webserviceName][$version];

			return !empty($webservice['state']);
		}

		return false;
	}

	/**
	 * Checks if specific Webservice is installed and active
	 *
	 * @param   string  $webserviceName  Webservice Name
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function getNewestWebserviceVersion($webserviceName)
	{
		$installedWebservices = self::getInstalledWebservices();

		if (!empty($installedWebservices))
		{
			// First element is always newest
			foreach ($installedWebservices[$webserviceName] as $version => $webservice)
			{
				return $version;
			}
		}

		return '';
	}
}

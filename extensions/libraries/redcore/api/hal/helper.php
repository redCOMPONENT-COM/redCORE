<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

jimport('joomla.filesystem.folder');

use Joomla\Utilities\ArrayHelper;

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
		return JPATH_ROOT . '/' . self::getWebservicesRelativePath();
	}

	/**
	 * Get Webservices path
	 *
	 * @return  string
	 *
	 * @since   1.2
	 */
	public static function getWebservicesRelativePath()
	{
		return 'media/redcore/webservices';
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
		$command = JFactory::getApplication()->input->get('task', '');

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
	 * @throws Exception
	 * @return  array  List of objects
	 */
	public static function getWebservices($client = '', $webserviceName = '', $version = '1.0.0', $path = '', $showNotifications = false)
	{
		if (empty(self::$webservices) || (!empty($webserviceName) && empty(self::$webservices[$client][$webserviceName][$version])))
		{
			try
			{
				self::loadWebservices($client, $webserviceName, $version, $path);
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
		if (empty($webserviceName))
		{
			$folders             = JFolder::folders(self::getWebservicesPath(), '.', true);
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
							$client                                                            = self::getWebserviceClient($xml);
							$version                                                           = !empty($xml->config->version) ? (string) $xml->config->version : $version;
							$xml->webservicePath                                               = trim($webserviceXmlPath);
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
			try
			{
				$xml = self::loadWebserviceConfiguration($webserviceName, $version, 'xml', $path, $client);

				if (!empty($xml))
				{
					$client                                                            = self::getWebserviceClient($xml);
					$version                                                           = !empty($xml->config->version) ? (string) $xml->config->version : $version;
					$xml->webservicePath                                               = trim($path);
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
			$version        = !empty($version) ? array(JPath::clean($version)) : array('1.0.0');
			$webservicePath = !empty($path) ? self::getWebservicesPath() . '/' . $path : self::getWebservicesPath();

			// Search for suffixed versions. Example: content.1.0.0.xml
			if (!empty($version))
			{
				foreach ($version as $suffix)
				{
					$rawPath = $webserviceName . '.' . $suffix;
					$rawPath = !empty($extension) ? $rawPath . '.' . $extension : $rawPath;
					$rawPath = !empty($client) ? $client . '.' . $rawPath : $rawPath;

					if ($configurationFullPath = JPath::find($webservicePath, $rawPath))
					{
						return $configurationFullPath;
					}
				}
			}

			// Standard version
			$rawPath = !empty($extension) ? $webserviceName . '.' . $extension : $webserviceName;
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
				$content     = file_get_contents($objectFile->tmp_name);
				$fileContent = null;

				if (is_string($content))
				{
					$fileContent = new SimpleXMLElement($content);
				}

				$name    = (string) $fileContent->config->name;
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
	 * Get list of all webservices from Redcore parameters
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function getInstalledWebservices()
	{
		if (!isset(self::$installedWebservices))
		{
			self::$installedWebservices = array();
			$db                         = JFactory::getDbo();

			$query = $db->getQuery(true)
				->select('*')
				->from('#__redcore_webservices')
				->order('created_date ASC');

			$db->setQuery($query);
			$webservices = $db->loadObjectList();

			if (!empty($webservices))
			{
				foreach ($webservices as $webservice)
				{
					self::$installedWebservices[$webservice->client][$webservice->name][$webservice->version] = ArrayHelper::fromObject($webservice);
				}
			}
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

		if (!empty($installedWebservices) && isset($installedWebservices[$client][$webserviceName]))
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

	/**
	 * Returns Scopes of the webservice
	 *
	 * @param   array  $filterScopes  Scopes that will be used as a filter
	 *
	 * @return  array
	 */
	public static function getWebserviceScopes($filterScopes = array())
	{
		$options              = array();
		$installedWebservices = self::getInstalledWebservices();

		if (empty($filterScopes))
		{
			// Options for all webservices
			$options[JText::_('COM_REDCORE_OAUTH_CLIENTS_SCOPES_ALL_WEBSERVICES')] = self::getDefaultScopes();
		}

		if (!empty($installedWebservices))
		{
			foreach ($installedWebservices as $webserviceClient => $webserviceNames)
			{
				foreach ($webserviceNames as $webserviceName => $webserviceVersions)
				{
					foreach ($webserviceVersions as $version => $webservice)
					{
						$webserviceDisplayName = JText::_('J' . $webserviceClient) . ' '
							. (!empty($webservice['title']) ? $webservice['title'] : $webserviceName);

						if (!empty($webservice['scopes']))
						{
							$scopes = json_decode($webservice['scopes'], true);

							foreach ($scopes as $scope)
							{
								$scopeParts = explode('.', $scope['scope']);

								// For global check of filtered scopes using $client . '.' . $operation
								$globalCheck = $scopeParts[0] . '.' . $scopeParts[2];

								if (empty($filterScopes) || in_array($scope['scope'], $filterScopes) || in_array($globalCheck, $filterScopes))
								{
									$options[$webserviceDisplayName][] = $scope;
								}
							}
						}
					}
				}
			}
		}

		return $options;
	}

	/**
	 * Generate a JWT
	 *
	 * @param   string  $privateKey  The private key to use to sign the token
	 * @param   string  $iss         The issuer, usually the client_id
	 * @param   string  $sub         The subject, usually a user_id
	 * @param   string  $aud         The audience, usually the URI for the oauth server
	 * @param   string  $exp         The expiration date. If the current time is greater than the exp, the JWT is invalid
	 * @param   string  $nbf         The "not before" time. If the current time is less than the nbf, the JWT is invalid
	 * @param   string  $jti         The "jwt token identifier", or nonce for this JWT
	 *
	 * @return string  JWT
	 */
	public static function generateJWT($privateKey, $iss, $sub, $aud, $exp = null, $nbf = null, $jti = null)
	{
		if (!$exp)
		{
			$exp = time() + 1000;
		}

		$params = array(
			'iss' => $iss,
			'sub' => $sub,
			'aud' => $aud,
			'exp' => $exp,
			'iat' => time(),
		);

		if ($nbf)
		{
			$params['nbf'] = $nbf;
		}

		if ($jti)
		{
			$params['jti'] = $jti;
		}

		$jwtUtil = new \OAuth2\Encryption\Jwt;

		return $jwtUtil->encode($params, $privateKey, 'RS256');
	}

	/**
	 * Returns list of transform elements
	 *
	 * @return  array
	 */
	public static function getTransformElements()
	{
		static $transformElements = null;

		if (!is_null($transformElements))
		{
			return $transformElements;
		}

		$transformElementsFiles = JFolder::files(JPATH_LIBRARIES . '/redcore/api/hal/transform', '.php');
		$transformElements      = array();

		foreach ($transformElementsFiles as $transformElement)
		{
			if (!in_array($transformElement, array('interface.php', 'base.php')))
			{
				$name                = str_replace('.php', '', $transformElement);
				$transformElements[] = array(
					'value' => $name,
					'text' => $name,
				);
			}
		}

		return $transformElements;
	}

	/**
	 * Returns transform element that is appropriate to db type
	 *
	 * @param   string  $type  Database type
	 *
	 * @return  string
	 */
	public static function getTransformElementByDbType($type)
	{
		$type = explode('(', $type);
		$type = strtoupper(trim($type[0]));

		// We do not test for Varchar because fallback Transform Element String
		switch ($type)
		{
			case 'TINYINT':
			case 'SMALLINT':
			case 'MEDIUMINT':
			case 'INT':
			case 'BIGINT':
				return 'int';
			case 'FLOAT':
			case 'DOUBLE':
			case 'DECIMAL':
				return 'float';
			case 'DATE':
			case 'DATETIME':
			case 'TIMESTAMP':
			case 'TIME':
				return 'datetime';
		}

		return 'string';
	}

	/**
	 * Returns uri to the webservice
	 *
	 * @param   string  $client        Client
	 * @param   string  $name          Name
	 * @param   string  $version       Version
	 * @param   string  $appendApi     Append api at the end or the URI
	 * @param   string  $appendFormat  Append format at the end or the URI
	 *
	 * @return  string
	 */
	public static function buildWebserviceUri($client, $name, $version, $appendApi = '', $appendFormat = '')
	{
		$uri = 'webserviceClient=' . $client
			. '&webserviceVersion=' . $version;

		// Views are separated by dash
		$view = explode('-', $name);
		$name = $view[0];

		$uri .= '&option=' . $name;

		if (!empty($view[1]))
		{
			$uri .= '&view=' . $view[1];
		}

		if (!empty($appendApi))
		{
			$uri .= '&api=' . $appendApi;
		}

		if (!empty($appendFormat))
		{
			$uri .= '&format=' . $appendFormat;
		}

		return $uri;
	}

	/**
	 * Returns Full URL to the webservice
	 *
	 * @param   string  $client        Client
	 * @param   string  $name          Name
	 * @param   string  $version       Version
	 * @param   string  $appendApi     Append api at the end or the URI
	 * @param   string  $appendFormat  Append format at the end or the URI
	 *
	 * @return  string
	 */
	public static function buildWebserviceFullUrl($client, $name, $version, $appendApi = '', $appendFormat = '')
	{
		$uri = self::buildWebserviceUri($client, $name, $version, $appendApi, $appendFormat);

		return rtrim(JUri::base(), '/') . '/index.php?' . $uri;
	}

	/**
	 * Returns user credentials from globals
	 *
	 * @return  array
	 */
	public static function getCredentialsFromGlobals()
	{
		$credentials = array();
		$headers     = RApi::getHeaderVariablesFromGlobals();

		if (isset($headers['PHP_AUTH_USER']) && isset($headers['PHP_AUTH_PW']))
		{
			return $credentials = array(
				'username'	 => $headers['PHP_AUTH_USER'],
				'password'	 => $headers['PHP_AUTH_PW']
			);
		}

		return $credentials;
	}

	/**
	 * Returns an array of fields from Element Fields properties
	 *
	 * @param   SimpleXMLElement  $xmlElement   Xml element
	 * @param   boolean           $primaryKeys  Only extract primary keys
	 *
	 * @return  array
	 */
	public static function getFieldsArray($xmlElement, $primaryKeys = false)
	{
		$fields = array();

		if (isset($xmlElement->fields->field))
		{
			foreach ($xmlElement->fields->field as $field)
			{
				$fieldAttributes = self::getXMLElementAttributes($field);

				if (($primaryKeys && self::isAttributeTrue($field, 'isPrimaryField'))
					|| !$primaryKeys)
				{
					$fields[$fieldAttributes['name']] = $fieldAttributes;
				}
			}
		}

		// If there are no primary keys defined we will use id field as default
		if (empty($fields) && $primaryKeys)
		{
			$fields['id'] = array('name' => 'id', 'transform' => 'int');
		}

		return $fields;
	}

	/**
	 * Gets list of filter fields from operation configuration
	 *
	 * @param   SimpleXMLElement  $configuration   Configuration for current action
	 * @param   boolean           $excludeSearch   Exclude the search element, maintaining just the xml-provided fields
	 * @param   boolean           $fullDefinition  Gets the full definition of the filter, not just the name
	 *
	 * @return  array
	 *
	 * @since   1.3
	 */
	public static function getFilterFields($configuration, $excludeSearch = false, $fullDefinition = false)
	{
		// We have one search filter field
		$filterFields = array();

		if (!$excludeSearch)
		{
			if ($fullDefinition)
			{
				$filterFields[] = array(
					'name' => 'search',
					'isRequiredField' => 'false',
					'transform' => 'string'
				);
			}
			else
			{
				$filterFields[] = 'search';
			}
		}

		if (!empty($configuration->fields))
		{
			foreach ($configuration->fields->field as $field)
			{
				if (self::isAttributeTrue($field, 'isFilterField'))
				{
					if ($fullDefinition)
					{
						$required = 'false';

						if (self::isAttributeTrue($field, 'isRequiredField'))
						{
							$required = 'true';
						}

						$filterFields[] = array(
							'name' => (string) $field['name'],
							'isRequiredField' => $required,
							'transform' => (isset($field['transform'])) ? (string) $field['transform'] : 'string'
						);
					}
					else
					{
						$filterFields[] = (string) $field["name"];
					}
				}
			}
		}

		return $filterFields;
	}
}

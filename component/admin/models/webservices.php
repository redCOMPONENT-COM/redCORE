<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Webservices Model
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.2
 */
class RedcoreModelWebservices extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_webservices';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'webservices_limit';

	/**
	 * xml Files from webservice folder
	 *
	 * @var  array
	 */
	public $xmlFiles = array();

	/**
	 * Installed xml Files from webservice folder
	 *
	 * @var  array
	 */
	public $installedXmlFiles = array();

	/**
	 * Number of available xml files for install
	 *
	 * @var  int
	 */
	public $xmlFilesAvailable = 0;

	/**
	 * Constructor
	 *
	 * @param   array  $config  Configuration array
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'published', 'w.published'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function setXmlFiles()
	{
		$xmlFiles = RApiHalHelper::getWebservices($client = '', $webserviceName = '', $version = '', $path = '', $showNotifications = true);

		if (!empty($xmlFiles))
		{
			$db	= $this->getDbo();

			$query = $db->getQuery(true)
				->select('CONCAT(' . $db->qn('client') . ', ' . $db->qn('name') . ', ' . $db->qn('version') . ')')
				->from($db->qn('#__redcore_webservices', 'w'));

			$db->setQuery($query);
			$webservices = $db->loadColumn();

			if (!empty($webservices))
			{
				foreach ($xmlFiles as $client => $webserviceNames)
				{
					foreach ($webserviceNames as $name => $webserviceVersions)
					{
						foreach ($webserviceVersions as $version => $xmlWebservice)
						{
							$this->xmlFilesAvailable++;

							foreach ($webservices as $webservice)
							{
								if ($webservice == $client . $name . $version)
								{
									// We store it so we can use it in webservice list so we do not load files twice
									$this->installedXmlFiles[$client][$name][$version] = $xmlWebservice;

									// We remove it from the list
									unset($xmlFiles[$client][$name][$version]);
									$this->xmlFilesAvailable--;
									break;
								}
							}
						}
					}
				}
			}

			$this->xmlFiles = $xmlFiles;
		}
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$db	= $this->getDbo();

		$query = $db->getQuery(true)
			->select('w.*')
			->from($db->qn('#__redcore_webservices', 'w'));

		// Filter search
		$search = $this->getState('filter.search_webservices');

		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where('(w.name LIKE ' . $search . ') OR (w.title LIKE ' . $search . ')');
		}

		// Ordering
		$orderList = $this->getState('list.ordering');
		$directionList = $this->getState('list.direction');

		$order = !empty($orderList) ? $orderList : 'w.title';
		$direction = !empty($directionList) ? $directionList : 'ASC';
		$query->order($db->escape($order) . ' ' . $db->escape($direction));

		return $query;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.2
	 */
	public function getItems()
	{
		// We are loading all webservice XML files with this
		$this->setXmlFiles();

		$items = parent::getItems();

		if (!empty($items))
		{
			foreach ($items as $item)
			{
				$item->xml = !empty($this->installedXmlFiles[$item->client][$item->name][$item->version]) ?
					$this->installedXmlFiles[$item->client][$item->name][$item->version] : false;

				$item->scopes = json_decode($item->scopes, true);
			}
		}

		return $items;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.2
	 */
	public function getXmlFiles()
	{
		return $this->xmlFiles;
	}

	/**
	 * Install Webservice from site
	 *
	 * @param   string  $client      Client
	 * @param   string  $webservice  Webservice Name
	 * @param   string  $version     Webservice version
	 * @param   string  $path        Path to webservice files
	 * @param   int     $id          Path to webservice files
	 *
	 * @return  boolean  Returns id if Webservice was successfully installed
	 */
	public function installWebservice($client = '', $webservice = '', $version = '1.0.0', $path = '', $id = 0)
	{
		$webserviceXml = RApiHalHelper::getWebservices($client, $webservice, $version, $path, true);

		if (!empty($webserviceXml))
		{
			$operations = array();
			$scopes = array();
			$client = RApiHalHelper::getWebserviceClient($webserviceXml);
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

			RApiHalHelper::$installedWebservices[$client][$webservice][$version] = array(
				'name'          => $webservice,
				'version'       => $version,
				'title'         => (string) $webserviceXml->name,
				'path'          => (string) $path,
				'xmlFile'       => $client . '.' . $webservice . '.' . $version . '.xml',
				'xmlHashed'     => md5($webserviceXml),
				'operations'    => json_encode($operations),
				'scopes'        => json_encode($scopes),
				'client'        => $client,
				'state'         => 1,
				'id'            => $id,
			);

			/** @var RedcoreTableWebservice $table */
			$table = RTable::getInstance('Webservice', 'RedcoreTable');
			$table->bind(RApiHalHelper::$installedWebservices[$client][$webservice][$version]);

			// Check the data.
			if (!$table->check())
			{
				return false;
			}

			if (!$table->store())
			{
				if (empty($id))
				{
					$this->setError(JText::sprintf('COM_REDCORE_WEBSERVICES_WEBSERVICE_NOT_INSTALLED', $table->getError()));
				}

				return false;
			}

			RApiHalHelper::saveOAuth2Scopes($client, $webservice, $scopes, false);

			$this->setState($this->getName() . '.id', $table->id);

			return $table->id;
		}

		return false;
	}

	/**
	 * Uninstalls Webservice access and deletes XML file
	 *
	 * @param   string  $client      Client
	 * @param   string  $webservice  Webservice name
	 * @param   string  $version     Webservice version
	 * @param   string  $path        Path to webservice files
	 *
	 * @return  boolean  Returns true if Content element was successfully purged
	 */
	public function deleteWebservice($client, $webservice = '', $version = '1.0.0', $path = '')
	{
		$xmlFilePath = RApiHalHelper::getWebserviceFile($client, strtolower($webservice), $version, 'xml', $path);
		$helperFilePath = RApiHalHelper::getWebserviceFile($client, strtolower($webservice), $version, 'php', $path);

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
}

<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');

/**
 * Webservices Model
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.2
 */
class RedcoreModelWebservice_History_Logs extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_webservice_history_logs';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'webservice_history_logs_limit';

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
				'webservice_name', 'whl.webservice_name',
				'webservice_version', 'whl.webservice_version',
				'webservice_client', 'whl.webservice_client',
				'operation', 'whl.operation',
				'using_soap', 'whl.using_soap',
				'authentication_user', 'whl.authentication_user',
				'created_date', 'whl.created_date',
				'created_by_start_date', 'created_by_end_date'
			);
		}

		parent::__construct($config);
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
			->select('whl.*')
			->from($db->qn('#__redcore_webservice_history_log', 'whl'));

		$client = $this->getState('filter.webservice_client');

		// Filter by webservice client.
		if ($client)
		{
			$query->where('whl.webservice_client = ' . $db->quote($db->escape($client, true)));
		}

		$version = $this->getState('filter.webservice_version');

		// Filter by webservice version
		if ($version)
		{
			$query->where('whl.webservice_version = ' . $db->quote($db->escape($version, true)));
		}

		// Filter by operation.
		$operation = $this->getState('filter.operation');

		if ($operation)
		{
			$query->where('whl.operation like ' . $db->quote('%' . $db->escape($operation, true) . '%'));
		}

		// Filter by using soap.
		$usingSoap = $this->getState('filter.using_soap');

		if ($usingSoap)
		{
			$query->where('whl.using_soap = ' . $db->quote($db->escape($usingSoap, true)));
		}

		// Filter by authentication user
		$user = $this->getState('filter.authentication_user');

		if ($user)
		{
			$query->where('whl.authentication_user = ' . $db->quote($db->escape($user, true)));
		}

		// Filter by start date
		$startDate = $this->getState('filter.created_by_start_date');

		if ($startDate)
		{
			$startDate = date('Y-m-d H:i:s', strtotime($startDate));
			$query->where('whl.created_date >= ' . $db->quote($db->escape($startDate, true)));
		}

		// Filter by start date
		$endDate = $this->getState('filter.created_by_end_date');

		if ($endDate)
		{
			$endDate = date('Y-m-d H:i:s', strtotime($endDate));
			$query->where('whl.created_date <= ' . $db->quote($db->escape($endDate, true)));
		}

		// Filter search
		$search = $this->getState('filter.search_webservice_history_logs');

		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where('(whl.webservice_name LIKE ' . $search . ') OR (whl.url LIKE ' . $search . ')');
		}

		// Ordering
		$orderList     = $this->getState('list.ordering');
		$directionList = $this->getState('list.direction');

		$order     = !empty($orderList) ? $orderList : 'whl.created_date';
		$direction = !empty($directionList) ? $directionList : 'DESC';
		$query->order($db->escape($order) . ' ' . $db->escape($direction));

		return $query;
	}
}

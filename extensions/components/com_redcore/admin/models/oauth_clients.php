<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Oauth Clients Model
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.2
 */
class RedcoreModelOauth_Clients extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_oauth_clients';

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
				'client_id', 'oc.client_id',
				'client_secret', 'oc.client_secret',
				'redirect_uri', 'oc.redirect_uri',
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
			->select('oc.*')
			->select('u.name')
			->from($db->qn('#__redcore_oauth_clients', 'oc'))
			->leftJoin($db->qn('#__users', 'u') . ' ON u.id = oc.user_id');

		// Filter search
		$search = $this->getState('filter.search_oauth_clients');

		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where('(oc.client_id LIKE ' . $search . ')');
		}

		// Ordering
		$orderList = $this->getState('list.ordering');
		$directionList = $this->getState('list.direction');

		$order = !empty($orderList) ? $orderList : 'oc.client_id';
		$direction = !empty($directionList) ? $directionList : 'ASC';
		$query->order($db->escape($order) . ' ' . $db->escape($direction));

		return $query;
	}

	/**
	 * Method to get an array of data items. override to add content items
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.2
	 */
	public function getItems()
	{
		$items = parent::getItems();

		return $items;
	}
}

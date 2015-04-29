<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Payments Model
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.5
 */
class RedcoreModelPayments extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_payments';

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
				'payment_name', 'p.payment_name',
				'extension_name', 'p.extension_name',
				'owner_name', 'p.owner_name',
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
			->select('p.*')
			->from($db->qn('#__redcore_payments', 'p'));

		// Filter search
		$search = $this->getState('filter.search_payments');

		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where('(p.order_name LIKE ' . $search . ')');
		}

		if ($paymentName = $this->getState('filter.payment_name'))
		{
			$paymentName = $db->quote($paymentName);
			$query->where('p.payment_name = ' . $paymentName);
		}

		if ($extensionName = $this->getState('filter.extension_name'))
		{
			$extensionName = $db->quote($extensionName);
			$query->where('p.extension_name = ' . $extensionName);
		}

		if ($ownerName = $this->getState('filter.owner_name'))
		{
			$ownerName = $db->quote($ownerName);
			$query->where('p.owner_name = ' . $ownerName);
		}

		// Ordering
		$orderList = $this->getState('list.ordering');
		$directionList = $this->getState('list.direction');

		$order = !empty($orderList) ? $orderList : 'p.payment_name';
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

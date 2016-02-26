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
 * Payment configurations Model
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.5
 */
class RedcoreModelPayment_Configurations extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_payment_configurations';

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
				'extension_name', 'pc.extension_name',
				'owner_name', 'pc.owner_name',
				'payment_name', 'pc.payment_name',
				'state', 'pc.state',
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

		$queryDefaults = $db->getQuery(true)
			->select('p.params as plugin_params, p.name as plugin_name, p.element, p.enabled, p.extension_id')
			->select('CONCAT("plg_redpayment_", p.element) as plugin_path_name')
			->from($db->qn('#__extensions', 'p'))
			->where($db->qn('p.type') . '= "plugin"')
			->where($db->qn('p.folder') . '= "redpayment"')
			->select('pc.*')
			->leftJoin($db->qn('#__redcore_payment_configuration', 'pc') . ' ON 1 = 2');

		$query = $db->getQuery(true)
			->select('p.params as plugin_params, p.name as plugin_name, p.element, p.enabled, p.extension_id')
			->select('CONCAT("plg_redpayment_", p.element) as plugin_path_name')
			->from($db->qn('#__redcore_payment_configuration', 'pc'))
			->where($db->qn('p.type') . '= "plugin"')
			->where($db->qn('p.folder') . '= "redpayment"')
			->select('pc.*')
			->leftJoin($db->qn('#__extensions', 'p') . ' ON pc.payment_name = p.element');

		// Filter search
		$search = $this->getState('filter.search_payment_configurations');

		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where('(pc.extension_name LIKE ' . $search . ' OR pc.payment_name LIKE ' . $search . ' OR pc.owner_name LIKE ' . $search . ' )');
			$queryDefaults->where('(pc.extension_name LIKE ' . $search . ' OR pc.payment_name LIKE ' . $search . ' OR pc.owner_name LIKE ' . $search . ')');
		}

		if ($paymentName = $this->getState('filter.payment_name'))
		{
			$paymentName = $db->quote($paymentName);
			$query->where('pc.payment_name = ' . $paymentName);
			$queryDefaults->where('p.element = ' . $paymentName);
		}

		if ($extensionName = $this->getState('filter.extension_name'))
		{
			$extensionName = $db->quote($extensionName);
			$query->where('pc.extension_name = ' . $extensionName);
			$queryDefaults->where('pc.extension_name = ' . $extensionName);
		}

		if ($ownerName = $this->getState('filter.owner_name'))
		{
			$ownerName = $db->quote($ownerName);
			$query->where('pc.owner_name = ' . $ownerName);
			$queryDefaults->where('pc.owner_name = ' . $ownerName);
		}

		// Ordering
		$orderList = $this->getState('list.ordering');
		$directionList = $this->getState('list.direction');

		$order = !empty($orderList) ? $orderList : 'element';
		$direction = !empty($directionList) ? $directionList : 'ASC';
		$query->order('extension_name ASC, owner_name ASC, ' . $db->escape($order) . ' ' . $db->escape($direction));

		$query = $queryDefaults . ' UNION ' . $query;

		return $query;
	}
}

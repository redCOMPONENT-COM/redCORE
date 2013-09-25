<?php
/**
 * @package     Redcore
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * RModelTable eases to deal with data tables (filtering/ordering/search).
 *
 * @package     Redcore
 * @subpackage  Base
 * @since       1.0
 */
abstract class RModelTable extends RModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields']  = array_merge(
				array_keys($this->getOrderableColumns()),
				array_values($this->getOrderableColumns())
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Get the filterable columns.
		$columns = $this->getFilterableColumns();

		// Search filter.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		foreach ($columns as $name => $column)
		{
			$filterName = str_replace('.', '_', $name);
			$filter = $this->getUserStateFromRequest($this->context . '.filter.' . $name, 'filter_' . $filterName);
			$this->setState('filter.' . $name, $filter);
		}

		// If we have a name or title filter, order by this column.
		if (isset($filterFields['name']))
		{
			parent::populateState($filterFields['name'], 'DESC');
		}

		elseif (isset($filterFields['title']))
		{
			parent::populateState($filterFields['title'], 'DESC');
		}

		else
		{
			parent::populateState();
		}
	}

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		foreach ($this->getFilterableColumns() as $name => $column)
		{
			$filterName = str_replace('.', '_', $name);
			$id .= ':' . $this->getState('filter.' . $filterName);
		}

		return parent::getStoreId($id);
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 */
	protected function getListQuery()
	{
		$db = $this->_db;
		$query = $this->getQuery();

		// Filtering.
		foreach ($this->getFilterableColumns() as $name => $column)
		{
			$filterName = str_replace('.', '_', $name);
			$filterValue = $this->getState('filter.' . $filterName);

			if (!is_null($filterValue))
			{
				$query->where($db->qn($column) . '=' . $db->q($filterValue));
			}
		}

		// Search.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			foreach ($this->getSearchableColumns() as $column)
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where($db->qn($column) . 'LIKE ' . $search);
			}
		}

		// Ordering.
		$listOrdering = $db->escape($this->getState('list.ordering'));
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));

		$query->order($listOrdering . ' ' . $listDirn);

		return $query;
	}

	/**
	 * Get the raw query without any filtering.
	 *
	 * @return  JDatabaseQuery  A JDatabaseQuery object to retrieve the data set.
	 */
	abstract protected function getQuery();

	/**
	 * Get the column names for filtering.
	 *
	 * @return  array  An array of filter name as keys and (aliased) column name as values.
	 *
	 * Example : array('created_date' => 'u.created_date')
	 */
	abstract protected function getFilterableColumns();

	/**
	 * Get the column names for ordering.
	 *
	 * @return  array  An array of filter name as keys and (aliased) column name as values.
	 *
	 * Example : array('created_date' => 'u.created_date')
	 */
	abstract protected function getOrderableColumns();

	/**
	 * Get the searchable column names.
	 *
	 * @return  array  An array of (aliased) searchable column names.
	 *
	 * Example : array('p.author_id', 'u.created_date')
	 */
	abstract protected function getSearchableColumns();
}

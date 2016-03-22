<?php
/**
 * @package     Redcore
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

JLoader::import('joomla.application.component.modellist');

/**
 * redCORE Dynamic Model List
 *
 * @package     Redcore
 * @subpackage  Base
 * @since       1.3
 */
class RApiHalModelList extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = null;

	/**
	 * Name of the table to load
	 *
	 * @var  string
	 */
	protected $tableName = null;

	/**
	 * Configuration to set up this model
	 *
	 * @var  string
	 */
	public $modelConfig = array();

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JModelLegacy
	 */
	public function __construct($config = array())
	{
		if (!empty($config["tableName"]))
		{
			$this->tableName = $config["tableName"];
			$this->name = $config["tableName"];
		}

		if (!empty($config["context"]))
		{
			$this->context = strtolower($config["context"]);
		}

		if (!empty($config["paginationPrefix"]))
		{
			$this->paginationPrefix = strtolower($config["paginationPrefix"]);
		}

		if (!empty($config["filterFields"]))
		{
			$this->filter_fields = $config["filterFields"];
		}

		$this->modelConfig = $config;

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getTableName()
	{
		return '#__' . str_replace('#__', '', $this->tableName);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$table = $this->getTableName();
		$db	= $this->getDbo();
		$query = $db->getQuery(true);
		$activeFields = array();
		$hiddenFields = array();
		$searchableFields = array();

		$query->from($db->qn($table));

		if (!empty($this->modelConfig['fields']))
		{
			foreach ($this->modelConfig['fields'] as $field)
			{
				if (!empty($field['isHiddenField']) && strtolower($field['isHiddenField']) == 'true')
				{
					$hiddenFields[] = $field['name'];
				}
				else
				{
					$activeFields[] = $field['name'];
					$query->select($db->qn($field['name']));
				}

				if (!empty($field['isSearchableField']) && strtolower($field['isSearchableField']) == 'true')
				{
					$searchableFields[] = $field['name'];
				}
			}

			// If no active fields are defined then we select all fields
			if (empty($activeFields))
			{
				// Fetch all columns from database table
				$dbColumns = $db->getTableColumns($table);
				$columns = array_keys($dbColumns);

				foreach ($columns as $column)
				{
					// If fields is not hidden, then we fetch it
					if (!in_array($column, $hiddenFields))
					{
						$query->select($db->qn($column));
					}
				}
			}
		}
		else
		{
			// If no fields are defined then we select all fields
			$query->select('*');
		}

		// Filter search
		$search = $this->getState('filter.search');

		if (!empty($search) && !empty($searchableFields))
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$searchColumns = array();

			foreach ($searchableFields as $column)
			{
				$searchColumns[] = '(' . $db->qn($column) . ' LIKE ' . $search . ')';
			}

			if (!empty($searchColumns))
			{
				$query->where('(' . implode(' OR ', $searchColumns) . ')');
			}
		}

		if (!empty($this->filter_fields))
		{
			foreach ($this->filter_fields as $filter)
			{
				if ($filter != 'search')
				{
					if ($filterValue = $this->getState('filter.' . $filter))
					{
						$query->where($db->qn($filter) . ' = ' . $db->q($filterValue));
					}
				}
			}
		}

		// Ordering
		$orderList = $this->getState('list.ordering');
		$directionList = $this->getState('list.direction');

		if (!empty($orderList))
		{
			$order = !empty($orderList) ? $orderList : '';
			$direction = !empty($directionList) ? $directionList : 'ASC';
			$query->order($db->escape($order) . ' ' . $db->escape($direction));
		}

		return $query;
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   12.2
	 * @throws  Exception
	 */
	public function getTable($name = '', $prefix = 'Table', $options = array())
	{
		$db = $this->getDbo();
		$config = $this->modelConfig;
		$tableName = str_replace('#__', '', $config['tableName']);

		if (!empty($config['primaryFields']))
		{
			if (count($config['primaryFields']) > 1)
			{
				$tableKey = $config['primaryFields'];
			}
			else
			{
				$tableKey = $config['primaryFields'][0];
			}
		}
		else
		{
			$tableKey = 'id';
		}

		$table = new RApiHalTableTable($tableName, $tableKey, $db);

		return $table;
	}

	/**
	 * Delete items
	 *
	 * @param   mixed  $pks  id or array of ids of items to be deleted
	 *
	 * @return  boolean
	 */
	public function delete($pks = null)
	{
		// Initialise variables.
		$table = $this->getTable();
		$key = $table->getKeyName();

		// We are dealing with multiple primary keys
		if (is_array($key) && count($key) > 1)
		{
			$table->load($pks);
		}

		$table->delete($pks);

		return true;
	}

	/**
	 * Publish/Unpublish items
	 *
	 * @param   mixed    $pks    id or array of ids of items to be published/unpublished
	 * @param   integer  $state  New desired state
	 *
	 * @return  boolean
	 */
	public function publish($pks = null, $state = 1)
	{
		// Initialise variables.
		$table = $this->getTable();
		$table->publish($pks, $state);

		return true;
	}
}

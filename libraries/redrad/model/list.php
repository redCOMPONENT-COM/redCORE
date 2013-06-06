<?php
/**
 * @package     RedRad
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_REDRAD') or die;

/**
 * redRAD Base Model List
 *
 * @package     RedRad
 * @subpackage  Base
 * @since       1.0
 */
abstract class RModelList extends JModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = null;

	/**
	 * Array of form objects.
	 *
	 * @var  JForm[]
	 */
	protected $forms = array();

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
		$table->delete($pks);

		return true;
	}

	/**
	 * Get the zone form
	 *
	 * @param   array    $data      data
	 * @param   boolean  $loadData  load current data
	 *
	 * @return  JForm/false  the JForm object or false
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = null;

		if (!empty($this->filterFormName))
		{
			// Get the form.
			$form = $this->loadForm(
				$this->context . '.filter',
				$this->filterFormName,
				array('control' => '', 'load_data' => $loadData)
			);
		}

		return $form;
	}

	/**
	 * Method to get a form object.
	 *
	 * @param   string   $name     The name of the form.
	 * @param   string   $source   The form source. Can be XML string if file flag is set to false.
	 * @param   array    $options  Optional array of options for the form creation.
	 * @param   boolean  $clear    Optional argument to force load a new form.
	 * @param   mixed    $xpath    An optional xpath to search for the fields.
	 *
	 * @return  mixed  JForm object on success, False on error.
	 *
	 * @see     JForm
	 */
	protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = false)
	{
		// Handle the optional arguments.
		$options['control'] = JArrayHelper::getValue($options, 'control', false);

		// Create a signature hash.
		$hash = md5($source . serialize($options));

		// Check if we can use a previously loaded form.
		if (isset($this->forms[$hash]) && !$clear)
		{
			return $this->forms[$hash];
		}

		// Get the form.
		JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');

		try
		{
			$form = RForm::getInstance($name, $source, $options, false, $xpath);

			if (isset($options['load_data']) && $options['load_data'])
			{
				// Get the data for the form.
				$data = $this->loadFormData();
			}
			else
			{
				$data = array();
			}

			// Allow for additional modification of the form, and events to be triggered.
			// We pass the data because plugins may require it.
			$this->preprocessForm($form, $data);

			// Load the data into the form after the plugins have operated.
			$form->bind($data);

		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// Store the form for later.
		$this->forms[$hash] = $form;

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState($this->context, array());

		return $data;
	}

	/**
	 * Method to allow derived classes to preprocess the form.
	 *
	 * @param   JForm   $form   A JForm object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @see     JFormField
	 * @throws  Exception if there is an error in the form event.
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		// Import the appropriate plugin group.
		JPluginHelper::importPlugin($group);

		// Get the dispatcher.
		$dispatcher = JDispatcher::getInstance();

		// Trigger the form preparation event.
		$results = $dispatcher->trigger('onContentPrepareForm', array($form, $data));

		// Check for errors encountered while preparing the form.
		if (count($results) && in_array(false, $results, true))
		{
			// Get the last error.
			$error = $dispatcher->getError();

			if (!($error instanceof Exception))
			{
				throw new Exception($error);
			}
		}
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

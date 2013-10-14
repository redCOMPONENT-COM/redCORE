<?php
/**
 * @package     Redcore
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

JLoader::import('joomla.application.component.modellist');

/**
 * redCORE Base Model List
 *
 * @package     Redcore
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
	 * Associated HTML form
	 *
	 * @var  string
	 */
	protected $htmlFormName = 'adminForm';

	/**
	 * Array of form objects.
	 *
	 * @var  JForm[]
	 */
	protected $forms = array();

	/**
	 * A prefix for pagination request variables.
	 *
	 * @var  string
	 */
	protected $paginationPrefix = '';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JModelLegacy
	 */
	public function __construct($config = array())
	{
		$input = JFactory::getApplication()->input;
		$view = $input->getString('view', '');
		$option = $input->getString('option', '');

		// Different context depending on the view
		if (empty($this->context))
		{
			$this->context = strtolower($option . '.' . $view . '.' . $this->getName());
		}

		// Different pagination depending on the view
		if (empty($this->paginationPrefix))
		{
			$this->paginationPrefix = strtolower($option . '_' . $view . '_' . $this->getName() . '_');
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
	 * Function to get the active filters
	 *
	 * @return  array  Associative array in the format: array('filter_published' => 0)
	 *
	 * @since   3.2
	 */
	public function getActiveFilters()
	{
		$activeFilters = array();

		if (!empty($this->filter_fields))
		{
			foreach ($this->filter_fields as $filter)
			{
				$filterName = 'filter.' . $filter;

				if (property_exists($this->state, $filterName) && (!empty($this->state->{$filterName}) || is_numeric($this->state->{$filterName})))
				{
					$activeFilters[$filter] = $this->state->get($filterName);
				}
			}
		}

		return $activeFilters;
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
	 * Method to get the associated form name
	 *
	 * @return  string  The name of the form
	 */
	public function getHtmlFormName()
	{
		return $this->htmlFormName;
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  JPagination  A JPagination object for the data set.
	 */
	public function getPagination()
	{
		// Get a storage key.
		$store = $this->getStoreId('getPagination');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Create the pagination object.
		$limit = (int) $this->getState('list.limit') - (int) $this->getState('list.links');
		$page = new RPagination($this->getTotal(), $this->getStart(), $limit, $this->paginationPrefix);

		// Set the name of the HTML form associated
		$page->set('formName', $this->htmlFormName);

		// Add the object to the internal cache.
		$this->cache[$store] = $page;

		return $this->cache[$store];
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// If the context is set, assume that stateful lists are used.
		if ($this->context)
		{
			$app = JFactory::getApplication();

			// Pre-fill the limit
			$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'uint');
			$this->setState('list.limit', $limit);

			// Receive & set filters
			if ($filters = $this->getUserStateFromRequest($this->context . '.filter', 'filter'))
			{
				foreach ($filters as $name => $value)
				{
					$this->setState('filter.' . $name, $value);
				}
			}

			// Receive & set list options
			if ($list = $this->getUserStateFromRequest($this->context . '.list', 'list'))
			{
				foreach ($list as $name => $value)
				{
					// Extra validations
					switch ($name)
					{
						case 'fullordering':
							$orderingParts = explode(' ', $value);

							if (count($orderingParts) >= 2)
							{
								// Latest part will be considered the direction
								$fullDirection = end($orderingParts);

								if (in_array(strtoupper($fullDirection), array('ASC', 'DESC', '')))
								{
									$this->setState('list.direction', $fullDirection);
								}

								unset($orderingParts[count($orderingParts) - 1]);

								// The rest will be the ordering
								$fullOrdering = implode(' ', $orderingParts);

								if (in_array($fullOrdering, $this->filter_fields))
								{
									$this->setState('list.ordering', $fullOrdering);
								}
							}
							else
							{
								$this->setState('list.ordering', $ordering);
								$this->setState('list.direction', $direction);
							}
							break;

						case 'ordering':
							if (!in_array($value, $this->filter_fields))
							{
								$value = $ordering;
							}
							break;

						case 'direction':
							if (!in_array(strtoupper($value), array('ASC', 'DESC', '')))
							{
								$value = $direction;
							}
							break;

						case 'start':
							$value = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
							break;

						// Just to keep the default case
						default:
							$value = $value;
							break;
					}

					$this->setState('list.' . $name, $value);
				}
			}
			else
			// Keep B/C
			{
				$value = $app->getUserStateFromRequest($this->context . '.limit', $this->paginationPrefix . 'limit', $app->getCfg('list_limit'), 'uint');
				$limit = $value;
				$this->setState('list.limit', $limit);

				$value = $app->getUserStateFromRequest($this->context . '.limitstart', $this->paginationPrefix . 'limitstart', 0);
				$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
				$this->setState('list.start', $limitstart);

				// Check if the ordering field is in the white list, otherwise use the incoming value.
				$value = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', $ordering);

				if (!in_array($value, $this->filter_fields))
				{
					$value = $ordering;
					$app->setUserState($this->context . '.ordercol', $value);
				}

				$this->setState('list.ordering', $value);

				// Check if the ordering direction is valid, otherwise use the incoming value.
				$value = $app->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', $direction);

				if (!in_array(strtoupper($value), array('ASC', 'DESC', '')))
				{
					$value = $direction;
					$app->setUserState($this->context . '.orderdirn', $value);
				}

				$this->setState('list.direction', $value);
			}
		}
		else
		{
			$this->setState('list.start', 0);
			$this->setState('list.limit', 0);
		}
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
		RForm::addFormPath(JPATH_COMPONENT . '/models/forms');
		RForm::addFieldPath(JPATH_COMPONENT . '/models/fields');

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
	 * @throws  Exception if there is an error in the form event.
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		// Import the appropriate plugin group.
		JPluginHelper::importPlugin($group);

		// Get the dispatcher.
		$dispatcher = RFactory::getDispatcher();

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
}

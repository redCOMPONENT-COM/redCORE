<?php
/**
 * @package     RedRad
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDRAD') or die;

JLoader::import('joomla.application.component.modellist');

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
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JModelLegacy
	 */
	public function __construct($config = array())
	{
		if (empty($this->context))
		{
			// Different context depending on the view
			$input = JFactory::getApplication()->input;
			$view = $input->getString('view', '');
			$option = $input->getString('option', '');
			$this->context = strtolower($option . '.' . $view . '.' . $this->getName());
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
	 *
	 * @since   12.2
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
		$page = new RPagination($this->getTotal(), $this->getStart(), $limit);

		// Set the name of the HTML form associated
		$page->set('formName', $this->htmlFormName);

		// Add the object to the internal cache.
		$this->cache[$store] = $page;

		return $this->cache[$store];
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

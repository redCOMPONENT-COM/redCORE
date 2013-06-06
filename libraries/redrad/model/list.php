<?php
/**
 * @package     RedRad
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
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
	 * @return JForm/false  the JForm object or false
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = null;

		if (!empty($this->filterFormName))
		{
			// Get the form.
			$form = $this->loadForm($this->context . '.filter', $this->filterFormName, array('control' => '', 'load_data' => $loadData));
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
	 * @param   string   $xpath    An optional xpath to search for the fields.
	 *
	 * @return  mixed  JForm object on success, False on error.
	 *
	 * @see     JForm
	 * @since   11.1
	 */
	protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = false)
	{
		// Handle the optional arguments.
		$options['control'] = JArrayHelper::getValue($options, 'control', false);

		// Create a signature hash.
		$hash = md5($source . serialize($options));

		// Check if we can use a previously loaded form.
		if (isset($this->_forms[$hash]) && !$clear)
		{
			return $this->_forms[$hash];
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
		$this->_forms[$hash] = $form;

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 *
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState($this->context, array());

		return $data;
	}

	/**
	 * Function to convert a string or array into a single string
	 *
	 * @param   mixed   $values        Array or string to use as values
	 * @param   string  $filter        Filter to apply to the values to quote or (int) them
	 * @param   array   $removeValues  Items to remove/filter from the source array
	 *
	 * @return  string
	 */
	protected function multipleSanitised($values, $filter = 'integer', $removeValues = array(''))
	{
		$db = JFactory::getDbo();

		// Extra verification to avoid null values
		if (is_null($values))
		{
			return false;
		}

		if (!is_array($values))
		{
			// Convert comma separated values to arrays
			$values = (array) explode(',', $values);
		}

		// If all is selected remove filter
		if (in_array('*', $values))
		{
			return null;
		}

		// Remove undesired source values
		if (!empty($removeValues))
		{
			$values = array_diff($values, $removeValues);
		}

		// Filter to sanitise data
		switch ($filter)
		{
			case 'integer':
				JArrayHelper::toInteger($values);
				break;
			default:
				$values = array_map(array($db, 'quote'), $values);
				break;
		}

		return $values;
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
	 * @since   11.1
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
}

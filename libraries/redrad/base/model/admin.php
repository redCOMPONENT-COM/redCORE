<?php
/**
 * @package     RedRad
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_REDRAD') or die;

JLoader::import('joomla.application.component.modeladmin');

/**
 * redRAD Base Model Admin
 *
 * @package     RedRad
 * @subpackage  Base
 * @since       1.0
 */
class RBaseModelAdmin extends JModelAdmin
{
	/**
	 * Component name
	 *
	 * @var  string
	 */
	protected $_component = null;

	/**
	 * Table Name
	 *
	 * @var  string
	 */
	protected $_tableType = null;

	/**
	 * Table Prefix
	 *
	 * @var  string
	 */
	protected $_tablePrefix = null;

	/**
	 * Context
	 *
	 * @var  string
	 */
	protected $_context = null;

	/**
	 * Constructor
	 *
	 * @param   array  $config  Configuration array
	 */
	public function __construct($config = array())
	{
		// Autogenerate context ?
		if (is_null($this->_context))
		{
			$this->_context = $this->_component . '.' . $this->_tableType;
		}

		if (is_null($this->_tablePrefix) || is_null($this->_component) || is_null($this->_tableType))
		{
			throw new InvalidArgumentException('Missing required prefix, component or table type in ' . __CLASS__ . ' definition');
		}

		parent::__construct($config);
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
		// Get the form.
		$form = $this->loadForm($this->_context, $this->_tableType, array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Get the associated JTable
	 *
	 * @param   string  $type    Table name
	 * @param   string  $prefix  Table prefix
	 * @param   array   $config  Configuration array
	 *
	 * @return JTable
	 */
	public function getTable($type = 'none', $prefix = 'RTable', $config = array())
	{
		return JTable::getInstance($this->_tableType, $this->_tablePrefix, $config);
	}

	/**
	 * Load the form data from session / table
	 *
	 * @return object  The data
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState($this->_component . '.edit.' . $this->_tableType . '.data', array());

		// If no session data try to load item
		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		return parent::getItem($pk);
	}
}

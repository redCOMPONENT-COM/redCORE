<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * redCORE Dynamic Model List
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.3
 */
class RApiHalModelItem extends RModelAdmin
{
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

		$this->modelConfig = $config;

		parent::__construct($config);
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
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		return $item;
	}

	/**
	 * Method to validate the form data.
	 * Each field error is stored in session and can be retrieved with getFieldError().
	 * Once getFieldError() is called, the error is deleted from the session.
	 *
	 * @param   JForm   $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  mixed  Array of filtered data if valid, false otherwise.
	 */
	public function validate($form, $data, $group = null)
	{
		// @todo We should integrate custom validation here as well
		return $data;
	}

	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// We are using just validate function without the form so we can use true to pass through initial form exist check
		return true;
	}

	/**
	 * Stock method to auto-populate the model state.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function populateState()
	{
		$this->setState($this->getName() . '.id', 0);
	}
}

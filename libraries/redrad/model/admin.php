<?php
/**
 * @package     RedRad
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_REDRAD') or die;

/**
 * redRAD Base Model Admin
 *
 * @package     RedRad
 * @subpackage  Model
 * @since       1.0
 */
class RModelAdmin extends JModelAdmin
{
	/**
	 * The form name.
	 *
	 * @var string
	 */
	protected $formName = null;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  Configuration array
	 *
	 * @throws  RuntimeException
	 */
	public function __construct($config = array())
	{
		if (is_null($this->context))
		{
			$this->context = strtolower($this->option . '.' . $this->getName());
		}

		if (is_null($this->formName))
		{
			throw new RuntimeException(
				sprintf(
					'The form name is missing in model %s',
					get_class($this)
				)
			);
		}

		parent::__construct($config);
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
		// Get the form.
		$form = $this->loadForm(
			$this->context . '.' . $this->formName, $this->formName,
			array(
				'control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The default data is an empty array.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState(
			$this->context . '.edit.' . $this->formName . '.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * Fields recognized :
	 *
	 * - created_date
	 * - modified_date
	 * - created_by
	 * - modified_by
	 *
	 * @param   JTable  $table  A reference to a JTable object.
	 *
	 * @return  void
	 */
	protected function prepareTable($table)
	{
		$now = JDate::getInstance();
		$nowFormatted = $now->toSql();

		if (property_exists($table, 'created_date')
			&& (is_null($table->created_date) || empty($table->created_date)))
		{
			$table->created_date = $nowFormatted;
		}

		if (property_exists($table, 'modified_date'))
		{
			$table->modified_date = $nowFormatted;
		}

		$userId = JFactory::getUser()->id;

		if (property_exists('created_by', $table)
			&& (is_null($table->created_by) || empty($table->created_by)))
		{
			$table->created_by = $userId;
		}

		if (property_exists($table, 'modified_by'))
		{
			$table->modified_by = $userId;
		}
	}
}

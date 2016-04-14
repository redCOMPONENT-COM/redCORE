<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Tables
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Translation Column table.
 *
 * @package     Redcore.Backend
 * @subpackage  Tables
 * @since       1.8
 */
class RedcoreTableTranslation_Column extends RTable
{
	/**
	 * @var  int
	 */
	public $id;

	/**
	 * @var  int
	 */
	public $translation_table_id;

	/**
	 * @var  string
	 */
	public $name;

	/**
	 * @var  string
	 */
	public $title;

	/**
	 * @var  string
	 */
	public $column_type = 'translate';

	/**
	 * @var  string
	 */
	public $value_type = 'text';

	/**
	 * @var  bool
	 */
	public $fallback = 0;

	/**
	 * @var  string
	 */
	public $filter = 'RAW';

	/**
	 * @var  string
	 */
	public $description;

	/**
	 * @var string
	 */
	public $params;

	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 *
	 * @throws  UnexpectedValueException
	 */
	public function __construct(&$db)
	{
		$this->_tableName = 'redcore_translation_columns';
		$this->_tbl_key = 'id';

		parent::__construct($db);
	}

	/**
	 * Checks that the object is valid and able to be stored.
	 *
	 * This method checks that the parent_id is non-zero and exists in the database.
	 * Note that the root node (parent_id = 0) cannot be manipulated with this class.
	 *
	 * @return  boolean  True if all checks pass.
	 */
	public function check()
	{
		// Check if client is not already created with this id.
		$client = clone $this;

		$this->name = trim($this->name);

		if (empty($this->name))
		{
			$this->setError(JText::_('COM_REDCORE_TRANSLATION_COLUMN_NAME_FIELD_CANNOT_BE_EMPTY'));

			return false;
		}

		if ($client->load(array('name' => $this->name, 'translation_table_id' => $this->translation_table_id)) && $client->id != $this->id)
		{
			$this->setError(JText::_('COM_REDCORE_TRANSLATION_COLUMN_NAME_ALREADY_EXISTS'));

			return false;
		}

		if (empty($this->title))
		{
			$this->title = $this->name;
		}

		return true;
	}

	/**
	 * Method to store a node in the database table.
	 *
	 * @param   boolean  $updateNulls  True to update null values as well.
	 *
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = true)
	{
		if (!parent::store($updateNulls))
		{
			return false;
		}

		return true;
	}
}

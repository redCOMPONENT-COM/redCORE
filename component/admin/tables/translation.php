<?php
/**
 * @package     Redshopb.Backend
 * @subpackage  Tables
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Category table.
 *
 * @package     Redshopb.Backend
 * @subpackage  Tables
 * @since       1.0
 */
class RedcoreTableTranslation extends RTable
{
	/**
	 * The table name without the prefix.
	 *
	 * @var  string
	 */
	protected $_tableName = 'extension';

	/**
	 * The table primary key
	 *
	 * @var  string
	 */
	protected $_tbl_key = 'rctranslations_id';

	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 *
	 * @throws  UnexpectedValueException
	 */
	public function __construct(&$db)
	{
		$table = RedcoreHelpersTranslation::getTranslationTable();

		$this->_tbl = RTranslationTable::getTranslationsTableName($table->table, '');
		$this->_tableName = str_replace('#__', '', $this->_tbl);

		if (empty($this->_tbl) || (empty($this->_tbl_key) && empty($this->_tbl_keys)))
		{
			throw new UnexpectedValueException(sprintf('Missing data to initialize %s table | id: %s', $this->_tbl, $this->_tbl_key));
		}

		parent::__construct($db);

		// Initialise custom fields
		$this->loadCustomFields();
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
		if (empty($this->rctranslations_language))
		{
			$this->setError(JText::_('COM_REDCORE_TRANSLATIONS_SELECT_LANGUAGE'));

			return false;
		}

		$this->rctranslations_modified = JFactory::getDate()->toSql();

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

	/**
	 * Method to load Fields dynamically for data bind
	 *
	 * @return  void
	 */
	public function loadCustomFields()
	{
		$db	= $this->getDbo();
		$fieldList = array();
		$query = 'SHOW COLUMNS FROM ' . $db->qn($this->_tbl);
		$db->setQuery($query);
		$fields = $db->loadObjectList();

		if (count($fields) > 0)
		{
			foreach ($fields as $field)
			{
				$fieldList[$field->Field] = $field->Default;
			}

			$this->setProperties($fieldList);
		}
	}
}

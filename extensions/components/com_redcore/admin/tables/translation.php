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
 * Translation table.
 *
 * @package     Redcore.Backend
 * @subpackage  Tables
 * @since       1.0
 */
class RedcoreTableTranslation extends RTable
{
	/**
	 * Field name to publish/unpublish/trash table registers. Ex: state
	 *
	 * @var  string
	 */
	protected $_tableFieldState = 'rctranslations_state';

	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 *
	 * @throws  UnexpectedValueException
	 */
	public function __construct(&$db)
	{
		$this->_tableName = 'extension';
		$this->_tbl_key = 'rctranslations_id';
		$app = JFactory::getApplication();
		$table = RTranslationTable::getTranslationTableByName($app->input->get('translationTableName', ''));

		$this->_tbl = RTranslationTable::getTranslationsTableName($table->name, '');
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
		$this->rctranslations_modified_by = JFactory::getUser()->get('id');

		return true;
	}

	/**
	 * Method to bind an associative array or object to the JTable instance.This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   mixed  $src     An associative array or object to bind to the JTable instance.
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  InvalidArgumentException
	 */
	public function bind($src, $ignore = array())
	{
		$bind = parent::bind($src, $ignore);

		// We set empty strings to null
		foreach ($this->getProperties() as $k => $v)
		{
			// Only process fields not in the ignore array.
			if (!in_array($k, $ignore))
			{
				if (isset($src[$k]))
				{
					if ($src[$k] == '')
					{
						$this->$k = null;
					}
				}
			}
		}

		return $bind;
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
		if (!parent::store(true))
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

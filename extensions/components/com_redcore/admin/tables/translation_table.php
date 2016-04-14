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
 * Translation table table.
 *
 * @package     Redcore.Backend
 * @subpackage  Tables
 * @since       1.8
 */
class RedcoreTableTranslation_Table extends RTable
{
	/**
	 * @var  int
	 */
	public $id;

	/**
	 * @var  string
	 */
	public $name;

	/**
	 * @var  string
	 */
	public $extension_name;

	/**
	 * @var  string
	 */
	public $title;

	/**
	 * @var  string
	 */
	public $version;

	/**
	 * @var  string
	 */
	public $primary_columns;

	/**
	 * @var  string
	 */
	public $translate_columns;

	/**
	 * @var  string
	 */
	public $fallback_columns;

	/**
	 * @var  string
	 */
	public $form_links;

	/**
	 * @var string
	 */
	public $xml_path;

	/**
	 * @var string
	 */
	public $xml_hashed;

	/**
	 * @var string
	 */
	public $filter_query;

	/**
	 * @var string
	 */
	public $params;

	/**
	 * @var int
	 */
	public $state;

	/**
	 * @var  string
	 */
	public $checked_out_time = '0000-00-00 00:00:00';

	/**
	 * @var  integer
	 */
	public $checked_out = null;

	/**
	 * @var  string
	 */
	public $created_date = '0000-00-00 00:00:00';

	/**
	 * @var  integer
	 */
	public $created_by = null;

	/**
	 * @var  string
	 */
	public $modified_date = '0000-00-00 00:00:00';

	/**
	 * @var  integer
	 */
	public $modified_by = null;

	/**
	 * @var  array
	 */
	protected $editFormLinks = array();

	/**
	 * @var  array
	 */
	protected $columns = array();

	/**
	 * @var  bool
	 */
	protected $fromEditForm = false;

	/**
	 * @var  bool
	 */
	protected $showNotifications = false;

	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 *
	 * @throws  UnexpectedValueException
	 */
	public function __construct(&$db)
	{
		$this->_tableName = 'redcore_translation_tables';
		$this->_tbl_key = 'id';

		parent::__construct($db);
	}

	/**
	 * Returns array of columns
	 *
	 * @return  array
	 */
	public function getTranslationColumns()
	{
		return $this->columns;
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
		$this->version = trim($this->version);
		$this->version = !empty($this->version) ? $this->version : '1.0.0';

		if (empty($this->name))
		{
			$this->setError(JText::_('COM_REDCORE_TRANSLATION_TABLE_NAME_FIELD_CANNOT_BE_EMPTY'));

			return false;
		}

		$this->name = '#__' . str_replace('#__', '', $this->name);

		if ($client->load(array('name' => $this->name)) && $client->id != $this->id)
		{
			$this->setError(JText::_('COM_REDCORE_TRANSLATION_TABLE_NAME_ALREADY_EXISTS'));

			return false;
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
		if ($this->fromEditForm)
		{
			$xml = RTranslationContentElement::generateTranslationXml($this);

			$dom = dom_import_simplexml($xml)->ownerDocument;
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$this->xml_hashed = md5($dom->saveXML());
		}

		// We will set XML file to the upload folder so this will be our priority content element
		if ($this->fromEditForm || empty($this->xml_path))
		{
			$this->xml_path = '/media/redcore/translations/upload/' . JPath::clean(str_replace('#__', '', $this->name) . '_' . $this->version . '.xml');
		}

		// Create new translation table
		if (!RTranslationTable::setTranslationTable($this, isset($this->showNotifications) ? $this->showNotifications : $this->fromEditForm))
		{
			return false;
		}

		// Store table information
		if (!parent::store($updateNulls))
		{
			return false;
		}

		// Store columns information
		if (!$this->storeColumns($this->columns))
		{
			return false;
		}

		if ($this->fromEditForm)
		{
			if (!$dom->save(JPath::clean(JPATH_SITE . '' . $this->xml_path)))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to store a node in the database table.
	 *
	 * @param   array  $columns  Columns to bind to the table
	 *
	 * @return  boolean  True on success.
	 */
	public function storeColumns($columns = array())
	{
		// Delete all items
		$db = $this->_db;
		$query = $db->getQuery(true)
			->delete('#__redcore_translation_columns')
			->where('translation_table_id = ' . $db->q($this->id));

		$db->setQuery($query);

		if (!$db->execute())
		{
			return false;
		}

		/** @var RedcoreTableTranslation_Column $xrefTable */
		$xrefTable = RTable::getAdminInstance('Translation_Column', array(), 'com_redcore');

		// Store new permissions if they exist
		if (is_array($columns) && count($columns) > 0)
		{
			// Store the new items
			foreach ($columns as $column)
			{
				$xrefTable->reset();
				$column['translation_table_id'] = $this->id;
				$column['id'] = 0;

				if (!$xrefTable->save($column))
				{
					$this->setError($xrefTable->getError());

					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Deletes this row in database (or if provided, the row of key $pk)
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 */
	public function delete($pk = null)
	{
		$db = $this->_db;
		$ids = $pk;

		// Initialise variables.
		$k = $this->_tbl_key;

		// Received an array of ids?
		if (is_array($ids))
		{
			// Sanitize input.
			$ids = Joomla\Utilities\ArrayHelper::toInteger($ids);
			$ids = RHelperArray::quote($ids);
			$ids = implode(',', $ids);
		}

		$ids = (is_null($ids)) ? $this->$k : $ids;

		// If no primary key is given, return false.
		if ($ids === null)
		{
			return false;
		}

		try
		{
			// Delete translation Table
			$newTable = RTranslationTable::getTranslationsTableName($this->name, '#__');
			RTranslationTable::removeExistingConstraintKeys($this->name);
			$db->dropTable($newTable);

			// Remove columns
			$query = $db->getQuery(true)
				->delete('#__redcore_translation_columns')
				->where('translation_table_id IN (' . $db->q($ids) . ')');

			$db->setQuery($query)
				->execute();

			// Delete this row
			return parent::delete($pk);
		}
		catch (Exception $e)
		{
			if ($e->getMessage())
			{
				$this->setError($e->getMessage());
			}

			return false;
		}
	}
}

<?php
/**
 * @package     Redcore
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2008 - 2021 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

JLoader::import('joomla.database.tablenested');

use Joomla\Utilities\ArrayHelper;

/**
 * redCORE Base Table
 *
 * @package     Redcore
 * @subpackage  Base
 * @since       1.0
 */
class RTableNested extends JTableNested
{
	/**
	 * The options.
	 *
	 * @var  array
	 */
	protected $_options = array();

	/**
	 * Prefix to add to log files
	 *
	 * @var  string
	 */
	protected $_logPrefix = 'redcore';

	/**
	 * The table name without the prefix. Ex: cursos_courses
	 *
	 * @var  string
	 */
	protected $_tableName = null;

	/**
	 * The table key column. Usually: id
	 *
	 * @var  string
	 */
	protected $_tableKey = 'id';

	/**
	 * Field name to publish/unpublish/trash table registers. Ex: state
	 *
	 * @var  string
	 */
	protected $_tableFieldState = 'state';

	/**
	 * Field name to keep creator user (created_by)
	 *
	 * @var  string
	 */
	protected $_tableFieldCreatedBy = 'created_by';

	/**
	 * Field name to keep latest modifier user (modified_by)
	 *
	 * @var  string
	 */
	protected $_tableFieldModifiedBy = 'modified_by';

	/**
	 * Field name to keep created date (created_date)
	 *
	 * @var  string
	 */
	protected $_tableFieldCreatedDate = 'created_date';

	/**
	 * Field name to keep latest modified user (modified_date)
	 *
	 * @var  string
	 */
	protected $_tableFieldModifiedDate = 'modified_date';

	/**
	 * Format for audit date fields (created_date, modified_date)
	 *
	 * @var  string
	 */
	protected $_auditDateFormat = 'Y-m-d H:i:s';

	/**
	 * An array of plugin types to import.
	 *
	 * @var  array
	 */
	protected $_pluginTypesToImport = array();

	/**
	 * Event name to trigger before load().
	 *
	 * @var  string
	 */
	protected $_eventBeforeLoad;

	/**
	 * Event name to trigger after load().
	 *
	 * @var  string
	 */
	protected $_eventAfterLoad;

	/**
	 * Event name to trigger before delete().
	 *
	 * @var  string
	 */
	protected $_eventBeforeDelete;

	/**
	 * Event name to trigger after delete().
	 *
	 * @var  string
	 */
	protected $_eventAfterDelete;

	/**
	 * Event name to trigger before check().
	 *
	 * @var  string
	 */
	protected $_eventBeforeCheck;

	/**
	 * Event name to trigger after check().
	 *
	 * @var  string
	 */
	protected $_eventAfterCheck;

	/**
	 * Event name to trigger before store().
	 *
	 * @var  string
	 */
	protected $_eventBeforeStore;

	/**
	 * Event name to trigger after store().
	 *
	 * @var  string
	 */
	protected $_eventAfterStore;

	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 *
	 * @throws  UnexpectedValueException
	 */
	public function __construct(&$db)
	{
		// Keep checking _tbl value for standard defined tables
		if (empty($this->_tbl) && !empty($this->_tableName))
		{
			// Add the table prefix
			$this->_tbl = '#__' . $this->_tableName;
		}

		$key = $this->_tbl_key;

		if (empty($key) && !empty($this->_tbl_keys))
		{
			$key = $this->_tbl_keys;
		}

		// Keep checking _tbl_key for standard defined tables
		if (empty($key) && !empty($this->_tableKey))
		{
			$this->_tbl_key = $this->_tableKey;
			$key            = $this->_tbl_key;
		}

		if (empty($this->_tbl) || empty($key))
		{
			throw new UnexpectedValueException(sprintf('Missing data to initialize %s table | id: %s', $this->_tbl, $key));
		}

		parent::__construct($this->_tbl, $key, $db);
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
		if (isset($src['params']) && is_array($src['params']))
		{
			$registry = new JRegistry;
			$registry->loadArray($src['params']);
			$src['params'] = (string) $registry;
		}

		if (isset($src['metadata']) && is_array($src['metadata']))
		{
			$registry = new JRegistry;
			$registry->loadArray($src['metadata']);
			$src['metadata'] = (string) $registry;
		}

		if (isset($src['rules']) && is_array($src['rules']))
		{
			$rules = new JAccessRules($src['rules']);
			$this->setRules($rules);
		}

		return parent::bind($src, $ignore);
	}

	/**
	 * Import the plugin types.
	 *
	 * @return  void
	 */
	private function importPluginTypes()
	{
		foreach ($this->_pluginTypesToImport as $type)
		{
			JPluginHelper::importPlugin($type);
		}
	}

	/**
	 * Called before load().
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 */
	protected function beforeLoad($keys = null, $reset = true)
	{
		if ($this->_eventBeforeLoad)
		{
			// Import the plugin types
			$this->importPluginTypes();

			// Trigger the event
			$results = RFactory::getDispatcher()
				->trigger($this->_eventBeforeLoad, array($this, $keys, $reset));

			if (count($results) && in_array(false, $results, true))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Called after load().
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 */
	protected function afterLoad($keys = null, $reset = true)
	{
		if ($this->_eventAfterLoad)
		{
			// Import the plugin types
			$this->importPluginTypes();

			// Trigger the event
			$results = RFactory::getDispatcher()
				->trigger($this->_eventAfterLoad, array($this, $keys, $reset));

			if (count($results) && in_array(false, $results, true))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to load a row from the database by primary key and bind the fields
	 * to the JTable instance properties.
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 */
	public function load($keys = null, $reset = true)
	{
		// Before load
		if (!$this->beforeLoad($keys, $reset))
		{
			return false;
		}

		// Load
		if (!parent::load($keys, $reset))
		{
			return false;
		}

		// After load
		if (!$this->afterLoad($keys, $reset))
		{
			return false;
		}

		return true;
	}

	/**
	 * Called before delete().
	 *
	 * @param   integer  $pk        The primary key of the node to delete.
	 * @param   boolean  $children  True to delete child nodes, false to move them up a level.
	 *
	 * @return  boolean  True on success.
	 */
	protected function beforeDelete($pk = null, $children = true)
	{
		if ($this->_eventBeforeDelete)
		{
			// Import the plugin types
			$this->importPluginTypes();

			// Trigger the event
			$results = RFactory::getDispatcher()
				->trigger($this->_eventBeforeDelete, array($this, $pk, $children));

			if (count($results) && in_array(false, $results, true))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Called after delete().
	 *
	 * @param   integer  $pk        The primary key of the node to delete.
	 * @param   boolean  $children  True to delete child nodes, false to move them up a level.
	 *
	 * @return  boolean  True on success.
	 */
	protected function afterDelete($pk = null, $children = true)
	{
		// Trigger after delete
		if ($this->_eventAfterDelete)
		{
			// Import the plugin types
			$this->importPluginTypes();

			// Trigger the event
			$results = RFactory::getDispatcher()
				->trigger($this->_eventAfterDelete, array($this, $pk, $children));

			if (count($results) && in_array(false, $results, true))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to delete a node and, optionally, its child nodes from the table.
	 *
	 * @param   integer  $pk        The primary key of the node to delete.
	 * @param   boolean  $children  True to delete child nodes, false to move them up a level.
	 *
	 * @return  boolean  True on success.
	 */
	public function delete($pk = null, $children = true)
	{
		// Before delete
		if (!$this->beforeDelete($pk, $children))
		{
			return false;
		}

		// Delete
		if (!parent::delete($pk, $children))
		{
			return false;
		}

		// After delete
		if (!$this->afterDelete($pk, $children))
		{
			return false;
		}

		return true;
	}

	/**
	 * Called before check().
	 *
	 * @return  boolean  True if all checks pass.
	 */
	protected function beforeCheck()
	{
		if ($this->_eventBeforeCheck)
		{
			// Import the plugin types
			$this->importPluginTypes();

			// Trigger the event
			$results = RFactory::getDispatcher()
				->trigger($this->_eventBeforeCheck, array($this));

			if (count($results) && in_array(false, $results, true))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Called after check().
	 *
	 * @return  boolean  True if all checks pass.
	 */
	protected function afterCheck()
	{
		// Trigger after check
		if ($this->_eventAfterCheck)
		{
			// Import the plugin types
			$this->importPluginTypes();

			// Trigger the event
			$results = RFactory::getDispatcher()
				->trigger($this->_eventAfterCheck, array($this));

			if (count($results) && in_array(false, $results, true))
			{
				return false;
			}
		}

		return true;
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
		// Before check
		if (!$this->beforeCheck())
		{
			return false;
		}

		// Check
		if (!parent::check())
		{
			return false;
		}

		// After check
		if (!$this->afterCheck())
		{
			return false;
		}

		return true;
	}

	/**
	 * Called before store().
	 *
	 * @param   boolean  $updateNulls  True to update null values as well.
	 *
	 * @return  boolean  True on success.
	 */
	protected function beforeStore($updateNulls = false)
	{
		if ($this->_eventBeforeStore)
		{
			// Import the plugin types
			$this->importPluginTypes();

			// Trigger the event
			$results = RFactory::getDispatcher()
				->trigger($this->_eventBeforeStore, array($this, $updateNulls));

			if (count($results) && in_array(false, $results, true))
			{
				return false;
			}
		}

		// Audit fields optional auto-update (on by default)
		if ($this->getOption('updateAuditFields', true))
		{
			RTable::updateAuditFields($this);
		}

		return true;
	}

	/**
	 * Called after store().
	 *
	 * @param   boolean  $updateNulls  True to update null values as well.
	 *
	 * @return  boolean  True on success.
	 */
	protected function afterStore($updateNulls = false)
	{
		if ($this->_eventAfterStore)
		{
			// Import the plugin types
			$this->importPluginTypes();

			// Trigger the event
			$results = RFactory::getDispatcher()
				->trigger($this->_eventAfterStore, array($this, $updateNulls));

			if (count($results) && in_array(false, $results, true))
			{
				return false;
			}
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
	public function store($updateNulls = false)
	{
		// Before store
		if (!$this->beforeStore($updateNulls))
		{
			return false;
		}

		// Store
		if (!parent::store($updateNulls))
		{
			return false;
		}

		// After store
		if (!$this->afterStore($updateNulls))
		{
			return false;
		}

		return true;
	}

	/**
	 * Override the parent checkin method to set checked_out = null instead of 0 so the foreign key doesn't fail.
	 *
	 * @param   mixed  $pk  An optional primary key value to check out.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  UnexpectedValueException
	 */
	public function checkIn($pk = null)
	{
		// If there is no checked_out or checked_out_time field, just return true.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
		{
			return true;
		}

		$k  = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// If no primary key is given, return false.
		if ($pk === null)
		{
			throw new UnexpectedValueException('Null primary key not allowed.');
		}

		// Check the row in by primary key.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set($this->_db->quoteName('checked_out') . ' = NULL');
		$query->set($this->_db->quoteName('checked_out_time') . ' = ' . $this->_db->quote($this->_db->getNullDate()));
		$query->where($this->_tbl_key . ' = ' . $this->_db->quote($pk));
		$this->_db->setQuery($query);

		// Check for a database error.
		$this->_db->execute();

		// Set table values in the object.
		$this->checked_out      = null;
		$this->checked_out_time = '';

		return true;
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table.  The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update.
	 *                            If not set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success; false if $pks is empty.
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		// Use an easy to handle variable for database
		$db = $this->_db;

		// Initialise variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		ArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = array($this->$k);
			}

			// Nothing to set publishing state on, return false.
			else
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));

				return false;
			}
		}

		// Build the main update query
		$query = $db->getQuery(true)
			->update($db->quoteName($this->_tbl))
			->set($db->quoteName($this->_tableFieldState) . ' = ' . (int) $state)
			->where($db->quoteName($k) . '=' . implode(' OR ' . $db->quoteName($k) . '=', $pks));

		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
		{
			$checkin = false;
		}
		else
		{
			$query->where('(checked_out = 0 OR checked_out IS NULL OR checked_out = ' . (int) $userId . ')');
			$checkin = true;
		}

		// Update the publishing state for rows with the given primary keys.
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
			JLog::add(JText::sprintf('REDCORE_ERROR_QUERY', $db->dump()), JLog::ERROR, $this->_logPrefix . 'Queries');

			return false;
		}

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
		{
			// Checkin the rows.
			foreach ($pks as $pk)
			{
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks))
		{
			$this->{$this->_tableFieldState} = $state;
		}

		$this->setError('');

		return true;
	}

	/**
	 * Set a table option value.
	 *
	 * @param   string  $key  The key
	 * @param   mixed   $val  The default value
	 *
	 * @return  JTable
	 */
	public function setOption($key, $val)
	{
		$this->_options[$key] = $val;

		return $this;
	}

	/**
	 * Get a table option value.
	 *
	 * @param   string  $key      The key
	 * @param   mixed   $default  The default value
	 *
	 * @return  mixed  The value or the default value
	 */
	public function getOption($key, $default = null)
	{
		if (isset($this->_options[$key]))
		{
			return $this->_options[$key];
		}

		return $default;
	}

	/**
	 * Validate that the primary key has been set.
	 *
	 * @return  boolean  True if the primary key(s) have been set.
	 *
	 * @since   1.5.2
	 */
	public function hasPrimaryKey()
	{
		// For Joomla 3.2+ a native method has been provided
		if (method_exists(get_parent_class(), 'hasPrimaryKey'))
		{
			return parent::hasPrimaryKey();
		}

		// Otherwise, it checks if the only key field compatible for older Joomla versions is set or not
		if (isset($this->_tbl_key) && !empty($this->_tbl_key) && empty($this->{$this->_tbl_key}))
		{
			return false;
		}

		return true;
	}

	/**
	 * Get the columns from database table.
	 *
	 * @param   bool  $reload  flag to reload cache
	 *
	 * @return  mixed  An array of the field names, or false if an error occurs.
	 *
	 * @since   11.1
	 * @throws  UnexpectedValueException
	 */
	public function getFields($reload = false)
	{
		static $cache = null;

		if ($cache !== null && !$reload)
		{
			return $cache;
		}

		$dbo = $this->getDbo();

		$query = $dbo->getQuery(true);

		$query->select('*');
		$query->from('#__redcore_schemas');

		$assetName = $this->_tbl;
		$query->where('asset_id = ' . $dbo->q($assetName));
		$result = $dbo->setQuery($query)->loadAssoc();

		if (is_null($result))
		{
			$result = $this->createSchema($assetName);
		}

		$cachedOn = new \JDate($result['cached_on']);
		$now      = new \JDate;

		if ($now->toUnix() > ($cachedOn->toUnix() + 86400))
		{
			$this->updateSchema($assetName, $now);
		}

		// Decode the fields
		$fields = (array) json_decode($result['fields']);

		if (empty($fields))
		{
			$msg = JText::sprintf('REDCORE_TABLE_ERROR_NO_COLUMNS_FOUND', $this->_tbl);

			throw new UnexpectedValueException($msg, 500);
		}

		$cache = $fields;

		return $cache;
	}

	/**
	 * Method to cache the table schema in the logical schemas table
	 *
	 * @param   string  $assetName  the asset name of this table. standard format is "com_componentName.TableName"
	 *
	 * @return array
	 */
	private function createSchema($assetName)
	{
		$dbo   = $this->getDbo();
		$query = $dbo->getQuery(true);

		$query->insert('#__redcore_schemas');
		$query->set('asset_id = ' . $dbo->q($assetName));

		$fields = json_encode($dbo->getTableColumns($this->_tbl, false));
		$query->set('fields = ' . $dbo->q($fields));

		$now = new \JDate;
		$query->set('cached_on = ' . $dbo->q($now->toSql()));

		$dbo->setQuery($query)->execute();

		return array('asset_id' => $assetName, 'fields' => $fields, 'cached_on' => $now->toSql());
	}

	/**
	 * Method to update the table schema in the logical schemas table
	 *
	 * @param   string  $assetName  the asset name of this table. standard format is "com_componentName.TableName"
	 * @param   \JDate  $now        the current time
	 *
	 * @return array
	 */
	private function updateSchema($assetName, \JDate $now)
	{
		$dbo   = $this->getDbo();
		$query = $dbo->getQuery(true);

		$query->update('#__redcore_schemas');

		$fields = json_encode($dbo->getTableColumns($this->_tbl, false));
		$query->set('fields = ' . $dbo->q($fields));
		$query->set('cached_on = ' . $dbo->q($now->toSql()));

		$query->where('asset_id = ' . $dbo->q($assetName));

		$dbo->setQuery($query)->execute();

		return array('asset_id' => $assetName, 'fields' => $fields, 'cached_on' => $now->toSql());
	}
}

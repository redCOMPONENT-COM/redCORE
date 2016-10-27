<?php
/**
 * @package     Redcore.Library
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2012 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Redcore Table table trait
 *
 * @package     Redcore.Library
 * @subpackage  Table
 * @since       1.0
 */
trait RTableTraitTable
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
	 * Convert a date to UTC based on the user timezone offset or on the server timezone offset.
	 *
	 * @var string
	 */
	protected $_dateConversion = 'USER_UTC';

	/**
	 * Event name to trigger before store().
	 *
	 * @var  string
	 */
	protected $_eventBeforeStore;

	/**
	 * The table key column. Usually: id
	 *
	 * @var  string
	 */
	protected $_tableKey = 'id';

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
	 * An array of plugin types to import.
	 *
	 * @var  array
	 */
	protected $_pluginTypesToImport = array();

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
	 * Event name to trigger after store().
	 *
	 * @var  string
	 */
	protected $_eventAfterStore;

	/**
	 * Get a table instance.
	 *
	 * @param   string  $name    The table name
	 * @param   mixed   $client  The client. null = auto, 1 = admin, 0 = frontend
	 * @param   array   $config  An optional array of configuration
	 * @param   string  $option  Component name, use for call table from another extension
	 *
	 * @return  RTable  The table
	 *
	 * @throws  InvalidArgumentException
	 */
	public static function getAutoInstance($name, $client = null, array $config = array(), $option = 'auto')
	{
		if ($option === 'auto')
		{
			$option = JFactory::getApplication()->input->getString('option', '');

			// Add com_ to the element name if not exist
			$option = (strpos($option, 'com_') === 0 ? '' : 'com_') . $option;

			if ($option == 'com_installer')
			{
				$installer = JInstaller::getInstance();
				$option = $installer->manifestClass->getElement($installer);
			}
		}

		$componentName = ucfirst(strtolower(substr($option, 4)));
		$prefix = $componentName . 'Table';

		if (is_null($client))
		{
			$client = (int) JFactory::getApplication()->isAdmin();
		}

		// Admin
		if ($client === 1)
		{
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/' . $option . '/tables');
		}

		// Site
		elseif ($client === 0)
		{
			JTable::addIncludePath(JPATH_SITE . '/components/' . $option . '/tables');
		}

		else
		{
			throw new InvalidArgumentException(
				sprintf('Cannot instanciate the table %s in component %s. Invalid client %s.', $name, $option, $client)
			);
		}

		$table = self::getInstance($name, $prefix, $config);

		if (!$table instanceof JTable)
		{
			throw new InvalidArgumentException(
				sprintf('Cannot instanciate the table %s in component %s from client %s.', $name, $option, $client)
			);
		}

		return $table;
	}

	/**
	 * Get a backend table instance
	 *
	 * @param   string  $name    The table name
	 * @param   array   $config  An optional array of configuration
	 * @param   string  $option  Component name, use for call table from another extension
	 *
	 * @return  RTable  The table
	 */
	public static function getAdminInstance($name, array $config = array(), $option = 'auto')
	{
		return self::getAutoInstance($name, 1, $config, $option);
	}

	/**
	 * Get a frontend table instance
	 *
	 * @param   string  $name    The table name
	 * @param   array   $config  An optional array of configuration
	 * @param   string  $option  Component name, use for call table from another extension
	 *
	 * @return  RTable  The table
	 */
	public static function getFrontInstance($name, array $config = array(), $option = 'auto')
	{
		return self::getAutoInstance($name, 0, $config, $option);
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
	protected function importPluginTypes()
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
			$this->updateAuditFields();
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

		$k = $this->_tbl_key;
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
		$this->checked_out = null;
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
		JArrayHelper::toInteger($pks);
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
			$db->query();
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
	 * @return  RedcoreTableTraitTable
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
	 * Method to update audit fields using a static function
	 *
	 * @return  void
	 *
	 * @since   1.5.2
	 */
	public function updateAuditFields()
	{
		$user = JFactory::getUser();

		switch ($this->_dateConversion)
		{
			// Get the user timezone setting defaulting to the server timezone setting.
			case 'USER_UTC':
				$offset = $user->getParam('timezone', JFactory::getConfig()->get('offset'));
				break;

			// Get the server timezone setting.
			case 'SERVER_UTC':
				$offset = JFactory::getConfig()->get('offset');
				break;
			default:
				$offset = null;
				break;
		}

		// Optional created_by field updated when present
		if (!$this->hasPrimaryKey() && property_exists($this, $this->_tableFieldCreatedBy))
		{
			if ($user->id)
			{
				$this->{$this->_tableFieldCreatedBy} = $user->id;
			}
			else
			{
				$this->{$this->_tableFieldCreatedBy} = null;
			}
		}

		// Optional created_date field updated when present
		if (!$this->hasPrimaryKey() && property_exists($this, $this->_tableFieldCreatedDate))
		{
			$this->{$this->_tableFieldCreatedDate} = JFactory::getDate('now', $offset)->format($this->_auditDateFormat);
		}

		// Optional modified_by field updated when present
		if (property_exists($this, $this->_tableFieldModifiedBy))
		{
			if ($user->id)
			{
				$this->{$this->_tableFieldModifiedBy} = $user->id;
			}
			else
			{
				$this->{$this->_tableFieldModifiedBy} = null;
			}
		}

		// Optional modified_date field updated when present
		if (property_exists($this, $this->_tableFieldModifiedDate))
		{
			$this->{$this->_tableFieldModifiedDate} = JFactory::getDate('now', $offset)->format($this->_auditDateFormat);
		}
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
}

<?php
/**
 * @package     Redcore
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * redCORE Base Table
 *
 * @package     Redcore
 * @subpackage  Base
 * @since       1.0
 */
class RTable extends JTable
{
	use RTableTraitTable;

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
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
			$key = $this->_tbl_key;
		}

		if (empty($this->_tbl) || empty($key))
		{
			throw new UnexpectedValueException(sprintf('Missing data to initialize %s table | id: %s', $this->_tbl, $key));
		}

		parent::__construct($this->_tbl, $key, $db);
	}

	/**
	 * Called before delete().
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 */
	protected function beforeDelete($pk = null)
	{
		if ($this->_eventBeforeDelete)
		{
			// Import the plugin types
			$this->importPluginTypes();

			// Trigger the event
			$results = RFactory::getDispatcher()
				->trigger($this->_eventBeforeDelete, array($this, $pk));

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
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 */
	protected function afterDelete($pk = null)
	{
		// Trigger after delete
		if ($this->_eventAfterDelete)
		{
			// Import the plugin types
			$this->importPluginTypes();

			// Trigger the event
			$results = RFactory::getDispatcher()
				->trigger($this->_eventAfterDelete, array($this, $pk));

			if (count($results) && in_array(false, $results, true))
			{
				return false;
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
		// Before delete
		if (!$this->beforeDelete($pk))
		{
			return false;
		}

		// Delete
		if (!$this->doDelete($pk))
		{
			return false;
		}

		// After delete
		if (!$this->afterDelete($pk))
		{
			return false;
		}

		return true;
	}

	/**
	 * Delete one or more registers
	 *
	 * @param   string/array  $pk  Array of ids or ids comma separated
	 *
	 * @return  boolean  Deleted successfuly?
	 */
	protected function doDelete($pk = null)
	{
		// Initialise variables.
		$k = $this->_tbl_key;

		// Multiple keys
		$multiplePrimaryKeys = count($this->_tbl_keys) > 1;

		// We are dealing with multiple primary keys
		if ($multiplePrimaryKeys)
		{
			// Received an array of ids?
			if (is_null($pk))
			{
				$pk = array();

				foreach ($this->_tbl_keys AS $key)
				{
					$pk[$key] = $this->$key;
				}
			}
			elseif (is_array($pk))
			{
				$pk = array();

				foreach ($this->_tbl_keys AS $key)
				{
					$pk[$key] = !empty($pk[$key]) ? $pk[$key] : $this->$key;
				}
			}
		}
		// Standard Joomla delete method
		else
		{
			if (is_array($pk))
			{
				// Sanitize input.
				JArrayHelper::toInteger($pk);
				$pk = RHelperArray::quote($pk);
				$pk = implode(',', $pk);
				$multipleDelete = true;
			}
			// Try the instance property value
			elseif (empty($pk) && $this->{$k})
			{
				$pk = $this->{$k};
			}
		}

		// If no primary key is given, return false.
		if ($pk === null)
		{
			return false;
		}

		// Implement JObservableInterface: Pre-processing by observers
		$this->_observers->update('onBeforeDelete', array($pk));

		// Delete the row by primary key.
		$query = $this->_db->getQuery(true);
		$query->delete($this->_db->quoteName($this->_tbl));

		if ($multiplePrimaryKeys)
		{
			foreach ($this->_tbl_keys AS $k)
			{
				$query->where($this->_db->quoteName($k) . ' = ' . $this->_db->quote($pk[$k]));
			}
		}
		else
		{
			$query->where($this->_db->quoteName($this->_tbl_key) . ' IN (' . $pk . ')');
		}

		$this->_db->setQuery($query);
		$this->_db->execute();

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		// Implement JObservableInterface: Post-processing by observers
		$this->_observers->update('onAfterDelete', array($pk));

		return true;
	}
}

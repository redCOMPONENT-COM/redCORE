<?php
/**
 * @package     Redcore
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

JLoader::import('joomla.database.tablenested');

/**
 * redCORE Base Table
 *
 * @package     Redcore
 * @subpackage  Base
 * @since       1.0
 */
class RTableNested extends JTableNested
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
}

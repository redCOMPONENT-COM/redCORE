<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Tables
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * redCORE Dynamic Table
 *
 * @package     Redshopb.Backend
 * @subpackage  Tables
 * @since       1.0
 */
class RApiHalTableTable extends RTable
{
	/**
	 * Object constructor to set table and key fields.  In most cases this will
	 * be overridden by child classes to explicitly set the table and key fields
	 * for a particular database table.
	 *
	 * @param   string           $table  Name of the table to model.
	 * @param   mixed            $key    Name of the primary key field in the table or array of field names that compose the primary key.
	 * @param   JDatabaseDriver  $db     JDatabaseDriver object.
	 *
	 * @since   11.1
	 */
	public function __construct($table, $key, $db)
	{
		$this->_tableName = $table;

		if (is_array($key))
		{
			$this->_tbl_keys = $key;
			$this->_tbl_key = $key[key($key)];
		}
		else
		{
			$this->_tbl_key = $key;
			$this->_tableKey = $key;
		}

		// Set all columns from table as properties
		$columns = array();
		$dbColumns = $db->getTableColumns('#__' . $table, false);

		if (count($dbColumns) > 0)
		{
			foreach ($dbColumns as $columnKey => $columnValue)
			{
				$columns[$columnValue->Field] = $columnValue->Default;
			}

			$this->setProperties($columns);
		}

		parent::__construct($db);
	}
}

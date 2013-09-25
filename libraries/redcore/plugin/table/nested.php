<?php
/**
 * @package     Redcore
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * An example of plugin listening to RTableNested events.
 *
 * @package     Redcore
 * @subpackage  Plugin
 * @since       1.0
 */
class RPluginTableNested extends RPlugin
{
	/**
	 * Event triggered before load().
	 *
	 * @param   RTableNested  $table  The table.
	 * @param   mixed         $keys   An optional primary key value to load the row by, or an array of fields to match.
	 *                                If not set the instance property value is used.
	 * @param   boolean       $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  False on failure.
	 */
	public function onBeforeLoad(RTableNested $table, $keys = null, $reset = true)
	{
	}

	/**
	 * Event triggered after load().
	 *
	 * @param   RTableNested  $table  The table.
	 * @param   mixed         $keys   An optional primary key value to load the row by, or an array of fields to match.
	 *                                If not set the instance property value is used.
	 * @param   boolean       $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  False on failure.
	 */
	public function onAfterLoad(RTableNested $table, $keys = null, $reset = true)
	{
	}

	/**
	 * Event triggered before delete().
	 *
	 * @param   RTableNested  $table     The table.
	 * @param   mixed         $pk        An optional primary key value to delete.
	 *                                   If not set the instance property value is used.
	 * @param   boolean       $children  True to delete child nodes, false to move them up a level.
	 *
	 * @return  boolean  False on failure.
	 */
	public function onBeforeDelete(RTableNested $table, $pk = null, $children = true)
	{
	}

	/**
	 * Event triggered after delete().
	 *
	 * @param   RTableNested  $table     The table.
	 * @param   mixed         $pk        An optional primary key value to delete.
	 *                                   If not set the instance property value is used.
	 * @param   boolean       $children  True to delete child nodes, false to move them up a level.
	 *
	 * @return  boolean  False on failure.
	 */
	public function onAfterDelete(RTableNested $table, $pk = null, $children = true)
	{
	}

	/**
	 * Event triggered before check().
	 *
	 * @param   RTableNested  $table  The table.
	 *
	 * @return  boolean  False on failure.
	 */
	public function onBeforeCheck(RTableNested $table)
	{
	}

	/**
	 * Event triggered after check().
	 *
	 * @param   RTableNested  $table  The table.
	 *
	 * @return  boolean  False on failure.
	 */
	public function onAfterCheck(RTableNested $table)
	{
	}

	/**
	 * Event triggered before store().
	 *
	 * @param   RTableNested  $table        The table.
	 * @param   boolean       $updateNulls  True to update null values as well.
	 *
	 * @return  boolean  False on failure.
	 */
	public function onBeforeStore(RTableNested $table, $updateNulls = false)
	{
	}

	/**
	 * Event triggered after store().
	 *
	 * @param   RTableNested  $table        The table.
	 * @param   boolean       $updateNulls  True to update null values as well.
	 *
	 * @return  boolean  False on failure.
	 */
	public function onAfterStore(RTableNested $table, $updateNulls = false)
	{
	}
}

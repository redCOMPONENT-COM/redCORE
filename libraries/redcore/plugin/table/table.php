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
 * An example of plugin listening to RTable events.
 *
 * @package     Redcore
 * @subpackage  Plugin
 * @since       1.0
 */
class RPluginTable extends RPlugin
{
	/**
	 * Event triggered before load().
	 *
	 * @param   RTable   $table  The table.
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.
	 *                           If not set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  False on failure.
	 */
	public function onBeforeLoad(RTable $table, $keys = null, $reset = true)
	{
	}

	/**
	 * Event triggered after load().
	 *
	 * @param   RTable   $table  The table.
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.
	 *                           If not set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  False on failure.
	 */
	public function onAfterLoad(RTable $table, $keys = null, $reset = true)
	{
	}

	/**
	 * Event triggered before delete().
	 *
	 * @param   RTable  $table  The table.
	 * @param   mixed   $pk     An optional primary key value to delete.
	 *                          If not set the instance property value is used.
	 *
	 * @return  boolean  False on failure.
	 */
	public function onBeforeDelete(RTable $table, $pk = null)
	{
	}

	/**
	 * Event triggered after delete().
	 *
	 * @param   RTable  $table  The table.
	 * @param   mixed   $pk     An optional primary key value to delete.
	 *                          If not set the instance property value is used.
	 *
	 * @return  boolean  False on failure.
	 */
	public function onAfterDelete(RTable $table, $pk = null)
	{
	}

	/**
	 * Event triggered before check().
	 *
	 * @param   RTable  $table  The table.
	 *
	 * @return  boolean  False on failure.
	 */
	public function onBeforeCheck(RTable $table)
	{
	}

	/**
	 * Event triggered after check().
	 *
	 * @param   RTable  $table  The table.
	 *
	 * @return  boolean  False on failure.
	 */
	public function onAfterCheck(RTable $table)
	{
	}

	/**
	 * Event triggered before store().
	 *
	 * @param   RTable   $table        The table.
	 * @param   boolean  $updateNulls  True to update null values as well.
	 *
	 * @return  boolean  False on failure.
	 */
	public function onBeforeStore(RTable $table, $updateNulls = false)
	{
	}

	/**
	 * Event triggered after store().
	 *
	 * @param   RTable   $table        The table.
	 * @param   boolean  $updateNulls  True to update null values as well.
	 *
	 * @return  boolean  False on failure.
	 */
	public function onAfterStore(RTable $table, $updateNulls = false)
	{
	}
}

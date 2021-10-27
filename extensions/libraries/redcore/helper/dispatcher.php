<?php
/**
 * @package     Redcore
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

use Joomla\CMS\Factory;

defined('JPATH_REDCORE') or die;

/**
 * dispatcher adpater for J4
 *
 * @package     Redcore
 * @subpackage  Helper
 * @since       __deploy_version__
 */
final class RHelperDispatcher
{
	/**
	 * Stores the singleton instance of the dispatcher.
	 *
	 * @var    RHelperDispatcher
	 * @since  __deploy_version__
	 */
	protected static $instance = null;

	/**
	 * Returns the global Event Dispatcher object, only creating it
	 * if it doesn't already exist.
	 *
	 * @return  RHelperDispatcher  The EventDispatcher object.
	 *
	 * @since  __deploy_version__
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Triggers an event by dispatching arguments to all observers that handle
	 * the event and returning their return values.
	 *
	 * @param   string  $event  The event to trigger.
	 * @param   array   $args   An array of arguments.
	 *
	 * @return  array  An array of results from each function call.
	 *
	 * @since   __deploy_version__
	 */
	public function trigger($event, $args = array())
	{
		return Factory::getApplication()->triggerEvent($event, $args);
	}
}
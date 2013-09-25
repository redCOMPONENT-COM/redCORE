<?php
/**
 * @package     Redcore
 * @subpackage  Factory
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Factory class.
 *
 * @package     Redcore
 * @subpackage  Factory
 * @since       1.0
 */
final class RFactory extends JFactory
{
	/**
	 * The dispatcher.
	 *
	 * @var  JEventDispatcher
	 */
	public static $dispatcher = null;

	/**
	 * Get the event dispatcher
	 *
	 * @return  JEventDispatcher
	 */
	public static function getDispatcher()
	{
		if (!self::$dispatcher)
		{
			self::$dispatcher = version_compare(JVERSION, '3.0', 'lt') ?
				JDispatcher::getInstance() : JEventDispatcher::getInstance();
		}

		return self::$dispatcher;
	}
}

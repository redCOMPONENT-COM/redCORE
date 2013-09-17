<?php
/**
 * @package     RedRad
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDRAD') or die;

/**
 * Base proxy to be sure we can add cool stuff here
 *
 * @package     RedRad
 * @subpackage  Model
 * @since       1.0
 */
abstract class RModel extends JModelLegacy
{
	/**
	 * Get a model instance.
	 *
	 * @param   string  $name    Model name
	 * @param   mixed   $client  Client. null = auto, 1 = admin, 0 = frontend
	 * @param   array   $config  An optional array of configuration
	 *
	 * @return  RModel  The model
	 *
	 * @throws  InvalidArgumentException
	 */
	public static function getAutoInstance($name, $client = null, array $config = array())
	{
		$option = JFactory::getApplication()->input->getString('option', '');
		$componentName = ucfirst(strtolower(substr($option, 4)));
		$prefix = $componentName . 'Model';

		if (is_null($client))
		{
			$client = (int) JFactory::getApplication()->isAdmin();
		}

		// Admin
		if ($client === 1)
		{
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/' . $option . '/' . $componentName . '/models');
		}

		// Site
		elseif ($client === 0)
		{
			JTable::addIncludePath(JPATH_SITE . '/components/' . $option . '/' . $componentName . '/models');
		}

		else
		{
			throw new InvalidArgumentException(
				sprintf('Cannot instanciate the model %s. Invalid client %s.', $name, $client)
			);
		}

		$model = self::getInstance($name, $prefix, $config);

		if (!$model instanceof JModel && !$model instanceof JModelLegacy)
		{
			throw new InvalidArgumentException(
				sprintf('Cannot instanciate the model %s from client %s.', $name, $client)
			);
		}

		return $model;
	}

	/**
	 * Get a backend model instance
	 *
	 * @param   string  $name    Model name
	 * @param   array   $config  An optional array of configuration
	 *
	 * @return  RModel  Model instance
	 */
	public static function getAdminInstance($name, array $config = array())
	{
		return self::getAutoInstance($name, 1, $config);
	}

	/**
	 * Get a frontend Model instance
	 *
	 * @param   string  $name    Model name
	 * @param   array   $config  An optional array of configuration
	 *
	 * @return  RTable  Model instance
	 */
	public static function getFrontInstance($name, array $config = array())
	{
		return self::getAutoInstance($name, 0, $config);
	}
}

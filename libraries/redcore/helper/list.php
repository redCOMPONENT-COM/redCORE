<?php
/**
 * @package     Redcore
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Helper to be extended by list helpers
 *
 * @package     Redcore
 * @subpackage  Helper
 * @since       1.0
 */
class RHelperList
{
	/**
	 * Get and instance of the model
	 *
	 * @param   array  $config  Configuration array for model. Optional.
	 *
	 * @return  object          An instance of the model
	 */
	public static function getModel($config = array('ignore_request' => true))
	{
		// Find the model for this helper
		$class = str_replace('Helper', 'Model', get_called_class());
		$class = RInflector::pluralize($class);

		if (!class_exists($class))
		{
			if (method_exists(get_class(), 'getModelPath'))
			{
				$path = static::getModelPath();

				if (!empty($path) && file_exists($path))
				{
					require_once $path;
				}
			}
		}

		// Get the name and prefix of the model
		$prefix = strstr($class, 'Model', true) . 'Model';
		$name   = str_replace($prefix, '', $class);

		return JModelLegacy::getInstance($name, $prefix, $config);
	}

	/**
	 * Search items based on filters
	 *
	 * @param   array  $filters  Filters to apply to the search
	 * @param   array  $options  start, limit, direction, ordering...
	 *
	 * @return  mixed            array -> items found | false -> error
	 *
	 * @todo  Create & use a frontend bookings model
	 */
	public static function search($filters = array(), $options = array())
	{
		if ($model = static::getModel())
		{
			// This is ugly. Please skip the next line. Force the raw template
			$model->setState('filter.template', 'default_raw');

			// Store filters in model session
			if ($filters)
			{
				foreach ($filters as $filterKey => $filterValue)
				{
					$model->setState('filter.' . $filterKey, $filterValue);
				}
			}

			// Apply options
			if ($options)
			{
				foreach ($options as $optionKey => $optionValue)
				{
					$model->setState('list.' . $optionKey, $optionValue);
				}
			}

			return $model->getItems();
		}

		return array();
	}

	/**
	 * Get the model path. This method is here just for reference
	 * Example: return JPATH_ADMINISTRATOR . '/components/com_redshopb/models/departments.php';
	 *
	 * @return  string
	 */
	protected static function getModelPath()
	{
		return null;
	}
}

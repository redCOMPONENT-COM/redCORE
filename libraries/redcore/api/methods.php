<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Common methods for Joomla models
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.2
 */
class RApiMethods
{
	/**
	 * List of available methods
	 *
	 * @var    array
	 * @since  1.2
	 */
	public static $methods = array(
		'featured',
		'unfeatured',
		'publish',
		'unpublish',
	);

	/**
	 * Set the selected items as featured
	 *
	 * @param   mixed             $model          Model class on which we call the methods
	 * @param   SimpleXMLElement  $configuration  Configuration for this task
	 * @param   mixed             $data           Data
	 *
	 * @return  mixed             Result of method with prepared data
	 */
	public static function featured($model, $configuration, $data = null)
	{
		if (empty($data))
		{
			return false;
		}

		$ids = self::getId($configuration, $data);

		if (empty($ids))
		{
			return false;
		}

		return method_exists($model, 'featured') ? $model->featured($ids, 1) : null;
	}

	/**
	 * Set the selected items as unfeatured
	 *
	 * @param   mixed             $model          Model class on which we call the methods
	 * @param   SimpleXMLElement  $configuration  Configuration for this task
	 * @param   mixed             $data           Data
	 *
	 * @return  mixed             Result of method with prepared data
	 */
	public static function unfeatured($model, $configuration, $data = null)
	{
		if (empty($data))
		{
			return false;
		}

		$ids = self::getId($configuration, $data);

		if (empty($ids))
		{
			return false;
		}

		return method_exists($model, 'featured') ? $model->featured($ids, 0) : null;
	}

	/**
	 * Set the selected items as published
	 *
	 * @param   mixed             $model          Model class on which we call the methods
	 * @param   SimpleXMLElement  $configuration  Configuration for this task
	 * @param   mixed             $data           Data
	 *
	 * @return  mixed             Result of method with prepared data
	 */
	public static function publish($model, $configuration, $data = null)
	{
		if (empty($data))
		{
			return false;
		}

		$ids = self::getId($configuration, $data);

		if (empty($ids))
		{
			return false;
		}

		return method_exists($model, 'publish') ? $model->publish($ids, 1) : null;
	}

	/**
	 * Set the selected items as unpublished
	 *
	 * @param   mixed             $model          Model class on which we call the methods
	 * @param   SimpleXMLElement  $configuration  Configuration for this task
	 * @param   mixed             $data           Data
	 *
	 * @return  mixed             Result of method with prepared data
	 */
	public static function unpublish($model, $configuration, $data = null)
	{
		if (empty($data))
		{
			return false;
		}

		$ids = self::getId($configuration, $data);

		if (empty($ids))
		{
			return false;
		}

		return method_exists($model, 'publish') ? $model->publish($ids, 0) : null;
	}

	/**
	 * Gets Id or Ids from data
	 *
	 * @param   SimpleXMLElement  $configuration  Configuration for this task
	 * @param   mixed             $data           Data
	 *
	 * @return  mixed             Id or Ids collected from the data
	 */
	public static function getId($configuration, $data = null)
	{
		// If primaryKeys is defined then we pass only Id(s)
		$primaryKeys = !empty($configuration['primaryKeys']) ?
			explode(',', (string) $configuration['primaryKeys']) : array('id');

		if (count($primaryKeys) == 1)
		{
			$ids = isset($data[$primaryKeys[0]]) ? $data[$primaryKeys[0]] : 0;
		}
		else
		{
			$ids = array();

			foreach ($primaryKeys as $key => $primaryKey)
			{
				$ids[$key][] = isset($data[$primaryKey]) ? $data[$primaryKey] : 0;
			}
		}

		return $ids;
	}
}

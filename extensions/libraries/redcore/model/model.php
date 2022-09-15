<?php
/**
 * @package     Redcore
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2021 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Base proxy to be sure we can add cool stuff here
 *
 * @package     Redcore
 * @subpackage  Model
 * @since       1.0
 */
abstract class RModel extends JModelLegacy
{
	/**
	 * Added from Joomla's legacy.php to preserve static $paths
	 *
	 * @param   string  $type    The model type to instantiate
	 * @param   string  $prefix  Prefix for the model class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  mixed   A model object or false on failure
	 */
	public static function getInstance($type, $prefix = '', $config = array())
	{
		$return = parent::getInstance($type, $prefix, $config);

		return $return;
	}

	/**
	 * Added from Joomla's legacy.php to preserve static $paths
	 *
	 * @param   mixed   $path    A path or array[sting] of paths to search.
	 * @param   string  $prefix  A prefix for models.
	 *
	 * @return  array  An array with directory elements. If prefix is equal to '', all directories are returned.
	 */
	public static function addIncludePath($path = '', $prefix = '')
	{
		$return = parent::addIncludePath($path, $prefix);

		return $return;
	}

	/**
	 * Get a model instance.
	 *
	 * @param   string  $name    Model name
	 * @param   mixed   $client  Client. null = auto, 1 = admin, 0 = frontend
	 * @param   array   $config  An optional array of configuration
	 * @param   string  $option  Component name, use for call model from modules
	 *
	 * @return  RModel  The model
	 *
	 * @throws  InvalidArgumentException
	 */
	public static function getAutoInstance($name, $client = null, array $config = array(), $option = 'auto')
	{
		if ($option === 'auto')
		{
			$option = JFactory::getApplication()->input->getString('option', '');

			// Add com_ to the element name if not exist
			$option = (strpos($option, 'com_') === 0 ? '' : 'com_') . $option;

			if ($option == 'com_installer')
			{
				$installer = JInstaller::getInstance();
				$option    = $installer->manifestClass->getElement($installer);
			}
		}

		$componentName = ucfirst(strtolower(substr($option, 4)));
		$prefix        = $componentName . 'Model';

		if (is_null($client))
		{
			$client = (int) (version_compare(JVERSION, '3.7', '<') ?
				JFactory::getApplication()->isAdmin() : JFactory::getApplication()->isClient('administrator'));
		}

		// Admin
		if ($client === 1)
		{
			self::addIncludePath(JPATH_ADMINISTRATOR . '/components/' . $option . '/models', $prefix);
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/' . $option . '/tables');
		}

		// Site
		elseif ($client === 0)
		{
			self::addIncludePath(JPATH_SITE . '/components/' . $option . '/models', $prefix);
			JTable::addIncludePath(JPATH_SITE . '/components/' . $option . '/tables');
		}

		else
		{
			throw new InvalidArgumentException(
				sprintf('Cannot instanciate the model %s in component %s. Invalid client %s.', $name, $option, $client)
			);
		}

		$model = self::getInstance($name, $prefix, $config);

		if (!$model instanceof JModel && !$model instanceof JModelLegacy)
		{
			throw new InvalidArgumentException(
				sprintf('Cannot instanciate the model %s in component %s. Invalid client %s.', $name, $option, $client)
			);
		}

		return $model;
	}

	/**
	 * Get a backend model instance
	 *
	 * @param   string  $name    Model name
	 * @param   array   $config  An optional array of configuration
	 * @param   string  $option  Component name, use for call model from modules
	 *
	 * @return  RModel  Model instance
	 */
	public static function getAdminInstance($name, array $config = array(), $option = 'auto')
	{
		return self::getAutoInstance($name, 1, $config, $option);
	}

	/**
	 * Get a frontend Model instance
	 *
	 * @param   string  $name    Model name
	 * @param   array   $config  An optional array of configuration
	 * @param   string  $option  Component name, use for call model from modules
	 *
	 * @return  RTable  Model instance
	 */
	public static function getFrontInstance($name, array $config = array(), $option = 'auto')
	{
		return self::getAutoInstance($name, 0, $config, $option);
	}
}

<?php
/**
 * @package     Redcore
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Utility class for media
 *
 * @package     Redcore
 * @subpackage  HTML
 * @since       1.4
 */
abstract class RHtmlMedia
{
	/**
	 * @var    string  Framework name to use
	 * @since  1.4
	 */
	public static $framework = '';

	/**
	 * @var    string  Framework suffix to use
	 * @since  1.4
	 */
	public static $frameworkSuffix = '';

	/**
	 * @var    array  Framework settings
	 * @since  1.4
	 */
	public static $frameworkOptions = '';

	/**
	 * Get selected framework
	 *
	 * @return  string  Framework name
	 *
	 * @since   1.4
	 */
	public static function getFramework()
	{
		return !empty(self::$framework) ? self::$framework : 'bootstrap2';
	}

	/**
	 * Set the framework
	 *
	 * @param   string  $framework  Framework name
	 * @param   array   $options    Framework options
	 *
	 * @return  void
	 *
	 * @since   1.4
	 */
	public static function setFramework($framework = 'bootstrap3', $options = array())
	{
		self::$framework = $framework;

		if ($framework == 'bootstrap3')
		{
			self::$frameworkSuffix  = 'bs3';
			self::$frameworkOptions = array(
				'disableMootools' => true,
			);
		}
		elseif ($framework == 'bootstrap2')
		{
			self::$frameworkSuffix  = 'bs2';
			self::$frameworkOptions = array(
				'disableMootools' => false,
			);
		}
		elseif ($framework == 'foundation5')
		{
			self::$frameworkSuffix  = 'fd5';
			self::$frameworkOptions = array(
				'disableMootools' => false,
			);
		}
		else
		{
			self::$frameworkSuffix  = '';
			self::$frameworkOptions = array(
				'disableMootools' => false,
			);
		}

		if (!empty($options))
		{
			self::$frameworkOptions = array_merge(self::$frameworkOptions, $options);
		}
	}

	/**
	 * Loads proper media framework library
	 *
	 * @param   string  $defaultFramework  Set as default framework
	 *
	 * @return  void
	 */
	public static function loadFrameworkCss($defaultFramework = '')
	{
		if (!empty($defaultFramework))
		{
			self::setFramework($defaultFramework);
		}

		$isAdmin = (version_compare(JVERSION, '3.7', '<') ?
			JFactory::getApplication()->isAdmin() : JFactory::getApplication()->isClient('administrator'));

		if (($isAdmin && defined('REDCORE_BOOTSTRAPPED')) || (!$isAdmin && RBootstrap::$loadFrontendCSS))
		{
			if (self::getFramework() == 'bootstrap2')
			{
				RHelperAsset::load('component.min.css', 'redcore');
			}
			elseif (self::getFramework() == 'bootstrap3')
			{
				RHelperAsset::load('component.bs3.min.css', 'redcore');
			}
			elseif (self::getFramework() == 'foundation5')
			{
			}
		}
	}

	/**
	 * Loads proper media framework library
	 *
	 * @param   string  $defaultFramework  Set as default framework
	 *
	 * @return  void
	 */
	public static function loadFrameworkJs($defaultFramework = '')
	{
		JHtml::_('rjquery.framework');

		if (!empty($defaultFramework))
		{
			self::setFramework($defaultFramework);
		}

		$isAdmin = (version_compare(JVERSION, '3.7', '<') ?
			JFactory::getApplication()->isAdmin() : JFactory::getApplication()->isClient('administrator'));

		if (($isAdmin && defined('REDCORE_BOOTSTRAPPED')) || (!$isAdmin && RBootstrap::$loadFrontendCSS))
		{
			if (self::getFramework() == 'bootstrap2')
			{
				RHelperAsset::load('lib/bootstrap/js/bootstrap.min.js', 'redcore');
			}
			elseif (self::getFramework() == 'bootstrap3')
			{
				RHelperAsset::load('lib/bootstrap3/js/bootstrap.min.js', 'redcore');
			}
			elseif (self::getFramework() == 'foundation5')
			{
			}
		}
	}

	/**
	 * Returns true if mootools should be disabled for current framework
	 *
	 * @param   string  $defaultFramework  Set as default framework
	 *
	 * @return  boolean
	 */
	public static function isMootoolsDisabled($defaultFramework = '')
	{
		if (!empty($defaultFramework))
		{
			self::setFramework($defaultFramework);
		}

		if (!empty(self::$frameworkOptions['disableMootools']))
		{
			return true;
		}

		return false;
	}
}

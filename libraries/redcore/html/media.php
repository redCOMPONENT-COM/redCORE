<?php
/**
 * @package     Redcore
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
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
	public static $framework = 'bootstrap2';

	/**
	 * @var    string  Framework suffix to use
	 * @since  1.4
	 */
	public static $frameworkSuffix = '';

	/**
	 * Get selected framework
	 *
	 * @return  string  Framework name
	 *
	 * @since   1.4
	 */
	public static function getFramework()
	{
		return self::$framework;
	}

	/**
	 * Set the framework
	 *
	 * @param   string  $framework  Framework name
	 *
	 * @return  void
	 *
	 * @since   1.4
	 */
	public static function setFramework($framework = 'bootstrap2')
	{
		self::$framework = $framework;

		if ($framework = 'bootstrap3')
		{
			self::$frameworkSuffix = 'bs3';
		}
		else
		{
			self::$frameworkSuffix = '';
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

		if (self::$framework == 'bootstrap2')
		{
			RHelperAsset::load('component.min.css', 'redcore');
		}
		elseif (self::$framework == 'bootstrap3')
		{
			RHelperAsset::load('component.bs3.min.css', 'redcore');
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

		if (self::$framework == 'bootstrap2')
		{
			RHelperAsset::load('lib/bootstrap.min.js', 'redcore');
		}
		elseif (self::$framework == 'bootstrap3')
		{
			RHelperAsset::load('lib/bootstrap3/bootstrap.min.js', 'redcore');
		}
	}
}

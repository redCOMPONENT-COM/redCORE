<?php
/**
 * Bootstrap file.
 * Including this file into your application and executing RBootstrap::bootstrap() will make redCORE available to use.
 *
 * @package    Redcore
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

if (!defined('JPATH_REDCORE'))
{
	// Sets redCORE path variable, to avoid setting it twice
	define('JPATH_REDCORE', dirname(__FILE__));

	require JPATH_REDCORE . '/functions.php';
}

/**
 * redCORE bootstrap class
 *
 * @package     Red
 * @subpackage  System
 * @since       1.0
 */
class RBootstrap
{
	/**
	 * Redcore configuration
	 *
	 * @var    JRegistry
	 */
	public static $config = null;

	/**
	 * Defines if redCORE base css should be loaded in Frontend component/modules
	 *
	 * @var    bool
	 */
	public static $loadFrontendCSS = false;

	/**
	 * Defines if jQuery should be loaded in Frontend component/modules
	 *
	 * @var    bool
	 */
	public static $loadFrontendjQuery = true;

	/**
	 * Defines if jQuery Migrate should be loaded in Frontend component/modules
	 *
	 * @var    bool
	 */
	public static $loadFrontendjQueryMigrate = true;

	/**
	 * Defines if Mootools should be disabled in Frontend component/modules
	 *
	 * @var    bool
	 */
	public static $disableFrontendMootools = false;

	/**
	 * Gets redCORE config param
	 *
	 * @param   string  $key      Config key
	 * @param   mixed   $default  Default value
	 *
	 * @return  mixed
	 */
	public static function getConfig($key, $default = null)
	{
		if (is_null(self::$config))
		{
			$plugin = JPluginHelper::getPlugin('system', 'redcore');

			if ($plugin)
			{
				if (is_string($plugin->params))
				{
					self::$config = new JRegistry($plugin->params);
				}
				elseif (is_object($plugin->params))
				{
					self::$config = $plugin->params;
				}
			}

			return null;
		}

		return self::$config->get($key, $default);
	}

	/**
	 * Effectively bootstrap redCORE.
	 *
	 * @param   bool  $loadBootstrap  Load bootstrap with redcore plugin options
	 *
	 * @return  void
	 */
	public static function bootstrap($loadBootstrap = true)
	{
		if ($loadBootstrap && !defined('REDCORE_BOOTSTRAPPED'))
		{
			define('REDCORE_BOOTSTRAPPED', 1);
		}

		if (!defined('REDCORE_LIBRARY_LOADED'))
		{
			// Sets bootstrapped variable, to avoid bootstrapping redCORE twice
			define('REDCORE_LIBRARY_LOADED', 1);

			// Use our own base field
			if (!class_exists('JFormField', false))
			{
				$baseField = JPATH_LIBRARIES . '/redcore/joomla/form/field.php';

				if (file_exists($baseField))
				{
					require_once $baseField;
				}
			}

			// Register the classes for autoload.
			JLoader::registerPrefix('R', JPATH_REDCORE);

			// Setup the RLoader.
			RLoader::setup();

			// Make available the redCORE fields
			JFormHelper::addFieldPath(JPATH_REDCORE . '/form/fields');

			// Make available the redCORE form rules
			JFormHelper::addRulePath(JPATH_REDCORE . '/form/rules');

			// HTML helpers
			JHtml::addIncludePath(JPATH_REDCORE . '/html');
			RHtml::addIncludePath(JPATH_REDCORE . '/html');

			// Load library language
			$lang = JFactory::getLanguage();
			$lang->load('lib_redcore', JPATH_REDCORE);

			// For Joomla! 2.5 compatibility we add some core functions
			if (version_compare(JVERSION, '3.0', '<'))
			{
				RLoader::registerPrefix('J',  JPATH_LIBRARIES . '/redcore/joomla', false, true);
			}

			// Make available the fields
			JFormHelper::addFieldPath(JPATH_LIBRARIES . '/redcore/form/fields');

			// Make available the rules
			JFormHelper::addRulePath(JPATH_LIBRARIES . '/redcore/form/rules');

			// Replaces Joomla database driver for redCORE database driver
			JFactory::$database = null;
			JFactory::$database = RFactory::getDbo();

			if (self::getConfig('enable_translations', 0) == 1 && !JFactory::getApplication()->isAdmin())
			{
				// This is our object now
				$db = JFactory::getDbo();

				// Enable translations
				$db->translate = self::getConfig('enable_translations', 0) == 1;

				// Reset plugin translations params if needed
				RTranslationHelper::resetPluginTranslation();
			}
		}
	}
}

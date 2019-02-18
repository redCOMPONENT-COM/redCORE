<?php
/**
 * Bootstrap file.
 * Including this file into your application and executing RBootstrap::bootstrap() will make redCORE available to use.
 *
 * @package    Redcore
 * @copyright  Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
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
			if (RComponentHelper::isInstalled('com_redcore'))
			{
				self::$config = JComponentHelper::getParams('com_redcore');

				// Sets initialization variables for frontend in Bootstrap class, according to plugin parameters
				self::$loadFrontendCSS = self::$config->get('frontend_css', false);
				self::$loadFrontendjQuery = self::$config->get('frontend_jquery', true);
				self::$loadFrontendjQueryMigrate = self::$config->get('frontend_jquery_migrate', true);
				self::$disableFrontendMootools = self::$config->get('frontend_mootools_disable', false);
			}
		}

		if (!self::$config)
		{
			return $default;
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

			// We are still in Joomla 2.5 or another version so we use alias to prevent errors
			if (!class_exists('Joomla\Registry\Registry'))
			{
				class_alias('JRegistry', 'Joomla\Registry\Registry');
			}

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
			JFormHelper::addFieldPath(JPATH_REDCORE . '/form/field');
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

			$isAdmin = RTranslationHelper::isAdmin();
			$isTranslateAdmin = (bool) self::getConfig('translate_in_admin', 0);

			if (self::getConfig('enable_translations', 0) == 1
				&& (!$isAdmin || ($isAdmin && $isTranslateAdmin)))
			{
				// This is our object now
				$db = JFactory::getDbo();

				// Enable translations
				$db->translate = self::getConfig('enable_translations', 0) == 1;

				// Setting default option for translation fallback
				RDatabaseSqlparserSqltranslation::setTranslationFallback(self::getConfig('enable_translation_fallback', '1') == '1');

				// Setting default option for force translate default language
				RDatabaseSqlparserSqltranslation::setForceTranslateDefaultLanguage(
					self::getConfig('force_translate_default_site_language', '0') == '1'
				);

				// Set option for "translate in admin"
				RDatabaseSqlparserSqltranslation::setTranslationInAdmin($isTranslateAdmin);

				// Reset plugin translations params if needed
				RTranslationHelper::resetPluginTranslation();
			}
			else
			{
				// We still need to set translate property to avoid notices as we check it from other functions
				$db = JFactory::getDbo();
				$db->translate = 0;
			}
		}
	}
}

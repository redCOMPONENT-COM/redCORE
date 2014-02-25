<?php
/**
 * Bootstrap file.
 * Including this file into your application and executing RBootstrap::bootstrap() will make redCORE available to use.
 *
 * @package    Redcore
 * @copyright  Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
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
	 * Defines if jQuery should be loaded in Frontend component/modules
	 *
	 * @var    bool
	 */
	public static $loadFrontendjQuery = false;

	/**
	 * Defines if jQuery Migrate should be loaded in Frontend component/modules
	 *
	 * @var    bool
	 */
	public static $loadFrontendjQueryMigrate = true;

	/**
	 * Defines if Bootstrap should be loaded in Frontend component/modules
	 *
	 * @var    bool
	 */
	public static $loadFrontendBootstrap = false;

	/**
	 * Defines if Mootools should be disabled in Frontend component/modules
	 *
	 * @var    bool
	 */
	public static $disableFrontendMootools = false;

	/**
	 * Effectively bootstrap redCORE.
	 *
	 * @return  void
	 */
	public static function bootstrap()
	{
		if (!defined('REDCORE_BOOTSTRAPPED'))
		{
			// Sets bootstrapped variable, to avoid bootstrapping redCORE twice
			define('REDCORE_BOOTSTRAPPED', 1);

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
			$lang->load('lib_redcore', JPATH_SITE);

			// For Joomla! 2.5 compatibility we add some core functions
			if (version_compare(JVERSION, '3.0', '<'))
			{
				RLoader::registerPrefix('J',  JPATH_LIBRARIES . '/redcore/joomla', false, true);
			}

			// Make available the fields
			JFormHelper::addFieldPath(JPATH_LIBRARIES . '/redcore/form/fields');

			// Make available the rules
			JFormHelper::addRulePath(JPATH_LIBRARIES . '/redcore/form/rules');
		}
	}
}

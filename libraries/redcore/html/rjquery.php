<?php
/**
 * @package     Redcore
 * @subpackage  Html
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * jQuery HTML class.
 *
 * @package     Redcore
 * @subpackage  Html
 * @since       1.0
 */
abstract class JHtmlRjquery
{
	/**
	 * Extension name to use in the asset calls
	 * Basically the media/com_xxxxx folder to use
	 */
	const EXTENSION = 'redcore';

	/**
	 * Array containing information for loaded files
	 *
	 * @var  array
	 */
	protected static $loaded = array();

	/**
	 * Load the chosen library
	 * We use this to avoid Mootools dependency
	 *
	 * @param   string  $selector  CSS Selector to initalise selects
	 * @param   mixed   $debug     Enable debug mode?
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public static function chosen($selector = '.chosen', $debug = null)
	{
		// Only load once
		if (isset(static::$loaded[__METHOD__][$selector]))
		{
			return;
		}

		self::framework();

		// Add chosen.jquery.js language strings
		JText::script('JGLOBAL_SELECT_SOME_OPTIONS');
		JText::script('JGLOBAL_SELECT_AN_OPTION');
		JText::script('JGLOBAL_SELECT_NO_RESULTS_MATCH');

		RHelperAsset::load('lib/chosen.jquery.js', self::EXTENSION);
		RHelperAsset::load('lib/chosen.css', self::EXTENSION);
		RHelperAsset::load('lib/chosen-extra.css', self::EXTENSION);

		JFactory::getDocument()->addScriptDeclaration("
			(function($){
				$(document).ready(function () {
					$('" . $selector . "').chosen({
						disable_search_threshold : 10,
						allow_single_deselect : true
					});
				});
			})(jQuery);
		");

		static::$loaded[__METHOD__][$selector] = true;

		return;
	}

	/**
	 * Load the datepicker.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public static function datepicker()
	{
		// Only load once
		if (!empty(static::$loaded[__METHOD__]))
		{
			return;
		}

		self::ui();

		// Include translations
		$langTag = JFactory::getLanguage()->getTag();
		$langTagParts = explode('-', $langTag);
		$mainLang = reset($langTagParts);
		RHelperAsset::load('lib/jquery-ui/i18n/jquery.ui.datepicker-' . $langTag . '.js', self::EXTENSION);
		RHelperAsset::load('lib/jquery-ui/i18n/jquery.ui.datepicker-' . $mainLang . '.js', self::EXTENSION);

		RHelperAsset::load('lib/jquery-ui/jquery.ui.datepicker.css', self::EXTENSION);

		static::$loaded[__METHOD__] = true;

		return;
	}

	/**
	 * Load the dependent fields
	 *
	 * @param   string  $childFieldSelector  DOM selector to apply the dropdowns
	 * @param   array   $options             Optional array parameters
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public static function childlist($childFieldSelector = '.js-childlist-child', $options = array())
	{
		// This does not cache because we can have multiple instances in the same form ,same parent field and same child field
		self::framework();

		RHelperAsset::load('jquery.childlist.js', self::EXTENSION);

		$options = static::options2Jregistry($options);

		JFactory::getDocument()->addScriptDeclaration("
			(function($){
				$(document).ready(function () {
					$('" . $childFieldSelector . "').childlist(" . $options->toString() . ");
				});
			})(jQuery);
		");
	}

	/**
	 * Load the jQuery framework
	 *
	 * @return  void
	 */
	public static function framework()
	{
		// Only load once
		if (!empty(static::$loaded[__METHOD__]))
		{
			return;
		}

		$isAdmin = JFactory::getApplication()->isAdmin();

		// Load jQuery in administration, or if it's frontend site and it has been asked via plugin parameters
		if ($isAdmin || (!$isAdmin && RBootstrap::$loadFrontendjQuery))
		{
			RHelperAsset::load('lib/jquery.min.js', self::EXTENSION);
			RHelperAsset::load('lib/jquery-noconflict.js', self::EXTENSION);
		}
		elseif (!$isAdmin && !RBootstrap::$loadFrontendBootstrap && !version_compare(JVERSION, '3.0', '<'))
		{
			JHtml::_('jquery.framework');
		}

		// Load jQuery Migrate in administration, or if it's frontend site and it has been asked via plugin parameters
		if ($isAdmin || (!$isAdmin && RBootstrap::$loadFrontendjQueryMigrate))
		{
			RHelperAsset::load('lib/jquery-migrate.min.js', self::EXTENSION);
		}

		static::$loaded[__METHOD__] = true;

		return;
	}

	/**
	 * Function to receive & pre-process javascript options
	 *
	 * @param   mixed  $options  Associative array/JRegistry object with options
	 *
	 * @return  JRegistry        Options converted to JRegistry object
	 */
	private static function options2Jregistry($options)
	{
		// Support options array
		if (is_array($options))
		{
			$options = new JRegistry($options);
		}

		if (!($options instanceof Jregistry))
		{
			$options = new JRegistry;
		}

		return $options;
	}

	/**
	 * Load the select2 library
	 * https://github.com/ivaynberg/select2
	 *
	 * @param   string   $selector          CSS Selector to initalise selects
	 * @param   array    $options           Optional array with options
	 * @param   boolean  $bootstrapSupport  Load Twitter Bootstrap integration CSS
	 *
	 * @todo    Add the multilanguage support
	 *
	 * @return  void
	 */
	public static function select2($selector = '.select2', $options = null, $bootstrapSupport = true)
	{
		// Only load once
		if (isset(static::$loaded[__METHOD__][$selector]))
		{
			return;
		}

		self::framework();

		RHelperAsset::load('lib/select2/select2.js', self::EXTENSION);
		RHelperAsset::load('lib/select2/select2.css', self::EXTENSION);

		if ($bootstrapSupport)
		{
			RHelperAsset::load('lib/select2/select2-bootstrap.css', self::EXTENSION);
		}

		RHelperAsset::load('lib/select2/select2-extra.css', self::EXTENSION);

		// Generate options with default values
		$options = static::formatSelect2Options($options);

		JFactory::getDocument()->addScriptDeclaration("
			(function($){
				$(document).ready(function () {
					$('" . $selector . "').select2(
						" . $options . "
					);
				});
			})(jQuery);
		");

		static::$loaded[__METHOD__][$selector] = true;

		return;
	}

	/**
	 * Function to receive & pre-process select2 options
	 *
	 * @param   mixed  $options  Associative array/JRegistry object with options
	 *
	 * @return  json             The options ready for the select2() function
	 */
	private static function formatSelect2Options($options)
	{
		// Support options array
		if (is_array($options))
		{
			$options = new JRegistry($options);
		}

		if (!($options instanceof Jregistry))
		{
			$options = new JRegistry;
		}

		// Fix the width to resolve by default
		if ($options->get('width', null) === null)
		{
			$options->set('width', 'resolve');
		}

		return $options->toString();
	}

	/**
	 * Load the jQuery UI library
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public static function ui()
	{
		// Only load once
		if (!empty(static::$loaded[__METHOD__]))
		{
			return;
		}

		self::framework();

		RHelperAsset::load('lib/jquery-ui/jquery-ui.min.js', self::EXTENSION);

		// Include datepicker translations
		$langTag      = JFactory::getLanguage()->getTag();
		$langTagParts = explode('-', $langTag);
		$mainLang     = reset($langTagParts);
		RHelperAsset::load('lib/jquery-ui/i18n/jquery.ui.datepicker-' . $langTag . '.js', self::EXTENSION);
		RHelperAsset::load('lib/jquery-ui/i18n/jquery.ui.datepicker-' . $mainLang . '.js', self::EXTENSION);

		// CSS
		RHelperAsset::load('lib/jquery-ui/jquery-ui.custom.min.css', self::EXTENSION);

		static::$loaded[__METHOD__] = true;

		return;
	}

	/**
	 * Load the flexslider library.
	 *
	 * @param   string  $selector  CSS Selector to initalise selects
	 * @param   array   $options   Optional array with options
	 *
	 * @return void
	 */
	public static function flexslider($selector = '.flexslider', $options = null)
	{
		// Only load once
		if (isset(static::$loaded[__METHOD__][$selector]))
		{
			return;
		}

		self::framework();

		RHelperAsset::load('lib/flexslider/jquery.flexslider.js', self::EXTENSION);
		RHelperAsset::load('lib/flexslider/flexslider.css', self::EXTENSION);

		$options = static::options2Jregistry($options);

		JFactory::getDocument()->addScriptDeclaration("
			(function($){
				$(document).ready(function () {
					$('" . $selector . "').flexslider(" . $options->toString() . ");
				});
			})(jQuery);
		");
		static::$loaded[__METHOD__][$selector] = true;

		return;
	}
}

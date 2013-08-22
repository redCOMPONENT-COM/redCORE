<?php
/**
 * @package     RedRad
 * @subpackage  Html
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDRAD') or die;

/**
 * jQuery HTML class.
 *
 * @package     RedRad
 * @subpackage  Html
 * @since       1.0
 */
abstract class JHtmlRjquery
{
	/**
	 * Extension name to use in the asset calls
	 * Basically the media/com_xxxxx folder to use
	 */
	const EXTENSION = 'redrad';

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
		self::framework();

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
		self::framework();

		RHelperAsset::load('lib/jquery-ui/jquery.ui.datepicker.js', self::EXTENSION);

		// Include translations
		$langTag = JFactory::getLanguage()->getTag();
		$langTagParts = explode('-', $langTag);
		$mainLang = reset($langTagParts);
		RHelperAsset::load('lib/jquery-ui/i18n/jquery.ui.datepicker-' . $langTag . '.js', self::EXTENSION);
		RHelperAsset::load('lib/jquery-ui/i18n/jquery.ui.datepicker-' . $mainLang . '.js', self::EXTENSION);

		RHelperAsset::load('lib/jquery-ui/jquery.ui.datepicker.css', self::EXTENSION);
	}

	/**
	 * Load the jQuery framework
	 *
	 * @return  void
	 */
	public static function framework()
	{
		RHelperAsset::load('lib/jquery.js', self::EXTENSION);
		RHelperAsset::load('lib/jquery-migrate.js', self::EXTENSION);
		RHelperAsset::load('lib/jquery-noconflict.js', self::EXTENSION);
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
		self::framework();

		RHelperAsset::load('lib/jquery-ui/jquery-ui.js', self::EXTENSION);

		// Include datepicker translations
		$langTag      = JFactory::getLanguage()->getTag();
		$langTagParts = explode('-', $langTag);
		$mainLang     = reset($langTagParts);
		RHelperAsset::load('lib/jquery-ui/i18n/jquery.ui.datepicker-' . $langTag . '.js', self::EXTENSION);
		RHelperAsset::load('lib/jquery-ui/i18n/jquery.ui.datepicker-' . $mainLang . '.js', self::EXTENSION);

		// CSS
		RHelperAsset::load('lib/jquery-ui/jquery-ui.css', self::EXTENSION);
	}
}

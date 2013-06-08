<?php
/**
 * @package     RedRad
 * @subpackage  Html
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_REDRAD') or die;

/**
 * jQuery HTML class.
 *
 * @package     RedRad
 * @subpackage  Html
 * @since       1.0
 */
abstract class RedradJquery
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
	 * @return  void
	 *
	 * @since   1.0
	 */
	public static function chosen()
	{
		self::framework();

		RHelperAsset::load('lib/chosen.jquery.js', self::EXTENSION);
		RHelperAsset::load('lib/chosen.css', self::EXTENSION);

		JFactory::getDocument()->addScriptDeclaration("
			(function($){
				$(document).ready(function () {
					$('select.chosen').chosen();
				});
			})(jQuery);
		");
	}

	/**
	 * Load the chosen library
	 * We use this to avoid Mootools dependency
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
		// For Joomla! 2.5 compatibility we add some core functions
		if (version_compare(JVERSION, '3.0', '<'))
		{
			RHelperAsset::load('lib/jquery.js', self::EXTENSION);
		}
		else
		{
			JHtml::_('jquery.framework');
		}
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

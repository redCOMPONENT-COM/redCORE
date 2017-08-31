<?php
/**
 * @package     Redcore
 * @subpackage  Html
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Holder.js library.
 *
 * @package     Redcore
 * @subpackage  Html
 * @since       1.0
 */
abstract class JHtmlRholder
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
	 * Displays an image
	 *
	 * @param   string  $src      Src attribute like 200x300/sky
	 * @param   string  $alt      Alt text
	 * @param   mixed   $attribs  Element attributes
	 *
	 * @return  string  The html
	 */
	public static function image($src, $alt = '', $attribs = null)
	{
		self::holder();

		$path = JURI::root() . 'media/redcore/lib/holder.min.js/' . $src;

		return '<img data-src="' . $path . '" alt="' . $alt . '" '
		. trim((is_array($attribs) ? ArrayHelper::toString($attribs) : $attribs) . ' /')
		. '>';
	}

	/**
	 * Load the jQuery framework
	 *
	 * @return  void
	 */
	public static function holder()
	{
		// Only load once
		if (!empty(static::$loaded[__METHOD__]))
		{
			return;
		}

		RHelperAsset::load('lib/holder.min.js', self::EXTENSION);

		static::$loaded[__METHOD__] = true;

		return;
	}
}

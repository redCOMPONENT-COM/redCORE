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

		$path = JPATH_ROOT . '/media/redcore/js/lib/holder.js/' . $src;

		return '<img data-src="' . $path . '" alt="' . $alt . '" '
		. trim((is_array($attribs) ? JArrayHelper::toString($attribs) : $attribs) . ' /')
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

		RHelperAsset::load('lib/holder.js', self::EXTENSION);

		static::$loaded[__METHOD__] = true;

		return;
	}
}

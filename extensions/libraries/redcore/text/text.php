<?php
/**
 * @package     Redcore
 * @subpackage  Text
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Text class.
 *
 * @package     Redcore
 * @subpackage  Text
 * @since       1.0
 */
class RText extends JText
{
	/**
	 * Replace tags delimited by $openingSeparator and $closingSeparator in string.
	 *
	 * @param   string  $string            The string to replace tags from.
	 * @param   array   $tags              An associative array of tag names as key and their replacement value as values.
	 * @param   string  $openingSeparator  The opening separator for the tag.
	 * @param   string  $closingSeparator  The closing separator for the tag.
	 *
	 * @return string The string with tags replaced.
	 */
	public static function replace($string, array $tags, $openingSeparator = '{', $closingSeparator = '}')
	{
		$replace = array();

		foreach ($tags as $key => $val)
		{
			$replace[$openingSeparator . $key . $closingSeparator] = $val;
		}

		return strtr($string, $replace);
	}

	/**
	 * Simple text format
	 *
	 * @param   string  $string  The string to format
	 * @param   array   $tags    An associative array of characters to replace
	 *
	 * @return  string The formatted string
	 */
	public static function format($string, array $tags)
	{
		foreach ($tags as $old => $new)
		{
			$newString = str_replace($old, $new, $string);
		}

		return $newString;
	}

	/**
	 * Returns translated string if found in translation files, if it does not exist it returns given string
	 *
	 * @param   string  $string     The string key to get from translation files
	 * @param   string  $prefix     Extension Key prefix
	 * @param   string  $separator  Separator between Extension prefix and string key
	 *
	 * @return  string The string from translation or default string
	 */
	public static function getTranslationIfExists($string, $prefix = 'COM_REDCORE', $separator = '_')
	{
		$lang = JFactory::getLanguage();

		if ($lang->hasKey(strtoupper($prefix . $separator . $string)))
		{
			return self::_(strtoupper($prefix . $separator . $string));
		}
		else
		{
			return $string;
		}
	}
}

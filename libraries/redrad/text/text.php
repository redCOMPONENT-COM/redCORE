<?php
/**
 * @package     RedRad
 * @subpackage  Text
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDRAD') or die;

/**
 * Text class.
 *
 * @package     RedRad
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
	 * @param   string  $openingSeparator  The opening seperator for the tag.
	 * @param   string  $closingSeparator  The closing seperator for the tag.
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
}

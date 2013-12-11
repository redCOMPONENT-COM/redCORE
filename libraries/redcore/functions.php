<?php
/**
 * B/C functions file.
 *
 * @package    Redcore
 * @copyright  Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

if (!function_exists('get_called_class'))
{
	/**
	 * Very bad hacked version of get_called_class() for PHP 5.2.x
	 * Uses debug_backtrace() to determine where the function was called
	 * from and gets the function name from the file itself
	 *
	 * @see http://php.net/manual/en/function.get-called-class.php
	 *
	 * @return string
	 */
	function get_called_class()
	{
		$backtrace = debug_backtrace();
		$backtrace = $backtrace[count($backtrace) - 1];

		if ($backtrace["function"] = "eval" || $backtrace["type"] == "::")
		{
			// Static method call, get the line from the file
			$file = file_get_contents($backtrace["file"]);

			$file = split("\n", $file);

			for ($line = $backtrace["line"] - 1; $line > 0; $line--)
			{
				preg_match("/(?P<class>\w+)::(.*)/", trim($file[$line]), $matches);

				if (isset($matches["class"]))
				{
					return $matches["class"];
				}
			}

			throw new Exception("Could not find class in get_called_class()");
		}
	}
}

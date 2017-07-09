<?php
/**
 * @package     Redcore
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Database helper class.
 *
 * @package     Redcore
 * @subpackage  Helper
 * @since       1.5
 */
final class RHelperDatabase
{
	/**
	 * Splits a string of multiple queries into an array of individual queries.
	 *
	 * @param   string  $sql  Input SQL string with which to split into individual queries.
	 *
	 * @return  array  The queries from the input string separated into an array.
	 *
	 * @since   11.1
	 */
	public static function splitSql($sql)
	{
		$start = 0;
		$open = false;
		$char = '';
		$end = strlen($sql);
		$queries = array();
		$delimiter = ';';
		$latest = '';

		for ($i = 0; $i < $end; $i++)
		{
			$current = substr($sql, $i, 1);
			$latest .= $current;

			if (strlen($latest) > strlen($delimiter))
			{
				$latest = substr($latest, 1);
			}

			if (($current == '"' || $current == '\''))
			{
				$n = 2;

				while (substr($sql, $i - $n + 1, 1) == '\\' && $n < $i)
				{
					$n++;
				}

				if ($n % 2 == 0)
				{
					if ($open)
					{
						if ($current == $char)
						{
							$open = false;
							$char = '';
						}
					}
					else
					{
						$open = true;
						$char = $current;
					}
				}
			}

			$fileEnd = ($i == $end - 1);

			if (!$open || $fileEnd)
			{
				$delimiterChange = false;

				if ($current == chr(10))
				{
					$statement = substr($sql, $start, ($i - $start + 1));

					if (preg_match('/delimiter(\\s*)(\'?)(.*?)(\'?)$/i', $statement, $matches))
					{
						$delimiter = $matches[3];
						$start = $i + 1;
						$latest = '';
						$delimiterChange = true;
					}
				}

				if ((!$delimiterChange && $latest == $delimiter) || $fileEnd)
				{
					$queries[] = substr($sql, $start, ($i - $start + 1) - strlen($delimiter));
					$start = $i + 1;
					$latest = '';
				}
			}
		}

		return $queries;
	}

	/**
	 * This function replaces a string identifier <var>$prefix</var> with the string held is the
	 * <var>tablePrefix</var> class variable.
	 *
	 * @param   string  $sql           The SQL statement to prepare.
	 * @param   string  $tablePrefix   This install table prefix
	 * @param   string  $prefix        The common table prefix.
	 * @param   mixed   $insideQuotes  Replace prefix inside quotes too
	 *
	 * @return  string  The processed SQL statement.
	 *
	 * @since   1.5
	 */
	public static function replacePrefix($sql, $tablePrefix, $prefix = '#__', $insideQuotes = false)
	{
		$startPos = 0;
		$literal = '';

		$sql = trim($sql);

		if ($insideQuotes)
		{
			return str_replace($prefix, $tablePrefix, $sql);
		}

		$n = strlen($sql);

		while ($startPos < $n)
		{
			$ip = strpos($sql, $prefix, $startPos);

			if ($ip === false)
			{
				break;
			}

			$j = strpos($sql, "'", $startPos);
			$k = strpos($sql, '"', $startPos);

			if (($k !== false) && (($k < $j) || ($j === false)))
			{
				$quoteChar = '"';
				$j = $k;
			}
			else
			{
				$quoteChar = "'";
			}

			if ($j === false)
			{
				$j = $n;
			}

			$literal .= str_replace($prefix, $tablePrefix, substr($sql, $startPos, $j - $startPos));
			$startPos = $j;

			$j = $startPos + 1;

			if ($j >= $n)
			{
				break;
			}

			// Quote comes first, find end of quote
			while (true)
			{
				$k = strpos($sql, $quoteChar, $j);
				$escaped = false;

				if ($k === false)
				{
					break;
				}

				$l = $k - 1;

				while ($l >= 0 && $sql{$l} == '\\')
				{
					$l--;
					$escaped = !$escaped;
				}

				if ($escaped)
				{
					$j = $k + 1;
					continue;
				}

				break;
			}

			if ($k === false)
			{
				// Error in the query - no end quote; ignore it
				break;
			}

			$literal .= substr($sql, $startPos, $k - $startPos + 1);
			$startPos = $k + 1;
		}

		if ($startPos < $n)
		{
			$literal .= substr($sql, $startPos, $n - $startPos);
		}

		return $literal;
	}
}

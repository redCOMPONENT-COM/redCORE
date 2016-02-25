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
 * Query helper class.
 *
 * @package     Redcore
 * @subpackage  Helper
 * @since       1.5
 */
final class RHelperQuery
{
	/**
	 * Execute File Queries
	 *
	 * @param   string  $path  Path to sql file
	 *
	 * @return bool
	 */
	public static function executeFileQueries($path)
	{
		if (JFile::exists($path))
		{
			$queryString = file_get_contents($path);

			// Graceful exit and rollback if read not successful
			if ($queryString === false)
			{
				JLog::add(JText::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'), JLog::WARNING, 'jerror');

				return false;
			}

			$db = JFactory::getDbo();
			$queries = RHelperDatabase::splitSql($queryString);

			if (count($queries) == 0)
			{
				// No queries to process
				return 0;
			}

			// Process each query in the $queries array (split out of sql file).
			foreach ($queries as $query)
			{
				$query = trim($query);

				if ($query != '' && $query{0} != '#')
				{
					$db->setQuery($query);

					if (!$db->execute())
					{
						JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)), JLog::WARNING, 'jerror');

						return false;
					}
				}
			}
		}

		return true;
	}
}

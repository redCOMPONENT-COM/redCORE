<?php
/**
 * @package     Redcore
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * MySQL database driver
 *
 * @package     Redcore
 * @subpackage  Database
 * @since       1.0
 */
class RDatabaseDriverMysql extends JDatabaseDriverMysql
{
	/**
	 * We can choose not to translate query with this variable
	 *
	 * @var  boolean
	 */
	public $translate = false;

	/**
	 * This function replaces a string identifier <var>$prefix</var> with the string held is the
	 * <var>tablePrefix</var> class variable.
	 *
	 * @param   string  $sql           The SQL statement to prepare.
	 * @param   string  $prefix        The common table prefix.
	 * @param   bool    $insideQuotes  Replace prefix inside quotes too
	 *
	 * @return  string  The processed SQL statement.
	 *
	 * @since   11.1
	 */
	public function replacePrefix($sql, $prefix = '#__', $insideQuotes = false)
	{
		// Basic check for translations
		if ($this->translate)
		{
			if ($parsedSql = RDatabaseSqlparserSqltranslation::buildTranslationQuery($sql, $prefix))
			{
				return RHelperDatabase::replacePrefix($parsedSql, $this->tablePrefix, $prefix, $insideQuotes);
			}
		}

		return RHelperDatabase::replacePrefix($sql, $this->tablePrefix, $prefix, $insideQuotes);
	}

	/**
	 * Execute the SQL statement.
	 *
	 * @param   boolean  $replacePrefixQuotes  Replace the prefixes inside the quotes too
	 *
	 * @return  mixed  A database cursor resource on success, boolean false on failure.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function execute($replacePrefixQuotes = false)
	{
		if ($replacePrefixQuotes)
		{
			$this->sql = $this->replacePrefix((string) $this->sql, '#__', $replacePrefixQuotes);
		}

		return parent::execute();
	}
}

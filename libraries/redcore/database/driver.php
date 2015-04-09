<?php
/**
 * @package     Redcore
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

if (version_compare(JVERSION, '3.0', 'lt'))
{
	/**
	 * Database Driver Class extends Joomla Platform Database Driver Class
	 *
	 * @package     Redcore
	 * @subpackage  Database
	 * @since       1.0
	 *
	 * @method      string  q()   q($text, $escape = true)  Alias for quote method
	 * @method      string  qn()  qn($name, $as = null)     Alias for quoteName method
	 */
	abstract class RDatabaseDriver extends JDatabase
	{
		/**
		 * Method to return a JDatabase instance based on the given options.  There are three global options and then
		 * the rest are specific to the database driver.  The 'driver' option defines which JDatabaseDriver class is
		 * used for the connection -- the default is 'mysql'.  The 'database' option determines which database is to
		 * be used for the connection.  The 'select' option determines whether the connector should automatically select
		 * the chosen database.
		 *
		 * Instances are unique to the given options and new objects are only created when a unique options array is
		 * passed into the method.  This ensures that we don't end up with unnecessary database connection resources.
		 *
		 * @param   array  $options  Parameters to be passed to the database driver.
		 *
		 * @return  JDatabase  A database object.
		 *
		 * @since   11.1
		 */
		public static function getInstance($options = array())
		{
			// Sanitize the database connector options.
			$options['driver'] = (isset($options['driver'])) ? preg_replace('/[^A-Z0-9_\.-]/i', '', $options['driver']) : 'mysql';
			$options['database'] = (isset($options['database'])) ? $options['database'] : null;
			$options['select'] = (isset($options['select'])) ? $options['select'] : true;

			// Get the options signature for the database connector.
			$signature = md5(serialize($options));

			// If we already have a database connector instance for these options then just use that.
			if (empty(self::$instances[$signature]))
			{
				// Derive the class name from the driver.
				$class = 'RDatabase' . ucfirst($options['driver']);

				// If the class doesn't exist, let's look for it and register it.
				if (!class_exists($class))
				{
					// Derive the file path for the driver class.
					$path = dirname(__FILE__) . '/database/' . $options['driver'] . '.php';

					// If the file exists register the class with our class loader.
					if (file_exists($path))
					{
						JLoader::register($class, $path);
					}
					// If it doesn't exist we are at an impasse so throw an exception.
					else
					{
						// Legacy error handling switch based on the JError::$legacy switch.
						// @deprecated  12.1

						if (JError::$legacy)
						{
							// Deprecation warning.
							JLog::add('JError is deprecated.', JLog::WARNING, 'deprecated');
							JError::setErrorHandling(E_ERROR, 'die');

							return JError::raiseError(500, JText::sprintf('JLIB_DATABASE_ERROR_LOAD_DATABASE_DRIVER', $options['driver']));
						}
						else
						{
							throw new JDatabaseException(JText::sprintf('JLIB_DATABASE_ERROR_LOAD_DATABASE_DRIVER', $options['driver']));
						}
					}
				}

				// If the class still doesn't exist we have nothing left to do but throw an exception.  We did our best.
				if (!class_exists($class))
				{
					// Legacy error handling switch based on the JError::$legacy switch.
					// @deprecated  12.1

					if (JError::$legacy)
					{
						// Deprecation warning.
						JLog::add('JError() is deprecated.', JLog::WARNING, 'deprecated');

						JError::setErrorHandling(E_ERROR, 'die');

						return JError::raiseError(500, JText::sprintf('JLIB_DATABASE_ERROR_LOAD_DATABASE_DRIVER', $options['driver']));
					}
					else
					{
						throw new JDatabaseException(JText::sprintf('JLIB_DATABASE_ERROR_LOAD_DATABASE_DRIVER', $options['driver']));
					}
				}

				// Create our new JDatabase connector based on the options given.
				try
				{
					$instance = $class::getInstance($options);
				}
				catch (JDatabaseException $e)
				{

					// Legacy error handling switch based on the JError::$legacy switch.
					// @deprecated  12.1

					if (JError::$legacy)
					{
						// Deprecation warning.
						JLog::add('JError() is deprecated.', JLog::WARNING, 'deprecated');

						JError::setErrorHandling(E_ERROR, 'ignore');

						return JError::raiseError(500, JText::sprintf('JLIB_DATABASE_ERROR_CONNECT_DATABASE', $e->getMessage()));
					}
					else
					{
						throw new JDatabaseException(JText::sprintf('JLIB_DATABASE_ERROR_CONNECT_DATABASE', $e->getMessage()));
					}
				}

				// Set the new connector to the global instances based on signature.
				self::$instances[$signature] = $instance;
			}

			return self::$instances[$signature];
		}

		/**
		 * Method to delete current instances so we can create override
		 *
		 * @return  void
		 *
		 * @since   11.1
		 */
		public static function deleteInstances()
		{
			self::$instances = null;
		}
	}
}
else
{
	/**
	 * Database Driver Class extends Joomla Platform Database Driver Class
	 *
	 * @package     Redcore
	 * @subpackage  Database
	 * @since       1.0
	 *
	 * @method      string  q()   q($text, $escape = true)  Alias for quote method
	 * @method      string  qn()  qn($name, $as = null)     Alias for quoteName method
	 */
	abstract class RDatabaseDriver extends JDatabaseDriver
	{
		/**
		 * Method to return a JDatabaseDriver instance based on the given options.  There are three global options and then
		 * the rest are specific to the database driver.  The 'driver' option defines which JDatabaseDriver class is
		 * used for the connection -- the default is 'mysqli'.  The 'database' option determines which database is to
		 * be used for the connection.  The 'select' option determines whether the connector should automatically select
		 * the chosen database.
		 *
		 * Instances are unique to the given options and new objects are only created when a unique options array is
		 * passed into the method.  This ensures that we don't end up with unnecessary database connection resources.
		 *
		 * @param   array  $options  Parameters to be passed to the database driver.
		 *
		 * @return  JDatabaseDriver  A database object.
		 *
		 * @since   11.1
		 * @throws  RuntimeException
		 */
		public static function getInstance($options = array())
		{
			// Sanitize the database connector options.
			$options['driver']   = (isset($options['driver'])) ? preg_replace('/[^A-Z0-9_\.-]/i', '', $options['driver']) : 'mysqli';
			$options['database'] = (isset($options['database'])) ? $options['database'] : null;
			$options['select']   = (isset($options['select'])) ? $options['select'] : true;

			// Get the options signature for the database connector.
			$signature = md5(serialize($options));

			// If we already have a database connector instance for these options then just use that.
			if (empty(self::$instances[$signature]))
			{
				// Derive the class name from the driver.
				$class = 'RDatabaseDriver' . ucfirst(strtolower($options['driver']));

				// If the class still doesn't exist we have nothing left to do but throw an exception.  We did our best.
				if (!class_exists($class))
				{
					// Only display error in admin
					if (JFactory::getApplication()->isAdmin())
					{
						JFactory::getApplication()->enqueueMessage(JText::sprintf('LIB_REDCORE_TRANSLATIONS_DRIVER_ERROR', $options['driver']), 'error');
					}

					// We will disable plugin option in this instance so we do not try to translate
					if (!empty(RTranslationHelper::$pluginParams))
					{
						RTranslationHelper::$pluginParams->set('enable_translations', 0);
					}
					// We are not supporting this driver
					return parent::getInstance($options);
				}

				// Create our new JDatabaseDriver connector based on the options given.
				try
				{
					$instance = new $class($options);
				}
				catch (RuntimeException $e)
				{
					throw new RuntimeException(sprintf('Unable to connect to the Database: %s', $e->getMessage()));
				}

				// Set the new connector to the global instances based on signature.
				self::$instances[$signature] = $instance;
			}

			return self::$instances[$signature];
		}

		/**
		 * Method to delete current instances so we can create override
		 *
		 * @return  void
		 *
		 * @since   11.1
		 */
		public static function deleteInstances()
		{
			self::$instances = null;
		}

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
			$line = '';
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

				$fileend = ($i == $end - 1);

				if (!$open || $fileend)
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

					if ((!$delimiterChange && $latest == $delimiter) || $fileend)
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
		 * @param   string  $insideQuotes  Replace prefix inside quotes too
		 *
		 * @return  string  The processed SQL statement.
		 *
		 * @since   1.5
		 */
		public static function replacePrefixRC($sql, $tablePrefix, $prefix = '#__', $insideQuotes = false)
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
}

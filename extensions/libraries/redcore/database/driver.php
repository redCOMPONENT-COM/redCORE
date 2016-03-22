<?php
/**
 * @package     Redcore
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
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

			// If the selected driver is `mysql` and we are on PHP 7 or greater, switch to the `mysqli` driver.
			if ($options['driver'] === 'mysql' && PHP_MAJOR_VERSION >= 7)
			{
				// Check if we have support for the other MySQL drivers
				$mysqliSupported   = JDatabaseDriverMysqli::isSupported();
				$pdoMysqlSupported = JDatabaseDriverPdomysql::isSupported();

				// If neither is supported, then the user cannot use MySQL; throw an exception
				if (!$mysqliSupported && !$pdoMysqlSupported)
				{
					throw new RuntimeException(
						'The PHP `ext/mysql` extension is removed in PHP 7, cannot use the `mysql` driver.'
						. ' Also, this system does not support MySQLi or PDO MySQL.  Cannot instantiate database driver.'
					);
				}

				// Prefer MySQLi as it is a closer replacement for the removed MySQL driver, otherwise use the PDO driver
				if ($mysqliSupported)
				{
					JLog::add(
						'The PHP `ext/mysql` extension is removed in PHP 7, cannot use the `mysql` driver.  Trying `mysqli` instead.',
						JLog::WARNING,
						'deprecated'
					);

					$options['driver'] = 'mysqli';
				}
				else
				{
					JLog::add(
						'The PHP `ext/mysql` extension is removed in PHP 7, cannot use the `mysql` driver.  Trying `pdomysql` instead.',
						JLog::WARNING,
						'deprecated'
					);

					$options['driver'] = 'pdomysql';
				}
			}

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

					// Initialize Config if not set
					RBootstrap::getConfig('enable_translations');

					// We will disable plugin option in this instance so we do not try to translate
					RBootstrap::$config->set('enable_translations', 0);

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
	}
}

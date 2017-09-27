<?php
/**
 * @package     Redcore
 * @subpackage  Factory
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Factory class.
 *
 * @package     Redcore
 * @subpackage  Factory
 * @since       1.0
 */
final class RFactory extends JFactory
{
	/**
	 * The dispatcher.
	 *
	 * @var  JEventDispatcher
	 */
	public static $dispatcher = null;

	/**
	 * Get the event dispatcher
	 *
	 * @return  JEventDispatcher
	 */
	public static function getDispatcher()
	{
		if (!self::$dispatcher)
		{
			self::$dispatcher = version_compare(JVERSION, '3.0', 'lt') ?
				JDispatcher::getInstance() : JEventDispatcher::getInstance();
		}

		return self::$dispatcher;
	}

	/**
	 * Get a database object.
	 *
	 * Returns the global {@link JDatabaseDriver} object, only creating it if it doesn't already exist.
	 *
	 * @return  RDatabaseDriver
	 *
	 * @see     JDatabaseDriver
	 * @since   11.1
	 */
	public static function getDbo()
	{
		if (!self::$database)
		{
			self::$database = self::createDbo();
		}

		return self::$database;
	}

	/**
	 * Create an database object
	 *
	 * @return  RDatabaseDriver
	 *
	 * @see     JDatabaseDriver
	 * @since   11.1
	 */
	protected static function createDbo()
	{
		$conf = self::getConfig();

		$host = $conf->get('host');
		$user = $conf->get('user');
		$password = $conf->get('password');
		$database = $conf->get('db');
		$prefix = $conf->get('dbprefix');
		$driver = $conf->get('dbtype');
		$debug = $conf->get('debug');

		$options = array('driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix);

		try
		{
			RDatabaseDriver::deleteInstances();
			$db = RDatabaseDriver::getInstance($options);
		}
		catch (RuntimeException $e)
		{
			if (!headers_sent())
			{
				header('HTTP/1.1 500 Internal Server Error');
			}

			jexit('Database Error: ' . $e->getMessage());
		}

		$db->setDebug($debug);

		return $db;
	}

	/**
	 * Create a mailer object
	 *
	 * @return  JMail object
	 *
	 * @see     JMail
	 * @since   11.1
	 */
	protected static function createMailer()
	{
		$conf = self::getConfig();

		$smtpauth = ($conf->get('smtpauth') == 0) ? null : 1;
		$smtpuser = $conf->get('smtpuser');
		$smtppass = $conf->get('smtppass');
		$smtphost = $conf->get('smtphost');
		$smtpsecure = $conf->get('smtpsecure');
		$smtpport = $conf->get('smtpport');
		$mailfrom = $conf->get('mailfrom');
		$fromname = $conf->get('fromname');
		$mailer = $conf->get('mailer');

		// Create a JMail object
		$mail = RMail::getInstance();

		// Clean the email address
		$mailfrom = JMailHelper::cleanLine($mailfrom);

		// Set default sender without Reply-to if the mailfrom is a valid address
		if (JMailHelper::isEmailAddress($mailfrom))
		{
			// Wrap in try/catch to catch phpmailerExceptions if it is throwing them
			try
			{
				// Check for a false return value if exception throwing is disabled
				if ($mail->setFrom($mailfrom, JMailHelper::cleanLine($fromname), false) === false)
				{
					JLog::add(__METHOD__ . '() could not set the sender data.', JLog::WARNING, 'mail');
				}
			}
			catch (phpmailerException $e)
			{
				JLog::add(__METHOD__ . '() could not set the sender data.', JLog::WARNING, 'mail');
			}
		}

		// Default mailer is to use PHP's mail function
		switch ($mailer)
		{
			case 'smtp':
				$mail->useSmtp($smtpauth, $smtphost, $smtpuser, $smtppass, $smtpsecure, $smtpport);
				break;

			case 'sendmail':
				$mail->isSendmail();
				break;

			default:
				$mail->isMail();
				break;
		}

		return $mail;
	}
}

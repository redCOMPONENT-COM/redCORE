<?php
/**
 * @package     Redcore
 * @subpackage  Library
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Email Class.  Provides a common interface to send email from the Joomla! Platform
 *
 * @package     Redcore
 * @subpackage  Mail
 * @since       1.8
 */
class RMail extends JMail
{
	/**
	 * Returns the global email object, only creating it if it doesn't already exist.
	 * Fixes it to backward compatible by turning exception to Off as a default value
	 *
	 * NOTE: If you need an instance to use that does not have the global configuration
	 * values, use an id string that is not 'Joomla'.
	 *
	 * @param   string   $id          The id string for the JMail instance [optional]
	 * @param   boolean  $exceptions  Flag if Exceptions should be thrown [optional]
	 *
	 * @return  JMail  The global JMail object
	 *
	 * @since   11.1
	 */
	public static function getInstance($id = 'Joomla', $exceptions = null)
	{
		// We are doing backward compatible call without exceptions as a result. It will return a boolean instead
		if (is_null($exceptions))
		{
			$exceptions = false;
		}

		return parent::getInstance($id, $exceptions);
	}

	/**
	 * Send the mail.
	 * Fixes it to backward compatibility of not throwing exceptions as a result which is expected to be a boolean
	 *
	 * @return  mixed  True if successful; JError if using legacy tree (no exception thrown in that case).
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public function Send()
	{
		// If this parameter is set and set to true, it will return exception if it has occurred
		if (!empty($this->exceptions))
		{
			return parent::Send();
		}

		// If we are not expecting exceptions we will return boolean
		return parent::Send() === true;
	}
}

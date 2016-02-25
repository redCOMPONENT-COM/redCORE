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
 * User class.
 *
 * @package     Redcore
 * @subpackage  User
 * @since       1.4
 */
final class RUser
{
	/**
	 * Login the user through his credentials
	 *
	 * @param   array  $credentials  Credentials
	 *
	 * @return  boolean  True on success
	 */
	public static function userLogin($credentials)
	{
		$login = JFactory::getApplication()->login($credentials);

		if ($login)
		{
			$user = JFactory::getUser();

			// Load the JUser class on application for this client
			JFactory::getApplication()->loadIdentity($user);
		}

		return $login;
	}

	/**
	 * Logout the user
	 *
	 * @return  boolean  True on success
	 */
	public static function userLogout()
	{
		$user = JFactory::getUser();

		if (!$user->guest)
		{
			JLoader::import('joomla.user.authentication');
			$loginOptions = array('remember' => false);
			$logout = array('username' => $user->username);

			return JFactory::getApplication()->triggerEvent('onLogoutUser', array($logout, $loginOptions));
		}

		return true;
	}
}

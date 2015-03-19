<?php
/**
 * @package     Redcore
 * @subpackage  Factory
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
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
		JLoader::import('joomla.user.authentication');
		JLoader::import('joomla.user.helper');
		JPluginHelper::importPlugin('user');
		$loginOptions = array('remember' => false);
		$authentication = JAuthentication::getInstance()->authenticate($credentials, $loginOptions);

		// Skip two factor authentication
		if ($authentication->status != JAuthentication::STATUS_SUCCESS && method_exists('JUserHelper', 'verifyPassword'))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('id, password')
				->from('#__users')
				->where('username=' . $db->q($credentials['username']));
			$db->setQuery($query);
			$user = $db->loadObject();

			if ($user)
			{
				$match = JUserHelper::verifyPassword($credentials['password'], $user->password, $user->id);

				if ($match === true)
				{
					$userObject = JUser::getInstance($user->id);
					$authentication->fullname = $userObject->name;
					$authentication->email = $userObject->email;
					$authentication->language = JFactory::getApplication()->isAdmin() ? $userObject->getParam('admin_language') : $userObject->getParam('language');
					$authentication->status = JAuthentication::STATUS_SUCCESS;
					$authentication->error_message = '';
				}
			}
		}

		if ($authentication->status == JAuthentication::STATUS_SUCCESS)
		{
			JFactory::getApplication()->triggerEvent('onLoginUser', array((array) $authentication, $loginOptions));

			$userId = JUserHelper::getUserId($authentication->username);
			$userObject = JFactory::getUser($userId);
			$session = JFactory::getSession();
			$session->set('user', $userObject);

			return true;
		}

		return false;
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

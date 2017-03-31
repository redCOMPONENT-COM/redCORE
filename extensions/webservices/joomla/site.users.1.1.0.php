<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2017 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * Api Helper class for overriding default methods
 *
 * @package     Redcore
 * @subpackage  Api Helper
 * @since       1.8
 */
class RApiHalHelperSiteUsers
{
	/**
	 * Service for reset password of user when they forgot password.
	 *
	 * @param   string  $email  Email of user account
	 *
	 * @return  boolean         True on success. False otherwise.
	 */
	public function forgotPassword($email)
	{
		// Load language from com_users
		$language = JFactory::getLanguage();
		$language->load('com_users');

		// Load stuff from com_users
		jimport('joomla.application.component.model');
		JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_users/models');
		JForm::addFormPath(JPATH_SITE . '/components/com_users/models/forms');
		JForm::addFieldPath(JPATH_SITE . '/components/com_users/models/fields');
		JLoader::import('route', JPATH_SITE . '/components/com_users/helpers');

		$model = RModel::getFrontInstance('Reset', array('ignore_request' => true), 'com_users');
		$data  = array('email' => $email);

		// Submit the password reset request.
		$return	= $model->processResetRequest($data);

		return (boolean) $return;
	}

	/**
	 * Service for get username when they forgot username.
	 *
	 * @param   string  $email  Email of user account
	 *
	 * @return  boolean         True on success. False otherwise.
	 */
	public function forgotUsername($email)
	{
		// Load language from com_users
		$language = JFactory::getLanguage();
		$language->load('com_users');

		// Load stuff from com_users
		jimport('joomla.application.component.model');
		JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_users/models');
		JForm::addFormPath(JPATH_SITE . '/components/com_users/models/forms');
		JForm::addFieldPath(JPATH_SITE . '/components/com_users/models/fields');
		JLoader::import('route', JPATH_SITE . '/components/com_users/helpers');

		$model = RModel::getFrontInstance('Remind', array('ignore_request' => true), 'com_users');
		$data  = array('email' => $email);

		// Submit the password reset request.
		$return	= $model->processRemindRequest($data);

		return (boolean) $return;
	}

	/**
	 * Service to authenticate a user
	 *
	 * @param   string  $email  Email of user account
	 *
	 * @return  boolean|object	User information or false 
	 */
	public function authenticateUser($email)
	{
		$session = JFactory::getSession();

		// Get user information
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select(array($db->qn('id'), $db->qn('name'), $db->qn('username'), $db->qn('email')))
			->from($db->qn('#__users'))
			->where($db->qn('email') . ' = ' . $db->q($email));

		$user = $db->setQuery($query)->loadObject();

		if (!$user->id)
		{
			// We didn't find a user, so we destroy the session
			$session->destroy();

			return false;
		}

		JPluginHelper::importPlugin('user');

		$options = array(
			'action' => 'core.login.site'
		);

		$response = new stdClass;
		$response->username = $user->username;

		$app = JFactory::getApplication('site');

		// We trigger the login
		if (!$app->triggerEvent('onUserLogin', array( (array) $response, $options)))
		{
			// Login failed, so we destroy the session
			$session->destroy();

			return false;
		}

		// Get user groups
		$query = $db->getQuery(true)
			->select(array($db->qn('um.group_id'), $db->qn('ug.title', 'group_title')))
			->from($db->qn('#__user_usergroup_map', 'um'))
			->join('INNER', $db->qn('#__usergroups', 'ug') . ' ON ' . $db->qn('um.group_id') . ' = ' . $db->qn('ug.id'))
			->where($db->qn('um.user_id') . ' = ' . (int) $user->id);

		$userGroups = $db->setQuery($query)->loadObjectList();

		$user->user_groups = $userGroups;

		// Set access token to expire for security reasons
		$accessTokenParamName = RBootstrap::getConfig('oauth2_token_param_name', 'access_token');
		$accessTokenKey = $app->input->get($accessTokenParamName);

		$query = $db->getQuery(true)
			->update('#__redcore_oauth_access_tokens')
			->set($db->qn('expires') . ' = NOW()')
			->where($db->qn('access_token') . ' = ' . $db->q($accessTokenKey));
		$db->setQuery($query);
		$db->execute();

		return $user;
	}
}

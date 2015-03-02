<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Api Helper class for overriding default methods
 *
 * @package     Redcore
 * @subpackage  Api Helper
 * @since       1.2
 */
class RApiHalHelperSiteUsers
{
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

		$resetModel = RModel::getFrontInstance('Reset', array('ignore_request' => true), 'com_users');
		$data = array('email' => $email);

		// Submit the password reset request.
		$return	= $resetModel->processResetRequest($data);

		return (boolean) $return;
	}
}

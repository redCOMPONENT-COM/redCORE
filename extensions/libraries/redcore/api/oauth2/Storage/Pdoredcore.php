<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
namespace OAuth2\Storage;

defined('JPATH_REDCORE') or die;

/**
 * Extended PDO storage for all storage types
 *
 * @since  1.2
 */
class Pdoredcore extends Pdo
{
	/**
	 * Grant access tokens for basic user credentials.
	 * Check the supplied username and password for validity.
	 *
	 * You can also use the $client_id param to do any checks required based
	 * on a client, if you need that.
	 *
	 * Required for OAuth2::GRANT_TYPE_USER_CREDENTIALS.
	 *
	 * @param   string  $username  Username to be check with.
	 * @param   string  $password  Password to be check with.
	 *
	 * @return boolean  TRUE if the username and password are valid, and FALSE if it isn't.
	 * Moreover, if the username and password are valid, and you want to
	 *
	 * @see http://tools.ietf.org/html/rfc6749#section-4.3
	 *
	 * @ingroup oauth2_section_4
	 */
	public function checkUserCredentials($username, $password)
	{
		$credentials = array('username' => $username, 'password' => $password);
		$response = \RUser::userLogin($credentials);

		return $response;
	}

	/**
	 * Gets user details
	 *
	 * @param   string  $username  Username to be check with.
	 *
	 * @return  array  The associated "user_id" and optional "scope" values.
	 * This function MUST return FALSE if the requested user does not exist or is
	 * invalid. "scope" is a space-separated list of restricted scopes.
	 *
	 * @code
	 * return array(
	 *     "user_id"  => USER_ID,    // REQUIRED user_id to be stored with the authorization code or access token
	 *     "scope"    => SCOPE       // OPTIONAL space-separated list of restricted scopes
	 * );
	 */
	public function getUserDetails($username)
	{
		$user = \JFactory::getUser();
		$request = \OAuth2\Request::createFromGlobals();

		// We load scopes from client
		$clientId = $request->request('client_id');
		$scopes = $this->getClientScope($clientId);

		return array (
			"user_id"   => $user->get('id'),
			"username"  => $user->get('username'),
			"name"      => $user->get('name'),
			"scope"     => $scopes,
		);
	}
}

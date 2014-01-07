<?php
/**
 * @package     Redcore
 * @subpackage  OAuth2
 *
 * This work is based on a Louis Landry work about oauth1 server suport for Joomla! Platform.
 * URL: https://github.com/LouisLandry/joomla-platform/tree/9bc988185ccc3e1c437256cc2c927e49312b3d00/libraries/joomla/oauth1
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

/**
 * OAuth PLAINTEXT Signature Method class for the redCORE.
 *
 * @package     Redcore
 * @subpackage  OAuth2
 * @since       1.0
 */
class ROauth2CredentialsSignerPlaintext extends ROauth2CredentialsSigner
{
	/**
	 * Perform a password authentication challenge.
	 *
	 * @param   ROauth2CredentialsStateToken  $state    The state object
	 * @param   ROauth2ProtocolRequest        $request  The request object.
	 *
	 * @return  boolean  True if authentication is ok, false if not
	 *
	 * @since   1.0
	 */
	public function sign(ROauth2CredentialsStateToken $state, ROauth2ProtocolRequest $request)
	{
		$request = $request->getParameters();

		if ($state->__get('client_id') != $request['client_id'])
		{
			return false;
		}

		if ($state->__get('access_token') != $request['access_token'])
		{
			return false;
		}

		return true;
	}

	/**
	 * Decode the client secret key
	 *
	 * @param   string  $clientSecret  The OAuth client's secret.
	 *
	 * @return  string  The decoded key
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function secretDecode($clientSecret)
	{
		$clientSecret = explode(":", base64_decode($clientSecret));
		$clientSecret = $clientSecret[1];

		return $clientSecret;
	}
}

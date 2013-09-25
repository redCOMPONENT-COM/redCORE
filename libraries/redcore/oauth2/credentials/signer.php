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
 * OAuth message signer interface for the redCORe.
 *
 * @package     Redcore
 * @subpackage  OAuth2
 * @since       1.0
 */
class ROauth2CredentialsSigner
{
	/**
	 * Method to get a message signer object based on the message's oauth_signature_method parameter.
	 *
	 * @param   string  $method  The method of the signer (HMAC-SHA1 || RSA-SHA1 || PLAINTEXT)
	 *
	 * @return  ROauth2CredentialsSigner  The OAuth message signer object for the message.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public static function getInstance($method)
	{
		switch ($method)
		{
			case 'HMAC-SHA1':
				$signer = new ROauth2CredentialsSignerHMAC;
				break;
			case 'RSA-SHA1':
				// @TODO We don't support RSA because we don't yet have a way to inject the private key.
				throw new InvalidArgumentException('RSA signatures are not supported');
				break;
			case 'PLAINTEXT':
				$signer = new ROauth2CredentialsSignerPlaintext;
				break;
			default:
				throw new InvalidArgumentException('No valid signature method was found.');
				break;
		}

		return $signer;
	}

	/**
	 * Perform a password authentication challenge.
	 *
	 * @param   ROauth2Client  $client   The client object
	 * @param   string         $request  The request object.
	 *
	 * @return  boolean  True if authentication is ok, false if not
	 *
	 * @since   1.0
	 */
	public function doJoomlaAuthentication(ROauth2Client $client, $request)
	{
		// Build the response for the client.
		$types = array('PHP_AUTH_', 'PHP_HTTP_', 'PHP_');

		foreach ( $types as $type )
		{
			if (isset($request->_headers[$type . 'USER']))
			{
				$user_decode = base64_decode($request->_headers[$type . 'USER']);
			}

			if (isset($request->_headers[$type . 'PW']))
			{
				$password_decode = base64_decode($request->_headers[$type . 'PW']);
			}
		}

		// Check if the username and password are present
		if ( !isset($user_decode) || !isset($password_decode) )
		{
			if (isset($request->client_id))
			{
				$user_decode = explode(":", base64_decode($request->client_id));
				$user_decode = $user_decode[0];
			}

			if (isset($request->client_secret))
			{
				$password_decode = explode(":", base64_decode($request->client_secret));
				$password_decode = base64_decode($password_decode[1]);
				$password_decode = explode(":", $password_decode);
				$password_decode = $password_decode[0];
			}
		}

		// Check if the username and password are present
		if (!isset($user_decode) || !isset($password_decode))
		{
			throw new Exception('Username or password is not set');
			exit;
		}

		// Explode the user
		$parts	= explode(':', $user_decode);
		$user	= $parts[0];

		// Explode the password
		$parts	= explode(':', $password_decode);
		$password_clean	= $parts[0];

		// Check the password
		$parts	= explode(':', $client->_identity->password);
		$crypt	= $parts[0];

		// Get the salt
		$salt	= @$parts[1];

		// Crypt the user password
		$testcrypt = JUserHelper::getCryptedPassword($password_clean, $salt);

		// Compare the password's
		if ($crypt != $testcrypt)
		{
			throw new Exception('Username or password do not match');
			exit;
		}

		return true;
	}
}

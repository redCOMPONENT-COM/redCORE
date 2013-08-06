<?php
/**
 * @package     RedRad
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
 * OAuth PLAINTEXT Signature Method class for the RedRAD.
 *
 * @package     RedRAD
 * @subpackage  OAuth2
 * @since       1.0
 */
class ROauth2CredentialsSignerPlaintext extends ROauth2CredentialsSigner
{
	/**
	 * Calculate and return the OAuth message signature using PLAINTEXT
	 *
	 * @param   string  $baseString        The OAuth message as a normalized base string.
	 * @param   string  $clientSecret      The OAuth client's secret.
	 * @param   string  $credentialSecret  The OAuth credentials' secret.
	 *
	 * @return  string  The OAuth message signature.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function sign($baseString, $clientSecret, $credentialSecret)
	{
		return $clientSecret . '&' . $credentialSecret;
	}

	/**
	 * Decode the client secret key
	 *
	 * @param   string  $clientSecret      The OAuth client's secret.
	 *
	 * @return  string  The decoded key
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function clientDecode($clientSecret)
	{
		return explode(":", base64_decode($clientSecret))[1];
	}
}

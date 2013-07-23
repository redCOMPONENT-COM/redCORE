<?php
/**
 * @package     Joomla.Platform
 * @subpackage  OAuth2
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * OAuth message signer interface for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth2
 * @since       1.0
 */
class ROAuth2MessageSigner
{
	/**
	 * Method to get a message signer object based on the message's oauth_signature_method parameter.
	 *
	 * @return  ROAuth2MessageSigner  The OAuth message signer object for the message.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public static function getInstance($method)
	{
		// Register the classes for autoload.
		JLoader::registerPrefix('ROAuth2', JPATH_REDRAD.'/oauth2/protocol');

		// Setup the autoloader for the application classes.
		JLoader::register('ROAuth2MessageSigner', JPATH_REDRAD.'/oauth2/protocol/signer.php');

		switch ($method)
		{
			case 'HMAC-SHA1':
				$signer = new ROAuth2MessageSignerHMAC;
				break;
			case 'RSA-SHA1':
				// @TODO We don't support RSA because we don't yet have a way to inject the private key.
				throw new InvalidArgumentException('RSA signatures are not supported');
				$signer = new ROAuth2MessageSignerRSA;
				break;
			case 'PLAINTEXT':

				// Setup the autoloader for the application classes.
				JLoader::register('ROAuth2MessageSignerPlaintext', JPATH_REDRAD.'/oauth2/protocol/signer/plaintext.php');

				$signer = new ROAuth2MessageSignerPlaintext;
				break;
			default:
				throw new InvalidArgumentException('No valid signature method was found.');
				break;
		}

		return $signer;
	}

	/**
	 * Calculate and return the OAuth message signature.
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
	//public function sign($baseString, $clientSecret, $credentialSecret){};
}

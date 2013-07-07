<?php
/**
 * @package     Joomla.Platform
 * @subpackage  OAuth2
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * OAuth PLAINTEXT Signature Method class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth1
 * @since       1.0
 */
class ROAuth2MessageSignerPlaintext implements ROAuth2MessageSigner
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
}

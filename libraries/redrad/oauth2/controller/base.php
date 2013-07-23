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
 * OAuth Controller class for initiating temporary credentials for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth2
 * @since       1.0
 */
class ROAuth2ControllerBase extends JControllerBase
{

	function execute() {}

	/**
	 * Create the credentials
	 *
	 * @return  ROAuth2Credentials
	 *
	 * @since   1.0
	 */
	protected function createCredentials()
	{
		return new ROAuth2Credentials;
	}

	/**
	 * Get an OAuth 2.0 client object based on the request message.
	 *
	 * @param   string  $consumerKey  The OAuth 2.0 consumer_key parameter for which to load the client.
	 *
	 * @return  ROAuth2Client
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function fetchClient($client_id)
	{
		$client_id = base64_decode($client_id);
		$client_id = explode(":", $client_id);
		$client_id = $client_id[0];

		// Ensure there is a consumer key.
		if (empty($client_id))
		{
			throw new InvalidArgumentException('There is no OAuth consumer key in the request.');
		}

		// Get an OAuth client object and load it using the incoming client key.
		$client = new ROAuth2Client;
		$client->loadByKey($client_id);

		// Verify the client key for the message.
		if ($client->username != $client_id)
		{
			throw new InvalidArgumentException('The OAuth consumer key is not valid.');
		}

		return $client;
	}

} // end class

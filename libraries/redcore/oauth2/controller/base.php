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
 * OAuth Controller class for initiating temporary credentials for the redCORE.
 *
 * @package     Redcore
 * @subpackage  OAuth2
 * @since       1.0
 */
class ROauth2ControllerBase extends JControllerBase
{
	/**
	 * Method required by JControllerBase
	 *
	 * @return  none
	 *
	 * @since   1.0
	 */
	public function execute()
	{
	}

	/**
	 * Initialise the controller
	 *
	 * @return  none
	 *
	 * @since   1.0
	 */
	protected function initialise()
	{
		// Verify that we have an OAuth 2.0 application.
		if ((!$this->app instanceof ApiApplicationWeb))
		{
			$this->respondError(400, 'invalid_request', 'Cannot perform OAuth 2.0 authorisation without an OAuth 2.0 application.');
		}

		// We need a valid signature to do initialisation.
		if (!isset($this->request->access_token) && (!$this->request->client_id || !$this->request->client_secret || !$this->request->signature_method) )
		{
			$this->respondError(400, 'invalid_request', 'Invalid OAuth request signature.');

			return false;
		}
	}

	/**
	 * Get an OAuth 2.0 client object based on the request message.
	 *
	 * @param   string  $client_id  The OAuth 2.0 client_id parameter for which to load the client.
	 *
	 * @return  ROauth2Client
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
			$this->respondError(400, 'unauthorized_client', 'There is no OAuth consumer key in the request.');
		}

		// Get an OAuth client object and load it using the incoming client key.
		$client = new ROauth2Client;
		$client->loadByKey($client_id);

		// Verify the client key for the message.
		if ($client->username != $client_id)
		{
			$this->respondError(400, 'unauthorized_client', 'The OAuth consumer key is not valid.');
		}

		return $client;
	}

	/**
	 * Return the JSON error based on RFC 6749 (http://tools.ietf.org/html/rfc6749#section-5.2)
	 *
	 * @param   int     $status   The HTTP protocol status. Default: 400 for errors
	 * @param   string  $code     The OAuth2 framework error code
	 * @param   string  $message  The error description
	 *
	 * @return  none
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function respondError($status, $code, $message)
	{
		$response = array(
			'error' => $code,
			'error_description' => $message
		);

		$this->response->setHeader('status', $status)
			->setBody(json_encode($response))
			->respond();

		exit;
	}
}

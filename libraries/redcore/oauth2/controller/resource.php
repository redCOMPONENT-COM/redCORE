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
class ROauth2ControllerResource extends ROauth2ControllerBase
{
	/**
	 * Constructor.
	 *
	 * @param   ROauth2ProtocolRequest   $request   The request object
	 * @param   ROauth2ProtocolResponse  $response  The response object
	 *
	 * @since   1.0
	 */
	public function __construct(ROauth2ProtocolRequest $request = null, ROauth2ProtocolResponse $response = null)
	{
		// Call parent first
		parent::__construct();

		// Setup the request object.
		$this->request = isset($request) ? $request : new ROauth2ProtocolRequest;

		// Setup the response object.
		$this->response = isset($response) ? $response : new ROauth2ProtocolResponse;
	}

	/**
	 * Handle the request.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function execute()
	{
		// Verify that we have an OAuth 2.0 application.
		$this->initialise();

		// Generate temporary credentials for the client.
		$credentials = new ROauth2Credentials($this->request);

		$credentials->load();

		// Getting the client object
		$client = $this->fetchClient($this->request->client_id);

		// Ensure the credentials are authorised.
		if ($credentials->getType() !== ROauth2Credentials::TOKEN)
		{
			$this->respondError(400, 'invalid_request', 'The token is not for a valid credentials yet.');
		}

		// Ensure the credentials are authorised.
		if (!$credentials->sign())
		{
			$this->respondError(400, 'unauthorized_client', 'Invalid sign');
		}

		// Load the JUser class on application for this client
		$this->app->loadIdentity($client->_identity);
	}
}

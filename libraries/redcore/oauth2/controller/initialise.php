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
class ROauth2ControllerInitialise extends ROauth2ControllerBase
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

		// Getting the client object
		$client = $this->fetchClient($this->request->client_id);

		// Doing authentication using Joomla! users
		$credentials->doJoomlaAuthentication($client);

		// Load the JUser class on application for this client
		$this->app->loadIdentity($client->_identity);

		// Initialize the credentials for this request
		$credentials->initialise(
			$client->_identity->username,
			$this->app->get('oauth.tokenlifetime', 'PT1H')
		);

		// Build the response for the client.
		$response = array(
			'oauth_code' => $credentials->getTemporaryToken(),
			'oauth_state' => true
		);

		// Set the response code and body.
		$this->response->setHeader('status', '200')
			->setBody(json_encode($response))
			->respond();
	}
}

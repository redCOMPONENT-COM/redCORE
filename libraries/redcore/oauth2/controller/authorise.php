<?php
/**
 * @package     Redcore
 * @subpackage  OAuth2
 *
 * This work is based on a Louis Landry work about oauth1 server support for Joomla! Platform.
 * URL: https://github.com/LouisLandry/joomla-platform/tree/9bc988185ccc3e1c437256cc2c927e49312b3d00/libraries/joomla/oauth1
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

/**
 * OAuth Controller class for authorising temporary credentials for the redCORE.
 *
 * According to RFC 5849, this must be handled using a GET request, so route accordingly. When implementing this in your own
 * app you should provide some means of protection against CSRF attacks.
 *
 * @package     Redcore
 * @subpackage  OAuth1
 * @since       12.3
 */
class ROauth2ControllerAuthorise extends ROauth2ControllerBase
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
	 * @since   12.3
	 */
	public function execute()
	{
		// Verify that we have an rest api application.
		$this->initialise();

		// Generate temporary credentials for the client.
		$credentials = new ROauth2Credentials($this->request);
		$credentials->load();

		// Getting the client object
		$client = $this->fetchClient($this->request->client_id);

		// Doing authentication using Joomla! users
		$credentials->doJoomlaAuthentication($client);

		// Load the JUser class on application for this client
		$this->app->loadIdentity($client->_identity);

		// Verify that we have a signed in user.
		if ($credentials->getTemporaryToken() !== $this->request->code)
		{
			$this->respondError(400, 'invalid_grant', 'Temporary token is not valid');
		}

		// Ensure the credentials are temporary.
		if ( (int) $credentials->getType() !== ROauth2Credentials::TEMPORARY)
		{
			$this->respondError(400, 'invalid_request', 'The token is not for a temporary credentials set.');
		}

		// Verify that we have a signed in user.
		if ($this->app->getIdentity()->get('guest'))
		{
			$this->respondError(400, 'unauthorized_client', 'You must first sign in.');
		}

		// Attempt to authorise the credentials for the current user.
		$credentials->authorise($this->app->getIdentity()->get('id'));

		/*
		if ($credentials->getCallbackUrl() && $credentials->getCallbackUrl() != 'oob')
		{
			$this->app->redirect($credentials->getCallbackUrl());

			return;
		}
		*/
		// Build the response for the client.
		$response = array(
			'oauth_code' => $credentials->getTemporaryToken(),
			'oauth_state' => true
		);

		// Set the response code and body.
		$this->response->setHeader('status', '200')
			->setBody(json_encode($response))
			->respond();
		exit;
	}
}

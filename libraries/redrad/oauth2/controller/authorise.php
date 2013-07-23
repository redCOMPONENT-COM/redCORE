<?php
/**
 * @package     Joomla.Platform
 * @subpackage  OAuth1
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/*
Step 2

   The client redirects Jane's user-agent to the server's Resource Owner
   Authorization endpoint to obtain Jane's approval for accessing her
   private photos:

     https://photos.example.net/authorise?oauth_token=hh5s93j4hdidpola

   The server requests Jane to sign in using her username and password
   and if successful, asks her to approve granting 'printer.example.com'
   access to her private photos.  Jane approves the request and her
   user-agent is redirected to the callback URI provided by the client
   in the previous request (line breaks are for display purposes only):

     http://printer.example.com/ready?
     oauth_token=hh5s93j4hdidpola&oauth_verifier=hfdp7dh39dks9884

 */

/**
 * OAuth Controller class for authorising temporary credentials for the Joomla Platform.
 *
 * According to RFC 5849, this must be handled using a GET request, so route accordingly. When implementing this in your own
 * app you should provide some means of protection against CSRF attacks.
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth1
 * @since       12.3
 */
class ROAuth2ControllerAuthorise extends ROAuth2ControllerBase
{
	/**
	 * Constructor.
	 *
	 * @param   JRegistry        $options      ROAuth2User options object
	 * @param   JHttp            $http         The HTTP client object
	 * @param   JInput           $input        The input object
	 * @param   JApplicationWeb  $application  The application object
	 *
	 * @since   1.0
	 */
	public function __construct(ROAuth2Request $request = null, ROAuth2Response $response = null)
	{
		// Call parent first
		parent::__construct();

		// Setup the autoloader for the application classes.
		JLoader::register('ROAuth2Request', JPATH_REDRAD.'/oauth2/protocol/request.php');
		JLoader::register('ROAuth2Response', JPATH_REDRAD.'/oauth2/protocol/response.php');

		$this->request = isset($request) ? $request : new ROAuth2Request;
		$this->response = isset($response) ? $response : new ROAuth2Response;
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
		// Verify that we have an OAuth 1.0 application.
		if ((!$this->app instanceof ApiApplicationWeb))
		{
			throw new LogicException('Cannot perform OAuth 2.0 authorisation without an RestFUL application.');
		}

		// Generate temporary credentials for the client.
		$credentials = $this->createCredentials();
		$credentials->load($this->request->client_secret);

		// Getting the client object
		$client = $this->fetchClient($this->request->client_id);

		// Doing authentication using Joomla! users
		$this->request->doOAuthAuthentication($client->_identity->password);

		// Load the JUser class on application for this client
		$this->app->loadIdentity($client->_identity);

		// Ensure the credentials are temporary.
		if ($credentials->getType() !== ROAuth2Credentials::TEMPORARY)
		{
echo "ZXZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZ";
			$this->app->sendInvalidAuthMessage('The token is not for a temporary credentials set.');
			return;
		}

		// Verify that we have a signed in user.
		if ($this->app->getIdentity()->get('guest'))
		{
			$this->app->sendInvalidAuthMessage('You must first sign in.');

			return;
		}

		// Attempt to authorise the credentials for the current user.
		$credentials->authorise($this->app->getIdentity()->get('id'));

		if ($credentials->getCallbackUrl() && $credentials->getCallbackUrl() != 'oob')
		{
			$this->app->redirect($credentials->getCallbackUrl());

			return;
		}

		// Build the response for the client.
		$response = array(
			'access_token' => $credentials->getResourceToken(),
			'expires_in' => 3600,
			'refresh_token' => $credentials->getRefreshToken()
		);

		// Set the response code and body.
		$this->response->setHeader('status', '200')
			->setBody(json_encode($response))
			->respond();
		exit;
	}
}

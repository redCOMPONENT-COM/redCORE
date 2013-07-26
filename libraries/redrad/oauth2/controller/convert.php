<?php
/**
 * @package     Joomla.Platform
 * @subpackage  OAuth1
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * OAuth Controller class for converting authorised credentials to token credentials for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth1
 * @since       12.3
 */
class ROAuth2ControllerConvert extends ROAuth2ControllerBase
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
		// Verify that we have an OAuth 2.0 application.
		if ((!$this->app instanceof ApiApplicationWeb))
		{
			throw new LogicException('Cannot perform OAuth 2.0 authorisation without an OAuth 2.0 application.');
		}

		// We need a valid signature to do initialisation.
		if (!$this->request->client_id || !$this->request->client_secret || !$this->request->signature_method )
		{
			$this->app->sendInvalidAuthMessage('Invalid OAuth request signature.');

			return 0;
		}

		// Get the credentials for the request.
		$credentials = $this->createCredentials();
		$credentials->load($this->request->client_secret);

		// Ensure the credentials are authorised.
		if ($credentials->getType() === ROAuth2Credentials::TOKEN)
		{
			$response = array(
				'error' => 'invalid_request',
				'error_description' => 'The token is not for a temporary credentials set.'
			);

			$this->response->setHeader('status', '302')
				->setBody(json_encode($response))
				->respond();

			return;
		}

		// Ensure the credentials are authorised.
		if ($credentials->getType() !== ROAuth2Credentials::AUTHORISED)
		{
			$response = array(
				'error' => 'invalid_request',
				'error_description' => 'The token has not been authorised by the resource owner.'
			);

			$this->response->setHeader('status', '302')
				->setBody(json_encode($response))
				->respond();

			return;
		}

		// Convert the credentials to valid Token credentials for requesting protected resources.
		$credentials->convert();

		// Build the response for the client.
		$response = array(
			'access_token' => $credentials->getAccessToken(),
			'expires_in' => 3600,
			'refresh_token' => $credentials->getRefreshToken()
		);

		// Set the response code and body.
		$this->response->setHeader('status', '200')
			->setBody(json_encode($response))
			->respond();
	}
}

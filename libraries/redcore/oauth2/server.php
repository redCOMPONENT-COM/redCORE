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

// No direct access
defined('_JEXEC') or die( 'Restricted access' );

// Register component prefix
JLoader::registerPrefix('ROauth2', dirname(__FILE__));

/**
 * ROauth2ProtocolRequest class
 *
 * @package  Redcore
 * @since    1.0
 */
class ROauth2Server
{
	/**
	 * @var    JRegistry  Options for the ROauth2Client object.
	 * @since  1.0
	 */
	protected $options;

	/**
	 * @var    JHttp  The HTTP client object to use in sending HTTP requests.
	 * @since  1.0
	 */
	protected $http;

	/**
	 * @var    ROauth2ProtocolRequest  The input object to use in retrieving GET/POST data.
	 * @since  1.0
	 */
	protected $request;

	/**
	 * @var    ROauth2ProtocolRequest  The input object to use in retrieving GET/POST data.
	 * @since  1.0
	 */
	protected $response;

	/**
	 * Constructor.
	 *
	 * @param   JRegistry               $options  The options object.
	 * @param   JHttp                   $http     The HTTP client object.
	 * @param   ROauth2ProtocolRequest  $request  The request object.
	 *
	 * @since   1.0
	 */
	public function __construct(JRegistry $options = null, JHttp $http = null, ROauth2ProtocolRequest $request = null)
	{
		// Setup the options object.
		$this->options = isset($options) ? $options : new JRegistry;

		// Setup the JHttp object.
		$this->http = isset($http) ? $http : new JHttp($this->options);

		// Setup the request object.
		$this->request = isset($request) ? $request : new ROauth2ProtocolRequest;

		// Setup the response object.
		$this->response = isset($response) ? $response : new ROauth2ProtocolResponse;

		// Getting application
		$this->_app = JFactory::getApplication();
	}

	/**
	 * Method to get the REST parameters for the current request. Parameters are retrieved from these locations
	 * in the order of precedence as follows:
	 *
	 *   - Authorization header
	 *   - POST variables
	 *   - GET query string variables
	 *
	 * @return  boolean  True if an REST message was found in the request.
	 *
	 * @since   1.0
	 */
	public function listen()
	{
		// Initialize variables.
		$found = false;

		// Get the OAuth 2.0 message from the request if there is one.
		$found = $this->request->fetchMessageFromRequest();

		// If we found an REST message somewhere we need to set the URI and request method.
		if ($found && isset($this->request->response_type) && !isset($this->request->access_token) )
		{
			// Load the correct controller type
			switch ($this->request->response_type)
			{
				case 'temporary':

					$controller = new ROauth2ControllerInitialise($this->request);

					break;
				case 'authorise':

					$controller = new ROauth2ControllerAuthorise($this->request);

					break;
				case 'token':

					$controller = new ROauth2ControllerConvert($this->request);

					break;
				default:
					throw new InvalidArgumentException('No valid response type was found.');
					break;
			}

			// Execute the controller
			$controller->execute();

			// Exit
			exit;
		}

		// If we found an REST message somewhere we need to set the URI and request method.
		if (isset($this->request->client_id) && isset($this->request->access_token) )
		{
			$controller = new ROauth2ControllerResource($this->request);
			$controller->execute();

			return true;
		}

		return $found;
	}
}

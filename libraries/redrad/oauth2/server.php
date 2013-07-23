<?php
/**
 * @package     RedRad
 * @subpackage  OAuth2
 *
 * This work is based on a Louis Landry work about oauth1 server suport for Joomla! Platform.
 * URL: https://github.com/LouisLandry/joomla-platform/tree/9bc988185ccc3e1c437256cc2c927e49312b3d00/libraries/joomla/oauth1
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * ROAuth2Request class
 *
 * @package     Joomla
 * @since       3.2
 */
class ROAuth2Server
{
	/**
	 * @var    JRegistry  Options for the ROAuth2Client object.
	 * @since  1.0
	 */
	protected $options;

	/**
	 * @var    JHttp  The HTTP client object to use in sending HTTP requests.
	 * @since  1.0
	 */
	protected $http;

	/**
	 * @var    ROAuth2Request  The input object to use in retrieving GET/POST data.
	 * @since  1.0
	 */
	protected $request;

	/**
	 * @var    ROAuth2Request  The input object to use in retrieving GET/POST data.
	 * @since  1.0
	 */
	protected $response;

	/**
	 * Constructor.
	 *
	 * @param   JRegistry        $options      ROAuth2Client options object
	 * @param   JHttp            $http         The HTTP client object
	 * @param   JInput           $input        The input object
	 * @param   JApplicationWeb  $application  The application object
	 *
	 * @since   1.0
	 */
	public function __construct(JRegistry $options = null, JHttp $http = null, ROAuth2Request $request = null)
	{
		// Setup the autoloader for the application classes.
		JLoader::register('ROAuth2Request', JPATH_REDRAD.'/oauth2/protocol/request.php');
		JLoader::register('ROAuth2Response', JPATH_REDRAD.'/oauth2/protocol/response.php');

		$this->options = isset($options) ? $options : new JRegistry;
		$this->http = isset($http) ? $http : new JHttp($this->options);
		$this->request = isset($request) ? $request : new ROAuth2Request;
		$this->response = new ROAuth2Response;

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

		if (!$found)
		{
			return false;
		}

		// If we found an REST message somewhere we need to set the URI and request method.
		if ($found)
		{
			// Load the correct controller type
			switch ($this->request->response_type)
			{
				case 'temporary':

					JLoader::register('ROAuth2ControllerInitialise', JPATH_REDRAD.'/oauth2/controller/initialise.php');

					$controller = new ROAuth2ControllerInitialise($this->request);
					$controller->execute();

					break;
				case 'authorise':

					JLoader::register('ROAuth2ControllerAuthorise', JPATH_REDRAD.'/oauth2/controller/authorise.php');

					$controller = new ROAuth2ControllerAuthorise($this->request);
					$controller->execute();

					break;
				case 'token':

					JLoader::register('ROAuth2ControllerConvert', JPATH_REDRAD.'/oauth2/controller/convert.php');

					$controller = new ROAuth2ControllerConvert($this->request);
					$controller->execute();

					break;
				default:
					throw new InvalidArgumentException('No valid response type was found.');
					break;
			}
		}

		return $found;
	} // end method

} // end class

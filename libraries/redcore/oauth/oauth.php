<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use OAuth\Common\Storage\Session;

defined('JPATH_BASE') or die;

/**
 * Interface to handle api calls
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.2
 */
class ROauth
{
	/**
	 * @var    array  RApi instances container.
	 * @since  1.2
	 */
	public static $instances = array();

	/**
	 * Method to return a ROauth instance based on the given options.
	 *
	 * Instances are unique to the given options and new objects are only created when a unique options array is
	 * passed into the method.  This ensures that we don't end up with unnecessary OAuth resources.
	 *
	 * @param   array  $options  Parameters to be passed to the creating oauth.
	 *
	 * @return  ROauth  Api object.
	 *
	 * @since   1.2
	 * @throws  RuntimeException
	 */
	public static function getInstance($options = array())
	{
		// Get the options signature for the api connector.
		$signature = md5(serialize($options));

		// If we already have a api connector instance for these options then just use that.
		if (empty(self::$instances[$signature]))
		{
			// Derive the class name from the driver.
			//$class = 'Redcore\\Oauth\\ROauth';
			$class = 'OAuth\\ROauth';

			// If the class still doesn't exist we have nothing left to do but throw an exception.
			if (!class_exists($class))
			{
				throw new RuntimeException(sprintf('Unable to load Api: %s', $options['api']));
			}

			// Create our new ROauth connector based on the options given.
			try
			{
				$instance = new $class($options);
			}
			catch (RuntimeException $e)
			{
				throw new RuntimeException(sprintf('Unable to connect to the OAuth: %s', $e->getMessage()));
			}

			// Set the new connector to the global instances based on signature.
			self::$instances[$signature] = $instance;
		}

		return self::$instances[$signature];
	}

	/**
	 * Method to instantiate the file-based api call.
	 *
	 * @param   mixed  $options  Optional custom options to load. JRegistry or array format
	 *
	 * @since   1.2
	 */
	public function __construct($options = null)
	{
		require_once __DIR__ . '/Common/AutoLoader.php';

		$autoloader = new \OAuth\Common\AutoLoader(__NAMESPACE__, dirname(__DIR__));
		\JLoader::registerNamespace(__NAMESPACE__, dirname(__DIR__));

		$autoloader->register();
	}

	/**
	 * Method to test request
	 *
	 * @return  void
	 *
	 * @since   1.2
	 */
	public function test()
	{
		// @todo finish implementing the client
		return null;
		$uriFactory = new \OAuth\Common\Http\Uri\UriFactory;
		$currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
		$currentUri->setQuery('');

		$servicesCredentials = array(
			'google' => array(
				'key'       => '952849546833-blk7mcg81k9oghh4t1crq1m9s8csXXXX.apps.googleusercontent.com',
				'secret'    => 'xqBImKOH5P92ln9xyz4xGXXX',
			),
		);

		$serviceFactory = new \OAuth\ServiceFactory;

		// Session storage
		$storage = new Session;

		// Setup the credentials for the requests
		$credentials = new \OAuth\Common\Consumer\Credentials(
			$servicesCredentials['google']['key'],
			$servicesCredentials['google']['secret'],
			$currentUri->getAbsoluteUri()
		);

		// Instantiate the Google service using the credentials, http client and storage mechanism for the token
		/** @var $googleService \OAuth\OAuth2\Service\Google */
		$googleService = $serviceFactory->createService('google', $credentials, $storage, array('userinfo_email', 'userinfo_profile'));

		if (!empty($_GET['code'])) {
			// This was a callback request from google, get the token
			$googleService->requestAccessToken($_GET['code']);

			// Send a request with it
			$result = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);

			// Show some of the resultant data
			echo 'Your unique google user id is: ' . $result['id'] . ' and your name is ' . $result['name'];
		} else {
			$url = $googleService->getAuthorizationUri();
			$this->http = new \JHttp(null);
			$response = $this->http->get($url);


			// This was a callback request from google, get the token
			$googleService->requestAccessToken($response->code);

			// Send a request with it
			$result = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);

			// Show some of the resultant data
			echo 'Your unique google user id is: ' . $result['id'] . ' and your name is ' . $result['name'];

			//header('Location: ' . $url);
		}
	}
}

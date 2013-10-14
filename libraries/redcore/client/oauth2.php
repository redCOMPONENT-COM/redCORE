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

defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.environment.response');

/**
 * redCORE class for interacting with an OAuth 2.0 server.
 *
 * @package     Redcore
 * @subpackage  OAuth2
 * @since       1.0
 */
class RClientOAuth2
{
	/**
	 * @var    JRegistry  Options for the RClientOAuth2 object.
	 * @since  1.0
	 */
	protected $options;

	/**
	 * @var    JHttp  The HTTP client object to use in sending HTTP requests.
	 * @since  1.0
	 */
	protected $http;

	/**
	 * @var    JInput  The input object to use in retrieving GET/POST data.
	 * @since  1.0
	 */
	protected $input;

	/**
	 * @var    JApplicationWeb  The application object to send HTTP headers for redirects.
	 * @since  1.0
	 */
	protected $application;

	/**
	 * Constructor.
	 *
	 * @param   JRegistry        $options      RClientOAuth2 options object
	 * @param   JHttp            $http         The HTTP client object
	 * @param   JInput           $input        The input object
	 * @param   JApplicationWeb  $application  The application object
	 *
	 * @since   1.0
	 */
	public function __construct(JRegistry $options = null, JHttp $http = null, JInput $input = null, JApplicationWeb $application = null)
	{
		$this->options = isset($options) ? $options : new JRegistry;
		$this->http = isset($http) ? $http : new JHttp($this->options);
		$this->application = isset($application) ? $application : new JApplicationWeb;
		$this->input = isset($input) ? $input : $this->application->input;

		$this->rest_key = $this->randomKey();
	}

	/**
	 * Fetch the access token making the OAuth 2.0 method process
	 *
	 * @return	string	Returns the JSON response from the server
	 *
	 * @since 	1.0
	 * @throws	Exception
	 */
	public function fetchAccessToken()
	{
		// Temporary token
		$temporary = (object) $this->getTemporary();

		// Get authorization token
		$authenticate = (object) $this->getAuthentication($temporary->oauth_code);

		// Get access token
		$token = (object) $this->getToken($authenticate->oauth_code);

		return $token;
	}

	/**
	 * Get the rest headers to send
	 *
	 * @param   string  $form  True if we like to use POST
	 *
	 * @return  array   The z
	 *
	 * @since   1.0
	 */
	protected function getRestHeaders($form = false)
	{
		// Encode the headers for REST
		$user_encode = $this->encode($this->options->get('username'), $this->rest_key);
		$pw_encode = $this->encode($this->options->get('password'), $this->rest_key);
		$authorization = $this->encode($user_encode, $pw_encode, true);

		$headers = array(
			'Authorization' => 'Bearer ' . base64_encode($authorization)
		);

		if ($form === true)
		{
			$headers['Content-Type'] = 'application/x-www-form-urlencoded';
		}

		return $headers;
	}

	/**
	 * Get the POST data to send
	 *
	 * @return  array   The POST data to send
	 *
	 * @since   1.0
	 */
	protected function getPostData()
	{
		// Set the user and password to headers
		$rest_key = $this->randomKey();

		// Encode the headers for REST
		$user_encode = $this->encode($this->options->get('username'), $this->rest_key);
		$pw_encode = $this->encode($this->options->get('password'), $this->rest_key);
		$client_secret = $this->encode($this->randomKey(), $pw_encode, true);

		$post = array(
			'oauth_client_id' => base64_encode($user_encode),
			'oauth_client_secret' => base64_encode($client_secret),
			'oauth_signature_method' => $this->options->get('signature_method')
		);

		return $post;
	}

	/**
	 * Encode the string with the key
	 *
	 * @param   string   $string  The string to encode.
	 * @param   string   $key     The key to encode the string.
	 * @param   boolean  $base64  True to encode the strings.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function encode($string, $key, $base64 = false)
	{
		if ($base64 === true)
		{
			$return = base64_encode($string) . ":" . base64_encode($key);
		}
		else
		{
			$return = "{$string}:{$key}";
		}

		return $return;
	}

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array	Returns a reference to the source data array.
	 *
	 * @since 	1.0
	 * @throws	Exception
	 */
	public function getTemporary()
	{
		// Get the POST data
		$data = $this->getPostData();
		$data['oauth_response_type'] = 'temporary';

		// Send the request
		$response = $this->http->post($this->options->get('url'), $data, $this->getRestHeaders(true));

		// Process the response
		$token = $this->processRequest($response);

		return $token;
	}

	/**
	 * Get the authentication token
	 *
	 * @param   string  $code  The old token to get the access_token
	 *
	 * @return	string	Returns authentication token
	 *
	 * @since 	1.0
	 * @throws	Exception
	 */
	public function getAuthentication($code)
	{
		// Get the headers
		$data = $this->getPostData();

		// Create the request array to be sent
		$append = array(
			'oauth_grant_type' => 'authorization_code',
			'oauth_response_type' => 'authorise',
			'oauth_code' => $code
		);

		// Append parameters to existing data
		$data = $data + $append;

		// Send the request
		$response = $this->http->post($this->options->get('url'), $data, $this->getRestHeaders(true));

		// Process the response
		$token = $this->processRequest($response);

		return $token;
	}

	/**
	 * Get the access_token code to access resources
	 *
	 * @param   string  $code  The old token to get the access_token
	 *
	 * @return	string	The access token
	 *
	 * @since 	1.0
	 * @throws	Exception
	 */
	public function getToken($code)
	{
		// Get the headers
		$data = $this->getPostData();

		// Create the request array to be sent
		$append = array(
			'oauth_response_type' => 'token',
			'oauth_code' => $code
		);

		// Append parameters to existing data
		$data = $data + $append;

		// Send the request
		$response = $this->http->post($this->options->get('url'), $data, $this->getRestHeaders(true));

		// Process the response
		$token = $this->processRequest($response);

		return $token;
	}

	/**
	 * Refresh the access token instance.
	 *
	 * @param   string  $token  The old token to be refreshed
	 *
	 * @return  array  The new access token
	 *
	 * @since   1.0
	 */
	public function refreshToken($token = null)
	{
		if (!$this->getOption('user_refresh'))
		{
			throw new RuntimeException('Refresh token is not supported for this OAuth instance.');
		}

		if (!$token)
		{
			$token = $this->getToken();

			if (!array_key_exists('refresh_token', $token))
			{
				throw new RuntimeException('No refresh token is available.');
			}

			$token = $token['refresh_token'];
		}

		$data['grant_type'] = 'refresh_token';
		$data['refresh_token'] = $token;
		$data['client_id'] = $this->getOption('client_id');
		$data['client_secret'] = $this->getOption('client_secret');
		$response = $this->http->post($this->getOption('token_url'), $data);

		// Process the response
		$token = $this->processRequest($response);

		return $token;
	}

	/**
	 * Get the resource using the access token.
	 *
	 * @param   string  $client_id  The client id
	 * @param   string  $token      The access token
	 *
	 * @return	string	Returns the JSON+HAL resource
	 *
	 * @since 	1.0
	 * @throws	Exception
	 */
	public function getResource($client_id, $token)
	{
		// Url encode client id
		$client_id = urlencode($client_id);

		// Add GET parameters to URL
		$url = $this->options->get('url') . "?oauth_access_token={$token}&oauth_client_id={$client_id}";

		// Send the request
		$response = $this->http->get($url, $this->getRestHeaders());

		if ($response->code >= 200 && $response->code < 400)
		{
			return $response->body;
		}
		else
		{
			throw new RuntimeException('Error code ' . $response->code . ': ' . $response->body . '.');
		}
	}

	/**
	 * Process the HTTP request and return and array with the token
	 *
	 * @param   string  $response  The response object
	 *
	 * @return	array	Returns a reference to the token response.
	 *
	 * @since 	1.0
	 * @throws	Exception
	 */
	public function processRequest($response)
	{
		// Check if the request is correct
		if ($response->code >= 200 && $response->code < 400)
		{
			if ($response->headers['X-Powered-By'] == 'JoomlaWebAPI/1.0')
			{
				$token = array_merge(json_decode($response->body, true), array('created' => time()));
			}
			else
			{
				parse_str($response->body, $token);
				$token = array_merge($token, array('created' => time()));
			}

			return $token;
		}
		else
		{
			throw new RuntimeException('Error code ' . $response->code . ': ' . $response->body . '.');
		}
	}

	/**
	 * Get an option from the RClientOAuth2 instance.
	 *
	 * @param   string  $key  The name of the option to get
	 *
	 * @return  mixed  The option value
	 *
	 * @since   1.0
	 */
	public function getOption($key)
	{
		return $this->options->get($key);
	}

	/**
	 * Set an option for the RClientOAuth2 instance.
	 *
	 * @param   string  $key    The name of the option to set
	 * @param   mixed   $value  The option value to set
	 *
	 * @return  RClientOAuth2  This object for method chaining
	 *
	 * @since   1.0
	 */
	public function setOption($key, $value)
	{
		$this->options->set($key, $value);

		return $this;
	}

	/**
	 * Get the access token from the RClientOAuth2 instance.
	 *
	 * @return  array  The access token
	 *
	 * @since   1.0
	 */
	public function getAccessToken()
	{
		return $this->getOption('access_token');
	}

	/**
	 * Set an option for the RClientOAuth2 instance.
	 *
	 * @param   array  $value  The access token
	 *
	 * @return  RClientOAuth2  This object for method chaining
	 *
	 * @since   1.0
	 */
	public function setToken($value)
	{
		if (is_array($value) && !array_key_exists('expires_in', $value) && array_key_exists('expires', $value))
		{
			$value['expires_in'] = $value['expires'];
			unset($value['expires']);
		}

		$this->setOption('access_token', $value);

		return $this;
	}

	/**
	 * Generate a random (and optionally unique) key.
	 *
	 * @param   boolean  $unique  True to enforce uniqueness for the key.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function randomKey($unique = false)
	{
		$str = md5(uniqid(rand(), true));

		if ($unique)
		{
			list ($u, $s) = explode(' ', microtime());
			$str .= dechex($u) . dechex($s);
		}

		return $str;
	}

	/**
	 * Verify if the client has been authenticated
	 *
	 * @return  boolean  Is authenticated
	 *
	 * @since   1.0
	 */
	public function isAuthenticated()
	{
		$token = $this->getToken();

		if (!$token || !array_key_exists('access_token', $token))
		{
			return false;
		}
		elseif (array_key_exists('expires_in', $token) && $token['created'] + $token['expires_in'] < time() + 20)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}

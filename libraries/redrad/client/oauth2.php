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

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.environment.response');

/**
 * redRAD class for interacting with an OAuth 2.0 server.
 *
 * @package     RedRad
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
	}

	/**
	 * Fetch the access token making the OAuth 2.0 method process
	 *
	 * @return	string	Returns the JSON response from the server
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
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array	Returns a reference to the source data array.
	 * @since 	1.0
	 * @throws	Exception
	 */
	public function getTemporary()
	{
		// Get the headers
		$headers = $this->getRestHeaders();
		$headers['oauth_response_type'] = 'temporary';

		// Perform the GET request via HTTP
		$response = $this->http->get($this->options->get('url'), $headers);

		if ($response->code >= 200 && $response->code < 400)
		{
			//if ($response->headers['Content-Type'] == 'application/json')
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
			throw new RuntimeException('Error code ' . $response->code . ' received requesting access token: ' . $response->body . '.');
		}

		return $token;
	}

	/**
	 * Get the authentication token
	 *
	 * @return	string	Returns authentication token
	 * @since 	1.0
	 * @throws	Exception
	 */
	public function getAuthentication($code)
	{
		// Create the request array to be sent
		$data = array(
			'oauth_grant_type' => 'authorization_code',
			'oauth_response_type' => 'authorise',
			'oauth_code' => $code
		);

		// Send the request
		$response = $this->http->post($this->options->get('url'), $data, $this->getRestHeaders(true));

		if ($response->code >= 200 && $response->code < 400)
		{
			//if ($response->headers['Content-Type'] == 'application/json')
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
			throw new RuntimeException('Error code ' . $response->code . ' received requesting authentication: ' . $response->body . '.');
		}

		return $token;
	}

	/**
	 * Get the access_token code to access resources
	 *
	 * @return	string	The access token
	 * @since 	1.0
	 * @throws	Exception
	 */
	public function getToken($code)
	{
		// Create the request array to be sent
		$data = array(
			'oauth_response_type' => 'token',
			'oauth_code' => $code
		);

		// Send the request
		$response = $this->http->post($this->options->get('url'), $data, $this->getRestHeaders());

		if ($response->code >= 200 && $response->code < 400)
		{
			//if ($response->headers['Content-Type'] == 'application/json')
			if ($response->headers['X-Powered-By'] == 'JoomlaWebAPI/1.0')
			{
				$token = array_merge(json_decode($response->body, true), array('created' => time()));
			}
			else
			{
				parse_str($response->body, $token);
				$token = array_merge($token, array('created' => time()));
			}

			$this->setToken($token);

			return $token;
		}
		else
		{
			throw new RuntimeException('Error code ' . $response->code . ' received requesting authentication: ' . $response->body . '.');
		}

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

		if ($response->code >= 200 || $response->code < 400)
		{
			if ($response->headers['Content-Type'] == 'application/json')
			{
				$token = array_merge(json_decode($response->body, true), array('created' => time()));
			}
			else
			{
				parse_str($response->body, $token);
				$token = array_merge($token, array('created' => time()));
			}

			$this->setToken($token);

			return $token;
		}
		else
		{
			throw new Exception('Error code ' . $response->code . ': ' . $response->body . '.');
		}
	}

	/**
	 * Get the resource using the access token.
	 *
	 * @param   string  $token  The access token
	 *
	 * @return	string	Returns the JSON+HAL resource
	 * @since 	1.0
	 * @throws	Exception
	 */
	public function getResource($code)
	{
		// Add GET parameters to URL
		$url = $this->options->get('url')."?oauth_access_token={$code}";

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
	 * Create the URL for authentication.
	 *
	 * @return  JHttpResponse  The HTTP response
	 *
	 * @since   1.0
	 */
	public function createUrl()
	{
		if (!$this->getOption('authurl') || !$this->getOption('clientid'))
		{
			throw new InvalidArgumentException('Authorization URL and client_id are required');
		}

		$url = $this->getOption('authurl');

		if (strpos($url, '?'))
		{
			$url .= '&';
		}
		else
		{
			$url .= '?';
		}

		$url .= 'response_type=code';
		$url .= '&client_id=' . urlencode($this->getOption('clientid'));

		if ($this->getOption('redirecturi'))
		{
			$url .= '&redirect_uri=' . urlencode($this->getOption('redirecturi'));
		}

		if ($this->getOption('scope'))
		{
			$scope = is_array($this->getOption('scope')) ? implode(' ', $this->getOption('scope')) : $this->getOption('scope');
			$url .= '&scope=' . urlencode($scope);
		}

		if ($this->getOption('state'))
		{
			$url .= '&state=' . urlencode($this->getOption('state'));
		}

		if (is_array($this->getOption('requestparams')))
		{
			foreach ($this->getOption('requestparams') as $key => $value)
			{
				$url .= '&' . $key . '=' . urlencode($value);
			}
		}

		return $url;
	}

	/**
	 * Send a signed Oauth request.
	 *
	 * @param   string  $url      The URL forf the request.
	 * @param   mixed   $data     The data to include in the request
	 * @param   array   $headers  The headers to send with the request
	 * @param   string  $method   The method with which to send the request
	 * @param   int     $timeout  The timeout for the request
	 *
	 * @return  string  The URL.
	 *
	 * @since   1.0
	 */
	public function query($url, $data = null, $headers = array(), $method = 'get', $timeout = null)
	{
		$token = $this->getToken();

		if (array_key_exists('expires_in', $token) && $token['created'] + $token['expires_in'] < time() + 20)
		{
			if (!$this->getOption('userefresh'))
			{
				return false;
			}
			$token = $this->refreshToken($token['refresh_token']);
		}

		if (!$this->getOption('authmethod') || $this->getOption('authmethod') == 'bearer')
		{
			$headers['Authorization'] = 'Bearer ' . $token['access_token'];
		}
		elseif ($this->getOption('authmethod') == 'get')
		{
			if (strpos($url, '?'))
			{
				$url .= '&';
			}
			else
			{
				$url .= '?';
			}
			$url .= $this->getOption('getparam') ? $this->getOption('getparam') : 'access_token';
			$url .= '=' . $token['access_token'];
		}

		switch ($method)
		{
			case 'head':
			case 'get':
			case 'delete':
			case 'trace':
			$response = $this->http->$method($url, $headers, $timeout);
			break;
			case 'post':
			case 'put':
			case 'patch':
			$response = $this->http->$method($url, $data, $headers, $timeout);
			break;
			default:
			throw new InvalidArgumentException('Unknown HTTP request method: ' . $method . '.');
		}

		if ($response->code < 200 || $response->code >= 400)
		{
			throw new RuntimeException('Error code ' . $response->code . ' received requesting data: ' . $response->body . '.');
		}
		return $response;
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
		// Set the user and password to headers
		$rest_username = $this->options->get('username');
		$rest_password = $this->options->get('password');
		$rest_key = $this->options->get('rest_key');

		// Encode the headers for REST
		$user_encode = $this->headerEncode($rest_username, $rest_key);
		$pw_encode = $this->headerEncode($rest_password, $rest_key);
		$authorization = $this->headerEncode($user_encode, $pw_encode, true);
		$client_secret = $this->headerEncode($this->randomKey(), $rest_key);

		$headers = array(
			'Authorization' => 'Basic ' . base64_encode($authorization),
			'oauth_client_id' => base64_encode($user_encode),
			'oauth_client_secret' => base64_encode($client_secret),
			'oauth_signature_method' => $this->options->get('signature_method')
		);

		if ($form === true) {
			$headers['Content-Type'] = 'application/x-www-form-urlencoded';
		}

		return $headers;
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
	protected function headerEncode($string, $key, $base64 = false)
	{
		if ($base64 === true) {
			$return = base64_encode($string).":".base64_encode($key);
		}else{
			$return = "{$string}:{$key}";
		}

		return $return;
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

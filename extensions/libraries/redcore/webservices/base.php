<?php
/**
 * @package     Redcore
 * @subpackage  Factory
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

use Joomla\Registry\Registry;

/**
 * Class for interacting with webservices.
 *
 * @package     Redcore
 * @subpackage  webservices
 * @since       1.8
 */
class RWebservicesBase
{
	/**
	 * @var    Registry  Options for the RWebservicesBase object.
	 * @since  1.8
	 */
	protected $options;

	/**
	 * @var    JHttp  The HTTP client object to use in sending HTTP requests.
	 * @since  1.8
	 */
	protected $http;

	/**
	 * @var    JInput  The input object to use in retrieving GET/POST data.
	 * @since  1.8
	 */
	protected $input;

	/**
	 * @var    JApplicationWeb  The application object to send HTTP headers for redirects.
	 * @since  1.8
	 */
	protected $application;

	/**
	 * @var    string  Authentication URL
	 */
	protected $token;

	/**
	 * Constructor.
	 *
	 * @param   Registry         $options      RWebservicesBase options object
	 * @param   JHttp            $http         The HTTP client object
	 * @param   JInput           $input        The input object
	 * @param   JApplicationWeb  $application  The application object
	 *
	 * @since   1.8
	 */
	public function __construct($options = null, $http = null, $input = null, $application = null)
	{
		$this->options = isset($options) ? $options : new Registry;
		$this->http = isset($http) ? $http : new JHttp($this->options);
		$this->application = isset($application) ? $application : new JApplicationWeb;
		$this->input = isset($input) ? $input : $this->application->input;
	}

	/**
	 * Gets already existing token array or generates new one
	 *
	 * @return array  Token array
	 */
	public function getToken()
	{
		$recreate = false;

		// If token key is already defined, we dont have to generate a new one
		if ($this->getOption('accesstoken'))
		{
			return $this->getOption('accesstoken');
		}
		// If there is no token we will fetch it we have access point for authentication
		elseif (!$this->token)
		{
			$recreate = true;
		}
		// We check if the token is expired
		elseif ($this->token)
		{
			// Different values of token expires
			if (array_key_exists('expires_in', $this->token) && array_key_exists('created', $this->token))
			{
				if ($this->token['created'] + $this->token['expires_in'] < time() + 20)
				{
					$recreate = true;
				}
			}
			elseif (isset($this->token['expireTimeFormatted']))
			{
				if ($this->token['expireTimeFormatted'] <= date('Y-m-d H:i:s'))
				{
					$recreate = true;
				}
			}
			elseif (isset($this->token['expires']))
			{
				if ($this->token['expires'] <= date('Y-m-d H:i:s'))
				{
					$recreate = true;
				}
			}
		}

		if ($recreate)
		{
			$this->token = $this->generateToken();
		}

		return $this->token;
	}

	/**
	 * Generate the access token
	 *
	 * @return  array|boolean  The access token
	 *
	 * @since   1.8
	 * @throws  Exception
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 */
	public function generateToken()
	{
		if ($this->token && array_key_exists('expires_in', $this->token) && array_key_exists('created', $this->token)
			&& $this->token['created'] + $this->token['expires_in'] < time() + 20)
		{
			if ($this->getOption('authorization.userefresh', true))
			{
				$token = $this->refreshToken($this->token['refresh_token']);

				if ($token)
				{
					$this->token = $token;

					return $this->token;
				}
			}
		}

		// It can be client, password, refresh_token, ...
		$data = array();
		$data['grant_type'] = $this->getOption('authorization.granttype', 'client_credentials');
		$data['client_id'] = $this->getOption('authorization.clientid');
		$data['client_secret'] = $this->getOption('authorization.clientsecret');
		$url = $this->getOption('authorization.tokenurl', $this->getOption('authorization.authurl'));
		$method = $this->getOption('authorization.authmethod', 'post');

		if ($this->getOption('authorization.redirecturi'))
		{
			$data['redirect_uri'] = $this->getOption('authorization.redirecturi');
		}

		if ($this->getOption('authorization.scope'))
		{
			$scope = is_array($this->getOption('authorization.scope')) ?
				implode(' ', $this->getOption('authorization.scope')) : $this->getOption('authorization.scope');
			$data['scope'] = $scope;
		}

		if ($this->getOption('authorization.state'))
		{
			$data['state'] = $this->getOption('authorization.state');
		}

		if ($this->getOption('authorization.username'))
		{
			$data['username'] = $this->getOption('authorization.username');
		}

		if ($this->getOption('authorization.password'))
		{
			$data['password'] = $this->getOption('authorization.password');
		}

		if ($this->getOption('authorization.requestparams'))
		{
			$requestParams = (array) $this->getOption('authorization.requestparams');

			foreach ($requestParams as $key => $value)
			{
				$data[$key] = $value;
			}
		}

		switch ($method)
		{
			case 'head':
			case 'get':
			case 'delete':
			case 'trace':

				if (strpos($url, '?'))
				{
					$url .= '&';
				}
				else
				{
					$url .= '?1=1';
				}

				foreach ($data as $key => $value)
				{
					$url .= '&' . $key . '=' . urlencode($value);
				}

				$response = $this->http->{$method}($url);
				break;
			case 'post':
			case 'put':
			case 'patch':
				$response = $this->http->{$method}($url, $data);
				break;
			default:
				throw new InvalidArgumentException('Unknown HTTP request method: ' . $method . '.');
		}

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

			return $this->token;
		}
		else
		{
			throw new Exception('Error code ' . $response->code . ' received refreshing token: ' . $response->body . '.');
		}
	}

	/**
	 * Refresh the access token instance.
	 *
	 * @param   string  $token  The refresh token
	 *
	 * @return  array|boolean  The new access token or false if failed
	 *
	 * @since   1.8
	 * @throws  Exception
	 */
	public function refreshToken($token = null)
	{
		if (!$this->getOption('authorization.userefresh', true))
		{
			return false;
		}

		$data['grant_type'] = 'refresh_token';
		$data['refresh_token'] = $token;
		$data['client_id'] = $this->getOption('authorization.clientid');
		$data['client_secret'] = $this->getOption('authorization.clientsecret');
		$url = $this->getOption('authorization.tokenurl', $this->getOption('authorization.authurl'));
		$response = $this->http->post($url, $data);

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

			return $this->token;
		}
		else
		{
			throw new Exception('Error code ' . $response->code . ' received refreshing token: ' . $response->body . '.');
		}
	}

	/**
	 * Sets token parameter
	 *
	 * @param   string  $token  Token
	 *
	 * @return  void
	 */
	public function setToken($token)
	{
		$this->token = $token;
	}

	/**
	 * Verify if the client has been authenticated
	 *
	 * @return  boolean  Is authenticated
	 *
	 * @since   1.8
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

	/**
	 * Get an option from the RWebservicesBase instance.
	 *
	 * @param   string  $key      The name of the option to get
	 * @param   mixed   $default  Default value
	 *
	 * @return  mixed  The option value
	 *
	 * @since   1.8
	 */
	public function getOption($key, $default = null)
	{
		return $this->options->get($key, $default);
	}

	/**
	 * Set an option for the RWebservicesBase instance.
	 *
	 * @param   string  $key    The name of the option to set
	 * @param   mixed   $value  The option value to set
	 *
	 * @return  RWebservicesBase  This object for method chaining
	 *
	 * @since   1.8
	 */
	public function setOption($key, $value)
	{
		$this->options->set($key, $value);

		return $this;
	}
}

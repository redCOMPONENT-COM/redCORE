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

/**
 * ROauth2ProtocolRequest class
 *
 * @package  Redcore
 * @since    1.0
 */
class ROauth2ProtocolRequest
{
	/**
	 * @var    string  The HTTP request method for the message.
	 * @since  1.0
	 */
	public $_method;

	/**
	 * @var    array  Associative array of parameters for the REST message.
	 * @since  1.0
	 */
	public $_headers = array();

	/**
	 * @var    string
	 * @since  1.0
	 */
	public $_identity;

	/**
	 * @var    string
	 * @since  1.0
	 */
	public $_credentials;

	/**
	 * @var    array  List of possible OAuth 2.0 parameters.
	 * @since  1.0
	 */
	static protected $_oauth_reserved = array(
		'client_id',
		'client_secret',
		'signature_method',
		'response_type',
		'scope',
		'state',
		'redirect_uri',
		'error',
		'error_description',
		'error_uri',
		'grant_type',
		'code',
		'access_token',
		'token_type',
		'expires_in',
		'username',
		'password',
		'refresh_token'
	);

	/**
	 * @var    JURI  The request URI for the message.
	 * @since  1.0
	 */
	private $_uri;

	/**
	 * Get the list of reserved OAuth 2.0 parameters.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public static function getReservedParameters()
	{
		return self::$_oauth_reserved;
	}

	/**
	 * Method to get the OAUTH parameters.
	 *
	 * @return  array  $parameters  The OAUTH message parameters.
	 *
	 * @since   1.0
	 */
	public function getParameters()
	{
		$parameters = array();

		foreach (self::$_oauth_reserved as $k => $v)
		{
			if (isset($this->$v))
			{
				$parameters[$v] = $this->$v;
			}
		}

		return $parameters;
	}

	/**
	 * Method to set the REST message parameters.  This will only set valid REST message parameters.  If non-valid
	 * parameters are in the input array they will be ignored.
	 *
	 * @param   array  $parameters  The REST message parameters to set.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setParameters($parameters)
	{
		// Ensure that only valid REST parameters are set if they exist.
		if (!empty($parameters))
		{
			foreach ($parameters as $k => $v)
			{
				if (0 === strpos($k, 'OAUTH_'))
				{
					$key = strtolower(substr($k, 6));
					$this->$key = $v;
				}
			}
		}
	}

	/**
	 * Object constructor.
	 *
	 * @param   ROauth2TableCredentials  $table  Connector object for table class.
	 *
	 * @since   1.0
	 */
	public function __construct(ROauth2TableCredentials $table = null)
	{
		// Load the Joomla! application
		$this->_app = JFactory::getApplication();

		// Setup the database object.
		$this->_input = $this->_app->input;

		// Getting the URI
		$this->_uri = new JURI($this->_fetchRequestUrl());

		// Getting the request method (POST||GET)
		$this->_method = strtoupper($_SERVER['REQUEST_METHOD']);

		// Loading the response class
		$this->_response = new ROauth2ProtocolResponse;
	}

	/**
	 * Check if the incoming request is signed using OAuth 2.0.  To determine this, OAuth parameters are searched
	 * for in the order of precedence as follows:
	 *
	 *   * Authorization header.
	 *   * POST variables.
	 *   * GET query string variables.
	 *
	 * @return  boolean  True if parameters found, false otherwise.
	 *
	 * @since   1.0
	 */
	public function fetchMessageFromRequest()
	{
		// Init flag
		$flag = false;

		// Loading the response class
		$requestHeader = new ROauth2ProtocolRequestHeader;

		// First we look and see if we have an appropriate Authorization header.
		$authorization = $requestHeader->fetchAuthorizationHeader();

		if ($authorization)
		{
			$this->_headers = $requestHeader->processAuthorizationHeader($authorization);

			if ($this->_headers)
			{
				// Bind the found parameters to the OAuth 2.0 message.
				$this->setParameters($this->_headers);

				$flag = true;
			}
		}

		// Getting the method
		$method = strtolower($this->_method);

		// Building the class name
		$class = "ROauth2ProtocolRequest" . ucfirst($method);

		// Creating the class
		$request = new $class;

		// If we didn't find an Authorization header or didn't find anything in it try the POST variables.
		$params = $request->processVars();

		if ($params)
		{
			// Bind the found parameters to the OAuth 2.0 message.
			$this->setParameters($params);

			$flag = true;
		}

		// TODO: Check errors

		return $flag;
	}

	/**
	 * Encode a string according to the RFC3986
	 *
	 * @param   string  $s  string to encode
	 *
	 * @return  string encoded string
	 *
	 * @link    http://www.ietf.org/rfc/rfc3986.txt
	 * @since   1.0
	 */
	public function encode($s)
	{
		return str_replace('%7E', '~', rawurlencode((string) $s));
	}

	/**
	 * Decode a string according to RFC3986.
	 * Also correctly decodes RFC1738 urls.
	 *
	 * @param   string  $s  string to decode
	 *
	 * @return  string  decoded string
	 *
	 * @link    http://www.ietf.org/rfc/rfc1738.txt
	 * @link    http://www.ietf.org/rfc/rfc3986.txt
	 * @since   1.0
	 */
	public function decode($s)
	{
		return rawurldecode((string) $s);
	}

	/**
	 * Method to detect and return the requested URI from server environment variables.
	 *
	 * @return  string  The requested URI
	 *
	 * @since   11.3
	 */
	public function _fetchRequestUrl()
	{
		// Initialise variables.
		$uri = '';

		// First we need to detect the URI scheme.
		if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off'))
		{
			$scheme = 'https://';
		}
		else
		{
			$scheme = 'http://';
		}

		/*
		 * There are some differences in the way that Apache and IIS populate server environment variables.  To
		 * properly detect the requested URI we need to adjust our algorithm based on whether or not we are getting
		 * information from Apache or IIS.
		 */

		// If PHP_SELF and REQUEST_URI are both populated then we will assume "Apache Mode".
		if (!empty($_SERVER['PHP_SELF']) && !empty($_SERVER['REQUEST_URI']))
		{
			// The URI is built from the HTTP_HOST and REQUEST_URI environment variables in an Apache environment.
			$uri = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

			$uri = explode("?", $uri);
			$uri = $uri[0];
		}

		// If not in "Apache Mode" we will assume that we are in an IIS environment and proceed.
		else
		{
			// IIS uses the SCRIPT_NAME variable instead of a REQUEST_URI variable... thanks, MS
			$uri = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

			// If the QUERY_STRING variable exists append it to the URI string.
			if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
			{
				$uri .= '?' . $_SERVER['QUERY_STRING'];
			}
		}

		return trim($uri);
	}

	/**
	 * Create a token-string
	 *
	 * @param   integer  $length  Length of string
	 *
	 * @return  string  Generated token
	 *
	 * @since   11.1
	 */
	protected function _createToken($length = 32)
	{
		static $chars = '0123456789abcdef';
		$max = strlen($chars) - 1;
		$token = '';
		$name = session_name();

		for ($i = 0; $i < $length; ++$i)
		{
			$token .= $chars[(rand(0, $max))];
		}

		return md5($token . $name);
	}
}

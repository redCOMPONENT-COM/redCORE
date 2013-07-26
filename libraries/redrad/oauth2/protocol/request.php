<?php
/**
 * @package     RedRad
 * @subpackage  OAuth2
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
 * @since       1.0
 */
class ROAuth2Request
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

		foreach ($this->_oauth_reserved as $k => $v)
		{
			if (isset($this->$v)) {
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
		    if (0 === strpos($k, 'OAUTH_')) {
					$key = strtolower(substr($k, 6));
					$this->$key = $v;
		    }
			}
		}
	}

	/**
	 * Object constructor.
	 *
	 * @param   ROAuth2TableCredentials  $table  Connector object for table class.
	 *
	 * @since   1.0
	 */
	public function __construct(ROAuth2TableCredentials $table = null)
	{
		$this->_app = JFactory::getApplication();

		// Setup the database object.
		$this->_input = $this->_app->input;

		// Getting the URI
		$this->_uri = new JURI($this->_fetchRequestUrl());
		// Getting the request method (POST||GET)
		$this->_method = strtoupper($_SERVER['REQUEST_METHOD']);

		// Setup the autoloader for the application classes.
		JLoader::register('ROAuth2Response', JPATH_REDRAD.'/oauth2/protocol/response.php');

		// Loading the response class
		$this->_response = new ROAuth2Response;
	}

	/**
	 * Authenticate an identity using OAuth 2.0.  This will validate an OAuth 1.0 message for a valid client,
	 * credentials if present and signature.  If the message is valid and the credentials are token credentials
	 * then the resource owner id is returned as the authenticated identity.
	 *
	 * @since   1.0
	 */
	public function doOAuthAuthentication($password)
	{
		// Looking the username header
		if (isset($this->_headers['PHP_AUTH_USER'])) {
			$user_decode = base64_decode($this->_headers['PHP_AUTH_USER']);
		} else if (isset($this->_headers['PHP_HTTP_USER'])) {
			$user_decode = base64_decode($this->_headers['PHP_HTTP_USER']);
		} else if (isset($this->_headers['PHP_USER'])) {
			$user_decode = base64_decode($this->_headers['PHP_USER']);
		}

		$parts	= explode( ':', $user_decode );
		$user	= $parts[0];

		// Looking the password header
		if (isset($this->_headers['PHP_AUTH_PW'])) {
			$password_decode = base64_decode($this->_headers['PHP_AUTH_PW']);
		} else if (isset($this->_headers['PHP_HTTP_PW'])) {
			$password_decode = base64_decode($this->_headers['PHP_HTTP_PW']);
		} else if (isset($this->_headers['PHP_PW'])) {
			$password_decode = base64_decode($this->_headers['PHP_PW']);
		}

		$parts	= explode( ':', $password_decode );
		$password_clean	= $parts[0];

		// Check the password
		$parts	= explode( ':', $password );
		$crypt	= $parts[0];

		$salt	= @$parts[1];

		$testcrypt = JUserHelper::getCryptedPassword($password_clean, $salt);

		if ($crypt != $testcrypt) {
			$this->_app->sendInvalidAuthMessage('Username or password do not match');
			exit;
		}

	} // end method

	/**
	 * Method to determine whether or not the message signature is valid.
	 *
	 * @param   string  $requestUrl        The message's request URL.
	 * @param   string  $requestMethod     The message's request method.
	 * @param   string  $clientSecret      The OAuth client's secret.
	 * @param   string  $credentialSecret  The OAuth credentials' secret.
	 *
	 * @return  boolean  True if the message is properly signed.
	 *
	 * @since   1.0
	 */
	public function isValid($requestUrl, $requestMethod, $clientSecret, $credentialSecret = null)
	{

//echo "$requestUrl, $requestMethod, $clientSecret, $credentialSecret\n";

		$signature = $this->sign($requestUrl, $requestMethod, $clientSecret, $credentialSecret);

//echo $signature;

/*
		if ($this->signature_method != 'PLAINTEXT' && !$this->_nonce->validate($this->nonce, $this->consumerKey, $this->timestamp, $this->token))
		{
			// The nonce was invalid (either the timestamp was too old or it has already been used).
			return false;
		}
*/
		//return ($this->signature && ($signature == str_replace(' ', '+', $this->signature)));
	}

	/**
	 * Get the message string complete and signed.
	 *
	 * @param   string  $requestUrl        The message's request URL.
	 * @param   string  $requestMethod     The message's request method.
	 * @param   string  $clientSecret      The OAuth client's secret.
	 * @param   string  $credentialSecret  The OAuth credentials' secret.
	 *
	 * @return  string  The OAuth message signature.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function sign($requestUrl, $requestMethod, $clientSecret, $credentialSecret = null)
	{
		// Setup the autoloader for the application classes.
		JLoader::register('ROAuth2MessageSigner', JPATH_REDRAD.'/oauth2/protocol/signer.php');
		// Get a message signer object.
		$signer = ROAuth2MessageSigner::getInstance($this->request->signature_method);

//echo "\n===========\n";

//print_r($signer);

//echo "\n===========\n";

//echo "$requestUrl, $requestMethod, $clientSecret";

		// Get the base string for signing.
		$baseString = $this->request->_fetchStringForSigning($requestUrl, $requestMethod);

//echo "\n\n$baseString\n\n";

		return $signer->sign($baseString, $this->request->encode($clientSecret), $this->request->encode($credentialSecret));
	}


	/**
	 * Check if the incoming request is signed using OAuth 2.0.  To determine this, OAuth parameters are searched
	 * for in the order of precedence as follows:
	 *
	 *   * Authorization header.
	 *   * POST variables.
	 *   * GET query string variables.
	 *
	 * @param   ROAuth2Request  $message  A ROAuth2Request object to populate with parameters.
	 *
	 * @return  boolean  True if parameters found, false otherwise.
	 *
	 * @since   1.0
	 */
	public function fetchMessageFromRequest()
	{
		// Setup the autoloader for the application classes.
		JLoader::register('ROAuth2RequestHeader', JPATH_REDRAD.'/oauth2/protocol/request/header.php');
		// Loading the response class
		$requestHeader = new ROAuth2RequestHeader;

		// First we look and see if we have an appropriate Authorization header.
		$authorization = $requestHeader->fetchAuthorizationHeader();

		if ($authorization)
		{
			$this->_headers = $requestHeader->processAuthorizationHeader($authorization);

			if ($this->_headers)
			{
				// Bind the found parameters to the OAuth 2.0 message.
				$this->setParameters($this->_headers);
			}
		}

		// Getting the method
		$method = strtolower($this->_method);
		// Building the class name
		$class = "ROAuth2Request". ucfirst($method);
		// Loading the class
		JLoader::register($class, JPATH_REDRAD."/oauth2/protocol/request/{$method}.php");
		// Creating the class
		$request = new $class;

		// If we didn't find an Authorization header or didn't find anything in it try the POST variables.
		$params = $request->processVars();

		if ($params)
		{
			// Bind the found parameters to the OAuth 2.0 message.
			$this->setParameters($params);

			return true;
		}

		// TODO: Check errors

		return false;

/*
		// If we didn't find an Authorization header or didn't find anything in it try the POST variables.
		$authorization = $requestPost->processVars();

		if ($authorization)
		{
			// Bind the found parameters to the OAuth 2.0 message.
			$this->setParameters($authorization);

			//return true;
		}

		// If we didn't find an Authorization header or didn't find anything in it try the POST variables.
		$authorization = $requestGet->processVars();

		if ($authorization)
		{
			// Bind the found parameters to the OAuth 2.0 message.
			$this->setParameters($authorization);

			//return true;
		}

		return false;
*/



	} // end method




	function validateACL()
	{

/*
		// Atempt to validate the OAuth message signature.
		$valid = $this->isValid(
				$this->_app->get('uri.request'),
				$this->_input->getMethod(),
				$client->secret,
				$credentials ? $credentials->getSecret() : null,
				$credentials ? $credentials->getVerifierKey() : null
		);

		// If the OAuth message signature isn't valid set the failure message and return.
		if (!$valid)
		{
			$this->_app->sendInvalidAuthMessage('Invalid OAuth request signature.');

			return 0;
		}

		// If the credentials are valid token credentials let's get the resource owner identity id.
		if ($credentials && ($credentials->getType() === ROAuth2Credentials::TOKEN))
		{
			return $credentials->getResourceOwnerId();
		}
*/
	}

	/**
	 * Perform a password authentication challenge.
	 *
	 * @param   string  $username  The username.
	 * @param   string  $password  The password.
	 *
	 * @return  integer  The authenticated user ID, or 0.
	 *
	 * @since   1.0
	 */
	//abstract protected function doPasswordAuthentication($username, $password);

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
	} // end method

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

} // end class

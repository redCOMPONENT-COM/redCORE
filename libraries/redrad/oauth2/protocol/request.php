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
	protected $_oauth_reserved = array(
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
	 * Get the list of reserved OAuth 2.0 parameters.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public static function getReservedParameters()
	{
		return self::$_reserved;
	}

	/**
	 * @var    JURI  The request URI for the message.
	 * @since  1.0
	 */
	private $_uri;

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
		$found = $this->_fetchMessageFromRequest();

		if (!$found)
		{
			return false;
		}

		// If we found an REST message somewhere we need to set the URI and request method.
		if ($found)
		{
			$this->_uri = new JURI($this->_fetchRequestUrl());
			$this->_method = strtoupper($_SERVER['REQUEST_METHOD']);

			// Get the OAuth user for the request.
			$this->_identity = $this->_fetchClient();

			// Getting the credentials
			$this->_credentials = $this->_fetchCredentials();

			// Check the authentication of this request
			$this->doOAuthAuthentication();

			//
			//$this->validateACL();

			switch ($this->response_type)
			{
				case 'code':

					// Build the response for the client.
					$response = array(
						'oauth_code' => $this->_credentials->getAccessToken(),
						'oauth_client_secret' => $this->_credentials->getClientSecret(),
						'oauth_state' => true
					);

					// Set the application response code and body.
					$this->_app->setHeader('status', '200');
					$this->_app->setBody(json_encode($response));
					$this->_app->respond();
					exit;

					break;
				case 'token':

					// Ensure the credentials are temporary.
					if ($this->_credentials->getType() !== ROAuth2Credentials::TEMPORARY)
					{
						$this->_app->setHeader('status', '400');
						$this->_app->setBody('The token is not for a temporary credentials set.');
						$this->_app->respond();
						exit;
					}

/*
					// Verify that we have a signed in user.
					if ($this->_identity->get('guest'))
					{
						$this->_app->setHeader('status', '400');
						$this->_app->setBody('You must first sign in.');
						$this->_app->respond();
						exit;
					}
*/
					// Attempt to authorise the credentials for the current user.
					$this->_credentials->authorise($this->_identity->id);

					if ($this->_credentials->getCallbackUrl() && $this->_credentials->getCallbackUrl() != 'oob')
					{
						$this->_app->redirect($this->_credentials->getCallbackUrl());
						$this->_app->respond();
						exit;
					}

					//$this->_app->setBody('Credentials authorised. The verifier token is ' . $this->_credentials->getToken());

					// Build the response for the client.
					$response = array(
						'access_token' => $this->_credentials->getResourceToken(),
						//'token_type' => $this->_credentials->getClientSecret(),
						'expires_in' => 3600,
						'refresh_token' => $this->_credentials->getRefreshToken()
					);

					// Set the application response code and body.
					$this->_app->setHeader('status', '200');
					$this->_app->setBody(json_encode($response));
					$this->_app->respond();
					exit;

					break;
				default:
					throw new InvalidArgumentException('No valid response type was found.');
					break;
			}
		}

		return $found;
	}

	/**
	 * Get an OAuth 2.0 credentials object based on the request message.
	 *
	 * @param   string  $token        The OAuth 2.0 token parameter for which to load the credentials.
	 * @param   string  $consumerKey  The OAuth 2.0 consumer_key parameter for which to load the credentials.
	 *
	 * @return  mixed  ROAuth2Credentials or boolean false if none exists.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	private function _fetchCredentials()
	{
		// If there is no credentials token then return false.
		if (empty($this->client_id))
		{
			return false;
		}

		$client_id = base64_decode($this->client_id);

		// Get an OAuth credentials object and load it using the incoming token.
		$credentials = new ROAuth2Credentials;


		switch ($this->response_type)
		{
			case 'code':

				$credentials->initialise($client_id, $this->client_secret,  $this->_uri, $this->_app->get('oauth.tokenlifetime', 3600));

				break;
			case 'token':

//echo $this->code;

				$credentials->load($this->code);


				// If there is an expiration date set and it is less than the current time, then the token is no longer good.
				if ($this->_credentials->getExpirationDate() > 0 && $this->_credentials->getExpirationDate() < time())
				{
					$this->_credentials->clean();

					$this->app->setHeader('status', '400');
					$this->app->setBody('The token has expired.');
				}


				break;
		}

		// Verify that the consumer key matches for the request and credentials.
		if ($client_id == $credentials->getClientId())
		{
			return $credentials;
		}
		else
		{
			throw new InvalidArgumentException('The OAuth credentials token is invalid.  Consumer key does not match.');
		}
	}

	/**
	 * Authenticate an identity using OAuth 1.0.  This will validate an OAuth 1.0 message for a valid client,
	 * credentials if present and signature.  If the message is valid and the credentials are token credentials
	 * then the resource owner id is returned as the authenticated identity.
	 *
	 * @since   1.0
	 */
	function doOAuthAuthentication()
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
		$password	= $parts[0];

		// Check the password
		$parts	= explode( ':', $this->_identity->password );
		$crypt	= $parts[0];
		$salt	= @$parts[1];
		$testcrypt = JUserHelper::getCryptedPassword($password, $salt);

		if ($crypt != $testcrypt) {
			$this->_app->sendInvalidAuthMessage('Username or password do not match');
			exit;
		}

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
		$signature = $this->sign($requestUrl, $requestMethod, $clientSecret, $credentialSecret);

		if ($this->signatureMethod != 'PLAINTEXT' && !$this->_nonce->validate($this->nonce, $this->consumerKey, $this->timestamp, $this->token))
		{
			// The nonce was invalid (either the timestamp was too old or it has already been used).
			return false;
		}

		return ($this->signature && ($signature == str_replace(' ', '+', $this->signature)));
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
		// Get a message signer object.
		$signer = $this->_fetchSigner();

echo "$requestUrl, $requestMethod, $clientSecret";

		// Get the base string for signing.
		$baseString = $this->_fetchStringForSigning($requestUrl, $requestMethod);

		return $signer->sign($baseString, rawurlencode($clientSecret), rawurlencode($credentialSecret));
	}

	/**
	 * Method to get a message signer object based on the message's oauth_signature_method parameter.
	 *
	 * @return  ROAuth2MessageSigner  The OAuth message signer object for the message.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	private function _fetchSigner()
	{
		// Register the classes for autoload.
		JLoader::registerPrefix('ROAuth2', JPATH_REDRAD.'/oauth2/protocol');

		// Setup the autoloader for the application classes.
		JLoader::register('ROAuth2MessageSigner', JPATH_REDRAD.'/oauth2/protocol/signer.php');

		switch ($this->signatureMethod)
		{
			case 'HMAC-SHA1':
				$signer = new ROAuth2MessageSignerHMAC;
				break;
			case 'RSA-SHA1':
				// @TODO We don't support RSA because we don't yet have a way to inject the private key.
				throw new InvalidArgumentException('RSA signatures are not supported');
				$signer = new ROAuth2MessageSignerRSA;
				break;
			case 'PLAINTEXT':

				// Setup the autoloader for the application classes.
				JLoader::register('ROAuth2MessageSignerPlaintext', JPATH_REDRAD.'/oauth2/protocol/signer/plaintext.php');

				$signer = new ROAuth2MessageSignerPlaintext;
				break;
			default:
				throw new InvalidArgumentException('No valid signature method was found.');
				break;
		}

		return $signer;
	}

	/**
	 * Method to get the OAuth message string for signing.
	 *
	 * Note: As of PHP 5.3 the rawurlencode() function is RFC 3986 compliant therefore this requires PHP 5.3+
	 *
	 * @param   string  $requestUrl     The message's request URL.
	 * @param   string  $requestMethod  The message's request method.
	 *
	 * @return  string  The unsigned OAuth message string.
	 *
	 * @link    http://www.faqs.org/rfcs/rfc3986
	 * @see     rawurlencode()
	 * @since   1.0
	 */
	private function _fetchStringForSigning($requestUrl, $requestMethod)
	{
		// Get a JURI instance for the request URL.
		$uri = new JURI($requestUrl);

		// Initialise base array.
		$base = array();

		// Get the found parameters.
		$params = $this->getParameters();;

		// Add the variables from the URI query string.
		foreach ($uri->getQuery(true) as $k => $v)
		{
			if (strpos($k, 'oauth_') !== 0)
			{
				$params[$k] = $v;
			}
		}

		// Make sure that any found oauth_signature is not included.
		unset($params['oauth_signature']);

		// Ensure the parameters are in order by key.
		ksort($params);

		// Iterate over the keys to add properties to the base.
		foreach ($params as $key => $value)
		{
			// If we have multiples for the parameter let's loop over them.
			if (is_array($value))
			{
				// Don't want to do this more than once in the inner loop.
				$key = rawurlencode($key);

				// Sort the value array and add each one.
				sort($value, SORT_STRING);

				foreach ($value as $v)
				{
					$base[] = $key . '=' . rawurlencode($v);
				}
			}
			// The common case is that there is one entry per property.
			else
			{
				$base[] = rawurlencode($key) . '=' . rawurlencode($value);
			}
		}

		// Start off building the base string by adding the request method and URI.
		$base = array(
			rawurlencode(strtoupper($requestMethod)),
			rawurlencode(strtolower($uri->toString(array('scheme', 'user', 'pass', 'host', 'port'))) . $uri->getPath()),
			rawurlencode(implode('&', $base))
		);

		return implode('&', $base);
	} // end method


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
	 * Get an OAuth 2.0 client object based on the request message.
	 *
	 * @param   string  $consumerKey  The OAuth 2.0 consumer_key parameter for which to load the client.
	 *
	 * @return  ROAuth2User
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	private function _fetchClient()
	{
		$consumerKey = base64_decode($this->client_id);

		// Ensure there is a consumer key.
		if (empty($consumerKey))
		{
			throw new InvalidArgumentException('There is no OAuth consumer key in the request.');
		}

		// Get an OAuth client object and load it using the incoming client key.
		$client = new ROAuth2User;
		$client->loadByKey($consumerKey);

		// Verify the client key for the message.
		if ($client->username != $consumerKey)
		{
			throw new InvalidArgumentException('The OAuth consumer key is not valid.');
		}

		return $client;
	}

	/**
	 * Authenticate an identity using HTTP Basic authentication for the request.
	 *
	 * @return  integer  Identity ID for the authenticated identity.
	 *
	 * @since   1.0
	 */
	protected function doBasicAuthentication()
	{
		// If we have basic auth information attempt to authenticate.
		$username = $this->_input->server->getString('OAUTH_CLIENT_ID');

		if ($username)
		{
			$password = $this->_input->server->getString('PHP_AUTH_PW');

			$identityId = $this->doPasswordAuthentication($username, $password);

			return $identityId;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Parse the request POST variables for OAuth parameters.
	 *
	 * @return  mixed  Array of OAuth 1.0 parameters if found or boolean false otherwise.
	 *
	 * @since   1.0
	 */
	private function _processPostVars()
	{
		// If we aren't handling a post request with urlencoded vars then there is nothing to do.
		if (strtoupper($this->_input->getMethod()) != 'POST'
			|| strtolower($this->_input->server->get('CONTENT_TYPE', '')) != 'application/x-www-form-urlencoded')
		{
			return false;
		}

		// Initialise variables.
		$parameters = array();

		// Iterate over the reserved parameters and look for them in the POST variables.
		foreach (ROAuth2Request::getReservedParameters() as $k)
		{
			if ($this->_input->post->getString($k, false))
			{
				$parameters[$k] = trim($this->_input->post->getString($k));
			}
		}

		// If we didn't find anything return false.
		if (empty($parameters))
		{
			return false;
		}

		return $parameters;
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
	private function _fetchMessageFromRequest()
	{
		// First we look and see if we have an appropriate Authorization header.
		$authorization = $this->_fetchAuthorizationHeader();

		if ($authorization)
		{
			$this->_headers = $this->_processAuthorizationHeader($authorization);

			if ($this->_headers)
			{
				// Bind the found parameters to the OAuth 2.0 message.
				$this->setParameters($this->_headers);

				return true;
			}
		}

		// If we didn't find an Authorization header or didn't find anything in it try the POST variables.
		$authorization = $this->_processPostVars();

		if ($authorization)
		{
			// Bind the found parameters to the OAuth 2.0 message.
			$this->setParameters($authorization);

			return true;
		}

		return false;
	}

	/**
	 * Get the HTTP request headers.  Header names have been normalized, stripping
	 * the leading 'HTTP_' if present, and capitalizing only the first letter
	 * of each word.
	 *
	 * @return  string  The Authorization header if it has been set.
	 */
	private function _fetchAuthorizationHeader()
	{
		// The simplest case is if the apache_request_headers() function exists.
		if (function_exists('apache_request_headers'))
		{
			$headers = apache_request_headers();

			if (isset($headers['Authorization']))
			{
				return trim($headers['Authorization']);
			}
		}
		// Otherwise we need to look in the $_SERVER superglobal.
		elseif ($this->_input->server->getString('HTTP_AUTHORIZATION', false))
		{
			return trim($this->_input->server->getString('HTTP_AUTHORIZATION'));
		}
		elseif ($this->_input->server->getString('HTTP_AUTH_USER', false))
		{
			return trim($this->_input->server->getString('HTTP_AUTH_USER'));
		}
		elseif ($this->_input->server->getString('HTTP_USER', false))
		{
			return trim($this->_input->server->getString('HTTP_USER'));
		}

		return false;
	}


	/**
	 * Parse an OAuth authorization header and set any found OAuth parameters.
	 *
	 * @param   string  $header  Authorization header.
	 *
	 * @return  mixed  Array of OAuth 1.2 parameters if found or boolean false otherwise.
	 *
	 * @since   1.0
	 */
	private function _processAuthorizationHeader($header)
	{
		// Initialise variables.
		$parameters = array();

		$server = $_SERVER;

    $headers = array();
    foreach ($server as $key => $value) {
      if (0 === strpos($key, 'HTTP_')) {
        $headers[substr($key, 5)] = $value;
      }
      // CONTENT_* are not prefixed with HTTP_
      elseif (in_array($key, array('CONTENT_LENGTH', 'CONTENT_MD5', 'CONTENT_TYPE'))) {
        $headers[strtolower($key)] = $value;
      }
    }

    if (isset($server['PHP_AUTH_USER'])) {
      $headers['PHP_AUTH_USER'] = $server['PHP_AUTH_USER'];
      $headers['PHP_AUTH_PW'] = isset($server['PHP_AUTH_PW']) ? $server['PHP_AUTH_PW'] : '';
    } else {
      /*
			* php-cgi under Apache does not pass HTTP Basic user/pass to PHP by default
			* For this workaround to work, add this line to your .htaccess file:
			* RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
			*
			* A sample .htaccess file:
			* RewriteEngine On
			* RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
			* RewriteCond %{REQUEST_FILENAME} !-f
			* RewriteRule ^(.*)$ app.php [QSA,L]
			*/

      $authorizationHeader = null;
      if (isset($server['HTTP_AUTHORIZATION'])) {
        $authorizationHeader = $server['HTTP_AUTHORIZATION'];
      } elseif (isset($server['REDIRECT_HTTP_AUTHORIZATION'])) {
        $authorizationHeader = $server['REDIRECT_HTTP_AUTHORIZATION'];
      } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();

        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

        if (isset($requestHeaders['Authorization'])) {
            $authorizationHeader = trim($requestHeaders['Authorization']);
        }
      }

      if (null !== $authorizationHeader) {
        $headers['AUTHORIZATION'] = $authorizationHeader;
        // Decode AUTHORIZATION header into PHP_AUTH_USER and PHP_AUTH_PW when authorization header is basic
        if (0 === stripos($authorizationHeader, 'basic')) {
          $exploded = explode(':', base64_decode(substr($authorizationHeader, 6)));
          if (count($exploded) == 2) {
              list($headers['PHP_AUTH_USER'], $headers['PHP_AUTH_PW']) = $exploded;
          }
        }
      }
    }

    // PHP_AUTH_USER/PHP_AUTH_PW
    if (isset($headers['PHP_AUTH_USER'])) {
        $headers['AUTHORIZATION'] = 'Basic '.base64_encode($headers['PHP_AUTH_USER'].':'.$headers['PHP_AUTH_PW']);
    }

    // PHP_USER/PHP_PW
    if (isset($headers['PHP_USER']) && empty($headers['AUTHORIZATION']) ) {
        $headers['AUTHORIZATION'] = 'Basic '.base64_encode($headers['PHP_USER'].':'.$headers['PHP_PW']);
    }

		// If we didn't find anything return false.
		if (empty($headers))
		{
			return false;
		}

		return $headers;
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
	private function _fetchRequestUrl()
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

<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('JPATH_REDCORE') or die;

/**
 * Class to represent a HAL standard object.
 *
 * @since  1.2
 */
class RApiOauth2Oauth2 extends RApi
{
	/**
	 * Option name parameter
	 * @var array
	 */
	public $optionName = null;

	/**
	 * Main OAuth2 Server object
	 * @var OAuth2\Server
	 */
	public $server = null;

	/**
	 * Main OAuth2 Server configuration
	 * @var array
	 */
	public $serverConfig = null;

	/**
	 * Result of OAuth2 Server response
	 * @var OAuth2\ResponseInterface
	 */
	public $response = null;

	/**
	 * Method to instantiate the file-based api call.
	 *
	 * @param   mixed  $options  Optional custom options to load. JRegistry or array format
	 *
	 * @since   1.2
	 */
	public function __construct($options = null)
	{
		parent::__construct($options);

		// Get the global JAuthentication object.
		jimport('joomla.user.authentication');

		// Register OAuth2 classes
		require_once dirname(__FILE__) . '/Autoloader.php';
		OAuth2\Autoloader::register();

		// OAuth2 Server config from plugin
		$this->serverConfig = array(
			'use_jwt_access_tokens'        => (boolean) RBootstrap::getConfig('oauth2_use_jwt_access_tokens', false),
			'store_encrypted_token_string' => (boolean) RBootstrap::getConfig('oauth2_store_encrypted_token_string', true),
			'use_openid_connect'       => (boolean) RBootstrap::getConfig('oauth2_use_openid_connect', false),
			'id_lifetime'              => RBootstrap::getConfig('oauth2_id_lifetime', 3600),
			'access_lifetime'          => RBootstrap::getConfig('oauth2_access_lifetime', 3600),
			'www_realm'                => 'Service',
			'token_param_name'         => RBootstrap::getConfig('oauth2_token_param_name', 'access_token'),
			'token_bearer_header_name' => RBootstrap::getConfig('oauth2_token_bearer_header_name', 'Bearer'),
			'enforce_state'            => (boolean) RBootstrap::getConfig('oauth2_enforce_state', true),
			'require_exact_redirect_uri' => (boolean) RBootstrap::getConfig('oauth2_require_exact_redirect_uri', true),
			'allow_implicit'           => (boolean) RBootstrap::getConfig('oauth2_allow_implicit', false),
			'allow_credentials_in_request_body' => (boolean) RBootstrap::getConfig('oauth2_allow_credentials_in_request_body', true),
			'allow_public_clients'     => (boolean) RBootstrap::getConfig('oauth2_allow_public_clients', true),
			'always_issue_new_refresh_token' => (boolean) RBootstrap::getConfig('oauth2_always_issue_new_refresh_token', false),
			'unset_refresh_token_after_use' => (boolean) RBootstrap::getConfig('oauth2_unset_refresh_token_after_use', true),
		);

		// Set database names to Redcore DB tables
		$prefix = JFactory::getDbo()->getPrefix();
		$databaseConfig = array(
			'client_table' => $prefix . 'redcore_oauth_clients',
			'access_token_table' => $prefix . 'redcore_oauth_access_tokens',
			'refresh_token_table' => $prefix . 'redcore_oauth_refresh_tokens',
			'code_table' => $prefix . 'redcore_oauth_authorization_codes',
			'user_table' => $prefix . 'redcore_oauth_users',
			'jwt_table'  => $prefix . 'redcore_oauth_jwt',
			'jti_table'  => $prefix . 'redcore_oauth_jti',
			'scope_table'  => $prefix . 'redcore_oauth_scopes',
			'public_key_table'  => $prefix . 'redcore_oauth_public_keys',
		);

		$conf = JFactory::getConfig();

		$dsn      = 'mysql:dbname=' . $conf->get('db') . ';host=' . $conf->get('host');
		$username = $conf->get('user');
		$password = $conf->get('password');

		$storage = new OAuth2\Storage\Pdoredcore(array('dsn' => $dsn, 'username' => $username, 'password' => $password), $databaseConfig);
		$this->server = new OAuth2\Server($storage, $this->serverConfig);

		// Add the "Authorization Code" grant type (this is where the oauth magic happens)
		$this->server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage, $this->serverConfig));

		// Add the "Client Credentials" grant type (it is the simplest of the grant types)
		$this->server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage, $this->serverConfig));

		// Add the "User Credentials" grant type (this is modified to suit Joomla authorization)
		$this->server->addGrantType(new OAuth2\GrantType\UserCredentials($storage, $this->serverConfig));

		// Add the "Refresh Token" grant type (this is great for extending expiration time on tokens)
		$this->server->addGrantType(new OAuth2\GrantType\RefreshToken($storage, $this->serverConfig));

		/*
		 * @todo Implement JwtBearer Grant type with public_key
		// Typically, the URI of the oauth server
		$audience = rtrim(JUri::base(), '/');

		// Add the "Refresh Token" grant type (this is great for extending expiration time on tokens)
		$this->server->addGrantType(new OAuth2\GrantType\JwtBearer($storage, $audience));
		*/

		// Init Environment
		$this->setApiOperation();
	}

	/**
	 * Set Method for Api
	 *
	 * @param   string  $operation  Operation name
	 *
	 * @return  RApi
	 *
	 * @since   1.2
	 */
	public function setApiOperation($operation = '')
	{
		if (!empty($operation))
		{
			$this->options->set('optionName', $operation);
		}

		$this->operation = strtolower($this->options->get('optionName', ''));

		return $this;
	}

	/**
	 * Execute the Api operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 * @throws  RuntimeException
	 */
	public function execute()
	{
		if (!$this->isOperationAllowed())
		{
			throw new RuntimeException(JText::_('LIB_REDCORE_API_HAL_OPERATION_NOT_ALLOWED'));
		}

		switch ($this->operation)
		{
			case 'token':
				$this->apiToken();
				$this->cleanExpiredTokens($this->operation);
				break;
			case 'resource':
				$this->apiResource();
				break;
			case 'authorize':
				$this->apiAuthorize();
				$this->cleanExpiredTokens($this->operation);
				break;
			case 'profile':
				$this->apiProfile();
				break;
		}

		return $this;
	}

	/**
	 * Method to update client scopes.
	 *
	 * @param   string  $operation  Operation name
	 *
	 * @return  boolean  True on success, False on error.
	 */
	public function cleanExpiredTokens($operation)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		switch ($operation)
		{
			// Delete Access Tokens
			case 'token':
				$query->delete('#__redcore_oauth_access_tokens')
					->where($db->qn('expires') . ' < ' . $db->q(date('Y-m-d H:i:s', strtotime('-2 weeks'))));
				$db->setQuery($query);
				$db->execute();

				$query = $db->getQuery(true)
					->delete('#__redcore_oauth_refresh_tokens')
					->where($db->qn('expires') . ' < ' . $db->q(date('Y-m-d H:i:s', strtotime('-2 weeks'))));
				$db->setQuery($query);
				$db->execute();
				break;

			// Delete Authorization codes
			case 'authorize':
			default:
				$query->delete('#__redcore_oauth_authorization_codes')
					->where($db->qn('expires') . ' < ' . $db->q(date('Y-m-d H:i:s', strtotime('-2 weeks'))));
				$db->setQuery($query);
				$db->execute();
				break;
		}

		return true;
	}

	/**
	 * Execute the Api Profile operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.7
	 */
	public function apiProfile()
	{
		// Handle a request for an OAuth2.0 Access Token and send the response to the client if the token has expired
		if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals(), null, $scopeToCheck = ''))
		{
			$this->response = $this->server->getResponse();

			return $this;
		}

		// We can take token and add more data to it
		$token = $this->server->getResourceController()->getToken();

		if (!empty($token['user_id']))
		{
			$this->loadUserIdentity($token['user_id']);
		}

		// Add expire Time
		$token['expireTimeFormatted'] = date('Y-m-d H:i:s', $token['expires']);

		// Add user Profile info
		$token['profile'] = RApiOauth2Helper::getUserProfileInformation();

		$this->response = json_encode($token);

		return $this;
	}

	/**
	 * Loads up user identity when requested
	 *
	 * @param   int  $userId  User ID
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.8
	 */
	public function loadUserIdentity($userId)
	{
		if (!empty($userId))
		{
			$user = JFactory::getUser($userId);

			// Load the JUser class on application for this client
			JFactory::getApplication()->loadIdentity($user);
			JFactory::getSession()->set('user', $user);
		}

		return $this;
	}

	/**
	 * Execute the Api Token operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	public function apiToken()
	{
		$request = OAuth2\Request::createFromGlobals();

		// Implicit grant type and Authorization code grant type require user to be logged in before authorising
		if ($request->request('grant_type') == 'implicit')
		{
			$user = $this->getLoggedUser();
		}

		$this->response = $this->server->handleTokenRequest($request);

		if ($this->response instanceof OAuth2\Response)
		{
			$expires = $this->response->getParameter('expires_in');

			if ($expires)
			{
				// Add expire Time
				$this->response->setParameter('expireTimeFormatted', date('Y-m-d H:i:s', $expires + time()));
				$this->response->setParameter('created', date('Y-m-d H:i:s'));
			}

			$this->response->setParameter('profile', RApiOauth2Helper::getUserProfileInformation());
		}

		return $this;
	}

	/**
	 * Execute the Api Resource operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	public function apiResource()
	{
		$scopeToCheck = $this->options->get('scope', '');
		$scopes = array();

		if (is_array($scopeToCheck) && count($scopeToCheck) > 0)
		{
			$scopes = $scopeToCheck;
			$scopeToCheck = null;
		}

		// Handle a request for an OAuth2.0 Access Token and send the response to the client
		if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals(), null, $scopeToCheck))
		{
			$this->response = $this->server->getResponse();

			return $this;
		}

		$token = $this->server->getResourceController()->getToken();

		if (!empty($scopes))
		{
			$requestValid = false;

			// Check all scopes
			foreach ($scopes as $scope)
			{
				if (!empty($scope) && !empty($token["scope"]) && $this->server->getScopeUtil()->checkScope($scope, $token['scope']))
				{
					$requestValid = true;
					break;
				}
			}

			if (!$requestValid)
			{
				$this->response = $this->server->getResponse();
				$this->response->setError(403, 'insufficient_scope', JText::_('LIB_REDCORE_API_OAUTH2_SERVER_INSUFFICIENT_SCOPE'));
				$this->response->addHttpHeaders(
					array(
						'WWW-Authenticate' => sprintf('%s realm="%s", scope="%s", error="%s", error_description="%s"',
							$this->server->getTokenType()->getTokenType(),
							$this->serverConfig['www_realm'],
							implode(', ', $scopes),
							$this->response->getParameter('error'),
							$this->response->getParameter('error_description')
						)
					)
				);

				return $this;
			}
		}

		$this->response = json_encode(
			array('success' => true, 'user_id' => $token['user_id'], 'message' => JText::_('LIB_REDCORE_API_OAUTH2_SERVER_ACCESS_SUCCESS'))
		);

		return $this;
	}

	/**
	 * Execute the Api Authorize operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	public function apiAuthorize()
	{
		$user = $this->getLoggedUser();
		$request = OAuth2\Request::createFromGlobals();
		$response = new OAuth2\Response;

		// Validate the authorize request
		if (!$this->server->validateAuthorizeRequest($request, $response))
		{
			$this->response = $response;

			return $this;
		}

		$clientId = $request->query('client_id');
		$scopes = RApiOauth2Helper::getClientScopes($clientId);

		if ($request->request('authorized', '') == '')
		{
			$clientScopes = !empty($scopes) ? explode(' ', $scopes) : array();

			if (!empty($clientScopes))
			{
				$clientScopes = RApiHalHelper::getWebserviceScopes($clientScopes);
			}

			$currentUri = JUri::getInstance();
			$formAction = JUri::root() . 'index.php?' . $currentUri->getQuery();

			// Display an authorization form
			$this->response = RLayoutHelper::render(
				'oauth2.authorize',
				array(
					'view' => $this,
					'options' => array (
						'clientId' => $clientId,
						'formAction' => $formAction,
						'scopes' => $clientScopes,
					)
				)
			);

			return $this;
		}

		// Print the authorization code if the user has authorized your client
		$is_authorized = $request->request('authorized', '') === JText::_('LIB_REDCORE_API_OAUTH2_SERVER_AUTHORIZE_CLIENT_YES')
			|| $request->request('authorized', '') == '1';

		// We are setting client scope instead of requesting scope from user request
		$request->request['scope'] = $scopes;
		$this->server->handleAuthorizeRequest($request, $response, $is_authorized, $user->id);

		// We add access_token directly to the URI of the redirect string (default is after the # character)
		if (RBootstrap::getConfig('oauth2_redirect_with_token', 0))
		{
			if ($response->isRedirection())
			{
				$location = $response->getHttpHeader('Location');
				$location = explode('#', $location);

				// Get access token
				if (isset($location[1]))
				{
					$location[1] = str_replace('&amp;', '&', $location[1]);
					$uris = explode('&', $location[1]);

					// We search for access_token parameter
					foreach ($uris as $uri)
					{
						if (strpos($uri, RBootstrap::getConfig('oauth2_token_param_name', 'access_token')) === 0)
						{
							$location[0] .= '&' . $uri;
							break;
						}
					}

					$location = implode('#', $location);
					$response->setHttpHeader('Location', $location);
				}
			}
		}

		$this->response = $response;

		return $this;
	}

	/**
	 * Checks if operation is allowed from the configuration file
	 *
	 * @return object This method may be chained.
	 *
	 * @throws  RuntimeException
	 */
	public function isOperationAllowed()
	{
		if (empty($this->operation))
		{
			throw new RuntimeException(JText::_('LIB_REDCORE_API_OAUTH2_OPERATION_NOT_SPECIFIED'));
		}

		return true;
	}

	/**
	 * Gets logged In user or redirect to login page
	 *
	 * @return JUser  Instance of the logged in user
	 */
	public function getLoggedUser()
	{
		$user = JFactory::getUser();

		// If user is not logged in we redirect him to the login page
		if (empty($user->id))
		{
			$currentUri = JUri::getInstance();
			$returnUrl = JUri::root() . 'index.php?' . $currentUri->getQuery();

			$loginLink = RRoute::_(JUri::root() . 'index.php?option=com_users&view=login');

			$loginPage = new JUri($loginLink);
			$loginPage->setVar('return', base64_encode(htmlspecialchars($returnUrl)));

			JFactory::getApplication()->redirect($loginPage);
			JFactory::getApplication()->close();
		}

		return $user;
	}

	/**
	 * Method to send the application response to the client.  All headers will be sent prior to the main
	 * application output data.
	 *
	 * @return  void
	 *
	 * @since   1.2
	 */
	public function render()
	{
		if ($this->response instanceof OAuth2\ResponseInterface)
		{
			$this->response->send();
		}
		else
		{
			$app = JFactory::getApplication();

			$body = $this->response;

			// Check if the request is CORS ( Cross-origin resource sharing ) and change the body if true
			$body = $this->prepareBody($body);

			json_decode((string) $body);

			if (json_last_error() == JSON_ERROR_NONE)
			{
				$app->setHeader('Content-length', strlen($body), true);
				$app->setHeader('Content-type', 'application/json; charset=UTF-8', true);
				$app->sendHeaders();
			}

			echo (string) $body;
		}
	}

	/**
	 * Prepares body for response
	 *
	 * @param   string  $message  The return message
	 *
	 * @return  string	The message prepared
	 *
	 * @since   1.2
	 */
	public function prepareBody($message)
	{
		return $message;
	}
}

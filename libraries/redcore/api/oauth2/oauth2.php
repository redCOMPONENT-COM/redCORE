<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

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
	 * Main Oauth2 Server object
	 * @var OAuth2\Server
	 */
	public $server = null;

	/**
	 * Result of Oauth2 Server response
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

		// Register OAuth2 classes
		require_once dirname(__FILE__) . '/Autoloader.php';
		OAuth2\Autoloader::register();

		// OAuth2 Server config from plugin
		$serverConfig = array(
			'use_crypto_tokens'        => (boolean) RTranslationHelper::$pluginParams->get('oauth2_use_crypto_tokens', false),
			'store_encrypted_token_string' => (boolean) RTranslationHelper::$pluginParams->get('oauth2_store_encrypted_token_string', true),
			'use_openid_connect'       => (boolean) RTranslationHelper::$pluginParams->get('oauth2_use_openid_connect', false),
			'id_lifetime'              => RTranslationHelper::$pluginParams->get('oauth2_id_lifetime', 3600),
			'access_lifetime'          => RTranslationHelper::$pluginParams->get('oauth2_access_lifetime', 3600),
			'www_realm'                => 'Service',
			'token_param_name'         => RTranslationHelper::$pluginParams->get('oauth2_token_param_name', 'access_token'),
			'token_bearer_header_name' => RTranslationHelper::$pluginParams->get('oauth2_token_bearer_header_name', 'Bearer'),
			'enforce_state'            => (boolean) RTranslationHelper::$pluginParams->get('oauth2_enforce_state', true),
			'require_exact_redirect_uri' => (boolean) RTranslationHelper::$pluginParams->get('oauth2_require_exact_redirect_uri', true),
			'allow_implicit'           => (boolean) RTranslationHelper::$pluginParams->get('oauth2_allow_implicit', false),
			'allow_credentials_in_request_body' => (boolean) RTranslationHelper::$pluginParams->get('oauth2_allow_credentials_in_request_body', true),
			'allow_public_clients'     => (boolean) RTranslationHelper::$pluginParams->get('oauth2_allow_public_clients', true),
			'always_issue_new_refresh_token' => (boolean) RTranslationHelper::$pluginParams->get('oauth2_always_issue_new_refresh_token', false),
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
			'scope_table'  => $prefix . 'redcore_oauth_scopes',
			'public_key_table'  => $prefix . 'redcore_oauth_public_keys',
		);

		$conf = JFactory::getConfig();

		$dsn      = 'mysql:dbname=' . $conf->get('db') . ';host=' . $conf->get('host');
		$username = $conf->get('user');
		$password = $conf->get('password');

		$storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password), $databaseConfig);
		$this->server = new OAuth2\Server($storage, $serverConfig);

		// Add the "Client Credentials" grant type (it is the simplest of the grant types)
		$this->server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage, $serverConfig));

		// Add the "Authorization Code" grant type (this is where the oauth magic happens)
		$this->server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage, $serverConfig));

		// Init Environment
		$this->setApiOperation();

		// @todo Add scopes
		//$doctrine = $storage->getTable('OAuth2Scope');
		//$scopeUtil = new OAuth2\Scope($doctrine);
		//$this->server->setScopeUtil($scopeUtil);
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
				break;
			case 'resource':
				$this->apiResource();
				break;
			case 'authorize':
				$this->apiAuthorize();
				break;
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
		$this->response = $this->server->handleTokenRequest(OAuth2\Request::createFromGlobals());

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
		$scope = $this->options->get('scope', '');

		// Handle a request for an OAuth2.0 Access Token and send the response to the client
		if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals(), null, $scope))
		{
			$this->response = $this->server->getResponse();

			return $this;
		}

		$token = $this->server->getResourceController()->getToken();

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
		$request = OAuth2\Request::createFromGlobals();
		$response = new OAuth2\Response;

		// Validate the authorize request
		if (!$this->server->validateAuthorizeRequest($request, $response))
		{
			$this->response = $response;

			return $this;
		}

		if (empty($_POST))
		{
			// Display an authorization form
			$this->response = RLayoutHelper::render('oauth2.authorize');

			return $this;
		}

		// Print the authorization code if the user has authorized your client
		$is_authorized = ($_POST['authorized'] === 'yes');
		$this->server->handleAuthorizeRequest($request, $response, $is_authorized);

		/*if ($is_authorized && $_POST['debug'])
		{
			// This is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
			$code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);

			exit("SUCCESS! Authorization Code: $code");
		}*/

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
			$body = $this->response;

			// Check if the request is CORS ( Cross-origin resource sharing ) and change the body if true
			$body = $this->prepareBody($body);

			if ($body_json = json_decode($body))
			{
				if (json_last_error() == JSON_ERROR_NONE)
				{
					$body = json_encode($body);
				}
			}

			echo $body;
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

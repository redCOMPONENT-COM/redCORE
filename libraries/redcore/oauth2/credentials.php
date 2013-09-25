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

defined('JPATH_PLATFORM') or die;

/**
 * OAuth Credentials base class for redCORE
 *
 * @package     Redcore
 * @subpackage  OAuth2
 * @since       1.0
 */
class ROauth2Credentials
{
	/**
	 * @var    integer  Indicates temporary credentials.  These are ready to be authorised.
	 * @since  1.0
	 */
	const TEMPORARY = 0;

	/**
	 * @var    integer  Indicates authorised temporary credentials.  These are ready to be converted to token credentials.
	 * @since  1.0
	 */
	const AUTHORISED = 1;

	/**
	 * @var    integer  Indicates token credentials.  These are ready to be used for accessing protected resources.
	 * @since  1.0
	 */
	const TOKEN = 2;

	/**
	 * @var    ROauth2TableCredentials  Connector object for table class.
	 * @since  1.0
	 */
	public $_table;

	/**
	 * @var    ROauth2CredentialsState  The current credential state.
	 * @since  1.0
	 */
	public $_state;

	/**
	 * @var    ROauth2ProtocolRequest   The current HTTP request.
	 * @since  1.0
	 */
	public $_request;

	/**
	 * Object constructor.
	 *
	 * @param   ROauth2ProtocolRequest   $request  The HTTP request
	 * @param   ROauth2TableCredentials  $table    Connector object for table class.
	 *
	 * @since   1.0
	 */
	public function __construct(ROauth2ProtocolRequest $request, ROauth2TableCredentials $table = null)
	{
		// Load the HTTP request
		$this->_request = $request ? $request : new ROauth2ProtocolRequest;

		// Setup the database object.
		$this->_table = $table ? $table : JTable::getInstance('Credentials', 'ROauth2Table');

		// Assume the base state for any credentials object to be new.
		$this->_state = new ROauth2CredentialsStateNew($this->_table);

		// Setup the correct signer
		$signature = isset($this->_request->signature_method) ? $this->_request->signature_method : 'PLAINTEXT';
		$this->_signer = ROauth2CredentialsSigner::getInstance($signature);
	}

	/**
	 * Method to authorise the credentials.  This will persist a temporary credentials set to be authorised by
	 * a resource owner.
	 *
	 * @param   integer  $resourceOwnerId  The id of the resource owner authorizing the temporary credentials.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  LogicException
	 */
	public function authorise($resourceOwnerId)
	{
		$this->_state = $this->_state->authorise($resourceOwnerId);
	}

	/**
	 * Method to convert a set of authorised credentials to token credentials.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  LogicException
	 */
	public function convert()
	{
		$this->_state = $this->_state->convert();
	}

	/**
	 * Method to deny a set of temporary credentials.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  LogicException
	 */
	public function deny()
	{
		$this->_state = $this->_state->deny();
	}

	/**
	 * Get the callback url associated with this token.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getCallbackUrl()
	{
		return $this->_state->callback_url;
	}

	/**
	 * Get the consumer key associated with this token.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getClientId()
	{
		return $this->_state->client_id;
	}

	/**
	 * Get the credentials key value.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getClientSecret()
	{
		return $this->_state->client_secret;
	}

	/**
	 * Get the temporary token secret.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getTemporaryToken()
	{
		return $this->_state->temporary_token;
	}

	/**
	 * Get the token secret.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getAccessToken()
	{
		return $this->_state->access_token;
	}

	/**
	 * Get the token secret.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getRefreshToken()
	{
		return $this->_state->refresh_token;
	}

	/**
	 * Get the ID of the user this token has been issued for.  Not all tokens
	 * will have known users.
	 *
	 * @return  integer
	 *
	 * @since   1.0
	 */
	public function getResourceOwnerId()
	{
		return $this->_state->resource_owner_id;
	}

	/**
	 * Get the credentials type.
	 *
	 * @return  integer
	 *
	 * @since   1.0
	 */
	public function getType()
	{
		return (int) $this->_state->type;
	}

	/**
	 * Get the expiration date.
	 *
	 * @return  integer
	 *
	 * @since   1.0
	 */
	public function getExpirationDate()
	{
		return $this->_state->expiration_date;
	}

	/**
	 * Get the temporary expiration date.
	 *
	 * @return  integer
	 *
	 * @since   1.0
	 */
	public function getTemporaryExpirationDate()
	{
		return $this->_state->temporary_expiration_date;
	}

	/**
	 * Method to initialise the credentials.  This will persist a temporary credentials set to be authorised by
	 * a resource owner.
	 *
	 * @param   string  $clientId  The key of the client requesting the temporary credentials.
	 * @param   int     $lifetime  The lifetime limit of the token.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  LogicException
	 */
	public function initialise($clientId, $lifetime = 'PT1H')
	{
		$clientSecret = $this->_signer->secretDecode($this->_request->client_secret);

		$this->_state = $this->_state->initialise($clientId, $clientSecret, $this->_request->_fetchRequestUrl(), $lifetime);
	}

	/**
	 * Perform a password authentication challenge.
	 *
	 * @param   ROauth2Client  $client  The client.
	 *
	 * @return  boolean  True if authentication is ok, false if not
	 *
	 * @since   1.0
	 */
	public function doJoomlaAuthentication(ROauth2Client $client)
	{
		return $this->_signer->doJoomlaAuthentication($client, $this->_request);
	}

	/**
	 * Method to load a set of credentials by key.
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function load()
	{
		// Initialise credentials_id
		$this->_table->credentials_id = 0;

		// Load the credential
		if ( isset($this->_request->response_type) && !isset($this->_request->access_token) )
		{
			// Get the correct client secret key
			$key = $this->_signer->secretDecode($this->_request->client_secret);

			$this->_table->loadBySecretKey($key, $this->_request->_fetchRequestUrl());
		}
		elseif (isset($this->_request->access_token))
		{
			$this->_table->loadByAccessToken($this->_request->access_token, $this->_request->_fetchRequestUrl());
		}

		// If nothing was found we will setup a new credential state object.
		if (!$this->_table->credentials_id)
		{
			$this->_state = new ROauth2CredentialsStateNew($this->_table);

			return false;
		}

		// Cast the type for validation.
		$this->_table->type = (int) $this->_table->type;

		// If we are loading a temporary set of credentials load that state.
		if ($this->_table->type === self::TEMPORARY)
		{
			$this->_state = new ROauth2CredentialsStateTemporary($this->_table);
		}

		// If we are loading a authorised set of credentials load that state.
		elseif ($this->_table->type === self::AUTHORISED)
		{
			$this->_state = new ROauth2CredentialsStateAuthorised($this->_table);
		}

		// If we are loading a token set of credentials load that state.
		elseif ($this->_table->type === self::TOKEN)
		{
			$this->_state = new ROauth2CredentialsStateToken($this->_table);
		}

		// Unknown OAuth credential type.
		else
		{
			throw new InvalidArgumentException('OAuth credentials not found.');
		}

		return true;
	}

	/**
	 * Sign the request for resource
	 *
	 * @return  bool  True if sign is fine, false if not.
	 *
	 * @since   1.0
	 */
	public function sign()
	{
		return $this->_signer->sign($this->_state, $this->_request);
	}

	/**
	 * Delete expired credentials.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function clean()
	{
		$this->_table->clean();
	}

	/**
	 * Method to revoke a set of token credentials.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  LogicException
	 */
	public function revoke()
	{
		$this->_state = $this->_state->revoke();
	}
}

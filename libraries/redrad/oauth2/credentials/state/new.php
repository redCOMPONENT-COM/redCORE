<?php
/**
 * @package     Joomla.Platform
 * @subpackage  OAuth2
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * OAuth New Credentials class for the Joomla Platform
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth2
 * @since       1.0
 */
class ROAuth2CredentialsStateNew extends ROAuth2CredentialsState
{
	/**
	 * Method to authorise the credentials.  This will persist a temporary credentials set to be authorised by
	 * a resource owner.
	 *
	 * @param   integer  $resourceOwnerId  The id of the resource owner authorizing the temporary credentials.
	 * @param   integer  $lifetime         How long the permanent credentials should be valid (defaults to forever).
	 *
	 * @return  ROAuth2CredentialsState
	 *
	 * @since   1.0
	 * @throws  LogicException
	 */
	public function authorise($resourceOwnerId, $lifetime = 0)
	{
		throw new LogicException('Only temporary credentials can be authorised.');
	}

	/**
	 * Method to convert a set of authorised credentials to token credentials.
	 *
	 * @return  ROAuth2CredentialsState
	 *
	 * @since   1.0
	 * @throws  LogicException
	 */
	public function convert()
	{
		throw new LogicException('Only authorised credentials can be converted.');
	}

	/**
	 * Method to deny a set of temporary credentials.
	 *
	 * @return  ROAuth2CredentialsState
	 *
	 * @since   1.0
	 * @throws  LogicException
	 */
	public function deny()
	{
		throw new LogicException('Only temporary credentials can be denied.');
	}

	/**
	 * Method to initialise the credentials.  This will persist a temporary credentials set to be authorised by
	 * a resource owner.
	 *
	 * @param   string   $clientId     The key of the client requesting the temporary credentials.
	 * @param   string   $clientSecret The secret key of the client requesting the temporary credentials.
	 * @param   string   $callbackUrl  The callback URL to set for the temporary credentials.
	 * @param   integer  $lifetime     How long the credentials are good for.
	 *
	 * @return  ROAuth2CredentialsState
	 *
	 * @since   1.0
	 * @throws  LogicException
	 */
	public function initialise($clientId, $clientSecret, $callbackUrl, $lifetime = 3600)
	{
		// Setup the properties for the credentials.
		$this->table->credentials_id = null;
		$this->table->callback_url = $callbackUrl;
		$this->table->client_id = $clientId;
		$this->table->client_secret = $clientSecret;
		$this->table->access_token = $this->randomKey();
		$this->table->resource_uri = $callbackUrl;
		$this->table->type = ROAuth2Credentials::TEMPORARY;
		$this->table->temporary_expiration_date = time() + $lifetime;

		// Persist the object in the database.
		$this->create();

		return new ROAuth2CredentialsStateTemporary($this->table);
	}

	/**
	 * Method to revoke a set of token credentials.
	 *
	 * @return  ROAuth2CredentialsState
	 *
	 * @since   1.0
	 * @throws  LogicException
	 */
	public function revoke()
	{
		throw new LogicException('Only token credentials can be revoked.');
	}
}

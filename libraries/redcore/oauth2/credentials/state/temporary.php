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
 * OAuth Temporary Credentials class for the redCORE
 *
 * @package     Redcore
 * @subpackage  OAuth2
 * @since       1.0
 */
class ROauth2CredentialsStateTemporary extends ROauth2CredentialsState
{
	/**
	 * Method to authorise the credentials.  This will persist a temporary credentials set to be authorised by
	 * a resource owner.
	 *
	 * @param   integer  $resourceOwnerId  The id of the resource owner authorizing the temporary credentials.
	 * @param   string   $lifetime         How long (DateInterval format) the credentials should be valid (defaults to 60 minutes).
	 *
	 * @url http://php.net/manual/en/class.dateinterval.php
	 *
	 * @return  ROauth2CredentialsState
	 *
	 * @since   1.0
	 * @throws  LogicException
	 */
	public function authorise($resourceOwnerId, $lifetime = 'PT1H')
	{
		// Setup the properties for the credentials.
		$this->table->resource_owner_id = (int) $resourceOwnerId;
		$this->table->type = ROauth2Credentials::AUTHORISED;
		$this->table->temporary_token = $this->randomKey();

		if ($lifetime > 0)
		{
			// Set the correct date adding the lifetime
			$date = JFactory::getDate();
			$date->add(new DateInterval($lifetime));
			$this->table->expiration_date = $date->toSql();
		}
		else
		{
			$this->table->expiration_date = 0;
		}

		// Persist the object in the database.
		$this->update();

		$authorised = new ROauth2CredentialsStateAuthorised($this->table);

		return $authorised;
	}

	/**
	 * Method to convert a set of authorised credentials to token credentials.
	 *
	 * @return  ROauth2CredentialsState
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
	 * @return  ROauth2CredentialsState
	 *
	 * @since   1.0
	 * @throws  LogicException
	 */
	public function deny()
	{
		// Remove the credentials from the database.
		$this->delete();

		return new ROauth2CredentialsStateDenied($this->table);
	}

	/**
	 * Method to initialise the credentials.  This will persist a temporary credentials set to be authorised by
	 * a resource owner.
	 *
	 * @param   string   $clientId      The key of the client requesting the temporary credentials.
	 * @param   string   $clientSecret  The secret key of the client requesting the temporary credentials.
	 * @param   string   $callbackUrl   The callback URL to set for the temporary credentials.
	 * @param   integer  $lifetime      How long the credentials are good for.
	 *
	 * @return  ROauth2CredentialsState
	 *
	 * @since   1.0
	 * @throws  LogicException
	 */
	public function initialise($clientId, $clientSecret, $callbackUrl, $lifetime = 0)
	{
		throw new LogicException('Only new credentials can be initialised.');
	}

	/**
	 * Method to revoke a set of token credentials.
	 *
	 * @return  ROauth2CredentialsState
	 *
	 * @since   1.0
	 * @throws  LogicException
	 */
	public function revoke()
	{
		throw new LogicException('Only token credentials can be revoked.');
	}
}

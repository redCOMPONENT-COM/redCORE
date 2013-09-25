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
 * OAuth2 Client Table
 *
 * @package     Redcore
 * @subpackage  OAuth2
 * @since       1.0
 */
class ROauth2TableCredentials extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database driver object.
	 *
	 * @since   1.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__oauth_credentials', 'credentials_id', $db);
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
		// Build the query to delete the rows from the database.
		$query = $this->_db->getQuery(true);
		$query->delete('#__oauth_credentials')
			->where(array('expiration_date < ' . time(), 'expiration_date > 0'), 'AND')
			->where(array('temporary_expiration_date < ' . time(), 'temporary_expiration_date > 0'), 'AND');

		// Set and execute the query.
		$this->_db->setQuery($query);
		$this->_db->execute();
	}

	/**
	 * Load the credentials by key.
	 *
	 * @param   string  $key  The key for which to load the credentials.
	 * @param   string  $uri  The uri from the request.
	 *
	 * @return  void
	 *
	 * @since 1.0
	 */
	public function loadBySecretKey($key, $uri)
	{
		// Build the query to load the row from the database.
		$query = $this->_db->getQuery(true);
		$query->select('*')
		->from('#__oauth_credentials')
		->where($this->_db->quoteName('client_secret') . ' = ' . $this->_db->quote($key))
		->where($this->_db->quoteName('resource_uri') . ' = ' . $this->_db->quote($uri));

		// Set and execute the query.
		$this->_db->setQuery($query);
		$properties = $this->_db->loadAssoc();

		// Bind the result to the object
		$this->bind($properties);
	}

	/**
	 * Load the credentials by key.
	 *
	 * @param   string  $key  The key for which to load the credentials.
	 * @param   string  $uri  The uri from the request.
	 *
	 * @return  void
	 *
	 * @since 1.0
	 */
	public function loadByAccessToken($key, $uri)
	{
		// Build the query to load the row from the database.
		$query = $this->_db->getQuery(true);
		$query->select('*')
		->from('#__oauth_credentials')
		->where($this->_db->quoteName('access_token') . ' = ' . $this->_db->quote($key))
		->where($this->_db->quoteName('resource_uri') . ' = ' . $this->_db->quote($uri));

		// Set and execute the query.
		$this->_db->setQuery($query);
		$properties = $this->_db->loadAssoc();

		if (empty($properties))
		{
			throw new InvalidArgumentException('OAuth credentials not found.');
		}

		// Bind the result to the object
		$this->bind($properties);
	}
}

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
jimport('joomla.environment.response');

/**
 * redCORE class for interacting with an OAuth 2.0 server.
 *
 * @package     Redcore
 * @subpackage  OAuth2
 * @since       1.0
 */
class ROauth2Client
{
	/**
	 * @var    ROauth2TableClient  JTable object for persisting the client object.
	 * @since  1.0
	 */
	protected $_table;

	/**
	 * @var    JUser  JUser object for persisting the Joomla! user.
	 * @since  1.0
	 */
	public $_identity;

	/**
	 * Object constructor.
	 *
	 * @param   ROauth2TableClient  $table       The JTable object to use when persisting the object.
	 * @param   array               $properties  A set of properties with which to prime the object.
	 *
	 * @codeCoverageIgnore
	 * @since   1.0
	 */
	public function __construct(ROauth2TableUsers $table = null, array $properties = null)
	{
		JTable::addIncludePath(JPATH_REDCORE . '/oauth2/table');

		// Setup the table object.
		$this->_table = $table ? $table : JTable::getInstance('Users', 'ROauth2Table');

		// Iterate over any input properties and bind them to the object.
		if ($properties)
		{
			foreach ($properties as $k => $v)
			{
				$this->_table->$k = $v;
			}
		}
	}

	/**
	 * Method to get a property value.
	 *
	 * @param   string  $p  The name of the property for which to return the value.
	 *
	 * @return  mixed  The property value for the given property name.
	 *
	 * @since   1.0
	 */
	public function __get($p)
	{
		if (isset($this->_table->$p))
		{
			return $this->_table->$p;
		}
	}

	/**
	 * Method to set a value for a property.
	 *
	 * @param   string  $p  The name of the property for which to set the value.
	 * @param   mixed   $v  The property value to set.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function __set($p, $v)
	{
		if (isset($this->_table->$p))
		{
			$this->_table->$p = $v;
		}
	}

	/**
	 * Method to create the client in the database.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0
	 */
	public function create()
	{
		// Can't insert something that already has an ID.
		if ($this->_table->client_id)
		{
			return false;
		}

		// Ensure we don't have an id to insert... use the auto-incrementor instead.
		$this->_table->client_id = null;

		return $this->_table->store();
	}

	/**
	 * Method to delete the client from the database.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function delete()
	{
		$this->_table->delete();
	}

	/**
	 * Method to load a client by id.
	 *
	 * @param   integer  $clientId  The id of the client to load.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function load($clientId)
	{
		$this->_table->load($clientId);
	}

	/**
	 * Method to load a client by key.
	 *
	 * @param   string  $key  The key of the client to load.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function loadByKey($key)
	{
		if ($this->_table->loadByKey($key))
		{
			$this->_identity = new JUser($this->_table->id);
		}
	}

	/**
	 * Method to update the client in the database.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0
	 */
	public function update()
	{
		if (!$this->_table->client_id)
		{
			return false;
		}

		return $this->_table->store();
	}
}

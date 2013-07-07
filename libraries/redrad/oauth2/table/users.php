<?php
/**
 * @package     RedRad
 * @subpackage  OAuth2
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * OAuth2 Credentials Table
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth1
 * @since       1.0
 */
class ROAuth2TableUsers extends JTableUser
{
	/**
	 * Load the credentials by key.
	 *
	 * @param   string  $key  The key for which to load the credentials.
	 *
	 * @return  void
	 *
	 * @since 1.0
	 */
	public function loadByKey($key)
	{
		// Build the query to load the row from the database.
		$query = $this->_db->getQuery(true);
		$query->select('*')
			->from('#__users')
			->where($this->_db->quoteName('username') . ' = ' . $this->_db->quote($key));

		// Set and execute the query.
		$this->_db->setQuery($query);
		$properties = $this->_db->loadAssoc();

//print_r($properties);

		// Iterate over any the loaded properties and bind them to the object.
		if ($properties)
		{
			foreach ($properties as $k => $v)
			{
				$this->$k = $v;
			}
		}
	}
}

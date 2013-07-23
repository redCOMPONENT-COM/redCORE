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
 * ROAuth2RequestPost class
 *
 * @package     Joomla
 * @since       1.0
 */
class ROAuth2RequestPost
{
	/**
	 * Object constructor.
	 *
	 * @param   ROAuth2TableCredentials  $table  Connector object for table class.
	 *
	 * @since   1.0
	 */
	public function __construct()
	{
		$this->_app = JFactory::getApplication();

		// Setup the database object.
		$this->_input = $this->_app->input;
	}

	/**
	 * Parse the request POST variables for OAuth parameters.
	 *
	 * @return  mixed  Array of OAuth 1.0 parameters if found or boolean false otherwise.
	 *
	 * @since   1.0
	 */
	public function processPostVars()
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

}

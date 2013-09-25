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
 * ROauth2ProtocolRequestPost class
 *
 * @package  Redcore
 * @since    1.0
 */
class ROauth2ProtocolRequestPost
{
	/**
	 * Object constructor.
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
	 * @return  mixed  Array of OAuth 2.0 parameters if found or boolean false otherwise.
	 *
	 * @since   1.0
	 */
	public function processVars()
	{
		// If we aren't handling a post request with urlencoded vars then there is nothing to do.
		if (strtoupper($this->_input->getMethod()) != 'POST'
			|| !strpos($this->_input->server->get('CONTENT_TYPE', ''), 'x-www-form-urlencoded') )
		{
			return false;
		}

		// Initialise variables.
		$parameters = array();

		// Iterate over the reserved parameters and look for them in the POST variables.
		foreach (ROauth2ProtocolRequest::getReservedParameters() as $k)
		{
			if ($this->_input->post->getString('oauth_' . $k, false))
			{
				$parameters['OAUTH_' . strtoupper($k)] = trim($this->_input->post->getString('oauth_' . $k));
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

<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Authentication.oauth2
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::import('cms.html.html');
JLoader::import('joomla.user.helper');

/**
 * OAuth2 Authentication Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Authentication.oauth2
 * @since       1.0
 */
class PlgAuthenticationOAuth2 extends JPlugin
{
	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function onBeforeExecute ()
	{
		// Loading redCORE libraries
		$this->loadRedcore();

		// Init the flag
		$request = false;

		// Load the Joomla! application
		$app = JFactory::getApplication();

		// Get the OAuth2 server instance
		$oauth_server = new ROAuth2Server;

		if ($oauth_server->listen())
		{
			$request = true;
		}
	}

	/**
	 * Determine if we are using a secure (SSL) connection.
	 *
	 * @return  boolean  True if using SSL, false if not.
	 *
	 * @since   1.0
	 */
	public function isSSLConnection()
	{
		return ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) || getenv('SSL_PROTOCOL_VERSION'));
	}

	/**
	 * Method to register custom library.
	 *
	 * @return  void
	 */
	public function loadRedcore()
	{
		$redcoreLoader = JPATH_PLATFORM . '/redcore/bootstrap.php';

		if (file_exists($redcoreLoader))
		{
			require_once $redcoreLoader;

			JLoader::registerPrefix('J',  JPATH_LIBRARIES . '/redcore/joomla');
		}

		// Setup the autoloader for the application classes.
		JLoader::registerPrefix('R', JPATH_REDCORE . '/oauth2');
	}
}

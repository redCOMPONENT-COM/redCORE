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
 * ROauth2ProtocolRequestGet class
 *
 * @package  Redcore
 * @since    1.0
 */
class ROauth2ProtocolRequestGet
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
	 * Method to get the OAuth message string for signing.
	 *
	 * @return  array  The filtered params
	 *
	 * @since   1.0
	 */
	public function processVars()
	{
		// Get a JURI instance for the request URL.
		$uri = new JURI($this->_app->get('uri.request'));

		// Initialise params array.
		$params = array();

		// Iterate over the reserved parameters and look for them in the POST variables.
		foreach (ROauth2ProtocolRequest::getReservedParameters() as $k)
		{
			if ($this->_input->get->getString('oauth_' . $k, false))
			{
				$params['OAUTH_' . strtoupper($k)] = trim($this->_input->get->getString('oauth_' . $k));
			}
		}

		// Make sure that any found oauth_signature is not included.
		unset($params['signature']);

		// Ensure the parameters are in order by key.
		ksort($params);

		return $params;
	}

	/**
	 * Method to get the OAuth message string for signing.
	 *
	 * Note: As of PHP 5.3 the $this->encode() function is RFC 3986 compliant therefore this requires PHP 5.3+
	 *
	 * @param   string  $requestUrl     The message's request URL.
	 * @param   string  $requestMethod  The message's request method.
	 *
	 * @return  string  The unsigned OAuth message string.
	 *
	 * @link    http://www.faqs.org/rfcs/rfc3986
	 * @see     $this->encode()
	 * @since   1.0
	 */
	public function _fetchStringForSigning($requestUrl, $requestMethod)
	{
		// Get a JURI instance for the request URL.
		$uri = new JURI($requestUrl);

		// Initialise base array.
		$base = array();

		// Get the found parameters.
		$params = $this->getParameters();

		// Add the variables from the URI query string.
		foreach ($uri->getQuery(true) as $k => $v)
		{
			if (strpos($k, 'oauth_') !== 0)
			{
				$params[$k] = $v;
			}
		}

		// Make sure that any found oauth_signature is not included.
		unset($params['oauth_signature']);

		// Ensure the parameters are in order by key.
		ksort($params);

		// Iterate over the keys to add properties to the base.
		foreach ($params as $key => $value)
		{
			// If we have multiples for the parameter let's loop over them.
			if (is_array($value))
			{
				// Don't want to do this more than once in the inner loop.
				$key = $this->encode($key);

				// Sort the value array and add each one.
				sort($value, SORT_STRING);

				foreach ($value as $v)
				{
					$base[] = $key . '=' . $this->encode($v);
				}
			}

			// The common case is that there is one entry per property.
			else
			{
				$base[] = $this->encode($key) . '=' . $this->encode($value);
			}
		}

		// Start off building the base string by adding the request method and URI.
		$base = array(
			$this->encode(strtoupper($requestMethod)),
			$this->encode(strtolower($uri->toString(array('scheme', 'user', 'pass', 'host', 'port'))) . $uri->getPath()),
			$this->encode(implode('&', $base))
		);

		return implode('&', $base);
	}
}

<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Interface to handle OAuth authorization
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.2
 */
class RApiOauth2Helper
{
	/**
	 * OAuth2 Server instance
	 *
	 * @var    RApiOauth2Oauth2
	 * @since  1.0
	 */
	public static $serverApi = null;

	/**
	 * An array to hold installed Webservices data
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $installedWebservices = null;

	/**
	 * Handles token Request
	 *
	 * @return  array  parameter array
	 */
	public static function handleTokenRequest()
	{
		$serverApi = self::getOauth2Server();
		$serverApi
			->setApiOperation('token')
			->execute();

		if ($serverApi->response instanceof OAuth2\ResponseInterface)
		{
			return $serverApi->response->getParameters();
		}

		return is_array($serverApi->response) ? $serverApi->response : array($serverApi->response);
	}

	/**
	 * Verifies Resource Request
	 *
	 * @param   string  $scope  Scope for permission filtering
	 *
	 * @return  array  parameter array
	 */
	public static function verifyResourceRequest($scope = null)
	{
		$serverApi = self::getOauth2Server();
		$serverApi
			->setApiOperation('resource')
			->setOption('scope', $scope)
			->execute();

		return $serverApi->response;
	}

	/**
	 * Handle Authorize Request
	 *
	 * @return  array  parameter array
	 */
	public static function handleAuthorizeRequest()
	{
		$serverApi = self::getOauth2Server();
		$serverApi
			->setApiOperation('authorize')
			->execute();

		if ($serverApi->response instanceof OAuth2\ResponseInterface)
		{
			return $serverApi->response->getParameters();
		}

		return is_array($serverApi->response) ? $serverApi->response : array($serverApi->response);
	}

	/**
	 * Creates instance of OAuth2 server object
	 *
	 * @return  RApiOauth2Oauth2
	 */
	public static function getOauth2Server()
	{
		if (!isset(self::$serverApi))
		{
			$options = array(
				'api' => 'oauth2',
			);

			self::$serverApi = RApi::getInstance($options);
		}

		return self::$serverApi;
	}
}

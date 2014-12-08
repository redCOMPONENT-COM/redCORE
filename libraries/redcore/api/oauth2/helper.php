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
	 * Oauth2 Server instance
	 *
	 * @var    RApiOauth2Oauth2
	 * @since  1.2
	 */
	public static $serverApi = null;

	/**
	 * OAuth2 Client Scopes
	 *
	 * @var    array
	 * @since  1.2
	 */
	public static $clientScopes = array();

	/**
	 * Handles token Request
	 *
	 * @return  array  parameter array
	 */
	public static function handleTokenRequest()
	{
		if (!self::getOAuth2Server())
		{
			return false;
		}

		self::$serverApi
			->setApiOperation('token')
			->execute();

		return self::$serverApi->response;
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
		if (!self::getOAuth2Server())
		{
			return false;
		}

		self::$serverApi
			->setApiOperation('resource')
			->setOption('scope', $scope)
			->execute();

		return self::$serverApi->response;
	}

	/**
	 * Handle Authorize Request
	 *
	 * @return  array  parameter array
	 */
	public static function handleAuthorizeRequest()
	{
		if (!self::getOAuth2Server())
		{
			return false;
		}

		self::$serverApi
			->setApiOperation('authorize')
			->execute();

		return self::$serverApi->response;
	}

	/**
	 * Creates instance of OAuth2 server object
	 *
	 * @return  RApiOauth2Oauth2
	 */
	public static function getOAuth2Server()
	{
		if (RTranslationHelper::$pluginParams->get('enable_oauth2_server', 0) == 0)
		{
			return null;
		}

		if (!isset(self::$serverApi))
		{
			$options = array(
				'api' => 'oauth2',
			);

			self::$serverApi = RApi::getInstance($options);
		}

		return self::$serverApi;
	}

	/**
	 * Gets Client Scopes
	 *
	 * @param   string  $clientId  Client Id
	 *
	 * @return  array
	 */
	public static function getClientScopes($clientId = '')
	{
		if (isset(self::$clientScopes[$clientId]))
		{
			return self::$clientScopes[$clientId];
		}

		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select('oc.scope')
			->from($db->qn('#__redcore_oauth_clients', 'oc'))
			->where('oc.client_id = ' . $db->q($clientId));

		$db->setQuery($query);

		$clientScopes = $db->loadResult();

		return $clientScopes;
	}
}

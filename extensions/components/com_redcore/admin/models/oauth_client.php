<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Oauth Client Model
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.2
 */
class RedcoreModelOauth_Client extends RModelAdmin
{
	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @throws  RuntimeException
	 *
	 * @since   1.2
	 */
	public function save($data)
	{
		if (isset($data['grant_types']))
		{
			foreach ($data['grant_types'] as $grantType)
			{
				if ($grantType == 'client_credentials')
				{
					if (empty($data['user_id']))
					{
						JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_OAUTH_CLIENTS_ERROR_CLIENT_CREDENTIALS_JOOMLA_USER'), 'warning');
					}
				}
			}
		}

		if (isset($data['grant_types']) && is_array($data['grant_types']))
		{
			$data['grant_types'] = implode(' ', $data['grant_types']);
		}

		if (isset($data['scope']) && is_array($data['scope']))
		{
			$data['scope'] = implode(' ', $data['scope']);
		}

		if (empty($data['id']))
		{
			$data['client_secret'] = $this->generateSecretKey($data['client_id']);
		}

		if (parent::save($data))
		{
			return $this->setTokensToExpire($data['client_id']);
		}

		return false;
	}

	/**
	 * Method to update client scopes.
	 *
	 * @param   string  $clientId  Client Id
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @throws  RuntimeException
	 *
	 * @since   1.2
	 */
	public function setTokensToExpire($clientId)
	{
		$db = JFactory::getDbo();

		// Update Access Tokens
		$query = $db->getQuery(true)
			->update('#__redcore_oauth_access_tokens')
			->set($db->qn('expires') . ' = NOW()')
			->where($db->qn('client_id') . ' = ' . $db->q($clientId))
			->where($db->qn('expires') . ' > NOW()');
		$db->setQuery($query);
		$db->execute();

		// Update Authorization codes
		$query = $db->getQuery(true)
			->update('#__redcore_oauth_authorization_codes')
			->set($db->qn('expires') . ' = NOW()')
			->where($db->qn('client_id') . ' = ' . $db->q($clientId))
			->where($db->qn('expires') . ' > NOW()');
		$db->setQuery($query);
		$db->execute();

		// Update Refresh Tokens
		$query = $db->getQuery(true)
			->update('#__redcore_oauth_refresh_tokens')
			->set($db->qn('expires') . ' = NOW()')
			->where($db->qn('client_id') . ' = ' . $db->q($clientId))
			->where($db->qn('expires') . ' > NOW()');
		$db->setQuery($query);
		$db->execute();

		return true;
	}

	/**
	 * Generate Client secret 64bit key
	 *
	 * @param   string  $clientId  The client id name
	 *
	 * @return  string
	 */
	public function generateSecretKey($clientId = '')
	{
		$uniqueString = RFilesystemFile::getUniqueName($clientId);

		return $uniqueString . RFilesystemFile::getUniqueName($uniqueString);
	}

	/**
	 * Load item object
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   1.2
	 */
	public function getItem($pk = null)
	{
		if (!$item = parent::getItem($pk))
		{
			return $item;
		}

		// Get Access token and Authorization codes
		$db	= $this->getDbo();

		// There can be multiple access tokens that are not expired yet so we only load last one
		$query = $db->getQuery(true)
		->select('oat.access_token, oat.expires as access_token_expires')
		->from($db->qn('#__redcore_oauth_access_tokens', 'oat'))
		->where('oat.client_id = ' . $db->quote($item->client_id))
		->order('oat.expires DESC');
		$db->setQuery($query);

		if ($accessToken = $db->loadObject())
		{
			$item->access_token = $accessToken->access_token;
			$item->access_token_expires = $accessToken->access_token_expires;
		}

		// There can be multiple authorization codes that are not expired yet so we only load last one
		$query = $db->getQuery(true)
			->select('oac.authorization_code, oac.expires as authorization_code_expires')
			->from($db->qn('#__redcore_oauth_authorization_codes', 'oac'))
			->where('oac.client_id = ' . $db->quote($item->client_id))
			->order('oac.expires DESC');
		$db->setQuery($query);

		if ($accessToken = $db->loadObject())
		{
			$item->authorization_code = $accessToken->authorization_code;
			$item->authorization_code_expires = $accessToken->authorization_code_expires;
		}

		// There can be multiple refresh tokens that are not expired yet so we only load last one
		$query = $db->getQuery(true)
			->select('ort.refresh_token, ort.expires as refresh_token_expires')
			->from($db->qn('#__redcore_oauth_refresh_tokens', 'ort'))
			->where('ort.client_id = ' . $db->quote($item->client_id))
			->order('ort.expires DESC');
		$db->setQuery($query);

		if ($accessToken = $db->loadObject())
		{
			$item->refresh_token = $accessToken->refresh_token;
			$item->refresh_token_expires = $accessToken->refresh_token_expires;
		}

		$item->grant_types = explode(' ', $item->grant_types);
		$item->scope = explode(' ', $item->scope);

		return $item;
	}
}

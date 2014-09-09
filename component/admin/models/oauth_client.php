<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Oauth Client Model
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.0
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
	 * @since   1.0
	 */
	public function save($data)
	{
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

		return parent::save($data);
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
	 * @since   12.2
	 */
	public function getItem($pk = null)
	{
		if (!$item = parent::getItem($pk))
		{
			return $item;
		}

		// Get Access token and Authorization codes
		$db	= $this->getDbo();

		$query = $db->getQuery(true)
		->select('oat.access_token, oat.expires as access_token_expires')
		->from('#__redcore_oauth_access_tokens AS oat')
		->where('oat.client_id = ' . $db->quote($item->client_id))
		->order('oat.expires DESC');
		$db->setQuery($query);

		if ($accessToken = $db->loadObject())
		{
			$item->access_token = $accessToken->access_token;
			$item->access_token_expires = $accessToken->access_token_expires;
		}

		$query = $db->getQuery(true)
			->select('oac.authorization_code, oac.expires as authorization_code_expires')
			->from('#__redcore_oauth_authorization_codes AS oac')
			->where('oac.client_id = ' . $db->quote($item->client_id))
			->order('oac.expires DESC');
		$db->setQuery($query);

		if ($accessToken = $db->loadObject())
		{
			$item->authorization_code = $accessToken->authorization_code;
			$item->authorization_code_expires = $accessToken->authorization_code_expires;
		}

		$query = $db->getQuery(true)
			->select('ort.refresh_token, ort.expires as refresh_token_expires')
			->from('#__redcore_oauth_refresh_tokens AS ort')
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

<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Tables
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Oauth client table.
 *
 * @package     Redcore.Backend
 * @subpackage  Tables
 * @since       1.2
 */
class RedcoreTableOauth_Client extends RTable
{
	/**
	 * @var  int
	 */
	public $id;

	/**
	 * @var  string
	 */
	public $client_id;

	/**
	 * @var  string
	 */
	public $client_secret;

	/**
	 * @var  string
	 */
	public $redirect_uri;

	/**
	 * @var  string
	 */
	public $grant_types;

	/**
	 * @var string
	 */
	public $scope;

	/**
	 * @var string
	 */
	public $user_id;

	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 *
	 * @throws  UnexpectedValueException
	 */
	public function __construct(&$db)
	{
		$this->_tableName = 'redcore_oauth_clients';
		$this->_tbl_key = 'id';

		parent::__construct($db);
	}

	/**
	 * Checks that the object is valid and able to be stored.
	 *
	 * This method checks that the parent_id is non-zero and exists in the database.
	 * Note that the root node (parent_id = 0) cannot be manipulated with this class.
	 *
	 * @return  boolean  True if all checks pass.
	 */
	public function check()
	{
		// Check if client is not already created with this id.
		$client = clone $this;

		$this->client_id = trim($this->client_id);

		if (empty($this->client_id))
		{
			$this->setError(JText::_('COM_REDCORE_OAUTH_CLIENT_ID_FIELD_CANNOT_BE_EMPTY'));

			return false;
		}

		if ($client->load(array('client_id' => $this->client_id)) && $client->id != $this->id)
		{
			$this->setError(JText::_('COM_REDCORE_OAUTH_CLIENT_ID_ALREADY_EXISTS'));

			return false;
		}

		return true;
	}

	/**
	 * Method to store a node in the database table.
	 *
	 * @param   boolean  $updateNulls  True to update null values as well.
	 *
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = true)
	{
		if (!parent::store($updateNulls))
		{
			return false;
		}

		return true;
	}
}

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
 * Webservice table.
 *
 * @package     Redcore.Backend
 * @subpackage  Tables
 * @since       1.4
 */
class RedcoreTableWebservice extends RTable
{
	/**
	 * @var  int
	 */
	public $id;

	/**
	 * @var  string
	 */
	public $name;

	/**
	 * @var  string
	 */
	public $version;

	/**
	 * @var  string
	 */
	public $title;

	/**
	 * @var  string
	 */
	public $path;

	/**
	 * @var string
	 */
	public $xmlFile;

	/**
	 * @var string
	 */
	public $operations;

	/**
	 * @var string
	 */
	public $scopes;

	/**
	 * @var int
	 */
	public $client;

	/**
	 * @var int
	 */
	public $state;

	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 *
	 * @throws  UnexpectedValueException
	 */
	public function __construct(&$db)
	{
		$this->_tableName = 'redcore_webservices';

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

		$this->client = trim($this->client);
		$this->name = trim($this->name);
		$this->version = trim($this->version);

		$version = '1.0.0';

		if (!empty($this->version))
		{
			$matches = array();

			// Major
			$versionPattern = '/^(?<version>[0-9]+\.[0-9]+\.[0-9]+)$/';

			// Match the possible parts of a SemVer
			$matched = preg_match(
				$versionPattern,
				$this->version,
				$matches
			);

			// No match, invalid
			if (!$matched)
			{
				$this->setError(JText::_('COM_REDCORE_WEBSERVICE_VERSION_WRONG_FORMAT'));

				return false;
			}

			$version = $matches['version'];
		}

		$this->version = $version;

		if (empty($this->name))
		{
			$this->setError(JText::_('COM_REDCORE_WEBSERVICE_NAME_FIELD_CANNOT_BE_EMPTY'));

			return false;
		}

		if (empty($this->client))
		{
			$this->setError(JText::_('COM_REDCORE_WEBSERVICE_CLIENT_FIELD_CANNOT_BE_EMPTY'));

			return false;
		}

		if ($client->load(array('client' => $this->client, 'name' => $this->name, 'version' => $this->version)) && $client->id != $this->id)
		{
			$this->setError(JText::_('COM_REDCORE_WEBSERVICE_ALREADY_EXISTS'));

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
	public function store($updateNulls = false)
	{
		if (!parent::store($updateNulls))
		{
			return false;
		}

		return true;
	}
}

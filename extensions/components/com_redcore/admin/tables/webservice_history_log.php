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
class RedcoreTableWebservice_History_Log extends RTable
{
	/**
	 * @var  int
	 */
	public $id;

	/**
	 * @var  string
	 */
	public $webservice_name;

	/**
	 * @var  string
	 */
	public $webservice_version;

	/**
	 * @var  string
	 */
	public $webservice_client;

	/**
	 * @var  string
	 */
	public $url;

	/**
	 * @var string
	 */
	public $authentication;

	/**
	 * @var string
	 */
	public $authentication_user;

	/**
	 * @var string
	 */
	public $operation;

	/**
	 * @var string
	 */
	public $method;

	/**
	 * @var int
	 */
	public $using_soap;

	/**
	 * @var int
	 */
	public $execution_time;

	/**
	 * @var int
	 */
	public $execution_memory;

	/**
	 * @var string
	 */
	public $messages;

	/**
	 * @var string
	 */
	public $file_name;

	/**
	 * @var string
	 */
	public $status;

	/**
	 * @var int
	 */
	public $created_by;

	/**
	 * @var string
	 */
	public $created_date;

	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 *
	 * @throws  UnexpectedValueException
	 */
	public function __construct(&$db)
	{
		$this->_tableName = 'redcore_webservice_history_log';

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
		$this->webservice_client  = trim($this->webservice_client);
		$this->webservice_name    = trim($this->webservice_name);
		$this->webservice_version = trim($this->webservice_version);

		$version = '1.0.0';

		if (!empty($this->webservice_version))
		{
			$matches = array();

			// Major
			$versionPattern = '/^(?<version>[0-9]+\.[0-9]+\.[0-9]+)$/';

			// Match the possible parts of a SemVer
			$matched = preg_match(
				$versionPattern,
				$this->webservice_version,
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

		$this->webservice_version = $version;

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

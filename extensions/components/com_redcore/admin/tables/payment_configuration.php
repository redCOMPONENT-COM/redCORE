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
 * Payment configuration table.
 *
 * @package     Redcore.Backend
 * @subpackage  Tables
 * @since       1.5
 */
class RedcoreTablePayment_Configuration extends RTable
{
	/**
	 * @var  int
	 */
	public $id;

	/**
	 * @var  string
	 */
	public $extension_name;

	/**
	 * @var  string
	 */
	public $owner_name;

	/**
	 * @var  string
	 */
	public $payment_name;

	/**
	 * @var  string
	 */
	public $params;

	/**
	 * @var int
	 */
	public $state;

	/**
	 * @var  string
	 */
	public $created_date = '0000-00-00 00:00:00';

	/**
	 * @var  integer
	 */
	public $created_by = null;

	/**
	 * @var  string
	 */
	public $modified_date = '0000-00-00 00:00:00';

	/**
	 * @var  integer
	 */
	public $modified_by = null;

	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 *
	 * @throws  UnexpectedValueException
	 */
	public function __construct(&$db)
	{
		$this->_tableName = 'redcore_payment_configuration';
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

		$this->extension_name = trim($this->extension_name);
		$this->owner_name = trim($this->owner_name);
		$this->payment_name = trim($this->payment_name);

		if (empty($this->extension_name))
		{
			$this->setError(JText::_('COM_REDCORE_PAYMENT_EXTENSION_NAME_FIELD_CANNOT_BE_EMPTY'));

			return false;
		}

		if (empty($this->payment_name))
		{
			$this->setError(JText::_('COM_REDCORE_PAYMENT_NAME_FIELD_CANNOT_BE_EMPTY'));

			return false;
		}

		$loadParams = array('payment_name' => $this->payment_name, 'owner_name' => $this->owner_name, 'extension_name' => $this->extension_name);

		if ($client->load($loadParams) && $client->id != $this->id)
		{
			$this->setError(JText::_('COM_REDCORE_PAYMENT_ID_ALREADY_EXISTS'));

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

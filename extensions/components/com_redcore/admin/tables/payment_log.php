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
 * Payment log table.
 *
 * @package     Redcore.Backend
 * @subpackage  Tables
 * @since       1.5
 */
class RedcoreTablePayment_Log extends RTable
{
	/**
	 * @var  int
	 */
	public $id;

	/**
	 * @var  int
	 */
	public $payment_id;

	/**
	 * @var  float
	 */
	public $amount;

	/**
	 * @var  string
	 */
	public $currency;

	/**
	 * @var string
	 */
	public $coupon_code;

	/**
	 * @var string
	 */
	public $ip_address;

	/**
	 * @var string
	 */
	public $referrer;

	/**
	 * @var string
	 */
	public $message_uri;

	/**
	 * @var string
	 */
	public $message_post;

	/**
	 * @var string
	 */
	public $message_text;

	/**
	 * @var string
	 */
	public $status;

	/**
	 * @var string
	 */
	public $transaction_id;

	/**
	 * @var string
	 */
	public $customer_note;

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
		$this->_tableName = 'redcore_payment_log';
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
		$this->payment_id = trim($this->payment_id);

		if (empty($this->payment_id))
		{
			$this->setError(JText::_('COM_REDCORE_PAYMENT_ID_FIELD_CANNOT_BE_EMPTY'));

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

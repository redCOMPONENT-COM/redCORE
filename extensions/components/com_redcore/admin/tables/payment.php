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
 * Payments table.
 *
 * @package     Redcore.Backend
 * @subpackage  Tables
 * @since       1.5
 */
class RedcoreTablePayment extends RTable
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
	 * @var  bool
	 */
	public $sandbox;

	/**
	 * @var  string
	 */
	public $order_name;

	/**
	 * @var  string
	 */
	public $order_id;

	/**
	 * @var  string
	 */
	public $url_cancel;

	/**
	 * @var  string
	 */
	public $url_accept;

	/**
	 * @var  string
	 */
	public $client_email;

	/**
	 * @var  string
	 */
	public $created_date;

	/**
	 * @var  string
	 */
	public $modified_date;

	/**
	 * @var  string
	 */
	public $confirmed_date;

	/**
	 * @var  string
	 */
	public $transaction_id;

	/**
	 * @var  float
	 */
	public $amount_original;

	/**
	 * @var  float
	 */
	public $amount_order_tax;

	/**
	 * @var  string
	 */
	public $order_tax_details;

	/**
	 * @var  float
	 */
	public $amount_shipping;

	/**
	 * @var  string
	 */
	public $shipping_details;

	/**
	 * @var  float
	 */
	public $amount_payment_fee;

	/**
	 * @var  float
	 */
	public $amount_total;

	/**
	 * @var  float
	 */
	public $amount_paid;

	/**
	 * @var  string
	 */
	public $currency;

	/**
	 * @var  string
	 */
	public $coupon_code;

	/**
	 * @var  string
	 */
	public $customer_note;

	/**
	 * @var  string
	 */
	public $status;

	/**
	 * @var  string
	 */
	public $params;

	/**
	 * @var int
	 */
	public $retry_counter;

	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 *
	 * @throws  UnexpectedValueException
	 */
	public function __construct(&$db)
	{
		$this->_tableName = 'redcore_payments';
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
		$this->extension_name = trim($this->extension_name);
		$this->owner_name = trim($this->owner_name);
		$this->payment_name = trim($this->payment_name);
		$this->order_id = trim($this->order_id);

		if (empty($this->extension_name))
		{
			$this->setError(JText::_('COM_REDCORE_PAYMENT_EXTENSION_NAME_FIELD_CANNOT_BE_EMPTY'));

			return false;
		}

		if (empty($this->order_id))
		{
			$this->setError(JText::_('COM_REDCORE_PAYMENT_ORDER_ID_FIELD_CANNOT_BE_EMPTY'));

			return false;
		}

		if (empty($this->payment_name))
		{
			$this->setError(JText::_('COM_REDCORE_PAYMENT_NAME_FIELD_CANNOT_BE_EMPTY'));

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

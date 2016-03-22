<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * Helper class for Payment statuses
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.5
 */
class RApiPaymentStatus
{
	/**
	 * Status codes
	 * @var array
	 */
	private static $statuses = array(
		// New payment not yet processed by the payment gateway
		'created'           => 'Created',
		// A payment has been accepted.
		'processed'         => 'Processed',
		// A payment has been authorized and ready for capture.
		'authorized'         => 'Authorized',
		// Payment is in Pending state (not completed or failed). We wait for the gateway for the next notification
		'pending'           => 'Pending',
		// This is approved and confirmed payment
		'completed'         => 'Completed',
		// For some reason this payment have failed. This usually happens if the payment was made from your customer's bank account.
		'failed'            => 'Failed',
		// This authorization has expired and cannot be captured.
		'expired'           => 'Expired',
		// Customer payment is denied
		'denied'            => 'Denied',
		// We have refunded this payment back to the client
		'refunded'          => 'Refunded',
		// A reversal has been canceled. Ex. we won a dispute with the customer, and the funds for the transaction that was reversed have been returned
		'canceled_reversal' => 'Canceled_Reversal',
		// We have lost a dispute case and transaction was reversed
		'reversed'          => 'Reversed',
		// We did not get proper status for the payment
		'undefined'         => 'Undefined',
	);

	/**
	 * Change Status from one state to another
	 *
	 * @param   string  $current  Current Status name
	 * @param   string  $new      New Status name
	 *
	 * @return  string
	 */
	public static function changeStatus($current, $new)
	{
		// Current status is not even set
		if (empty($current))
		{
			return self::getStatus($new);
		}

		// Same status
		if ($current == $new)
		{
			return self::getStatus($new);
		}

		// If new is created then we will use current status as it is same or higher
		if ($new == self::getStatusCreated())
		{
			return self::getStatus($current);
		}

		// If status has moved on from created but system wants to set it to created again
		if ($current != self::getStatusCreated() && $new == self::getStatusCreated())
		{
			return self::getStatus($current);
		}

		// Processed should only be after created, not after other statuses
		if ($current != self::getStatusCreated() && $new == self::getStatusProcessed())
		{
			return self::getStatus($current);
		}

		return self::getStatus($new);
	}

	/**
	 * Gives proper status string for Status Name
	 *
	 * @param   string  $statusName  Status Key name
	 *
	 * @return  string
	 */
	public static function getStatus($statusName)
	{
		if (isset(self::$statuses[strtolower($statusName)]))
		{
			return self::$statuses[strtolower($statusName)];
		}

		return self::getStatusUndefined();
	}

	/**
	 * Gives proper status label for Status Name
	 *
	 * @param   string  $statusName  Status Key name
	 *
	 * @return  string
	 */
	public static function getStatusLabel($statusName)
	{
		$key = self::getStatus($statusName);

		return JText::_('LIB_REDCORE_PAYMENT_STATUS_' . strtoupper($key));
	}

	/**
	 * Gives proper status label class for Status Name
	 *
	 * @param   string  $statusName  Status Key name
	 *
	 * @return  string
	 */
	public static function getStatusLabelClass($statusName)
	{
		$key = self::getStatus($statusName);

		switch ($key)
		{
			case self::getStatusCreated():
			case self::getStatusProcessed():
			case self::getStatusAuthorized():
				return 'default';
			case self::getStatusUndefined():
			case self::getStatusPending():
				return 'danger';
			case self::getStatusCompleted():
			case self::getStatusCanceled_Reversal():
				return 'success';
			case self::getStatusExpired():
			case self::getStatusReversed():
			case self::getStatusFailed():
			case self::getStatusDenied():
			case self::getStatusRefunded():
				return 'danger';
		}

		return JText::_('LIB_REDCORE_PAYMENT_STATUS_' . strtoupper($key));
	}

	/**
	 * Gives proper status string for Status Created
	 *
	 * @return  string
	 */
	public static function getStatusCreated()
	{
		return self::$statuses['created'];
	}

	/**
	 * Gives proper status string for Status Processed
	 *
	 * @return  string
	 */
	public static function getStatusProcessed()
	{
		return self::$statuses['processed'];
	}

	/**
	 * Gives proper status string for Status Authorized
	 *
	 * @return  string
	 */
	public static function getStatusAuthorized()
	{
		return self::$statuses['authorized'];
	}

	/**
	 * Gives proper status string for Status Completed
	 *
	 * @return  string
	 */
	public static function getStatusCompleted()
	{
		return self::$statuses['completed'];
	}

	/**
	 * Gives proper status string for Status Pending
	 *
	 * @return  string
	 */
	public static function getStatusPending()
	{
		return self::$statuses['pending'];
	}

	/**
	 * Gives proper status string for Status Failed
	 *
	 * @return  string
	 */
	public static function getStatusFailed()
	{
		return self::$statuses['failed'];
	}

	/**
	 * Gives proper status string for Status Expired
	 *
	 * @return  string
	 */
	public static function getStatusExpired()
	{
		return self::$statuses['expired'];
	}

	/**
	 * Gives proper status string for Status Denied
	 *
	 * @return  string
	 */
	public static function getStatusDenied()
	{
		return self::$statuses['denied'];
	}

	/**
	 * Gives proper status string for Status Refunded
	 *
	 * @return  string
	 */
	public static function getStatusRefunded()
	{
		return self::$statuses['refunded'];
	}

	/**
	 * Gives proper status string for Status Canceled_Reversal
	 *
	 * @return  string
	 */
	public static function getStatusCanceled_Reversal()
	{
		return self::$statuses['canceled_reversal'];
	}

	/**
	 * Gives proper status string for Status Reversed
	 *
	 * @return  string
	 */
	public static function getStatusReversed()
	{
		return self::$statuses['reversed'];
	}

	/**
	 * Gives proper status string for Status Undefined
	 *
	 * @return  string
	 */
	public static function getStatusUndefined()
	{
		return self::$statuses['undefined'];
	}
}
